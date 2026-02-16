<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class MarketCard99OrderController extends Controller
{
    public function store(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'MarketCard99 integration is disabled.',
        ], 410);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'MarketCard99 integration is disabled.',
            'order_id' => $id,
        ], 410);
    }
}
