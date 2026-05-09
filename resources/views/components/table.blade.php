<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-2xl border border-slate-50 dark:border-slate-700']) }}>
    <table class="rt-table">
        {{ $slot }}
    </table>
</div>
