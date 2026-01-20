<?php

namespace App\Http\Requests;

use App\Models\DepositRequest as DepositRequestModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:'.config('coin-card.deposit_min_amount'),
                'max:'.config('coin-card.deposit_max_amount'),
            ],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'يرجى إدخال المبلغ المحول.',
            'amount.numeric' => 'يرجى إدخال مبلغ صالح.',
            'amount.min' => 'الحد الأدنى للمبلغ هو '.config('coin-card.deposit_min_amount').'.',
            'amount.max' => 'الحد الأقصى للمبلغ هو '.config('coin-card.deposit_max_amount').'.',
            'proof.required' => 'يرجى رفع إثبات التحويل.',
            'proof.mimes' => 'ملف الإثبات يجب أن يكون صورة أو PDF.',
            'proof.max' => 'حجم الملف كبير جداً.',
        ];
    }

    protected function passedValidation(): void
    {
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
