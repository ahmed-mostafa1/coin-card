<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $wallet = $user->wallet()->firstOrCreate([]);
        $unreadNotificationsCount = auth()->user()->unreadNotifications()->count();
        $recentOrders = $user->orders()
            ->with(['service', 'variant'])
            ->latest()
            ->limit(5)
            ->get();
        $recentDeposits = $user->depositRequests()
            ->with('paymentMethod')
            ->latest()
            ->limit(5)
            ->get();

        return view('account', compact('wallet', 'unreadNotificationsCount', 'recentOrders', 'recentDeposits'));
    }

    public function update(): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'يرجى إدخال الاسم.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('account')->with('status', 'تم تحديث البيانات بنجاح.');
    }

    public function changePassword(): View
    {
        return view('account.change-password');
    }

    public function updatePassword(): RedirectResponse
    {
        $user = auth()->user();

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'يرجى إدخال الاسم.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفًا.',
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        // Update name
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Only update password if current_password is provided
        if (!empty($validated['current_password'])) {
            // Check if current password is correct
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->back()->withInput()->with('error', 'كلمة المرور الحالية غير صحيحة.');
            }

            // Validate that new password is provided
            if (empty($validated['password'])) {
                return redirect()->back()->withInput()->with('error', 'يرجى إدخال كلمة المرور الجديدة.');
            }

            // Update password
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('account')->with('status', 'تم تحديث البيانات بنجاح.');
    }
}
