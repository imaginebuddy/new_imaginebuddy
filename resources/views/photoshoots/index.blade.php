@extends('layouts.app')

@section('title'){{ $currentCategory ? $currentCategory->name . ' AI Photoshoot Prompts & Sets - ' : 'AI Product Photoshoot Prompts & Multi-Angle Sets - ' }}@endsection

@section('content')
<section class="section section-sm py-4" style="padding-top: 95px !important;">
  <div class="container">
    
    <!-- Hero Header -->
    <div class="text-center pt-3 pt-md-4 pb-3 mb-4">
      <span class="badge rounded-pill px-4 py-2 mb-3 d-inline-flex align-items-center" style="color: #00d690; border: 1px solid #00d690; background-color: transparent; font-size: 0.85rem; font-weight: 500;">
        <i class="bi bi-collection-play me-2"></i> Photoshoot Sets
      </span>
      <h1 class="fw-bold text-dark title-custom display-5 mb-3">
        @if ($currentCategory)
          {{ $currentCategory->name }} AI Photoshoot Prompts
        @else
          AI Product Photoshoot Prompts & Commercial Sets
        @endif
      </h1>
      <p class="lead text-muted mx-auto" style="max-width: 720px;">
        Explore curated multi-prompt commercial photoshoot batches. Generate consistent virtual models, studio lighting setups, and multiple camera angles across ChatGPT, Google Gemini, and Midjourney.
      </p>
    </div>

    <!-- Category Filter Scrollable Bar -->
    <div class="photoshoot-filter-wrapper mb-5 position-relative">
      <div class="photoshoot-category-scroll d-flex align-items-center gap-2 overflow-x-auto py-2 px-1">
        <a href="{{ url('photoshoots') }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 {{ empty($categorySlug) ? 'btn-custom fw-semibold active text-white' : 'btn-outline-custom' }}">
          All Categories
        </a>
        @foreach ($categories as $cat)
          <a href="{{ url('photoshoots') }}?category={{ $cat->slug }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 {{ $categorySlug == $cat->slug ? 'btn-custom fw-semibold active text-white' : 'btn-outline-custom' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <!-- Photoshoots Grid Container -->
    @if ($photoshoots->total() != 0)
      <div class="row g-4" id="photoshootsContainer">
        @foreach ($photoshoots as $photoshoot)
          @include('includes.photoshoot-card', ['photoshoot' => $photoshoot])
        @endforeach
      </div>

      <!-- Crawlable Server-Side Pagination for Googlebot & Progressive Fallback -->
      @if ($photoshoots->hasPages())
        <div class="photoshoot-pagination-wrapper my-5 d-flex justify-content-center" id="photoshootsPagination">
          {{ $photoshoots->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
        </div>
      @endif

      <!-- Infinite Scroll Loader -->
      <div id="photoshootsLoader" class="text-center py-5 d-none">
        <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
          <span class="visually-hidden">Loading more...</span>
        </div>
        <p class="text-muted small mt-2">Loading more photoshoots...</p>
      </div>

    @else
      <div class="text-center py-5 my-5 bg-card-custom rounded-4 border border-custom p-5">
        <div class="mb-3">
          <i class="bi bi-collection-play text-muted opacity-50" style="font-size: 4rem;"></i>
        </div>
        <h4 class="fw-bold text-dark title-custom mb-2">No photoshoots found</h4>
        <p class="text-muted mb-4">
          @if ($currentCategory)
            No photoshoot sets available in <strong>{{ $currentCategory->name }}</strong> yet.
          @else
            No photoshoot sets available at the moment.
          @endif
        </p>
        @if ($categorySlug)
          <a href="{{ url('photoshoots') }}" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> View All Photoshoots
          </a>
        @endif
      </div>
    @endif

    <!-- SEO Topical Authority: Guide & FAQ Section -->
    <div class="mt-5 pt-5 border-top border-custom">
      <div class="row g-4 align-items-center mb-5">
        <div class="col-lg-7">
          <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            Multi-Angle Prompt Consistency
          </span>
          <h2 class="display-6 fw-bold title-custom mb-3" style="line-height: 1.2;">
            Consistent Commercial AI Photoshoots for E-Commerce &amp; Creators
          </h2>
          <p class="text-muted mb-3" style="line-height: 1.75; font-size: 0.98rem;">
            Creating professional product catalogs and commercial campaigns with generative AI requires subject consistency across diverse camera angles, focal lengths, and studio lighting setups. ImagineBuddy's curated photoshoot sets provide tested, multi-prompt recipes that preserve subject identity, materials, and color accuracy across every frame.
          </p>
          <p class="text-muted mb-0" style="line-height: 1.75; font-size: 0.98rem;">
            Create professional <strong>AI product photography</strong> for <a href="{{ url('category/beauty-and-skincare') }}">beauty and skincare products</a>, <a href="{{ url('category/footwear') }}">footwear</a>, <a href="{{ url('category/fashion-and-apparel') }}">fashion and apparel</a>, <a href="{{ url('category/electronics-and-gadgets') }}">consumer electronics</a>, and more. Our <strong>AI product photography prompts</strong> are designed for leading AI image generation platforms, including <strong>ChatGPT</strong>, <strong>Google Gemini</strong>, and <strong>Midjourney</strong>. Simply copy a prompt, past in your favorite ai image generator, and generate high-quality commercial product images, advertising visuals, and complete product lookbooks in minutes.
          </p>

        </div>
        <div class="col-lg-5">
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom shadow-xs">
            <h3 class="h5 fw-bold title-custom mb-3 d-flex align-items-center">
              <i class="bi bi-stars text-mint me-2"></i> Why Use Photoshoot Sets?
            </h3>
            <ul class="list-unstyled mb-0 d-flex flex-column gap-3 text-muted" style="line-height: 1.6; font-size: 0.92rem;">
              <li class="d-flex align-items-start">
                <i class="bi bi-check-circle-fill text-mint me-2 mt-1"></i>
                <span><strong class="text-dark title-custom">Consistent Lighting &amp; Ambience:</strong> Studio softbox, dramatic rim light, and natural golden hour consistency across every prompt.</span>
              </li>
              <li class="d-flex align-items-start">
                <i class="bi bi-check-circle-fill text-mint me-2 mt-1"></i>
                <span><strong class="text-dark title-custom">Multi-Angle Coverage:</strong> Front hero view, 45° dynamic profile, macro texture close-ups, and contextual lifestyle shots.</span>
              </li>
              <li class="d-flex align-items-start">
                <i class="bi bi-check-circle-fill text-mint me-2 mt-1"></i>
                <span><strong class="text-dark title-custom">Commercial License Ready:</strong> Tested prompt recipes tailored for e-commerce listings, social media ads, and brand marketing.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Photoshoot FAQ Section (Styled identically to /frequently-asked-questions) -->
      <div class="my-5">
        <div class="text-center mb-4">
          <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            {{ __('admin.faq') }}
          </span>
          <h2 class="display-6 fw-bold title-custom mb-3" style="line-height: 1.2;">
            Frequently Asked Questions
          </h2>
          <p class="text-muted lead mx-auto mb-4" style="font-size: 1.05rem; max-width: 680px;">
            Find quick answers to common questions about our AI product photoshoots, commercial licenses, and consistent prompt formulas.
          </p>
        </div>

        <div class="faq-accordion-wrapper" id="photoshootFaqAccordion" style="max-width: 860px; margin: 0 auto;">

          <!-- FAQ Item 1 -->
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs faq-item-wrapper">
            <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#photoshoot-faq-collapse-1"
                    aria-expanded="true"
                    aria-controls="photoshoot-faq-collapse-1">
              <div class="me-3">
                <h3 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                  What is an AI photoshoot set?
                </h3>
              </div>
              <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
            </button>
            <div id="photoshoot-faq-collapse-1"
                 class="collapse show faq-collapse"
                 data-bs-parent="#photoshootFaqAccordion">
              <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                An AI photoshoot set is a curated collection of complementary prompts created around a single product, model, or aesthetic theme. Rather than generating a single isolated image, photoshoot sets give you matching prompts covering multiple camera angles, lighting conditions, and contextual scenes to create a cohesive commercial lookbook.
              </div>
            </div>
          </div>

          <!-- FAQ Item 2 -->
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs faq-item-wrapper">
            <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0 collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#photoshoot-faq-collapse-2"
                    aria-expanded="false"
                    aria-controls="photoshoot-faq-collapse-2">
              <div class="me-3">
                <h3 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                  Can I use these photoshoot prompts for commercial e-commerce stores?
                </h3>
              </div>
              <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
            </button>
            <div id="photoshoot-faq-collapse-2"
                 class="collapse faq-collapse"
                 data-bs-parent="#photoshootFaqAccordion">
              <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                Yes. All prompts on ImagineBuddy are tailored for commercial workflows, including Amazon product listings, Shopify stores, social media advertising, and marketing collateral. You can customize the prompt descriptors to feature your own brand colors, materials, and product dimensions.
              </div>
            </div>
          </div>

          <!-- FAQ Item 3 -->
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs faq-item-wrapper">
            <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0 collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#photoshoot-faq-collapse-3"
                    aria-expanded="false"
                    aria-controls="photoshoot-faq-collapse-3">
              <div class="me-3">
                <h3 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                  Which AI image generators work best with these photoshoot prompts?
                </h3>
              </div>
              <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
            </button>
            <div id="photoshoot-faq-collapse-3"
                 class="collapse faq-collapse"
                 data-bs-parent="#photoshootFaqAccordion">
              <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                Our photoshoot prompts are optimized and tested across top generative AI platforms, including <strong>ChatGPT (DALL-E 3)</strong>, <strong>Google Gemini</strong>, and <strong>Midjourney</strong>. Many prompts feature precise camera lens parameters (such as 85mm f/1.4, 35mm wide angle), depth of field, and studio lighting modifiers that produce photo-realistic commercial renders across all three platforms.
              </div>
            </div>
          </div>

          <!-- FAQ Item 4 -->
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs faq-item-wrapper">
            <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0 collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#photoshoot-faq-collapse-4"
                    aria-expanded="false"
                    aria-controls="photoshoot-faq-collapse-4">
              <div class="me-3">
                <h3 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                  How do I maintain subject and product consistency across multiple shots?
                </h3>
              </div>
              <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
            </button>
            <div id="photoshoot-faq-collapse-4"
                 class="collapse faq-collapse"
                 data-bs-parent="#photoshootFaqAccordion">
              <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                Consistency is achieved by locking stylistic anchor tokens—including lighting conditions (e.g., Profoto studio softbox), color palette, camera model, and material descriptors—while only altering camera perspective tokens (such as eye-level hero shot, 45-degree angle, or macro close-up detail). In Midjourney, you can also pair these prompts with <code>--cref</code> (character reference) or <code>--sref</code> (style reference), while in ChatGPT and Google Gemini you can maintain continuous conversational context and anchor seed references across your session.
              </div>
            </div>
          </div>

        </div><!-- /.faq-accordion-wrapper -->
      </div>
    </div>

  </div>
</section>

@php
  // Build ItemList for current photoshoot cards
  $itemListElements = [];
  foreach ($photoshoots as $index => $item) {
    $itemCover = null;
    $firstImg = $item->images ? $item->images->first() : null;
    if ($firstImg) {
      if (!empty($firstImg->preview)) {
        $itemCover = Storage::url(config('path.preview') . $firstImg->preview);
      } elseif (!empty($firstImg->thumbnail)) {
        $itemCover = Storage::url(config('path.thumbnail') . $firstImg->thumbnail);
      }
    }
    $itemListElements[] = [
      '@type' => 'ListItem',
      'position' => $index + 1,
      'name' => $item->title,
      'url' => url('photoshoots', $item->slug),
      'image' => $itemCover ?: url('public/img', config('settings.logo_light', 'logo.png')),
    ];
  }

  // Build Breadcrumbs
  $breadcrumbs = [
    [
      '@type' => 'ListItem',
      'position' => 1,
      'name' => 'Home',
      'item' => url('/'),
    ],
    [
      '@type' => 'ListItem',
      'position' => 2,
      'name' => 'Photoshoots',
      'item' => url('photoshoots'),
    ],
  ];
  if ($currentCategory) {
    $breadcrumbs[] = [
      '@type' => 'ListItem',
      'position' => 3,
      'name' => $currentCategory->name . ' Photoshoots',
      'item' => url('photoshoots') . '?category=' . $currentCategory->slug,
    ];
  }

  // Build FAQ items
  $faqStructured = [
    [
      '@type' => 'Question',
      'name' => 'What is an AI photoshoot set?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'An AI photoshoot set is a curated collection of complementary prompts created around a single product, model, or aesthetic theme. Photoshoot sets give you matching prompts covering multiple camera angles, lighting conditions, and contextual scenes to create a cohesive commercial lookbook.'
      ]
    ],
    [
      '@type' => 'Question',
      'name' => 'Can I use these photoshoot prompts for commercial e-commerce stores?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes. All prompts on ImagineBuddy are tailored for commercial workflows, including Amazon product listings, Shopify stores, social media advertising, and marketing collateral.'
      ]
    ],
    [
      '@type' => 'Question',
      'name' => 'Which AI image generators work best with these photoshoot prompts?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Our photoshoot prompts are optimized and tested across top generative AI platforms, including ChatGPT (DALL-E 3), Google Gemini, and Midjourney. Many prompts feature precise camera lens parameters, depth of field, and studio lighting modifiers that produce photo-realistic commercial renders across all three platforms.'
      ]
    ],
    [
      '@type' => 'Question',
      'name' => 'How do I maintain subject and product consistency across multiple shots?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Consistency is achieved by locking stylistic anchor tokens—including lighting conditions, color palette, camera model, and material descriptors—while only altering camera perspective tokens. In Midjourney, you can also pair these prompts with --cref or --sref, while in ChatGPT and Google Gemini you can maintain continuous conversational context and anchor seed references across your session.'
      ]
    ]
  ];
