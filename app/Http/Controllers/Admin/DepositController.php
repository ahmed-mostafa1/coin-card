<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveDepositRequest;
use App\Http\Requests\RejectDepositRequest;
use App\Models\DepositRequest;
use App\Notifications\DepositStatusChangedNotification;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DepositController extends Controller
{
    public function index(): View
    {
        $deposits = DepositRequest::query()
            ->with(['user', 'paymentMethod'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('q'), function ($query, $term) {
                $query->whereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.deposits.index', compact('deposits'));
    }

    public function show(DepositRequest $depositRequest): View
    {
        $depositRequest->load(['user', 'paymentMethod', 'evidence']);

        return view('admin.deposits.show', compact('depositRequest'));
    }

    public function approve(ApproveDepositRequest $request, DepositRequest $depositRequest, WalletService $walletService): RedirectResponse
    {
        if ($depositRequest->status !== DepositRequest::STATUS_PENDING) {
            return back()->withErrors(['status' => 'تمت مراجعة الطلب مسبقاً.']);
        }

        $updated = false;

        DB::transaction(function () use ($request, $depositRequest, $walletService, &$updated) {
            $deposit = DepositRequest::whereKey($depositRequest->id)->lockForUpdate()->firstOrFail();

            if ($deposit->status !== DepositRequest::STATUS_PENDING) {
                return;
            }

            $approvedAmount = (string) $request->input('approved_amount');

            $deposit->update([
                'status' => DepositRequest::STATUS_APPROVED,
                'approved_amount' => $approvedAmount,
                'admin_note' => $request->string('admin_note')->toString(),
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $wallet = $deposit->user->wallet()->firstOrCreate([]);

            $walletService->credit($wallet, $approvedAmount, [
                'type' => 'deposit',
                'status' => 'approved',
                'reference_type' => 'deposit_request',
                'reference_id' => $deposit->id,
                'created_by_user_id' => $deposit->user_id,
                'approved_by_user_id' => $request->user()->id,
                'approved_at' => now(),
                'note' => $request->string('admin_note')->toString(),
            ], false);

            $updated = true;
        });

        $depositRequest->refresh();
        $depositRequest->load('user');

        DB::afterCommit(function () use ($depositRequest, $updated): void {
            if (! $updated) {
                return;
            }

            $depositRequest->user->notify(new DepositStatusChangedNotification($depositRequest));
        });

        return redirect()->route('admin.deposits.show', $depositRequest)
            ->with('status', 'تم اعتماد طلب الشحن بنجاح.');
    }

    public function reject(RejectDepositRequest $request, DepositRequest $depositRequest): RedirectResponse
    {
        if ($depositRequest->status !== DepositRequest::STATUS_PENDING) {
            return back()->withErrors(['status' => 'تمت مراجعة الطلب مسبقاً.']);
        }

        $updated = false;

        $depositRequest->update([
            'status' => DepositRequest::STATUS_REJECTED,
            'admin_note' => $request->string('admin_note')->toString(),
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        $updated = true;

        $depositRequest->load('user');

        DB::afterCommit(function () use ($depositRequest, $updated): void {
            if (! $updated) {
                return;
            }

            $depositRequest->user->notify(new DepositStatusChangedNotification($depositRequest));
        });

        return redirect()->route('admin.deposits.show', $depositRequest)
            ->with('status', 'تم رفض طلب الشحن.');
    }

    public function downloadEvidence(DepositRequest $depositRequest): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $depositRequest->load('evidence');

        if (! $depositRequest->evidence) {
            abort(404);
        }

        return Storage::disk('local')->response($depositRequest->evidence->file_path);
    }
}
