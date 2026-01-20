<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceFormFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:text,select'],
            'label' => ['required', 'string', 'max:255'],
            'name_key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'validation_rules' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'يرجى اختيار نوع الحقل.',
            'label.required' => 'يرجى إدخال عنوان الحقل.',
            'name_key.required' => 'يرجى إدخال مفتاح الحقل.',
            'name_key.regex' => 'مفتاح الحقل يجب أن يكون بحروف لاتينية وأرقام وشرطة سفلية فقط.',
        ];
    }
}
