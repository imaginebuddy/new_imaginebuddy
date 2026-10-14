@extends('layouts.app')

@php
  // Build fallback keywords list
  $allKeywords = array_unique(array_filter(array_map('trim', array_merge(
    !empty($page->meta_keywords) ? explode(',', $page->meta_keywords) : [],
    !empty($page->primary_keyword) ? [$page->primary_keyword] : [],
    !empty($page->secondary_keywords) ? explode(',', $page->secondary_keywords) : []
  ))));
  $keywordsString = implode(', ', $allKeywords);

  // Author & Publisher details for Schema & OpenGraph
  $authorName = $page->user ? ($page->user->name ?: $page->user->username) : config('settings.title', 'ImagineBuddy');
  $authorUrl = $page->user ? url($page->user->username) : url('/');
  $siteTitle = config('settings.title', 'ImagineBuddy');
  $logoUrl = url('public/img', config('settings.logo', 'logo.png'));
  $coverImageUrl = !empty($page->featured_image) ? url('public/uploads/guides/' . $page->featured_image) : $logoUrl;

  // Schema Type
  $schemaType = in_array($page->schema_type, ['Article', 'TechArticle', 'HowTo', 'BlogPosting']) ? $page->schema_type : 'Article';

  $schemaArticle = [
    '@context' => 'https://schema.org',
    '@type' => $schemaType,
    'mainEntityOfPage' => [
      '@type' => 'WebPage',
      '@id' => url('guides/' . $page->slug)
    ],
    'headline' => $page->title,
    'name' => $page->title,
    'description' => !empty($page->meta_description) ? $page->meta_description : ($page->excerpt ?: $page->title),
    'image' => [
      '@type' => 'ImageObject',
      'url' => $coverImageUrl,
      'width' => 1200,
      'height' => 675
    ],
    'datePublished' => $page->published_at ? $page->published_at->toIso8601String() : ($page->created_at ? $page->created_at->toIso8601String() : date('c')),
    'dateModified' => $page->updated_at ? $page->updated_at->toIso8601String() : date('c'),
    'author' => [
      '@type' => 'Person',
      'name' => $authorName,
      'url' => $authorUrl
    ],
    'publisher' => [
      '@type' => 'Organization',
      'name' => $siteTitle,
      'url' => url('/'),
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $logoUrl
      ]
    ]
  ];

  if (!empty($keywordsString)) {
    $schemaArticle['keywords'] = $keywordsString;
  }

  if (!empty($page->target_audience)) {
    $schemaArticle['audience'] = [
      '@type' => 'Audience',
      'audienceType' => $page->target_audience
    ];
  }

  if (!empty($page->category)) {
    $schemaArticle['articleSection'] = $page->category->name;
  }

  // Breadcrumbs Schema
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
  $bPos = 2;
  if ($page->category) {
    $schemaBreadcrumb['itemListElement'][] = [
      '@type' => 'ListItem',
      'position' => $bPos++,
      'name' => $page->category->name,
      'item' => url('category/' . $page->category->slug)
    ];
  }
  $schemaBreadcrumb['itemListElement'][] = [
    '@type' => 'ListItem',
    'position' => $bPos,
    'name' => $page->title,
    'item' => url('guides/' . $page->slug)
  ];

  $schemasToOutput = [$schemaArticle, $schemaBreadcrumb];

  // FAQ Schema if FAQ section exists
  $faqSection = $page->sections->first(function($sec) {
    return strtolower($sec->badge ?? '') === 'faq' || 
           Str::contains(strtolower($sec->heading ?? ''), ['faq', 'frequently asked']);
  });

  if ($faqSection && !empty($faqSection->content)) {
    preg_match_all('/<h[34][^>]*>(.*?)<\/h[34]>\s*<p[^>]*>(.*?)<\/p>/is', $faqSection->content, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
      $faqQuestions = [];
      foreach ($matches as $m) {
        $q = trim(strip_tags($m[1]));
        $a = trim(strip_tags($m[2]));
        if (!empty($q) && !empty($a)) {
          $faqQuestions[] = [
            '@type' => 'Question',
            'name' => $q,
            'acceptedAnswer' => [
              '@type' => 'Answer',
              'text' => $a
            ]
          ];
        }
      }
      if (!empty($faqQuestions)) {
        $schemasToOutput[] = [
          '@context' => 'https://schema.org',
          '@type' => 'FAQPage',
          'mainEntity' => $faqQuestions
        ];
      }
    }
  }
@endphp

@section('title', !empty($page->meta_title) ? $page->meta_title : ($page->title . ' - ' . config('settings.title', 'ImagineBuddy')))

@if (!empty($page->meta_description))
  @section('description_override', Helper::removeLineBreak(e($page->meta_description)))
@elseif ($page->excerpt)
  @section('description_custom', Helper::removeLineBreak(e($page->excerpt)))
@endif

@if (!empty($page->meta_keywords))
  @section('keywords_override', $page->meta_keywords)
@elseif (!empty($keywordsString))
  @section('keywords_override', $keywordsString)
@endif

@section('robots', (!empty($page->robots) ? $page->robots : 'index, follow') . (str_contains($page->robots ?? '', 'max-image-preview') ? '' : ', max-image-preview:large'))
@section('og_type', 'article')
@section('twitter_card', 'summary_large_image')
@section('og_title', !empty($page->og_title) ? $page->og_title : (!empty($page->meta_title) ? $page->meta_title : $page->title))
@section('og_description', !empty($page->og_description) ? $page->og_description : (!empty($page->meta_description) ? $page->meta_description : $page->excerpt))

@if (!empty($coverImageUrl))
  @section('og_image', $coverImageUrl)
@endif

@section('article_published_time', $page->published_at ? $page->published_at->toIso8601String() : ($page->created_at ? $page->created_at->toIso8601String() : date('c')))
@section('article_modified_time', $page->updated_at ? $page->updated_at->toIso8601String() : date('c'))
@section('article_author', $authorName)
@if ($page->category)
  @section('article_section', $page->category->name)
@endif

@section('css')
<style>
/* ==========================================================================
   Editorial Guide Reading Experience & Design Tokens (Light & Dark)
   ========================================================================== */
