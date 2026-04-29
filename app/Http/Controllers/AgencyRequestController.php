<?php

namespace App\Http\Controllers;

use App\Models\AgencyRequest;
use App\Models\AgencyRequestField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyRequestController extends Controller
{
    public function create(): View
    {
        $fields = AgencyRequestField::orderBy('sort_order')->orderBy('id')->get();
        if ($fields->isEmpty()) {
            $fields = collect([
                (object) ['name_key' => 'contact_number', 'type' => 'tel', 'localized_label' => 'رقم التواصل', 'localized_placeholder' => '', 'is_required' => true],
                (object) ['name_key' => 'full_name', 'type' => 'text', 'localized_label' => 'الاسم الكامل', 'localized_placeholder' => '', 'is_required' => true],
                (object) ['name_key' => 'region', 'type' => 'text', 'localized_label' => 'المنطقة', 'localized_placeholder' => '', 'is_required' => true],
                (object) ['name_key' => 'starting_amount', 'type' => 'number', 'localized_label' => 'المبلغ المبدئي', 'localized_placeholder' => '', 'is_required' => true],
            ]);
        }
        return view('agency-requests.create', compact('fields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $fields = AgencyRequestField::orderBy('sort_order')->orderBy('id')->get();
        if ($fields->isEmpty()) {
            $fields = collect([
                (object) ['name_key' => 'contact_number', 'type' => 'tel', 'is_required' => true],
                (object) ['name_key' => 'full_name', 'type' => 'text', 'is_required' => true],
                (object) ['name_key' => 'region', 'type' => 'text', 'is_required' => true],
                (object) ['name_key' => 'starting_amount', 'type' => 'number', 'is_required' => true],
            ]);
        }

        $rules = [];
        foreach ($fields as $field) {
            $rule = [$field->is_required ? 'required' : 'nullable', 'string', 'max:1000'];
            if ($field->type === 'number') {
                $rule = [$field->is_required ? 'required' : 'nullable', 'numeric'];
            } elseif ($field->type === 'email') {
                $rule = [$field->is_required ? 'required' : 'nullable', 'email', 'max:255'];
            } elseif ($field->type === 'tel') {
                $rule = [$field->is_required ? 'required' : 'nullable', 'string', 'max:30'];
            }
            $rules[$field->name_key] = $rule;
        }

        $validated = $request->validate($rules);

        AgencyRequest::create([
            'payload' => $validated,
            'contact_number' => $validated['contact_number'] ?? null,
            'full_name' => $validated['full_name'] ?? null,
            'region' => $validated['region'] ?? null,
            'starting_amount' => $validated['starting_amount'] ?? null,
        ]);

        return redirect()
            ->route('agency-requests.create')
            ->with('status', 'تم إرسال طلبك بنجاح.');
    }
}
