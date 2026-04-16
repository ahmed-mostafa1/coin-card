@php
    $routeName = request()->route()?->getName();
    // SEO title setting overrides logo text for meta purposes only
    $siteName = trim(filled($sharedSeoTitle) ? $sharedSeoTitle : ($sharedLogoText ?: config('app.name', 'S7SH.com|شحنك شات')));

    // Use the dedicated meta_description DB setting if set; fall back to store_description.
    $globalDescription = filled($sharedMetaDescription)
        ? $sharedMetaDescription
        : (app()->getLocale() === 'en' && filled($sharedStoreDescriptionEn) ? $sharedStoreDescriptionEn : $sharedStoreDescription);

    $defaultDescription = \Illuminate\Support\Str::limit(
        trim(strip_tags($globalDescription ?: 'Digital services and gaming top-ups.')),
        160,
        ''
    );

    $rawTitle = trim($__env->yieldContent('title', $siteName));
    $metaTitle = $rawTitle === '' || $rawTitle === $siteName
        ? $siteName
        : $rawTitle.' | '.$siteName;

    $metaDescription = trim($__env->yieldContent('meta_description', $defaultDescription));
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($metaDescription ?: $defaultDescription), 160, '');

    $publicSeoRoutes = ['home', 'categories.show', 'services.show', 'about', 'privacy-policy', 'terms-of-use', 'contact-us.show'];
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
    $metaKeywords = trim($__env->yieldContent('meta_keywords', $sharedMetaKeywords ?? ''));
    $metaImage = trim($__env->yieldContent('meta_image', ''));
    $metaImage = $metaImage !== ''
        ? $metaImage
        : ($sharedLogoImage ? asset('storage/'.$sharedLogoImage) : asset('img/placeholder-banner.jpg'));
    $twitterCard = trim($__env->yieldContent('meta_twitter_card', 'summary_large_image'));
    $metaLocale = app()->getLocale() === 'ar' ? 'ar_AR' : 'en_US';
    $sameAs = array_values(array_filter([
        ($sharedWhatsappEnabled ?? '0') === '1' ? ($sharedWhatsappLink ?? null) : null,
        ($sharedTelegramEnabled ?? '0') === '1' ? ($sharedTelegramLink ?? null) : null,
        ($sharedFacebookEnabled ?? '0') === '1' ? ($sharedFacebookLink ?? null) : null,
        ($sharedYoutubeEnabled ?? '0') === '1' ? ($sharedYoutubeLink ?? null) : null,
        ($sharedTiktokEnabled ?? '0') === '1' ? ($sharedTiktokLink ?? null) : null,
    ], fn ($url) => filled($url) && $url !== '#'));

    $organizationSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $metaImage,
        'sameAs' => $sameAs ?: null,
        'contactPoint' => ($sharedWhatsappEnabled ?? '0') === '1' && ! empty($sharedWhatsappLink) && $sharedWhatsappLink !== '#'
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

@if (filled($sharedGaId ?? ''))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $sharedGaId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $sharedGaId }}');
</script>
@endif

@if (filled($sharedFbPixelId ?? ''))
<!-- Meta (Facebook) Pixel -->
<script>
  !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window,document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{ $sharedFbPixelId }}');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $sharedFbPixelId }}&ev=PageView&noscript=1"/></noscript>
@endif

@if (filled($sharedHeadScripts ?? ''))
{!! $sharedHeadScripts !!}
@endif
