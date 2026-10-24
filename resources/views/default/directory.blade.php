@extends('layouts.app')

@section('title', 'ImagineBuddy Directory — AI Prompts, Product Photography & Tools')
@section('description_override', 'Explore the ImagineBuddy directory to discover thousands of AI product image prompts, commercial product photography collections, creative styles, free tools, and practical resources.')

@section('css')
<style>
  /* ==========================================================================
     ImagineBuddy Directory — Design Tokens & Theme Variables
     ========================================================================== */
  :root {
    --dir-accent: #00d690;
    --dir-accent-hover: #00be80;
    --dir-accent-subtle: rgba(0, 214, 144, 0.08);
    --dir-accent-border: rgba(0, 214, 144, 0.25);
    
    /* Light Theme (Default) */
    --dir-page-bg: #f8fafc;
    --dir-surface: #ffffff;
    --dir-surface-hover: #ffffff;
    --dir-surface-muted: #f1f5f9;
    --dir-border: #e2e8f0;
    --dir-border-hover: rgba(0, 214, 144, 0.4);
    --dir-text-heading: #0f172a;
    --dir-text-body: #334155;
    --dir-text-muted: #64748b;
    --dir-link: #334155;
    --dir-link-hover: #00a86b;
    --dir-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
    --dir-shadow-md: 0 4px 14px rgba(15, 23, 42, 0.06);
    --dir-shadow-hover: 0 10px 25px -3px rgba(0, 214, 144, 0.12), 0 4px 6px -4px rgba(15, 23, 42, 0.05);
    --dir-sticky-bg: rgba(255, 255, 255, 0.94);
  }

  [data-bs-theme="dark"] {
    --dir-page-bg: #0b0f17;
    --dir-surface: #141a24;
    --dir-surface-hover: #19202d;
    --dir-surface-muted: rgba(255, 255, 255, 0.04);
    --dir-border: rgba(255, 255, 255, 0.08);
    --dir-border-hover: rgba(0, 214, 144, 0.45);
    --dir-text-heading: #f8fafc;
    --dir-text-body: #cbd5e1;
    --dir-text-muted: #94a3b8;
    --dir-link: #cbd5e1;
    --dir-link-hover: #00d690;
    --dir-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --dir-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.4);
    --dir-shadow-hover: 0 12px 30px rgba(0, 214, 144, 0.16), 0 4px 8px rgba(0, 0, 0, 0.3);
    --dir-sticky-bg: rgba(14, 19, 28, 0.94);
  }

  /* Page Wrapper */
  .directory-page {
    background-color: var(--dir-page-bg);
    min-height: 100vh;
    color: var(--dir-text-body);
    transition: background-color 0.25s ease, color 0.25s ease;
  }

  /* Hero Section */
  .directory-hero {
    position: relative;
    padding-top: 110px;
    padding-bottom: 45px;
    background: radial-gradient(circle at 50% 0%, rgba(0, 214, 144, 0.12) 0%, rgba(0, 214, 144, 0.01) 60%, transparent 100%);
    border-bottom: 1px solid var(--dir-border);
    overflow: hidden;
  }

  .directory-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background-image: radial-gradient(var(--dir-border) 1px, transparent 1px);
    background-size: 32px 32px;
    opacity: 0.4;
    pointer-events: none;
  }

  .dir-hero-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 1rem;
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    background: var(--dir-accent-subtle);
    border: 1px solid var(--dir-accent-border);
    color: var(--dir-accent);
    margin-bottom: 1.25rem;
  }

  .dir-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: var(--dir-accent);
    box-shadow: 0 0 0 3px rgba(0, 214, 144, 0.25);
    animation: dirPulse 2s infinite;
  }

  @keyframes dirPulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 214, 144, 0.5); }
    70% { box-shadow: 0 0 0 7px rgba(0, 214, 144, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 214, 144, 0); }
  }

  .directory-hero h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    letter-spacing: -0.025em;
    line-height: 1.15;
    color: var(--dir-text-heading);
    margin-bottom: 1rem;
  }

  .directory-hero p.lead {
    font-size: clamp(1rem, 1.5vw, 1.125rem);
    line-height: 1.6;
    color: var(--dir-text-muted);
    max-width: 740px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 2rem;
  }

  /* Metric Stat Chips */
  .dir-stats-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.65rem 1rem;
    margin-bottom: 2rem;
  }

  .dir-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    font-size: 0.82rem;
    color: var(--dir-text-muted);
    box-shadow: var(--dir-shadow-sm);
  }

  .dir-stat-chip strong {
    color: var(--dir-text-heading);
  }

  /* Search Container */
  .dir-search-box {
    position: relative;
    max-width: 680px;
    margin: 0 auto;
  }

  .dir-search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--dir-surface);
    border: 1.5px solid var(--dir-border);
    border-radius: 9999px;
    padding: 0.4rem 0.6rem 0.4rem 1.25rem;
    box-shadow: var(--dir-shadow-md);
    transition: all 0.2s ease;
  }

  .dir-search-wrapper:focus-within {
    border-color: var(--dir-accent);
    box-shadow: 0 0 0 4px rgba(0, 214, 144, 0.18), var(--dir-shadow-md);
  }

  .dir-search-icon {
    font-size: 1.2rem;
    color: var(--dir-text-muted);
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
  }

  .dir-search-input {
    flex: 1;
    border: none;
    background: transparent;
    color: var(--dir-text-heading);
    font-size: 1.05rem;
    padding: 0.6rem 0;
    outline: none !important;
    width: 100%;
  }

  .dir-search-input::placeholder {
    color: var(--dir-text-muted);
    font-size: 0.95rem;
  }

  .dir-kbd-badge {
    font-size: 0.75rem;
    font-family: inherit;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    background: var(--dir-surface-muted);
    color: var(--dir-text-muted);
    border: 1px solid var(--dir-border);
    margin-right: 0.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
  }

  .dir-search-clear {
    border: none;
    background: transparent;
    color: var(--dir-text-muted);
    padding: 0.4rem;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s ease;
  }

  .dir-search-clear:hover {
    color: var(--dir-accent);
  }

  /* Quick Suggestion Tags */
  .dir-quick-tags {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 1rem;
    font-size: 0.8rem;
    color: var(--dir-text-muted);
  }

  .dir-quick-btn {
    border: none;
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    color: var(--dir-text-muted);
    border-radius: 9999px;
    padding: 0.2rem 0.65rem;
    font-size: 0.78rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .dir-quick-btn:hover {
    background: var(--dir-accent-subtle);
    border-color: var(--dir-accent-border);
    color: var(--dir-accent);
    transform: translateY(-1px);
  }

  /* Sticky Navigation Bar */
  .dir-sticky-nav {
    position: sticky;
    top: 70px;
    z-index: 95;
    background: var(--dir-surface);
    background: var(--dir-sticky-bg);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--dir-border);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
  }

  .dir-nav-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
  }

  .dir-nav-scroller {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    overflow-x: auto;
    padding: 0.65rem 0.5rem 0.65rem 0;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    width: 100%;
  }

  .dir-nav-scroller::-webkit-scrollbar {
    display: none;
  }

  .dir-nav-btn,
  .dir-nav-btn:link,
  .dir-nav-btn:visited {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.38rem 0.72rem;
    border-radius: 9999px;
    font-size: 0.83rem;
    font-weight: 600;
    color: var(--dir-text-muted) !important;
    text-decoration: none !important;
    white-space: nowrap;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    cursor: pointer;
    flex-shrink: 0;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
  }

  .dir-nav-btn i {
    color: inherit !important;
  }

  .dir-nav-btn:hover {
    color: var(--dir-text-heading) !important;
    background: var(--dir-surface-muted) !important;
    text-decoration: none !important;
  }

  .dir-nav-btn:focus,
  .dir-nav-btn:focus-visible,
  .dir-nav-btn:active {
    color: var(--dir-text-muted) !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
  }

  .dir-nav-btn.active,
  .dir-nav-btn.active:link,
  .dir-nav-btn.active:visited,
  .dir-nav-btn.active:hover,
  .dir-nav-btn.active:focus,
  .dir-nav-btn.active:focus-visible {
    color: #ffffff !important;
    background: var(--dir-accent) !important;
    box-shadow: 0 2px 10px rgba(0, 214, 144, 0.35) !important;
    border-color: transparent !important;
  }

  .dir-nav-btn.active i {
    color: #ffffff !important;
  }

  .dir-nav-count {
    font-size: 0.72rem;
    padding: 0.1rem 0.42rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
  }

  .dir-nav-btn.active .dir-nav-count {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }

  .dir-nav-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    color: var(--dir-text-heading);
    box-shadow: var(--dir-shadow-md);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .dir-nav-arrow:hover {
    background: var(--dir-accent);
    color: #ffffff;
    border-color: var(--dir-accent);
  }

  .dir-nav-arrow-left {
    left: -10px;
  }

  .dir-nav-arrow-right {
    right: -10px;
  }

  @media (min-width: 1080px) {
    .dir-nav-wrapper {
      justify-content: center;
    }
    .dir-nav-scroller {
      justify-content: center;
    }
    .dir-nav-arrow {
      display: none !important;
    }
  }

  @media (max-width: 1079px) {
    .dir-nav-wrapper {
      justify-content: flex-start;
    }
    .dir-nav-scroller {
      justify-content: flex-start;
      padding-right: 2.5rem;
    }
  }

  /* Main Directory Section Styles */
  .dir-section {
    padding-top: 4rem;
    padding-bottom: 2rem;
    scroll-margin-top: 155px;
  }

  .dir-section-header {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 2rem;
  }

  .dir-section-pretitle {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dir-accent);
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .dir-section-title {
    font-size: clamp(1.4rem, 2.5vw, 1.85rem);
    font-weight: 800;
    color: var(--dir-text-heading);
    letter-spacing: -0.02em;
    margin: 0;
  }

  .dir-section-desc {
    font-size: 0.96rem;
    color: var(--dir-text-muted);
    max-width: 720px;
    margin: 0;
  }

  /* Cards Grid */
  .dir-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }

  .dir-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
  }

  @media (max-width: 1200px) {
    .dir-grid-4 {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 992px) {
    .dir-grid-3,
    .dir-grid-4 {
      grid-template-columns: repeat(2, 1fr);
      gap: 1.25rem;
    }
  }

  @media (max-width: 640px) {
    .dir-grid-3,
    .dir-grid-4 {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    .dir-section {
      padding-top: 2.75rem;
    }
    .directory-hero {
      padding-top: 95px;
      padding-bottom: 30px;
    }
  }

  /* Directory Card Component */
  .dir-card {
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    border-radius: 16px;
    padding: 1.35rem 1.35rem 1.15rem;
    box-shadow: var(--dir-shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), 
                border-color 0.2s ease;
    height: 100%;
    position: relative;
  }

  .dir-card:hover {
    transform: translateY(-3px);
    border-color: var(--dir-border-hover);
    box-shadow: var(--dir-shadow-hover);
  }

  .dir-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding-bottom: 0.9rem;
    margin-bottom: 0.9rem;
    border-bottom: 1px solid var(--dir-border);
  }

  .dir-card-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--dir-accent-subtle);
    color: var(--dir-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }

  .dir-card-title {
    font-size: 1.02rem;
    font-weight: 700;
    color: var(--dir-text-heading);
    margin: 0;
    line-height: 1.25;
    flex: 1;
  }

  .dir-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .dir-card-title a:hover {
    color: var(--dir-accent);
  }

  .dir-badge-pill {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    background: var(--dir-surface-muted);
    color: var(--dir-text-muted);
    border: 1px solid var(--dir-border);
    white-space: nowrap;
  }

  /* List items - Explicitly remove all bullets/dots across all browsers and global styles */
  .directory-page ul,
  .directory-page ol,
  .directory-page li,
  .dir-card ul,
  .dir-card li,
  .dir-link-list,
  .dir-link-item,
  ul.dir-link-list,
  ul.dir-link-list > li {
    list-style: none !important;
    list-style-type: none !important;
    padding-left: 0 !important;
    margin-left: 0 !important;
  }

  .directory-page li::marker,
  .dir-card li::marker,
  .dir-link-list li::marker,
  .dir-link-item::marker,
  ul.dir-link-list > li::marker {
    display: none !important;
    content: "" !important;
    font-size: 0 !important;
  }

  .dir-link-list {
    list-style: none !important;
    list-style-type: none !important;
    padding: 0 !important;
    margin: 0 0 1rem !important;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
  }

  .dir-link-item {
    list-style: none !important;
    list-style-type: none !important;
    transition: all 0.15s ease;
  }

  .dir-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.45rem 0.65rem;
    border-radius: 8px;
    color: var(--dir-link);
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 500;
    line-height: 1.35;
    min-height: 40px;
    transition: all 0.15s ease;
  }

  .dir-link:hover {
    background: var(--dir-accent-subtle);
    color: var(--dir-link-hover);
    padding-left: 0.85rem;
  }

  .dir-link:focus-visible {
    outline: 2px solid var(--dir-accent);
    outline-offset: 1px;
    background: var(--dir-accent-subtle);
  }

  .dir-link-arrow {
    opacity: 0;
    transform: translateX(-4px);
    font-size: 0.8rem;
    color: var(--dir-accent);
    transition: all 0.15s ease;
    flex-shrink: 0;
  }

  .dir-link:hover .dir-link-arrow {
    opacity: 1;
    transform: translateX(0);
  }

  .dir-card-footer {
    padding-top: 0.75rem;
    border-top: 1px dashed var(--dir-border);
  }

  .dir-view-all {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.84rem;
    font-weight: 600;
    color: var(--dir-accent);
    text-decoration: none;
    transition: gap 0.15s ease, color 0.15s ease;
  }

  .dir-view-all:hover {
    color: var(--dir-accent-hover);
    gap: 0.55rem;
  }

  /* Engine Showcase Cards */
  .dir-engine-card {
    border-top: 3px solid var(--dir-accent);
  }

  /* Creative Goals Cards */
  .dir-goal-card {
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    border-radius: 16px;
    padding: 1.35rem;
    box-shadow: var(--dir-shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: all 0.2s ease;
    position: relative;
  }

  .dir-goal-card:hover {
    transform: translateY(-3px);
    border-color: var(--dir-accent);
    box-shadow: var(--dir-shadow-hover);
  }

  .dir-goal-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--dir-text-heading);
    margin-bottom: 0.5rem;
    line-height: 1.3;
  }

  .dir-goal-desc {
    font-size: 0.88rem;
    color: var(--dir-text-muted);
    line-height: 1.5;
    margin-bottom: 1.25rem;
  }

  /* Editorial Guide Card */
  .dir-guide-card {
    background: var(--dir-surface);
    border: 1px solid var(--dir-border);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: var(--dir-shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), 
                border-color 0.2s ease;
  }

  .dir-guide-card:hover {
    transform: translateY(-4px);
    border-color: var(--dir-border-hover);
    box-shadow: var(--dir-shadow-hover);
  }

  .dir-guide-cover {
    position: relative;
    width: 100%;
    height: 185px;
    overflow: hidden;
    background-color: #0f172a;
    display: block;
    text-decoration: none;
  }

  .dir-guide-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
  }

  .dir-guide-card:hover .dir-guide-img {
    transform: scale(1.05);
  }

  .dir-guide-badge-left {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
  }

  .dir-guide-badge-right {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 3;
  }

  .dir-guide-body {
    padding: 1.35rem 1.35rem 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .dir-guide-title {
    font-size: 1.12rem;
    font-weight: 700;
    color: var(--dir-text-heading);
    line-height: 1.35;
    margin-bottom: 0.65rem;
  }

  .dir-guide-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .dir-guide-title a:hover {
    color: var(--dir-accent);
  }

  .dir-guide-excerpt {
    font-size: 0.88rem;
    color: var(--dir-text-muted);
    line-height: 1.55;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .dir-guide-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: auto;
    margin-bottom: 0.5rem;
  }

  .dir-guide-footer {
    padding: 0.9rem 1.35rem 1.25rem;
    border-top: 1px solid var(--dir-border);
  }

  /* Live Highlighting */
  mark.dir-highlight {
    background-color: rgba(0, 214, 144, 0.3);
    color: inherit;
    padding: 0.05rem 0.2rem;
    border-radius: 3px;
    font-weight: 600;
  }

  /* Empty Search State */
  .dir-empty-state {
    display: none;
    text-align: center;
    padding: 5rem 1.5rem;
    background: var(--dir-surface);
    border: 1px dashed var(--dir-border);
    border-radius: 20px;
    margin: 3rem 0;
  }

  .dir-empty-icon {
    font-size: 3rem;
    color: var(--dir-accent);
    margin-bottom: 1rem;
    display: inline-block;
  }

  /* Active Results Floating Badge */
  #searchStatusBanner {
    display: none;
    margin-top: 1rem;
    font-size: 0.88rem;
    color: var(--dir-text-muted);
  }
