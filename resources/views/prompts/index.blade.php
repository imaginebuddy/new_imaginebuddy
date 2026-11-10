@extends('layouts.app')

@section('title'){{ $currentCategory ? $currentCategory->name . ' AI Photography Prompts & Collections - ' : (!empty($promptSettings->meta_title) ? $promptSettings->meta_title . ' - ' : 'AI Product Photography Prompts & Collections - ') }}{{ config('settings.title') ?: config('app.name') }}@endsection

@section('description_custom'){{ !empty($promptSettings->meta_description) ? $promptSettings->meta_description . ' - ' : 'Browse tested AI product photography prompts across ethnic wear, jewelry, footwear, and consumer goods.' }}@endsection

@section('keywords_custom'){{ !empty($promptSettings->meta_keywords) ? $promptSettings->meta_keywords . ',' : 'ai prompts, product photography prompts, saree photography prompts, midjourney prompts, flux prompts' }}@endsection

@section('css')
<style>
/* ==========================================================================
   PROMPTS DIRECTORY: LUXURY EDITORIAL & RESPONSIVE DESIGN SYSTEM
   ========================================================================== */

:root {
  --prompt-mint: #00DF81;
  --prompt-mint-glow: rgba(0, 223, 129, 0.2);
  --prompt-mint-subtle: rgba(0, 223, 129, 0.08);
  --prompt-dark-surface: #07140E;
  --prompt-dark-card: #0D1913;
  --prompt-font-display: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Light Theme Variables */
html[data-bs-theme="light"] .prompts-directory-page {
  --prompt-page-bg: #FBF9F5;
  --prompt-card-bg: #FFFFFF;
  --prompt-card-border: rgba(0, 0, 0, 0.08);
  --prompt-card-hover-border: rgba(0, 223, 129, 0.6);
  --prompt-card-shadow: 0 4px 20px -2px rgba(24, 30, 26, 0.05);
  --prompt-card-hover-shadow: 0 20px 35px -8px rgba(18, 38, 28, 0.12);
  --prompt-text-title: #0F172A;
  --prompt-text-body: #475569;
  --prompt-text-muted: #64748B;
  --prompt-tag-bg: #F1EFEA;
  --prompt-tag-text: #334155;
  --prompt-tag-border: rgba(0, 0, 0, 0.06);
  --prompt-btn-bg: #0F172A;
  --prompt-btn-text: #FFFFFF;
}

/* Dark Theme Variables */
html[data-bs-theme="dark"] .prompts-directory-page {
  --prompt-page-bg: #080D0A;
  --prompt-card-bg: #111A15;
  --prompt-card-border: rgba(255, 255, 255, 0.08);
  --prompt-card-hover-border: rgba(0, 223, 129, 0.45);
  --prompt-card-shadow: 0 6px 24px -4px rgba(0, 0, 0, 0.4);
  --prompt-card-hover-shadow: 0 24px 40px -8px rgba(0, 223, 129, 0.12);
  --prompt-text-title: #F8FAFC;
  --prompt-text-body: #94A3B8;
  --prompt-text-muted: #64748B;
  --prompt-tag-bg: rgba(255, 255, 255, 0.06);
  --prompt-tag-text: #CBD5E1;
  --prompt-tag-border: rgba(255, 255, 255, 0.08);
  --prompt-btn-bg: #00DF81;
  --prompt-btn-text: #05140D;
}

.prompts-directory-page {
  background-color: var(--prompt-page-bg);
  min-height: 100vh;
  transition: background-color 0.3s ease;
}

/* --------------------------------------------------------------------------
   1. HERO SECTION (SCALIO LUXURY EDITORIAL CANVAS)
   -------------------------------------------------------------------------- */
.prompts-hero-section {
  background: linear-gradient(180deg, #030A06 0%, #06150D 60%, #081C12 100%);
  color: #FFFFFF;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding-top: 120px;
  padding-bottom: 70px;
  position: relative;
  overflow: hidden;
  border-bottom: 1px solid rgba(0, 223, 129, 0.12);
}

.prompts-hero-ambient-glow {
  position: absolute;
  top: -80px;
  left: 50%;
  transform: translateX(-50%);
  width: 780px;
  height: 480px;
  background: radial-gradient(circle, rgba(0, 223, 129, 0.16) 0%, rgba(0, 223, 129, 0.03) 50%, transparent 75%);
  pointer-events: none;
  z-index: 1;
}

.prompt-hero-eyebrow {
  display: inline-flex;
  align-items: center;
  padding: 6px 18px;
  border-radius: 9999px;
  background: rgba(0, 0, 0, 0.40);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 223, 129, 0.45);
  color: #00DF81;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  box-shadow: 0 0 20px -3px rgba(0, 223, 129, 0.25);
}

.prompt-hero-headline {
  font-family: var(--prompt-font-display);
  font-size: clamp(1.6rem, 2.85vw, 2.85rem);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.03em;
  color: #FFFFFF !important;
  margin-top: 18px;
  margin-bottom: 18px;
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.70);
  white-space: nowrap;
}

