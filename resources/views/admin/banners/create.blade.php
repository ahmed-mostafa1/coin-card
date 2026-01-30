@extends('layouts.app')

@section('title', __('messages.add_banner'))

@section('content')
    <div class="mx-auto max-w-2xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.add_banner') }}</h1>

        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="title" :value="__('messages.banner_title_ar_optional')" />
                <x-text-input id="title" name="title" type="text" :value="old('title')" />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="title_en" :value="__('messages.banner_title_en_optional')" />
                <x-text-input id="title_en" name="title_en" type="text" :value="old('title_en')" />
                <x-input-error :messages="$errors->get('title_en')" />
            </div>

            <div>
                <x-input-label for="image" :value="__('messages.image')" />
                <input id="image" name="image" type="file" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700" required>
                <x-input-error :messages="$errors->get('image')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                <label for="is_active">{{ __('messages.activate_banner') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', 0)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.save') }}</x-primary-button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
