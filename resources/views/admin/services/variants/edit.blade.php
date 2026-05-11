@extends('layouts.app')

@section('title', __('messages.edit_variant'))

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm transition-colors duration-200">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-900 dark:text-emerald-100">{{ __('messages.edit_variant_name', ['variant' => $variant->name]) }}</h1>
            <a href="{{ route('admin.services.variants.index', $service) }}" class="text-sm text-emerald-900 dark:text-emerald-100 hover:text-emerald-800 dark:hover:text-emerald-100">{{ __('messages.back_to_variants') }}</a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-100">
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
                <x-input-label for="price" value="السعر الأساسي" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" :value="old('price', $variant->price)" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="service_fee_type" value="نوع رسوم الخدمة" />
                    <x-select id="service_fee_type" name="service_fee_type">
                        <option value="fixed" @selected(old('service_fee_type', $variant->service_fee_type ?? 'fixed') === 'fixed')>مبلغ ثابت</option>
                        <option value="percentage" @selected(old('service_fee_type', $variant->service_fee_type) === 'percentage')>نسبة %</option>
                    </x-select>
                </div>
                <div>
                    <x-input-label for="service_fee_value" value="قيمة رسوم الخدمة" />
                    <x-text-input id="service_fee_value" name="service_fee_value" type="number" step="0.0001" min="0" :value="old('service_fee_value', $variant->service_fee_value ?? 0)" />
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-900 dark:text-slate-100">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-50 dark:border-slate-900 bg-white dark:bg-slate-700 text-emerald-600 focus:ring-emerald-500" {{ $variant->is_active ? 'checked' : '' }}>
                <label for="is_active">{{ __('messages.activate_variant') }}</label>
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('messages.sort_order')" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $variant->sort_order)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('messages.update') }}</x-primary-button>
                <a href="{{ route('admin.services.variants.index', $service) }}" class="rounded-full border border-slate-50 dark:border-slate-900 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-slate-50 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">{{ __('messages.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
