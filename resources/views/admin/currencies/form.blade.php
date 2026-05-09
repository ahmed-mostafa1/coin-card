<div>
    <x-input-label for="name" value="اسم العملة" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $currency?->name)" required />
    <x-input-error :messages="$errors->get('name')" />
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="code" value="الكود" />
        <x-text-input id="code" name="code" type="text" dir="ltr" :value="old('code', $currency?->code)" required />
        <x-input-error :messages="$errors->get('code')" />
    </div>
    <div>
        <x-input-label for="symbol" value="الرمز" />
        <x-text-input id="symbol" name="symbol" type="text" :value="old('symbol', $currency?->symbol)" />
        <x-input-error :messages="$errors->get('symbol')" />
    </div>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="exchange_rate_to_usd" value="سعر التحويل إلى USD" />
        <x-text-input id="exchange_rate_to_usd" name="exchange_rate_to_usd" type="number" step="0.00000001" min="0.00000001" dir="ltr" :value="old('exchange_rate_to_usd', $currency?->exchange_rate_to_usd ?? 1)" required />
        <x-input-error :messages="$errors->get('exchange_rate_to_usd')" />
    </div>
    <div>
        <x-input-label for="sort_order" value="الترتيب" />
        <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $currency?->sort_order ?? 0)" />
        <x-input-error :messages="$errors->get('sort_order')" />
    </div>
</div>
<label class="flex items-center gap-3 text-sm text-slate-900 dark:text-slate-400">
    <input type="checkbox" name="is_enabled" value="1" class="rounded border-slate-50 text-emerald-600" @checked(old('is_enabled', $currency?->is_enabled ?? true))>
    العملة مفعلة
</label>
<div class="flex gap-3">
    <x-primary-button>{{ __('messages.save') }}</x-primary-button>
    <a href="{{ route('admin.currencies.index') }}" class="rounded-full border border-slate-50 px-4 py-2 text-sm font-semibold text-slate-900 dark:border-slate-900 dark:text-slate-50">{{ __('messages.cancel') }}</a>
</div>
