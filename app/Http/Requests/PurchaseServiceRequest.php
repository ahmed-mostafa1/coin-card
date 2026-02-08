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

        $rules = app(ServiceFormValidator::class)->rules($service);

        $variantsQuery = $service->variants()->where('is_active', true);

        $variantRule = Rule::exists('service_variants', 'id')
            ->where('service_id', $service->id)
            ->where('is_active', true);

        if ($variantsQuery->exists()) {
            $rules['variant_id'] = ['required', 'integer', $variantRule];
        } else {
            $rules['variant_id'] = ['nullable', 'integer', $variantRule];
        }

        // Add selected_price validation
        $rules['selected_price'] = ['nullable', 'numeric', 'min:0'];
        
        if ($service->is_quantity_based) {
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
