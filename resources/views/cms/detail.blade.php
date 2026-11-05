@extends('layouts.app')

@php
  // Build fallback keywords
  $allKeywords = array_unique(array_filter(array_map('trim', array_merge(
    !empty($page->meta_keywords) ? explode(',', $page->meta_keywords) : [],
    !empty($page->primary_keyword) ? [$page->primary_keyword] : [],
    !empty($page->secondary_keywords) ? explode(',', $page->secondary_keywords) : []
  ))));
  $keywordsString = implode(', ', $allKeywords);

  $siteTitle = config('settings.title', 'ImagineBuddy');
  $logoUrl = url('public/img', config('settings.logo', 'logo.png'));
  $coverImageUrl = !empty($page->featured_image) ? $page->featured_image_url : $logoUrl;

  // Schema.org Structured Data
  $schemaService = [
    '@context' => 'https://schema.org',
    '@type' => in_array($page->schema_type, ['Service', 'Product', 'TechArticle', 'HowTo']) ? $page->schema_type : 'Service',
    'name' => $page->title,
    'description' => !empty($page->meta_description) ? $page->meta_description : ($page->excerpt ?: $page->title),
    'image' => $coverImageUrl,
    'url' => $page->public_url,
    'provider' => [
      '@type' => 'Organization',
      'name' => $siteTitle,
      'url' => url('/')
    ]
  ];

  $schemaBreadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => url('/')
      ]
    ]
  ];
  $pos = 2;
  if (!empty($page->route_prefix)) {
    $schemaBreadcrumb['itemListElement'][] = [
      '@type' => 'ListItem',
      'position' => $pos++,
      'name' => ucwords(str_replace('-', ' ', $page->route_prefix)),
      'item' => url($page->route_prefix)
    ];
  }
  $schemaBreadcrumb['itemListElement'][] = [
    '@type' => 'ListItem',
    'position' => $pos,
    'name' => $page->title,
    'item' => $page->public_url
  ];

  // Resolve prompts from attached items
  $attachedPrompts = $page->activeItems()
    ->where('item_type', 'prompt')
    ->with('prompt.user')
    ->get()
    ->pluck('prompt')
    ->filter();
@endphp

@section('title'){{ !empty($page->meta_title) ? $page->meta_title : $page->title }} - @endsection

@section('css')
<link href="{{ asset('public/css/cms-pages.css') }}?v={{ filemtime(public_path('css/cms-pages.css')) }}" rel="stylesheet">
@endsection

