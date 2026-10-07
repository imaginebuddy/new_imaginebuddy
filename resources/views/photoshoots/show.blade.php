@extends('layouts.app')

@section('title', !empty($photoshoot->meta_title) ? $photoshoot->meta_title : ($photoshoot->title . ' - AI Photoshoot Set'))

@if (!empty($photoshoot->meta_description))
  @section('description_override', Helper::removeLineBreak(e($photoshoot->meta_description)))
@elseif ($photoshoot->description)
  @section('description_custom', Helper::removeLineBreak($photoshoot->description))
@endif

@if (!empty($photoshoot->meta_keywords))
  @section('keywords_override', $photoshoot->meta_keywords)
@endif

@section('content')
@php
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

  $hSrc0 = $getHeroImgUrl($hImg0);
  $hSrc1 = $getHeroImgUrl($hImg1);
  $hSrc2 = $getHeroImgUrl($hImg2);
@endphp

<section class="section section-sm py-3 py-md-4 photoshoot-detail-page">
  <div class="container px-3 px-md-4">
    
    <!-- Top Breadcrumb Navigation (Mobile Optimized) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 mb-md-4 gap-2">
      <nav aria-label="breadcrumb" class="w-100 w-md-auto overflow-hidden">
        <div class="breadcrumb-pill-box rounded-pill shadow-xs border border-custom px-3 py-2 d-inline-flex align-items-center bg-card-custom max-w-100">
          <ol class="breadcrumb mb-0 align-items-center list-unstyled flex-nowrap overflow-auto text-nowrap" style="scrollbar-width: none;">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('photoshoots') }}" class="text-decoration-none text-muted">Photoshoots</a></li>
            @if ($photoshoot->category)
              <li class="breadcrumb-item"><a href="{{ url('photoshoots') }}?category={{ $photoshoot->category->slug }}" class="text-decoration-none text-muted">{{ $photoshoot->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
              <span class="badge bg-custom-mint text-white rounded-pill px-2 px-md-3 py-1 py-md-2 fw-semibold text-truncate d-inline-block align-middle" title="{{ $photoshoot->title }}" style="max-width: 170px; font-size: 0.82rem; letter-spacing: -0.2px;">{{ $photoshoot->title }}</span>
            </li>
          </ol>
        </div>
      </nav>
    </div>

    <!-- Photoshoot Showcase Split Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-sm-4 p-md-5 mb-4 mb-md-5 bg-card-custom border border-custom position-relative overflow-hidden photoshoot-hero-card">
      <div class="row align-items-center g-4 g-lg-5">
        
        <!-- Left Column: Editorial Details & Actions -->
        <div class="col-lg-7">
          <!-- Metadata Badges Strip -->
          <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            @if ($photoshoot->category)
              <a href="{{ url('photoshoots') }}?category={{ $photoshoot->category->slug }}" class="badge bg-subtle-custom text-secondary border border-custom text-decoration-none rounded-pill px-3 py-2 fw-medium" style="font-size: 0.8rem;">
                <i class="bi bi-tag-fill text-mint me-1"></i> {{ $photoshoot->category->name }}
              </a>
            @endif

            @if ($photoshoot->ai_model)
              <span class="badge badge-dark-custom rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-xs" style="font-size: 0.8rem; font-weight: 500;">
                <i class="bi bi-cpu text-mint me-1"></i> {{ $photoshoot->ai_model }}
              </span>
            @endif

            <span class="badge badge-mint-subtle rounded-pill px-3 py-2 d-inline-flex align-items-center" style="font-size: 0.8rem; font-weight: 500;">
              <i class="bi bi-images me-1"></i> {{ $images->total() }} {{ str_plural('Angle', $images->total()) }} Set
            </span>

            <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-3 py-2 d-inline-flex align-items-center" style="font-size: 0.8rem; font-weight: 500;">
              <i class="bi bi-patch-check-fill text-mint me-1"></i> Commercial License Ready
            </span>
          </div>

          <!-- Main H1 Heading -->
          <h1 class="fw-bold title-custom photoshoot-hero-title mb-3 tracking-tight">
            {{ $photoshoot->title }}
          </h1>

          <!-- Editorial Lead Description -->
          @if ($photoshoot->description)
            <p class="lead text-muted photoshoot-lead-text mb-4">
              {{ $photoshoot->description }}
            </p>
          @else
            <p class="lead text-muted photoshoot-lead-text mb-4">
              A curated collection of coherent commercial photography prompts engineered for cohesive multi-angle product photography, advertising lookbooks, and high-converting e-commerce listings.
            </p>
          @endif

          <!-- Feature Highlight Badges -->
          <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mb-4 py-2">
            <span class="d-inline-flex align-items-center">
              <i class="bi bi-aspect-ratio text-mint me-2 fs-6"></i> Multi-Angle Consistency
            </span>
            <span class="d-inline-flex align-items-center">
              <i class="bi bi-brightness-high text-mint me-2 fs-6"></i> Studio Lighting Recipes
            </span>
            <span class="d-inline-flex align-items-center">
              <i class="bi bi-lightning-charge text-mint me-2 fs-6"></i> Prompt-Ready Formulas
            </span>
          </div>

        </div>

        <!-- Right Column: Visual Lookbook Preview Mosaic -->
        <div class="col-lg-5">
          @if ($images->count() > 0)
            <div class="lookbook-hero-preview position-relative">
              <div class="wrap-collection mb-0 rounded-4 overflow-hidden border border-custom shadow-sm" style="margin-bottom: 0 !important; padding-bottom: 82%;">
                <div class="grid-collection" style="border-radius: 16px; top: 0; left: 0;">
                  <div class="collection-1">
                    @if ($hSrc0)
                      <a href="{{ $hImg0 ? url('prompt', $hImg0->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                        <img class="img-collection" src="{{ $hSrc0 }}" alt="{{ $photoshoot->title }} - Primary Shot" loading="eager">
                        <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 position-absolute top-0 start-0 m-2 text-uppercase fw-semibold shadow-xs" style="font-size: 0.65rem; backdrop-filter: blur(4px);">
                          Hero Packshot
                        </span>
                      </a>
                    @endif
                  </div>

                  <div class="collection-right">
                    <div class="collection-2">
                      @if ($hSrc1)
                        <a href="{{ $hImg1 ? url('prompt', $hImg1->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                          <img class="img-collection" src="{{ $hSrc1 }}" alt="{{ $photoshoot->title }} - Angle 2" loading="eager">
                          <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 position-absolute top-0 start-0 m-2 text-uppercase fw-semibold shadow-xs" style="font-size: 0.65rem; backdrop-filter: blur(4px);">
                            Angle 2
                          </span>
                        </a>
                      @endif
                    </div>

                    <div class="collection-2">
                      @if ($hSrc2)
                        <a href="{{ $hImg2 ? url('prompt', $hImg2->slug) : '#prompts-grid' }}" class="d-block w-100 h-100 position-relative">
                          <img class="img-collection" src="{{ $hSrc2 }}" alt="{{ $photoshoot->title }} - Angle 3" loading="eager">
                          <span class="badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1 position-absolute top-0 start-0 m-2 text-uppercase fw-semibold shadow-xs" style="font-size: 0.65rem; backdrop-filter: blur(4px);">
                            Angle 3
                          </span>
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              <!-- Micro Lookbook Strip -->
              <div class="d-flex align-items-center justify-content-between pt-2 px-1 text-muted small">
                <span class="d-inline-flex align-items-center">
                  <i class="bi bi-camera-fill text-mint me-1"></i> Multi-Angle Studio Lookbook
                </span>
                <a href="#prompts-grid" class="text-mint text-decoration-none fw-semibold">
                  View all {{ $images->total() }} &darr;
                </a>
              </div>
            </div>
          @endif
        </div>

        <!-- Bottom Action Bar (Full Width with Right-Hand Side Buttons As Like Before) -->
        <div class="col-12 border-top border-custom pt-3 mt-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 text-muted small">
              <span><i class="bi bi-clock me-1"></i> {{ Helper::formatDate($photoshoot->created_at) }}</span>
              @if ($photoshoot->user)
                <span><i class="bi bi-person me-1"></i> By {{ $photoshoot->user->username }}</span>
              @endif
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
              <button type="button" id="btnSharePhotoshoot" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-2 fw-medium" onclick="navigator.clipboard.writeText(window.location.href); $(this).html('<i class=\'bi bi-check2 text-mint me-1\'></i> Copied!'); setTimeout(() => $(this).html('<i class=\'bi bi-share me-1\'></i> Share'), 2500);">
                <i class="bi bi-share me-1"></i> Share
              </button>

              <a href="{{ url('photoshoots') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-2 fw-medium">
                <i class="bi bi-grid me-1"></i> All Photoshoots
              </a>

              <a href="#prompts-grid" class="btn btn-sm btn-custom-mint text-white rounded-pill px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-images me-1"></i> Browse Prompts ({{ $images->total() }})
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Prompts Grid Section -->
    <div id="prompts-grid" class="mb-4 mb-md-5 pt-2 pt-md-3">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-end mb-4 pb-3 border-bottom border-custom gap-3">
        <div>
          <span class="badge badge-dark-custom rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">
            Angle Catalog
          </span>
          <h2 class="fw-bold title-custom m-0 h3">Prompts in this Photoshoot</h2>
          <p class="text-muted small mb-0 mt-1">
            Copy and adapt these studio prompt formulas across multiple angles for your own product catalog.
          </p>
        </div>
        <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-3 py-2 fw-medium align-self-start align-self-sm-auto" style="font-size: 0.85rem;">
          <i class="bi bi-grid-fill text-mint me-1"></i> Showing <span id="showingCount">{{ $images->count() }}</span> of {{ $images->total() }} {{ str_plural('Prompt', $images->total()) }}
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
          <p class="text-muted m-0">No active prompts found in this photoshoot.</p>
        </div>
      @endif
    </div>

    <!-- SEO Topical Authority: Minimal Creative Studio Direction -->
    @php
      $cdData = $photoshoot->creative_direction_data;
      $faqsList = $photoshoot->faq_items;
    @endphp
    <div class="mt-5 pt-4 pt-md-5 border-top border-custom" role="region" aria-labelledby="direction-heading">
      
      <!-- Top Grid: Editorial Narrative & Studio Blueprint Card -->
      <div class="row g-4 align-items-stretch mb-4 mb-md-5">
        
        <!-- Left: Editorial Context & SEO Depth -->
        <div class="col-lg-7 d-flex flex-column justify-content-center">
          <div class="pe-lg-4">
            <span class="badge badge-dark-custom rounded-pill px-3 py-1 fw-bold text-uppercase mb-2 d-inline-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">
              {{ $cdData['badge'] }}
            </span>
            <h2 id="direction-heading" class="fw-bold title-custom mb-3" style="font-size: clamp(1.4rem, 2.5vw, 1.85rem); line-height: 1.3;">
              {{ $cdData['heading'] }}
            </h2>
            <p class="text-muted mb-3" style="line-height: 1.75; font-size: 0.96rem;">
              {!! nl2br(e($cdData['description'])) !!}
            </p>
            @if (!empty($cdData['subtext']))
              <p class="text-muted mb-0 small" style="line-height: 1.7;">
                {!! nl2br(e($cdData['subtext'])) !!}
              </p>
            @endif
          </div>
        </div>

        <!-- Right: Compact Technical Blueprint Card -->
        <div class="col-lg-5">
          <div class="card bg-card-custom rounded-4 border border-custom p-4 h-100 shadow-xs d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-custom">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-mint fs-5"></i>
                <span class="fw-bold title-custom small text-uppercase" style="letter-spacing: 0.5px;">{{ $cdData['blueprint_title'] }}</span>
              </div>
              <span class="badge badge-mint-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                <i class="bi bi-patch-check-fill me-1"></i> Verified Spec
              </span>
            </div>

            <div class="row g-2 flex-grow-1 align-content-center my-1">
              <div class="col-6">
                <div class="p-2.5 p-md-3 rounded-3 bg-subtle-custom border border-custom h-100">
                  <span class="d-block text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 600; letter-spacing: 0.5px;">Lighting Key</span>
                  <strong class="title-custom small d-block mt-1">{{ $cdData['lighting'] }}</strong>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2.5 p-md-3 rounded-3 bg-subtle-custom border border-custom h-100">
                  <span class="d-block text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 600; letter-spacing: 0.5px;">Optical Lenses</span>
                  <strong class="title-custom small d-block mt-1">{{ $cdData['lenses'] }}</strong>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2.5 p-md-3 rounded-3 bg-subtle-custom border border-custom h-100">
                  <span class="d-block text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 600; letter-spacing: 0.5px;">Target AI Models</span>
                  <strong class="title-custom small d-block mt-1">{{ $cdData['target_ai'] }}</strong>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2.5 p-md-3 rounded-3 bg-subtle-custom border border-custom h-100">
                  <span class="d-block text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 600; letter-spacing: 0.5px;">Commercial Rights</span>
                  <strong class="text-mint small d-block mt-1"><i class="bi bi-shield-check me-1"></i> {{ $cdData['commercial'] }}</strong>
                </div>
              </div>
            </div>

            <div class="pt-3 mt-2 border-top border-custom d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.78rem;">
              <span><i class="bi bi-check2-circle text-mint me-1"></i> Studio-calibrated prompt syntax</span>
              <span class="fw-semibold title-custom">{{ $images->total() }} {{ str_plural('Angle', $images->total()) }}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Bottom Grid: 3 Distinct Feature Cards (Clean, Balanced & Breathable) -->
      <div class="row g-3 g-md-4 mb-5">
        
        <!-- Pillar 1: Lighting & Optical Science -->
        <div class="col-md-4">
          <div class="card bg-card-custom rounded-4 border border-custom p-4 h-100 shadow-xs hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="badge badge-mint-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">01</span>
              <i class="bi bi-brightness-high text-mint fs-5"></i>
            </div>
            <h3 class="h6 fw-bold title-custom mb-2">{{ $cdData['pillar_1_title'] }}</h3>
            <p class="text-muted small mb-0" style="line-height: 1.65;">
              {!! nl2br(e($cdData['pillar_1_desc'])) !!}
            </p>
          </div>
        </div>

        <!-- Pillar 2: Multi-Angle Coverage -->
        <div class="col-md-4">
          <div class="card bg-card-custom rounded-4 border border-custom p-4 h-100 shadow-xs hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="badge badge-mint-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">02</span>
              <i class="bi bi-camera-reels text-mint fs-5"></i>
            </div>
            <h3 class="h6 fw-bold title-custom mb-2">{{ $cdData['pillar_2_title'] }}</h3>
            <p class="text-muted small mb-0" style="line-height: 1.65;">
              {!! nl2br(e($cdData['pillar_2_desc'])) !!}
            </p>
          </div>
        </div>

        <!-- Pillar 3: Cross-Model Model Fidelity -->
        <div class="col-md-4">
          <div class="card bg-card-custom rounded-4 border border-custom p-4 h-100 shadow-xs hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="badge badge-mint-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">03</span>
              <i class="bi bi-cpu text-mint fs-5"></i>
            </div>
            <h3 class="h6 fw-bold title-custom mb-2">{{ $cdData['pillar_3_title'] }}</h3>
            <p class="text-muted small mb-0" style="line-height: 1.65;">
              {!! nl2br(e($cdData['pillar_3_desc'])) !!}
            </p>
          </div>
        </div>

      </div>
    </div>

    <!-- Visual Transformation Proof: Before / After Showcase -->
    <div class="photoshoot-transformation-wrapper mt-5 pt-4 pt-md-5 border-top border-custom">
      @include('includes.transformation-section')
    </div>

    <!-- FAQ Accordion (Styled identically to /frequently-asked-questions) -->
    <div class="my-5 pt-3 pt-md-4">
        <div class="text-center mb-4">
          <span class="badge badge-dark-custom rounded-pill px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            {{ __('admin.faq') }}
          </span>
          <h2 class="display-6 fw-bold title-custom mb-3" style="line-height: 1.25;">
            Frequently Asked Questions
          </h2>
          <p class="text-muted lead mx-auto mb-4" style="font-size: 1.05rem; max-width: 680px;">
            Quick answers about this photoshoot session, prompt customization, and multi-platform workflows.
          </p>
        </div>

        <div class="faq-accordion-wrapper" id="detailFaqAccordion" style="max-width: 860px; margin: 0 auto;">

          @foreach ($faqsList as $faqIndex => $faqItem)
            <div class="faq-card bg-card-custom rounded-4 p-3 p-md-4 border border-custom mb-3 shadow-xs faq-item-wrapper">
              <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0 {{ $faqIndex === 0 ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#detail-faq-collapse-{{ $faqIndex }}"
                      aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}"
                      aria-controls="detail-faq-collapse-{{ $faqIndex }}">
                <div class="me-3">
                  <h3 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                    {{ $faqItem['question'] }}
                  </h3>
                </div>
                <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
              </button>
              <div id="detail-faq-collapse-{{ $faqIndex }}"
                   class="collapse {{ $faqIndex === 0 ? 'show' : '' }} faq-collapse"
                   data-bs-parent="#detailFaqAccordion">
                <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                  {!! nl2br(e($faqItem['answer'])) !!}
                </div>
              </div>
            </div>
          @endforeach

        </div><!-- /.faq-accordion-wrapper -->
      </div>

    <!-- Related Photoshoots Section -->
    @if (isset($relatedPhotoshoots) && $relatedPhotoshoots->count() > 0)
      <div class="mt-5 pt-4 border-top border-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
          <div>
            <span class="text-mint fw-semibold small text-uppercase tracking-wider">Explore More Sets</span>
            <h2 class="fw-bold title-custom mt-1 mb-0 h3">Related AI Photoshoots</h2>
          </div>
          <a href="{{ url('photoshoots') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1">
            View All Photoshoots <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="row g-4">
          @foreach ($relatedPhotoshoots as $relPhotoshoot)
            @include('includes.photoshoot-card', ['photoshoot' => $relPhotoshoot])
          @endforeach
        </div>
      </div>
    @endif

  </div>
</section>

@php
  // Structured FAQ data for Google
  $faqStructured = [];
  foreach ($faqsList as $faqItem) {
    if (!empty($faqItem['question']) && !empty($faqItem['answer'])) {
      $faqStructured[] = [
        '@type' => 'Question',
        'name' => $faqItem['question'],
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => strip_tags($faqItem['answer'])
        ]
      ];
    }
  }
@endphp

<!-- Rich Schema.org FAQPage Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": {!! json_encode($faqStructured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
}
</script>

<!-- Rich Schema.org ImageGallery Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ImageGallery",
  "name": "{{ addslashes($photoshoot->title) }}",
  "description": "{{ addslashes($photoshoot->meta_description ?: ($photoshoot->description ?: $photoshoot->title)) }}",
  "url": "{{ url('photoshoots/' . $photoshoot->slug) }}",
  "numberOfItems": {{ $images->total() }}
}
</script>

