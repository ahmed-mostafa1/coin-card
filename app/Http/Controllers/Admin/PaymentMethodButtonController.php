<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodButton;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodButtonController extends Controller
{
    public function create(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.buttons.create', compact('paymentMethod'));
    }

    public function store(PaymentMethod $paymentMethod, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label_ar'   => ['required', 'string', 'max:120'],
            'label_en'   => ['nullable', 'string', 'max:120'],
            'url'        => ['required', 'url', 'max:500'],
            'bg_color'   => ['required', 'regex:/^#[0-9a-fA-F]{3,6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $paymentMethod->buttons()->create($data);

        return redirect()->route('admin.payment-methods.edit', $paymentMethod)
            ->with('status', __('messages.button_created'));
    }

    public function edit(PaymentMethod $paymentMethod, PaymentMethodButton $button): View
    {
        abort_if($button->payment_method_id !== $paymentMethod->id, 404);

        return view('admin.payment-methods.buttons.edit', compact('paymentMethod', 'button'));
    }

    public function update(PaymentMethod $paymentMethod, PaymentMethodButton $button, Request $request): RedirectResponse
    {
        abort_if($button->payment_method_id !== $paymentMethod->id, 404);

        $data = $request->validate([
            'label_ar'   => ['required', 'string', 'max:120'],
            'label_en'   => ['nullable', 'string', 'max:120'],
            'url'        => ['required', 'url', 'max:500'],
            'bg_color'   => ['required', 'regex:/^#[0-9a-fA-F]{3,6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $button->update($data);

        return redirect()->route('admin.payment-methods.edit', $paymentMethod)
            ->with('status', __('messages.button_updated'));
    }

    public function destroy(PaymentMethod $paymentMethod, PaymentMethodButton $button): RedirectResponse
    {
        abort_if($button->payment_method_id !== $paymentMethod->id, 404);

        $button->delete();

        return redirect()->route('admin.payment-methods.edit', $paymentMethod)
            ->with('status', __('messages.button_deleted'));
    }
}
