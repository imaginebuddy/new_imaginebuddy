@php
    $seoData = seo()->resolve();

    // Title priority:
    // 1. Admin SEO Settings configured title ($seoData['has_custom_title'])
    // 2. View section title (@yield('title'))
    // 3. Fallback resolved title ($seoData['title'])
    if (!empty($seoData['has_custom_title'])) {
        $finalTitle = $seoData['title'];
    } elseif (View::hasSection('title')) {
        $finalTitle = trim(View::yieldContent('title'));
    } else {
        $finalTitle = $seoData['title'];
    }
    // Clean up trailing dashes from legacy title yields
    $finalTitle = rtrim($finalTitle, " -");
    if (empty($finalTitle)) {
        $finalTitle = $seoData['title'];
    }

    // Description priority:
    if (!empty($seoData['has_custom_desc'])) {
        $finalDesc = $seoData['description'];
    } elseif (View::hasSection('description_override')) {
        $finalDesc = trim(View::yieldContent('description_override'));
    } elseif (View::hasSection('description_custom')) {
        $finalDesc = trim(View::yieldContent('description_custom'));
    } else {
        $finalDesc = $seoData['description'];
    }
    $finalDesc = rtrim($finalDesc, " -");

    // Keywords priority:
    if (!empty($seoData['has_custom_keywords'])) {
        $finalKeywords = $seoData['keywords'];
    } elseif (View::hasSection('keywords_override')) {
        $finalKeywords = trim(View::yieldContent('keywords_override'));
    } elseif (View::hasSection('keywords_custom')) {
        $finalKeywords = trim(View::yieldContent('keywords_custom'));
    } else {
        $finalKeywords = $seoData['keywords'];
    }
    $finalKeywords = rtrim($finalKeywords, ",");

    $finalRobots = View::hasSection('robots') ? trim(View::yieldContent('robots')) : $seoData['robots'];

    // OG Title & Description priority:
    if (View::hasSection('og_title')) {
        $finalOgTitle = trim(View::yieldContent('og_title'));
    } elseif (!empty($seoData['og_title'])) {
        $finalOgTitle = $seoData['og_title'];
    } else {
        $finalOgTitle = $finalTitle;
    }

    if (View::hasSection('og_description')) {
        $finalOgDesc = trim(View::yieldContent('og_description'));
    } elseif (!empty($seoData['og_description'])) {
        $finalOgDesc = $seoData['og_description'];
    } else {
        $finalOgDesc = $finalDesc;
    }
@endphp

{{-- Primary Meta Tags --}}
<title>@auth {{ auth()->user()->unseenNotifications() ? '('.auth()->user()->unseenNotifications().') ' : null }} @endauth{{ $finalTitle }}</title>
@if (!empty($finalDesc))
<meta name="description" content="{{ $finalDesc }}">
@endif
@if (!empty($finalKeywords))
<meta name="keywords" content="{{ $finalKeywords }}">
@endif
@if (!empty($finalRobots))
<meta name="robots" content="{{ $finalRobots }}">
@endif
<link rel="canonical" href="{{ $seoData['canonical'] }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $seoData['og_type'] }}">
<meta property="og:site_name" content="{{ config('settings.title', 'ImagineBuddy') }}">
<meta property="og:title" content="{{ $finalOgTitle }}">
<meta property="og:description" content="{{ $finalOgDesc }}">
<meta property="og:url" content="{{ $seoData['canonical'] }}">
@if (!empty($seoData['og_image']))
<meta property="og:image" content="{{ $seoData['og_image'] }}">
@endif

{{-- Twitter Cards --}}
<meta name="twitter:card" content="{{ $seoData['twitter_card'] }}">
<meta name="twitter:title" content="{{ $finalOgTitle }}">
<meta name="twitter:description" content="{{ $finalOgDesc }}">
@if (!empty($seoData['og_image']))
<meta name="twitter:image" content="{{ $seoData['og_image'] }}">
@endif

{{-- Schema.org JSON-LD Structured Data --}}
{!! seo()->generateJsonLd() !!}
