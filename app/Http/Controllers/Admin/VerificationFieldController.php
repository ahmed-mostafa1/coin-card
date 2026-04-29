<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VerificationFieldController extends Controller
{
    public function index(): View
    {
        $fields = VerificationField::query()->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.verification-fields.index', compact('fields'));
    }

    public function create(): View
    {
        return view('admin.verification-fields.create', ['field' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        VerificationField::create($this->validated($request));
        return redirect()->route('admin.verification-fields.index')->with('status', 'تم إضافة الحقل.');
    }

    public function edit(VerificationField $verificationField): View
    {
        return view('admin.verification-fields.edit', ['field' => $verificationField]);
    }

    public function update(Request $request, VerificationField $verificationField): RedirectResponse
    {
        $verificationField->update($this->validated($request, $verificationField));
        return redirect()->route('admin.verification-fields.index')->with('status', 'تم تحديث الحقل.');
    }

    public function destroy(VerificationField $verificationField): RedirectResponse
    {
        $verificationField->delete();
        return redirect()->route('admin.verification-fields.index')->with('status', 'تم حذف الحقل.');
    }

    private function validated(Request $request, ?VerificationField $field = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'name_key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', Rule::unique('verification_fields', 'name_key')->ignore($field?->id)],
            'type' => ['required', Rule::in(['text', 'textarea', 'number', 'date', 'select', 'file', 'image', 'camera'])],
            'options_text' => ['nullable', 'string'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'placeholder_en' => ['nullable', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'is_enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $options = collect(preg_split('/\r\n|\r|\n/', (string) ($data['options_text'] ?? '')))
            ->map(fn ($option) => trim($option))
            ->filter()
            ->values()
            ->all();

        unset($data['options_text']);
        $data['options'] = $options ?: null;
        $data['is_required'] = $request->boolean('is_required');
        $data['is_enabled'] = $request->boolean('is_enabled', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
