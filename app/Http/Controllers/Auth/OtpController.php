<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Services\MailDeliveryService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $user = auth()->user();

        if (!$user->otp_code || !$user->otp_expires_at) {
             return response()->json(['message' => __('messages.invalid_otp')], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
             return response()->json(['message' => __('messages.otp_expired')], 400);
        }

        if ($request->otp != $user->otp_code) {
             return response()->json(['message' => __('messages.invalid_otp')], 400);
        }

        // Verify user
        $user->forceFill([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ])->save();

        session()->forget('show_otp_verify');

        return response()->json(['message' => __('messages.success'), 'redirect' => route('home')]);
    }

    public function resend(Request $request, MailDeliveryService $mailDelivery)
    {
        $user = auth()->user();
        
        // Rate limiting or simple check
        if ($user->email_verified_at) {
             return response()->json(['message' => __('messages.already_verified')], 400);
        }

        $otp = rand(100000, 999999);

        if (! $mailDelivery->send($user, new OtpMail($otp), 'otp_resend', $user->id)) {
            return response()->json(['message' => __('messages.mail_delivery_unavailable')], 429);
        }

        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        return response()->json(['message' => __('messages.otp_sent')]);
    }
}
