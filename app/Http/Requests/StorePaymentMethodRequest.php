<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:payment_methods,slug'],
            'instructions' => ['required', 'string'],
            'instructions_en' => ['nullable', 'string'],
            'account_number' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'fields' => ['nullable', 'array'],
            'fields.*.label' => ['required_with:fields', 'string', 'max:255'],
            'fields.*.name_key' => ['required_with:fields', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'distinct'],
            'fields.*.type' => ['required_with:fields', 'in:text,textarea'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم الطريقة.',
            'slug.required' => 'يرجى إدخال المعرف المختصر.',
            'slug.regex' => 'المعرف المختصر يجب أن يكون بحروف لاتينية وأرقام وشرطات فقط.',
            'instructions.required' => 'يرجى إدخال تعليمات التحويل.',
            'account_number.required' => 'يرجى إدخال رقم المحفظة أو الحساب.',
            'icon.mimes' => 'الأيقونة يجب أن تكون صورة صالحة.',
        ];
    }
}
