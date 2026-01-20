<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\Wallet;
use App\Notifications\OrderStatusChangedNotification;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['user', 'service'])
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('q'), function ($query, $term) {
                $query->whereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'service.formFields',
            'variant',
            'events' => fn ($query) => $query->oldest()->with('actor'),
        ]);
        $fieldLabels = $order->service->formFields->pluck('label', 'name_key');

        return view('admin.orders.show', compact('order', 'fieldLabels'));
    }

    public function update(Order $order, WalletService $walletService): RedirectResponse
    {
        request()->validate([
            'status' => ['required', 'in:new,processing,done,rejected,cancelled'],
            'admin_note' => ['nullable', 'string'],
        ], [
            'status.required' => 'يرجى اختيار الحالة الجديدة.',
        ]);

        $statusChanged = false;
        $oldStatus = null;
        $newStatus = null;

        DB::transaction(function () use ($order, $walletService, &$statusChanged, &$oldStatus, &$newStatus) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $newStatus = request('status');
            $currentStatus = $order->status;
            $oldStatus = $currentStatus;

            $allowedTransitions = [
                Order::STATUS_NEW => [Order::STATUS_NEW, Order::STATUS_PROCESSING, Order::STATUS_REJECTED],
                Order::STATUS_PROCESSING => [Order::STATUS_PROCESSING, Order::STATUS_DONE, Order::STATUS_REJECTED],
                Order::STATUS_DONE => [Order::STATUS_DONE],
                Order::STATUS_REJECTED => [Order::STATUS_REJECTED],
                Order::STATUS_CANCELLED => [Order::STATUS_CANCELLED],
            ];

            if (! in_array($newStatus, $allowedTransitions[$currentStatus] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'لا يمكن تغيير حالة الطلب وفق التسلسل المحدد.',
                ]);
            }

            if ($newStatus === Order::STATUS_DONE && $currentStatus !== Order::STATUS_DONE) {
                if ($order->released_at) {
                    throw ValidationException::withMessages([
                        'status' => 'تم إرجاع المبلغ المعلّق مسبقًا.',
                    ]);
                }

                if ($order->amount_held > 0 && $order->settled_at === null) {
                    $wallet = Wallet::where('user_id', $order->user_id)->lockForUpdate()->firstOrCreate(['user_id' => $order->user_id]);

                    $walletService->settleHeldAmount($wallet, (string) $order->amount_held, [
                        'type' => 'settle',
                        'status' => 'approved',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by_user_id' => request()->user()->id,
                        'approved_by_user_id' => request()->user()->id,
                        'approved_at' => now(),
                        'note' => 'تأكيد خصم طلب خدمة',
                    ], false);

                    $order->settled_at = now();
                }
            }

            if ($newStatus === Order::STATUS_REJECTED && $currentStatus !== Order::STATUS_REJECTED) {
                if ($order->settled_at) {
                    throw ValidationException::withMessages([
                        'status' => 'لا يمكن رفض طلب بعد خصم المبلغ.',
                    ]);
                }

                if ($order->amount_held > 0 && $order->released_at === null) {
                    $wallet = Wallet::where('user_id', $order->user_id)->lockForUpdate()->firstOrCreate(['user_id' => $order->user_id]);

                    $walletService->releaseHeldAmount($wallet, (string) $order->amount_held, [
                        'type' => 'release',
                        'status' => 'approved',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by_user_id' => request()->user()->id,
                        'approved_by_user_id' => request()->user()->id,
                        'approved_at' => now(),
                        'note' => 'إرجاع مبلغ طلب مرفوض',
                    ], false);

                    $order->released_at = now();
                }
            }

            $order->status = $newStatus;
            $order->admin_note = request('admin_note');
            $order->handled_by_user_id = request()->user()->id;
            $order->handled_at = now();
            $order->save();

            if ($oldStatus !== $newStatus) {
                $statusChanged = true;

                $message = 'تم تغيير حالة الطلب.';
                if ($newStatus === Order::STATUS_PROCESSING) {
                    $message = 'تم بدء تنفيذ الطلب.';
                } elseif ($newStatus === Order::STATUS_DONE) {
                    $message = 'تم تنفيذ الطلب وتأكيد الخصم.';
                } elseif ($newStatus === Order::STATUS_REJECTED) {
                    $message = 'تم رفض الطلب وإرجاع الرصيد.';
                }

                OrderEvent::create([
                    'order_id' => $order->id,
                    'type' => 'status_changed',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'message' => $message,
                    'meta' => [
                        'amount_held' => $order->amount_held,
                        'admin_note' => $order->admin_note,
                        'settled_at' => $order->settled_at,
                        'released_at' => $order->released_at,
                    ],
                    'actor_user_id' => request()->user()->id,
                ]);
            }
        });

        DB::afterCommit(function () use ($order, $statusChanged, $oldStatus, $newStatus): void {
            if (! $statusChanged) {
                return;
            }

            $order->load(['service', 'user']);
            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $newStatus));
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('status', 'تم تحديث حالة الطلب.');
    }
}
