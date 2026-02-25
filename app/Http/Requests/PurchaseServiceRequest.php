<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Services\ServiceFormValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Service $service */
        $service = $this->route('service');
        $isDiscountedInput = $service->isDiscountedInputPricing();

        $rules = app(ServiceFormValidator::class)->rules($service);

        $variantRule = Rule::exists('service_variants', 'id')
            ->where('service_id', $service->id)
            ->where('is_active', true);

        if ($isDiscountedInput) {
            $rules['variant_id'] = ['nullable', 'integer', $variantRule];
        } elseif ($service->variants()->where('is_active', true)->exists()) {
            $rules['variant_id'] = ['required', 'integer', $variantRule];
        } else {
            $rules['variant_id'] = ['nullable', 'integer', $variantRule];
        }

        $rules['selected_price'] = ['nullable', 'numeric', 'min:0'];

        if ($isDiscountedInput) {
            $rules['offer_image'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
            $rules['offer_amount'] = ['required', 'numeric', 'min:0', 'decimal:0,2'];
            $rules['selected_price'] = ['required', 'numeric', 'min:0', 'decimal:0,2'];
        } elseif ($service->is_quantity_based) {
            $rules['quantity'] = [
                'required', 
                'integer', 
                'min:' . ($service->min_quantity ?? 1),
                $service->max_quantity ? 'max:' . $service->max_quantity : ''
            ];
            // Filter out empty rules
            $rules['quantity'] = array_filter($rules['quantity']);
        }

        return $rules;
    }
}
