@extends('layouts.app')

@section('content')
<!-- Scroll Reading Progress Bar -->
<div id="readingProgressBar" class="reading-progress-bar"></div>

@if (isset($isPreview) && $isPreview)
<div class="alert alert-warning border-0 rounded-0 text-center py-2 mb-0 sticky-top" style="z-index: 1040; top: 70px;">
  <i class="bi bi-eye-fill me-1"></i> <strong>Draft Preview Mode:</strong> This article is unpublished and visible only to authorized administrators.
</div>
@endif

<article class="blog-detail-article" style="padding-top: 90px;">
  <div class="container blog-container">

    <!-- Top Minimalist Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb blog-breadcrumb align-items-center mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none"><i class="bi-house-door me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('blog') }}" class="text-decoration-none">Blog</a></li>
        @if ($post->category)
          <li class="breadcrumb-item"><a href="{{ url('blog/category', $post->category->slug) }}" class="text-decoration-none">{{ $post->category->name }}</a></li>
        @endif
        <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 280px;">{{ $post->title }}</li>
      </ol>
    </nav>

    <!-- Editorial Header Section (Clean Canvas, No Clunky Box) -->
    <header class="blog-hero-header text-center mb-4 mb-md-5">
      @if ($post->category)
        <div class="mb-3">
          <a href="{{ url('blog/category', $post->category->slug) }}" class="blog-category-badge text-decoration-none">
            <span class="category-dot"></span>
            {{ $post->category->name }}
          </a>
        </div>
      @endif

      <!-- Expressive Title -->
      <h1 class="blog-article-title mb-3">
        {{ $post->title }}
      </h1>

      @if ($post->excerpt)
        <p class="blog-article-lead mx-auto mb-4">
          {{ $post->excerpt }}
        </p>
      @endif

      <!-- Sleek Author & Metadata Capsule -->
      <div class="blog-meta-strip d-inline-flex flex-wrap align-items-center justify-content-center gap-2 gap-md-3">
        <!-- Author info -->
        <div class="d-inline-flex align-items-center gap-2 blog-author-chip">
          @if ($post->user && $post->user->avatar)
            <img src="{{ Storage::url(config('path.avatar') . $post->user->avatar) }}" alt="{{ $post->user->username }}" class="author-avatar rounded-circle">
          @else
            <div class="author-avatar-fallback rounded-circle">
              <i class="bi bi-person-fill"></i>
            </div>
          @endif
          <a href="{{ $post->user ? url($post->user->username) : '#' }}" class="author-name text-decoration-none fw-semibold">
            {{ $post->user ? ($post->user->name ?: $post->user->username) : 'Super Admin' }}
          </a>
          <i class="bi bi-patch-check-fill text-mint ms-1" title="Verified Author"></i>
        </div>

        <span class="meta-divider d-none d-sm-inline">&bull;</span>

        <!-- Date -->
        <span class="meta-item" title="Published Date">
          <i class="bi bi-calendar3 me-1"></i>
          {{ Helper::formatDate($post->published_at ?: $post->created_at) }}
        </span>

        <span class="meta-divider d-none d-sm-inline">&bull;</span>

        <!-- Reading Time -->
        <span class="meta-item">
          <i class="bi bi-clock me-1 text-mint"></i>
          {{ $post->reading_time }} min read
        </span>

        @if ($post->views_count > 0)
          <span class="meta-divider d-none d-md-inline">&bull;</span>
          <span class="meta-item d-none d-md-inline">
            <i class="bi bi-eye me-1"></i>
            {{ number_format($post->views_count) }} views
          </span>
        @endif
      </div>
    </header>

    <!-- Cinematic Featured Image Hero -->
    @if ($post->featured_image)
    <figure class="blog-featured-image-wrapper mb-4 mb-md-5">
      <div class="featured-image-frame position-relative overflow-hidden">
        <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="img-fluid w-100 featured-hero-img" loading="eager" />
      </div>
      @if ($post->featured_image_alt)
        <figcaption class="text-center text-muted small mt-2 fst-italic">
          {{ $post->featured_image_alt }}
        </figcaption>
      @endif
    </figure>
    @endif

    <!-- Main Content Layout: Sticky Sidebar (Desktop) + Clean Reading Area -->
    <div class="row g-4 g-lg-5">

      <!-- Left Column: Desktop Sticky Table of Contents (>= 992px) -->
      <aside class="col-lg-4 col-xl-3 d-none d-lg-block">
        <div class="sticky-sidebar-wrapper" id="stickyTocWrapper">
          <div class="toc-card p-4 rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-subtle">
              <span class="toc-heading-title text-uppercase fw-bold">
                <i class="bi bi-list-nested text-mint me-2"></i>On This Page
              </span>
              <span class="pulse-indicator" title="Live reading tracker"></span>
            </div>

            <!-- Dynamic Table of Contents Links -->
            <nav id="desktopTocList" class="desktop-toc-nav"></nav>

            <!-- Quick Share & Reading Progress Widget in Sidebar -->
            <div class="sidebar-footer-widget pt-3 mt-3 border-top border-subtle">
              <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                <span>Reading Progress</span>
                <span id="tocProgressPercent" class="fw-semibold text-mint">0%</span>
              </div>
              <div class="progress" style="height: 4px; background: rgba(150, 150, 150, 0.15); border-radius: 4px;">
                <div id="tocSidebarProgressBar" class="progress-bar bg-mint" style="width: 0%; transition: width 0.1s ease;"></div>
              </div>

              <!-- Quick Copy in Sidebar -->
              <button type="button" class="btn btn-sm btn-subtle-pill w-100 mt-3 d-flex align-items-center justify-content-center gap-2" onclick="copyArticleLink(this)">
                <i class="bi bi-link-45deg fs-6"></i>
                <span class="btn-copy-label">Copy Article Link</span>
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- Right Column: Reading Canvas & Mobile TOC -->
      <div class="col-12 col-lg-8 col-xl-9">

        <!-- Mobile / Tablet Collapsible Table of Contents (< 992px) -->
        <div id="mobileTocWrapper" class="mobile-toc-container d-lg-none mb-4 d-none">
          <div class="mobile-toc-card rounded-4 overflow-hidden border border-subtle">
            <button class="mobile-toc-toggle w-100 p-3 text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#mobileTocCollapse" aria-expanded="false" aria-controls="mobileTocCollapse">
              <span class="d-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-list-nested text-mint fs-5"></i>
                <span>Table of Contents</span>
                <span class="badge rounded-pill bg-subtle-mint text-mint px-2 py-1 ms-1" id="mobileTocCount">0</span>
              </span>
              <i class="bi bi-chevron-down mobile-toc-chevron transition-transform"></i>
            </button>
            <div class="collapse" id="mobileTocCollapse">
              <nav id="mobileTocList" class="mobile-toc-nav p-3 pt-0 border-top border-subtle"></nav>
            </div>
          </div>
        </div>

        <!-- The Article Body: Pure Editorial Reading Flow -->
        <div class="blog-reading-canvas">
          <div class="blog-article-content">
            {!! $post->content !!}
          </div>
        </div>

        <!-- Creative Contextual In-Article CTA Banner -->
        <div class="blog-cta-banner rounded-4 p-4 p-md-5 my-5 position-relative overflow-hidden">
          <div class="cta-glow-backdrop"></div>
          <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8 mb-3 mb-md-0">
              <div class="d-inline-flex align-items-center gap-2 cta-pill-badge mb-3">
                <i class="bi bi-stars text-mint"></i>
                <span>ImagineBuddy Studio</span>
              </div>
              <h3 class="cta-title fw-bold text-white mb-2 h4">
                Bring These AI Prompts to Life
              </h3>
              <p class="cta-subtitle text-white-50 mb-0 small">
                Explore thousands of tested commercial product photoshoot prompts, AI recipes, and high-converting visual setups ready to copy and use.
              </p>
            </div>
            <div class="col-md-4 text-md-end">
              <a href="{{ url('photoshoots') }}" class="btn btn-mint-glow rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                <span>Browse Prompts</span>
                <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Tags & Modern Social Sharing Bar -->
        <div class="blog-article-footer pt-4 pb-4 mb-4 border-top border-bottom border-subtle">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <!-- Tags -->
            <div class="d-flex flex-wrap align-items-center gap-2">
              @if ($post->tags->count() > 0)
                <span class="text-muted small fw-semibold me-1"><i class="bi-tags me-1"></i> Topics:</span>
                @foreach ($post->tags as $t)
                  <a href="{{ url('blog/tag', $t->slug) }}" class="blog-tag-pill text-decoration-none">
                    #{{ $t->name }}
                  </a>
                @endforeach
              @endif
            </div>

            <!-- Social Share Buttons -->
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small fw-semibold me-1">Share:</span>

              <!-- Twitter / X -->
              <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url('blog', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-share-circle" title="Share on X">
                <i class="bi-twitter-x"></i>
              </a>

              <!-- Facebook -->
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('blog', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-share-circle" title="Share on Facebook">
                <i class="fab fa-facebook-f"></i>
              </a>

              <!-- LinkedIn -->
              <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('blog', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-share-circle" title="Share on LinkedIn">
                <i class="fab fa-linkedin-in"></i>
              </a>

              <!-- WhatsApp -->
              <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url('blog', $post->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-share-circle" title="Share on WhatsApp">
                <i class="bi-whatsapp"></i>
              </a>

              <!-- Copy Link -->
              <button type="button" class="btn-share-circle" onclick="copyArticleLink(this)" title="Copy Link">
                <i class="bi-link-45deg"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Previous & Next Article Navigation -->
        <div class="row g-3 mb-5">
          <div class="col-sm-6">
            @if ($prevPost)
              <a href="{{ url('blog', $prevPost->slug) }}" class="post-nav-card p-3 p-md-4 rounded-4 text-decoration-none d-flex flex-column h-100">
                <span class="nav-direction mb-1"><i class="bi-arrow-left me-1"></i> Previous Article</span>
                <span class="nav-title fw-bold text-truncate">{{ $prevPost->title }}</span>
              </a>
            @endif
          </div>
          <div class="col-sm-6">
            @if ($nextPost)
              <a href="{{ url('blog', $nextPost->slug) }}" class="post-nav-card p-3 p-md-4 rounded-4 text-decoration-none d-flex flex-column h-100 text-end">
                <span class="nav-direction mb-1">Next Article <i class="bi-arrow-right ms-1"></i></span>
                <span class="nav-title fw-bold text-truncate">{{ $nextPost->title }}</span>
              </a>
            @endif
          </div>
        </div>

      </div><!-- /.col-xl-9 -->
    </div><!-- /.row -->

    <!-- Related Articles Grid -->
    @if ($relatedPosts->count() > 0)
    <section class="related-articles-section pt-5 mt-4 border-top border-subtle">
      <div class="text-center mb-4">
        <span class="badge bg-subtle-mint text-mint rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
          Keep Reading
        </span>
        <h3 class="fw-bold title-custom h4">Related Guides &amp; Insights</h3>
      </div>
      <div class="row g-4">
        @foreach ($relatedPosts as $rel)
          @include('includes.blog-card', ['post' => $rel, 'cardCol' => 'col-sm-6 col-md-6 col-lg-4 mb-4 blog-item'])
        @endforeach
      </div>
    </section>
    @endif

  </div><!-- /.container -->
</article>

<!-- Creative Blog UX Styles (Light & Dark Theme Compatible) -->
<style>
/* CSS Variables for Clean Theming */
:root {
  --blog-bg-surface: #ffffff;
  --blog-text-main: #1f2937;
  --blog-text-muted: #64748b;
  --blog-border-subtle: rgba(0, 0, 0, 0.08);
  --blog-card-hover: rgba(0, 0, 0, 0.03);
  --color-mint: #00d690;
  --color-mint-subtle: rgba(0, 214, 144, 0.12);
  --color-mint-glow: rgba(0, 214, 144, 0.35);
}

[data-bs-theme="dark"] {
  --blog-bg-surface: #181d24;
  --blog-text-main: #e2e8f0;
  --blog-text-muted: #94a3b8;
  --blog-border-subtle: rgba(255, 255, 255, 0.08);
  --blog-card-hover: rgba(255, 255, 255, 0.04);
}

/* Reading Progress Bar */
.reading-progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 3px;
  background: linear-gradient(90deg, #00d690, #00b377);
  width: 0%;
  z-index: 1060;
  transition: width 0.1s ease;
  box-shadow: 0 0 10px rgba(0, 214, 144, 0.6);
}

.text-mint {
  color: var(--color-mint) !important;
}
.bg-mint {
  background-color: var(--color-mint) !important;
}
.bg-subtle-mint {
  background-color: var(--color-mint-subtle) !important;
}
.border-subtle {
  border-color: var(--blog-border-subtle) !important;
}

/* Container & Breadcrumb */
.blog-container {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1.25rem;
  padding-right: 1.25rem;
}
.blog-breadcrumb {
  font-size: 0.85rem;
  overflow-x: auto;
  white-space: nowrap;
  padding: 0.4rem 0;
}
.blog-breadcrumb .breadcrumb-item a {
  color: var(--blog-text-muted);
  transition: color 0.2s ease;
}
.blog-breadcrumb .breadcrumb-item a:hover {
  color: var(--color-mint);
}
.blog-breadcrumb .breadcrumb-item.active {
  color: var(--blog-text-main);
  opacity: 0.8;
}

/* Header & Typography */
.blog-hero-header {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
}
.blog-category-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.95rem;
  border-radius: 9999px;
  background: var(--color-mint-subtle);
  color: var(--color-mint);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
  border: 1px solid rgba(0, 214, 144, 0.25);
}
.blog-category-badge:hover {
  background: var(--color-mint);
  color: #ffffff;
  transform: translateY(-1px);
}
.category-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
}

.blog-article-title {
  font-size: clamp(1.9rem, 4vw, 3.1rem);
  font-weight: 800;
  line-height: 1.2;
  letter-spacing: -0.025em;
  color: var(--blog-text-main);
}
[data-bs-theme="dark"] .blog-article-title {
  color: #f8fafc;
}

.blog-article-lead {
  font-size: clamp(1.05rem, 2vw, 1.25rem);
  line-height: 1.65;
  color: var(--blog-text-muted);
  font-weight: 400;
}

/* Meta Strip */
.blog-meta-strip {
  padding: 0.6rem 1.25rem;
  border-radius: 9999px;
  background: var(--blog-bg-surface);
  border: 1px solid var(--blog-border-subtle);
  font-size: 0.86rem;
  color: var(--blog-text-muted);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.author-avatar {
  width: 28px;
  height: 28px;
  object-fit: cover;
  border: 1.5px solid var(--color-mint);
}
.author-avatar-fallback {
  width: 28px;
  height: 28px;
  background: #111;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}
.author-name {
  color: var(--blog-text-main);
  transition: color 0.2s ease;
}
.author-name:hover {
  color: var(--color-mint);
}
.meta-divider {
  opacity: 0.35;
}
.meta-item {
  display: inline-flex;
  align-items: center;
}

/* Featured Hero Image */
.blog-featured-image-wrapper {
  width: 100%;
  max-width: 100%;
  margin-left: 0;
  margin-right: 0;
}
.featured-image-frame {
  border-radius: 20px;
  border: 1px solid var(--blog-border-subtle);
  box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.14);
  max-height: 520px;
  background: #0f1216;
}
.featured-hero-img {
  width: 100%;
  max-height: 520px;
  object-fit: cover;
  display: block;
}
@media (max-width: 768px) {
  .featured-image-frame {
    border-radius: 14px;
    max-height: 320px;
  }
}

/* Sticky Sidebar Table of Contents */
.sticky-sidebar-wrapper {
  position: sticky;
  top: 105px;
}
.toc-card {
  background: var(--blog-bg-surface);
  border: 1px solid var(--blog-border-subtle);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}
.toc-heading-title {
  font-size: 0.76rem;
  letter-spacing: 0.8px;
  color: var(--blog-text-muted);
}
.pulse-indicator {
  width: 8px;
  height: 8px;
  background: var(--color-mint);
  border-radius: 50%;
  box-shadow: 0 0 0 0 rgba(0, 214, 144, 0.7);
  animation: pulse-ring 2s infinite;
}
@keyframes pulse-ring {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 214, 144, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(0, 214, 144, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 214, 144, 0); }
}

.desktop-toc-nav {
  max-height: 380px;
  overflow-y: auto;
  padding-right: 4px;
}
.desktop-toc-nav::-webkit-scrollbar {
  width: 3px;
}
.desktop-toc-nav::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.2);
  border-radius: 4px;
}
.desktop-toc-nav a {
  display: block;
  padding: 0.45rem 0.6rem;
  border-radius: 8px;
  font-size: 0.86rem;
  color: var(--blog-text-muted);
  text-decoration: none;
  line-height: 1.4;
  transition: all 0.2s ease;
  border-left: 2px solid transparent;
}
.desktop-toc-nav a:hover {
  color: var(--blog-text-main);
  background: var(--blog-card-hover);
  transform: translateX(3px);
}
.desktop-toc-nav a.toc-active {
  color: var(--color-mint) !important;
  font-weight: 700;
  border-left-color: var(--color-mint);
  background: var(--color-mint-subtle);
}
.desktop-toc-nav a.toc-h3 {
  padding-left: 1.4rem;
  font-size: 0.8rem;
}

