@extends('layouts.app')

@php
  $pageTitle = !empty($collection->meta_title) ? $collection->meta_title : ($collection->title . ' - AI Prompts for Midjourney, Flux & Gemini | ' . (config('settings.title') ?: config('app.name')));
  $pageDescription = !empty($collection->meta_description) ? $collection->meta_description : ($collection->summary ?: ('Curated collection of ' . $images->total() . '+ production-tested commercial photography prompts for ' . $collection->title . '.'));
  $pageKeywords = !empty($collection->meta_keywords) ? $collection->meta_keywords : ($collection->title . ', midjourney prompts, flux prompts, ai product photography, commercial prompts');
  $canonicalUrl = url('prompts/' . $collection->slug);
  $faqs = $collection->faq_items;

  // Hero Preview Images Mosaic
  $heroPreviewImages = $images->take(3);
  $hImg0 = $heroPreviewImages->get(0);
  $hImg1 = $heroPreviewImages->get(1);
  $hImg2 = $heroPreviewImages->get(2);

  $getHeroImgUrl = function($img) {
    if (!$img) return '';
    if (!empty($img->preview)) {
      return Storage::url(config('path.preview') . $img->preview);
    }
    if (!empty($img->thumbnail)) {
      return Storage::url(config('path.thumbnail') . $img->thumbnail);
    }
    $stock = $img->stock ? $img->stock->first() : null;
    return $stock ? Storage::url(config('path.small') . $stock->name) : '';
  };

  $hSrc0 = $getHeroImgUrl($hImg0) ?: $collection->cover_image_url;
  $hSrc1 = $getHeroImgUrl($hImg1) ?: $hSrc0;
  $hSrc2 = $getHeroImgUrl($hImg2) ?: $hSrc0;
@endphp

@section('title'){{ $pageTitle }}@endsection
@section('description_custom'){{ Helper::removeLineBreak(e($pageDescription)) }}@endsection
@section('keywords_custom'){{ $pageKeywords }}@endsection

@section('content')
<!-- Structured Data: BreadcrumbList -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ url('/') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Prompts",
      "item": "{{ url('prompts') }}"
    },
    @if ($collection->category)
    {
      "@type": "ListItem",
      "position": 3,
      "name": "{{ $collection->category->name }}",
      "item": "{{ url('prompts') }}?category={{ $collection->category->slug }}"
    },
    {
      "@type": "ListItem",
      "position": 4,
      "name": "{{ $collection->title }}",
      "item": "{{ $canonicalUrl }}"
    }
    @else
    {
      "@type": "ListItem",
      "position": 3,
      "name": "{{ $collection->title }}",
      "item": "{{ $canonicalUrl }}"
    }
    @endif
  ]
}
</script>

