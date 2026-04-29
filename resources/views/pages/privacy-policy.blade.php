@extends('layouts.app')

@php
    $privacyTitle = app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy';
    $privacyContent = app()->getLocale() === 'en'
        ? ($sharedPrivacyEn ?: $sharedPrivacyAr)
        : ($sharedPrivacyAr ?: $sharedPrivacyEn);
    $privacyDescriptionSource = $privacyContent ?: (app()->getLocale() === 'ar'
        ? 'اطلع على سياسة الخصوصية وآلية جمع البيانات واستخدامها وحماية معلومات المستخدمين داخل المنصة.'
        : 'Read the privacy policy covering data collection, usage, and how user information is protected on the platform.');
    $privacyDescription = \Illuminate\Support\Str::limit(strip_tags($privacyDescriptionSource), 160, '');
    $privacySchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $privacyTitle,
        'url' => route('privacy-policy'),
        'description' => $privacyDescription,
        'inLanguage' => app()->getLocale(),
    ];
@endphp

@section('title', $privacyTitle)
@section('meta_description', $privacyDescription)
@section('meta_canonical', route('privacy-policy'))
@section('meta_type', 'website')
@section('meta_robots', 'index,follow')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($privacySchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.privacy_policy')" :center="true" />
        <span class="sr-only">Privacy</span>
        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-white text-center">
            @if(filled($privacyContent))
                {!! nl2br(e($privacyContent)) !!}
            @else
                <p>{{ app()->getLocale() === 'ar' ? 'لم يتم إضافة محتوى سياسة الخصوصية بعد من لوحة الإدارة.' : 'Privacy policy content has not been added from the admin dashboard yet.' }}</p>
            @endif
        </div>
    </x-card>
@endsection
