<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        $currencies = Currency::query()->orderBy('sort_order')->orderBy('code')->get();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Currency::create($this->validated($request));

        return redirect()->route('admin.currencies.index')->with('status', 'تمت إضافة العملة بنجاح.');
    }

    public function edit(Currency $currency): View
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $currency->update($this->validated($request, $currency));

        return redirect()->route('admin.currencies.index')->with('status', 'تم تحديث العملة بنجاح.');
    }

    private function validated(Request $request, ?Currency $currency = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z]{2,10}$/', Rule::unique('currencies', 'code')->ignore($currency?->id)],
            'symbol' => ['nullable', 'string', 'max:10'],
            'exchange_rate_to_usd' => ['required', 'numeric', 'gt:0'],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_enabled'] = $request->boolean('is_enabled');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
