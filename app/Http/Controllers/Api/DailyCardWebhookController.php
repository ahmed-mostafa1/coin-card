<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\DailyCardOrderService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DailyCardWebhookController extends Controller
{
    public function __construct(
        private readonly DailyCardOrderService $dailyCardOrderService,
        private readonly OrderStatusService $orderStatusService
    ) {}

    /**
     * Handle the DailyCard status callback.
     * Expected GET parameters:
     * - order_id
     * - status
     * - client_order_id
     */
    public function handle(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');
        $status = $request->query('status');
        $clientOrderId = $request->query('client_order_id');

        Log::info('DailyCard Webhook Received', [
            'order_id' => $orderId,
            'status' => $status,
            'client_order_id' => $clientOrderId,
        ]);

        if (! $clientOrderId || ! $status) {
            return response()->json(['message' => 'Missing required parameters.'], 400);
        }

        // Extract our local order ID from client_order_id (e.g. 'COINCARD-123')
        $localOrderId = str_replace('COINCARD-', '', $clientOrderId);
        $order = Order::find($localOrderId);

        if (! $order) {
            Log::warning("DailyCard Webhook: Order not found: {$localOrderId}");
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // We use syncStatus to query the provider and ensure status authenticity 
        // as well as grab any 'replay' messages before taking action locally.
        try {
            $sync = $this->dailyCardOrderService->syncStatus($order);

            if ($sync['ok'] ?? false) {
                $localStatus = $this->dailyCardOrderService->mapToLocalStatus(
                    $sync['execution_status'] ?? '',
                    $sync['provider_status'] ?? ''
                );

                if ($localStatus && ! in_array($order->status, [Order::STATUS_DONE, Order::STATUS_REJECTED], true)) {
                    // Find an admin system user to attribute this action to
                    $admin = User::role('admin')->first() ?? User::first();
                    
                    if ($admin) {
                        $this->orderStatusService->updateStatus(
                            $order,
                            $localStatus,
                            'تم تحديث الحالة تلقائياً عبر إشعار الـ Webhook الخاص بـ DailyCard',
                            $admin
                        );
                    } else {
                        Log::error('DailyCard Webhook: No admin user found for order timeline attribution');
                    }
                }
            } else {
                Log::error('DailyCard Webhook: Failed to sync order status', [
                    'order_id' => $order->id,
                    'error' => $sync['error_message'] ?? 'Unknown error',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('DailyCard Webhook: Exception while updating status', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Internal server error.'], 500);
        }

        return response()->json(['message' => 'Webhook processed successfully.']);
    }
}
