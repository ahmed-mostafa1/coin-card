<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
        $currencies = Currency::query()->orderBy('sort_order')->orderBy('code')->get();

        return view('admin.payment-methods.create', compact('currencies'));
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['show_account_number'] = $request->boolean('show_account_number');
        $data['show_contact_button'] = $request->boolean('show_contact_button');
        $data['require_transfer_proof'] = $request->boolean('require_transfer_proof');

        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('payment-methods/icons', 'public');
        }

        DB::transaction(function () use ($request, $data): void {
            $paymentMethod = PaymentMethod::create($data);
            $this->syncFields($paymentMethod, $request->input('fields', []));
            $this->syncCurrencies($paymentMethod, $request->input('currencies', []));
        });

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'تم إضافة طريقة الدفع بنجاح.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        $paymentMethod->load(['fields', 'currencyConfigs.currency']);
        $currencies = Currency::query()->orderBy('sort_order')->orderBy('code')->get();

        return view('admin.payment-methods.edit', compact('paymentMethod', 'currencies'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['show_account_number'] = $request->boolean('show_account_number');
        $data['show_contact_button'] = $request->boolean('show_contact_button');
        $data['require_transfer_proof'] = $request->boolean('require_transfer_proof');

        if ($request->hasFile('icon')) {
            if ($paymentMethod->icon_path) {
                Storage::disk('public')->delete($paymentMethod->icon_path);
            }

            $data['icon_path'] = $request->file('icon')->store('payment-methods/icons', 'public');
        }

        DB::transaction(function () use ($request, $paymentMethod, $data): void {
            $paymentMethod->update($data);
            $this->syncFields($paymentMethod, $request->input('fields', []));
            $this->syncCurrencies($paymentMethod, $request->input('currencies', []));
        });

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'تم تحديث طريقة الدفع بنجاح.');
    }

    private function syncFields(PaymentMethod $paymentMethod, array $fields): void
    {
        $paymentMethod->fields()->delete();

        foreach ($fields as $index => $field) {
            $paymentMethod->fields()->create([
                'type' => $field['type'],
                'label' => $field['label'],
                'label_en' => $field['label_en'] ?? null,
                'name_key' => $field['name_key'],
                'is_required' => isset($field['is_required']) ? (bool) $field['is_required'] : false,
                'sort_order' => $field['sort_order'] ?? $index,
            ]);
        }
    }

    private function syncCurrencies(PaymentMethod $paymentMethod, array $currencies): void
    {
        $enabledIds = [];

        if ($currencies === []) {
            $usd = Currency::query()->where('code', 'USD')->first();
            if ($usd) {
                $currencies[$usd->id] = [
                    'enabled' => true,
                    'is_enabled' => true,
                    'commission_type' => 'percentage',
                    'commission_value' => 0,
                    'sort_order' => 0,
                ];
            }
        }

        foreach ($currencies as $currencyId => $config) {
            if (empty($config['enabled'])) {
                continue;
            }

            $enabledIds[] = (int) $currencyId;
            $paymentMethod->currencyConfigs()->updateOrCreate(
                ['currency_id' => (int) $currencyId],
                [
                    'exchange_rate_override' => $config['exchange_rate_override'] ?? null,
                    'commission_type' => in_array($config['commission_type'] ?? 'percentage', ['percentage', 'fixed'], true) ? $config['commission_type'] : 'percentage',
                    'commission_value' => $config['commission_value'] ?? 0,
                    'min_amount' => $config['min_amount'] ?? null,
                    'max_amount' => $config['max_amount'] ?? null,
                    'is_enabled' => ! empty($config['is_enabled']),
                    'sort_order' => $config['sort_order'] ?? 0,
                ]
            );
        }

        $paymentMethod->currencyConfigs()
            ->whereNotIn('currency_id', $enabledIds ?: [0])
            ->update(['is_enabled' => false]);
    }

}
