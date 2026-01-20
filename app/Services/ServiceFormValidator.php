<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceFormField;
use Illuminate\Validation\Rule;

class ServiceFormValidator
{
    public function rules(Service $service): array
    {
        $rules = [];

        $fields = $service->formFields()
            ->with('options')
            ->orderBy('sort_order')
            ->get();

        foreach ($fields as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->type === ServiceFormField::TYPE_TEXT) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:255';
            }

            if ($field->type === ServiceFormField::TYPE_SELECT) {
                $allowed = $field->options->pluck('value')->all();
                $fieldRules[] = Rule::in($allowed);
            }

            if ($field->validation_rules) {
                $fieldRules = array_merge($fieldRules, explode('|', $field->validation_rules));
            }

            $rules['fields.'.$field->name_key] = $fieldRules;
        }

        return $rules;
    }
}
