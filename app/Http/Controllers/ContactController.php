<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessageToAdminMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $admins = User::role('admin')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get(['id', 'name', 'email']);

        if ($admins->isEmpty()) {
            Log::warning('Contact form submitted with no admin recipients configured.');

            return back()
                ->withInput()
                ->withErrors(['contact' => __('contact.unavailable')]);
        }

        $payload = $request->safe()->only(['name', 'email', 'subject', 'message']);
        $submittedBy = $request->user();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ContactMessageToAdminMail(
                payload: $payload,
                recipientName: $admin->name,
                submittedBy: $submittedBy,
            ));
        }

        return redirect()
            ->route('contact-us.show')
            ->with('status', __('contact.success'));
    }
}
