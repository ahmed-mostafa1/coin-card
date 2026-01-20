<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم التصنيف.',
            'slug.regex' => 'المعرف المختصر يجب أن يكون بحروف لاتينية وأرقام وشرطات فقط.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة jpg أو png أو webp.',
        ];
    }
}
