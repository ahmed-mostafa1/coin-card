<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700']) }}>
    {{ $slot }}
</button>
