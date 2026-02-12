<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseServiceRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Service;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Services\NotificationService;
use App\Services\MarketCard99OrderService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(Service $service): View
    {
        abort_unless($service->is_active && $service->category->is_active, 404);

        $service->load([
            'formFields' => fn ($query) => $query->orderBy('sort_order'),
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
        ]);

        $wallet = auth()->check() ? auth()->user()->wallet()->firstOrCreate([]) : null;

        return view('services.show', compact('service', 'wallet'));
    }

    public function purchase(
        PurchaseServiceRequest $request,
        Service $service,
        WalletService $walletService,
        NotificationService $notificationService,
        MarketCard99OrderService $marketCard99OrderService
    ): RedirectResponse
    {
        abort_unless($service->is_active && $service->category->is_active, 404);

        $user = $request->user();

        if ($service->source === Service::SOURCE_MARKETCARD99) {
            $order = $marketCard99OrderService->createOrder($user, $service, [
                'selected_price' => $request->input('selected_price'),
                'customer_identifier' => $request->input('customer_identifier'),
                'external_amount' => $request->input('external_amount'),
                'purchase_password' => $request->input('purchase_password'),
            ]);

            $statusMessage = 'تم إرسال طلبك إلى المزود الخارجي وسيتم تحديث حالته تلقائياً.';
            if ($order->status === Order::STATUS_DONE) {
                $statusMessage = 'تم تنفيذ الطلب مباشرة وتأكيد الخصم.';
            } elseif ($order->status === Order::STATUS_REJECTED) {
                $statusMessage = 'تعذر تنفيذ الطلب الخارجي وتم إرجاع الرصيد إلى محفظتك.';
            }

            return redirect()->route('account.orders')
                ->with('status', $statusMessage);
        }

        $payload = $request->input('fields', []);
        $allowedKeys = $service->formFields()->pluck('name_key')->all();
        $payload = array_intersect_key($payload, array_flip($allowedKeys));
        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity', 1);

        $order = null;

        DB::transaction(function () use ($user, $service, $payload, $walletService, $variantId, $quantity, $request, &$order) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);
            $variant = null;
            
            // Get VIP discount
            $vipDiscount = 0;
            $userVipStatus = $user->load('vipStatus.vipTier')->vipStatus;
            if ($userVipStatus && $userVipStatus->vipTier) {
                $vipDiscount = $userVipStatus->vipTier->discount_percentage ?? 0;
            }

            // Calculate the expected price based on service type
            if ($service->is_quantity_based) {
                // Quantity-based pricing
                $basePrice = $service->price_per_unit * $quantity;
                $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
            } elseif ($service->variants()->where('is_active', true)->exists()) {
                // Variant-based pricing
                $variant = $service->variants()
                    ->where('is_active', true)
                    ->whereKey($variantId)
                    ->firstOrFail();

                $basePrice = $variant->price;
                $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
            } else {
                // Regular service pricing
                $basePrice = $service->price;
                $price = $vipDiscount > 0 ? $basePrice * (1 - $vipDiscount / 100) : $basePrice;
            }

            // Use the selected_price from request if provided (frontend calculated)
            $selectedPrice = $request->input('selected_price');
            if ($selectedPrice !== null && $selectedPrice !== '') {
                $selectedPrice = (float) $selectedPrice;
                $expectedPrice = (float) $price;
                
                // Validate that the selected price matches expected (with small tolerance for rounding)
                if (abs($selectedPrice - $expectedPrice) > 0.01) {
                    throw ValidationException::withMessages([
                        'selected_price' => 'السعر المحدد غير صحيح.',
                    ]);
                }
                
                $price = $selectedPrice;
            }

            $price = (string) $price;
            $balance = (string) $wallet->balance;

            $insufficient = function_exists('bccomp')
                ? bccomp($balance, $price, 2) < 0
                : (float) $balance < (float) $price;

            if ($insufficient) {
                throw ValidationException::withMessages([
                    'balance' => 'رصيدك غير كافٍ لإتمام عملية الشراء.',
                ]);
            }

            // Add quantity to payload if quantity-based
            if ($service->is_quantity_based) {
                $payload['quantity'] = $quantity;
            }

            // Calculate discount amount
            $discountAmount = $vipDiscount > 0 ? ($basePrice - $price) : 0;

            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'variant_id' => $variant?->id,
                'status' => Order::STATUS_NEW,
                'price_at_purchase' => $price,
                'original_price' => $vipDiscount > 0 ? $basePrice : null,
                'discount_percentage' => $vipDiscount,
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
