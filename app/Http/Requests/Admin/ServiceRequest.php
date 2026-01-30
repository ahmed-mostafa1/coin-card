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
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', Rule::unique('services', 'slug')->ignore($serviceId)],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0.01'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.sort_order' => ['nullable', 'integer', 'min:0'],
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
            'category_id.required' => 'يرجى اختيار التصنيف.',
            'name.required' => 'يرجى إدخال اسم الخدمة.',
            'slug.regex' => 'المعرف المختصر يجب أن يكون بحروف لاتينية وأرقام وشرطات فقط.',
            'price.required' => 'يرجى إدخال سعر الخدمة.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة jpg أو png أو webp.',
        ];
    }
}