:root {
  --guide-bg-canvas: #fcfdfd;
  --guide-bg-surface: #ffffff;
  --guide-bg-subtle: #f8fafc;
  --guide-border-subtle: #e2e8f0;
  --guide-border-hover: #cbd5e1;
  --guide-text-main: #0f172a;
  --guide-text-body: #334155;
  --guide-text-muted: #64748b;
  --guide-mint: #00d690;
  --guide-mint-subtle: rgba(0, 214, 144, 0.1);
  --guide-mint-border: rgba(0, 214, 144, 0.25);
  --guide-card-radius: 20px;
  --guide-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
  --guide-shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.07), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --guide-card-inner-bg: #ffffff;
  --guide-img-bg: #f1f5f9;
}

[data-bs-theme="dark"] {
  --guide-bg-canvas: #0b0f17;
  --guide-bg-surface: #111827;
  --guide-bg-subtle: #1a2234;
  --guide-border-subtle: rgba(255, 255, 255, 0.09);
  --guide-border-hover: rgba(255, 255, 255, 0.18);
  --guide-text-main: #f8fafc;
  --guide-text-body: #cbd5e1;
  --guide-text-muted: #94a3b8;
  --guide-mint: #00d690;
  --guide-mint-subtle: rgba(0, 214, 144, 0.15);
  --guide-mint-border: rgba(0, 214, 144, 0.3);
  --guide-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.4);
  --guide-shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.55);
  --guide-card-inner-bg: #151d2e;
  --guide-img-bg: #1e293b;
}

html {
  scroll-behavior: smooth;
}

/* Scroll Reading Progress Bar */
.guide-progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 3px;
  background: var(--guide-mint);
  width: 0%;
  z-index: 9999;
  transition: width 0.1s ease-out;
}

/* Section scroll margin offset for fixed header navigation */
.guide-section-block,
.guide-cta-card,
.guide-prompts-showcase,
.guide-photoshoots-showcase {
  scroll-margin-top: 100px;
}

/* Page Frame - Clear fixed header */
.guide-article-wrapper {
  background-color: var(--guide-bg-canvas);
  color: var(--guide-text-body);
  min-height: 100vh;
  padding-top: 95px !important;
}
@media (max-width: 991.98px) {
  .guide-article-wrapper {
    padding-top: 85px !important;
  }
}

/* Breadcrumb Pill Styling for Guides (Show full title) */
.guide-article-wrapper .breadcrumb-pill-box .breadcrumb-item.active .badge {
  max-width: none !important;
  white-space: nowrap !important;
}

/* Category Pill */
.guide-category-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.95rem;
  border-radius: 9999px;
  background: var(--guide-mint-subtle);
  color: var(--guide-mint);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border: 1px solid var(--guide-mint-border);
  transition: all 0.2s ease;
}
.guide-category-badge:hover {
  background: var(--guide-mint);
  color: #ffffff;
  transform: translateY(-1px);
}
.category-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
}

/* Target Audience Sidebar Widget & Hero Line */
.guide-audience-sidebar-card {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: var(--guide-card-radius);
  padding: 1.25rem 1.4rem;
  box-shadow: var(--guide-shadow-sm);
  transition: border-color 0.2s ease;
}
.guide-audience-sidebar-card:hover {
  border-color: var(--guide-border-hover);
}
.guide-audience-role-pill {
  display: inline-block;
  padding: 0.25rem 0.65rem;
  border-radius: 9999px;
  background: var(--guide-bg-subtle);
  border: 1px solid var(--guide-border-subtle);
  color: var(--guide-text-body);
  font-size: 0.78rem;
  font-weight: 500;
  line-height: 1.4;
  transition: all 0.2s ease;
}
.guide-audience-role-pill:hover {
  border-color: var(--guide-mint);
  color: var(--guide-mint);
  background: var(--guide-mint-subtle);
}
/* Target Audience Hero Line */
.guide-hero-audience-line {
  font-size: 0.85rem;
  color: var(--guide-text-muted);
  line-height: 1.55;
  display: flex;
  align-items: flex-start;
  gap: 0.55rem;
  hyphens: none;
  -webkit-hyphens: none;
}
.guide-hero-audience-line .guide-audience-icon {
  font-size: 0.95rem;
  line-height: 1.55;
  color: var(--guide-mint);
  flex-shrink: 0;
  margin-top: 1px;
}
.guide-hero-audience-line .guide-audience-content {
  flex: 1;
  word-break: normal;
  overflow-wrap: break-word;
}
.guide-hero-audience-line strong {
  color: var(--guide-text-main);
  font-weight: 600;
}
.guide-hero-audience-line .guide-audience-text {
  color: var(--guide-text-muted);
}

/* Typography & Hero Banner */
.guide-hero-banner {
  margin-bottom: 2.5rem;
}
.guide-main-title {
  font-size: clamp(1.85rem, 4vw, 3.25rem);
  font-weight: 800;
  line-height: 1.18;
  letter-spacing: -0.025em;
  color: var(--guide-text-main);
  word-break: break-word;
}
.guide-lead-text {
  font-size: clamp(1rem, 1.8vw, 1.2rem);
  line-height: 1.65;
  color: var(--guide-text-muted);
  font-weight: 400;
}

/* Hero Media Frame */
.guide-hero-media-frame {
  border-radius: 20px;
  border: 1px solid var(--guide-border-subtle);
  box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  background: var(--guide-img-bg);
}
.guide-hero-img {
  width: 100%;
  height: auto;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  display: block;
}

/* Meta Bar Strip */
.guide-meta-strip {
  padding: 0.48rem 1.15rem;
  border-radius: 9999px;
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  font-size: 0.84rem;
  color: var(--guide-text-muted);
  box-shadow: var(--guide-shadow-sm);
  display: inline-flex;
  align-items: center;
  flex-wrap: nowrap;
  white-space: nowrap;
  gap: 0.65rem;
  max-width: 100%;
  overflow-x: auto;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
}
.guide-meta-strip::-webkit-scrollbar {
  display: none;
}
.guide-meta-author {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}
.guide-author-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
  border: 1.5px solid var(--guide-mint);
  flex-shrink: 0;
}
.guide-author-name {
  color: var(--guide-text-main);
  font-weight: 600;
  text-decoration: none;
}
.guide-author-name:hover {
  color: var(--guide-mint);
}
.guide-meta-dot {
  opacity: 0.3;
  color: var(--guide-text-muted);
  user-select: none;
  flex-shrink: 0;
}
.guide-meta-item {
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
  flex-shrink: 0;
}

