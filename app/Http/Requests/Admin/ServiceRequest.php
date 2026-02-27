<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
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
        $pricingMode = $this->input('pricing_mode', Service::PRICING_MODE_FIXED);
        $isDiscountedInput = $pricingMode === Service::PRICING_MODE_DISCOUNTED_INPUT;

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('source', 'manual')->orWhereNull('source');
                    });
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', Rule::unique('services', 'slug')->ignore($serviceId)],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'additional_rules' => ['nullable', 'string'],
            'additional_rules_en' => ['nullable', 'string'],
            'pricing_mode' => ['nullable', Rule::in([Service::PRICING_MODE_FIXED, Service::PRICING_MODE_DISCOUNTED_INPUT])],
            'admin_discount_percent' => ['required_if:pricing_mode,' . Service::PRICING_MODE_DISCOUNTED_INPUT, 'nullable', 'numeric', 'between:-100,100', 'decimal:0,2'],
            'is_quantity_based' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'boolean'],
            'min_quantity' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'integer', 'min:1'],
            'max_quantity' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'integer', 'gte:min_quantity'],
            'price_per_unit' => [Rule::prohibitedIf($isDiscountedInput), 'required_if:is_quantity_based,1', 'nullable', 'numeric', 'gt:0'],
            'price' => $isDiscountedInput
                ? ['required', 'numeric', 'min:0', 'max:0']
                : ['required_unless:is_quantity_based,1', 'nullable', 'numeric', 'gt:0'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'offer_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'is_offer_active' => ['nullable', 'boolean'],
            'is_limited_offer_label_active' => ['nullable', 'boolean'],
            'limited_offer_label' => ['exclude_unless:is_limited_offer_label_active,1', 'required', 'string', 'max:120'],
            'limited_offer_label_en' => ['exclude_unless:is_limited_offer_label_active,1', 'nullable', 'string', 'max:120'],
            'is_limited_offer_countdown_active' => ['nullable', 'boolean'],
            'limited_offer_ends_at' => ['exclude_unless:is_limited_offer_countdown_active,1', 'required', 'date', 'after:now'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'variants' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'array'],
            'variants.*.name' => [Rule::prohibitedIf($isDiscountedInput), 'required_with:variants', 'string', 'max:255'],
            'variants.*.price' => [Rule::prohibitedIf($isDiscountedInput), 'required_with:variants', 'numeric', 'min:0.01'],
            'variants.*.is_active' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'boolean'],
            'variants.*.sort_order' => [Rule::prohibitedIf($isDiscountedInput), 'nullable', 'integer', 'min:0'],
            'fields' => ['nullable', 'array'],
            'fields.*.label' => ['required_with:fields', 'string', 'max:255'],
            'fields.*.label_en' => ['nullable', 'string', 'max:255'],
            'fields.*.name_key' => ['required_with:fields', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'distinct'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.placeholder_en' => ['nullable', 'string', 'max:255'],
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
            'admin_discount_percent.between' => 'نسبة الخصم يجب أن تكون بين -100 و 100.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة jpg أو png أو webp.',
            'limited_offer_label.required' => 'يرجى إدخال نص شارة العرض المحدود.',
            'limited_offer_ends_at.required' => 'يرجى تحديد موعد نهاية العد التنازلي.',
            'limited_offer_ends_at.after' => 'موعد نهاية العد التنازلي يجب أن يكون في المستقبل.',
        ];
    }
}
