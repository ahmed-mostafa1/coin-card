<?php

namespace App\Http\Requests;

use App\Models\DepositRequest as DepositRequestModel;
use App\Services\DepositQuoteService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreDepositRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('currency_id')) {
            return;
        }

        $paymentMethod = $this->route('paymentMethod');
        $currencyId = $paymentMethod?->currencyConfigs()
            ->where('is_enabled', true)
            ->whereHas('currency', fn ($query) => $query->where('is_enabled', true))
            ->orderBy('sort_order')
            ->value('currency_id');

        if (! $currencyId) {
            $currencyId = \App\Models\Currency::query()->where('code', 'USD')->where('is_enabled', true)->value('id');
        }

        if ($currencyId) {
            $this->merge(['currency_id' => $currencyId]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'amount' => [
                'required',
                'numeric',
                'min:'.config('coin-card.deposit_min_amount'),
                'max:'.config('coin-card.deposit_max_amount'),
            ],
            'currency_id' => ['required', 'integer'],
            'proof' => [
                ($this->route('paymentMethod')?->require_transfer_proof ?? true) ? 'required' : 'nullable',
                'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120',
            ],
        ];

        $paymentMethod = $this->route('paymentMethod');
        if ($paymentMethod) {
            $paymentMethod->loadMissing('fields');
            if ($paymentMethod->fields->isNotEmpty()) {
                $rules['fields'] = ['array'];
                foreach ($paymentMethod->fields as $field) {
                    $fieldRules = ['nullable', 'string', 'max:1000'];
                    if ($field->is_required) {
                        $fieldRules[0] = 'required';
                    }
                    $rules['fields.'.$field->name_key] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'يرجى إدخال المبلغ المحول.',
            'amount.numeric' => 'يرجى إدخال مبلغ صالح.',
            'amount.min' => 'الحد الأدنى للمبلغ هو '.config('coin-card.deposit_min_amount').'.',
            'amount.max' => 'الحد الأقصى للمبلغ هو '.config('coin-card.deposit_max_amount').'.',
            'currency_id.required' => 'يرجى اختيار العملة.',
            'proof.required' => 'يرجى رفع إثبات التحويل.',
            'proof.mimes' => 'ملف الإثبات يجب أن يكون صورة أو PDF.',
            'proof.max' => 'حجم الملف كبير جداً.',
        ];
    }

    protected function passedValidation(): void
    {
        $paymentMethod = $this->route('paymentMethod');
        if ($paymentMethod) {
            $quoteService = app(DepositQuoteService::class);
            $config = $quoteService->enabledConfigFor($paymentMethod, $this->input('currency_id'));
            $quoteService->quote($config, $this->input('amount'));
        }

        $pending = DepositRequestModel::query()
            ->where('user_id', $this->user()->id)
            ->where('status', DepositRequestModel::STATUS_PENDING)
            ->count();

        if ($pending >= config('coin-card.max_pending_deposits')) {
            throw ValidationException::withMessages([
                'amount' => 'لديك طلبات شحن معلّقة كثيرة. يرجى انتظار مراجعتها أولاً.',
            ]);
        }
    }
}