.btn-subtle-pill {
  background: var(--blog-card-hover);
  border: 1px solid var(--blog-border-subtle);
  color: var(--blog-text-muted);
  border-radius: 9999px;
  font-size: 0.82rem;
  padding: 0.45rem 0.9rem;
  transition: all 0.2s ease;
}
.btn-subtle-pill:hover {
  background: var(--color-mint);
  color: #fff;
  border-color: var(--color-mint);
}

/* Mobile Collapsible TOC */
.mobile-toc-card {
  background: var(--blog-bg-surface);
}
.mobile-toc-toggle {
  background: transparent;
  border: none;
  color: var(--blog-text-main);
}
.mobile-toc-chevron {
  transition: transform 0.25s ease;
}
.mobile-toc-toggle[aria-expanded="true"] .mobile-toc-chevron {
  transform: rotate(180deg);
}
.mobile-toc-nav a {
  display: block;
  padding: 0.45rem 0.5rem;
  color: var(--blog-text-muted);
  text-decoration: none;
  font-size: 0.9rem;
}
.mobile-toc-nav a:hover,
.mobile-toc-nav a.toc-active {
  color: var(--color-mint);
  font-weight: 600;
}
.mobile-toc-nav a.toc-h3 {
  padding-left: 1.25rem;
  font-size: 0.84rem;
}

