<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition duration-200 hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 cc-press']) }}>
    {{ $slot }}
</button>
