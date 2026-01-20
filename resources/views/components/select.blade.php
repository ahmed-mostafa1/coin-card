@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100 disabled:text-slate-400']) }}>
    {{ $slot }}
</select>
