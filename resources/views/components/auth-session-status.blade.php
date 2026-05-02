@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:bg-emerald-800 dark:border-emerald-700 dark:text-emerald-300 ']) }}>
        {{ $status }}
    </div>
@endif
