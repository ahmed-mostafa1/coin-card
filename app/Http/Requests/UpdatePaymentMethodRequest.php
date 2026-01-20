<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $methodId = $this->route('paymentMethod')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:payment_methods,slug,'.$methodId],
            'instructions' => ['required', 'string'],
            'icon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم الطريقة.',
            'slug.required' => 'يرجى إدخال المعرف المختصر.',
            'slug.regex' => 'المعرف المختصر يجب أن يكون بحروف لاتينية وأرقام وشرطات فقط.',
            'instructions.required' => 'يرجى إدخال تعليمات التحويل.',
            'icon.mimes' => 'الأيقونة يجب أن تكون صورة صالحة.',
        ];
    }
}
