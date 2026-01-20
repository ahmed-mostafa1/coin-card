@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            @if ($category->image_path)
                <img src="{{ asset('storage/'.$category->image_path) }}" alt="{{ $category->name }}" class="h-20 w-20 rounded-2xl object-cover">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">{{ mb_substr($category->name, 0, 1) }}</div>
            @endif
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">{{ $category->name }}</h1>
                <p class="mt-2 text-sm text-slate-600">اختر الخدمة المناسبة لإكمال عملية الشراء.</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($services as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="group rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-200">
                    @if ($service->image_path)
                        <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" class="h-32 w-full rounded-xl object-cover">
                    @else
                        <div class="flex h-32 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">{{ mb_substr($service->name, 0, 1) }}</div>
                    @endif
                    <h3 class="mt-4 text-sm font-semibold text-slate-700 group-hover:text-emerald-700">{{ $service->name }}</h3>
                    <p class="mt-2 text-sm font-semibold text-emerald-700">{{ number_format($service->price, 2) }} ر.س</p>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">لا توجد خدمات متاحة حالياً.</div>
            @endforelse
        </div>
    </div>
@endsection
