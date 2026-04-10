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
        return view('agency-requests.create', compact('fields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $fields = AgencyRequestField::orderBy('sort_order')->orderBy('id')->get();

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

        AgencyRequest::create(['payload' => $validated]);

        return redirect()
            ->route('agency-requests.create')
            ->with('status', 'تم إرسال طلبك بنجاح.');
    }
}
