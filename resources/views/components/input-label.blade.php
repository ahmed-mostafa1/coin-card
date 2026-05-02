@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 block text-sm font-medium text-slate-700 dark:text-slate-50']) }}>
    {{ $value ?? $slot }}
</label>