/* Reading Canvas & Article Typography */
.blog-reading-canvas {
  width: 100%;
}
.blog-article-content {
  font-size: 1.15rem;
  line-height: 1.85;
  color: var(--blog-text-main);
  letter-spacing: -0.005em;
  width: 100%;
}
.blog-article-content p,
.blog-article-content ul,
.blog-article-content ol {
  max-width: 860px;
  margin-bottom: 1.65rem;
}

/* Heading Typography */
.blog-article-content h2 {
  font-size: clamp(1.6rem, 3.5vw, 2.1rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-top: 3.25rem;
  margin-bottom: 1.25rem;
  line-height: 1.3;
  color: var(--blog-text-main);
  position: relative;
  padding-bottom: 0.5rem;
}
.blog-article-content h2::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 42px;
  height: 3px;
  background: var(--color-mint);
  border-radius: 2px;
}
.blog-article-content h3 {
  font-size: clamp(1.25rem, 2.5vw, 1.5rem);
  font-weight: 700;
  letter-spacing: -0.015em;
  margin-top: 2.25rem;
  margin-bottom: 0.85rem;
  line-height: 1.35;
  color: var(--blog-text-main);
}
.blog-article-content h4 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
  color: var(--blog-text-main);
}

/* Blockquotes */
.blog-article-content blockquote {
  position: relative;
  border-left: 4px solid var(--color-mint);
  padding: 1.25rem 1.75rem;
  margin: 2rem 0;
  background: var(--blog-card-hover);
  border-radius: 0 14px 14px 0;
  font-style: italic;
  font-size: 1.18rem;
  line-height: 1.75;
  color: var(--blog-text-main);
}
.blog-article-content blockquote::before {
  content: "\201C";
  position: absolute;
  top: -10px;
  left: 12px;
  font-size: 3rem;
  opacity: 0.15;
  color: var(--color-mint);
  line-height: 1;
}

