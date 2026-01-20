@extends('layouts.app')

@section('title', $service->name)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm lg:col-span-2">
            <div class="flex flex-wrap items-center gap-4">
                @if ($service->image_path)
                    <img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name }}" class="h-20 w-20 rounded-2xl object-cover">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">{{ mb_substr($service->name, 0, 1) }}</div>
                @endif
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">{{ $service->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $service->category->name }}</p>
                    <p class="mt-2 text-lg font-semibold text-emerald-700">{{ number_format($service->price, 2) }} ر.س</p>
                </div>
            </div>

            @if ($service->description)
                <div class="mt-6 text-sm leading-7 text-slate-600 whitespace-pre-line">{{ $service->description }}</div>
            @endif
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <h2 class="text-lg font-semibold text-emerald-700">تفاصيل الطلب</h2>

            @if (session('status'))
                <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('balance'))
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first('balance') }}
                </div>
            @endif

            @auth
                <p class="mt-4 text-sm text-slate-600">رصيدك الحالي: <span class="font-semibold text-emerald-700">{{ number_format($wallet?->balance ?? 0, 2) }} ر.س</span></p>
            @endauth

            <form method="POST" action="{{ route('services.purchase', $service->slug) }}" class="mt-6 space-y-4">
                @csrf

                @forelse ($service->formFields->sortBy('sort_order') as $field)
                    <div>
                        <x-input-label for="field_{{ $field->name_key }}" :value="$field->label" />
                        @if ($field->type === 'text')
                            <x-text-input id="field_{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" type="text" :value="old('fields.'.$field->name_key)" placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }} />
                        @else
                            <select id="field_{{ $field->name_key }}" name="fields[{{ $field->name_key }}]" class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm text-slate-700" {{ $field->is_required ? 'required' : '' }}>
                                <option value="">اختر</option>
                                @foreach ($field->options as $option)
                                    <option value="{{ $option->value }}" @selected(old('fields.'.$field->name_key) === $option->value)>{{ $option->label }}</option>
                                @endforeach
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('fields.'.$field->name_key)" />
                    </div>
                @empty
                    <p class="text-sm text-slate-500">لا توجد بيانات مطلوبة لهذه الخدمة.</p>
                @endforelse

                @guest
                    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">سجل الدخول لإتمام الشراء</a>
                @else
                    <x-primary-button class="w-full">شراء الآن</x-primary-button>
                @endguest
            </form>
        </div>
    </div>
@endsection
