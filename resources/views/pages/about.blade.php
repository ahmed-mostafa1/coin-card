@extends('layouts.app')

@section('title', __('messages.about_us'))

@section('content')
    <x-card :hover="false">
        <x-page-header :title="__('messages.about_us')" />
        <div class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-white">
            @if(app()->getLocale() == 'ar')
                يقدم موقع 8bp.in خدماته منذ عام 2018، ويعرض حاليًا مجموعة متنوعة تشمل شحن 200 تطبيقًا ولعبة مختلفة،
                مع إستمرار إضافة المزيد

                يمكن للاعبين والتجار بيع المنتجات مثل رموز coupon والشحن المباشر عبر معرف الحساب على مدار 24 ساعة حيث يتم تسليم
                أموال اللعبة خلال 10 دقائق بواسطة الموزعين المعتمدين

                يقدم الموقع نظامًا للترتيب بين الأعضاء، مما يتيح الحصول على خصومات أكبر مع زيادة عمليات الشراء وأسعار خاصة
                لأصحاب المواقع والمتاجر

                نقدم أيضا" خدمات تصاميم المواقع والتطبيقات

                منذ انطلاق الخدمة، يعمل فريق الدعم المباشر على مساعدة اللاعبين والوكلاء في جميع جوانب الخدمة . يمكنكم الحصول على
                معلومات إضافية على مدار الساعة من خلال خط WhatsApp أو عبر البريد الإلكتروني
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
        </div>
    </x-card>
@endsection