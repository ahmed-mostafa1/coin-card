<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_note' => ['required', 'string', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_note.required' => 'يرجى توضيح سبب الرفض.',
            'admin_note.min' => 'يرجى إدخال سبب أوضح.',
        ];
    }
}
