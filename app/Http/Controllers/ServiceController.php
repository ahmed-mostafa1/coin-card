<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseServiceRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Service;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Services\NotificationService;
use App\Services\WalletService;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(Service $service): View
    {
        abort_unless($service->is_active && $service->category->is_active, 404);
        abort_unless(($service->source ?? Service::SOURCE_MANUAL) === Service::SOURCE_MANUAL, 404);

        $service->load([
            'formFields' => fn ($query) => $query->orderBy('sort_order'),
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            'buttons' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        SEOTools::setTitle($service->localized_name);
        if ($service->localized_description) {
            SEOTools::setDescription(Str::limit(strip_tags($service->localized_description), 160));
        }
        if ($service->image_path) {
            SEOTools::opengraph()->addImage(asset('storage/' . $service->image_path));
        }

        $wallet = auth()->check() ? auth()->user()->wallet()->firstOrCreate([]) : null;

        return view('services.show', compact('service', 'wallet'));
    }

    public function purchase(
        PurchaseServiceRequest $request,
        Service $service,
        WalletService $walletService,
        NotificationService $notificationService
    ): RedirectResponse {
        abort_unless($service->is_active && $service->category->is_active, 404);
        abort_unless(($service->source ?? Service::SOURCE_MANUAL) === Service::SOURCE_MANUAL, 404);

        $user = $request->user();

        $payload = $request->input('fields', []);
        $allowedKeys = $service->formFields()->pluck('name_key')->all();
        $payload = array_intersect_key($payload, array_flip($allowedKeys));

        $isDiscountedInput = $service->isDiscountedInputPricing();
        $variantId = $isDiscountedInput ? null : $request->input('variant_id');
        $quantity = max(1, (int) $request->input('quantity', 1));

        $order = null;

        DB::transaction(function () use ($user, $service, $payload, $walletService, $variantId, $quantity, $request, $isDiscountedInput, &$order) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);
            $variant = null;

            $vipDiscount = 0.0;
            $basePrice = 0.0;
            $price = 0.0;

            if ($isDiscountedInput) {
                $offerAmount = round((float) $request->input('offer_amount', 0), 2);
                $serviceDiscountPercent = round((float) $service->admin_discount_percent, 2);
                $discountFactor = max(0, 1 - ($serviceDiscountPercent / 100));

                $basePrice = $offerAmount;
                $price = round($offerAmount * $discountFactor, 2);

                if ($request->hasFile('offer_image')) {
                    $payload['offer_image_path'] = $request->file('offer_image')->store('orders/offer-images', 'public');
                }
                $payload['offer_amount'] = number_format($offerAmount, 2, '.', '');
                $payload['service_discount_percent'] = number_format($serviceDiscountPercent, 2, '.', '');
                $payload['payable_after_discount'] = number_format($price, 2, '.', '');
            } else {
                $userVipStatus = $user->load('vipStatus.vipTier')->vipStatus;
                if ($userVipStatus && $userVipStatus->vipTier) {
                    $vipDiscount = (float) ($userVipStatus->vipTier->discount_percentage ?? 0);
                }

                if ($service->is_quantity_based) {
                    $basePrice = (float) $service->price_per_unit * $quantity;
                    $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
                } elseif ($service->variants()->where('is_active', true)->exists()) {
                    $variant = $service->variants()
                        ->where('is_active', true)
                        ->whereKey($variantId)
                        ->firstOrFail();

                    $basePrice = (float) $variant->price;
                    $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
                } else {
                    $basePrice = (float) $service->price;
                    $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
                }
            }

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

            $discountPercentage = $isDiscountedInput
                ? (float) $service->admin_discount_percent
                : $vipDiscount;
            $discountAmount = round(max(0, (float) $basePrice - (float) $price), 2);

            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'variant_id' => $variant?->id,
                'status' => Order::STATUS_NEW,
                'price_at_purchase' => $price,
                'original_price' => $discountAmount > 0 ? round((float) $basePrice, 2) : null,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'amount_held' => $price,
                'payload' => $payload,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'type' => 'created',
                'message' => 'تم إنشاء الطلب وتعليق المبلغ.',
                'meta' => [
                    'amount_held' => $price,
                    'variant_name' => $variant?->name,
                    'vip_discount' => $vipDiscount,
                    'service_discount' => $isDiscountedInput ? (float) $service->admin_discount_percent : null,
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

        DB::afterCommit(function () use ($order, $notificationService, $user): void {
            if (! $order) {
                return;
            }

            $order->load(['service', 'user']);

            $user->notify(new \App\Notifications\UserOrderCreatedNotification($order));
            $notificationService->notifyAdmins(new NewOrderNotification($order));
        });

        return redirect()->route('account.orders')
            ->with('status', 'تم إنشاء الطلب، وسيظل المبلغ معلّقًا حتى تأكيد التنفيذ.');
    }
}
