<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', Rule::unique('services', 'slug')->ignore($serviceId)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'يرجى اختيار التصنيف.',
            'name.required' => 'يرجى إدخال اسم الخدمة.',
            'slug.regex' => 'المعرف المختصر يجب أن يكون بحروف لاتينية وأرقام وشرطات فقط.',
            'price.required' => 'يرجى إدخال سعر الخدمة.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة jpg أو png أو webp.',
        ];
    }
}
