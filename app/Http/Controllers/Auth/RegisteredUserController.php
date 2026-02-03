<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $user->assignRole('customer');

        event(new Registered($user));

        Auth::login($user);

        // Send OTP Email
        \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\OtpMail($otp));

        // Set session to show OTP popup
        session(['show_otp_verify' => true]);

        return redirect()->route('home');
    }
}