/* Code Snippets & AI Prompts */
.blog-article-content pre {
  background: #15181e !important;
  color: #e2e8f0 !important;
  padding: 1.25rem 1.5rem !important;
  border-radius: 14px !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  font-size: 0.96rem !important;
  line-height: 1.75 !important;
  margin: 1.75rem 0 !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
  white-space: pre-wrap !important; /* Wrap long lines to show full content in height */
  word-wrap: break-word !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
  height: auto !important;
  min-height: auto !important;
  max-height: none !important;
  overflow-x: visible !important;
  overflow-y: visible !important;
  position: relative !important;
}

.blog-article-content pre code {
  color: #f1f5f9 !important;
  background: transparent !important;
  padding: 0 !important;
  border-radius: 0 !important;
  white-space: pre-wrap !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
  display: block !important;
  width: 100% !important;
  font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
  font-size: 0.95rem !important;
  line-height: 1.75 !important;
}

/* Standalone Inline Code */
.blog-article-content :not(pre) > code {
  color: var(--color-mint);
  background: var(--color-mint-subtle);
  padding: 0.2rem 0.45rem;
  border-radius: 6px;
  font-size: 0.9em;
  word-break: break-word;
}

/* Prompt Copy Button */
.pre-copy-btn {
  position: absolute;
  top: 10px;
  right: 12px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.14);
  color: #94a3b8;
  font-size: 0.76rem;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  user-select: none;
  z-index: 5;
  letter-spacing: 0.3px;
}
.pre-copy-btn:hover {
  background: var(--color-mint);
  border-color: var(--color-mint);
  color: #0d1117;
  transform: translateY(-1px);
}

