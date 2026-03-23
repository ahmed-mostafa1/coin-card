@extends('layouts.app')

@php
    $aboutTitle = app()->getLocale() === 'ar' ? 'من نحن' : 'About Us';
    $aboutDescriptionSource = app()->getLocale() === 'ar'
        ? ($sharedAboutAr ?: 'تعرف على المتجر وخدماته الرقمية وآلية العمل والدعم المتاح للعملاء والوكلاء.')
        : ($sharedAboutEn ?: 'Learn more about the store, its digital services, and how support works for customers and agents.');
    $aboutDescription = \Illuminate\Support\Str::limit(strip_tags($aboutDescriptionSource), 160, '');
    $aboutSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => $aboutTitle,
        'url' => route('about'),
        'description' => $aboutDescription,
        'inLanguage' => app()->getLocale(),
    ];
@endphp

@section('title', $aboutTitle)
@section('meta_description', $aboutDescription)
@section('meta_canonical', route('about'))
@section('meta_type', 'website')
@section('meta_robots', 'index,follow')

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($aboutSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.about_us')" :center="true" />
        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-white text-center">
            @if(app()->getLocale() == 'ar')
                @if(!empty($sharedAboutAr))
                    {!! nl2br(e($sharedAboutAr)) !!}
                @else
                    يقدم موقع 8bp.in خدماته منذ عام 2018، ويعرض حاليًا مجموعة متنوعة تشمل شحن 200 تطبيقًا ولعبة مختلفة،
                    مع إستمرار إضافة المزيد
    
                    يمكن للاعبين والتجار بيع المنتجات مثل رموز coupon والشحن المباشر عبر معرف الحساب على مدار 24 ساعة حيث يتم تسليم
                    أموال اللعبة خلال 10 دقائق بواسطة الموزعين المعتمدين
    
                    يقدم الموقع نظامًا للترتيب بين الأعضاء، مما يتيح الحصول على خصومات أكبر مع زيادة عمليات الشراء وأسعار خاصة
                    لأصحاب المواقع والمتاجر
    
                    نقدم أيضا" خدمات تصاميم المواقع والتطبيقات
    
                    منذ انطلاق الخدمة، يعمل فريق الدعم المباشر على مساعدة اللاعبين والوكلاء في جميع جوانب الخدمة . يمكنكم الحصول على
                    معلومات إضافية على مدار الساعة من خلال خط WhatsApp أو عبر البريد الإلكتروني
                @endif
            @else
                @if(!empty($sharedAboutEn))
                    {!! nl2br(e($sharedAboutEn)) !!}
                @else
                    8bp.in has been serving customers since 2018, currently offering a diverse range of top-ups for over
                    200 different apps and games, with more being added continuously.
    
                    Players and merchants can sell products like coupon codes and direct top-ups via Account ID 24/7, with game
                    currency delivered within 10 minutes by authorized distributors.
    
                    The site offers a member ranking system, allowing for greater discounts as purchases increase, and special
                    prices for site owners and store merchants.
    
                    We also offer web and application design services.
    
                    Since the launch of the service, our direct support team works to assist players and agents in all aspects of
                    the service. You can get additional information around the clock via WhatsApp or email.
                @endif
            @endif
        </div>
    </x-card>
@endsection
