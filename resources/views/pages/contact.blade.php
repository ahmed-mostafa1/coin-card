@extends('layouts.app')

@php
    $contactTitle = __('contact.title');
    $contactDescription = __('contact.description');
    $contactSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => $contactTitle,
        'url' => route('contact-us.show'),
        'description' => $contactDescription,
        'inLanguage' => app()->getLocale(),
    ];
@endphp

@section('title', $contactTitle)
@section('meta_description', $contactDescription)
@section('meta_canonical', route('contact-us.show'))
@section('meta_type', 'website')
@section('meta_robots', 'index,follow')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($contactSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <x-card :hover="false" class="p-6 sm:p-8">
            <x-page-header :title="$contactTitle" :center="false" />

            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('contact.form_description') }}</p>

            @if (session('status'))
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('contact'))
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-300">
                    {{ $errors->first('contact') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact-us.send') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('contact.name_label')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', auth()->user()?->name ?? '')" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('contact.email_label')" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', auth()->user()?->email ?? '')" required dir="ltr" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="subject" :value="__('contact.subject_label')" />
                    <x-text-input id="subject" name="subject" type="text" :value="old('subject')" required />
                    <x-input-error :messages="$errors->get('subject')" />
                </div>

                <div>
                    <x-input-label for="message" :value="__('contact.message_label')" />
                    <textarea id="message" name="message" rows="7" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" />
                </div>

                <x-primary-button class="w-full justify-center">
                    {{ __('contact.submit') }}
                </x-primary-button>
            </form>
        </x-card>

        <x-card :hover="false" class="p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('contact.support_title') }}</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ __('contact.support_description') }}</p>
            <p class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                {{ __('contact.response_time') }}
            </p>

            <div class="mt-6 space-y-3">
                @if (($sharedWhatsappEnabled ?? '0') === '1' && !empty($sharedWhatsappLink) && $sharedWhatsappLink !== '#')
                    <a href="{{ $sharedWhatsappLink }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-emerald-300 hover:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-600">
                        <span>{{ __('messages.contact_whatsapp') }}</span>
                        <i class="fa-brands fa-whatsapp text-lg text-green-500"></i>
                    </a>
                @endif

                @if (($sharedTelegramEnabled ?? '0') === '1' && !empty($sharedTelegramLink) && $sharedTelegramLink !== '#')
                    <a href="{{ $sharedTelegramLink }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700 transition hover:border-emerald-300 hover:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-emerald-600">
                        <span>Telegram</span>
                        <i class="fa-brands fa-telegram text-lg text-sky-500"></i>
                    </a>
                @endif

            </div>
        </x-card>
    </div>
@endsection
