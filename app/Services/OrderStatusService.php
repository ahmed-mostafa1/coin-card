<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly VipService $vipService
    ) {
    }

    public function updateStatus(Order $order, string $newStatus, ?string $adminNote, User $actor): array
    {
        $statusChanged = false;
        $oldStatus = null;
        $shouldUpdateVip = false;

        DB::transaction(function () use ($order, $newStatus, $adminNote, $actor, &$statusChanged, &$oldStatus, &$shouldUpdateVip) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
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

                    $this->walletService->settleHeldAmount($wallet, (string) $order->amount_held, [
                        'type' => 'settle',
                        'status' => 'approved',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by_user_id' => $actor->id,
                        'approved_by_user_id' => $actor->id,
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

                    $this->walletService->releaseHeldAmount($wallet, (string) $order->amount_held, [
                        'type' => 'release',
                        'status' => 'approved',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by_user_id' => $actor->id,
                        'approved_by_user_id' => $actor->id,
                        'approved_at' => now(),
                        'note' => 'إرجاع مبلغ طلب مرفوض',
                    ], false);

                    $order->released_at = now();
                }
            }

            $order->status = $newStatus;
            $order->admin_note = $adminNote;
            $order->handled_by_user_id = $actor->id;
            $order->handled_at = now();
            $order->save();

            if ($oldStatus !== $newStatus) {
                $statusChanged = true;
                if ($newStatus === Order::STATUS_DONE) {
                    $shouldUpdateVip = true;
                }

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
                    'actor_user_id' => $actor->id,
                ]);
            }
        });

        DB::afterCommit(function () use ($order, $statusChanged, $oldStatus, $newStatus, $shouldUpdateVip): void {
            if (! $statusChanged) {
                return;
            }

            $order->refresh();
            $order->load(['service', 'user']);
            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $newStatus));

            if ($shouldUpdateVip && $newStatus === Order::STATUS_DONE) {
                $this->vipService->updateUserVipStatus($order->user);
            }
        });

        return [
            'statusChanged' => $statusChanged,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
        ];
    }
}