/* Sticky Desktop Table of Contents Sidebar */
.guide-sticky-sidebar {
  position: sticky;
  top: 95px;
}
.guide-toc-card {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: var(--guide-card-radius);
  padding: 1.5rem;
  box-shadow: var(--guide-shadow-sm);
}
.guide-toc-card .badge {
  background: var(--guide-bg-subtle) !important;
  color: var(--guide-text-muted) !important;
  border-color: var(--guide-border-subtle) !important;
}
.guide-toc-nav {
  max-height: calc(100vh - 240px);
  overflow-y: auto;
  scrollbar-width: thin;
}
.guide-toc-nav::-webkit-scrollbar {
  width: 3px;
}
.guide-toc-nav::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.2);
  border-radius: 4px;
}
.guide-toc-nav a {
  display: block;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.86rem;
  color: var(--guide-text-muted);
  text-decoration: none;
  line-height: 1.45;
  transition: all 0.2s ease;
  border-left: 2px solid transparent;
  word-break: break-word;
  margin-bottom: 2px;
}
.guide-toc-nav a:hover {
  color: var(--guide-text-main);
  background: var(--guide-bg-subtle);
  transform: translateX(3px);
}
.guide-toc-nav a.toc-active {
  color: var(--guide-mint) !important;
  font-weight: 700;
  border-left-color: var(--guide-mint);
  background: var(--guide-mint-subtle);
}

/* Mobile Collapsible TOC */
.guide-mobile-toc-toggle {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: 14px;
  color: var(--guide-text-main);
  padding: 0.85rem 1.15rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}
.guide-mobile-toc-chevron {
  transition: transform 0.25s ease;
}
.guide-mobile-toc-toggle[aria-expanded="true"] .guide-mobile-toc-chevron {
  transform: rotate(180deg);
}
.guide-mobile-toc-collapse {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-top: none;
  border-radius: 0 0 14px 14px;
  padding: 1rem;
}

/* Typography Hierarchy Reset (Overrides global 800 weight) */
.guide-article-wrapper h1,
.guide-main-title {
  font-weight: 750 !important;
}
.guide-article-wrapper h2,
.guide-section-heading {
  font-weight: 700 !important;
}
.guide-article-wrapper h3,
.guide-section-body h3 {
  font-weight: 650 !important;
}
.guide-article-wrapper h4,
.guide-section-body h4 {
  font-weight: 600 !important;
}
.guide-article-wrapper h5,
.guide-section-body h5,
.guide-article-wrapper h6,
.guide-section-body h6 {
  font-weight: 600 !important;
}

/* Content Section Blocks */
.guide-section-block {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: var(--guide-card-radius);
  padding: 2.75rem 3rem;
  margin-bottom: 2.5rem;
  box-shadow: var(--guide-shadow-sm);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.guide-section-block:hover {
  border-color: var(--guide-border-hover);
}
.guide-section-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.3rem 0.85rem;
  border-radius: 9999px;
  background: var(--guide-bg-subtle);
  color: var(--guide-text-muted);
  border: 1px solid var(--guide-border-subtle);
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 1rem;
}
.guide-section-heading {
  font-size: clamp(1.4rem, 2.2vw, 1.85rem);
  color: var(--guide-text-main);
  letter-spacing: -0.02em;
  line-height: 1.3;
  margin-bottom: 0.65rem;
  word-break: break-word;
}
.guide-section-subheading {
  color: var(--guide-text-muted);
  font-size: 1.05rem;
  line-height: 1.6;
  margin-bottom: 2rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid var(--guide-border-subtle);
}

/* Rich Editorial Section Body Typography */
.guide-section-body {
  font-size: 1.06rem;
  line-height: 1.85;
  color: var(--guide-text-body);
  letter-spacing: -0.005em;
  word-break: break-word;
}
.guide-section-body p {
  margin-bottom: 1.35rem;
  line-height: 1.85;
}
.guide-section-body p:last-child {
  margin-bottom: 0;
}
.guide-section-body strong,
.guide-section-body b {
  font-weight: 650;
  color: var(--guide-text-main);
}
.guide-section-body h2 {
  font-size: 1.4rem !important;
  line-height: 1.35;
  letter-spacing: -0.015em;
  color: var(--guide-text-main);
  margin-top: 2.25rem;
  margin-bottom: 0.85rem;
}
.guide-section-body h3 {
  font-size: 1.22rem !important;
  line-height: 1.4;
  letter-spacing: -0.01em;
  color: var(--guide-text-main);
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}
.guide-section-body h4 {
  font-size: 1.08rem !important;
  line-height: 1.45;
  color: var(--guide-text-main);
  margin-top: 1.6rem;
  margin-bottom: 0.5rem;
}
.guide-section-body h5,
.guide-section-body h6 {
  font-size: 1rem !important;
  color: var(--guide-text-main);
  margin-top: 1.25rem;
  margin-bottom: 0.5rem;
}

/* Lists & Spacing */
.guide-section-body ul,
.guide-section-body ol {
  padding-left: 1.5rem;
  margin-top: 0.5rem;
  margin-bottom: 1.65rem;
}
.guide-section-body li {
  margin-bottom: 0.65rem;
  line-height: 1.75;
  color: var(--guide-text-body);
}
.guide-section-body li:last-child {
  margin-bottom: 0;
}
.guide-section-body ul > li::marker {
  color: var(--guide-mint);
}
.guide-section-body ol > li::marker {
  color: var(--guide-mint);
  font-weight: 700;
}

/* Editorial Tables (Comparison & Specifications) */
.guide-section-body .table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin: 1.85rem 0 2rem 0;
  border-radius: 14px;
  border: 1px solid var(--guide-border-subtle);
  background: var(--guide-bg-surface);
  box-shadow: var(--guide-shadow-sm);
}

.guide-section-body table {
  width: 100%;
  min-width: 580px; /* Prevents columns from squishing into vertical 1-character text */
  margin: 0;
  border-collapse: separate;
  border-spacing: 0;
  background: transparent;
  font-size: 0.94rem;
  line-height: 1.6;
  border: none;
}

