<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MarketCard99OrderService
{
    public function __construct(
        private MarketCard99Client $client,
        private WalletService $walletService,
        private NotificationService $notificationService
    ) {}

    /**
     * Create an order with MarketCard99 integration
     * 
     * @throws ValidationException
     * @throws \Exception
     */
    public function createOrder(
        User $user,
        Service $service,
        int $qty = 1,
        ?string $customerIdentifier = null,
        ?string $externalAmount = null,
        ?string $purchasePassword = null
    ): Order {
        // Validate service is active
        if (!$service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => ['Service is not active'],
            ]);
        }

        // Validate external product is configured
        if (!$service->external_product_id) {
            throw ValidationException::withMessages([
                'service_id' => ['Service is not configured for external fulfillment'],
            ]);
        }

        // Validate required fields based on service configuration
        if ($service->requires_customer_id && !$customerIdentifier) {
            throw ValidationException::withMessages([
                'customer_identifier' => ['Customer identifier is required for this service'],
            ]);
        }

        if ($service->requires_amount && !$externalAmount) {
            throw ValidationException::withMessages([
                'external_amount' => ['Amount is required for this service'],
            ]);
        }

        // Calculate pricing
        $sellUnitPrice = $service->price ?? $service->price_per_unit ?? 0;
        $sellTotal = $sellUnitPrice * $qty;

        // Check user wallet balance
        $wallet = $user->wallet;
        if (!$wallet) {
            throw new \Exception('User wallet not found');
        }

        if ($wallet->balance < $sellTotal) {
            throw ValidationException::withMessages([
                'balance' => ['Insufficient wallet balance'],
            ]);
        }

        // Idempotency check: prevent duplicate orders within 10 seconds
        $recentOrder = Order::where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->whereIn('status', ['creating_external', 'submitted'])
            ->where('created_at', '>', now()->subSeconds(10))
            ->first();

        if ($recentOrder) {
            throw ValidationException::withMessages([
                'order' => ['Please wait before placing another order for this service'],
            ]);
        }

        // Step 1: Create order and debit wallet in transaction
        $order = DB::transaction(function () use (
            $user,
            $service,
            $qty,
            $sellUnitPrice,
            $sellTotal,
            $customerIdentifier,
            $externalAmount,
            $purchasePassword,
            $wallet
        ) {
            // Create order with status creating_external
            $order = Order::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'qty' => $qty,
                'sell_unit_price' => $sellUnitPrice,
                'sell_total' => $sellTotal,
                'status' => 'creating_external',
                'customer_identifier' => $customerIdentifier,
                'external_amount' => $externalAmount,
                'has_purchase_password' => !is_null($purchasePassword),
                'price_at_purchase' => $sellTotal, // For compatibility with existing order system
                'amount_held' => $sellTotal,      // Required for email notifications compatibility
            ]);

            // Debit wallet
            $this->walletService->debit($wallet, (string) $sellTotal, [
                'type' => 'purchase',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'note' => "Order #{$order->id} - {$service->name}",
                'created_by_user_id' => $user->id,
                'approved_at' => now(),
            ], false); // useTransaction = false because we're already in a transaction

            return $order;
        });

        // Step 2: Call external API (outside DB transaction to avoid long locks)
        $createdAtLocal = Carbon::now();
        
        $createResult = $this->client->createBill(
            $service->external_product_id,
            $customerIdentifier,
            $externalAmount,
            $purchasePassword
        );

        // Store external payload for debugging (excluding password)
        $externalPayload = [
            'product_id' => $service->external_product_id,
            'customer_identifier' => $customerIdentifier,
            'amount' => $externalAmount,
            'has_password' => !is_null($purchasePassword),
            'created_at_local' => $createdAtLocal->toDateTimeString(),
        ];

        $order->external_payload = $externalPayload;
        $order->save();

        // Step 3: Handle external creation result
        if (!$createResult['success']) {
            // External creation failed - refund and mark as failed
            Log::error('MarketCard99: Order creation failed', [
                'order_id' => $order->id,
                'service_id' => $service->id,
                'error' => $createResult['message'],
            ]);

            // Refund wallet
            $this->walletService->credit($wallet, (string) $sellTotal, [
                'type' => 'refund',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'note' => "Refund for failed order #{$order->id}",
                'created_by_user_id' => $user->id,
                'approved_at' => now(),
            ]);

            $order->update([
                'status' => 'failed',
                'admin_note' => 'External bill creation failed: ' . $createResult['message'],
            ]);

            throw new \Exception('Failed to create external bill: ' . $createResult['message']);
        }

        // Step 4: Resolve bill ID by polling
        $billResolution = $this->client->resolveBillIdAfterCreate(
            $service->external_product_id,
            $customerIdentifier,
            $createdAtLocal
        );

        if (!$billResolution) {
            // Failed to resolve bill ID - refund and mark as failed
            Log::error('MarketCard99: Failed to resolve bill ID', [
                'order_id' => $order->id,
                'service_id' => $service->id,
                'product_id' => $service->external_product_id,
            ]);

            // Refund wallet
            $this->walletService->credit($wallet, (string) $sellTotal, [
                'type' => 'refund',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'note' => "Refund for order #{$order->id} - bill ID not resolved",
                'created_by_user_id' => $user->id,
                'approved_at' => now(),
            ]);

            $order->update([
                'status' => 'failed',
                'admin_note' => 'Failed to resolve external bill ID after creation',
            ]);

            throw new \Exception('Failed to resolve external bill ID. Please contact support.');
        }

        // Step 5: Update order with external bill details
        $order->update([
            'external_bill_id' => $billResolution['external_bill_id'],
            'external_uuid' => $billResolution['external_uuid'],
            'external_status' => $billResolution['external_status'],
            'external_raw' => $billResolution['external_raw'],
            'status' => $this->mapExternalStatus($billResolution['external_status']),
        ]);

        // Send notifications
        try {
            $user->notify(new \App\Notifications\UserOrderCreatedNotification($order));
            $this->notificationService->notifyAdmins(new \App\Notifications\NewOrderNotification($order));
        } catch (\Exception $e) {
            Log::error('MarketCard99: Failed to send notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('MarketCard99: Order created successfully', [
            'order_id' => $order->id,
            'external_bill_id' => $order->external_bill_id,
            'external_status' => $order->external_status,
        ]);

        return $order->fresh();
    }

    /**
     * Sync order status from external bill
     * 
     * @return bool True if order was updated
     */
    public function syncOrderStatus(Order $order): bool
    {
        if (!$order->external_bill_id) {
            return false;
        }

        // Don't sync if already in final state
        if (in_array($order->status, ['fulfilled', 'refunded', 'cancelled'])) {
            return false;
        }

        $bill = $this->client->getBill($order->external_bill_id);

        if (!$bill) {
            Log::warning('MarketCard99: Failed to fetch bill for sync', [
                'order_id' => $order->id,
                'external_bill_id' => $order->external_bill_id,
            ]);
            return false;
        }

        $externalStatus = $bill['status'] ?? null;
        $newStatus = $this->mapExternalStatus($externalStatus);
        $oldStatus = $order->status;

        // Check if we need to refund
        $shouldRefund = in_array(strtolower($externalStatus ?? ''), ['cancel', 'failed', 'rejected']);

        if ($shouldRefund && $order->status !== 'refunded') {
            // Refund the customer
            $wallet = $order->user->wallet;
            
            if ($wallet) {
                $this->walletService->credit($wallet, (string) $order->sell_total, [
                    'type' => 'refund',
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'note' => "Refund for cancelled/failed order #{$order->id}",
                    'created_by_user_id' => $order->user_id,
                    'approved_at' => now(),
                ]);

                Log::info('MarketCard99: Order refunded', [
                    'order_id' => $order->id,
                    'external_bill_id' => $order->external_bill_id,
                    'external_status' => $externalStatus,
                    'amount' => $order->sell_total,
                ]);
            }

            $newStatus = 'refunded';
        }

        // Update order
        $updated = $order->update([
            'external_status' => $externalStatus,
            'external_raw' => $bill,
            'status' => $newStatus,
        ]);

        if ($updated) {
            Log::info('MarketCard99: Order status synced', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'external_status' => $externalStatus,
            ]);

            // Notify user of status change
             try {
                $order->user->notify(new \App\Notifications\OrderStatusChangedNotification($order, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                Log::error('MarketCard99: Failed to send status notification', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $updated;
    }

    /**
     * Map external status to internal status
     */
    private function mapExternalStatus(?string $externalStatus): string
    {
        if (!$externalStatus) {
            return 'processing';
        }

        $status = strtolower($externalStatus);

        return match (true) {
            in_array($status, ['success', 'done', 'completed']) => 'fulfilled',
            in_array($status, ['cancel', 'failed', 'rejected']) => 'failed',
            in_array($status, ['pending', 'submitted']) => 'submitted',
            default => 'processing',
        };
    }
}
