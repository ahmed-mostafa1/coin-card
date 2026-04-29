<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseServiceRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Service;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusChangedNotification;
use App\Services\DailyCardOrderService;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Services\ServicePricingService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(Service $service): View
    {
        abort_unless($service->is_active && $service->category->is_active && $service->isProviderAvailable(), 404);
        abort_unless(
            in_array($service->source ?? Service::SOURCE_MANUAL, [Service::SOURCE_MANUAL, Service::SOURCE_DAILYCARD], true) || $service->provider_id !== null,
            404
        );

        $service->load([
            'formFields' => fn ($query) => $query->orderBy('sort_order'),
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            'buttons' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $wallet = auth()->check() ? auth()->user()->wallet()->firstOrCreate([]) : null;

        return view('services.show', compact('service', 'wallet'));
    }

    public function purchase(
        PurchaseServiceRequest $request,
        Service $service,
        WalletService $walletService,
        NotificationService $notificationService,
        DailyCardOrderService $dailyCardOrderService,
        ServicePricingService $pricingService,
        LoyaltyService $loyaltyService,
        \App\Services\ApiProviderManager $apiProviderManager
    ): RedirectResponse {
        abort_unless($service->is_active && $service->category->is_active && $service->isProviderAvailable(), 404);
        abort_unless(
            in_array($service->source ?? Service::SOURCE_MANUAL, [Service::SOURCE_MANUAL, Service::SOURCE_DAILYCARD], true) || $service->provider_id !== null,
            404
        );

        $isDailyCard = (($service->source ?? Service::SOURCE_MANUAL) === Service::SOURCE_DAILYCARD)
            || ($service->provider_id !== null && str_contains(strtolower((string) $service->source), 'dailycard'))
            || (optional($service->provider)->slug === 'daily-card');

        $isGenericProvider = ! $isDailyCard && $service->provider_id !== null && optional($service->provider)->hasOrderFulfillment();
        $shouldAutoPlace = $isDailyCard || $isGenericProvider;

        $user = $request->user();

        $payload = $request->input('fields', []);
        $allowedKeys = $service->formFields()->pluck('name_key')->all();
        $payload = array_intersect_key($payload, array_flip($allowedKeys));

        $isDiscountedInput = $service->isDiscountedInputPricing();
        $variantId = $isDiscountedInput ? null : $request->input('variant_id');
        $quantity = max(1, (int) $request->input('quantity', 1));
        $uploadedImage = $request->file('order_image');

        $order = null;

        DB::transaction(function () use (
            $user,
            $service,
            $payload,
            $walletService,
            $variantId,
            $quantity,
            $request,
            $uploadedImage,
            $isDiscountedInput,
            $shouldAutoPlace,
            $pricingService,
            $loyaltyService,
            &$order
        ) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);
            $variant = null;

            $userDiscount = $isDiscountedInput ? 0.0 : $loyaltyService->discountPercent($user);
            $quote = [];

            if ($isDiscountedInput) {
                $offerAmount = round((float) $request->input('offer_amount', 0), 2);
                $quote = $pricingService->discountedInput($service, $offerAmount);

                if ($request->hasFile('offer_image')) {
                    $payload['offer_image_path'] = $request->file('offer_image')->store('orders/offer-images', 'public');
                }

                $payload['offer_amount'] = number_format($offerAmount, 2, '.', '');
                $payload['payable_after_discount'] = $quote['payable_total'];
            } else {
                if ($service->is_quantity_based) {
                    $quote = $pricingService->fixed($service, null, $quantity, $userDiscount);
                } elseif ($service->variants()->where('is_active', true)->exists()) {
                    $variant = $service->variants()
                        ->where('is_active', true)
                        ->whereKey($variantId)
                        ->firstOrFail();

                    $quote = $pricingService->fixed($service, $variant, $quantity, $userDiscount);
                } else {
                    $quote = $pricingService->fixed($service, null, $quantity, $userDiscount);
                }
            }

            $price = (float) $quote['payable_total'];

            $selectedPrice = $request->input('selected_price');
            if ($selectedPrice !== null && $selectedPrice !== '') {
                $selectedPrice = round((float) $selectedPrice, 2);
                $expectedPrice = round((float) $price, 2);

                if (abs($selectedPrice - $expectedPrice) > 0.01) {
                    throw ValidationException::withMessages([
                        'selected_price' => 'السعر المحدد غير صحيح.',
                    ]);
                }

                $price = $selectedPrice;
            }

            $price = number_format((float) $price, 2, '.', '');
            $balance = (string) $wallet->balance;

            $insufficient = function_exists('bccomp')
                ? bccomp($balance, $price, 2) < 0
                : (float) $balance < (float) $price;

            if ($insufficient) {
                throw ValidationException::withMessages([
                    'balance' => 'رصيدك غير كافٍ لإتمام عملية الشراء.',
                ]);
            }

            if (! $isDiscountedInput && $service->is_quantity_based) {
                $payload['quantity'] = $quantity;
            }

            $discountPercentage = (float) $quote['user_discount_percentage'];
            $discountAmount = (float) $quote['user_discount_amount'];
            $uploadedImagePath = null;
            $uploadedImageOriginalName = null;
            $uploadedImageMime = null;
            $uploadedImageSize = null;

            if ($uploadedImage) {
                $uploadedImagePath = $uploadedImage->store('orders/images/'.$user->id, 'public');
                $uploadedImageOriginalName = $uploadedImage->getClientOriginalName();
                $uploadedImageMime = $uploadedImage->getClientMimeType();
                $uploadedImageSize = $uploadedImage->getSize();
                $payload['order_image_path'] = $uploadedImagePath;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'variant_id' => $variant?->id,
                'status' => $shouldAutoPlace ? Order::STATUS_PROCESSING : Order::STATUS_NEW,
                'price_at_purchase' => $price,
                'base_price_at_purchase' => $quote['base_price'],
                'service_fee_type' => $quote['service_fee_type'],
                'service_fee_value' => $quote['service_fee_value'],
                'service_fee_amount' => $quote['service_fee_amount'],
                'gross_total_at_purchase' => $quote['gross_total'],
                'user_discount_percentage' => $quote['user_discount_percentage'],
                'user_discount_amount' => $quote['user_discount_amount'],
                'original_price' => $discountAmount > 0 ? $quote['gross_total'] : null,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'amount_held' => $price,
                'payload' => $payload,
                'uploaded_image_path' => $uploadedImagePath,
                'uploaded_image_original_name' => $uploadedImageOriginalName,
                'uploaded_image_mime' => $uploadedImageMime,
                'uploaded_image_size' => $uploadedImageSize,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'type' => 'created',
                'message' => 'تم إنشاء الطلب وتعليق المبلغ.',
                'meta' => [
                    'amount_held' => $price,
                    'variant_name' => $variant?->name,
                    'user_discount' => $discountPercentage,
                    'service_fee_amount' => $quote['service_fee_amount'],
                    'quantity' => $service->is_quantity_based ? $quantity : null,
                ],
                'actor_user_id' => $user->id,
            ]);

            $walletService->holdAmount($wallet, $price, [
                'type' => 'hold',
                'status' => 'approved',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'created_by_user_id' => $user->id,
                'approved_by_user_id' => $user->id,
                'approved_at' => now(),
                'note' => 'تعليق مبلغ شراء خدمة',
            ], false);
        });

        DB::afterCommit(function () use ($order, $notificationService, $user, $isDailyCard, $isGenericProvider, $shouldAutoPlace, $dailyCardOrderService, $apiProviderManager): void {
            if (! $order) {
                return;
            }

            $order->load(['service', 'user']);

            $user->notify(new OrderStatusChangedNotification($order, null, $order->status));
            $notificationService->notifyAdmins(new NewOrderNotification($order));
            app(\App\Services\SecurityLogger::class)->log('order_created', $user, request(), [
                'order_id' => $order->id,
                'service_id' => $order->service_id,
                'amount' => $order->amount_held,
            ], $order);

            if ($shouldAutoPlace) {
                if ($isDailyCard) {
                    $dailyCardOrderService->place($order);
                } elseif ($isGenericProvider) {
                    $orderService = $apiProviderManager->forOrder($order);
                    $orderService?->place($order);
                }
            }
        });

        $redirectRoute = $isDiscountedInput ? 'account.orders' : 'account.orders.show';

        return redirect()->route($redirectRoute, $isDiscountedInput ? [] : $order)
            ->with('status', 'تم إنشاء الطلب، وسيظل المبلغ معلّقًا حتى تأكيد التنفيذ.');
    }
}