/* Standalone fallback if table is not yet in .table-responsive wrapper */
.guide-section-body > table {
  display: block;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border: 1px solid var(--guide-border-subtle);
  border-radius: 14px;
  box-shadow: var(--guide-shadow-sm);
  margin: 1.85rem 0 2rem 0;
}

.guide-section-body thead {
  background: var(--guide-bg-subtle);
}
.guide-section-body th {
  background: var(--guide-bg-subtle);
  color: var(--guide-text-main);
  font-weight: 700 !important;
  font-size: 0.84rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0.95rem 1.15rem;
  border-bottom: 1px solid var(--guide-border-subtle);
  text-align: left;
  white-space: nowrap !important; /* Never allow header text to break into vertical letters */
}
.guide-section-body td {
  padding: 0.95rem 1.15rem;
  border-bottom: 1px solid var(--guide-border-subtle);
  color: var(--guide-text-body);
  vertical-align: top;
  line-height: 1.65;
  word-break: normal;
  overflow-wrap: break-word;
}
.guide-section-body table th:first-child,
.guide-section-body table td:first-child {
  min-width: 135px;
}
.guide-section-body table th:nth-child(2),
.guide-section-body table td:nth-child(2) {
  min-width: 175px;
}
.guide-section-body table th:nth-child(3),
.guide-section-body table td:nth-child(3) {
  min-width: 250px;
}
.guide-section-body tbody tr:last-child td {
  border-bottom: none;
}
.guide-section-body tbody tr {
  transition: background-color 0.15s ease;
}
.guide-section-body tbody tr:hover {
  background-color: var(--guide-bg-subtle);
}

/* Code & Pre Blocks */
.guide-section-body code {
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  background: var(--guide-mint-subtle);
  color: var(--guide-mint);
  padding: 0.2rem 0.45rem;
  border-radius: 6px;
  font-size: 0.88em;
  border: 1px solid var(--guide-mint-border);
  word-break: break-word;
}
.guide-section-body pre {
  background: var(--guide-bg-subtle);
  border: 1px solid var(--guide-border-subtle);
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
  margin: 1.5rem 0;
  overflow-x: auto;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.9rem;
  line-height: 1.65;
  color: var(--guide-text-main);
}
.guide-section-body pre code {
  background: transparent;
  color: inherit;
  padding: 0;
  border: none;
  font-size: 0.95em;
}

