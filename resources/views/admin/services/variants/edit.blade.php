@extends('layouts.app')

@section('title', __('messages.edit_variant'))

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700">{{ __('messages.edit_variant_name', ['variant' => $variant->name]) }}</h1>
            <a href="{{ route('admin.services.variants.index', $service) }}" class="text-sm text-emerald-700">{{ __('messages.back_to_variants') }}</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.services.variants.update', [$service, $variant]) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="__('messages.variant_name_ar')" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $variant->name)" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="name_en" :value="__('messages.variant_name_en')" />
                <x-text-input id="name_en" name="name_en" type="text" :value="old('name_en', $variant->name_en)" />
                <x-input-error :messages="$errors->get('name_en')" />
            </div>

            <div>
                <x-input-label for="price" :value="__('messages.price')" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" :value="old('price', $variant->price)" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ $variant->is_active ? 'checked' : '' }}>
                <label for="is_active">{{ __('messages.activate_variant') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $variant->sort_order)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.update') }}</x-primary-button>
                <a href="{{ route('admin.services.variants.index', $service) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
