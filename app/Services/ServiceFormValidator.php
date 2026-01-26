<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceFormField;

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

            if ($field->type === ServiceFormField::TYPE_TEXTAREA) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:1000';
            }

            if ($field->validation_rules) {
                $extra = array_filter(explode('|', $field->validation_rules), function ($rule) {
                    // Accept only Laravel-ish rule tokens (alpha/underscore + optional params) to avoid calling non-existent validators.
                    return (bool) preg_match('/^[a-zA-Z][a-zA-Z0-9_]*(?::.*)?$/', trim($rule));
                });
                $fieldRules = array_merge($fieldRules, $extra);
            }

            $rules['fields.'.$field->name_key] = $fieldRules;
        }

        return $rules;
    }
}
