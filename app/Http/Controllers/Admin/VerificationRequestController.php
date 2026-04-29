<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VerificationRequestController extends Controller
{
    public function index(): View
    {
        $requests = VerificationRequest::query()->with('user')->latest()->paginate(20);
        return view('admin.verification-requests.index', compact('requests'));
    }

    public function show(VerificationRequest $verificationRequest): View
    {
        $verificationRequest->load(['user', 'files.field', 'reviewedBy']);
        return view('admin.verification-requests.show', compact('verificationRequest'));
    }

    public function approve(Request $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $data = $request->validate([
            'assigned_discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $discount = (float) ($data['assigned_discount_percentage'] ?? 0);
        $verificationRequest->update([
            'status' => VerificationRequest::STATUS_APPROVED,
            'assigned_discount_percentage' => $discount,
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $verificationRequest->user->forceFill([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_discount_percentage' => $discount,
        ])->save();

        app(\App\Services\SecurityLogger::class)->log('verification_approved', $verificationRequest->user, $request, [
            'verification_request_id' => $verificationRequest->id,
            'assigned_discount_percentage' => $discount,
        ], $verificationRequest, $request->user());

        return redirect()->route('admin.verification-requests.show', $verificationRequest)->with('status', 'تم اعتماد التوثيق.');
    }

    public function updateStatus(Request $request, VerificationRequest $verificationRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:rejected,needs_changes'],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $verificationRequest->update([
            'status' => $data['status'],
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        app(\App\Services\SecurityLogger::class)->log('verification_'.$data['status'], $verificationRequest->user, $request, [
            'verification_request_id' => $verificationRequest->id,
            'status' => $data['status'],
        ], $verificationRequest, $request->user());

        return redirect()->route('admin.verification-requests.show', $verificationRequest)->with('status', 'تم تحديث حالة الطلب.');
    }

    public function downloadFile(VerificationRequest $verificationRequest, int $fileId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $file = $verificationRequest->files()->whereKey($fileId)->firstOrFail();
        return Storage::disk('local')->response($file->file_path);
    }
}
