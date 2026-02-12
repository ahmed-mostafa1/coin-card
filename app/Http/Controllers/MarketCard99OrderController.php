<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\MarketCard99OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MarketCard99OrderController extends Controller
{
    public function __construct(
        private MarketCard99OrderService $orderService
    ) {}

    /**
     * Create a new order with MarketCard99 integration
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate basic fields
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'qty' => 'nullable|integer|min:1|max:100',
            'customer_identifier' => 'nullable|string|max:255',
            'external_amount' => 'nullable|string|max:50',
            'purchase_password' => 'nullable|string|max:255',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Validate conditional fields based on service configuration
        if ($service->requires_customer_id && empty($validated['customer_identifier'])) {
            throw ValidationException::withMessages([
                'customer_identifier' => ['Customer identifier is required for this service'],
            ]);
        }

        if ($service->requires_amount && empty($validated['external_amount'])) {
            throw ValidationException::withMessages([
                'external_amount' => ['Amount is required for this service'],
            ]);
        }

        try {
            $order = $this->orderService->createOrder($user, $service, [
                'selected_price' => (float) ($service->price ?? 0),
                'customer_identifier' => $validated['customer_identifier'] ?? null,
                'external_amount' => $validated['external_amount'] ?? null,
                'purchase_password' => $validated['purchase_password'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'external_bill_id' => $order->external_bill_id,
                    'external_status' => $order->external_status,
                    'sell_total' => $order->sell_total,
                ],
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get order status
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()
            ->with('service')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'service_name' => $order->service->name,
                'status' => $order->status,
                'external_status' => $order->external_status,
                'external_bill_id' => $order->external_bill_id,
                'sell_total' => $order->sell_total,
                'customer_identifier' => $order->customer_identifier,
                'created_at' => $order->created_at,
            ],
        ]);
    }
}
