@extends('layouts.app')

@php
    $termsTitle = app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms and Conditions';
    $termsContent = app()->getLocale() === 'en'
        ? ($sharedTermsEn ?: $sharedTermsAr)
        : ($sharedTermsAr ?: $sharedTermsEn);
    $termsDescriptionSource = $termsContent ?: (app()->getLocale() === 'ar'
        ? 'اطلع على الشروط والأحكام ومسؤوليات العميل قبل تنفيذ الطلبات.'
        : 'Review the terms and conditions and customer responsibilities before placing orders.');
    $termsDescription = \Illuminate\Support\Str::limit(strip_tags($termsDescriptionSource), 160, '');
    $termsSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $termsTitle,
        'url' => route('terms-of-use'),
        'description' => $termsDescription,
        'inLanguage' => app()->getLocale(),
    ];
@endphp

@section('title', $termsTitle)
@section('meta_description', $termsDescription)
@section('meta_canonical', route('terms-of-use'))
@section('meta_type', 'website')
@section('meta_robots', 'index,follow')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($termsSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <x-card :hover="false">
        <x-page-header :title="$termsTitle" :center="true" />

        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-white text-center">
            @if (filled($termsContent))
                {!! nl2br(e($termsContent)) !!}
            @else
                <p>{{ app()->getLocale() === 'ar' ? 'لم يتم إضافة محتوى الشروط والأحكام بعد من لوحة الإدارة.' : 'Terms and conditions content has not been added from the admin dashboard yet.' }}</p>
            @endif
        </div>
    </x-card>
@endsection
