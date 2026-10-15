@php
    use App\Support\Seo;

    // Semua nilai diambil dari section yang sudah dipakai halaman publik
    // (`title`, `description`) dan ditambah section opsional baru. Default
    // jatuh ke config/seo.php supaya tidak ada meta yang kosong.
    $seoTitle = trim($__env->yieldContent('title', config('seo.default_title')));
    $seoDescription = Seo::excerpt($__env->yieldContent('description', config('seo.default_description')));
    $seoCanonical = trim($__env->yieldContent('canonical', url()->current()));
    $seoKeywords = trim($__env->yieldContent('keywords', config('seo.keywords')));
    $seoRobots = trim($__env->yieldContent('robots', 'index, follow'));
    $seoType = trim($__env->yieldContent('og_type', 'website'));
    $seoImage = Seo::image(trim($__env->yieldContent('og_image')) ?: null);

    // Organization + WebSite dirender di seluruh halaman publik; node
    // tambahan (breadcrumb, CreativeWork, dsb.) di-push halaman lewat
    // @push('json-ld'). Satu graph per halaman lebih disukai Google.
    $seoGraph = [
        Seo::organization(),
        Seo::website(),
    ];

    $seoJsonOptions = JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS;
@endphp

<title>{{ $seoTitle }}</title>

<meta name="description" content="{{ $seoDescription }}">
@if (filled($seoKeywords))
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="robots" content="{{ $seoRobots }}">
<meta name="author" content="{{ config('app.name') }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta name="theme-color" content="{{ config('seo.theme_color') }}">

{{-- Verifikasi Search Console (hanya tampil bila token diisi di .env). --}}
@if (filled(config('seo.google_site_verification')))
    <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
@endif
@if (filled(config('seo.bing_site_verification')))
    <meta name="msvalidate.01" content="{{ config('seo.bing_site_verification') }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">
<meta property="og:locale" content="{{ config('seo.locale') }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoTitle }}">
@if (filled(config('seo.twitter')))
    <meta name="twitter:site" content="{{ config('seo.twitter') }}">
@endif

<script type="application/ld+json">{!! json_encode(Seo::graph($seoGraph), $seoJsonOptions) !!}</script>

@stack('json-ld')
