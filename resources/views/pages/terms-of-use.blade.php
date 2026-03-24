@extends('layouts.app')

@php
    $termsTitle = __('messages.terms_of_use');
    $termsDescriptionSource = app()->getLocale() === 'ar'
        ? ($sharedTermsAr ?: 'اطلع على شروط استخدام المتجر ومسؤوليات العميل قبل تنفيذ الطلبات.')
        : ($sharedTermsEn ?: 'Review the store terms of use and customer responsibilities before placing orders.');
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
            @if (app()->getLocale() === 'ar')
                {!! nl2br(e($sharedTermsAr ?: 'باستخدامك خدمات المتجر فإنك توافق على الالتزام بالشروط والتعليمات الخاصة بكل خدمة، وتتحمل مسؤولية صحة البيانات التي تدخلها أثناء الطلب. جميع الطلبات الرقمية التي يتم تنفيذها بنجاح تعتبر نهائية، كما تحتفظ الإدارة بحق مراجعة أو إيقاف أي طلب عند وجود بيانات غير صحيحة أو استخدام مخالف.')) !!}
            @else
                {!! nl2br(e($sharedTermsEn ?: 'By using the store services, you agree to follow the terms and instructions of each service and you accept full responsibility for the accuracy of the information you submit. Successfully fulfilled digital orders are considered final, and the administration reserves the right to review or stop any order if the submitted data is incorrect or violates store policies.')) !!}
            @endif
        </div>
    </x-card>
@endsection
