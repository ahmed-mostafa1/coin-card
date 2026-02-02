<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminGeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function create(): View
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
        ]);

        // Send to all users
        // This might take time, so ideally it should be queued.
        // For now we use Notification::send which processes them.
        // If queue is configured, Notification implements ShouldQueue logic if the notification class uses Queueable.
        // AdminGeneralNotification uses Queueable.

        Notification::send(User::all(), new AdminGeneralNotification(
            $data['title_ar'],
            $data['title_en'],
            $data['content_ar'],
            $data['content_en']
        ));

        return redirect()->route('admin.notifications.create')
            ->with('status', 'تم بدء إرسال الإشعار لجميع المستخدمين.');
    }
}
