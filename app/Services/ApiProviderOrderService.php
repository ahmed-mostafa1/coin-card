<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * Places orders and syncs status for any provider using its order config.
 */
class ApiProviderOrderService
{
    public function __construct(
        private readonly ApiProvider $provider,
        private readonly ApiProviderClient $client
    ) {}

    /**
     * Place an order on the external provider.
     *
     * @return array{ok:bool, error_message:?string}
     */
    public function place(Order $order): array
    {
        if (! $this->provider->hasOrderFulfillment()) {
            return ['ok' => false, 'error_message' => 'المزود لا يدعم تنفيذ الطلبات تلقائياً.'];
        }

        $service = $order->service;

        if (! $service || $service->provider_id !== $this->provider->id) {
            return ['ok' => false, 'error_message' => 'الطلب لا ينتمي لهذا المزود.'];
        }

        if (blank($service->external_product_id)) {
            return ['ok' => false, 'error_message' => 'الخدمة غير مرتبطة بمنتج خارجي.'];
        }

        $payload  = $order->payload ?? [];
        $apiPayload = [];

        // Product ID field
        if (filled($this->provider->order_product_field)) {
            $apiPayload[$this->provider->order_product_field] = $service->external_product_id;
        }

        // Account / game ID field
        if (filled($this->provider->order_account_id_field)) {
            $source    = $this->provider->order_account_id_source ?? 'customer_identifier';
            $accountId = $payload[$source] ?? ($order->customer_identifier ?? null);

            if (blank($accountId)) {
                return ['ok' => false, 'error_message' => 'معرف الحساب مطلوب لإتمام الطلب.'];
            }
            $apiPayload[$this->provider->order_account_id_field] = (string) $accountId;
        }

        // Quantity field
        if (filled($this->provider->order_quantity_field)) {
            $qtySource = $this->provider->order_quantity_source;
            $quantity  = filled($qtySource) && isset($payload[$qtySource]) && is_numeric($payload[$qtySource])
                ? (int) $payload[$qtySource]
                : 1;
            $apiPayload[$this->provider->order_quantity_field] = $quantity;
        }

        // Our order reference field
        if (filled($this->provider->order_our_ref_field)) {
            $apiPayload[$this->provider->order_our_ref_field] = 'COINCARD-' . $order->id;
        }

        // Extra static fields
        if (! empty($this->provider->order_extra_fields)) {
            $apiPayload = array_merge($apiPayload, $this->provider->order_extra_fields);
        }

        $method = strtolower($this->provider->order_method);
        $result = $method === 'get'
            ? $this->client->get($this->provider->order_endpoint, $apiPayload)
            : $this->client->post($this->provider->order_endpoint, $apiPayload);

        if (! ($result['ok'] ?? false)) {
            Log::error('ApiProvider: failed to place order', [
                'provider' => $this->provider->slug,
                'order_id' => $order->id,
                'error'    => $result['error_message'] ?? null,
            ]);
            return ['ok' => false, 'error_message' => $result['error_message'] ?? 'فشل إرسال الطلب للمزود.'];
        }

        $data = $result['data'] ?? [];

        $transactionId  = filled($this->provider->order_transaction_id_path)
            ? $this->client->resolvePath($data, $this->provider->order_transaction_id_path)
            : null;
        $executionStatus = filled($this->provider->order_exec_status_path)
            ? $this->client->resolvePath($data, $this->provider->order_exec_status_path)
            : 'processing';

        $order->update([
            'provider_transaction_id'   => $transactionId ? (string) $transactionId : null,
            'provider_execution_status' => $executionStatus ? (string) $executionStatus : 'processing',
            'provider_replay'           => null,
        ]);

        Log::info('ApiProvider: order placed', [
            'provider'       => $this->provider->slug,
            'order_id'       => $order->id,
            'transaction_id' => $transactionId,
        ]);

        return ['ok' => true, 'error_message' => null];
    }

    /**
     * Fetch latest status from the provider and update order fields.
     *
     * @return array{ok:bool, changed:bool, execution_status:?string, error_message:?string}
     */
    public function syncStatus(Order $order): array
    {
        if (! $this->provider->hasStatusCheck()) {
            return [
                'ok'               => false,
                'changed'          => false,
                'execution_status' => null,
                'error_message'    => 'المزود لا يدعم مزامنة الحالة تلقائياً.',
            ];
        }

        $idParam = $this->provider->status_id_param ?? 'client_order_id';
        $query   = [$idParam => 'COINCARD-' . $order->id];

        $method = strtolower($this->provider->status_method);
        $result = $method === 'post'
            ? $this->client->post($this->provider->status_endpoint, $query)
            : $this->client->get($this->provider->status_endpoint, $query);

        if (! ($result['ok'] ?? false)) {
            return [
                'ok'               => false,
                'changed'          => false,
                'execution_status' => null,
                'error_message'    => $result['error_message'] ?? 'فشل استعلام حالة الطلب.',
            ];
        }

        $data            = $result['data'] ?? [];
        $execPath        = $this->provider->status_exec_path;
        $executionStatus = strtolower((string) (
            filled($execPath) ? $this->client->resolvePath($data, $execPath) : ''
        ));

        $changed = $order->provider_execution_status !== $executionStatus;

        $order->update([
            'provider_execution_status' => $executionStatus ?: $order->provider_execution_status,
        ]);

        return [
            'ok'               => true,
            'changed'          => $changed,
            'execution_status' => $executionStatus,
            'error_message'    => null,
        ];
    }

    /**
     * Map provider execution status to our local order status.
     * Returns null if still in-progress.
     */
    public function mapToLocalStatus(string $executionStatus): ?string
    {
        $successValues = $this->provider->status_success_values ?? ['success', 'completed', 'done'];
        $failedValues  = $this->provider->status_failed_values  ?? ['failed', 'error', 'cancelled'];

        if (in_array($executionStatus, $successValues, true)) {
            return Order::STATUS_DONE;
        }

        if (in_array($executionStatus, $failedValues, true)) {
            return Order::STATUS_REJECTED;
        }

        return null;
    }
}