/* Prompt Blueprint Card Box */
.guide-prompt-blueprint-box,
.guide-section-body .guide-prompt-blueprint-box {
  background: var(--guide-bg-subtle);
  border: 1px solid var(--guide-border-subtle);
  border-left: 4px solid var(--guide-mint);
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
  margin: 1.65rem 0 2rem 0;
  position: relative;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.guide-prompt-blueprint-box:hover {
  border-color: var(--guide-mint);
  box-shadow: 0 4px 18px rgba(0, 214, 144, 0.08);
}
.guide-prompt-blueprint-box strong {
  display: block;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: var(--guide-mint);
  margin-bottom: 0.65rem;
  font-weight: 700 !important;
}
.guide-prompt-blueprint-box code {
  display: block;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.92rem;
  line-height: 1.7;
  color: var(--guide-text-main);
  background: transparent;
  padding: 0;
  border: none;
  word-break: break-word;
  user-select: all;
}

/* Callout Blockquotes */
.guide-section-body blockquote {
  border-left: 4px solid var(--guide-mint);
  background: var(--guide-bg-subtle);
  padding: 1.15rem 1.4rem;
  border-radius: 0 12px 12px 0;
  margin: 1.75rem 0;
  color: var(--guide-text-main);
  font-style: normal;
}
.guide-section-body blockquote p:last-child {
  margin-bottom: 0;
}
.guide-section-body img {
  max-width: 100%;
  height: auto;
  border-radius: 14px;
}

/* Specialized FAQ Section Styling */
.is-faq-section .guide-section-body h3 {
  font-size: 1.18rem !important;
  font-weight: 650 !important;
  color: var(--guide-text-main);
  margin-top: 2rem;
  margin-bottom: 0.75rem;
  padding-left: 0.95rem;
  border-left: 3px solid var(--guide-mint);
  line-height: 1.45;
}
.is-faq-section .guide-section-body h3:first-child {
  margin-top: 0.5rem;
}
.is-faq-section .guide-section-body p {
  padding-left: 0.95rem;
  margin-bottom: 1.85rem;
}

/* Callout CTA Box */
.guide-cta-card {
  background: linear-gradient(135deg, #09131f 0%, #111e30 100%);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--guide-card-radius);
  padding: 2.5rem;
  color: #ffffff;
  position: relative;
  overflow: hidden;
  box-shadow: var(--guide-shadow-md);
}
.guide-cta-card::after {
  content: '';
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(0, 214, 144, 0.25) 0%, rgba(0,0,0,0) 70%);
  pointer-events: none;
}

/* Related Guides */
.guide-related-card {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: 16px;
  padding: 1.25rem;
  transition: all 0.25s ease;
  height: 100%;
}
.guide-related-card:hover {
  transform: translateY(-2px);
  border-color: var(--guide-mint);
  box-shadow: var(--guide-shadow-md);
}
.guide-related-card h4 {
  color: var(--guide-text-main);
}
.guide-related-card .badge {
  background: var(--guide-bg-subtle) !important;
  color: var(--guide-text-muted) !important;
  border-color: var(--guide-border-subtle) !important;
}

/* Prompts Card Grid & Photoshoot Dark/Light Sync */
.guide-prompt-card-wrapper {
  background: var(--guide-bg-surface) !important;
  border: 1px solid var(--guide-border-subtle) !important;
  transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.guide-prompt-card-wrapper:hover {
  border-color: var(--guide-border-hover) !important;
  box-shadow: var(--guide-shadow-md) !important;
}
.guide-prompt-media-frame {
  background-color: var(--guide-img-bg) !important;
}
.guide-prompt-bottom-info {
  background: var(--guide-bg-surface) !important;
}
.guide-prompt-title {
  color: var(--guide-text-main) !important;
}
.guide-prompt-title:hover {
  color: var(--guide-mint) !important;
}
.guide-prompt-snippet {
  color: var(--guide-text-muted) !important;
}
.guide-prompt-author-pill {
  background: var(--guide-bg-subtle) !important;
  color: var(--guide-text-main) !important;
  border: 1px solid var(--guide-border-subtle) !important;
}
.guide-prompt-share-btn {
  background: var(--guide-bg-subtle) !important;
  color: var(--guide-text-muted) !important;
  border: 1px solid var(--guide-border-subtle) !important;
  transition: all 0.2s ease;
}
.guide-prompt-share-btn:hover {
  color: var(--guide-mint) !important;
  border-color: var(--guide-mint) !important;
}
.guide-prompt-copy-btn {
  background: #111827 !important;
  color: #ffffff !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
[data-bs-theme="dark"] .guide-prompt-copy-btn {
  background: var(--guide-mint) !important;
  color: #0b0f17 !important;
  border-color: transparent !important;
}
.guide-prompt-copy-btn:hover {
  filter: brightness(1.1);
}
.guide-prompt-heart-badge {
  background: var(--guide-bg-surface) !important;
  border: 1px solid var(--guide-border-subtle) !important;
  color: var(--guide-text-main) !important;
/* Related Topics Tag Cloud */
.guide-topics-cloud-card {
  background: var(--guide-bg-surface);
  border: 1px solid var(--guide-border-subtle);
  border-radius: var(--guide-card-radius);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.guide-topics-cloud-card:hover {
  border-color: var(--guide-border-hover);
}
.guide-topic-pill {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.95rem;
  border-radius: 9999px;
  background: var(--guide-bg-subtle);
  border: 1px solid var(--guide-border-subtle);
  color: var(--guide-text-body);
  font-size: 0.86rem;
  font-weight: 500;
  transition: all 0.2s ease;
}
.guide-topic-pill:hover {
  color: var(--guide-mint);
  border-color: var(--guide-mint);
  background: var(--guide-mint-subtle);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 214, 144, 0.12);
}

/* Mobile & Tablet Responsive Optimizations */
@media (max-width: 991.98px) {
  .guide-hero-banner {
    margin-bottom: 2rem;
  }
  .guide-meta-strip {
    padding: 0.44rem 0.95rem;
    font-size: 0.81rem;
    gap: 0.55rem;
    max-width: 100%;
    border-radius: 9999px;
  }
  .guide-section-block {
    padding: 1.85rem 1.6rem;
    border-radius: 18px;
    margin-bottom: 1.85rem;
  }
  .guide-cta-card {
    padding: 2rem 1.6rem;
    border-radius: 18px;
  }
}

@media (max-width: 575.98px) {
  .guide-article-wrapper {
    padding-top: 80px !important;
  }
  .guide-hero-img {
    aspect-ratio: 16 / 9;
    height: auto;
  }
  .guide-meta-strip {
    font-size: 0.77rem;
    padding: 0.42rem 0.85rem;
    gap: 0.5rem;
    max-width: 100%;
    border-radius: 9999px;
    flex-wrap: nowrap;
    white-space: nowrap;
  }
  .guide-author-avatar {
    width: 24px;
    height: 24px;
  }
  .guide-meta-author {
    gap: 0.4rem;
  }
  .guide-meta-dot {
    font-size: 0.75rem;
  }
  .guide-hero-audience-line {
    font-size: 0.81rem;
    line-height: 1.5;
    gap: 0.45rem;
    margin-top: 0.75rem !important;
    hyphens: none;
    -webkit-hyphens: none;
  }
  .guide-hero-audience-line .guide-audience-icon {
    font-size: 0.92rem;
    margin-top: 2px;
  }
  .guide-section-block {
    padding: 1.35rem 1.15rem;
    border-radius: 16px;
    margin-bottom: 1.35rem;
  }
  .guide-section-heading {
    font-size: 1.28rem;
  }
  .guide-section-subheading {
    font-size: 0.95rem;
    margin-bottom: 1.35rem;
    padding-bottom: 1rem;
  }
  .guide-section-body {
    font-size: 1rem;
    line-height: 1.75;
  }
  .guide-section-body h3 {
    font-size: 1.14rem !important;
    margin-top: 1.6rem;
    margin-bottom: 0.6rem;
  }
  .guide-section-body .table-responsive {
    margin: 1.25rem 0 1.5rem 0;
    border-radius: 12px;
  }
  .guide-section-body table th,
  .guide-section-body table td {
    padding: 0.75rem 0.85rem;
    font-size: 0.85rem;
  }
  .guide-prompt-blueprint-box {
    padding: 1rem 1.15rem;
    margin: 1.25rem 0 1.5rem 0;
  }
  .guide-prompt-media-frame {
    min-height: 290px !important;
  }
}
</style>
@endsection

@section('content')
{{-- Google SEO Schema.org JSON-LD (Article, Breadcrumbs, FAQ & Audience) --}}
<script type="application/ld+json">
{!! json_encode($schemasToOutput, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

<!-- Scroll Reading Progress Bar -->
<div id="guideProgressBar" class="guide-progress-bar"></div>

@if (isset($isPreview) && $isPreview)
<div class="alert alert-warning border-0 rounded-0 text-center py-2 mb-0 sticky-top" style="z-index: 1040; top: 70px;">
  <i class="bi bi-eye-fill me-1"></i> <strong>Draft Preview Mode:</strong> This landing page is an unpublished draft and visible only to administrators.
</div>
@endif

<article class="guide-article-wrapper pb-5">
  <div class="container px-3 px-md-4">

    <!-- Top Breadcrumb Navigation -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <nav aria-label="breadcrumb" style="max-width: 100%;">
        <div class="breadcrumb-pill-box rounded-pill shadow-sm border px-3 px-md-4 py-2 d-inline-flex align-items-center">
          <ol class="breadcrumb mb-0 align-items-center list-unstyled">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item d-none d-sm-inline-block"><a href="{{ url('categories') }}" class="text-decoration-none">Categories</a></li>
            @if ($page->category)
              <li class="breadcrumb-item"><a href="{{ url('category', $page->category->slug) }}" class="text-decoration-none">{{ $page->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
              <span class="badge bg-custom-mint text-white rounded-pill px-3 py-2 fw-bold" title="{{ $page->title }}" style="font-size: 0.85rem; letter-spacing: -0.2px;">{{ $page->title }}</span>
            </li>
          </ol>
        </div>
      </nav>
    </div>

    <!-- Editorial Header Section -->
    <header class="guide-hero-banner mb-4 mb-lg-5">
      <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-7">
          @if ($page->category)
            <div class="mb-3">
              <a href="{{ url('category', $page->category->slug) }}" class="guide-category-badge text-decoration-none">
                <span class="category-dot"></span>
                {{ $page->category->name }}
              </a>
            </div>
          @endif

          <h1 class="guide-main-title mb-3">
            {{ $page->title }}
          </h1>

          @if ($page->excerpt)
            <p class="guide-lead-text mb-4">
              {{ $page->excerpt }}
            </p>
          @endif

          <!-- Refined Metadata Strip -->
          <div class="guide-meta-strip">
            @if ($page->user)
              <div class="guide-meta-author">
                @if ($page->user->avatar)
                  <img src="{{ Storage::url(config('path.avatar') . $page->user->avatar) }}" alt="{{ $page->user->username }}" class="guide-author-avatar">
                @else
                  <div class="guide-author-avatar bg-dark text-white d-flex align-items-center justify-content-center" style="font-size: 0.75rem;">
                    <i class="bi bi-person-fill"></i>
                  </div>
                @endif
                <a href="{{ url($page->user->username) }}" class="guide-author-name">
                  {{ $page->user->name ?: $page->user->username }}
                </a>
                <i class="bi bi-patch-check-fill text-mint" title="Verified Author"></i>
              </div>
              <span class="guide-meta-dot">&bull;</span>
            @endif

            <span class="guide-meta-item" title="Last updated">
              <i class="bi bi-calendar3 me-1"></i>
              {{ $page->published_at ? $page->published_at->format('M d, Y') : now()->format('M d, Y') }}
            </span>

            @if (isset($readingTime))
              <span class="guide-meta-dot">&bull;</span>
              <span class="guide-meta-item">
                <i class="bi bi-clock me-1 text-mint"></i>
                {{ $readingTime }} min read
              </span>
            @endif

            @if ($prompts->count() > 0)
              <span class="guide-meta-dot">&bull;</span>
              <span class="guide-meta-item">
                <i class="bi bi-collection me-1 text-mint"></i>
                {{ $prompts->count() }} {{ $prompts->count() == 1 ? 'Prompt' : 'Prompts' }}
              </span>
            @endif
          </div>

          @if (!empty($page->target_audience))
            <div class="guide-hero-audience-line mt-3">
              <i class="bi bi-bullseye text-mint guide-audience-icon"></i>
              <div class="guide-audience-content">
                <strong>Curated for:</strong>
                <span class="guide-audience-text">{{ $page->target_audience }}</span>
              </div>
            </div>
          @endif
        </div>

        @if (!empty($page->featured_image))
          <div class="col-lg-5">
            <div class="guide-hero-media-frame">
              <img src="{{ url('public/uploads/guides/' . $page->featured_image) }}" alt="{{ $page->featured_image_alt ?: $page->title }}" class="guide-hero-img" width="1200" height="675" fetchpriority="high" decoding="async">
            </div>
          </div>
        @endif
      </div>
    </header>

    <!-- Main Content Grid: Sticky Sidebar + Reading Flow -->
    <div class="row g-4 g-lg-5">

      <!-- Left Column: Desktop Sticky Table of Contents (>= 992px) -->
      @if ($page->enable_table_of_contents)
      <aside class="col-lg-4 col-xl-3 d-none d-lg-block">
        <div class="guide-sticky-sidebar" id="guideStickySidebar">
          <div class="guide-toc-card">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle">
              <span class="text-uppercase fw-bold small text-muted" style="letter-spacing: 0.8px; font-size: 0.75rem;">
                <i class="bi bi-list-nested text-mint me-2"></i>On This Page
              </span>
              <span class="badge rounded-pill bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">Guide Index</span>
            </div>

            <!-- Dynamic Table of Contents Links -->
            <nav id="desktopGuideToc" class="guide-toc-nav"></nav>

            <!-- Quick Action: Copy Guide Link -->
            <div class="pt-3 mt-3 border-top border-light-subtle">
              <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 d-flex align-items-center justify-content-center gap-2" onclick="copyGuideLink(this)">
                <i class="bi bi-link-45deg fs-6"></i>
                <span class="copy-label">Share This Guide</span>
              </button>
            </div>
          </div>
        </div>
      </aside>
      @endif

      <!-- Right Column: Primary Reading Canvas & Dynamic Content Sections -->
      <div class="col-12 {{ $page->enable_table_of_contents ? 'col-lg-8 col-xl-9' : 'col-lg-12' }}">

        <!-- Mobile / Tablet Collapsible Table of Contents (< 992px) -->
        @if ($page->enable_table_of_contents)
        <div id="mobileGuideTocWrapper" class="d-lg-none mb-4">
          <button class="guide-mobile-toc-toggle shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mobileGuideTocCollapse" aria-expanded="false" aria-controls="mobileGuideTocCollapse">
            <span class="d-flex align-items-center gap-2">
              <i class="bi bi-list-nested text-mint fs-5"></i>
              <span>Table of Contents</span>
            </span>
            <i class="bi bi-chevron-down guide-mobile-toc-chevron"></i>
          </button>
          <div class="collapse" id="mobileGuideTocCollapse">
            <div class="guide-mobile-toc-collapse shadow-sm">
              <nav id="mobileGuideToc" class="guide-toc-nav"></nav>
            </div>
          </div>
        </div>
        @endif

        @php
          $promptsRendered = false;
        @endphp

        <!-- Dynamic Editorial Sections Loop -->
        <div class="guide-content-stream" id="guideContentStream">
          @foreach ($page->sections as $section)
            @php
              $isFaqSection = (strtolower($section->badge ?? '') === 'faq' || 
                               Str::contains(strtolower($section->heading ?? ''), 'faq') || 
                               Str::contains(strtolower($section->heading ?? ''), 'frequently asked'));
            @endphp

            {{-- Render Curated Prompts Showcase directly BEFORE the FAQ section --}}
            @if ($isFaqSection && !$promptsRendered && $prompts->count() > 0)
              @include('guides.prompts-showcase', ['prompts' => $prompts])
              @php $promptsRendered = true; @endphp
            @endif

            @if ($section->type === 'cta_banner')
              <!-- Premium CTA Banner Block -->
              <div class="guide-cta-card mb-4" id="section-{{ $section->id }}" data-heading="{{ $section->heading ?: 'Call to Action' }}">
                <div class="position-relative" style="z-index: 2;">
                  @if ($section->badge)
                    <span class="badge bg-custom-mint text-white rounded-pill px-3 py-1 mb-2 fw-semibold small">
                      {{ $section->badge }}
                    </span>
                  @endif
                  <h3 class="h4 fw-bold text-white mb-2">{{ $section->heading ?: 'Explore More Prompts' }}</h3>
                  @if ($section->subheading)
                    <p class="text-white-50 mb-3">{{ $section->subheading }}</p>
                  @endif
                  @if ($section->content)
                    <div class="text-light opacity-90 small mb-4 lh-base">
                      {!! $section->content !!}
                    </div>
                  @endif
                  <a href="{{ url('photoshoots') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-dark shadow-sm">
                    Browse Prompt Library <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            @elseif ($section->type === 'prompt_grid')
              {{-- Explicit prompt grid section --}}
              @if (!$promptsRendered && $prompts->count() > 0)
                @include('guides.prompts-showcase', ['prompts' => $prompts])
                @php $promptsRendered = true; @endphp
              @endif
            @else
              <!-- Standard Clean Section Card -->
              <section class="guide-section-block {{ $isFaqSection ? 'is-faq-section' : '' }}" id="section-{{ $section->id }}" data-heading="{{ $section->heading ?: 'Section' }}">
                @if ($section->badge)
                  <span class="guide-section-badge">
                    <i class="bi bi-tag-fill me-1" style="font-size: 0.65rem;"></i>{{ $section->badge }}
                  </span>
                @endif

                @if ($section->heading)
                  <h2 class="guide-section-heading">{{ $section->heading }}</h2>
                @endif

                @if ($section->subheading)
                  <p class="guide-section-subheading">{{ $section->subheading }}</p>
                @endif

                @if ($section->content)
                  @php
                    $sectionContent = $section->content;
                    if (stripos($sectionContent, '<table') !== false && stripos($sectionContent, 'table-responsive') === false) {
                      $sectionContent = preg_replace('/(<table[^>]*>.*?<\/table>)/is', '<div class="table-responsive my-3">$1</div>', $sectionContent);
                    }
                  @endphp
                  <div class="guide-section-body">
                    {!! $sectionContent !!}
                  </div>
                @endif
              </section>
            @endif
          @endforeach

          {{-- If no FAQ section was present, render prompts showcase here before photoshoots --}}
          @if (!$promptsRendered && $prompts->count() > 0)
            @include('guides.prompts-showcase', ['prompts' => $prompts])
            @php $promptsRendered = true; @endphp
          @endif
        </div>

        {{-- Related Topics & Search Keywords Tag Cloud --}}
        @if (!empty($page->secondary_keywords) || !empty($page->primary_keyword))
          @php
            $rawKeywords = array_merge(
              !empty($page->primary_keyword) ? [$page->primary_keyword] : [],
              !empty($page->secondary_keywords) ? explode(',', $page->secondary_keywords) : []
            );
            $cleanKeywords = array_unique(array_filter(array_map('trim', $rawKeywords)));
          @endphp
          @if (count($cleanKeywords) > 0)
            <div class="guide-topics-cloud-card mb-5 p-4 rounded-4 shadow-sm">
              <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-tags-fill text-mint fs-5"></i>
                <h3 class="h6 fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; color: var(--guide-text-main);">
                  Related Topics & Search Keywords
                </h3>
              </div>
              <div class="d-flex flex-wrap gap-2">
                @foreach ($cleanKeywords as $kw)
                  <a href="{{ url('search') }}?q={{ urlencode($kw) }}" class="guide-topic-pill text-decoration-none" title="Search for {{ $kw }}">
                    <span class="text-mint me-1">#</span>{{ $kw }}
                  </a>
                @endforeach
              </div>
            </div>
          @endif
        @endif

        <!-- Connected Photoshoots Section -->
        @if ($photoshoots->count() > 0)
          <section class="guide-photoshoots-showcase my-5" id="photoshoots-showcase" data-heading="More Product Photoshoot Concepts">
            <div class="mb-4">
              <span class="badge bg-custom-mint text-white rounded-pill px-3 py-1 mb-2 fw-semibold">Multi-Angle Sets</span>
              <h2 class="h3 fw-bold mb-0" style="color: var(--guide-text-main);">More Product Photoshoot Concepts</h2>
            </div>
            <div class="row g-4">
              @foreach ($photoshoots as $photoshoot)
                @include('includes.photoshoot-card', ['photoshoot' => $photoshoot])
              @endforeach
            </div>
          </section>
        @endif

        <!-- Related Guides in Same Category -->
        @if ($relatedGuides->count() > 0)
          <section class="related-guides-section pt-5 mt-5 border-top border-light-subtle">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <h3 class="h4 fw-bold mb-0" style="color: var(--guide-text-main);">More Photography Guides</h3>
              <a href="{{ url('photoshoots') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                All Collections <i class="bi bi-chevron-right ms-1"></i>
              </a>
            </div>
            <div class="row g-3">
              @foreach ($relatedGuides as $rg)
                <div class="col-md-4">
                  <a href="{{ url('guides', $rg->slug) }}" class="text-decoration-none">
                    <div class="guide-related-card">
                      <span class="badge bg-light text-muted border rounded-pill mb-2 small">{{ $rg->category ? $rg->category->name : 'Guide' }}</span>
                      <h4 class="h6 fw-bold mb-2" style="color: var(--guide-text-main);">{{ $rg->title }}</h4>
                      <p class="text-muted small mb-0 line-clamp-2">{{ Str::limit($rg->excerpt, 90) }}</p>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          </section>
        @endif

      </div>
    </div>

  </div>
</article>
@endsection

@section('javascript')
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Reading Progress Bar Tracker
    var progressBar = document.getElementById('guideProgressBar');
    window.addEventListener('scroll', function() {
      var docElem = document.documentElement;
      var docBody = document.body;
      var scrollTop = docElem['scrollTop'] || docBody['scrollTop'];
      var scrollBottom = (docElem['scrollHeight'] || docBody['scrollHeight']) - window.innerHeight;
      var scrollPercent = scrollBottom > 0 ? (scrollTop / scrollBottom) * 100 : 0;
      if (progressBar) {
        progressBar.style.width = Math.min(100, Math.max(0, scrollPercent)) + '%';
      }
    });

    // 2. Build Dynamic Table of Contents from Page Sections & Showcases
    var desktopToc = document.getElementById('desktopGuideToc');
    var mobileToc = document.getElementById('mobileGuideToc');

    if (desktopToc || mobileToc) {
      var sectionElements = document.querySelectorAll('.guide-section-block, .guide-cta-card, .guide-prompts-showcase, .guide-photoshoots-showcase');
      var validHeadings = [];

      sectionElements.forEach(function(sec, index) {
        var headingText = sec.getAttribute('data-heading');
        if (!headingText) {
          var h2 = sec.querySelector('h2, h3');
          if (h2) headingText = h2.innerText.trim();
        }

        if (headingText && headingText.length > 2) {
          var secId = sec.getAttribute('id') || ('guide-sec-' + index);
          sec.setAttribute('id', secId);
          validHeadings.push({ id: secId, title: headingText, el: sec });
        }
      });

      if (validHeadings.length > 0) {
        validHeadings.forEach(function(item) {
          // Click handler function with offset scroll
          function scrollToSection(e, targetId) {
            e.preventDefault();
            var targetEl = document.getElementById(targetId);
            if (targetEl) {
              var headerOffset = 95;
              var elementPosition = targetEl.getBoundingClientRect().top;
              var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
              window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
              });
              history.pushState(null, null, '#' + targetId);
            }
          }

          // Desktop Link
          if (desktopToc) {
            var dLink = document.createElement('a');
            dLink.href = '#' + item.id;
            dLink.innerText = item.title;
            dLink.setAttribute('data-target', item.id);
            dLink.addEventListener('click', function(e) {
              scrollToSection(e, item.id);
            });
            desktopToc.appendChild(dLink);
          }

          // Mobile Link
          if (mobileToc) {
            var mLink = document.createElement('a');
            mLink.href = '#' + item.id;
            mLink.innerText = item.title;
            mLink.setAttribute('data-target', item.id);
            mLink.addEventListener('click', function(e) {
              scrollToSection(e, item.id);
              var collapseEl = document.getElementById('mobileGuideTocCollapse');
              if (collapseEl && window.bootstrap && window.bootstrap.Collapse) {
                var bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
                if (bsCollapse) bsCollapse.hide();
              }
            });
            mobileToc.appendChild(mLink);
          }
        });

        // Active Intersection Observer for ScrollSpy
        var observer = new IntersectionObserver(function(entries) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              var activeId = entry.target.getAttribute('id');
              document.querySelectorAll('#desktopGuideToc a, #mobileGuideToc a').forEach(function(link) {
                if (link.getAttribute('data-target') === activeId) {
                  link.classList.add('toc-active');
                } else {
                  link.classList.remove('toc-active');
                }
              });
            }
          });
        }, {
          rootMargin: '-15% 0px -65% 0px',
          threshold: 0.1
        });

        validHeadings.forEach(function(item) {
          observer.observe(item.el);
        });
      } else {
        var sidebar = document.getElementById('guideStickySidebar');
        if (sidebar) sidebar.style.display = 'none';
        var mobileTocWrap = document.getElementById('mobileGuideTocWrapper');
        if (mobileTocWrap) mobileTocWrap.style.display = 'none';
      }
    }

    // 3. Ensure Responsive Wrapper for all Editorial Tables
    document.querySelectorAll('.guide-section-body table').forEach(function(table) {
      if (!table.parentElement.classList.contains('table-responsive')) {
        var wrapper = document.createElement('div');
        wrapper.className = 'table-responsive my-3';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
      }
    });

    // 4. One-Click Copy for Adaptive Prompt Blueprint Boxes
    document.querySelectorAll('.guide-prompt-blueprint-box').forEach(function(box) {
      if (box.querySelector('.btn-copy-blueprint')) return;
      var codeEl = box.querySelector('code');
      if (!codeEl) return;

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-sm btn-outline-secondary btn-copy-blueprint position-absolute top-0 end-0 m-2 rounded-pill px-2 py-1 shadow-none';
      btn.style.fontSize = '0.72rem';
      btn.style.zIndex = '3';
      btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy';
      btn.title = 'Copy Prompt Blueprint';
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var promptText = codeEl.innerText.trim();
        if (navigator.clipboard) {
          navigator.clipboard.writeText(promptText).then(function() {
            btn.innerHTML = '<i class="bi bi-check2 text-success me-1"></i>Copied!';
            btn.classList.add('btn-success', 'text-white');
            btn.classList.remove('btn-outline-secondary');
            setTimeout(function() {
              btn.innerHTML = '<i class="bi bi-copy me-1"></i>Copy';
              btn.classList.remove('btn-success', 'text-white');
              btn.classList.add('btn-outline-secondary');
            }, 2000);
          });
        }
      });
      box.appendChild(btn);
    });
  });

  // Copy Guide Link Function
  function copyGuideLink(btn) {
    var url = window.location.href;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(function() {
        showCopyFeedback(btn);
      });
    } else {
      var dummy = document.createElement('input');
      document.body.appendChild(dummy);
      dummy.value = url;
      dummy.select();
      document.execCommand('copy');
      document.body.removeChild(dummy);
      showCopyFeedback(btn);
    }
  }

  function showCopyFeedback(btn) {
    var label = btn.querySelector('.copy-label') || btn;
    var originalText = label.innerText;
    label.innerText = 'Link Copied!';
    btn.classList.add('btn-success', 'text-white');
    btn.classList.remove('btn-outline-secondary');
    setTimeout(function() {
      label.innerText = originalText;
      btn.classList.remove('btn-success', 'text-white');
      btn.classList.add('btn-outline-secondary');
    }, 2000);
  }
</script>
@endsection
