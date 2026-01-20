<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceFormFieldRequest;
use App\Models\Service;
use App\Models\ServiceFormField;
use App\Models\ServiceFormOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceFormFieldController extends Controller
{
    public function create(Service $service): View
    {
        return view('admin.services.fields.create', compact('service'));
    }

    public function store(ServiceFormFieldRequest $request, Service $service): RedirectResponse
    {
        $data = $request->validated();
        $data['is_required'] = $request->boolean('is_required');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $request->validate([
            'name_key' => [Rule::unique('service_form_fields', 'name_key')->where('service_id', $service->id)],
        ]);

        $service->formFields()->create($data);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'تم إضافة الحقل بنجاح.');
    }

    public function edit(Service $service, ServiceFormField $field): View
    {
        if ($field->service_id !== $service->id) {
            abort(404);
        }

        $field->load(['options' => fn ($query) => $query->orderBy('sort_order')]);

        return view('admin.services.fields.edit', compact('service', 'field'));
    }

    public function update(ServiceFormFieldRequest $request, Service $service, ServiceFormField $field): RedirectResponse
    {
        if ($field->service_id !== $service->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['is_required'] = $request->boolean('is_required');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $request->validate([
            'name_key' => [Rule::unique('service_form_fields', 'name_key')->where('service_id', $service->id)->ignore($field->id)],
        ]);

        $field->update($data);

        return redirect()->route('admin.services.fields.edit', [$service, $field])
            ->with('status', 'تم تحديث الحقل بنجاح.');
    }

    public function destroy(Service $service, ServiceFormField $field): RedirectResponse
    {
        if ($field->service_id !== $service->id) {
            abort(404);
        }

        $field->delete();

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'تم حذف الحقل بنجاح.');
    }

    public function storeOption(Service $service, ServiceFormField $field): RedirectResponse
    {
        if ($field->service_id !== $service->id) {
            abort(404);
        }

        if ($field->type !== ServiceFormField::TYPE_SELECT) {
            abort(404);
        }

        request()->validate([
            'value' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'value.required' => 'يرجى إدخال قيمة الخيار.',
            'label.required' => 'يرجى إدخال عنوان الخيار.',
        ]);

        $field->options()->create([
            'value' => request('value'),
            'label' => request('label'),
            'sort_order' => request('sort_order', 0),
        ]);

        return redirect()->route('admin.services.fields.edit', [$service, $field])
            ->with('status', 'تم إضافة الخيار بنجاح.');
    }

    public function destroyOption(Service $service, ServiceFormField $field, ServiceFormOption $option): RedirectResponse
    {
        if ($field->service_id !== $service->id) {
            abort(404);
        }

        if ($option->field_id !== $field->id) {
            abort(404);
        }

        $option->delete();

        return redirect()->route('admin.services.fields.edit', [$service, $field])
            ->with('status', 'تم حذف الخيار بنجاح.');
    }
}