@section('content')
<main class="cms-page-wrapper">
  @php
    $heroRendered = false;
    $promptGridRendered = false;
    $testimonialsRendered = false;
    $relatedSiloRendered = false;
  @endphp

  @foreach ($page->activeSections as $section)
    @switch($section->type)
      @case('hero')
        @include('cms.sections.detail-hero', ['page' => $page, 'section' => $section])
        @php $heroRendered = true; @endphp
        @break

      @case('problem_solution')
        @include('cms.sections.problem-solution', ['page' => $page, 'section' => $section])
        @break

      @case('role_grid')
        @include('cms.sections.role-grid', ['page' => $page, 'section' => $section])
        @break

      @case('alternating_rows')
      @case('workflow_steps')
        @include('cms.sections.alternating-rows', ['page' => $page, 'section' => $section])
        @break

      @case('specs_table')
        @include('cms.sections.specs-table', ['page' => $page, 'section' => $section])
        @break

      @case('metrics_counter')
        @include('cms.sections.metrics-counter', ['page' => $page, 'section' => $section])
        @break

      @case('prompt_grid')
        @if (!empty($attachedPrompts) && $attachedPrompts->count() > 0 && View::exists('guides.prompts-showcase'))
          <section class="cms-section" id="prompts-showcase">
            <div class="container">
              <div class="cms-section-header">
                <span class="cms-eyebrow-badge mb-2">{{ $section->badge ?: 'PROMPT RECIPES' }}</span>
                <h2 class="cms-section-title">{{ $section->heading ?: 'Connected Prompt Library' }}</h2>
                @if (!empty($section->subheading))
                  <p class="cms-section-sub">{{ $section->subheading }}</p>
                @else
                  <p class="cms-section-sub">Tested photoshoot recipes and visual prompts crafted specifically for {{ strtolower($page->title) }}.</p>
                @endif
              </div>
              @include('guides.prompts-showcase', ['prompts' => $attachedPrompts, 'hideShowcaseHeader' => true])
            </div>
          </section>
          @php $promptGridRendered = true; @endphp
        @endif
        @break

      @case('testimonials')
        @if (isset($testimonials) && $testimonials->isNotEmpty())
          @include('cms.sections.testimonials', ['page' => $page, 'section' => $section, 'testimonials' => $testimonials])
          @php $testimonialsRendered = true; @endphp
        @endif
        @break

      @case('faq_accordion')
        {{-- Auto-showcase connected prompts before FAQ if not explicitly placed in section builder --}}
        @if (!$promptGridRendered && !empty($attachedPrompts) && $attachedPrompts->count() > 0 && View::exists('guides.prompts-showcase'))
          <section class="cms-section" id="prompts-showcase">
            <div class="container">
              <div class="cms-section-header">
                <span class="cms-eyebrow-badge mb-2">PROMPT RECIPES</span>
                <h2 class="cms-section-title">Tested Prompts for {{ $page->title }}</h2>
                <p class="cms-section-sub">Production-ready prompt formulas crafted for studio-quality {{ strtolower($page->title) }}.</p>
              </div>
              @include('guides.prompts-showcase', ['prompts' => $attachedPrompts, 'hideShowcaseHeader' => true])
            </div>
          </section>
          @php $promptGridRendered = true; @endphp
        @endif

        {{-- Auto-showcase testimonials before FAQ if not explicitly placed in section builder --}}
        @if (!$testimonialsRendered && isset($testimonials) && $testimonials->isNotEmpty())
          @include('cms.sections.testimonials', ['page' => $page, 'section' => null, 'testimonials' => $testimonials])
          @php $testimonialsRendered = true; @endphp
        @endif

        @include('cms.sections.faq-accordion', ['page' => $page, 'section' => $section])
        @break

      @case('related_silo')
      @case('explore_more')
        @include('cms.sections.related-silo', ['page' => $page, 'section' => $section, 'relatedPages' => $relatedPages ?? null])
        @php $relatedSiloRendered = true; @endphp
        @break

      @case('cta_banner')
        {{-- Auto-render Explore More Use Cases before bottom CTA banner if not explicitly placed earlier --}}
        @if (!$relatedSiloRendered)
          @include('cms.sections.related-silo', ['page' => $page, 'section' => null, 'relatedPages' => $relatedPages ?? null])
          @php $relatedSiloRendered = true; @endphp
        @endif
        @include('cms.sections.cta-card', ['page' => $page, 'section' => $section])
        @break

      @default
        <section class="cms-section">
          <div class="container">
            @if (!empty($section->heading))
              <div class="cms-section-header">
                @if (!empty($section->badge))
                  <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
                @endif
                <h2 class="cms-section-title">{{ $section->heading }}</h2>
                @if (!empty($section->subheading))
                  <p class="cms-section-sub">{{ $section->subheading }}</p>
                @endif
              </div>
            @endif
            @if (!empty($section->content))
              <div class="cms-feature-card">
                {!! $section->content !!}
              </div>
            @endif
          </div>
        </section>
    @endswitch
  @endforeach

  {{-- Fallback prompt showcase if page has attached prompts but no FAQ section was present --}}
  @if (!$promptGridRendered && !empty($attachedPrompts) && $attachedPrompts->count() > 0 && View::exists('guides.prompts-showcase'))
    <section class="cms-section" id="prompts-showcase">
      <div class="container">
        <div class="cms-section-header">
          <span class="cms-eyebrow-badge mb-2">PROMPT RECIPES</span>
          <h2 class="cms-section-title">Tested Prompts for {{ $page->title }}</h2>
          <p class="cms-section-sub">Production-ready prompt formulas crafted for studio-quality {{ strtolower($page->title) }}.</p>
        </div>
        @include('guides.prompts-showcase', ['prompts' => $attachedPrompts, 'hideShowcaseHeader' => true])
      </div>
    </section>
  @endif

  {{-- Fallback Hero if no hero section was configured --}}
  @if (!$heroRendered)
    @php
      $dummySection = new \App\Models\SeoLandingPageSection([
        'heading' => $page->title,
        'subheading' => $page->excerpt,
        'badge' => $page->header_badge
      ]);
    @endphp
    @include('cms.sections.detail-hero', ['page' => $page, 'section' => $dummySection])
  @endif



  {{-- Bottom Fallback Explore More if not already rendered --}}
  @if (!$relatedSiloRendered)
    @include('cms.sections.related-silo', ['page' => $page, 'section' => null, 'relatedPages' => $relatedPages ?? null])
    @php $relatedSiloRendered = true; @endphp
  @endif

  {{-- Bottom Fallback CTA Banner if not in sections --}}
  @if (!$page->activeSections->where('type', 'cta_banner')->count())
    @include('cms.sections.cta-card', [
      'badge' => 'START TODAY',
      'heading' => 'Ready to Scale Your ' . ($page->title) . '?',
      'subheading' => 'Eliminate studio bottlenecks and generate compliant, studio-grade images in minutes.',
      'ctaText' => 'Start Free Trial',
      'ctaUrl' => url('register')
    ])
  @endif
</main>

{{-- Structured Data JSON-LD --}}
<script type="application/ld+json">
{!! json_encode($schemaService, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode($schemaBreadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
