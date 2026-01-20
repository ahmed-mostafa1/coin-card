@extends('layouts.app')

@section('title', 'تعديل حقل')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-emerald-700">تعديل الحقل</h1>
                <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700">عودة للخدمة</a>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.services.fields.update', [$service, $field]) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="type" value="نوع الحقل" />
                    <select id="type" name="type" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" required>
                        <option value="text" @selected(old('type', $field->type) === 'text')>نصي</option>
                        <option value="select" @selected(old('type', $field->type) === 'select')>قائمة اختيار</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" />
                </div>

                <div>
                    <x-input-label for="label" value="عنوان الحقل" />
                    <x-text-input id="label" name="label" type="text" :value="old('label', $field->label)" required />
                    <x-input-error :messages="$errors->get('label')" />
                </div>

                <div>
                    <x-input-label for="name_key" value="مفتاح الحقل" />
                    <x-text-input id="name_key" name="name_key" type="text" :value="old('name_key', $field->name_key)" required />
                    <x-input-error :messages="$errors->get('name_key')" />
                </div>

                <div>
                    <x-input-label for="placeholder" value="نص توضيحي (اختياري)" />
                    <x-text-input id="placeholder" name="placeholder" type="text" :value="old('placeholder', $field->placeholder)" />
                </div>

                <div>
                    <x-input-label for="validation_rules" value="قواعد إضافية (اختياري)" />
                    <x-text-input id="validation_rules" name="validation_rules" type="text" :value="old('validation_rules', $field->validation_rules)" />
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <input id="is_required" name="is_required" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ $field->is_required ? 'checked' : '' }}>
                    <label for="is_required">حقل مطلوب</label>
                </div>

                <div>
                    <x-input-label for="sort_order" value="ترتيب العرض" />
                    <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $field->sort_order)" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button>تحديث</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.services.fields.destroy', [$service, $field]) }}" class="mt-6">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600">حذف الحقل</button>
            </form>
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">خيارات الحقل</h2>

            @if ($field->type !== 'select')
                <p class="mt-3 text-sm text-slate-500">الخيارات متاحة فقط لحقول القائمة.</p>
            @else
                <form method="POST" action="{{ route('admin.services.fields.options.store', [$service, $field]) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="option_value" value="القيمة" />
                        <x-text-input id="option_value" name="value" type="text" />
                    </div>
                    <div>
                        <x-input-label for="option_label" value="العنوان" />
                        <x-text-input id="option_label" name="label" type="text" />
                    </div>
                    <div>
                        <x-input-label for="option_sort_order" value="ترتيب" />
                        <x-text-input id="option_sort_order" name="sort_order" type="number" min="0" :value="0" />
                    </div>
                    <x-primary-button class="w-full">إضافة خيار</x-primary-button>
                </form>

                <div class="mt-6 space-y-3">
                    @forelse ($field->options as $option)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-3 text-sm">
                            <div>
                                <p class="font-semibold text-slate-700">{{ $option->label }}</p>
                                <p class="text-xs text-slate-500">{{ $option->value }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.services.fields.options.destroy', [$service, $field, $option]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600">حذف</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">لا توجد خيارات بعد.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
@endsection
