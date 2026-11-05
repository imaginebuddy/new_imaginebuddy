@extends('layouts.app')

@section('title'){{ $metaTitle ?? $title }} - @endsection

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
    'ctaText' => $ctaText ?? 'Explore Solutions',
    'ctaUrl' => $ctaUrl ?? '#catalog'
  ])

  {{-- 2. Master 3-Column Solution Cards Grid --}}
  <div id="catalog">
    @include('cms.sections.hub-grid', [
      'items' => $items,
      'prefix' => $prefix,
      'verticalName' => $verticalName ?? 'SOLUTION',
      'sectionBadge' => 'CATALOG',
      'sectionHeading' => $gridHeading ?? 'All ' . ($verticalName ?? 'Photography') . ' Solutions',
      'sectionSubheading' => $gridSubheading ?? 'Browse specialized photography solutions tailored to your products and marketing channels.'
    ])
  </div>

  {{-- 3. Social Proof Client Logos --}}
  @if (View::exists('includes.client-logos-slider'))
    <section class="cms-section cms-section-sm bg-body">
      <div class="container text-center">
        <span class="cms-eyebrow-badge mb-3">TRUSTED BY 500+ D2C BRANDS & AGENCIES</span>
        @include('includes.client-logos-slider')
      </div>
    </section>
  @endif

  {{-- 4. Floating High-Impact CTA Banner --}}
  @include('cms.sections.cta-card', [
    'badge' => 'GET STARTED',
    'heading' => 'Scale Your Product Visuals with AI',
    'subheading' => 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.',
    'ctaText' => 'Create Free Account',
    'ctaUrl' => url('register')
  ])
</main>
@endsection
