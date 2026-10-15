@extends('layouts.app')

@section('title'){{ $hireSettings->meta_title ?? 'Hire Us | Custom AI Product Photoshoots' }}@endsection
@section('description_override'){{ $hireSettings->meta_description ?? 'Request custom AI-assisted product photoshoots tailored to your brand, creative direction, and campaign goals. 100% product fidelity guaranteed.' }}@endsection
@section('keywords_override'){{ $hireSettings->meta_keywords ?? 'custom AI product photography, AI photoshoots, brand visual campaigns, D2C creative agency, product packshots' }}@endsection

@section('css')
<style>
/* ==========================================================================
   HIRE PAGE DESIGN TOKENS (LIGHT & DARK THEMES)
   ========================================================================== */
:root {
  --hire-font-serif: 'Playfair Display', Georgia, serif;
  --hire-bg-body: #fafaf9;
  --hire-bg-surface: #ffffff;
  --hire-bg-surface-elevated: #ffffff;
  --hire-bg-subtle: #f4f4f5;
  --hire-border: #e4e4e7;
  --hire-border-subtle: #f1f1f4;
  --hire-text-title: #09090b;
  --hire-text-body: #3f3f46;
  --hire-text-muted: #71717a;
  --hire-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --hire-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
  --hire-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --hire-accent: var(--color-default, #0284c7);
  --hire-accent-rgb: 2, 132, 199;
}

[data-bs-theme="dark"] {
  --hire-bg-body: #090d16;
  --hire-bg-surface: #111726;
  --hire-bg-surface-elevated: #161e31;
  --hire-bg-subtle: #1a2236;
  --hire-border: rgba(255, 255, 255, 0.09);
  --hire-border-subtle: rgba(255, 255, 255, 0.05);
  --hire-text-title: #f8fafc;
  --hire-text-body: #cbd5e1;
  --hire-text-muted: #94a3b8;
  --hire-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.35);
  --hire-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.45);
  --hire-shadow-lg: 0 20px 30px rgba(0, 0, 0, 0.6);
}

/* ==========================================================================
   GLOBAL LAYOUT & TYPOGRAPHY UTILITIES
   ========================================================================== */
.hire-page {
  background-color: var(--hire-bg-body);
  color: var(--hire-text-body);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  overflow-x: hidden;
}

.hire-container {
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 20px;
  padding-right: 20px;
}

.hire-section {
  padding-top: 88px;
  padding-bottom: 88px;
  position: relative;
}

@media (max-width: 768px) {
  .hire-section {
    padding-top: 56px;
    padding-bottom: 56px;
  }
}

.hire-section-header {
  max-width: 760px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
  margin-bottom: 52px;
}

.hire-eyebrow-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--hire-accent);
  background: rgba(var(--hire-accent-rgb), 0.1);
  padding: 6px 16px;
  border-radius: 9999px;
  border: 1px solid rgba(var(--hire-accent-rgb), 0.2);
  margin-bottom: 18px;
}

.hire-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--hire-accent);
  box-shadow: 0 0 0 0 rgba(var(--hire-accent-rgb), 0.7);
  animation: hirePulse 2s infinite;
}

@keyframes hirePulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(var(--hire-accent-rgb), 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(var(--hire-accent-rgb), 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(var(--hire-accent-rgb), 0); }
}

.hire-title-lg {
  font-size: clamp(2.2rem, 4.5vw, 3.6rem);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.03em;
  color: var(--hire-text-title);
}

.hire-title-md {
  font-size: clamp(1.8rem, 3.2vw, 2.5rem);
  font-weight: 800;
  line-height: 1.25;
  letter-spacing: -0.02em;
  color: var(--hire-text-title);
}

.hire-title-sm {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--hire-text-title);
  letter-spacing: -0.01em;
}

.hire-lead {
  font-size: 1.125rem;
  line-height: 1.65;
  color: var(--hire-text-muted);
}

.hire-serif-accent {
  font-family: var(--hire-font-serif);
  font-style: italic;
  font-weight: 600;
}

/* ==========================================================================
   CARDS & SURFACES
   ========================================================================== */
.hire-card {
  background: var(--hire-bg-surface);
  border: 1px solid var(--hire-border);
  border-radius: 20px;
  padding: 32px;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
  position: relative;
}

.hire-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--hire-shadow-lg);
  border-color: rgba(var(--hire-accent-rgb), 0.35);
}

/* ==========================================================================
   HERO SECTION
   ========================================================================== */
.hire-hero-section {
  padding-top: 140px;
  padding-bottom: 80px;
  position: relative;
}

.hire-trust-strip {
  display: flex;
  flex-wrap: wrap;
  gap: 16px 24px;
  align-items: center;
  font-size: 0.88rem;
  color: var(--hire-text-muted);
  border-top: 1px solid var(--hire-border-subtle);
  padding-top: 24px;
}

.hire-trust-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
}

.hire-trust-item i {
  color: #10b981;
}

.hire-hero-showcase {
  background: var(--hire-bg-surface);
  border: 1px solid var(--hire-border);
  border-radius: 24px;
  padding: 18px;
  box-shadow: var(--hire-shadow-lg);
  position: relative;
}

.hire-showcase-header {
  padding-bottom: 14px;
  border-bottom: 1px solid var(--hire-border-subtle);
}

.hire-hero-showcase .before-after-slider-container {
  min-height: 480px;
  max-height: 560px;
  aspect-ratio: 4 / 5;
  margin: 0 auto;
}

@media (max-width: 576px) {
  .hire-hero-showcase .before-after-slider-container {
    min-height: 380px;
  }
}

/* ==========================================================================
   BUTTONS
   ========================================================================== */
.hire-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  background-color: var(--hire-text-title);
  color: var(--hire-bg-body) !important;
  font-weight: 700;
  font-size: 1rem;
  padding: 14px 32px;
  border-radius: 9999px;
  border: 1px solid var(--hire-text-title);
  text-decoration: none;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: var(--hire-shadow-sm);
  cursor: pointer;
}

.hire-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--hire-shadow-md);
  opacity: 0.94;
}

.hire-btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background-color: transparent;
  color: var(--hire-text-title) !important;
  font-weight: 600;
  font-size: 1rem;
  padding: 14px 28px;
  border-radius: 9999px;
  border: 1px solid var(--hire-border);
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
}

.hire-btn-secondary:hover {
  background-color: var(--hire-bg-subtle);
  border-color: var(--hire-text-title);
}

/* ==========================================================================
   PORTFOLIO GALLERY
   ========================================================================== */
.hire-filter-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  margin-bottom: 36px;
}

.hire-filter-btn {
  background: var(--hire-bg-surface);
  border: 1px solid var(--hire-border);
  color: var(--hire-text-body);
  font-size: 0.88rem;
  font-weight: 600;
  padding: 8px 20px;
  border-radius: 9999px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.hire-filter-btn:hover {
  border-color: var(--hire-text-title);
  color: var(--hire-text-title);
}

.hire-filter-btn.active {
  background: var(--hire-text-title);
  border-color: var(--hire-text-title);
  color: var(--hire-bg-body) !important;
}

.hire-gallery-card {
  position: relative;
  border-radius: 18px;
  overflow: hidden;
  background: #0f172a;
  aspect-ratio: 1 / 1;
  box-shadow: var(--hire-shadow-sm);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}

.hire-gallery-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--hire-shadow-lg);
}

.hire-gallery-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.hire-gallery-card:hover img {
  transform: scale(1.06);
}

.hire-gallery-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(15, 23, 42, 0.65) 100%);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 14px;
  color: #ffffff;
  pointer-events: none;
}

