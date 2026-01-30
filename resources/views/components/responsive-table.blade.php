@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700']) }}>
    <table class="rt-table {{ $class }}">
        {{ $slot }}
    </table>
</div>
