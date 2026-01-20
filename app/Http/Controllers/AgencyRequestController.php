<?php

namespace App\Http\Controllers;

use App\Models\AgencyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyRequestController extends Controller
{
    public function create(): View
    {
        return view('agency-requests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'min:6', 'max:30'],
            'full_name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'starting_amount' => ['required', 'numeric', 'min:0'],
        ], [
            'contact_number.required' => 'يرجى إدخال رقم للتواصل.',
            'contact_number.min' => 'رقم التواصل قصير جدًا.',
            'contact_number.max' => 'رقم التواصل طويل جدًا.',
            'full_name.required' => 'يرجى إدخال الاسم الثلاثي.',
            'full_name.max' => 'الاسم طويل جدًا.',
            'region.required' => 'يرجى إدخال المنطقة.',
            'region.max' => 'المنطقة طويلة جدًا.',
            'starting_amount.required' => 'يرجى إدخال المبلغ الذي تستطيع بدأ العمل به.',
            'starting_amount.numeric' => 'يرجى إدخال مبلغ صحيح.',
            'starting_amount.min' => 'يجب أن يكون المبلغ صفر أو أكثر.',
        ]);

        AgencyRequest::create($validated);

        return redirect()
            ->route('agency-requests.create')
            ->with('status', 'تم إرسال طلبك بنجاح.');
    }
}
