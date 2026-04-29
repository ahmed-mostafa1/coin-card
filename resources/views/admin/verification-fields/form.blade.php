<div class="grid gap-4 md:grid-cols-2">
    <div><x-input-label for="label" value="اسم الحقل" /><x-text-input id="label" name="label" :value="old('label', $field?->label)" required /><x-input-error :messages="$errors->get('label')" /></div>
    <div><x-input-label for="label_en" value="Field Label EN" /><x-text-input id="label_en" name="label_en" :value="old('label_en', $field?->label_en)" /></div>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div><x-input-label for="name_key" value="المفتاح" /><x-text-input id="name_key" name="name_key" dir="ltr" :value="old('name_key', $field?->name_key)" required /><x-input-error :messages="$errors->get('name_key')" /></div>
    <div><x-input-label for="type" value="النوع" /><x-select id="type" name="type"><option value="text" @selected(old('type', $field?->type ?? 'text') === 'text')>نص</option><option value="textarea" @selected(old('type', $field?->type) === 'textarea')>نص طويل</option><option value="number" @selected(old('type', $field?->type) === 'number')>رقم</option><option value="date" @selected(old('type', $field?->type) === 'date')>تاريخ</option><option value="select" @selected(old('type', $field?->type) === 'select')>قائمة</option><option value="file" @selected(old('type', $field?->type) === 'file')>ملف</option><option value="image" @selected(old('type', $field?->type) === 'image')>صورة</option><option value="camera" @selected(old('type', $field?->type) === 'camera')>كاميرا فقط</option></x-select></div>
</div>
<div><x-input-label for="options_text" value="خيارات القائمة (كل خيار في سطر)" /><textarea id="options_text" name="options_text" rows="4" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white">{{ old('options_text', implode("\n", $field?->options ?? [])) }}</textarea></div>
<div class="grid gap-4 md:grid-cols-2">
    <div><x-input-label for="placeholder" value="Placeholder عربي" /><x-text-input id="placeholder" name="placeholder" :value="old('placeholder', $field?->placeholder)" /></div>
    <div><x-input-label for="placeholder_en" value="Placeholder EN" /><x-text-input id="placeholder_en" name="placeholder_en" :value="old('placeholder_en', $field?->placeholder_en)" /></div>
</div>
<div class="grid gap-4 md:grid-cols-3">
    <div><x-input-label for="sort_order" value="الترتيب" /><x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $field?->sort_order ?? 0)" /></div>
    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_required" value="1" class="rounded border-slate-300 text-emerald-600" @checked(old('is_required', $field?->is_required ?? true))> مطلوب</label>
    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_enabled" value="1" class="rounded border-slate-300 text-emerald-600" @checked(old('is_enabled', $field?->is_enabled ?? true))> مفعل</label>
</div>
<div class="flex gap-3"><x-primary-button>{{ __('messages.save') }}</x-primary-button><a href="{{ route('admin.verification-fields.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('messages.cancel') }}</a></div>
