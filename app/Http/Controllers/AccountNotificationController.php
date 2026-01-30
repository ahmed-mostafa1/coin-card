<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountNotificationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $filter = request('filter', 'all');

        $notificationsQuery = $filter === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $notificationsQuery->latest()->paginate(15)->withQueryString();

        return view('account.notifications.index', compact('notifications', 'filter'));
    }

    public function markAllRead(): RedirectResponse
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return redirect()->route('account.notifications')->with('status', 'تم تعليم جميع الإشعارات كمقروءة.');
    }

    public function markAsRead(string $id): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