.prompt-text-gradient {
  color: #FFFFFF !important;
  -webkit-text-fill-color: #FFFFFF !important;
  background: none !important;
}

.prompt-hero-subhead {
  font-size: clamp(1rem, 1.4vw, 1.2rem);
  line-height: 1.65;
  color: rgba(255, 255, 255, 0.90);
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 32px;
  text-shadow: 0 1px 8px rgba(0, 0, 0, 0.75);
}

/* Hero Luxury Search Form */
.prompt-search-wrapper {
  max-width: 680px;
  margin-left: auto;
  margin-right: auto;
}

.prompt-search-card {
  background: rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 9999px;
  padding: 6px;
  display: flex;
  align-items: center;
  box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.45);
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.prompt-search-card:focus-within {
  border-color: #00DF81;
  box-shadow: 0 0 28px -4px rgba(0, 223, 129, 0.35);
}

.prompt-search-input {
  background: transparent !important;
  border: none !important;
  color: #FFFFFF !important;
  font-size: 1rem;
  padding-left: 18px;
}

.prompt-search-input::placeholder {
  color: rgba(255, 255, 255, 0.45);
}

.prompt-search-input:focus {
  outline: none !important;
  box-shadow: none !important;
}

.prompt-search-btn {
  background: #00DF81;
  color: #041209;
  border: none;
  font-weight: 700;
  font-size: 0.95rem;
  padding: 12px 28px;
  border-radius: 9999px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  white-space: nowrap;
}

.prompt-search-btn:hover {
  background: #00f891;
  color: #020b05;
  transform: scale(1.02);
  box-shadow: 0 4px 18px rgba(0, 223, 129, 0.45);
}

/* Quick Search Tag Suggestions */
.prompt-quick-tags {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 18px;
  font-size: 0.85rem;
}

.prompt-quick-tag {
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 4px 14px;
  border-radius: 9999px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.prompt-quick-tag:hover {
  color: #00DF81;
  background: rgba(0, 223, 129, 0.1);
  border-color: rgba(0, 223, 129, 0.35);
}

/* --------------------------------------------------------------------------
   2. CATEGORIES FILTER & TOOLBAR
   -------------------------------------------------------------------------- */
.prompts-toolbar-section {
  padding-top: 36px;
  padding-bottom: 18px;
}

.prompt-category-scroll {
  display: flex;
  align-items: center;
  gap: 10px;
  overflow-x: auto;
  padding: 8px 4px 16px 4px;
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 223, 129, 0.3) transparent;
  -webkit-overflow-scrolling: touch;
}

.prompt-category-scroll::-webkit-scrollbar {
  height: 4px;
}

.prompt-category-scroll::-webkit-scrollbar-thumb {
  background: rgba(0, 223, 129, 0.3);
  border-radius: 4px;
}