/* Images & Media in Article */
.blog-article-content img {
  max-width: 100%;
  height: auto;
  border-radius: 16px;
  margin: 2rem 0;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid var(--blog-border-subtle);
}

/* Tables */
.blog-article-content table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin: 2rem 0;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid var(--blog-border-subtle);
}
.blog-article-content table th {
  background: var(--blog-card-hover);
  padding: 0.85rem 1.15rem;
  font-weight: 700;
  text-align: left;
  border-bottom: 1px solid var(--blog-border-subtle);
  color: var(--blog-text-main);
}
.blog-article-content table td {
  padding: 0.85rem 1.15rem;
  border-bottom: 1px solid var(--blog-border-subtle);
  color: var(--blog-text-main);
}

/* In-Article Promotion CTA Banner */
.blog-cta-banner {
  background: linear-gradient(135deg, #10141a 0%, #171d26 50%, #0d1219 100%);
  border: 1px solid rgba(0, 214, 144, 0.3) !important;
  box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.45);
}
.cta-glow-backdrop {
  position: absolute;
  top: -50%;
  right: -20%;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle, rgba(0, 214, 144, 0.22) 0%, rgba(0, 214, 144, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
}
.cta-pill-badge {
  background: rgba(0, 214, 144, 0.15);
  color: #00d690;
  border: 1px solid rgba(0, 214, 144, 0.3);
  padding: 0.3rem 0.85rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.btn-mint-glow {
  background: var(--color-mint);
  color: #111;
  border: none;
  box-shadow: 0 4px 15px var(--color-mint-glow);
  transition: all 0.25s ease;
}
.btn-mint-glow:hover {
  background: #00ffaa;
  color: #000;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 214, 144, 0.5);
}

/* Tags & Sharing */
.blog-tag-pill {
  padding: 0.35rem 0.8rem;
  border-radius: 9999px;
  background: var(--blog-card-hover);
  border: 1px solid var(--blog-border-subtle);
  color: var(--blog-text-muted);
  font-size: 0.8rem;
  transition: all 0.2s ease;
}
.blog-tag-pill:hover {
  color: var(--color-mint);
  border-color: var(--color-mint);
  background: var(--color-mint-subtle);
  transform: translateY(-1px);
}

.btn-share-circle {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--blog-bg-surface);
  border: 1px solid var(--blog-border-subtle);
  color: var(--blog-text-muted);
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 0.95rem;
  cursor: pointer;
}
.btn-share-circle:hover {
  background: var(--color-mint);
  color: #111;
  border-color: var(--color-mint);
  transform: translateY(-2px);
  box-shadow: 0 4px 10px var(--color-mint-glow);
}

/* Next & Previous Cards */
.post-nav-card {
  background: var(--blog-bg-surface);
  border: 1px solid var(--blog-border-subtle);
  transition: all 0.25s ease;
}
.post-nav-card:hover {
  border-color: var(--color-mint);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
}
.nav-direction {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--blog-text-muted);
  font-weight: 600;
}
.nav-title {
  color: var(--blog-text-main);
  font-size: 0.95rem;
}

