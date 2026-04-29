@extends('layouts.app')

@section('title', 'طلب وكالة')

@section('content')
    <div class="w-full rounded-3xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-800 p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-700 dark:text-emerald-400">طلب وكالة</h1>
        <span class="sr-only">Agency</span>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">املأ البيانات التالية وسيتم التواصل معك.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($fields->isNotEmpty())
            <form method="POST" action="{{ route('agency-requests.store') }}" class="mt-6 space-y-4">
                @csrf

                @foreach ($fields as $field)
                    <div>
                        <x-input-label :for="$field->name_key" :value="$field->localized_label" />

                        @if ($field->type === 'textarea')
                            <textarea
                                id="{{ $field->name_key }}"
                                name="{{ $field->name_key }}"
                                rows="3"
                                placeholder="{{ $field->localized_placeholder ?? '' }}"
                                {{ $field->is_required ? 'required' : '' }}
                                class="mt-1 block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            >{{ old($field->name_key) }}</textarea>
                        @else
                            <x-text-input
                                :id="$field->name_key"
                                :name="$field->name_key"
                                :type="$field->type"
                                :value="old($field->name_key)"
                                :placeholder="$field->localized_placeholder ?? ''"
                                :required="$field->is_required"
                            />
                        @endif

                        @error($field->name_key)
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <x-primary-button class="w-full">إرسال</x-primary-button>
            </form>
        @else
            <p class="mt-6 text-sm text-slate-500 dark:text-slate-400">نموذج الطلب غير متاح حالياً. يرجى التواصل معنا مباشرة.</p>
        @endif
    </div>
@endsection
