@php
    $routeName = request()->route()?->getName();
    $siteName = trim($sharedLogoText ?: config('app.name', 'Arab 8BP'));
    $defaultDescriptionSource = app()->getLocale() === 'en' && filled($sharedStoreDescriptionEn)
        ? $sharedStoreDescriptionEn
        : $sharedStoreDescription;
    $defaultDescription = \Illuminate\Support\Str::limit(
        trim(strip_tags($defaultDescriptionSource ?: 'Digital services and gaming top-ups.')),
        160,
        ''
    );

    $rawTitle = trim($__env->yieldContent('title', $siteName));
    $metaTitle = $rawTitle === '' || $rawTitle === $siteName
        ? $siteName
        : $rawTitle.' | '.$siteName;

    $metaDescription = trim($__env->yieldContent('meta_description', $defaultDescription));
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($metaDescription ?: $defaultDescription), 160, '');

    $publicSeoRoutes = ['home', 'categories.show', 'services.show', 'about', 'privacy-policy'];
    $isPublicSeoPage = isset($forcePublicSeo)
        ? (bool) $forcePublicSeo
        : in_array($routeName, $publicSeoRoutes, true);

    $defaultRobots = $isPublicSeoPage ? 'index,follow' : 'noindex,nofollow';
    if (! empty($forceNoindex)) {
        $defaultRobots = 'noindex,nofollow';
    }

    $metaRobots = trim($__env->yieldContent('meta_robots', $defaultRobots));
    $metaCanonical = trim($__env->yieldContent('meta_canonical', url()->current()));
    $metaType = trim($__env->yieldContent('meta_type', $routeName === 'services.show' ? 'product' : 'website'));
    $metaKeywords = trim($__env->yieldContent('meta_keywords', ''));
    $metaImage = trim($__env->yieldContent('meta_image', ''));
    $metaImage = $metaImage !== ''
        ? $metaImage
        : ($sharedLogoImage ? asset('storage/'.$sharedLogoImage) : asset('img/placeholder-banner.jpg'));
    $twitterCard = trim($__env->yieldContent('meta_twitter_card', 'summary_large_image'));
    $metaLocale = app()->getLocale() === 'ar' ? 'ar_AR' : 'en_US';
    $sameAs = array_values(array_filter([
        $sharedWhatsappLink ?? null,
        $sharedInstagramLink ?? null,
        $sharedTelegramLink ?? null,
        $sharedFacebookLink ?? null,
        $sharedYoutubeLink ?? null,
    ], fn ($url) => filled($url) && $url !== '#'));

    $organizationSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $metaImage,
        'sameAs' => $sameAs ?: null,
        'contactPoint' => ! empty($sharedWhatsappLink) && $sharedWhatsappLink !== '#'
            ? [[
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'url' => $sharedWhatsappLink,
                'availableLanguage' => ['ar', 'en'],
            ]]
            : null,
    ], fn ($value) => $value !== null && $value !== [] && $value !== '');

    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => url('/'),
        'inLanguage' => app()->getLocale(),
    ];
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $metaRobots }}">
<meta name="theme-color" content="#059669">
@if ($metaKeywords !== '')
    <meta name="keywords" content="{{ $metaKeywords }}">
@endif
<link rel="canonical" href="{{ $metaCanonical }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

<meta property="og:locale" content="{{ $metaLocale }}">
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $metaCanonical }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $rawTitle !== '' ? $rawTitle : $siteName }}">

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@stack('structured-data')
