@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-50 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-900']) }}>
    {{ $slot }}
</select>