/* Mobile Optimizations */
@media (max-width: 576px) {
  .blog-meta-strip {
    border-radius: 16px;
    padding: 0.75rem 1rem;
    width: 100%;
  }
  .blog-article-content {
    font-size: 1.08rem;
    line-height: 1.78;
  }
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
  // Reading Progress Bar
  window.addEventListener('scroll', function() {
    var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    var bar = document.getElementById("readingProgressBar");
    if (bar) {
      bar.style.width = scrolled + "%";
    }

    // Update sidebar progress widgets
    var sidebarPercent = document.getElementById("tocProgressPercent");
    var sidebarBar = document.getElementById("tocSidebarProgressBar");
    var roundedPercent = Math.min(100, Math.max(0, Math.round(scrolled)));
    if (sidebarPercent) sidebarPercent.innerText = roundedPercent + "%";
    if (sidebarBar) sidebarBar.style.width = roundedPercent + "%";
  });

  // Dynamic Table of Contents Generator with ScrollSpy
  document.addEventListener('DOMContentLoaded', function() {
    var contentArea = document.querySelector('.blog-article-content');
    var desktopToc = document.getElementById('desktopTocList');
    var mobileTocWrapper = document.getElementById('mobileTocWrapper');
    var mobileToc = document.getElementById('mobileTocList');
    var mobileCount = document.getElementById('mobileTocCount');

    if (!contentArea) return;

    var headings = contentArea.querySelectorAll('h2, h3');
    if (headings.length >= 2) {
      if (mobileTocWrapper) mobileTocWrapper.classList.remove('d-none');
      if (mobileCount) mobileCount.innerText = headings.length;

      headings.forEach(function(heading, index) {
        var id = heading.id || 'topic-' + (index + 1);
        heading.id = id;

        var isH3 = heading.tagName.toLowerCase() === 'h3';
        var cleanText = heading.innerText.trim();

        // 1. Build Desktop Link
        if (desktopToc) {
          var dLink = document.createElement('a');
          dLink.href = '#' + id;
          dLink.innerText = cleanText;
          dLink.className = isH3 ? 'toc-h3' : 'toc-h2';
          dLink.setAttribute('data-target', id);
          desktopToc.appendChild(dLink);
        }

        // 2. Build Mobile Link
        if (mobileToc) {
          var mLink = document.createElement('a');
          mLink.href = '#' + id;
          mLink.innerText = cleanText;
          mLink.className = isH3 ? 'toc-h3' : 'toc-h2';
          mLink.setAttribute('data-target', id);
          mLink.addEventListener('click', function() {
            var collapseEl = document.getElementById('mobileTocCollapse');
            if (collapseEl && window.bootstrap && window.bootstrap.Collapse) {
              var bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
              if (bsCollapse) bsCollapse.hide();
            }
          });
          mobileToc.appendChild(mLink);
        }
      });

      // Active ScrollSpy for Desktop & Mobile TOC
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            var id = entry.target.getAttribute('id');
            document.querySelectorAll('#desktopTocList a, #mobileTocList a').forEach(function(link) {
              if (link.getAttribute('data-target') === id) {
                link.classList.add('toc-active');
              } else {
                link.classList.remove('toc-active');
              }
            });
          }
        });
      }, {
        rootMargin: '-20% 0px -60% 0px',
        threshold: 0.1
      });

      headings.forEach(function(h) {
        observer.observe(h);
      });
    } else {
      var desktopWrapper = document.getElementById('stickyTocWrapper');
      if (desktopWrapper) desktopWrapper.classList.add('d-none');
    }

    // Enhance all <pre> code & prompt blocks with a Copy button
    document.querySelectorAll('.blog-article-content pre').forEach(function(preBlock) {
      if (preBlock.querySelector('.pre-copy-btn')) return;

      var copyBtn = document.createElement('button');
      copyBtn.type = 'button';
      copyBtn.className = 'pre-copy-btn';
      copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> <span>Copy</span>';

      copyBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        var codeElem = preBlock.querySelector('code');
        // Extract plain prompt/code text
        var textToCopy = (codeElem ? codeElem.innerText : preBlock.innerText);
        // Exclude the button text itself if matched
        textToCopy = textToCopy.replace(/^(Copy|Copied!)\s*/i, '').trim();

        if (navigator.clipboard) {
          navigator.clipboard.writeText(textToCopy).then(function() {
            copyBtn.innerHTML = '<i class="bi bi-check2"></i> <span>Copied!</span>';
            copyBtn.style.background = 'var(--color-mint)';
            copyBtn.style.color = '#0d1117';
            setTimeout(function() {
              copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> <span>Copy</span>';
              copyBtn.style.background = '';
              copyBtn.style.color = '';
            }, 2200);
          });
        }
      });

      preBlock.appendChild(copyBtn);
    });
  });

  // Copy Article Link Function with feedback
  function copyArticleLink(btn) {
    var url = window.location.href;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function() {
        showCopySuccess(btn);
      });
    } else {
      var dummy = document.createElement('input');
      document.body.appendChild(dummy);
      dummy.value = url;
      dummy.select();
      document.execCommand('copy');
      document.body.removeChild(dummy);
      showCopySuccess(btn);
    }
  }

  function showCopySuccess(btn) {
    if (!btn) return;
    var originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2 text-mint"></i> Copied!';
    setTimeout(function() {
      btn.innerHTML = originalHtml;
    }, 2200);
  }
</script>
@endsection
