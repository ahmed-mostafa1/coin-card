@extends('layouts.app')

@section('title', 'تعديل باقة')

@section('content')
    <div class="mx-auto max-w-3xl rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-emerald-700">تعديل باقة {{ $variant->name }}</h1>
            <a href="{{ route('admin.services.variants.index', $service) }}" class="text-sm text-emerald-700">عودة للباقات</a>
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
                <x-input-label for="name" value="اسم الباقة" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $variant->name)" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="price" value="السعر" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0.01" :value="old('price', $variant->price)" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>

            <div class="flex items-center gap-3 text-sm text-slate-600">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ $variant->is_active ? 'checked' : '' }}>
                <label for="is_active">تفعيل الباقة</label>
            </div>

            <div>
                <x-input-label for="sort_order" value="ترتيب العرض" />
                <x-text-input id="sort_order" name="sort_order" type="number" min="0" :value="old('sort_order', $variant->sort_order)" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>تحديث</x-primary-button>
                <a href="{{ route('admin.services.variants.index', $service) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
