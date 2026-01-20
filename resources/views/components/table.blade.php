<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-2xl border border-slate-200']) }}>
    <table class="w-full text-right text-sm">
        {{ $slot }}
    </table>
</div>