.hire-gallery-tag {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: #ffffff;
  padding: 5px 12px;
  border-radius: 9999px;
  align-self: flex-start;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
}

/* Hide GLightbox slide description bar */
.gslide-description,
.gslide-description.description-bottom {
  display: none !important;
}

/* ==========================================================================
   COMPARISON MATRIX TABLE
   ========================================================================== */
.hire-matrix-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid var(--hire-border);
  border-radius: 18px;
  overflow: hidden;
  background: var(--hire-bg-surface);
}

.hire-matrix-table th,
.hire-matrix-table td {
  padding: 18px 24px;
  border-bottom: 1px solid var(--hire-border-subtle);
  vertical-align: middle;
  font-size: 0.95rem;
}

.hire-matrix-table th {
  background: var(--hire-bg-subtle);
  font-weight: 700;
  color: var(--hire-text-title);
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.hire-matrix-table tr:last-child td {
  border-bottom: none;
}

.hire-matrix-highlight-col {
  background: rgba(var(--hire-accent-rgb), 0.04);
  font-weight: 600;
  color: var(--hire-text-title);
  border-left: 2px solid var(--hire-accent) !important;
  border-right: 2px solid var(--hire-accent) !important;
}

th.hire-matrix-highlight-col {
  background: rgba(var(--hire-accent-rgb), 0.12) !important;
  color: var(--hire-accent) !important;
}

/* ==========================================================================
   PRICING & CURRENCY SWITCHER
   ========================================================================== */
.hire-currency-switch-box {
  display: inline-flex;
  align-items: center;
  background: var(--hire-bg-subtle);
  border: 1px solid var(--hire-border);
  padding: 4px;
  border-radius: 9999px;
  margin-bottom: 12px;
}

.hire-currency-btn {
  background: transparent;
  border: none;
  padding: 8px 20px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--hire-text-muted);
  cursor: pointer;
  transition: all 0.2s ease;
}

.hire-currency-btn.active {
  background: var(--hire-bg-surface);
  color: var(--hire-text-title);
  box-shadow: var(--hire-shadow-sm);
}

.hire-price-tag {
  font-size: 2.8rem;
  font-weight: 900;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--hire-text-title);
}

.hire-price-unit {
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--hire-text-muted);
}

/* ==========================================================================
   INQUIRY FORM
   ========================================================================== */
.hire-form-section {
  background: var(--hire-bg-surface);
  border: 1px solid var(--hire-border);
  border-radius: 28px;
  padding: 48px;
  box-shadow: var(--hire-shadow-lg);
}

@media (max-width: 768px) {
  .hire-form-section {
    padding: 24px 20px;
  }
}

.hire-form-group-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--hire-text-title);
  padding-bottom: 12px;
  border-bottom: 1px solid var(--hire-border-subtle);
  margin-bottom: 24px;
  margin-top: 32px;
}

.hire-form-group-title:first-of-type {
  margin-top: 0;
}

.hire-form-group-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--hire-bg-subtle);
  color: var(--hire-text-title);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 800;
}

.hire-input,
.hire-select,
.hire-textarea {
  width: 100%;
  background-color: var(--hire-bg-subtle);
  border: 1px solid var(--hire-border);
  color: var(--hire-text-title);
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.hire-input:focus,
.hire-select:focus,
.hire-textarea:focus {
  outline: none;
  background-color: var(--hire-bg-surface);
  border-color: var(--hire-accent);
  box-shadow: 0 0 0 4px rgba(var(--hire-accent-rgb), 0.15);
}

.hire-chip-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.hire-chip-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.hire-chip-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 9999px;
  font-size: 0.88rem;
  font-weight: 500;
  border: 1px solid var(--hire-border);
  background: var(--hire-bg-subtle);
  color: var(--hire-text-body);
  cursor: pointer;
  transition: all 0.2s ease;
  user-select: none;
}

.hire-chip-label:hover {
  border-color: var(--hire-text-title);
}

.hire-chip-input:checked + .hire-chip-label {
  background: var(--hire-text-title);
  border-color: var(--hire-text-title);
  color: var(--hire-bg-body) !important;
  font-weight: 600;
}

.hire-chip-input:focus-visible + .hire-chip-label {
  outline: 2px solid var(--hire-accent);
  outline-offset: 2px;
}

/* ==========================================================================
   TIMELINE STEPS
   ========================================================================== */
.hire-step-badge {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: var(--hire-bg-subtle);
  border: 1px solid var(--hire-border);
  color: var(--hire-text-title);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  font-weight: 800;
  margin-bottom: 20px;
  transition: all 0.2s ease;
}

.hire-card:hover .hire-step-badge {
  background: var(--hire-accent);
  border-color: var(--hire-accent);
  color: #ffffff;
}

/* ==========================================================================
   ACCORDION (FAQ)
   ========================================================================== */
.hire-accordion-item {
  background: var(--hire-bg-surface);
  border: 1px solid var(--hire-border);
  border-radius: 14px;
  margin-bottom: 12px;
  overflow: hidden;
  transition: border-color 0.2s ease;
}

.hire-accordion-item:hover {
  border-color: rgba(var(--hire-accent-rgb), 0.4);
}

.hire-accordion-btn {
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  padding: 20px 24px;
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--hire-text-title);
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  gap: 16px;
}

.hire-accordion-btn:focus-visible {
  outline: 2px solid var(--hire-accent);
  outline-offset: -2px;
}

.hire-accordion-icon {
  font-size: 1.25rem;
  transition: transform 0.25s ease;
  color: var(--hire-text-muted);
}

.hire-accordion-btn:not(.collapsed) .hire-accordion-icon {
  transform: rotate(180deg);
  color: var(--hire-accent);
}

.hire-accordion-body {
  padding: 0 24px 22px;
  color: var(--hire-text-body);
  font-size: 0.96rem;
  line-height: 1.65;
}

/* ==========================================================================
   FINAL CTA BANNER
   ========================================================================== */
.hire-final-cta-card {
  background: linear-gradient(135deg, #090d16 0%, #161e31 100%);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 28px;
  padding: 64px 36px;
  text-align: center;
  color: #ffffff;
  position: relative;
  overflow: hidden;
  box-shadow: var(--hire-shadow-lg);
}

.hire-final-cta-card h2 {
  color: #ffffff;
}

.hire-final-cta-card p {
  color: #94a3b8;
}
</style>

{{-- JSON-LD STRUCTURED DATA FOR SEARCH ENGINES (SEO) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "ProfessionalService",
      "@id": "{{ url('hire') }}#service",
      "name": "Custom AI Product Photoshoots by ImagineBuddy",
      "url": "{{ url('hire') }}",
      "description": "{{ $hireSettings->meta_description ?? 'Custom AI-assisted product photoshoot production with human commercial refinement for D2C e-commerce brands.' }}",
      "areaServed": "Global",
      "priceRange": "$$$",
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "AI Product Photography Services",
        "itemListElement": [
          {
            "@type": "Offer",
            "name": "Single SKU Custom Photoshoot Package",
            "price": "{{ $hireSettings->price_1_sku_usd }}",
            "priceCurrency": "USD",
            "description": "8 art-directed visual setups per SKU, multi-ratio formats, 24h delivery guarantee."
          },
          {
            "@type": "Offer",
            "name": "Multi-SKU Growth Photoshoot Package",
            "price": "{{ $hireSettings->price_3_5_sku_usd }}",
            "priceCurrency": "USD",
            "description": "Volume tier for 3 to 5 SKUs with dedicated creative direction."
          }
        ]
      }
    },
    {
      "@type": "FAQPage",
      "@id": "{{ url('hire') }}#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "What is a custom AI product photoshoot?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "A custom AI product photoshoot uses AI-assisted visual production paired with human art direction to develop distinctive commercial imagery based on an agreed brief, without shipping physical samples or renting photo studios."
          }
        },
        {
          "@type": "Question",
          "name": "How is a custom project different from the standard visual package?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Our standard package provides eight established visual setups per SKU under fixed parameters. A custom photoshoot is tailored to your specific campaign concepts, bespoke art direction, custom aspect ratios, or unique deliverable volumes."
          }
        },
        {
          "@type": "Question",
          "name": "How is 100% product fidelity guaranteed?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "We start with your real product packshot reference and apply human quality control to details such as logo typography, bottle geometry, textures, and edges, with rework and refund commitments under our guarantee."
          }
        }
      ]
    },
    {
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
          "name": "Hire Us",
          "item": "{{ url('hire') }}"
        }
      ]
    }
  ]
}
</script>
@endsection

