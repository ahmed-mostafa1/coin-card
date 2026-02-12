<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\NewOrderNotification;
use App\Notifications\UserOrderCreatedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MarketCard99OrderService
{
    public function __construct(
        private readonly MarketCard99Client $client,
        private readonly WalletService $walletService,
        private readonly OrderStatusService $orderStatusService,
        private readonly NotificationService $notificationService
    ) {
    }

    /**
     * @param array{
     *  selected_price?:string|float|int|null,
     *  customer_identifier?:string|null,
     *  external_amount?:string|float|int|null,
     *  purchase_password?:string|null
     * } $input
     */
    public function createOrder(User $user, Service $service, array $input): Order
    {
        if ($service->source !== Service::SOURCE_MARKETCARD99) {
            throw ValidationException::withMessages([
                'service_id' => 'الخدمة لا تتبع مزود MarketCard99.',
            ]);
        }

        if (!$service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => 'الخدمة غير مفعلة حالياً.',
            ]);
        }

        if (!$service->external_product_id) {
            throw ValidationException::withMessages([
                'service_id' => 'الخدمة غير مرتبطة بمنتج خارجي.',
            ]);
        }

        $customerIdentifier = $input['customer_identifier'] ?? null;
        $externalAmount = $input['external_amount'] ?? null;
        $purchasePassword = $input['purchase_password'] ?? null;
        $selectedPrice = isset($input['selected_price']) ? (float) $input['selected_price'] : (float) $service->price;

        if ($selectedPrice <= 0) {
            throw ValidationException::withMessages([
                'selected_price' => 'السعر المحدد غير صالح.',
            ]);
        }

        if ($service->requires_customer_id && blank($customerIdentifier)) {
            throw ValidationException::withMessages([
                'customer_identifier' => 'معرف المستخدم مطلوب لهذه الخدمة.',
            ]);
        }

        if ($service->requires_amount && blank($externalAmount)) {
            throw ValidationException::withMessages([
                'external_amount' => 'المبلغ مطلوب لهذه الخدمة.',
            ]);
        }

        if ($service->requires_purchase_password && blank($purchasePassword)) {
            throw ValidationException::withMessages([
                'purchase_password' => 'كلمة سر الشراء مطلوبة لهذه الخدمة.',
            ]);
        }

        $recentOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_PROCESSING])
            ->where('created_at', '>', now()->subSeconds(10))
            ->first();

        if ($recentOrder) {
            throw ValidationException::withMessages([
                'order' => 'يرجى الانتظار قليلاً قبل إنشاء طلب آخر لنفس الخدمة.',
            ]);
        }

        $order = DB::transaction(function () use (
            $user,
            $service,
            $customerIdentifier,
            $externalAmount,
            $purchasePassword,
            $selectedPrice
        ) {
            $wallet = Wallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrCreate(['user_id' => $user->id]);

            $balance = (float) $wallet->balance;
            if ($balance < $selectedPrice) {
                throw ValidationException::withMessages([
                    'balance' => 'رصيدك غير كافٍ لإتمام عملية الشراء.',
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'status' => Order::STATUS_PROCESSING,
                'price_at_purchase' => $selectedPrice,
                'original_price' => $selectedPrice,
                'amount_held' => $selectedPrice,
                'payload' => [
                    'customer_identifier' => $customerIdentifier,
                    'external_amount' => $externalAmount,
                    'has_purchase_password' => filled($purchasePassword),
                ],
                'customer_identifier' => $customerIdentifier,
                'external_amount' => $externalAmount,
                'has_purchase_password' => filled($purchasePassword),
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'type' => 'created',
                'message' => 'تم إنشاء الطلب وإرسال التنفيذ إلى المزود.',
                'meta' => [
                    'amount_held' => $selectedPrice,
                    'external_product_id' => $service->external_product_id,
                ],
                'actor_user_id' => $user->id,
            ]);

            $this->walletService->holdAmount($wallet, (string) $selectedPrice, [
                'type' => 'hold',
                'status' => 'approved',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'created_by_user_id' => $user->id,
                'approved_by_user_id' => $user->id,
                'approved_at' => now(),
                'note' => 'تعليق مبلغ طلب خدمة MarketCard99',
            ], false);

            return $order;
        });

        $this->submitAndApplyExternalStatus($order, $service, $user, $customerIdentifier, $externalAmount, $purchasePassword);

        try {
            $order->load(['service', 'user']);
            $user->notify(new UserOrderCreatedNotification($order));
            $this->notificationService->notifyAdmins(new NewOrderNotification($order));
        } catch (\Throwable $e) {
            Log::error('MarketCard99: failed sending order creation notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $order->fresh();
    }

    public function syncOrderStatus(Order $order, User $actor): bool
    {
        if (!$order->external_bill_id) {
            return false;
        }

        $billResponse = $this->client->getBill((int) $order->external_bill_id);
        if (!($billResponse['ok'] ?? false)) {
            Log::warning('MarketCard99: bill status sync skipped due to API failure', [
                'order_id' => $order->id,
                'external_bill_id' => $order->external_bill_id,
                'error' => $billResponse['error_message'] ?? null,
            ]);

            return false;
        }

        $bill = data_get($billResponse['data'], 'data.bill');
        if (!is_array($bill)) {
            return false;
        }

        $externalStatus = strtolower((string) ($bill['status'] ?? ''));
        $mappedLocalStatus = $this->mapExternalStatus($externalStatus);
        $hasExternalChanges = $order->external_status !== ($externalStatus !== '' ? $externalStatus : null)
            || $order->external_raw !== $bill;

        $order->external_status = $externalStatus !== '' ? $externalStatus : null;
        $order->external_raw = $bill;
        $order->save();

        if (in_array($mappedLocalStatus, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)
            && !in_array($order->status, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)) {
            $note = $mappedLocalStatus === Order::STATUS_DONE
                ? 'تمت تسوية الطلب تلقائياً بناءً على حالة المزود.'
                : 'تم رفض الطلب تلقائياً بناءً على حالة المزود.';

            $this->orderStatusService->updateStatus($order, $mappedLocalStatus, $note, $actor);
            return true;
        }

        if ($mappedLocalStatus === Order::STATUS_PROCESSING
            && in_array($order->status, [Order::STATUS_NEW, Order::STATUS_SUBMITTED, Order::STATUS_CREATING_EXTERNAL], true)) {
            $this->orderStatusService->updateStatus($order, Order::STATUS_PROCESSING, null, $actor);
            return true;
        }

        return $hasExternalChanges;
    }

    private function submitAndApplyExternalStatus(
        Order $order,
        Service $service,
        User $actor,
        ?string $customerIdentifier,
        mixed $externalAmount,
        ?string $purchasePassword
    ): void {
        $payload = [
            'product_id' => $service->external_product_id,
        ];

        if (filled($customerIdentifier)) {
            $payload['id_user'] = $customerIdentifier;
            $payload['customer_id'] = $customerIdentifier;
        }

        if ($externalAmount !== null && $externalAmount !== '') {
            $payload['amount'] = (string) $externalAmount;
        }

        if (filled($purchasePassword)) {
            $payload['old'] = $purchasePassword;
        }

        $createdAtLocal = now();
        $createResult = $this->client->createBill($payload);

        $order->external_payload = [
            'product_id' => $service->external_product_id,
            'customer_identifier' => $customerIdentifier,
            'amount' => $externalAmount,
            'has_password' => filled($purchasePassword),
            'created_at_local' => $createdAtLocal->toDateTimeString(),
        ];
        $order->save();

        if (!($createResult['ok'] ?? false)) {
            $order->external_status = 'failed';
            $order->save();
            $this->orderStatusService->updateStatus(
                $order,
                Order::STATUS_REJECTED,
                'فشل إنشاء الفاتورة الخارجية: '.($createResult['error_message'] ?? 'Unknown error'),
                $actor
            );
            return;
        }

        $billResolution = $this->client->resolveBillIdAfterCreate(
            (int) $service->external_product_id,
            $customerIdentifier,
            $createdAtLocal
        );

        if (!$billResolution) {
            $order->external_status = 'unresolved';
            $order->save();
            $this->orderStatusService->updateStatus(
                $order,
                Order::STATUS_REJECTED,
                'تعذر ربط الفاتورة الخارجية بعد الإنشاء.',
                $actor
            );
            return;
        }

        $order->update([
            'external_bill_id' => $billResolution['external_bill_id'],
            'external_uuid' => $billResolution['external_uuid'],
            'external_status' => $billResolution['external_status'],
            'external_raw' => $billResolution['external_raw'],
        ]);

        $mappedLocalStatus = $this->mapExternalStatus((string) ($billResolution['external_status'] ?? ''));
        if ($mappedLocalStatus === Order::STATUS_DONE) {
            $this->orderStatusService->updateStatus($order, Order::STATUS_DONE, null, $actor);
            return;
        }

        if ($mappedLocalStatus === Order::STATUS_REJECTED) {
            $this->orderStatusService->updateStatus(
                $order,
                Order::STATUS_REJECTED,
                'تم رفض الطلب بناءً على نتيجة المزود.',
                $actor
            );
            return;
        }

        if ($order->status !== Order::STATUS_PROCESSING) {
            $this->orderStatusService->updateStatus($order, Order::STATUS_PROCESSING, null, $actor);
        }
    }

    private function mapExternalStatus(?string $externalStatus): string
    {
        $status = strtolower((string) $externalStatus);

        return match (true) {
            in_array($status, ['success', 'done', 'completed'], true) => Order::STATUS_DONE,
            in_array($status, ['cancel', 'failed', 'rejected'], true) => Order::STATUS_REJECTED,
            default => Order::STATUS_PROCESSING,
        };
    }
}
