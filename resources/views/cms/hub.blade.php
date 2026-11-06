@extends('layouts.app')

@section('title'){{ $metaTitle ?? $title }} - @endsection

@if (!empty($metaDescription))
@section('description_custom'){{ $metaDescription }}@endsection
@endif

@if (!empty($metaKeywords))
@section('keywords_custom'){{ $metaKeywords }}@endsection
@endif

@section('css')
<link href="{{ asset('public/css/cms-pages.css') }}?v={{ filemtime(public_path('css/cms-pages.css')) }}" rel="stylesheet">
@endsection

@section('content')
<main class="cms-page-wrapper">
  {{-- 1. Hub Hero with Title, Filter Pills & Search --}}
  @include('cms.sections.hub-hero', [
    'badge' => $badge ?? 'DIRECTORY & SOLUTIONS',
    'title' => $title,
    'subtitle' => $subtitle ?? '',
    'filterPills' => $filterPills ?? [],
    'ctaText' => $heroCtaText ?? ($ctaText ?? 'Explore Solutions'),
    'ctaUrl' => $heroCtaUrl ?? ($ctaUrl ?? '#catalog')
  ])

  {{-- 2. Master 3-Column Solution Cards Grid --}}
  <div id="catalog">
    @include('cms.sections.hub-grid', [
      'items' => $items,
      'prefix' => $prefix,
      'verticalName' => $verticalName ?? 'SOLUTION',
      'sectionBadge' => $gridBadge ?? 'CATALOG',
      'sectionHeading' => $gridHeading ?? 'All ' . ($verticalName ?? 'Photography') . ' Solutions',
      'sectionSubheading' => $gridSubheading ?? 'Browse specialized photography solutions tailored to your products and marketing channels.'
    ])
  </div>

  {{-- 3. Social Proof Client Logos --}}
  @if (View::exists('includes.client-logos-slider'))
    <section class="cms-section cms-section-sm bg-body">
      <div class="container text-center">
        <span class="cms-eyebrow-badge mb-3">{{ $socialProofBadge ?? 'TRUSTED BY 500+ D2C BRANDS & AGENCIES' }}</span>
        @include('includes.client-logos-slider')
      </div>
    </section>
  @endif

  {{-- 4. Floating High-Impact CTA Banner --}}
  @include('cms.sections.cta-card', [
    'badge' => $ctaBadge ?? 'GET STARTED',
    'heading' => $ctaHeading ?? 'Scale Your Product Visuals with AI',
    'subheading' => $ctaSubheading ?? 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
    'ctaText' => $ctaBtnText ?? 'Create Free Account',
    'ctaUrl' => $ctaBtnUrl ?? url('register'),
    'ctaSecondaryText' => $ctaSecondaryBtnText ?? 'View Pricing Plans',
    'ctaSecondaryUrl' => $ctaSecondaryBtnUrl ?? url('pricing')
  ])
</main>
@endsection