.prompt-cat-pill {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  padding: 8px 20px;
  border-radius: 9999px;
  font-size: 0.88rem;
  font-weight: 500;
  text-decoration: none;
  background: var(--prompt-card-bg);
  color: var(--prompt-text-body);
  border: 1px solid var(--prompt-card-border);
  box-shadow: var(--prompt-card-shadow);
  transition: all 0.2s ease;
}

.prompt-cat-pill:hover {
  color: var(--prompt-text-title);
  border-color: rgba(0, 223, 129, 0.4);
  transform: translateY(-2px);
}

.prompt-cat-pill.active {
  background: #00DF81;
  color: #041209 !important;
  font-weight: 700;
  border-color: #00DF81;
  box-shadow: 0 4px 16px rgba(0, 223, 129, 0.3);
}

.prompt-count-pill-badge {
  display: inline-block;
  font-size: 0.75rem;
  padding: 2px 7px;
  border-radius: 9999px;
  background: rgba(0, 0, 0, 0.08);
  margin-left: 6px;
}

.prompt-cat-pill.active .prompt-count-pill-badge {
  background: rgba(0, 0, 0, 0.18);
  color: #041209;
}

/* --------------------------------------------------------------------------
   3. PHOTOSHOOT ITEM & CARD STYLES (MATCHING /photoshoots 1:1)
   -------------------------------------------------------------------------- */
.photoshoot-item .photoshoot-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.photoshoot-item .photoshoot-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 32px -4px rgba(0, 0, 0, 0.1) !important;
}

.photoshoot-card-title a {
  transition: color 0.2s ease;
}

.photoshoot-item .photoshoot-card:hover .photoshoot-card-title a {
  color: var(--color-default) !important;
}

.btn-outline-custom {
  color: var(--color-default) !important;
  border-color: var(--color-default) !important;
  background: transparent;
  transition: all 0.2s ease;
}

