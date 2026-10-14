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

    $pageParam = request()->query('page');
    if ($pageParam !== null && is_numeric($pageParam) && (int) $pageParam > 1) {
        $finalRobots = (!empty($finalRobots) && str_contains($finalRobots, 'nofollow')) ? 'noindex, nofollow' : 'noindex, follow';
    }

    $isDiscoverEligible = request()->routeIs(['guides.show', 'blog.show', 'guides.preview', 'blog.preview'])
        || (request()->is('guides/*') && !request()->is('guides/preview/*'))
        || (request()->is('blog/*') && !request()->is('blog/category/*') && !request()->is('blog/tag/*') && !request()->is('blog/preview/*'));

    // Google Discover Requirement: max-image-preview:large ONLY on guides/{slug} and blog/{slug}
    if ($isDiscoverEligible && !empty($finalRobots) && str_contains($finalRobots, 'index') && !str_contains($finalRobots, 'max-image-preview')) {
        $finalRobots .= ', max-image-preview:large';
    }

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

    // OG Image priority:
    if (View::hasSection('og_image')) {
        $finalOgImage = trim(View::yieldContent('og_image'));
    } elseif (!empty($seoData['og_image'])) {
        $finalOgImage = $seoData['og_image'];
    } else {
        $finalOgImage = null;
    }

    // OG Type priority:
    if (View::hasSection('og_type')) {
        $finalOgType = trim(View::yieldContent('og_type'));
    } else {
        $finalOgType = $seoData['og_type'] ?: 'website';
    }

    // Twitter Card priority:
    if (View::hasSection('twitter_card')) {
        $finalTwitterCard = trim(View::yieldContent('twitter_card'));
    } elseif ($isDiscoverEligible) {
        $finalTwitterCard = 'summary_large_image';
    } else {
        $finalTwitterCard = !empty($seoData['twitter_card']) ? $seoData['twitter_card'] : 'summary';
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
@if ($isDiscoverEligible)
<meta name="googlebot" content="{{ $finalRobots }}">
@endif
@endif
<link rel="canonical" href="{{ $seoData['canonical'] }}">

{{-- Open Graph / Facebook (Discover & Social Acquisition) --}}
<meta property="og:type" content="{{ $finalOgType }}">
<meta property="og:site_name" content="{{ config('settings.title', 'ImagineBuddy') }}">
<meta property="og:title" content="{{ $finalOgTitle }}">
<meta property="og:description" content="{{ $finalOgDesc }}">
<meta property="og:url" content="{{ $seoData['canonical'] }}">
@if (!empty($finalOgImage))
<meta property="og:image" content="{{ $finalOgImage }}">
@if ($isDiscoverEligible)
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $finalOgTitle }}">
@endif
@endif

@if ($isDiscoverEligible && $finalOgType === 'article')
@if (View::hasSection('article_published_time'))
<meta property="article:published_time" content="@yield('article_published_time')">
@endif
@if (View::hasSection('article_modified_time'))
<meta property="article:modified_time" content="@yield('article_modified_time')">
@endif
@if (View::hasSection('article_author'))
<meta property="article:author" content="@yield('article_author')">
@endif
@if (View::hasSection('article_section'))
<meta property="article:section" content="@yield('article_section')">
@endif
@endif

{{-- Twitter Cards (Large Image Preview for Discover parity on eligible pages) --}}
<meta name="twitter:card" content="{{ $finalTwitterCard }}">
<meta name="twitter:title" content="{{ $finalOgTitle }}">
<meta name="twitter:description" content="{{ $finalOgDesc }}">
@if (!empty($finalOgImage))
<meta name="twitter:image" content="{{ $finalOgImage }}">
@endif

{{-- Schema.org JSON-LD Structured Data --}}
{!! seo()->generateJsonLd() !!}
