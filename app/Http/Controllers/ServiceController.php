<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseServiceRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Service;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusChangedNotification;
use App\Services\NotificationService;
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
        NotificationService $notificationService
    ): RedirectResponse
    {
        abort_unless($service->is_active && $service->category->is_active, 404);

        $user = $request->user();
        $payload = $request->input('fields', []);
        $allowedKeys = $service->formFields()->pluck('name_key')->all();
        $payload = array_intersect_key($payload, array_flip($allowedKeys));
        $variantId = $request->input('variant_id');

        $order = null;

        DB::transaction(function () use ($user, $service, $payload, $walletService, $variantId, &$order) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);
            $variant = null;
            $price = (string) $service->price;

            if ($service->variants()->where('is_active', true)->exists()) {
                $variant = $service->variants()
                    ->where('is_active', true)
                    ->whereKey($variantId)
                    ->firstOrFail();

                $price = (string) $variant->price;
            }

            $balance = (string) $wallet->balance;

            $insufficient = function_exists('bccomp')
                ? bccomp($balance, $price, 2) < 0
                : (float) $balance < (float) $price;

            if ($insufficient) {
                throw ValidationException::withMessages([
                    'balance' => 'رصيدك غير كافٍ لإتمام عملية الشراء.',
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'variant_id' => $variant?->id,
                'status' => Order::STATUS_NEW,
                'price_at_purchase' => $price,
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

            $user->notify(new OrderStatusChangedNotification($order, null, Order::STATUS_NEW));
            $notificationService->notifyAdmins(new NewOrderNotification($order));
        });

        return redirect()->route('account.orders')
            ->with('status', 'تم إنشاء الطلب، وسيظل المبلغ معلّقًا حتى تأكيد التنفيذ.');
    }
}