.btn-outline-custom:hover {
  background-color: var(--color-default) !important;
  color: #FFFFFF !important;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* --------------------------------------------------------------------------
   4. CONVERSION BRIDGE BANNER (PRODUCT-LED SaaS UPSELL)
   -------------------------------------------------------------------------- */
.prompt-bridge-banner {
  background: linear-gradient(135deg, #05120B 0%, #081D13 50%, #0A2417 100%);
  border: 1px solid rgba(0, 223, 129, 0.25);
  border-radius: 28px;
  padding: 50px 45px;
  color: #FFFFFF;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5);
}

.prompt-bridge-badge {
  display: inline-block;
  padding: 5px 14px;
  border-radius: 9999px;
  background: rgba(0, 223, 129, 0.12);
  border: 1px solid rgba(0, 223, 129, 0.35);
  color: #00DF81;
  font-size: 0.75rem;
  font-weight: 700;
  text-uppercase;
  letter-spacing: 0.8px;
  margin-bottom: 16px;
}

.prompt-bridge-headline {
  font-family: var(--prompt-font-display);
  font-size: clamp(1.6rem, 2.8vw, 2.3rem);
  font-weight: 800;
  line-height: 1.25;
  color: #FFFFFF;
  margin-bottom: 14px;
}

.prompt-bridge-desc {
  font-size: 1.05rem;
  line-height: 1.65;
  color: rgba(255, 255, 255, 0.75);
  max-width: 650px;
}

.prompt-bridge-cta-btn {
  background: #00DF81;
  color: #05150D !important;
  font-weight: 800;
  font-size: 1.05rem;
  padding: 16px 36px;
  border-radius: 9999px;
  border: none;
  box-shadow: 0 8px 24px -2px rgba(0, 223, 129, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  white-space: nowrap;
}

.prompt-bridge-cta-btn:hover {
  background: #00f891;
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(0, 223, 129, 0.5);
}

.prompt-bridge-ambient {
  position: absolute;
  top: -100px;
  right: -100px;
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, rgba(0, 223, 129, 0.2) 0%, transparent 70%);
  pointer-events: none;
}

/* --------------------------------------------------------------------------
   5. RESPONSIVE BREAKPOINTS
   -------------------------------------------------------------------------- */
@media (max-width: 991px) {
  .prompts-hero-section {
    padding-top: 110px;
    padding-bottom: 60px;
  }
  .prompt-hero-headline {
    white-space: normal;
  }
  .prompt-bridge-banner {
    padding: 35px 25px;
  }
}

@media (max-width: 576px) {
  .prompt-search-card {
    border-radius: 18px;
    flex-direction: column;
    padding: 10px;
    gap: 10px;
  }
  .prompt-search-btn {
    width: 100%;
  }
  .prompt-card-media-wrapper {
    aspect-ratio: 16 / 10;
  }
}
</style>
@endsection

@section('content')
<div class="prompts-directory-page">

  <!-- ========================================================================
       1. HERO HEADER: CINEMATIC SCALIO EDITORIAL LUXURY CANVAS
       ======================================================================== -->
  @php
    $promptsHeroBg = !empty($promptSettings->cover_image) 
      ? $promptSettings->cover_image_url 
      : asset('public/img/' . $settings->image_header);
  @endphp
  <header class="prompts-hero-section text-center position-relative overflow-hidden d-flex flex-column justify-content-center" style="background-image: url('{{ $promptsHeroBg }}') !important; background-size: cover !important; background-position: center center !important;">
    
    {{-- Atmospheric Gradient Overlay matching homepage opacity and lighting --}}
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 50% 40%, rgba(3, 10, 6, 0.25) 0%, rgba(3, 10, 6, 0.65) 100%), linear-gradient(180deg, rgba(3, 10, 6, 0.25) 0%, transparent 40%, rgba(3, 10, 6, 0.75) 100%); pointer-events: none; z-index: 1;"></div>

    <div class="container position-relative my-auto" style="z-index: 2;">
      
      <!-- Eyebrow Pill -->
      <div class="d-inline-flex mb-3">
        <span class="prompt-hero-eyebrow">
          <i class="bi bi-stars me-2"></i> {{ $promptSettings->hero_badge ?: 'Curated AI Prompt Directory' }}
        </span>
      </div>

      <!-- Main Monumental Headline (H1) -->
      <h1 class="prompt-hero-headline text-white">
        @if ($currentCategory)
          {{ $currentCategory->name }} AI Photography Prompts
        @elseif (!empty($promptSettings->hero_title))
          {{ $promptSettings->hero_title }}
        @else
          Better prompts for better commercial product work.
        @endif
      </h1>

      <!-- Value Proposition Subhead -->
      <p class="prompt-hero-subhead">
        @if ($currentCategory)
          Curated commercial photography prompt recipes for {{ strtolower($currentCategory->name) }} products. Production-tested across Midjourney v6, Flux.1, Google Gemini, and ChatGPT.
        @else
          {{ $promptSettings->hero_subtitle ?: 'Explore production-tested AI photography prompt collections for e-commerce, fashion, and luxury goods. Optimized for Midjourney, Flux, Google Gemini, and ChatGPT.' }}
        @endif
      </p>

      <!-- Integrated Luxury Search Bar -->
      <div class="prompt-search-wrapper">
        <form action="{{ url('prompts') }}" method="GET" class="position-relative">
          @if ($currentCategory)
            <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
          @endif
          @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
          @endif

          <div class="prompt-search-card">
            <span class="ps-3 pe-2 text-white-50"><i class="bi bi-search fs-5"></i></span>
            <input type="text" 
                   name="q" 
                   class="form-control prompt-search-input" 
                   value="{{ $searchQuery }}" 
                   placeholder="Search prompts (e.g. 'saree', 'jewelry', 'watches', 'sneakers')..."
                   autocomplete="off">
            @if (!empty($searchQuery))
              <a href="{{ url('prompts') }}{{ $currentCategory ? '?category=' . $currentCategory->slug : '' }}" class="btn btn-link text-white-50 text-decoration-none px-2" title="Clear search">
                <i class="bi bi-x-circle-fill"></i>
              </a>
            @endif
            <button type="submit" class="prompt-search-btn">
              <span>Search Prompts</span>
              <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        <!-- Quick Search Pill Suggestions -->
        <div class="prompt-quick-tags">
          <span class="text-white-50 small me-1">Popular:</span>
          <a href="{{ url('prompts') }}?q=saree" class="prompt-quick-tag">Saree Prompts</a>
          <a href="{{ url('prompts') }}?q=jewelry" class="prompt-quick-tag">Jewelry &amp; Necklaces</a>
          <a href="{{ url('prompts') }}?q=watch" class="prompt-quick-tag">Luxury Watches</a>
          <a href="{{ url('prompts') }}?q=shoes" class="prompt-quick-tag">Footwear</a>
          <a href="{{ url('prompts') }}?q=cosmetics" class="prompt-quick-tag">Cosmetics</a>
        </div>
      </div>

    </div>
  </header>

  <!-- ========================================================================
       2. CATALOG DISCOVERY TOOLBAR & CATEGORIES FILTER
       ======================================================================== -->
  <section class="prompts-toolbar-section">
    <div class="container">
      
      <!-- Category Filter Scrollable Bar -->
      <div class="position-relative mb-4">
        <div class="prompt-category-scroll">
          <a href="{{ url('prompts') }}{{ !empty($searchQuery) ? '?q=' . urlencode($searchQuery) : '' }}" 
             class="prompt-cat-pill {{ empty($currentCategory) ? 'active' : '' }}">
            <span>All Prompts</span>
            <span class="prompt-count-pill-badge">{{ $totalCollectionsCount }}</span>
          </a>
          @foreach ($categories as $cat)
            <a href="{{ url('prompts') }}?category={{ $cat->slug }}{{ !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' }}" 
               class="prompt-cat-pill {{ ($currentCategory && $currentCategory->id == $cat->id) ? 'active' : '' }}">
              <span>{{ $cat->name }}</span>
              <span class="prompt-count-pill-badge">{{ $cat->prompt_collections_count }}</span>
            </a>
          @endforeach
        </div>
      </div>

      <!-- Section Title & Filter Controls Bar -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 border-bottom" style="border-color: var(--prompt-card-border) !important;">
        <div>
          <h2 class="h5 fw-bold mb-0" style="color: var(--prompt-text-title);">
            @if ($currentCategory)
              {{ $currentCategory->name }} Collections
            @elseif (!empty($searchQuery))
              Search Results for "{{ $searchQuery }}"
            @else
              Start with what you sell
            @endif
          </h2>
          <small style="color: var(--prompt-text-muted);">
            Showing {{ $collections->total() }} commercial prompt {{ str_plural('collection', $collections->total()) }}
          </small>
        </div>

        <!-- Sorting & Filter Controls -->
        <div class="d-flex align-items-center gap-3">
          @if ($currentCategory || !empty($searchQuery))
            <a href="{{ url('prompts') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5" style="font-size: 0.8rem;">
              <i class="bi bi-x-circle me-1"></i> Reset Filters
            </a>
          @endif

          <!-- Sort Dropdown -->
          <div class="dropdown">
            <button class="btn btn-sm rounded-pill px-3 py-1.5 dropdown-toggle" 
                    type="button" 
                    id="sortDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false"
                    style="background: var(--prompt-card-bg); border: 1px solid var(--prompt-card-border); color: var(--prompt-text-title); font-size: 0.82rem; font-weight: 600;">
              <i class="bi bi-arrow-down-up me-1 text-mint"></i>
              Sort: {{ ucfirst($sort ?? 'Featured') }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="sortDropdown" style="font-size: 0.85rem;">
              <li><a class="dropdown-item {{ ($sort ?? '') == 'featured' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}">Featured First</a></li>
              <li><a class="dropdown-item {{ ($sort ?? '') == 'prompts' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'prompts']) }}">Most Prompts</a></li>
              <li><a class="dropdown-item {{ ($sort ?? '') == 'views' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'views']) }}">Most Viewed</a></li>
              <li><a class="dropdown-item {{ ($sort ?? '') == 'newest' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">Newest Added</a></li>
            </ul>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ========================================================================
       3. PROMPT COLLECTIONS GRID CONTAINER
       ======================================================================== -->
  <section class="py-4">
    <div class="container">
      
      @if ($collections->total() != 0)
        <div class="row g-4" id="promptsGridContainer">
          @foreach ($collections as $collection)
            @include('includes.prompt-collection-card', ['collection' => $collection])
          @endforeach
        </div>

        <!-- Crawlable Server-Side Pagination -->
        @if ($collections->hasPages())
          <div class="photoshoot-pagination-wrapper my-5 d-flex justify-content-center">
            {{ $collections->links('vendor.pagination.bootstrap-4') }}
          </div>
        @endif

      @else
        <!-- High-Quality Empty State -->
        <div class="text-center py-5 my-5 rounded-4 p-5" style="background: var(--prompt-card-bg); border: 1px dashed var(--prompt-card-border);">
          <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: rgba(0, 223, 129, 0.1); color: #00DF81; font-size: 2rem;">
              <i class="bi bi-search"></i>
            </span>
          </div>
          <h3 class="fw-bold h4 mb-2" style="color: var(--prompt-text-title);">No prompt collections matched</h3>
          <p class="mb-4 mx-auto" style="color: var(--prompt-text-muted); max-width: 520px;">
            @if (!empty($searchQuery))
              We couldn't find any prompt collections matching <strong>"{{ $searchQuery }}"</strong>. Try searching for broader terms like "saree", "watch", or "apparel".
            @elseif ($currentCategory)
              No collections have been published under <strong>{{ $currentCategory->name }}</strong> yet.
            @else
              Check back soon as we curate and publish fresh commercial prompt recipes daily.
            @endif
          </p>
          <a href="{{ url('prompts') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> View All Prompt Collections
          </a>
        </div>
      @endif

    </div>
  </section>

  <!-- ========================================================================
       4. CONVERSION BRIDGE BANNER (PRODUCT-LED SaaS UPSELL)
       ======================================================================== -->
  <section class="py-5">
    <div class="container">
      <div class="prompt-bridge-banner">
        <div class="prompt-bridge-ambient"></div>
        
        <div class="row align-items-center position-relative" style="z-index: 2;">
          <div class="col-lg-8 mb-4 mb-lg-0">
            <span class="prompt-bridge-badge">
              <i class="bi bi-lightning-charge-fill me-1"></i> Automated AI Studio
            </span>
            <h2 class="prompt-bridge-headline">
              {{ $promptSettings->banner_title ?: 'Done with manual prompts? Let AI do the studio work.' }}
            </h2>
            <p class="prompt-bridge-desc mb-4">
              {{ $promptSettings->banner_subtitle ?: 'Upload a single raw smartphone photo and transform it into marketplace-ready model and studio catalog visuals in seconds.' }}
            </p>

            <div class="row g-3">
              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-check-circle-fill text-mint fs-5"></i>
                  <span class="text-white small fw-semibold">Preserves real fabric weave &amp; colors</span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-check-circle-fill text-mint fs-5"></i>
                  <span class="text-white small fw-semibold">Amazon &amp; Shopify compliant lighting</span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-check-circle-fill text-mint fs-5"></i>
                  <span class="text-white small fw-semibold">Multi-angle model photoshoot outputs</span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-check-circle-fill text-mint fs-5"></i>
                  <span class="text-white small fw-semibold">Zero prompt engineering required</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 text-lg-end">
            <a href="{{ $promptSettings->banner_cta_url ?: url('register') }}" class="btn prompt-bridge-cta-btn">
              <span>{{ $promptSettings->banner_cta_text ?: 'Try ImagineBuddy Free' }}</span>
              <i class="bi bi-arrow-right ms-2"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

</div>
@endsection