<style>
/* --- Core Brand Accents --- */
.text-mint {
  color: #00d690 !important;
}
.border-mint {
  border: 1px solid #00d690 !important;
}

/* --- Solid Primary Action Button (Always Visible) --- */
.btn-custom-mint {
  background-color: #00d690 !important;
  border: 1px solid #00d690 !important;
  color: #ffffff !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  text-decoration: none !important;
  transition: all 0.2s ease-in-out !important;
}
.btn-custom-mint i {
  color: #ffffff !important;
}
.btn-custom-mint:hover,
.btn-custom-mint:focus,
.btn-custom-mint:active {
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

/* --- Responsive Hero Typography --- */
.photoshoot-detail-page {
  padding-top: 85px !important;
}
@media (min-width: 768px) {
  .photoshoot-detail-page {
    padding-top: 95px !important;
  }
}
.photoshoot-hero-title {
  font-size: clamp(1.65rem, 4.5vw, 2.35rem);
  line-height: 1.25;
}
.photoshoot-lead-text {
  font-size: 1rem;
  line-height: 1.65;
}
@media (min-width: 768px) {
  .photoshoot-lead-text {
    font-size: 1.05rem;
  }
}

/* --- Hover & Transition Utilities --- */
.hover-lift {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}
.lookbook-hero-preview .wrap-collection {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.lookbook-hero-preview .wrap-collection:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12) !important;
}
.lookbook-hero-preview .img-collection {
  transition: transform 0.4s ease;
}
.lookbook-hero-preview .wrap-collection:hover .img-collection {
  transform: scale(1.03);
}