<!-- Structured Data: FAQPage Schema -->
@if (!empty($faqs))
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach ($faqs as $fIndex => $faq)
    {
      "@type": "Question",
      "name": {!! json_encode($faq['question']) !!},
      "acceptedAnswer": {
        "@type": "Answer",
        "text": {!! json_encode($faq['answer']) !!}
      }
    }{{ $fIndex < count($faqs) - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endif

<div class="prompts-detail-wrapper">

  <!-- ============================================================== -->
  <!-- 1. SCALIO EDITORIAL DARK HERO HEADER                           -->
  <!-- ============================================================== -->
  <section class="scalio-hero position-relative overflow-hidden d-flex flex-column justify-content-center" style="background: linear-gradient(180deg, #05110B 0%, #091A11 100%); color: #ffffff; min-height: 100vh; padding-top: 120px; padding-bottom: 70px;">
    @if (!empty($collection->cover_image))
      <!-- Custom Cover Banner Hero Background Layer (Full 100% Opacity) -->
      <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 0; pointer-events: none; overflow: hidden;">
        <img src="{{ $collection->cover_image_url }}" alt="{{ $collection->title }} Cover Banner" class="w-100 h-100" style="object-fit: cover; object-position: center; opacity: 1; transform: scale(1.01);">
        <!-- Soft gradient protecting text contrast on left while keeping banner artwork 100% vibrant -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(5, 17, 11, 0.65) 0%, rgba(5, 17, 11, 0.35) 45%, transparent 100%), linear-gradient(180deg, rgba(5, 17, 11, 0.30) 0%, transparent 40%, rgba(5, 17, 11, 0.85) 100%);"></div>
      </div>
    @else
      <!-- Ambient Green Glow Filter (Default when no custom cover banner is uploaded) -->
      <div class="position-absolute" style="top: -120px; left: 50%; transform: translateX(-50%); width: 750px; height: 420px; background: radial-gradient(circle at 50% 30%, rgba(0, 214, 144, 0.22) 0%, transparent 68%); pointer-events: none; z-index: 1;"></div>
    @endif

    <div class="container position-relative my-auto" style="z-index: 2;">
      
      <!-- Breadcrumb Navigation -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 align-items-center flex-wrap" style="font-size: 0.85rem;">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ url('prompts') }}" class="text-white-50 text-decoration-none">Prompts</a></li>
          @if ($collection->category)
            <li class="breadcrumb-item"><a href="{{ url('prompts') }}?category={{ $collection->category->slug }}" class="text-white-50 text-decoration-none">{{ $collection->category->name }}</a></li>
          @endif
          <li class="breadcrumb-item active text-white fw-medium" aria-current="page">{{ $collection->title }}</li>
        </ol>
      </nav>

      <div class="row align-items-center g-4 g-lg-5">
        
        <!-- Left Column: Editorial Details & Scalio CTAs -->
        <div class="col-lg-6 col-xl-6">
          <!-- Metadata Badges Strip -->
          <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center" style="background: rgba(0, 214, 144, 0.16); color: #00d690; border: 1px solid rgba(0, 214, 144, 0.35); font-size: 0.78rem;">
              <i class="bi bi-stars me-2"></i> {{ $collection->ai_engine ?: 'Midjourney, Flux & Gemini Tested' }}
            </span>

            <span class="badge rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.08); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15); font-size: 0.78rem;">
              <i class="bi bi-images me-2"></i> {{ $images->total() }} Tested Prompts
            </span>

            <span class="badge rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.08); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15); font-size: 0.78rem;">
              <i class="bi bi-patch-check-fill text-mint me-2"></i> Commercial Ready
            </span>
          </div>

          <!-- Main H1 Heading -->
          <h1 class="fw-bold text-white mb-3 tracking-tight" style="font-size: clamp(2.2rem, 4.3vw, 3.4rem); line-height: 1.15; text-shadow: 0 2px 14px rgba(0, 0, 0, 0.85);">
            {{ $collection->title }}
          </h1>

          <!-- Editorial Lead Description -->
          <p class="text-white-50 lead mb-4" style="font-size: 1.1rem; line-height: 1.65; max-width: 620px; text-shadow: 0 1px 8px rgba(0, 0, 0, 0.75);">
            {{ $collection->summary ?: 'Curated collection of production-tested commercial photography prompts for e-commerce, advertising campaigns, and luxury catalog lookbooks. Engineered to preserve realistic textures, studio lighting, and true-to-life product details.' }}
          </p>

          <!-- Scalio Primary Action Buttons -->
          <div class="d-flex flex-wrap align-items-center gap-3 pt-1">
            <a href="#prompts-grid" class="btn btn-lg rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm" style="background: #00d690; border: none; font-size: 0.96rem;">
              Browse Prompts &darr;
            </a>

            <a href="{{ $promptSettings->banner_cta_url ?: url('register') }}" class="btn btn-lg rounded-pill px-4 py-2.5 fw-semibold btn-outline-light" style="font-size: 0.96rem; border-color: rgba(255, 255, 255, 0.3); background: rgba(255, 255, 255, 0.05);">
              <i class="bi bi-lightning-charge text-mint me-2"></i> Try 1-Click Studio
            </a>

            <button type="button" class="btn btn-lg rounded-pill px-3 py-2.5 fw-medium text-white-50 btn-outline-light" onclick="navigator.clipboard.writeText(window.location.href); $(this).html('<i class=\'bi bi-check2 text-mint me-2\'></i> Copied link!'); setTimeout(() => $(this).html('<i class=\'bi bi-share me-2\'></i> Share'), 2500);" style="font-size: 0.92rem; border-color: rgba(255, 255, 255, 0.18); background: transparent;">
              <i class="bi bi-share me-2"></i> Share
            </button>
          </div>
        </div>

        <!-- Right Column: Scalio Editorial Lookbook Showcase -->
        <div class="col-lg-6 col-xl-6">
          @if ($images->count() > 0)
            <div class="scalio-hero-showcase position-relative mx-auto ms-lg-auto" style="max-width: 505px;">
              <div class="wrap-collection mb-0 rounded-4 overflow-hidden shadow-lg position-relative" style="margin-bottom: 0 !important; padding-bottom: 84%; border: 1.5px solid rgba(255, 255, 255, 0.18); box-shadow: 0 25px 55px rgba(0, 0, 0, 0.5) !important;">
                <div class="grid-collection" style="border-radius: 16px; top: 0; left: 0;">
                  <div class="collection-1">
                    @if ($hSrc0)
                      <a href="{{ $hImg0 ? url('prompt', $hImg0->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                        <img class="img-collection" src="{{ $hSrc0 }}" alt="{{ $collection->title }} - Primary Shot" loading="eager" style="object-fit: cover;">
                      </a>
                    @endif
                  </div>

                  <div class="collection-right">
                    <div class="collection-2">
                      @if ($hSrc1)
                        <a href="{{ $hImg1 ? url('prompt', $hImg1->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                          <img class="img-collection" src="{{ $hSrc1 }}" alt="{{ $collection->title }} - Angle 2" loading="eager" style="object-fit: cover;">
                        </a>
                      @endif
                    </div>

                    <div class="collection-2">
                      @if ($hSrc2)
                        <a href="{{ $hImg2 ? url('prompt', $hImg2->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                          <img class="img-collection" src="{{ $hSrc2 }}" alt="{{ $collection->title }} - Angle 3" loading="eager" style="object-fit: cover;">
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              <!-- Micro Lookbook Strip -->
              <div class="d-flex align-items-center justify-content-between pt-2.5 px-1 text-white-50 small">
                <span class="d-inline-flex align-items-center">
                  <i class="bi bi-camera-fill text-mint me-2"></i> Multi-Angle Studio Lookbook
                </span>
                <a href="#prompts-grid" class="text-mint text-decoration-none fw-semibold">
                  View all {{ $images->total() }} &darr;
                </a>
              </div>
            </div>
          @else
            <div class="rounded-4 overflow-hidden border shadow-lg mx-auto ms-lg-auto" style="max-width: 505px; border-color: rgba(255,255,255,0.15) !important;">
              <img src="{{ $collection->cover_image_url }}" alt="{{ $collection->title }}" class="w-100" style="max-height: 420px; object-fit: cover;">
            </div>
          @endif
        </div>

      </div>
    </div>
  </section>

  <!-- Main Content Container with Warm Editorial Theme -->
  <div class="scalio-body-wrap py-4 py-md-5">
    <div class="container px-3 px-md-4">

      <!-- ============================================================== -->
      <!-- 2. PROMPTS GRID SECTION (STRICTLY PRESERVED AS ORDERED BY USER)-->
      <!-- ============================================================== -->
      <div id="prompts-grid" class="mb-4 mb-md-5 pt-3 pt-md-4 border-top border-custom">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-end mb-4 pb-3 border-bottom border-custom gap-3">
          <div>
            <span class="badge badge-dark-custom rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
              Angle Catalog
            </span>
            <h2 class="fw-bold title-custom m-0 h3">Prompts in this Collection</h2>
            <p class="text-muted small mb-0 mt-1">
              Copy and adapt these studio prompt formulas across multiple angles for your own product catalog.
            </p>
          </div>
          <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-3 py-2 fw-medium align-self-start align-self-sm-auto" style="font-size: 0.85rem;">
            <i class="bi bi-grid-fill text-mint me-2"></i> Showing <span id="showingCount">{{ $images->count() }}</span> of {{ $images->total() }} {{ str_plural('Prompt', $images->total()) }}
          </span>
        </div>

        @if ($images->total() != 0)
          <div class="dataResult">
            @include('includes.images', ['images' => $images])

            <!-- Crawlable Pagination for Googlebot & Progressive Fallback -->
            @if ($images->hasPages())
              <div id="linkPagination" class="my-4 d-flex justify-content-center">
                {{ $images->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
              </div>
            @endif
          </div>

          <!-- Scroll Sentinel for Prompts Grid -->
          <div id="promptsSentinel" class="w-100" style="height: 1px; visibility: hidden;"></div>

          <!-- Infinite Scroll Loader -->
          <div id="infiniteScrollLoader" class="text-center py-4 my-3 d-none">
            <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted small mt-2 mb-0">Loading more prompts...</p>
          </div>
        @else
          <div class="text-center py-5 my-4 bg-card-custom rounded-4 border border-custom p-4 p-md-5">
            <p class="text-muted m-0">No active prompts found in this collection.</p>
          </div>
        @endif
      </div>

      <!-- ============================================================== -->
      <!-- 3. THE SHORTCUT: DYNAMIC BEFORE & AFTER STUDIO SHOWCASE        -->
      <!-- ============================================================== -->
      @if ($collection->show_shortcut_section ?? true)
      @php
        $scEyebrow = $collection->shortcut_eyebrow ?: 'THE SHORTCUT';
        $scTitle = $collection->shortcut_title ?: 'You don’t need to learn prompting to get the images you want';
        $scDesc = $collection->shortcut_description ?: ('Every prompt above invents a new ' . strtolower($collection->title) . ' image that isn\'t yours, and re-rolling gives you a different one each time. Upload a photo of what you actually sell and ImagineBuddy does the rest — no prompt, no re-rolls, no studio.');
        $scFeatures = $collection->shortcut_feature_items;
        $scCtaText = $collection->shortcut_cta_text ?: ($promptSettings->banner_cta_text ?: 'Try ImagineBuddy Free');
        $scCtaUrl = $collection->shortcut_cta_url ?: ($promptSettings->banner_cta_url ?: url('register'));
        $scSubtext = $collection->shortcut_subtext ?: 'Free to start · No card needed · Works on web, iOS and Android';
        $scCaption = $collection->shortcut_compare_caption ?: 'Hover to compare — both frames generated by ImagineBuddy from one uploaded photo.';

        // Fallback images
        $defaultBefore = asset('public/img/White-Oud-Perfume-01.webp');
        $defaultAfter = asset('public/img/premium-white-oud-perfume-ad-02.webp');

        $beforeImgUrl = $collection->raw_image_url ?: ($collection->cover_image_url ?: $defaultBefore);
        $afterImgUrl = $collection->transformed_image_url ?: ($hSrc0 ?: $defaultAfter);

        // Dynamically compute exact aspect ratio to prevent cropping and eliminate layout shift
        $sliderAspectRatio = '4 / 3';
        if (!empty($collection->raw_image)) {
            $rawPath = public_path('uploads/prompt-collections/before/' . $collection->raw_image);
            if (file_exists($rawPath)) {
                $dims = @getimagesize($rawPath);
                if ($dims && $dims[0] > 0 && $dims[1] > 0) {
                    $sliderAspectRatio = $dims[0] . ' / ' . $dims[1];
                }
            }
        }
        if ($sliderAspectRatio === '4 / 3' && !empty($collection->transformed_image)) {
            $transPath = public_path('uploads/prompt-collections/after/' . $collection->transformed_image);
            if (file_exists($transPath)) {
                $dims = @getimagesize($transPath);
                if ($dims && $dims[0] > 0 && $dims[1] > 0) {
                    $sliderAspectRatio = $dims[0] . ' / ' . $dims[1];
                }
            }
        }
      @endphp

      <section class="shortcut-transformation-section rounded-4 p-4 p-md-5 mb-5 position-relative overflow-hidden" id="shortcutSection">
        <div class="row align-items-center g-4 g-lg-5">
          
          <!-- Left Column: Content & Benefits (Matching Reference Design) -->
          <div class="col-lg-6">
            <div class="shortcut-eyebrow fw-bold text-uppercase mb-3 d-inline-flex align-items-center gap-2">
              <span class="shortcut-eyebrow-dot rounded-circle d-inline-block"></span>
              {{ $scEyebrow }}
            </div>

            <h2 class="fw-bold shortcut-heading mb-3.5 tracking-tight">
              {{ $scTitle }}
            </h2>

            <p class="shortcut-desc lead mb-4 pb-1">
              {{ $scDesc }}
            </p>

            <!-- 4 Features 2x2 Grid with Checkmarks -->
            <div class="row g-2.5 mb-4 pb-2">
              @foreach ($scFeatures as $featureText)
                <div class="col-sm-6">
                  <div class="d-flex align-items-center gap-2">
                    <span class="shortcut-check-icon d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                      <i class="bi bi-check-lg"></i>
                    </span>
                    <span class="shortcut-feature-text fw-medium small">{{ $featureText }}</span>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- CTA Button & Subtext -->
            <div class="pt-1">
              <a href="{{ $scCtaUrl }}" class="btn btn-lg rounded-pill px-4 py-3 fw-bold text-dark shadow-sm shortcut-cta-btn d-inline-flex align-items-center gap-2">
                <span>{{ $scCtaText }}</span> <i class="bi bi-arrow-right"></i>
              </a>
              <div class="shortcut-subtext mt-2 small">
                {{ $scSubtext }}
              </div>
            </div>
          </div>

          <!-- Right Column: Interactive Before/After Card (As shown in reference image) -->
          <div class="col-lg-6">
            <div class="shortcut-compare-card card border-0 rounded-4 p-3 position-relative">
              
              <!-- Comparison Frame -->
              <div class="shortcut-slider-container position-relative overflow-hidden rounded-4" id="shortcutSlider" style="aspect-ratio: {{ $sliderAspectRatio }};">
                
                <!-- Background Image: Transformed (After) -->
                <img 
                  src="{{ $afterImgUrl }}" 
                  alt="{{ $collection->title }} - After AI Transformation" 
                  class="shortcut-img shortcut-after-img w-100 h-100 position-absolute top-0 start-0" 
                  draggable="false"
                  loading="lazy"
                  onerror="if (this.src !== '{{ $defaultAfter }}') this.src='{{ $defaultAfter }}';"
                >

                <!-- Foreground Clipped Image: Raw Product (Before) -->
                <div class="shortcut-before-clip position-absolute top-0 start-0 w-100 h-100 overflow-hidden" id="shortcutBeforeClip" style="clip-path: inset(0 50% 0 0);">
                  <img 
                    src="{{ $beforeImgUrl }}" 
                    alt="{{ $collection->title }} - Before Raw Snapshot" 
                    class="shortcut-img shortcut-before-img position-absolute top-0 start-0 w-100 h-100" 
                    draggable="false"
                    loading="lazy"
                    onerror="if (this.src !== '{{ $defaultBefore }}') this.src='{{ $defaultBefore }}';"
                  >
                </div>

                <!-- Floating Before Badge -->
                <div class="position-absolute top-0 start-0 m-3" style="z-index: 10; pointer-events: none;">
                  <span class="badge rounded-pill bg-dark bg-opacity-75 text-white px-3 py-1.5 fw-semibold shadow-xs" style="font-size: 0.75rem; backdrop-filter: blur(4px);">
                    Before
                  </span>
                </div>

                <!-- Slider Divider Line & Center Circular Handle -->
                <div class="shortcut-slider-divider position-absolute top-0 bottom-0" id="shortcutDivider" style="left: 50%;">
                  <div class="shortcut-slider-handle rounded-circle shadow d-flex align-items-center justify-content-center" id="shortcutHandle" title="Hover or drag to compare">
                    <i class="bi bi-arrow-left-right text-dark"></i>
                  </div>
                </div>

              </div>

              <!-- Caption Below Image Card -->
              <div class="text-center pt-2.5 px-2">
                <small class="shortcut-caption d-block">
                  {{ $scCaption }}
                </small>
              </div>

            </div>
          </div>

        </div>
      </section>
      @endif

      <!-- ============================================================== -->
      <!-- 4. PROVEN IMPACT & PERFORMANCE METRICS SECTION                -->
      <!-- ============================================================== -->
      @if ($collection->show_metrics_section ?? true)
      @php
        $metricsEyebrow = $collection->metrics_eyebrow ?: 'PROVEN IMPACT';
        $metricsTitle = $collection->metrics_title ?: 'Measurable Results Across Fashion Brands & Agencies';
        $metricsSubtitle = $collection->metrics_subtitle ?: "Real metrics reported by D2C apparel houses, independent fashion designers, and commercial creative agencies switching to Imaginebuddy's adaptive prompt workflows.";
        $metricCards = $collection->metrics_item_list;
      @endphp
      <section class="metrics-impact-section mb-5 pt-3 pb-2" id="provenImpact">
        <!-- Section Header (Centered) -->
        <div class="text-center mb-4 mb-md-5">
          <div class="d-inline-block mb-2">
            <span class="metrics-eyebrow-badge">
              {{ $metricsEyebrow }}
            </span>
          </div>
          <h2 class="fw-bold title-custom mb-3 mx-auto" style="max-width: 900px; font-size: clamp(1.65rem, 3vw, 2.35rem); letter-spacing: -0.02em;">
            {{ $metricsTitle }}
          </h2>
          @if (!empty($metricsSubtitle))
            <p class="text-muted mx-auto mb-0" style="max-width: 760px; font-size: clamp(0.92rem, 1.3vw, 1.05rem); line-height: 1.6;">
              {{ $metricsSubtitle }}
            </p>
          @endif
        </div>

        <!-- 4 Metric Cards Grid -->
        <div class="row g-3 g-md-4 justify-content-center">
          @foreach ($metricCards as $metric)
            <div class="col-12 col-sm-6 col-lg-3">
              <div class="metric-impact-card">
                <div class="metric-impact-stat">
                  {{ $metric['stat'] ?? '' }}
                </div>
                <div class="metric-impact-title title-custom">
                  {{ $metric['title'] ?? '' }}
                </div>
                @if (!empty($metric['description']))
                  <p class="metric-impact-desc text-muted">
                    {{ $metric['description'] }}
                  </p>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </section>
      @endif

    </div>{{-- End container --}}
  </div>{{-- End scalio-body-wrap --}}

  <!-- ============================================================== -->
  <!-- HOW IT WORKS: 3-STEP WORKFLOW (MATCHING HOMEPAGE)             -->
  <!-- ============================================================== -->
  @include('includes.home-how-it-works')

  <!-- Resume Main Content Container -->
  <div class="scalio-body-wrap py-4 py-md-5">
    <div class="container px-3 px-md-4">

      <!-- ============================================================== -->
      <!-- 5. HIGH-CONVERSION CTA BANNER                                 -->
      <!-- ============================================================== -->
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #07140E 0%, #0A1C13 100%); color: #fff;">
        <div class="position-relative py-3" style="z-index: 2;">
          <h2 class="fw-bold text-white mb-3" style="font-size: clamp(1.7rem, 3.2vw, 2.4rem);">
            Ready to Create Commercial-Grade Product Visuals?
          </h2>
          <p class="text-white-50 mx-auto mb-4" style="max-width: 620px; font-size: 1.05rem; line-height: 1.6;">
            Join thousands of e-commerce brands, photographers, and creators generating high-converting product catalogs with ImagineBuddy.
          </p>
          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ url('register') }}" class="btn btn-lg rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm" style="background: #00d690; border: none; font-size: 0.96rem;">
              Get Started Free <i class="bi bi-arrow-right ms-2"></i>
            </a>
            <a href="{{ url('prompts') }}" class="btn btn-lg rounded-pill px-4 py-2.5 fw-semibold btn-outline-light" style="font-size: 0.96rem; border-color: rgba(255,255,255,0.3);">
              Explore More Prompts
            </a>
          </div>
        </div>
        <div class="position-absolute" style="bottom: -120px; left: 50%; transform: translateX(-50%); width: 550px; height: 260px; background: radial-gradient(circle, rgba(0, 214, 144, 0.16) 0%, transparent 70%); pointer-events: none;"></div>
      </div>

    </div>{{-- End container --}}
  </div>{{-- End scalio-body-wrap --}}

  <!-- ============================================================== -->
  <!-- TESTIMONIALS SECTION (MATCHING HOMEPAGE)                       -->
  <!-- ============================================================== -->
  @if (isset($testimonials) && $testimonials->isNotEmpty())
  <section class="section py-5 py-large testimonials-section border-top border-bottom">
    <div class="container px-3 px-md-4">
      <div class="btn-block text-center mb-5">
        <h2 class="m-0 h3 fw-bold title-custom">{{ __('misc.creators_testimonials_heading') }}</h2>
        <p class="text-muted mt-2">
          {{ __('misc.creators_testimonials_subtitle') }}
        </p>
      </div>

      @include('includes.testimonials-slider', ['testimonials' => $testimonials])
    </div>
  </section>
  @endif

  <!-- Resume Main Content Container for FAQ & Related Collections -->
  <div class="scalio-body-wrap py-4 py-md-5">
    <div class="container px-3 px-md-4">

      <!-- ============================================================== -->
      <!-- 6. FREQUENTLY ASKED QUESTIONS (SCALIO FAQ ACCORDION)           -->
      <!-- ============================================================== -->
      @if (!empty($faqs))
      <div class="mb-5 pt-2">
        <div class="text-center mb-4 pb-1">
          <span class="badge badge-dark-custom rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
            FAQ
          </span>
          <h2 class="fw-bold title-custom mb-2 h3">
            Questions About {{ $collection->title }}
          </h2>
          <p class="text-muted small">Everything you need to know about commercial licensing, prompt parameters, and AI model compatibility.</p>
        </div>

        <div class="mx-auto" style="max-width: 860px;">
          <div class="accordion accordion-custom" id="collectionFaqAccordion">
            @foreach ($faqs as $fKey => $faqItem)
              <div class="accordion-item border border-custom rounded-3 mb-3 overflow-hidden bg-card-custom shadow-xs">
                <h2 class="accordion-header" id="headingFaq-{{ $fKey }}">
                  <button class="accordion-button fw-bold title-custom bg-transparent collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq-{{ $fKey }}" aria-expanded="false" aria-controls="collapseFaq-{{ $fKey }}" style="box-shadow: none;">
                    {{ $faqItem['question'] }}
                  </button>
                </h2>
                <div id="collapseFaq-{{ $fKey }}" class="accordion-collapse collapse" aria-labelledby="headingFaq-{{ $fKey }}" data-bs-parent="#collectionFaqAccordion">
                  <div class="accordion-body text-muted pt-0" style="line-height: 1.6; font-size: 0.95rem;">
                    {{ $faqItem['answer'] }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      <!-- ============================================================== -->
      <!-- 7. RELATED PROMPT COLLECTIONS GRID (SCALIO EXPLORE SETS)       -->
      <!-- ============================================================== -->
      @if ($relatedCollections->isNotEmpty())
      <div class="mb-5 pt-4 border-top border-custom">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
          <div>
            <span class="badge badge-dark-custom rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
              Explore More Sets
            </span>
            <h2 class="fw-bold title-custom mb-1 h3">
              Related Prompt Collections
            </h2>
            <p class="text-muted small mb-0">Browse tested prompts across complementary product categories.</p>
          </div>
          <a href="{{ url('prompts') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-2 fw-medium">
            View All Collections <i class="bi bi-arrow-right ms-2"></i>
          </a>
        </div>

        <div class="row g-4">
          @foreach ($relatedCollections as $related)
            @include('includes.prompt-collection-card', ['collection' => $related])
          @endforeach
        </div>
      </div>
      @endif

    </div>
  </div>

</div>
@endsection

@section('css')
<style type="text/css">
/* --- The Shortcut / Before-After Transformation Section (As shown in reference image) --- */
.shortcut-transformation-section {
  background-color: #ebf3ed !important;
  transition: background-color 0.3s ease;
}
.shortcut-heading {
  font-size: clamp(1.9rem, 3.4vw, 2.75rem);
  line-height: 1.18;
  color: #111827;
}
.shortcut-desc {
  color: #4b5563;
  font-size: 1.05rem;
  line-height: 1.65;
}
.shortcut-eyebrow-dot {
  width: 7px;
  height: 7px;
  background-color: #00b87b;
}
.shortcut-check-icon {
  background-color: #00b87b;
  color: #ffffff;
  width: 20px;
  height: 20px;
  font-size: 12px;
}
.shortcut-feature-text {
  color: #1f2937;
}
.shortcut-cta-btn {
  background: #00d690 !important;
  color: #05160e !important;
  border: none !important;
  font-size: 0.98rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.shortcut-cta-btn:hover {
  background: #00bd77 !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 214, 144, 0.35) !important;
}
.shortcut-subtext {
  font-size: 0.82rem;
  color: #6b7280;
}

/* Before / After Comparison Card */
.shortcut-compare-card {
  background-color: #ffffff;
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.08) !important;
  max-width: 500px;
  margin: 0 auto;
}
.shortcut-slider-container {
  aspect-ratio: 4 / 3;
  width: 100%;
  cursor: ew-resize;
  user-select: none;
  background-color: #f8fafc;
  touch-action: pan-y;
  max-height: 560px;
}
.shortcut-img {
  object-fit: cover;
  object-position: center;
  pointer-events: none;
}
.shortcut-slider-divider {
  width: 2px;
  background-color: #ffffff;
  z-index: 15;
  pointer-events: none;
  transform: translateX(-50%);
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}
.shortcut-slider-handle {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 44px;
  height: 44px;
  background: #ffffff;
  border: 2px solid #00d690;
  pointer-events: auto;
  cursor: ew-resize;
  transition: transform 0.15s ease;
}
.shortcut-slider-handle:hover,
.shortcut-slider-container:hover .shortcut-slider-handle {
  transform: translate(-50%, -50%) scale(1.08);
}
.shortcut-caption {
  font-size: 0.82rem;
  color: #6b7280;
}

/* Dark Theme Overrides */
[data-bs-theme="dark"] .shortcut-transformation-section {
  background-color: #121d16 !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .shortcut-heading {
  color: #f8fafc !important;
}
[data-bs-theme="dark"] .shortcut-desc {
  color: #cbd5e1 !important;
}
[data-bs-theme="dark"] .shortcut-feature-text {
  color: #e2e8f0 !important;
}
[data-bs-theme="dark"] .shortcut-subtext,
[data-bs-theme="dark"] .shortcut-caption {
  color: #94a3b8 !important;
}
[data-bs-theme="dark"] .shortcut-compare-card {
  background-color: #19261e !important;
  border: 1px solid rgba(255, 255, 255, 0.09) !important;
  box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4) !important;
}

/* --- Scalio Color Tokens & Styling --- */
.text-mint {
  color: #00d690 !important;
}
.btn-custom-mint {
  background-color: var(--color-default, #00d690) !important;
  border-color: var(--color-default, #00d690) !important;
  color: #ffffff !important;
  transition: all 0.2s ease-in-out;
}
.btn-custom-mint:hover {
  background-color: #00b87b !important;
  border-color: #00b87b !important;
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(0, 214, 144, 0.35) !important;
}

/* --- Badges --- */
.badge-mint-subtle {
  color: #00d690 !important;
  background-color: rgba(0, 214, 144, 0.1) !important;
  border: 1px solid rgba(0, 214, 144, 0.3) !important;
}
.badge-dark-custom {
  background-color: #1e293b !important;
  color: #ffffff !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* --- Proven Impact & Metrics Section --- */
.metrics-eyebrow-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.4rem 1.1rem;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #00B865;
  background-color: #E8F8F0;
  border: 1px solid rgba(0, 184, 101, 0.2);
  border-radius: 9999px;
}

.metric-impact-card {
  background-color: #ffffff;
  border: 1px solid #e9ecef;
  border-radius: 1.25rem;
  padding: 2.5rem 1.5rem;
  text-align: center;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.metric-impact-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
  border-color: rgba(0, 184, 101, 0.35);
}

.metric-impact-stat {
  color: #00B865;
  font-weight: 800;
  line-height: 1;
  font-size: clamp(2.35rem, 3.4vw, 3rem);
  letter-spacing: -0.02em;
  margin-bottom: 0.85rem;
}

.metric-impact-title {
  font-weight: 700;
  font-size: 1.05rem;
  line-height: 1.35;
  margin-bottom: 0.65rem;
  color: #0f172a;
}

.metric-impact-desc {
  font-size: 0.875rem;
  line-height: 1.55;
  margin-bottom: 0;
  color: #64748b;
}

/* --- Lookbook Mosaic Hover Effects --- */
.scalio-hero {
  min-height: 100vh;
}
@media (max-width: 991px) {
  .scalio-hero {
    min-height: auto !important;
    padding-top: 110px !important;
    padding-bottom: 55px !important;
  }
}
.scalio-hero-showcase .wrap-collection {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.scalio-hero-showcase .wrap-collection:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4) !important;
}
.scalio-hero-showcase .img-collection {
  transition: transform 0.4s ease;
}
.scalio-hero-showcase .wrap-collection:hover .img-collection {
  transform: scale(1.03);
}

/* ==========================================================================
   Dark Theme Overrides & High-Contrast Optimization
   ========================================================================== */
[data-bs-theme="dark"] .bg-card-custom {
  background-color: #1a1e23 !important;
  border-color: rgba(255, 255, 255, 0.09) !important;
}

[data-bs-theme="dark"] .border-custom {
  border-color: rgba(255, 255, 255, 0.09) !important;
}

[data-bs-theme="dark"] .title-custom {
  color: #f8fafc !important;
}

[data-bs-theme="dark"] .text-muted {
  color: #94a3b8 !important;
}

[data-bs-theme="dark"] .badge-dark-custom {
  background-color: rgba(255, 255, 255, 0.08) !important;
  color: #f1f5f9 !important;
  border-color: rgba(255, 255, 255, 0.14) !important;
}

[data-bs-theme="dark"] .bg-subtle-custom {
  background-color: rgba(255, 255, 255, 0.05) !important;
  color: #cbd5e1 !important;
  border-color: rgba(255, 255, 255, 0.12) !important;
}

[data-bs-theme="dark"] .badge-mint-subtle {
  background-color: rgba(0, 214, 144, 0.15) !important;
  border-color: rgba(0, 214, 144, 0.4) !important;
}

[data-bs-theme="dark"] .metrics-eyebrow-badge {
  color: #34d399 !important;
  background-color: rgba(16, 185, 129, 0.14) !important;
  border-color: rgba(52, 211, 153, 0.3) !important;
}

[data-bs-theme="dark"] .metric-impact-card {
  background-color: #1a1e23 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
}

[data-bs-theme="dark"] .metric-impact-card:hover {
  border-color: rgba(0, 214, 144, 0.35) !important;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.4) !important;
}

[data-bs-theme="dark"] .metric-impact-stat {
  color: #00d690 !important;
}

[data-bs-theme="dark"] .metric-impact-title {
  color: #f8fafc !important;
}

[data-bs-theme="dark"] .metric-impact-desc {
  color: #94a3b8 !important;
}

[data-bs-theme="dark"] .btn-outline-custom {
  border-color: rgba(255, 255, 255, 0.15) !important;
  color: #cbd5e1 !important;
  background-color: transparent !important;
}

[data-bs-theme="dark"] .btn-outline-custom:hover {
  border-color: #00d690 !important;
  color: #00d690 !important;
  background-color: rgba(0, 214, 144, 0.1) !important;
}

/* --- Testimonials Section (Matching Homepage) --- */
.testimonials-section {
  background-color: #fafbfc;
  border-color: #e9ecef !important;
}
[data-bs-theme="dark"] .testimonials-section {
  background-color: #0b0e14 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .testimonials-section h2 {
  color: #ffffff !important;
}
[data-bs-theme="dark"] .testimonials-section p.text-muted {
  color: #94a3b8 !important;
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  var state = {
    page: {{ $images->hasMorePages() ? ($images->currentPage() + 1) : 'null' }},
    hasMore: {{ $images->hasMorePages() ? 'true' : 'false' }},
    loading: false
  };

  // Hide server-rendered pagination when interactive infinite scroll is active
  if (state.hasMore) {
    $('#linkPagination').addClass('d-none');
  }

  function checkScrollLoad() {
    if (state.loading || !state.hasMore || !state.page) return;

    var $target = $('#promptsSentinel');
    if (!$target.length) {
      $target = $('#imagesFlex');
    }
    if (!$target.length) return;

    var targetTop = $target.offset().top;
    var currentScroll = $(window).scrollTop() + $(window).height();

    // Trigger loading early (500px before reaching the end of the prompts grid)
    if (currentScroll >= targetTop - 500) {
      loadNextPage();
    }
  }

  function loadNextPage() {
    if (state.loading || !state.hasMore || !state.page) return;

    state.loading = true;
    $('#infiniteScrollLoader').removeClass('d-none');

    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('page', state.page);

    $.ajax({
      url: currentUrl.toString(),
      type: 'GET',
      dataType: 'json',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response && response.html) {
          var $wrapper = $('<div>').html(response.html);
          var $newItems = $wrapper.find('.item');

          if ($newItems.length > 0) {
            $('#imagesFlex').append($newItems);

            state.hasMore = !!response.hasMore;
            state.page = response.nextPage;

            var currentCount = $('#imagesFlex').find('.item').length;
            $('#showingCount').text(currentCount);

            setTimeout(checkScrollLoad, 150);
          } else {
            state.hasMore = false;
          }
        } else if (typeof response === 'string' && response.trim() !== '') {
          var $wrapper = $('<div>').html(response);
          var $newItems = $wrapper.find('.item');

          if ($newItems.length > 0) {
            $('#imagesFlex').append($newItems);
            state.page++;

            var hasNext = $wrapper.find('#linkPagination .pagination .next, #linkPagination .pagination [rel="next"]').length > 0;
            state.hasMore = hasNext;

            var currentCount = $('#imagesFlex').find('.item').length;
            $('#showingCount').text(currentCount);

            setTimeout(checkScrollLoad, 150);
          } else {
            state.hasMore = false;
          }
        } else {
          state.hasMore = false;
        }

        state.loading = false;
        $('#infiniteScrollLoader').addClass('d-none');
      },
      error: function() {
        state.loading = false;
        $('#infiniteScrollLoader').addClass('d-none');
        $('#linkPagination').removeClass('d-none');
      }
    });
  }

  // IntersectionObserver for early prompt grid infinite scroll
  if ('IntersectionObserver' in window) {
    var sentinelEl = document.getElementById('promptsSentinel');
    if (sentinelEl) {
      var observer = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting) {
          loadNextPage();
        }
      }, {
        rootMargin: '600px 0px 600px 0px'
      });
      observer.observe(sentinelEl);
    }
  }

  $(window).on('scroll resize', checkScrollLoad);

  // Interactive Before/After Split Comparison Slider (Hover & Touch)
  function initShortcutSlider() {
    var container = document.getElementById('shortcutSlider');
    var clip = document.getElementById('shortcutBeforeClip');
    var divider = document.getElementById('shortcutDivider');
    var handle = document.getElementById('shortcutHandle');

    if (!container || !clip || !divider) return;

    // Auto-adjust aspect ratio if image loads with specific natural dimensions
    var baseImg = container.querySelector('.shortcut-after-img');
    if (baseImg) {
      var adjustRatio = function() {
        if (baseImg.naturalWidth && baseImg.naturalHeight) {
          container.style.aspectRatio = baseImg.naturalWidth + ' / ' + baseImg.naturalHeight;
        }
      };
      if (baseImg.complete) {
        adjustRatio();
      } else {
        baseImg.addEventListener('load', adjustRatio);
      }
    }

    var isDragging = false;

    function updateSlider(percent) {
      var clamped = Math.max(0, Math.min(100, percent));
      clip.style.clipPath = 'inset(0 ' + (100 - clamped) + '% 0 0)';
      divider.style.left = clamped + '%';
    }

    function getPercent(e) {
      var rect = container.getBoundingClientRect();
      var clientX = (e.touches && e.touches.length > 0) ? e.touches[0].clientX : e.clientX;
      var x = clientX - rect.left;
      return (x / rect.width) * 100;
    }

    // Hover / mousemove interaction (as in Scalio: "Hover to compare")
    container.addEventListener('mousemove', function(e) {
      updateSlider(getPercent(e));
    });

    // Touch & Drag Support
    function onPointerDown(e) {
      isDragging = true;
      updateSlider(getPercent(e));
    }
    function onPointerMove(e) {
      if (!isDragging) return;
      updateSlider(getPercent(e));
    }
    function onPointerUp() {
      isDragging = false;
    }

    container.addEventListener('pointerdown', onPointerDown);
    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);

    // Initial position: 50%
    updateSlider(50);
  }

  $(document).ready(function() {
    checkScrollLoad();
    initShortcutSlider();
  });
})(jQuery);
</script>
@endsection
