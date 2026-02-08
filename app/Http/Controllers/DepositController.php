<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\DepositEvidence;
use App\Models\DepositRequest;
use App\Models\PaymentMethod;
use App\Notifications\NewDepositRequestNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('deposits.index', compact('methods'));
    }

    public function show(PaymentMethod $paymentMethod): View
    {
        abort_unless($paymentMethod->is_active, 404);

        return view('deposits.show', compact('paymentMethod'));
    }

    public function store(
        StoreDepositRequest $request,
        PaymentMethod $paymentMethod,
        NotificationService $notificationService
    ): RedirectResponse
    {
        abort_unless($paymentMethod->is_active, 404);

        $user = $request->user();
        $paymentMethod->loadMissing('fields');

        $file = $request->file('proof');
        $fileHash = hash_file('sha256', $file->getRealPath());

        $deposit = null;

        $payload = [];
        foreach ($paymentMethod->fields as $field) {
            $payload[$field->name_key] = $request->input('fields.'.$field->name_key);
        }

        DB::transaction(function () use ($request, $paymentMethod, $user, $file, $fileHash, $payload, &$deposit) {
            $deposit = DepositRequest::create([
                'user_id' => $user->id,
                'payment_method_id' => $paymentMethod->id,
                'user_amount' => (string) $request->input('amount'),
                'status' => DepositRequest::STATUS_PENDING,
                'payload' => $payload,
            ]);

            $path = $file->store('deposit-evidences/'.$user->id, 'local');

            DepositEvidence::create([
                'deposit_request_id' => $deposit->id,
                'file_path' => $path,
                'file_hash' => $fileHash,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        });

        DB::afterCommit(function () use ($deposit, $notificationService): void {
            if (! $deposit) {
                return;
            }

            $deposit->load('user');
            $deposit->user->notify(new \App\Notifications\UserDepositRequestCreatedNotification($deposit));
            $notificationService->notifyAdmins(new NewDepositRequestNotification($deposit));
        });

        return redirect()
            ->route('account.deposits')
            ->with('status', 'تم إرسال طلب الشحن بنجاح. سيتم مراجعته من الإدارة.');
    }
}
