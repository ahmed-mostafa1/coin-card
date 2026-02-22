@extends('layouts.app')

@section('title', __('messages.add_button'))

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">{{ __('messages.add_button') }}</h1>
            <a href="{{ route('admin.services.edit', $service) }}" class="text-sm text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('messages.back_to_service') }}</a>
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-700 dark:text-rose-400">
                <ul class="list-disc ps-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.services.buttons.store', $service) }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="label_ar" :value="__('messages.button_label_ar')" />
                <x-text-input id="label_ar" name="label_ar" type="text" :value="old('label_ar')" required />
                <x-input-error :messages="$errors->get('label_ar')" />
            </div>

            <div>
                <x-input-label for="label_en" :value="__('messages.button_label_en')" />
                <x-text-input id="label_en" name="label_en" type="text" :value="old('label_en')" />
                <x-input-error :messages="$errors->get('label_en')" />
            </div>

            <div>
                <x-input-label for="url" :value="__('messages.button_url')" />
                <x-text-input id="url" name="url" type="url" dir="ltr" :value="old('url')" required />
                <x-input-error :messages="$errors->get('url')" />
            </div>

            <div>
                <x-input-label for="bg_color" :value="__('messages.button_bg_color')" />
                <div class="flex items-center gap-3">
                    <input id="bg_color" name="bg_color" type="color" value="{{ old('bg_color', '#f2a900') }}"
                        class="h-10 w-16 cursor-pointer rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 p-1">
                    <x-text-input id="bg_color_text" type="text" :value="old('bg_color', '#f2a900')"
                        class="w-32 font-mono" dir="ltr" placeholder="#f2a900"
                        oninput="document.getElementById('bg_color').value = this.value" />
                </div>
                <x-input-error :messages="$errors->get('bg_color')" />
                <script>
                    document.getElementById('bg_color').addEventListener('input', function () {
                        document.getElementById('bg_color_text').value = this.value;
                    });
                </script>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
                <x-input-error :messages="$errors->get('sort_order')" />
            </div>

            <div class="flex gap-3 pt-2">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.services.edit', $service) }}"
                    class="rounded-full border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