.faq-toggle:focus,
.faq-toggle:active {
  box-shadow: none !important;
  outline: none !important;
}

/* ==========================================================================
   Dark Theme Overrides & High-Contrast Optimization
   ========================================================================== */
[data-bs-theme="dark"] .photoshoot-hero-card,
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

[data-bs-theme="dark"] .photoshoot-lead-text {
  color: #cbd5e1 !important;
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

[data-bs-theme="dark"] .breadcrumb-pill-box {
  background-color: #1a1e23 !important;
  border-color: rgba(255, 255, 255, 0.09) !important;
}

[data-bs-theme="dark"] .faq-card {
  background-color: #1a1e23 !important;
  border-color: rgba(255, 255, 255, 0.09) !important;
}

[data-bs-theme="dark"] .faq-card:hover {
  background-color: #21262d !important;
  border-color: rgba(0, 214, 144, 0.3) !important;
}

[data-bs-theme="dark"] .faq-answer-content {
  color: #cbd5e1 !important;
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

/* --- Mobile Specific Tweaks --- */
@media (max-width: 575.98px) {
  .breadcrumb-pill-box {
    width: 100%;
  }
  .breadcrumb-pill-box ol {
    width: 100%;
  }
}

/* --- Photoshoot Detail Transformation Section Tuning --- */
.photoshoot-transformation-wrapper .transformation-section {
  padding-top: 1rem !important;
  padding-bottom: 2rem !important;
}
.photoshoot-transformation-wrapper .transformation-section .container {
  padding-left: 0 !important;
  padding-right: 0 !important;
  max-width: 100% !important;
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  if ($('#imagesFlex').length && $.fn.flexImages) {
    $('#imagesFlex').flexImages({ rowHeight: 580 });
  }

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
            if ($('#imagesFlex').length && $.fn.flexImages) {
              $('#imagesFlex').flexImages({ rowHeight: 580 });
            }

            state.hasMore = !!response.hasMore;
            state.page = response.nextPage;

            var currentCount = $('#imagesFlex').find('.item').length;
            $('#showingCount').text(currentCount);

            // Check again after DOM update in case new end is already in view
            setTimeout(checkScrollLoad, 150);
          } else {
            state.hasMore = false;
          }
        } else if (typeof response === 'string' && response.trim() !== '') {
          var $wrapper = $('<div>').html(response);
          var $newItems = $wrapper.find('.item');

          if ($newItems.length > 0) {
            $('#imagesFlex').append($newItems);
            if ($('#imagesFlex').length && $.fn.flexImages) {
              $('#imagesFlex').flexImages({ rowHeight: 580 });
            }
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
        // Show server-rendered pagination if AJAX fails
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

  $(document).ready(function() {
    checkScrollLoad();
  });
})(jQuery);
</script>
@endsection