@section('content')
<div class="hire-page">

  {{-- 1. HERO SECTION --}}
  <header class="hire-hero-section">
    <div class="hire-container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hire-eyebrow-badge">
            <span class="hire-pulse-dot"></span>
            <span>{{ $hireSettings->hero_eyebrow ?? 'CUSTOM AI PRODUCT PHOTOSHOOTS' }}</span>
          </div>

          <h1 class="hire-title-lg mb-3">
            Your Product. Your Vision.<br>
            <span class="hire-serif-accent">A Campaign Built Around Your Brand.</span>
          </h1>

          <p class="hire-lead mb-4">
            {{ $hireSettings->hero_subheadline ?? 'Need product visuals that go beyond standard catalog photography? Hire us to create custom AI-assisted product photoshoots tailored to your brand, creative direction, and campaign goals. From premium studio-style imagery to imaginative campaign concepts, we transform your product references into distinctive visuals for your brand.' }}
          </p>

          <div class="d-flex flex-wrap gap-3 align-items-center mb-4">
            <a href="#inquiry-form" class="hire-btn-primary">
              <span>Request a Custom Photoshoot</span>
              <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#portfolio" class="hire-btn-secondary">
              <span>Explore Our Work</span>
              <i class="bi bi-arrow-down"></i>
            </a>
          </div>

          <div class="hire-trust-strip">
            <div class="hire-trust-item">
              <i class="bi bi-shield-check"></i>
              <span>100% Product Fidelity Guarantee</span>
            </div>
            <div class="hire-trust-item">
              <i class="bi bi-lightning-charge"></i>
              <span>24h Turnaround Pipeline</span>
            </div>
            <div class="hire-trust-item">
              <i class="bi bi-box-seam"></i>
              <span>Zero Physical Shipping</span>
            </div>
            <div class="hire-trust-item">
              <i class="bi bi-person-check"></i>
              <span>Human Commercial Retouching</span>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="hire-hero-showcase">
            <!-- Header: See the Difference Yourself -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 hire-showcase-header">
              <div>
                <div class="transformation-eyebrow text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em; color: var(--color-default, #00d690);">
                  <span class="eyebrow-dash me-1">—</span> THE TRANSFORMATION
                </div>
                <h3 class="transformation-heading h5 fw-bold title-custom mb-0">
                  See the <span class="font-serif-italic">Difference Yourself</span>
                </h3>
              </div>
              <span class="badge rounded-pill badge-campaign-ready px-2.5 py-1.5 fw-bold shadow-xs" style="font-size: 0.75rem;">
                <i class="bi bi-sliders me-1"></i> Drag to Compare
              </span>
            </div>

            <!-- Interactive Before / After Split Slider -->
            <div class="before-after-slider-container shadow-sm position-relative overflow-hidden rounded-4 mt-3" id="transformationSlider">
              
              <!-- Background: Campaign Ready Image (After) -->
              <img 
                src="{{ asset('public/img/premium-white-oud-perfume-ad-02.webp') }}" 
                alt="Campaign Ready Commercial Visual" 
                class="before-after-img after-img w-100 h-100" 
                draggable="false"
                loading="eager"
                fetchpriority="high"
              >

              <!-- Foreground: Raw Snapshot Image (Before) -->
              <div class="before-img-clip position-absolute top-0 start-0 w-100 h-100 overflow-hidden" id="beforeImgClip">
                <img 
                  src="{{ asset('public/img/White-Oud-Perfume-01.webp') }}" 
                  alt="Raw Desk Snapshot" 
                  class="before-after-img before-img position-absolute top-0 start-0 w-100 h-100" 
                  draggable="false"
                  loading="eager"
                >
              </div>

              <!-- Floating Pill Badges -->
              <div class="slider-badge slider-badge-before position-absolute">
                <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-semibold shadow-sm">
                  <i class="bi bi-camera me-1"></i> Raw Snapshot
                </span>
              </div>
              <div class="slider-badge slider-badge-after position-absolute">
                <span class="badge rounded-pill badge-campaign-ready px-3 py-2 fw-bold shadow-sm">
                  <i class="bi bi-stars me-1"></i> Campaign Ready
                </span>
              </div>

              <!-- Draggable Vertical Divider & Handle -->
              <div class="slider-divider position-absolute top-0 bottom-0" id="sliderDivider">
                <div 
                  class="slider-handle shadow" 
                  id="sliderHandle" 
                  tabindex="0" 
                  role="slider" 
                  aria-valuenow="50" 
                  aria-valuemin="0" 
                  aria-valuemax="100" 
                  aria-label="Before and after transformation slider"
                >
                  <i class="bi bi-arrow-left-right text-dark"></i>
                </div>
              </div>

              <!-- Accessible hidden native range slider -->
              <input 
                type="range" 
                min="0" 
                max="100" 
                value="50" 
                class="visually-hidden" 
                id="sliderAccessibleRange" 
                aria-label="Before after split percentage"
              >
            </div>

            <!-- Footer Microcopy -->
            <div class="d-flex align-items-center justify-content-between px-2 pt-2.5 pb-0 text-muted small" style="font-size: 0.78rem;">
              <span><i class="bi bi-camera me-1 text-muted"></i> Raw packshot reference</span>
              <span class="fw-medium text-center d-none d-sm-inline" style="color: var(--color-default, #00d690);"><i class="bi bi-arrow-left-right me-1"></i> Drag to inspect detail</span>
              <span><i class="bi bi-stars me-1 text-warning"></i> Studio campaign grade</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  {{-- 2. CUSTOM PHOTOSHOOT SERVICE INTRODUCTION --}}
  <section class="hire-section border-top">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-award me-1"></i> Tailored Production
        </span>
        <h2 class="hire-title-md mb-3">
          {{ $hireSettings->service_intro_title ?? 'More Than Product Images. A Creative Direction Built for Your Brand.' }}
        </h2>
        <p class="hire-lead mb-0">
          {{ $hireSettings->service_intro_subtitle ?? 'Every product and brand has a different story to tell. Our custom AI product photoshoots are designed for brands that need a specific visual direction, a distinctive campaign concept, or a more tailored approach to product photography.' }}
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <article class="hire-card h-100">
            <div class="hire-step-badge">01</div>
            <h3 class="hire-title-sm mb-3">Custom Art Direction</h3>
            <p class="text-muted mb-0">
              Visual concepts, lighting, backgrounds, props, and compositions tailored to your brand identity, product aesthetics, and target audience.
            </p>
          </article>
        </div>

        <div class="col-md-4">
          <article class="hire-card h-100">
            <div class="hire-step-badge">02</div>
            <h3 class="hire-title-sm mb-3">AI-Assisted Visual Production</h3>
            <p class="text-muted mb-0">
              Create imaginative product campaign environments and distinctive visual directions without relying exclusively on a conventional physical photoshoot.
            </p>
          </article>
        </div>

        <div class="col-md-4">
          <article class="hire-card h-100">
            <div class="hire-step-badge">03</div>
            <h3 class="hire-title-sm mb-3">Human Refinement</h3>
            <p class="text-muted mb-0">
              Every final visual undergoes commercial-grade retouching to inspect typography, geometry, textures, lighting, and edges to meet agreed quality requirements.
            </p>
          </article>
        </div>
      </div>
    </div>
  </section>

  {{-- 3. PORTFOLIO & CREATIVE DIRECTIONS GALLERY --}}
  <section class="hire-section border-top" id="portfolio">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-grid-3x3-gap me-1"></i> Creative Portfolio
        </span>
        <h2 class="hire-title-md mb-3">Your Product Can Tell a Bigger Visual Story.</h2>
        <p class="hire-lead mb-0">
          From clean studio packshots to expressive campaign concepts, explore the kinds of creative directions we can develop for your product.
        </p>
      </div>

      <div class="hire-filter-bar" role="tablist" aria-label="Portfolio filters">
        <button type="button" class="hire-filter-btn active" data-filter="all">All Styles</button>
        <button type="button" class="hire-filter-btn" data-filter="studio">Clean Studio</button>
        <button type="button" class="hire-filter-btn" data-filter="luxury">Luxury Editorial</button>
        <button type="button" class="hire-filter-btn" data-filter="lifestyle">Lifestyle & Nature</button>
        <button type="button" class="hire-filter-btn" data-filter="bold">Bold & Colorful</button>
      </div>

      <div class="row g-4" id="portfolioGrid">
        @forelse($showcaseImages->take(8) as $index => $item)
          @php
            $styles = ['studio', 'luxury', 'lifestyle', 'bold'];
            $styleCategory = $styles[$index % 4];
            $styleName = match($styleCategory) {
              'studio' => 'Clean Studio',
              'luxury' => 'Luxury Editorial',
              'lifestyle' => 'Natural Lifestyle',
              default => 'Bold & Vibrant'
            };
            $previewUrl = Storage::url(config('path.preview') . $item->preview);
          @endphp
          <div class="col-6 col-md-4 col-lg-3 portfolio-item" data-category="{{ $styleCategory }}">
            <div class="hire-gallery-card">
              <a href="{{ $previewUrl }}" class="glightbox" data-gallery="hire-gallery">
                <img src="{{ $previewUrl }}" alt="{{ $item->title }} - {{ $styleName }}" loading="lazy">
                <div class="hire-gallery-overlay">
                  <span class="hire-gallery-tag">{{ $styleName }}</span>
                </div>
              </a>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5 text-muted">
            <p>Portfolio examples are loading.</p>
          </div>
        @endforelse
      </div>

      <div class="text-center mt-5">
        <a href="{{ url('photoshoots') }}" class="hire-btn-secondary">
          <span>Explore All Studio Photoshoots</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  {{-- 4. WHAT CLIENTS CAN REQUEST (DELIVERABLES) --}}
  <section class="hire-section border-top bg-light-subtle">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-collection-play me-1"></i> Deliverable Types
        </span>
        <h2 class="hire-title-md mb-3">What Would You Like to Create?</h2>
        <p class="hire-lead mb-0">
          Whether you are launching a flagship SKU or refreshing your entire digital catalog, we tailor each asset to your campaign placements.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                <i class="bi bi-bullseye fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">Product Hero Images</h3>
            </div>
            <p class="text-muted small mb-0">
              High-impact visuals that put the product front and center with pristine studio lighting, crisp packaging reflections, and macro detail focus.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-success-subtle text-success">
                <i class="bi bi-house-door fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">Lifestyle Product Imagery</h3>
            </div>
            <p class="text-muted small mb-0">
              Product visuals placed naturally within carefully art-directed lifestyle environments, bathrooms, kitchens, vanity tables, and outdoor settings.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                <i class="bi bi-lightbulb fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">Campaign Concepts</h3>
            </div>
            <p class="text-muted small mb-0">
              Distinctive, bold creative directions crafted for product launches, brand story campaigns, and signature marketing collateral.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-info-subtle text-info">
                <i class="bi bi-bag-check fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">E-commerce Visuals</h3>
            </div>
            <p class="text-muted small mb-0">
              Clean product imagery engineered for high-converting Shopify product pages, Amazon A+ enhanced brand content, and digital storefronts.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                <i class="bi bi-megaphone fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">Paid Social Creative</h3>
            </div>
            <p class="text-muted small mb-0">
              Attention-grabbing visual concepts optimized for Instagram, Facebook, and TikTok ad placements in 9:16 vertical and 4:5 feed aspect ratios.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="p-2 rounded-3 bg-secondary-subtle text-secondary">
                <i class="bi bi-calendar-event fs-4"></i>
              </div>
              <h3 class="hire-title-sm mb-0">Seasonal Campaigns</h3>
            </div>
            <p class="text-muted small mb-0">
              Creative directions for product launches, holiday gifting periods, summer drops, and promotional calendar refreshes.
            </p>
          </div>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="#inquiry-form" class="hire-btn-primary">
          <span>Tell Us What You Have in Mind</span>
          <i class="bi bi-arrow-down"></i>
        </a>
      </div>
    </div>
  </section>

  {{-- 5. WHY HIRE US --}}
  <section class="hire-section border-top">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-check2-circle me-1"></i> The Hybrid Difference
        </span>
        <h2 class="hire-title-md mb-3">Creative Direction Meets AI-Assisted Production.</h2>
        <p class="hire-lead mb-0">
          Skip the frustrating trial-and-error of DIY generation tools and the exorbitant day rates of conventional photo studios.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <i class="bi bi-fingerprint fs-2 text-primary"></i>
              <h3 class="hire-title-sm mb-0">Built Around Your Brand</h3>
            </div>
            <p class="text-muted mb-0">
              We develop visual concepts based on your actual product packaging, brand identity, target demographic, and campaign objectives.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <i class="bi bi-compass fs-2 text-success"></i>
              <h3 class="hire-title-sm mb-0">More Room to Explore</h3>
            </div>
            <p class="text-muted mb-0">
              Explore varied visual environments, studio lighting styles, and artistic compositions according to the agreed brief without renting physical sets.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <i class="bi bi-speedometer2 fs-2 text-warning"></i>
              <h3 class="hire-title-sm mb-0">A Streamlined Workflow</h3>
            </div>
            <p class="text-muted mb-0">
              Share your brief and digital references through a single structured process instead of coordinating an unnecessarily complicated production chain.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="hire-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <i class="bi bi-gem fs-2 text-info"></i>
              <h3 class="hire-title-sm mb-0">Human-Guided Refinement</h3>
            </div>
            <p class="text-muted mb-0">
              Senior retouchers maintain absolute visual quality, precise label representation, and seamless alignment with the approved creative direction.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- 6. SIDE-BY-SIDE COMPARISON MATRIX --}}
  <section class="hire-section border-top bg-light-subtle">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-sliders me-1"></i> Clear Comparison
        </span>
        <h2 class="hire-title-md mb-3">Stop Choosing Between Studio Quality and AI Speed.</h2>
        <p class="hire-lead mb-0">
          How our hybrid creative production engine compares against conventional studios and DIY AI software.
        </p>
      </div>

      <div class="table-responsive">
        <table class="hire-matrix-table" aria-label="Production model comparison">
          <thead>
            <tr>
              <th scope="col" style="width: 25%;">Decision Factor</th>
              <th scope="col" style="width: 25%;">Traditional Photo Studio</th>
              <th scope="col" style="width: 25%;">DIY AI Generators</th>
              <th scope="col" class="hire-matrix-highlight-col" style="width: 25%;">Our Custom AI Photoshoot</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Turnaround Time</strong></td>
              <td class="text-muted">3 to 5 weeks</td>
              <td class="text-muted">Minutes (raw iterations)</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> 24h Delivery Commitment</td>
            </tr>
            <tr>
              <td><strong>Physical Samples</strong></td>
              <td class="text-muted">Physical shipping required</td>
              <td class="text-muted">No physical samples</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> Zero sample shipping needed</td>
            </tr>
            <tr>
              <td><strong>Brand & Label Accuracy</strong></td>
              <td class="text-muted">High (camera capture)</td>
              <td class="text-muted text-danger"><i class="bi bi-x-circle me-1"></i> Warped labels, gibberish text</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> 100% Product Fidelity Guarantee</td>
            </tr>
            <tr>
              <td><strong>Creative Variety</strong></td>
              <td class="text-muted">Restricted by set budget</td>
              <td class="text-muted">Chaotic variations</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> 8 art-directed bespoke setups</td>
            </tr>
            <tr>
              <td><strong>Human Refinement</strong></td>
              <td class="text-muted">Included in shoot fee</td>
              <td class="text-muted text-danger"><i class="bi bi-x-circle me-1"></i> None (unreviewed output)</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> Dedicated commercial retouchers</td>
            </tr>
            <tr>
              <td><strong>Omnichannel Ratios</strong></td>
              <td class="text-muted">Additional resize fees</td>
              <td class="text-muted">Single square output</td>
              <td class="hire-matrix-highlight-col"><i class="bi bi-check-circle-fill text-success me-1"></i> 9:16, 4:5, 1:1, 16:9 included</td>
            </tr>
            <tr>
              <td><strong>Starting Investment</strong></td>
              <td class="text-muted">$6,000–$12,000+</td>
              <td class="text-muted">Tool subscriptions ($30/mo)</td>
              <td class="hire-matrix-highlight-col"><strong>Starting at $699 / ₹54,999</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{-- 7. HOW IT WORKS (THE 4-STEP WORKFLOW) --}}
  <section class="hire-section border-top">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-arrow-repeat me-1"></i> Simple Workflow
        </span>
        <h2 class="hire-title-md mb-3">From Product Reference to Campaign-Ready Visuals.</h2>
        <p class="hire-lead mb-0">
          A predictable, human-perfected production process that keeps your campaign calendar on schedule.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="hire-card h-100">
            <div class="hire-step-badge">1</div>
            <h3 class="hire-title-sm mb-2">Share Your Brief</h3>
            <p class="text-muted small mb-0">
              Provide product packshot references, label materials, brand guidelines, and desired aesthetics through our intake brief.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="hire-card h-100">
            <div class="hire-step-badge">2</div>
            <h3 class="hire-title-sm mb-2">Align Direction</h3>
            <p class="text-muted small mb-0">
              We review your requirements, confirm the lighting, angles, and campaign environments, and lock in the turnaround clock.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="hire-card h-100">
            <div class="hire-step-badge">3</div>
            <h3 class="hire-title-sm mb-2">Create & Refine</h3>
            <p class="text-muted small mb-0">
              Our hybrid engine generates the visual environments, while senior retouchers inspect labels, geometry, and reflections.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="hire-card h-100">
            <div class="hire-step-badge">4</div>
            <h3 class="hire-title-sm mb-2">Deploy Assets</h3>
            <p class="text-muted small mb-0">
              Download your multi-ratio asset pack ready for immediate integration into ad accounts, Shopify stores, and marketing channels.
            </p>
          </div>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="#inquiry-form" class="hire-btn-primary">
          <span>Start Your Project Inquiry</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  {{-- 8. PRICING GUIDANCE (DYNAMIC GEOIP CURRENCY) --}}
  <section class="hire-section border-top bg-light-subtle" id="pricing-guidance">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-tag me-1"></i> Pricing Guidance
        </span>
        <h2 class="hire-title-md mb-3">Every Project Has Its Own Creative Requirements.</h2>
        <p class="hire-lead mb-3">
          Compare our published reference package rates with bespoke custom photoshoot options.
        </p>

        <!-- Dynamic Currency Toggle Pill -->
        <div class="d-inline-flex flex-column align-items-center">
          <div class="hire-currency-switch-box" role="group" aria-label="Currency Switcher">
            <button type="button" class="hire-currency-btn {{ $isIndia ? '' : 'active' }}" data-currency="USD" id="btnSwitchUSD" aria-label="Switch to US Dollars">
              <i class="bi bi-currency-dollar me-1"></i> USD ($)
            </button>
            <button type="button" class="hire-currency-btn {{ $isIndia ? 'active' : '' }}" data-currency="INR" id="btnSwitchINR" aria-label="Switch to Indian Rupees">
              <i class="bi bi-currency-rupee me-1"></i> INR (₹)
            </button>
          </div>
          <span class="small text-muted">
            <i class="bi bi-geo-alt me-1"></i> Detected visitor region: <strong>{{ $isIndia ? 'India (INR)' : 'International (USD)' }}</strong>
          </span>
        </div>
      </div>

      <div class="row g-4 justify-content-center">
        <!-- Option A: Standard Visual Package -->
        <div class="col-lg-5">
          <div class="hire-card h-100 p-4 p-xl-5 border-primary-subtle">
            <span class="badge bg-primary-subtle text-primary fw-bold mb-3 px-3 py-2 rounded-pill">Reference Package</span>
            <h3 class="hire-title-md fs-3 mb-2">Productized Visual Package</h3>
            <p class="text-muted small mb-4">
              Published reference rates for our established 8-setup package, designed for rapid production and standardized deliverables.
            </p>

            <div class="p-3 bg-light rounded-3 mb-4">
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                  <div class="fw-bold text-dark">1 SKU Single Launch</div>
                  <div class="f-size-12 text-muted">8 bespoke setups for one product</div>
                </div>
                <div class="text-end">
                  <span class="hire-price-tag fs-3 price-val" data-usd="${{ number_format($hireSettings->price_1_sku_usd, 0) }}" data-inr="₹{{ number_format($hireSettings->price_1_sku_inr, 0) }}">
                    {{ $isIndia ? '₹' . number_format($hireSettings->price_1_sku_inr, 0) : '$' . number_format($hireSettings->price_1_sku_usd, 0) }}
                  </span>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                  <div class="fw-bold text-dark">3–5 SKUs Growth Tier</div>
                  <div class="f-size-12 text-muted">Volume discount for catalog refreshes</div>
                </div>
                <div class="text-end">
                  <span class="hire-price-tag fs-3 price-val" data-usd="${{ number_format($hireSettings->price_3_5_sku_usd, 0) }}" data-inr="₹{{ number_format($hireSettings->price_3_5_sku_inr, 0) }}">
                    {{ $isIndia ? '₹' . number_format($hireSettings->price_3_5_sku_inr, 0) : '$' . number_format($hireSettings->price_3_5_sku_usd, 0) }}
                  </span>
                  <span class="hire-price-unit">/ SKU</span>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center py-2">
                <div>
                  <div class="fw-bold text-dark">6+ SKUs Scale Tier</div>
                  <div class="f-size-12 text-muted">Large collections & recurring pipeline</div>
                </div>
                <div class="text-end">
                  <span class="hire-price-tag fs-3 price-val" data-usd="${{ number_format($hireSettings->price_6_plus_sku_usd, 0) }}" data-inr="₹{{ number_format($hireSettings->price_6_plus_sku_inr, 0) }}">
                    {{ $isIndia ? '₹' . number_format($hireSettings->price_6_plus_sku_inr, 0) : '$' . number_format($hireSettings->price_6_plus_sku_usd, 0) }}
                  </span>
                  <span class="hire-price-unit">/ SKU</span>
                </div>
              </div>
            </div>

            <ul class="list-unstyled text-muted small mb-0">
              <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> 8 art-directed visual setups per SKU</li>
              <li class="mb-2"><i class="bi bi-check-lg text-success me-2"></i> Omnichannel multi-ratio pack (9:16, 4:5, 1:1, 16:9)</li>
              <li class="mb-0"><i class="bi bi-check-lg text-success me-2"></i> 100% Product Fidelity Guarantee & 24h Clock</li>
            </ul>
          </div>
        </div>

        <!-- Option B: Custom AI Photoshoot -->
        <div class="col-lg-5">
          <div class="hire-card h-100 p-4 p-xl-5 border-dark shadow-sm">
            <span class="badge bg-dark text-white fw-bold mb-3 px-3 py-2 rounded-pill">Custom Production</span>
            <h3 class="hire-title-md fs-3 mb-2">Custom AI Photoshoot</h3>
            <p class="text-muted small mb-4">
              {{ $hireSettings->custom_quote_text ?? 'Custom quote based on your brief, creative direction, deliverables, and project scope.' }}
            </p>

            <div class="p-4 bg-light rounded-3 mb-4 text-center">
              <span class="text-muted small text-uppercase fw-bold d-block mb-1">Tailored Project Scope</span>
              <div class="hire-title-md fs-2 text-dark mb-2">Bespoke Quote</div>
              <p class="small text-muted mb-0">
                Ideal for complex multi-product campaign stories, unique prop styling, non-standard deliverable counts, or specialized artistic concepts.
              </p>
            </div>

            <ul class="list-unstyled text-muted small mb-4">
              <li class="mb-2"><i class="bi bi-stars text-warning me-2"></i> Fully customized art direction & set designs</li>
              <li class="mb-2"><i class="bi bi-stars text-warning me-2"></i> Flexible deliverables & non-standard aspect ratios</li>
              <li class="mb-2"><i class="bi bi-stars text-warning me-2"></i> Dedicated account review & collaborative iterations</li>
            </ul>

            <a href="#inquiry-form" class="hire-btn-primary w-100">
              <span>Request a Custom Quote</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- 9. CUSTOM PROJECT INQUIRY FORM --}}
  <section class="hire-section border-top" id="inquiry-form">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-envelope-paper me-1"></i> Start Your Project
        </span>
        <h2 class="hire-title-md mb-3">Let's Create Something for Your Brand.</h2>
        <p class="hire-lead mb-0">
          Tell us about your product and the visuals you have in mind. Share your creative brief, and we will follow up with scoping and recommended next steps.
        </p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
          <div class="hire-form-section">
            
            <!-- Success Confirmation Screen -->
            <div id="formSuccessAlert" class="text-center py-4 d-none" role="alert">
              <div class="p-3 bg-success-subtle text-success rounded-circle d-inline-flex mb-3">
                <i class="bi bi-check2 fs-1"></i>
              </div>
              <h3 class="hire-title-sm fs-4 mb-2">Thanks for sharing your project.</h3>
              <p class="text-muted mb-4" id="formSuccessMessage">
                Your inquiry has been submitted successfully. We'll review your brief and follow up using the contact information you provided within 1 business day.
              </p>
              <div class="p-4 bg-light rounded-3 text-start small mb-4">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1 text-primary"></i> What Happens Next?</h6>
                <ol class="mb-0 ps-3 text-muted">
                  <li class="mb-1">Our creative director reviews your product references and campaign goals.</li>
                  <li class="mb-1">We determine the recommended photoshoot scope, visual setups, and timeline.</li>
                  <li>We send a tailored proposal and schedule an alignment discussion if needed.</li>
                </ol>
              </div>
              <button type="button" class="hire-btn-secondary" onclick="location.reload();">Submit Another Inquiry</button>
            </div>

            <!-- Error Alert -->
            <div id="formErrorAlert" class="alert alert-danger d-none mb-4" role="alert"></div>

            <form id="hireProjectForm" method="POST" action="{{ url('hire/inquiry') }}" novalidate>
              @csrf
              <!-- Anti-spam honeypot -->
              <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off" aria-hidden="true">
              <input type="hidden" name="currency" id="hiddenFormCurrency" value="{{ $isIndia ? 'INR' : 'USD' }}">

              <!-- Group 1: Contact Information -->
              <div class="hire-form-group-title">
                <span class="hire-form-group-num">1</span>
                <span>Contact Information</span>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label for="fullNameInput" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                  <input type="text" id="fullNameInput" name="full_name" class="hire-input" placeholder="e.g. Jane Doe" required>
                </div>

                <div class="col-md-6">
                  <label for="emailInput" class="form-label small fw-bold">Work Email <span class="text-danger">*</span></label>
                  <input type="email" id="emailInput" name="email" class="hire-input" placeholder="jane@yourbrand.com" required>
                </div>

                <div class="col-md-6">
                  <label for="companyInput" class="form-label small fw-bold">Brand or Company Name <span class="text-danger">*</span></label>
                  <input type="text" id="companyInput" name="company_name" class="hire-input" placeholder="e.g. Aura Botanicals" required>
                </div>

                <div class="col-md-6">
                  <label for="websiteInput" class="form-label small fw-bold">Website or Store URL <span class="text-muted">(Optional)</span></label>
                  <input type="url" id="websiteInput" name="website_url" class="hire-input" placeholder="https://yourbrand.com">
                </div>
              </div>

              <!-- Group 2: Project Scope -->
              <div class="hire-form-group-title">
                <span class="hire-form-group-num">2</span>
                <span>Project Scope & Specifications</span>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label for="productCategorySelect" class="form-label small fw-bold">Product Category <span class="text-danger">*</span></label>
                  <select id="productCategorySelect" name="product_category" class="hire-select" required>
                    <option value="" disabled selected>Select your product category...</option>
                    @php
                      $categories = is_array($hireSettings->form_categories) && count($hireSettings->form_categories) > 0 ? $hireSettings->form_categories : [
                        'Skincare & Cosmetics', 'Beauty & Grooming', 'Wellness & Supplements',
                        'Food & Beverage', 'Fragrance & Perfumery', 'Lifestyle Products',
                        'Consumer Goods', 'Other'
                      ];
                    @endphp
                    @foreach($categories as $cat)
                      <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="skuCountSelect" class="form-label small fw-bold">Number of Products / SKUs <span class="text-danger">*</span></label>
                  <select id="skuCountSelect" name="sku_count" class="hire-select" required>
                    <option value="" disabled selected>Select number of SKUs...</option>
                    <option value="1 SKU">1 SKU Single Launch</option>
                    <option value="2–5 SKUs">2–5 SKUs Growth Tier</option>
                    <option value="6–10 SKUs">6–10 SKUs Scale Tier</option>
                    <option value="10+ SKUs">10+ SKUs Full Catalog</option>
                    <option value="Ongoing monthly production">Ongoing Monthly Retainer</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label small fw-bold d-block mb-2">Type of Photoshoot <span class="text-danger">*</span></label>
                  <div class="hire-chip-wrap">
                    @php
                      $shootTypes = is_array($hireSettings->form_shoot_types) && count($hireSettings->form_shoot_types) > 0 ? $hireSettings->form_shoot_types : [
                        'Product hero imagery', 'Lifestyle imagery', 'Campaign concepts',
                        'E-commerce imagery', 'Paid social creative', 'Seasonal campaign', 'Other / Not sure yet'
                      ];
                    @endphp
                    @foreach($shootTypes as $idx => $st)
                      <div>
                        <input class="hire-chip-input" type="checkbox" name="photoshoot_types[]" value="{{ $st }}" id="st_chip_{{ $idx }}" @checked($loop->first)>
                        <label class="hire-chip-label" for="st_chip_{{ $idx }}">
                          <i class="bi bi-check-lg" style="font-size: 0.8rem;"></i> {{ $st }}
                        </label>
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label small fw-bold d-block mb-2">Intended Placements & Channels <span class="text-muted">(Optional)</span></label>
                  <div class="hire-chip-wrap">
                    @php
                      $intendedUses = is_array($hireSettings->form_intended_uses) && count($hireSettings->form_intended_uses) > 0 ? $hireSettings->form_intended_uses : [
                        'E-commerce product pages', 'Paid advertising (Meta, TikTok)', 'Organic social media',
                        'Website & landing pages', 'Product launch', 'Print / Collateral', 'Other'
                      ];
                    @endphp
                    @foreach($intendedUses as $idx => $iu)
                      <div>
                        <input class="hire-chip-input" type="checkbox" name="intended_use[]" value="{{ $iu }}" id="iu_chip_{{ $idx }}">
                        <label class="hire-chip-label" for="iu_chip_{{ $idx }}">
                          <i class="bi bi-plus-lg" style="font-size: 0.8rem;"></i> {{ $iu }}
                        </label>
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="preferredDirectionInput" class="form-label small fw-bold">Preferred Visual Style <span class="text-muted">(Optional)</span></label>
                  <input type="text" id="preferredDirectionInput" name="preferred_direction" class="hire-input" placeholder="e.g. Minimalist warm sculptural, organic outdoor">
                </div>

                <div class="col-md-6">
                  <label for="dynamicBudgetSelect" class="form-label small fw-bold">Project Budget Range <span class="text-muted">(Optional)</span></label>
                  <select id="dynamicBudgetSelect" name="budget_range" class="hire-select">
                    <option value="">Select estimated budget...</option>
                    <!-- Populated dynamically via JS matching USD/INR -->
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="deliveryDateInput" class="form-label small fw-bold">Desired Delivery Date <span class="text-muted">(Optional)</span></label>
                  <input type="date" id="deliveryDateInput" name="desired_delivery_date" class="hire-input" min="{{ date('Y-m-d') }}">
                </div>
              </div>

              <!-- Group 3: Creative Brief -->
              <div class="hire-form-group-title">
                <span class="hire-form-group-num">3</span>
                <span>Creative Brief & Reference Links</span>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label for="projectDescTextarea" class="form-label small fw-bold">Project Description & Creative Goals <span class="text-danger">*</span></label>
                  <textarea id="projectDescTextarea" name="project_description" rows="4" class="hire-textarea" placeholder="Tell us about your product, what visual aesthetic you envision, any specific background props/lighting requirements, and what makes your product unique..." required minlength="10"></textarea>
                  <div class="form-text small text-muted">Please describe your product and campaign goals in at least a few sentences.</div>
                </div>

                <div class="col-12">
                  <label for="refLinksInput" class="form-label small fw-bold">Reference Links <span class="text-muted">(Google Drive, Dropbox, Pinterest, or Figma moodboards)</span></label>
                  <input type="text" id="refLinksInput" name="reference_links" class="hire-input" placeholder="https://drive.google.com/... or moodboard link">
                </div>

                <div class="col-12">
                  <label for="additionalNotesTextarea" class="form-label small fw-bold">Additional Notes <span class="text-muted">(Optional)</span></label>
                  <textarea id="additionalNotesTextarea" name="additional_notes" rows="2" class="hire-textarea" placeholder="Any additional questions, timing requirements, or notes for our creative team..."></textarea>
                </div>
              </div>

              <div class="pt-3">
                <button type="submit" id="submitInquiryBtn" class="hire-btn-primary w-100 py-3 fs-5">
                  <span id="submitInquiryLabel">Submit Project Inquiry <i class="bi bi-arrow-right ms-1"></i></span>
                  <span id="submitInquirySpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
              </div>

              <div class="text-center text-muted small mt-3">
                <i class="bi bi-lock-fill me-1 text-success"></i> 
                We respect your confidentiality. Your creative references and brand briefs are kept strictly private.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- 10. FREQUENTLY ASKED QUESTIONS --}}
  <section class="hire-section border-top">
    <div class="hire-container">
      <div class="hire-section-header">
        <span class="hire-eyebrow-badge">
          <i class="bi bi-patch-question me-1"></i> FAQ
        </span>
        <h2 class="hire-title-md mb-3">Frequently Asked Questions</h2>
        <p class="hire-lead mb-0">
          Everything you need to know about our custom photoshoot service, guarantees, and deliverables.
        </p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="accordion" id="hireFaqAccordionList">

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem1" aria-expanded="true" aria-controls="faqItem1">
                  <span>What is a custom AI product photoshoot?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem1" class="collapse show" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  A custom AI product photoshoot uses AI-assisted visual production paired with human art direction to develop distinctive product imagery based on an agreed brief. Instead of shipping products to a physical photo studio, we build tailored environments, lighting, and compositions around your digital product packshot references.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem2" aria-expanded="false" aria-controls="faqItem2">
                  <span>How is a custom project different from the standard visual package?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem2" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Our standard package provides eight established visual setups per SKU under fixed parameters. A custom photoshoot is tailored to your specific campaign concepts, bespoke art direction, custom aspect ratios, complex set requirements, or non-standard deliverable volumes. Standard package pricing does not automatically apply to custom projects.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem3" aria-expanded="false" aria-controls="faqItem3">
                  <span>What do I need to provide?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem3" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  All you need to provide are high-resolution packshots of your physical product (front, angle, back), accurate label artwork or vector assets if available, brand guidelines, and visual references or moodboards that show the aesthetic you want to achieve. No physical sample shipping is necessary.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem4" aria-expanded="false" aria-controls="faqItem4">
                  <span>Can I request a specific visual style?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem4" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Yes. In your inquiry brief, describe your desired aesthetic—such as warm sculptural minimalism, moody luxury editorial, bold pops of color, or natural outdoor sunlight—and include reference links. We align on visual references during project scoping before production begins.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem5" aria-expanded="false" aria-controls="faqItem5">
                  <span>How much does a custom photoshoot cost?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem5" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Custom pricing depends on the agreed creative complexity, number of products/SKUs, specific deliverables, and timeline requirements. We provide a clear, upfront quote after reviewing your project brief so you know the investment before starting.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem6" aria-expanded="false" aria-controls="faqItem6">
                  <span>How long does a project take?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem6" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Standard packages feature defined turnaround commitments once the brief is approved. For custom projects, the production schedule depends on project scope, SKU count, and revision requirements, which are confirmed before commissioning the work.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem7" aria-expanded="false" aria-controls="faqItem7">
                  <span>Can I use the visuals for commercial purposes?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem7" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Yes. Full commercial usage rights for paid advertising campaigns, e-commerce storefronts, Amazon A+ listings, social placements, and marketing materials are included upon final delivery.
                </div>
              </div>
            </div>

            <div class="hire-accordion-item">
              <h3 class="mb-0">
                <button class="hire-accordion-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem8" aria-expanded="false" aria-controls="faqItem8">
                  <span>Can you help if I am unsure what kind of visuals I need?</span>
                  <i class="bi bi-chevron-down hire-accordion-icon"></i>
                </button>
              </h3>
              <div id="faqItem8" class="collapse" data-bs-parent="#hireFaqAccordionList">
                <div class="hire-accordion-body">
                  Yes. Many clients simply share their current packaging photos and brand goals. Our creative team will review your product and recommend tailored creative directions and compositions during initial scoping.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- 11. FINAL CTA BANNER --}}
  <section class="hire-section">
    <div class="hire-container">
      <div class="hire-final-cta-card">
        <h2 class="hire-title-md fs-2 mb-3">Have a Product. Have an Idea. Let's Build the Visuals.</h2>
        <p class="hire-lead max-w-700 mx-auto mb-4">
          Whether you have a detailed campaign concept or want to explore what is possible for your product, start by sharing your project details.
        </p>
        <div class="d-inline-flex gap-3 flex-wrap justify-content-center">
          <a href="#inquiry-form" class="btn btn-light btn-lg rounded-pill px-4 py-3 fw-bold text-dark shadow animate-up-2">
            Request Your Custom Photoshoot <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Lightbox with GLightbox
    if (typeof GLightbox !== 'undefined') {
        GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true
        });
    }

    // 2. Portfolio Category Filter
    const filterBtns = document.querySelectorAll('.hire-filter-btn');
    const portfolioCards = document.querySelectorAll('.portfolio-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');
            portfolioCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 3. Dynamic Currency System (USD vs INR)
    const budgetsUsdList = @json($budgetsUsd);
    const budgetsInrList = @json($budgetsInr);
    
    const budgetSelectEl = document.getElementById('dynamicBudgetSelect');
    const hiddenCurrencyEl = document.getElementById('hiddenFormCurrency');
    const priceDisplayElements = document.querySelectorAll('.price-val');
    const btnUSD = document.getElementById('btnSwitchUSD');
    const btnINR = document.getElementById('btnSwitchINR');

    function populateBudgetOptions(options) {
        if (!budgetSelectEl) return;
        budgetSelectEl.innerHTML = '<option value="">Select estimated budget...</option>';
        options.forEach(opt => {
            const optElement = document.createElement('option');
            optElement.value = opt;
            optElement.textContent = opt;
            budgetSelectEl.appendChild(optElement);
        });
    }

    function switchActiveCurrency(currencyCode) {
        if (currencyCode === 'INR') {
            btnINR?.classList.add('active');
            btnUSD?.classList.remove('active');
            if (hiddenCurrencyEl) hiddenCurrencyEl.value = 'INR';

            priceDisplayElements.forEach(el => {
                el.textContent = el.getAttribute('data-inr');
            });
            populateBudgetOptions(budgetsInrList);
        } else {
            btnUSD?.classList.add('active');
            btnINR?.classList.remove('active');
            if (hiddenCurrencyEl) hiddenCurrencyEl.value = 'USD';

            priceDisplayElements.forEach(el => {
                el.textContent = el.getAttribute('data-usd');
            });
            populateBudgetOptions(budgetsUsdList);
        }
    }

    // Initialize with GeoIP detection from backend
    const defaultCurrency = '{{ $isIndia ? "INR" : "USD" }}';
    switchActiveCurrency(defaultCurrency);

    btnUSD?.addEventListener('click', () => switchActiveCurrency('USD'));
    btnINR?.addEventListener('click', () => switchActiveCurrency('INR'));

    // 4. Interactive Inquiry Form Submission
    const inquiryForm = document.getElementById('hireProjectForm');
    const submitBtn = document.getElementById('submitInquiryBtn');
    const submitLabel = document.getElementById('submitInquiryLabel');
    const submitSpinner = document.getElementById('submitInquirySpinner');
    const successAlert = document.getElementById('formSuccessAlert');
    const errorAlert = document.getElementById('formErrorAlert');

    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Check HTML5 validity
            if (!inquiryForm.checkValidity()) {
                inquiryForm.reportValidity();
                return;
            }

            // Lock submit button
            submitBtn.disabled = true;
            submitLabel.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            errorAlert.innerHTML = '';

            const formData = new FormData(inquiryForm);

            fetch(inquiryForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, data: data })))
            .then(({ status, data }) => {
                submitBtn.disabled = false;
                submitLabel.classList.remove('d-none');
                submitSpinner.classList.add('d-none');

                if (status === 200 && data.success) {
                    inquiryForm.classList.add('d-none');
                    successAlert.classList.remove('d-none');
                    if (data.message) {
                        document.getElementById('formSuccessMessage').textContent = data.message;
                    }
                    successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    let errHtml = '<ul class="mb-0 ps-3">';
                    if (data.errors) {
                        for (const k in data.errors) {
                            data.errors[k].forEach(errText => {
                                errHtml += `<li>${errText}</li>`;
                            });
                        }
                    } else {
                        errHtml += `<li>${data.message || 'An error occurred while submitting your inquiry. Please review the fields and try again.'}</li>`;
                    }
                    errHtml += '</ul>';

                    errorAlert.innerHTML = errHtml;
                    errorAlert.classList.remove('d-none');
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitLabel.classList.remove('d-none');
                submitSpinner.classList.add('d-none');

                errorAlert.innerHTML = 'A network error occurred while submitting your brief. Please try again or contact us directly.';
                errorAlert.classList.remove('d-none');
            });
        });
    }

    // 5. Before & After Transformation Slider Initialization
    (function initTransformationSlider() {
        const container = document.getElementById('transformationSlider');
        const clip = document.getElementById('beforeImgClip');
        const divider = document.getElementById('sliderDivider');
        const handle = document.getElementById('sliderHandle');
        const rangeInput = document.getElementById('sliderAccessibleRange');

        if (!container || !clip || !divider) return;

        let isDragging = false;

        function updateSlider(percent) {
            const clamped = Math.max(0, Math.min(100, percent));
            clip.style.clipPath = `inset(0 ${100 - clamped}% 0 0)`;
            divider.style.left = `${clamped}%`;

            if (handle) {
                handle.setAttribute('aria-valuenow', Math.round(clamped));
            }
            if (rangeInput && rangeInput.value != clamped) {
                rangeInput.value = clamped;
            }
        }

        function getPercentFromEvent(e) {
            const rect = container.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const x = clientX - rect.left;
            return (x / rect.width) * 100;
        }

        function onPointerDown(e) {
            isDragging = true;
            container.classList.add('is-dragging');
            updateSlider(getPercentFromEvent(e));
            if (e.pointerId && container.setPointerCapture) {
                try { container.setPointerCapture(e.pointerId); } catch(err) {}
            }
        }

        function onPointerMove(e) {
            if (!isDragging) return;
            requestAnimationFrame(() => {
                updateSlider(getPercentFromEvent(e));
            });
        }

        function onPointerUp(e) {
            if (!isDragging) return;
            isDragging = false;
            container.classList.remove('is-dragging');
            if (e.pointerId && container.releasePointerCapture) {
                try { container.releasePointerCapture(e.pointerId); } catch(err) {}
            }
        }

        container.addEventListener('pointerdown', onPointerDown);
        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', onPointerUp);
        window.addEventListener('pointercancel', onPointerUp);

        if (handle) {
            handle.addEventListener('keydown', function (e) {
                let current = parseFloat(handle.getAttribute('aria-valuenow') || '50');
                if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    updateSlider(current - 5);
                } else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    updateSlider(current + 5);
                } else if (e.key === 'Home') {
                    e.preventDefault();
                    updateSlider(0);
                } else if (e.key === 'End') {
                    e.preventDefault();
                    updateSlider(100);
                }
            });
        }

        if (rangeInput) {
            rangeInput.addEventListener('input', function () {
                updateSlider(parseFloat(this.value));
            });
        }

        updateSlider(50);
    })();
});
</script>
@endsection