</style>
@endsection

@section('content')
<div class="directory-page">

  <!-- ====================================================================== -->
  <!-- 1. Hero & Central Search Section                                       -->
  <!-- ====================================================================== -->
  <header class="directory-hero">
    <div class="container" style="max-width: 1240px;">
      
      <!-- Breadcrumb -->
      <nav aria-label="Breadcrumb" class="mb-3">
        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill border" style="background: var(--dir-surface); font-size: 0.85rem; border-color: var(--dir-border) !important;">
          <a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="bi-house-door me-1"></i> Home</a>
          <span class="mx-2 text-muted">/</span>
          <span class="fw-semibold" style="color: var(--dir-text-heading);">Directory</span>
        </div>
      </nav>

      <div class="text-center position-relative" style="z-index: 2;">
        <!-- Eyebrow Pill -->
        <div class="dir-hero-pill">
          <span class="dir-pulse-dot"></span>
          <span>Complete Creative Resource &amp; Sitemap Index</span>
        </div>

        <!-- Main H1 -->
        <h1>Explore the ImagineBuddy Directory</h1>

        <!-- Value Proposition Lead -->
        <p class="lead">
          Discover high-converting AI image prompts, commercial studio photography ideas, specialized lighting setups, free tools, and tutorials. Browse our complete library to kickstart your next commercial project.
        </p>

        <!-- Metric Stat Chips -->
        <div class="dir-stats-grid">
          <div class="dir-stat-chip">
            <i class="bi-cpu text-mint"></i>
            <span><strong>4</strong> AI Engines</span>
          </div>
          <div class="dir-stat-chip">
            <i class="bi-bag-check text-mint"></i>
            <span><strong>{{ $categories->count() }}</strong> E-Commerce Categories</span>
          </div>
          <div class="dir-stat-chip">
            <i class="bi-palette text-mint"></i>
            <span><strong>28+</strong> Lighting &amp; Perspectives</span>
          </div>
          <div class="dir-stat-chip">
            <i class="bi-journal-richtext text-mint"></i>
            <span><strong>{{ $guides->count() + $blogPosts->count() }}</strong> Guides &amp; Tutorials</span>
          </div>
          <div class="dir-stat-chip">
            <i class="bi-check2-circle text-mint"></i>
            <span><strong>100%</strong> Commercial Discovery</span>
          </div>
        </div>

        <!-- Prominent Floating Search -->
        <div class="dir-search-box">
          <div class="dir-search-wrapper">
            <div class="dir-search-icon">
              <i class="bi-search"></i>
            </div>
            <input type="search" 
                   id="directorySearch" 
                   class="dir-search-input" 
                   placeholder="Search prompts, categories, styles, tools, and guides..." 
                   autocomplete="off" 
                   aria-label="Search directory listings">
            <button class="dir-search-clear d-none" id="directoryClearBtn" type="button" aria-label="Clear search input">
              <i class="bi-x-circle-fill fs-5"></i>
            </button>
          </div>

          <!-- Active match counter -->
          <div id="searchStatusBanner">
            Found <strong id="matchCountNumber" class="text-mint">0</strong> matching resources for "<span id="activeSearchQuery"></span>"
          </div>

          <!-- Quick Suggestion Tags -->
          <div class="dir-quick-tags" id="dirQuickTags">
            <span>Popular shortcuts:</span>
            <button type="button" class="dir-quick-btn" data-search-term="Midjourney">Midjourney</button>
            <button type="button" class="dir-quick-btn" data-search-term="Skincare">Skincare</button>
            <button type="button" class="dir-quick-btn" data-search-term="Jewellery">Jewellery</button>
            <button type="button" class="dir-quick-btn" data-search-term="FLUX">FLUX.1</button>
            <button type="button" class="dir-quick-btn" data-search-term="Softbox">Softbox Lighting</button>
            <button type="button" class="dir-quick-btn" data-search-term="Amazon Hero">Amazon Hero</button>
            <button type="button" class="dir-quick-btn" data-search-term="Guides">Guides</button>
          </div>
        </div>

      </div>
    </div>
  </header>

  <!-- ====================================================================== -->
  <!-- 2. Sticky Segmented Navigation Bar                                     -->
  <!-- ====================================================================== -->
  <nav class="dir-sticky-nav" id="directoryNav" aria-label="Directory Categories Navigation">
    <div class="container" style="max-width: 1260px;">
      <div class="dir-nav-wrapper">
        <button class="dir-nav-arrow dir-nav-arrow-left" id="dirNavLeftBtn" type="button" aria-label="Scroll navigation left">
          <i class="bi-chevron-left"></i>
        </button>

        <div class="dir-nav-scroller" id="dirNavScroller">
          <a href="#section-all" class="dir-nav-btn active" data-filter="all">
            <i class="bi-grid"></i> All
          </a>
          <a href="#section-engines" class="dir-nav-btn" data-filter="engines">
            <i class="bi-cpu"></i> AI Engines
          </a>
          <a href="#section-commercial" class="dir-nav-btn" data-filter="commercial">
            <i class="bi-bag-check"></i> Products
            <span class="dir-nav-count">{{ $categories->count() }}</span>
          </a>
          <a href="#section-styles" class="dir-nav-btn" data-filter="styles">
            <i class="bi-palette"></i> Styles &amp; Lighting
          </a>
          <a href="#section-tools" class="dir-nav-btn" data-filter="tools">
            <i class="bi-tools"></i> Tools
          </a>
          <a href="#section-guides" class="dir-nav-btn" data-filter="guides">
            <i class="bi-book"></i> Guides
            <span class="dir-nav-count">{{ $guides->count() }}</span>
          </a>
          <a href="#section-blog" class="dir-nav-btn" data-filter="blog">
            <i class="bi-journal-text"></i> Blog
          </a>
          <a href="#section-goals" class="dir-nav-btn" data-filter="goals">
            <i class="bi-bullseye"></i> Goals
          </a>
          <a href="#section-company" class="dir-nav-btn" data-filter="company">
            <i class="bi-info-circle"></i> Company &amp; Legal
          </a>
        </div>

        <button class="dir-nav-arrow dir-nav-arrow-right" id="dirNavRightBtn" type="button" aria-label="Scroll navigation right">
          <i class="bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- ====================================================================== -->
  <!-- 3. Directory Content Container                                         -->
  <!-- ====================================================================== -->
  <main class="container py-3" style="max-width: 1240px;">

    <!-- Empty Search State -->
    <div class="dir-empty-state" id="dirEmptyState">
      <div class="dir-empty-icon">
        <i class="bi-search"></i>
      </div>
      <h2 class="h4 fw-bold" style="color: var(--dir-text-heading);">No matching resources found</h2>
      <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
        We couldn't locate any prompts, categories, or tools matching "<strong id="emptySearchTerm" class="text-mint"></strong>". Try searching with another keyword or reset to browse all categories.
      </p>
      <button type="button" class="btn btn-custom rounded-pill px-4" id="btnResetSearch">
        <i class="bi-arrow-counterclockwise me-1"></i> Reset Search &amp; View All
      </button>
    </div>

    <!-- Active Listings Wrapper -->
    <div id="dirMainContent">

      <!-- ================================================================== -->
      <!-- SECTION A: AI Image Prompts by Engine                              -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-engines" data-category="engines">
        <div class="dir-section-header">
          <div class="dir-section-pretitle">
            <i class="bi-cpu text-mint"></i> Foundational AI Models
          </div>
          <h2 class="dir-section-title">AI Image Prompts by Engine</h2>
          <p class="dir-section-desc">
            Model-specific prompt architectures, aspect ratio parameters, and commercial render recipes tuned for every major AI image generator.
          </p>
        </div>

        <div class="dir-grid-3">
          @foreach ($engines as $engine)
          <div class="dir-card dir-engine-card dir-item-group" data-item-type="engine">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="{{ $engine['icon'] }}"></i>
                  </div>
                  <h3 class="dir-card-title">
                    <a href="{{ $engine['url'] }}">{{ $engine['name'] }}</a>
                  </h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($engine['links'] as $link)
                <li class="dir-link-item" data-search-text="{{ strtolower($link['title'] . ' ' . $link['keywords'] . ' ' . $engine['name']) }}">
                  <a href="{{ $link['url'] }}" class="dir-link">
                    <span class="dir-link-text">{{ $link['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ $engine['url'] }}" class="dir-view-all">
                Explore {{ $engine['name'] }} <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION B: Commercial & D2C Product Photography                    -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-commercial" data-category="commercial">
        <div class="dir-section-header d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
          <div>
            <div class="dir-section-pretitle">
              <i class="bi-bag-check text-mint"></i> E-Commerce &amp; Brands
            </div>
            <h2 class="dir-section-title">Commercial &amp; D2C Product Photography</h2>
            <p class="dir-section-desc">
              Browse prompt formulas curated specifically for e-commerce stores, Shopify listings, Amazon hero images, and brand advertising.
            </p>
          </div>
          <a href="{{ url('categories') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 align-self-start text-nowrap">
            View All Categories <i class="bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="dir-grid-3">
          @foreach ($categories as $cat)
          @php
            $catIcon = $categoryIconMap[$cat->slug] ?? 'bi-box-seam';
            $catQueries = $categoryQueriesMap[$cat->slug] ?? [
                'Studio Hero Photoshoots',
                'White Background E-commerce',
                'Lifestyle & In-Context',
                'Flat-Lay Compositions',
                'Macro Texture & Close-Ups',
                'Packaging & Unboxing Photography',
            ];
          @endphp
          <div class="dir-card dir-item-group" data-item-type="category">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="{{ $catIcon }}"></i>
                  </div>
                  <h3 class="dir-card-title">
                    <a href="{{ url('category', $cat->slug) }}">{{ $cat->name }}</a>
                  </h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                <li class="dir-link-item" data-search-text="{{ strtolower($cat->name . ' all prompts collection photoshoot hero') }}">
                  <a href="{{ url('category', $cat->slug) }}" class="dir-link fw-semibold">
                    <span class="dir-link-text">{{ $cat->name }} Master Collection</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>

                @if ($cat->subcategories && $cat->subcategories->count() > 0)
                  @foreach ($cat->subcategories as $sub)
                  <li class="dir-link-item" data-search-text="{{ strtolower($cat->name . ' ' . $sub->name . ' product photoshoot prompts') }}">
                    <a href="{{ url('category/' . $cat->slug . '/' . $sub->slug) }}" class="dir-link">
                      <span class="dir-link-text">{{ $sub->name }}</span>
                      <i class="bi-chevron-right dir-link-arrow"></i>
                    </a>
                  </li>
                  @endforeach
                @else
                  @foreach ($catQueries as $queryTitle)
                  <li class="dir-link-item" data-search-text="{{ strtolower($cat->name . ' ' . $queryTitle . ' product photoshoot prompts') }}">
                    <a href="{{ url('search?q=' . urlencode($cat->name . ' ' . $queryTitle)) }}" class="dir-link">
                      <span class="dir-link-text">{{ $queryTitle }}</span>
                      <i class="bi-chevron-right dir-link-arrow"></i>
                    </a>
                  </li>
                  @endforeach
                @endif
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('category', $cat->slug) }}" class="dir-view-all">
                Browse {{ $cat->name }} <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION C: Themes, Settings & Lighting Styles                      -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-styles" data-category="styles">
        <div class="dir-section-header d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
          <div>
            <div class="dir-section-pretitle">
              <i class="bi-palette text-mint"></i> Visual Craft &amp; Direction
            </div>
            <h2 class="dir-section-title">Themes, Settings &amp; Lighting Styles</h2>
            <p class="dir-section-desc">
              Direct camera perspectives, studio lighting setups, background textures, and visual aesthetics with precise prompt terms.
            </p>
          </div>
          <a href="{{ url('categories') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 align-self-start text-nowrap">
            View All Categories <i class="bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="dir-grid-3">
          @foreach ($stylesAndThemes as $styleGroup)
          <div class="dir-card dir-item-group" data-item-type="style">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="{{ $styleGroup['icon'] }}"></i>
                  </div>
                  <h3 class="dir-card-title">{{ $styleGroup['title'] }}</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($styleGroup['links'] as $sLink)
                <li class="dir-link-item" data-search-text="{{ strtolower($sLink['title'] . ' ' . $sLink['keywords'] . ' ' . $styleGroup['title']) }}">
                  <a href="{{ $sLink['url'] }}" class="dir-link">
                    <span class="dir-link-text">{{ $sLink['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('categories') }}" class="dir-view-all">
                View All {{ $styleGroup['title'] }} <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION D: Free Tools, Generators & Interactive Resources          -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-tools" data-category="tools">
        <div class="dir-section-header">
          <div class="dir-section-pretitle">
            <i class="bi-tools text-mint"></i> Utilities &amp; Cheatsheets
          </div>
          <h2 class="dir-section-title">Free Tools, Generators &amp; Resources</h2>
          <p class="dir-section-desc">
            Speed up your production workflow with prompt refiners, cheat sheets, curated image model libraries, and photoshoot kits.
          </p>
        </div>

        <div class="dir-grid-3">
          @foreach ($toolsAndResources as $toolGroup)
          <div class="dir-card dir-item-group" data-item-type="tool">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="{{ $toolGroup['icon'] }}"></i>
                  </div>
                  <h3 class="dir-card-title">{{ $toolGroup['title'] }}</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($toolGroup['links'] as $tLink)
                <li class="dir-link-item" data-search-text="{{ strtolower($tLink['title'] . ' ' . $tLink['keywords'] . ' ' . $toolGroup['title']) }}">
                  <a href="{{ $tLink['url'] }}" class="dir-link fw-semibold">
                    <span class="dir-link-text"><i class="bi-arrow-right-short text-mint me-1"></i>{{ $tLink['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('search') }}" class="dir-view-all">
                Launch Tools <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endforeach

          {{-- Photoshoots Pack Box --}}
          @if ($photoshoots->count() > 0)
          <div class="dir-card dir-item-group" data-item-type="photoshoot">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-camera-reels"></i>
                  </div>
                  <h3 class="dir-card-title">
                    <a href="{{ url('photoshoots') }}">Curated Photoshoots</a>
                  </h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                <li class="dir-link-item" data-search-text="photoshoots all brand packs photoshoots archive">
                  <a href="{{ url('photoshoots') }}" class="dir-link fw-semibold">
                    <span class="dir-link-text">Explore All Photoshoots</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @foreach ($photoshoots->take(5) as $ps)
                <li class="dir-link-item" data-search-text="{{ strtolower($ps->title . ' photoshoot kit concept prompts') }}">
                  <a href="{{ url('photoshoots', $ps->slug) }}" class="dir-link">
                    <span class="dir-link-text">{{ $ps->title }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('photoshoots') }}" class="dir-view-all">
                View Photoshoot Library <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endif
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION E: Guides & Prompt Tutorials                               -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-guides" data-category="guides">
        <div class="dir-section-header">
          <div class="dir-section-pretitle">
            <i class="bi-book text-mint"></i> Masterclasses &amp; Breakdowns
          </div>
          <h2 class="dir-section-title">Guides &amp; Prompt Tutorials</h2>
          <p class="dir-section-desc">
            In-depth commercial photography masterclasses with copy-paste prompt formulas, parameter cheat sheets, and production workflows.
          </p>
        </div>

        <div class="dir-grid-3">
          <!-- Prompt Guides Chunked across 3 cards in row -->
          @php
            $guideGroups = $guides->chunk(ceil(max(1, $guides->count()) / 3));
            $cardTitles = [
              0 => 'Masterclasses &amp; Tutorials',
              1 => 'Prompt Formulas &amp; Breakdowns',
              2 => 'Production &amp; Lighting Workflows',
            ];
          @endphp
          @foreach ($guideGroups as $gIndex => $gChunk)
          <div class="dir-card dir-item-group" data-item-type="guide">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-journal-bookmark"></i>
                  </div>
                  <h3 class="dir-card-title">{{ $cardTitles[$gIndex] ?? ('Commercial Guides (' . ($gIndex + 1) . ')') }}</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($gChunk as $guide)
                <li class="dir-link-item" data-search-text="{{ strtolower($guide->title . ' ' . $guide->excerpt . ' ' . ($guide->category->name ?? '') . ' guide tutorial prompts masterclass') }}">
                  <a href="{{ url('guides', $guide->slug) }}" class="dir-link">
                    <span class="dir-link-text">{{ $guide->title }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION E2: Blog Articles & Topics                                 -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-blog" data-category="blog">
        <div class="dir-section-header d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
          <div>
            <div class="dir-section-pretitle">
              <i class="bi-journal-text text-mint"></i> Articles &amp; Insights
            </div>
            <h2 class="dir-section-title">Blog Articles &amp; Insights</h2>
            <p class="dir-section-desc">
              Weekly industry analyses, negative prompt guides, camera lighting explanations, and e-commerce case studies.
            </p>
          </div>
          <a href="{{ url('blog') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 align-self-start text-nowrap">
            Explore Blog <i class="bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="dir-grid-3">
          <!-- Blog Categories -->
          <div class="dir-card dir-item-group" data-item-type="blog-category">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-folder2-open"></i>
                  </div>
                  <h3 class="dir-card-title">Blog Topics</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                <li class="dir-link-item" data-search-text="blog all articles latest tutorials tips">
                  <a href="{{ url('blog') }}" class="dir-link fw-semibold">
                    <span class="dir-link-text">All Blog Posts</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @foreach ($blogCategories as $bCat)
                <li class="dir-link-item" data-search-text="{{ strtolower($bCat->name . ' blog category topic tutorial') }}">
                  <a href="{{ url('blog/category', $bCat->slug) }}" class="dir-link">
                    <span class="dir-link-text">{{ $bCat->name }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('blog') }}" class="dir-view-all">
                Browse Topics <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Blog Posts Chunked into 2 cards to form exactly 3 cards in row -->
          @php
            $postGroups = $blogPosts->chunk(ceil(max(1, $blogPosts->count()) / 2));
          @endphp
          @foreach ($postGroups as $gIndex => $pChunk)
          <div class="dir-card dir-item-group" data-item-type="blog-post">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-file-text"></i>
                  </div>
                  <h3 class="dir-card-title">{{ $gIndex === 0 ? 'Tutorials &amp; Guides' : 'Articles &amp; Insights' }}</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($pChunk as $post)
                <li class="dir-link-item" data-search-text="{{ strtolower($post->title . ' blog post tutorial guide') }}">
                  <a href="{{ url('blog', $post->slug) }}" class="dir-link">
                    <span class="dir-link-text">{{ $post->title }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('blog') }}" class="dir-view-all">
                Read Articles <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION F: Browse by Creative Goal                                 -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-goals" data-category="goals">
        <div class="dir-section-header">
          <div class="dir-section-pretitle">
            <i class="bi-bullseye text-mint"></i> Intent-Driven Playbooks
          </div>
          <h2 class="dir-section-title">Browse by Creative Goal</h2>
          <p class="dir-section-desc">
            Know the commercial outcome you want to produce? Jump straight into prompt workflows designed for your specific marketing milestone.
          </p>
        </div>

        <div class="dir-grid-3">
          @foreach ($creativeGoals as $goal)
          <div class="dir-goal-card dir-item-group" data-item-type="goal" data-search-text="{{ strtolower($goal['title'] . ' ' . $goal['desc'] . ' ' . ($goal['type_label'] ?? '')) }}">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="dir-card-icon-box">
                  <i class="{{ $goal['icon'] ?? 'bi-bullseye' }}"></i>
                </div>
                @if (!empty($goal['type_label']))
                <span class="dir-badge-pill">
                  <i class="{{ $goal['badge_icon'] ?? 'bi-link-45deg' }} me-1"></i> {{ $goal['type_label'] }}
                </span>
                @endif
              </div>
              <h3 class="dir-goal-title">
                <a href="{{ $goal['url'] }}" class="text-decoration-none text-reset stretched-link">
                  {{ $goal['title'] }}
                </a>
              </h3>
              <p class="dir-goal-desc">
                {{ $goal['desc'] }}
              </p>
            </div>
            <div class="dir-view-all">
              {{ $goal['action_text'] ?? 'Explore Prompts' }} <i class="bi-arrow-right"></i>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      <!-- ================================================================== -->
      <!-- SECTION G: Company, Plans & Legal Information                      -->
      <!-- ================================================================== -->
      <section class="dir-section" id="section-company" data-category="company">
        <div class="dir-section-header">
          <div class="dir-section-pretitle">
            <i class="bi-info-circle text-mint"></i> Platform &amp; Governance
          </div>
          <h2 class="dir-section-title">Company, Plans &amp; Legal</h2>
          <p class="dir-section-desc">
            Access official documentation, subscription pricing, commercial copyright licensing, and customer support channels.
          </p>
        </div>

        <div class="dir-grid-3">
          <!-- About & Support -->
          <div class="dir-card dir-item-group" data-item-type="company">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-building"></i>
                  </div>
                  <h3 class="dir-card-title">About ImagineBuddy</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($companyLinks['company'] as $cLink)
                <li class="dir-link-item" data-search-text="{{ strtolower($cLink['title'] . ' ' . $cLink['keywords']) }}">
                  <a href="{{ $cLink['url'] }}" class="dir-link">
                    <span class="dir-link-text">{{ $cLink['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('contact') }}" class="dir-view-all">
                Contact Support <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Pricing & Usage -->
          <div class="dir-card dir-item-group" data-item-type="company">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-credit-card"></i>
                  </div>
                  <h3 class="dir-card-title">Plans &amp; Rights</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($companyLinks['plans'] as $pLink)
                <li class="dir-link-item" data-search-text="{{ strtolower($pLink['title'] . ' ' . $pLink['keywords']) }}">
                  <a href="{{ $pLink['url'] }}" class="dir-link">
                    <span class="dir-link-text">{{ $pLink['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('pricing') }}" class="dir-view-all">
                View Pricing Plans <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Policies & Legal -->
          <div class="dir-card dir-item-group" data-item-type="company">
            <div>
              <div class="dir-card-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="dir-card-icon-box">
                    <i class="bi-shield-check"></i>
                  </div>
                  <h3 class="dir-card-title">Legal &amp; Privacy</h3>
                </div>
              </div>

              <ul class="dir-link-list list-unstyled">
                @foreach ($companyLinks['legal'] as $lLink)
                <li class="dir-link-item" data-search-text="{{ strtolower($lLink['title'] . ' ' . $lLink['keywords']) }}">
                  <a href="{{ $lLink['url'] }}" class="dir-link">
                    <span class="dir-link-text">{{ $lLink['title'] }}</span>
                    <i class="bi-chevron-right dir-link-arrow"></i>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            <div class="dir-card-footer">
              <a href="{{ url('page/terms-and-conditions') }}" class="dir-view-all">
                Review Terms <i class="bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </section>

    </div><!-- /dirMainContent -->

  </main>

</div><!-- /directory-page -->

<!-- ====================================================================== -->
<!-- 4. Schema.org JSON-LD Structured Data                                  -->
<!-- ====================================================================== -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@graph": [
    {
      "@@type": "CollectionPage",
      "@@id": "{{ url('directory') }}#webpage",
      "url": "{{ url('directory') }}",
      "name": "ImagineBuddy Directory — AI Prompts, Product Photography & Tools",
      "description": "Explore the complete ImagineBuddy directory to discover AI image prompts, commercial product photography collections, creative styles, free tools, and practical resources.",
      "isPartOf": {
        "@@type": "WebSite",
        "@@id": "{{ url('/') }}#website",
        "name": "{{ $settings->title }}",
        "url": "{{ url('/') }}"
      }
    },
    {
      "@@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "{{ url('/') }}"
        },
        {
          "@@type": "ListItem",
          "position": 2,
          "name": "Directory",
          "item": "{{ url('directory') }}"
        }
      ]
    },
    {
      "@@type": "ItemList",
      "name": "ImagineBuddy Creative Directory Sections",
      "itemListElement": [
        {
          "@@type": "SiteNavigationElement",
          "position": 1,
          "name": "AI Image Prompts by Engine",
          "url": "{{ url('directory#section-engines') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 2,
          "name": "Commercial & D2C Product Photography",
          "url": "{{ url('directory#section-commercial') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 3,
          "name": "Themes, Settings & Lighting Styles",
          "url": "{{ url('directory#section-styles') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 4,
          "name": "Free Tools, Generators & Resources",
          "url": "{{ url('directory#section-tools') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 5,
          "name": "Guides & Prompt Tutorials",
          "url": "{{ url('directory#section-guides') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 6,
          "name": "Blog Articles & Insights",
          "url": "{{ url('directory#section-blog') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 7,
          "name": "Browse by Creative Goal",
          "url": "{{ url('directory#section-goals') }}"
        },
        {
          "@@type": "SiteNavigationElement",
          "position": 8,
          "name": "Company, Plans & Legal Information",
          "url": "{{ url('directory#section-company') }}"
        }
      ]
    }
  ]
}
</script>

<!-- ====================================================================== -->
<!-- 5. High-Performance Interactive Client-Side Engine                     -->
<!-- ====================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('directorySearch');
  const searchClear = document.getElementById('directoryClearBtn');
  const searchStatus = document.getElementById('searchStatusBanner');
  const matchCountNumber = document.getElementById('matchCountNumber');
  const activeSearchQuery = document.getElementById('activeSearchQuery');
  const emptyState = document.getElementById('dirEmptyState');
  const emptySearchTerm = document.getElementById('emptySearchTerm');
  const mainContent = document.getElementById('dirMainContent');
  const btnReset = document.getElementById('btnResetSearch');
  const navBtns = document.querySelectorAll('.dir-nav-btn');
  const sections = document.querySelectorAll('.dir-section');
  const quickButtons = document.querySelectorAll('.dir-quick-btn');

  // Cache original text for text nodes to cleanly restore after highlighting
  const linkElements = document.querySelectorAll('.dir-link-text, .dir-guide-link-title');
  const originalTexts = new Map();
  linkElements.forEach(el => {
    originalTexts.set(el, el.textContent.trim());
  });

  // Global Keyboard Navigation
  document.addEventListener('keydown', function (e) {
    if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    } else if (e.key === 'Escape' && document.activeElement === searchInput) {
      clearSearch();
    }
  });

  // Live Search Input Listener
  searchInput.addEventListener('input', function () {
    const query = this.value.trim().toLowerCase();
    if (query.length > 0) {
      searchClear.classList.remove('d-none');
    } else {
      searchClear.classList.add('d-none');
    }
    performSearch(query);
  });

  searchClear.addEventListener('click', function () {
    clearSearch();
  });

  if (btnReset) {
    btnReset.addEventListener('click', function () {
      clearSearch();
    });
  }

  // Quick Suggestion Chips Listener
  quickButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const term = this.getAttribute('data-search-term');
      searchInput.value = term;
      searchClear.classList.remove('d-none');
      performSearch(term.toLowerCase());
      searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });

  function clearSearch() {
    searchInput.value = '';
    searchClear.classList.add('d-none');
    searchStatus.style.display = 'none';
    emptyState.style.display = 'none';
    mainContent.style.display = 'block';

    // Restore original unhighlighted texts
    linkElements.forEach(el => {
      const original = originalTexts.get(el);
      if (original) el.textContent = original;
    });

    // Reset visibility of all sections, cards, and list items
    sections.forEach(sec => sec.style.display = '');
    document.querySelectorAll('.dir-item-group').forEach(card => card.style.display = '');
    document.querySelectorAll('.dir-link-item').forEach(item => item.style.display = '');

    searchInput.focus();
  }

  function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function performSearch(query) {
    if (!query) {
      clearSearch();
      return;
    }

    const tokens = query.split(/\s+/).filter(t => t.length > 0);
    let totalMatches = 0;

    sections.forEach(function (sec) {
      let sectionMatches = 0;
      const cards = sec.querySelectorAll('.dir-item-group');

      cards.forEach(function (card) {
        let cardMatches = 0;
        const linkItems = card.querySelectorAll('.dir-link-item');

        // Check if card itself has search text (like Goal cards or Guide cards)
        if (linkItems.length === 0) {
          const cardText = (card.getAttribute('data-search-text') || '') + ' ' + card.textContent.toLowerCase();
          const matches = tokens.every(token => cardText.includes(token));
          if (matches) {
            card.style.display = '';
            cardMatches++;
            totalMatches++;

            // Highlight in guide title if present
            const guideTitleEl = card.querySelector('.dir-guide-link-title');
            if (guideTitleEl) {
              const original = originalTexts.get(guideTitleEl) || guideTitleEl.textContent.trim();
              let highlighted = original;
              tokens.forEach(tok => {
                const regex = new RegExp('(' + escapeRegex(tok) + ')', 'gi');
                highlighted = highlighted.replace(regex, '<mark class="dir-highlight">$1</mark>');
              });
              guideTitleEl.innerHTML = highlighted;
            }
          } else {
            card.style.display = 'none';
            const guideTitleEl = card.querySelector('.dir-guide-link-title');
            if (guideTitleEl && originalTexts.has(guideTitleEl)) {
              guideTitleEl.textContent = originalTexts.get(guideTitleEl);
            }
          }
        } else {
          linkItems.forEach(function (item) {
            const searchText = (item.getAttribute('data-search-text') || '') + ' ' + item.textContent.toLowerCase();
            const matches = tokens.every(token => searchText.includes(token));

            const textEl = item.querySelector('.dir-link-text');
            const originalText = textEl ? (originalTexts.get(textEl) || textEl.textContent) : '';

            if (matches) {
              item.style.display = '';
              cardMatches++;
              totalMatches++;

              // Highlight matched tokens in link text
              if (textEl && originalText) {
                let highlighted = originalText;
                tokens.forEach(tok => {
                  const regex = new RegExp('(' + escapeRegex(tok) + ')', 'gi');
                  highlighted = highlighted.replace(regex, '<mark class="dir-highlight">$1</mark>');
                });
                textEl.innerHTML = highlighted;
              }
            } else {
              item.style.display = 'none';
              if (textEl && originalText) {
                textEl.textContent = originalText;
              }
            }
          });

          // Show or hide card based on whether any links matched
          if (cardMatches > 0) {
            card.style.display = '';
            sectionMatches += cardMatches;
          } else {
            card.style.display = 'none';
          }
        }
      });

      // Show or hide whole section
      if (sectionMatches > 0) {
        sec.style.display = '';
      } else {
        sec.style.display = 'none';
      }
    });

    // Update Status Banner and Empty State
    if (totalMatches === 0) {
      mainContent.style.display = 'none';
      emptyState.style.display = 'block';
      emptySearchTerm.textContent = searchInput.value.trim();
      searchStatus.style.display = 'none';
    } else {
      mainContent.style.display = 'block';
      emptyState.style.display = 'none';
      matchCountNumber.textContent = totalMatches;
      activeSearchQuery.textContent = searchInput.value.trim();
      searchStatus.style.display = 'block';
    }
  }

  // Single source of truth for active tab selection
  function setActiveTab(filterKey) {
    navBtns.forEach(btn => {
      const isTarget = btn.getAttribute('data-filter') === filterKey;
      if (isTarget) {
        if (!btn.classList.contains('active')) {
          btn.classList.add('active');
        }
        // In mobile/tablet horizontal scroll mode, ensure the active button is visible
        if (window.innerWidth < 1080 && navScroller) {
          const scrollerRect = navScroller.getBoundingClientRect();
          const btnRect = btn.getBoundingClientRect();
          if (btnRect.left < scrollerRect.left + 15 || btnRect.right > scrollerRect.right - 15) {
            btn.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
          }
        }
      } else {
        btn.classList.remove('active');
      }
    });
  }

  function syncStickyNavTop() {
    const mainHeader = document.getElementById('header');
    const stickyNav = document.getElementById('directoryNav');
    if (mainHeader && stickyNav) {
      const h = mainHeader.offsetHeight;
      stickyNav.style.top = h + 'px';
    }
  }
  syncStickyNavTop();
  window.addEventListener('resize', syncStickyNavTop, { passive: true });

  function getStickyOffset() {
    const mainHeader = document.getElementById('header');
    const headerH = mainHeader ? mainHeader.offsetHeight : 70;
    const stickyNav = document.getElementById('directoryNav');
    const navHeight = stickyNav ? stickyNav.offsetHeight : 54;
    return headerH + navHeight + 20; // Exact header + nav height + 20px comfortable breathing room
  }

  let isClickScrolling = false;
  let scrollTimeout = null;

  // Smooth Scrolling & Section Navigation for Tabs
  navBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      this.blur();
      const filter = this.getAttribute('data-filter');
      
      setActiveTab(filter);
      isClickScrolling = true;
      clearTimeout(scrollTimeout);

      if (filter === 'all') {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      } else {
        const targetSection = document.getElementById('section-' + filter);
        if (targetSection) {
          const stickyOffset = getStickyOffset();
          const targetY = targetSection.getBoundingClientRect().top + window.pageYOffset - stickyOffset;
          window.scrollTo({
            top: Math.max(0, targetY),
            behavior: 'smooth'
          });
        }
      }

      scrollTimeout = setTimeout(() => {
        isClickScrolling = false;
      }, 850);
    });
  });

  // Accurate ScrollSpy on Window Scroll (Only one tab active at a time)
  let ticking = false;

  function updateScrollSpy() {
    if (isClickScrolling) return;

    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const stickyOffset = getStickyOffset();
    const windowHeight = window.innerHeight;
    const docHeight = document.documentElement.scrollHeight;

    // 1. If at or near bottom of document, activate the last section
    if (scrollY + windowHeight >= docHeight - 70) {
      setActiveTab('company');
      return;
    }

    // 2. If above the first section (Hero / Search area), activate 'all'
    const firstSection = document.getElementById('section-engines');
    if (firstSection) {
      const firstSecRect = firstSection.getBoundingClientRect();
      if (firstSecRect.top > stickyOffset + 40) {
        setActiveTab('all');
        return;
      }
    }

    // 3. Check sections in reverse order (bottom-most section matching view zone wins)
    const sectionIds = ['company', 'goals', 'blog', 'guides', 'tools', 'styles', 'commercial', 'engines'];
    for (const key of sectionIds) {
      const sec = document.getElementById('section-' + key);
      if (sec && sec.style.display !== 'none') {
        const rect = sec.getBoundingClientRect();
        if (rect.top <= stickyOffset + 50 && rect.bottom > stickyOffset + 15) {
          setActiveTab(key);
          return;
        }
      }
    }

    setActiveTab('all');
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        updateScrollSpy();
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });

  // Initial trigger to ensure correct active state
  updateScrollSpy();

  // Horizontal Nav Scroller & Arrows for responsive viewports
  const navScroller = document.getElementById('dirNavScroller');
  const navLeftBtn = document.getElementById('dirNavLeftBtn');
  const navRightBtn = document.getElementById('dirNavRightBtn');

  function updateNavArrows() {
    if (!navScroller || !navLeftBtn || !navRightBtn) return;
    if (window.innerWidth >= 1080) {
      navLeftBtn.style.display = 'none';
      navRightBtn.style.display = 'none';
      return;
    }
    const canScrollLeft = navScroller.scrollLeft > 8;
    const canScrollRight = navScroller.scrollLeft < (navScroller.scrollWidth - navScroller.clientWidth - 8);
    const hasOverflow = navScroller.scrollWidth > navScroller.clientWidth + 5;

    if (hasOverflow) {
      navLeftBtn.style.display = canScrollLeft ? 'flex' : 'none';
      navRightBtn.style.display = canScrollRight ? 'flex' : 'none';
    } else {
      navLeftBtn.style.display = 'none';
      navRightBtn.style.display = 'none';
    }
  }

  if (navLeftBtn && navRightBtn && navScroller) {
    navLeftBtn.addEventListener('click', () => {
      navScroller.scrollBy({ left: -240, behavior: 'smooth' });
    });
    navRightBtn.addEventListener('click', () => {
      navScroller.scrollBy({ left: 240, behavior: 'smooth' });
    });
    navScroller.addEventListener('scroll', updateNavArrows, { passive: true });
    window.addEventListener('resize', () => {
      updateNavArrows();
      updateScrollSpy();
    });
    setTimeout(updateNavArrows, 150);
  }
});
</script>
@endsection
