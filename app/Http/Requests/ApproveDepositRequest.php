<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approved_amount' => [
                'required',
                'numeric',
                'min:'.config('coin-card.deposit_min_amount'),
                'max:'.config('coin-card.deposit_max_amount'),
            ],
            'admin_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'approved_amount.required' => 'يرجى إدخال المبلغ المعتمد.',
            'approved_amount.numeric' => 'يرجى إدخال مبلغ صالح.',
            'approved_amount.min' => 'الحد الأدنى للمبلغ هو '.config('coin-card.deposit_min_amount').'.',
            'approved_amount.max' => 'الحد الأقصى للمبلغ هو '.config('coin-card.deposit_max_amount').'.',
        ];
    }
}
