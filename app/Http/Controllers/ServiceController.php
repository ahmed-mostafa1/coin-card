<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseServiceRequest;
use App\Models\Order;
use App\Models\Service;
use App\Models\Wallet;
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

        $service->load(['formFields.options' => fn ($query) => $query->orderBy('sort_order')]);

        $wallet = auth()->check() ? auth()->user()->wallet()->firstOrCreate([]) : null;

        return view('services.show', compact('service', 'wallet'));
    }

    public function purchase(PurchaseServiceRequest $request, Service $service, WalletService $walletService): RedirectResponse
    {
        abort_unless($service->is_active && $service->category->is_active, 404);

        $user = $request->user();
        $payload = $request->input('fields', []);
        $allowedKeys = $service->formFields()->pluck('name_key')->all();
        $payload = array_intersect_key($payload, array_flip($allowedKeys));

        DB::transaction(function () use ($user, $service, $payload, $walletService) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrCreate(['user_id' => $user->id]);

            $balance = (string) $wallet->balance;
            $price = (string) $service->price;

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
                'status' => Order::STATUS_NEW,
                'price_at_purchase' => (string) $service->price,
                'payload' => $payload,
            ]);

            $walletService->debit($wallet, (string) $service->price, [
                'type' => 'purchase',
                'status' => 'approved',
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'created_by_user_id' => $user->id,
                'approved_by_user_id' => $user->id,
                'approved_at' => now(),
                'note' => 'شراء خدمة',
            ], false);
        });

        return redirect()->route('account.orders')
            ->with('status', 'تم إنشاء الطلب بنجاح. سيتم معالجته قريباً.');
    }
}
