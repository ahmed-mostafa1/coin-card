<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.payment-methods.index', compact('methods'));
    }

    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('payment-methods/icons', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'تم إضافة طريقة الدفع بنجاح.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('icon')) {
            if ($paymentMethod->icon_path) {
                Storage::disk('public')->delete($paymentMethod->icon_path);
            }

            $data['icon_path'] = $request->file('icon')->store('payment-methods/icons', 'public');
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'تم تحديث طريقة الدفع بنجاح.');
    }
}