@endphp

<!-- Rich Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "itemListElement": {!! json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    },
    @if(count($itemListElements) > 0)
    {
      "@type": "ItemList",
      "name": "{{ $currentCategory ? $currentCategory->name . ' AI Photoshoot Sets' : 'AI Product Photography Sets' }}",
      "description": "Curated multi-prompt commercial AI photoshoots for e-commerce and creative branding.",
      "itemListElement": {!! json_encode($itemListElements, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    },
    @endif
    {
      "@type": "FAQPage",
      "mainEntity": {!! json_encode($faqStructured, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    }
  ]
}
</script>

<style>
.photoshoot-category-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(150, 150, 150, 0.3) transparent;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
}
.photoshoot-category-scroll::-webkit-scrollbar {
  height: 4px;
}
.photoshoot-category-scroll::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.3);
  border-radius: 4px;
}
.photoshoot-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.1) !important;
}
.photoshoot-card .img-collection,
.photoshoot-card .photoshoot-cover-img {
  transition: transform 0.4s ease;
}
.photoshoot-card:hover .img-collection,
.photoshoot-card:hover .photoshoot-cover-img {
  transform: scale(1.05);
}
.text-mint {
  color: #00d690 !important;
}
</style>

@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  function centerActiveCategory(smooth) {
    var container = document.querySelector('.photoshoot-category-scroll');
    if (!container) return;
    var activeTab = container.querySelector('.active');
    if (!activeTab) return;

    var containerRect = container.getBoundingClientRect();
    var tabRect = activeTab.getBoundingClientRect();

    var diff = (tabRect.left + tabRect.width / 2) - (containerRect.left + containerRect.width / 2);
    var targetScroll = container.scrollLeft + diff;

    if (smooth) {
      container.scrollTo({ left: Math.max(0, targetScroll), behavior: 'smooth' });
    } else {
      container.scrollLeft = Math.max(0, targetScroll);
    }
  }

  // Center selected category tab immediately on load and after fonts/layout settle
  centerActiveCategory(false);
  setTimeout(function() { centerActiveCategory(true); }, 60);
  setTimeout(function() { centerActiveCategory(true); }, 250);

  $(window).on('resize', function() {
    centerActiveCategory(false);
  });

  var state = {
    page: {{ $photoshoots->hasMorePages() ? ($photoshoots->currentPage() + 1) : 'null' }},
    hasMore: {{ $photoshoots->hasMorePages() ? 'true' : 'false' }},
    loading: false,
    category: '{{ $categorySlug }}'
  };

  // Hide server-rendered pagination when interactive infinite scroll is active
  if (state.hasMore) {
    $('#photoshootsPagination').addClass('d-none');
  }

  function checkScrollLoad() {
    if (state.loading || !state.hasMore || !state.page) return;

    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    // Trigger loading 450px before reaching bottom
    if (scrollTop + windowHeight >= docHeight - 450) {
      fetchNextPage();
    }
  }

  function fetchNextPage() {
    state.loading = true;
    $('#photoshootsLoader').removeClass('d-none');

    $.ajax({
      url: '{{ url("photoshoots") }}',
      type: 'GET',
      data: {
        category: state.category,
        page: state.page
      },
      dataType: 'json',
      success: function(response) {
        if (response.html && response.html.trim() !== '') {
          $('#photoshootsContainer').append(response.html);
        }

        state.hasMore = response.hasMore;
        state.page = response.nextPage;
        state.loading = false;
        $('#photoshootsLoader').addClass('d-none');
      },
      error: function() {
        state.loading = false;
        $('#photoshootsLoader').addClass('d-none');
        // If AJAX load fails, reveal standard pagination as a reliable fallback
        $('#photoshootsPagination').removeClass('d-none');
      }
    });
  }

  $(window).on('scroll resize', checkScrollLoad);
})(jQuery);
</script>
@endsection
