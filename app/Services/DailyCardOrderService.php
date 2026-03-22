<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class DailyCardOrderService
{
    public function __construct(
        private readonly DailyCardClient $client
    ) {}

    /**
     * Place an order on DailyCard.shop for the given local order.
     * Call this when transitioning an order to 'processing'.
     *
     * @return array{ok:bool,error_message:?string}
     */
    public function place(Order $order): array
    {
        $service = $order->service;

        if (! $service || $service->source !== Service::SOURCE_DAILYCARD) {
            return ['ok' => false, 'error_message' => 'الطلب لا ينتمي لمزود DailyCard.'];
        }

        if (! $service->external_product_id) {
            return ['ok' => false, 'error_message' => 'الخدمة غير مرتبطة بمنتج خارجي.'];
        }

        $payload = $order->payload ?? [];
        $accountId = $payload['customer_identifier'] ?? $order->customer_identifier ?? null;
        $quantity  = isset($payload['external_amount']) && is_numeric($payload['external_amount'])
            ? (int) $payload['external_amount']
            : 1;

        if (blank($accountId)) {
            return ['ok' => false, 'error_message' => 'معرف الحساب (account_id) مطلوب لإتمام الطلب.'];
        }

        $apiPayload = [
            'product'         => (int) $service->external_product_id,
            'account_id'      => (string) $accountId,
            'quantity'        => $quantity,
            'client_order_id' => 'COINCARD-' . $order->id,
        ];

        $result = $this->client->createOrder($apiPayload);

        if (! ($result['ok'] ?? false)) {
            Log::error('DailyCard: failed to place order', [
                'order_id'      => $order->id,
                'error_message' => $result['error_message'] ?? null,
            ]);

            return ['ok' => false, 'error_message' => $result['error_message'] ?? 'فشل إرسال الطلب للمزود.'];
        }

        $data = $result['data'] ?? [];

        $order->update([
            'provider_transaction_id'    => $data['transaction_id'] ?? null,
            'provider_execution_status'  => $data['execution_status'] ?? 'processing',
            'provider_replay'            => isset($data['replay']) ? (string) $data['replay'] : null,
        ]);

        Log::info('DailyCard: order placed successfully', [
            'order_id'       => $order->id,
            'transaction_id' => $data['transaction_id'] ?? null,
        ]);

        return ['ok' => true, 'error_message' => null];
    }

    /**
     * Fetch latest status from DailyCard and update order fields.
     * Returns ['ok', 'changed', 'execution_status', 'error_message'].
     */
    public function syncStatus(Order $order): array
    {
        $result = $this->client->getOrderStatus('COINCARD-' . $order->id);

        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'changed' => false, 'error_message' => $result['error_message'] ?? 'فشل استعلام حالة الطلب.'];
        }

        $data            = $result['data'] ?? [];
        $executionStatus = strtolower((string) ($data['execution_status'] ?? ''));
        $providerStatus  = strtolower((string) ($data['status'] ?? ''));
        $replay          = isset($data['replay']) ? (string) $data['replay'] : null;

        $changed = $order->provider_execution_status !== $executionStatus
            || ($replay && $order->provider_replay !== $replay);

        $order->update([
            'provider_execution_status' => $executionStatus ?: $order->provider_execution_status,
            'provider_replay'           => $replay ?? $order->provider_replay,
        ]);

        return [
            'ok'               => true,
            'changed'          => $changed,
            'execution_status' => $executionStatus,
            'provider_status'  => $providerStatus,
            'error_message'    => null,
        ];
    }

    /**
     * Map DailyCard execution_status / order status to local order status.
     */
    public function mapToLocalStatus(string $executionStatus, string $providerStatus): ?string
    {
        if (in_array($executionStatus, ['success', 'completed', 'done'], true)
            || in_array($providerStatus, ['completed'], true)) {
            return Order::STATUS_DONE;
        }

        if (in_array($executionStatus, ['failed', 'error'], true)
            || in_array($providerStatus, ['cancelled'], true)) {
            return Order::STATUS_REJECTED;
        }

        return null; // still in-progress
    }
}
