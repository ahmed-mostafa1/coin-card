<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Services\ServiceFormValidator;
use Illuminate\Foundation\Http\FormRequest;

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

        return app(ServiceFormValidator::class)->rules($service);
    }
}
