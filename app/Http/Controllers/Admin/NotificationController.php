<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSentNotification;
use App\Models\User;
use App\Notifications\AdminGeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Throwable;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function create(): View
    {
        $sentNotifications = AdminSentNotification::with(['admin', 'targetUser'])->latest()->paginate(20);

        return view('admin.notifications.create', compact('sentNotifications'));
    }

    public function index(): View
    {
        $sentNotifications = AdminSentNotification::with(['admin', 'targetUser'])->latest()->paginate(30);

        return view('admin.notifications.index', compact('sentNotifications'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('notifications', 'public')
            : null;

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $history = AdminSentNotification::create([
            'admin_user_id' => $request->user()?->id,
            'scope' => AdminSentNotification::SCOPE_ALL_USERS,
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'content_ar' => $data['content_ar'],
            'content_en' => $data['content_en'],
            'image_path' => $imagePath,
            'recipient_count' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            Notification::send($users, new AdminGeneralNotification(
                $data['title_ar'],
                $data['title_en'],
                $data['content_ar'],
                $data['content_en'],
                $imagePath
            ));

            $history->update([
                'recipient_count' => $users->count(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('admin.notifications.create')
                ->with('status', 'تم حفظ الإشعار في السجل ولكن حدثت مشكلة أثناء الإرسال. يرجى المحاولة مرة أخرى.');
        }

        return redirect()->route('admin.notifications.create')
            ->with('status', 'تم إرسال الإشعار لجميع المستخدمين بنجاح.');
    }
}
