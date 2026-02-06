<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyRequestField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyRequestFieldController extends Controller
{
    public function index(): View
    {
        $fields = AgencyRequestField::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.agency-request-fields.index', compact('fields'));
    }

    public function create(): View
    {
        return view('admin.agency-request-fields.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'name_key' => 'required|string|max:255|unique:agency_request_fields,name_key',
            'type' => 'required|in:text,textarea,number,email,tel',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_required'] = $request->boolean('is_required');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        AgencyRequestField::create($validated);

        return redirect()->route('admin.agency-request-fields.index')
            ->with('status', 'تم إضافة الحقل بنجاح.');
    }

    public function edit(AgencyRequestField $field): View
    {
        return view('admin.agency-request-fields.edit', compact('field'));
    }

    public function update(Request $request, AgencyRequestField $field): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'name_key' => 'required|string|max:255|unique:agency_request_fields,name_key,' . $field->id,
            'type' => 'required|in:text,textarea,number,email,tel',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_required'] = $request->boolean('is_required');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $field->update($validated);

        return redirect()->route('admin.agency-request-fields.index')
            ->with('status', 'تم تحديث الحقل بنجاح.');
    }

    public function destroy(AgencyRequestField $field): RedirectResponse
    {
        $field->delete();

        return redirect()->route('admin.agency-request-fields.index')
            ->with('status', 'تم حذف الحقل بنجاح.');
    }
}
