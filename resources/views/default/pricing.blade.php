@extends('layouts.app')

@section('title') {{ __('misc.pricing') }} - @endsection

@section('css')
<style type="text/css">
:root {
  --pricing-bg: transparent;
  --pricing-card-bg: #ffffff;
  --pricing-card-border: #e2e8f0;
  --pricing-pro-card-bg: #ffffff;
  --pricing-pro-card-border: #0f172a;
  --pricing-pro-badge-bg: #0f172a;
  --pricing-pro-badge-text: #ffffff;
  --pricing-text-primary: #0f172a;
  --pricing-text-secondary: #334155;
  --pricing-text-muted: #64748b;
  --pricing-text-disabled: #94a3b8;
  --pricing-hero-badge-bg: rgba(245, 158, 11, 0.12);
  --pricing-hero-badge-border: rgba(245, 158, 11, 0.3);
  --pricing-hero-badge-text: #0f172a;
  --pricing-badge-bg: #f1f5f9;
  --pricing-badge-text: #334155;
  --pricing-badge-border: #e2e8f0;
  --pricing-daily-badge-bg: #fef3c7;
  --pricing-daily-badge-border: #fde68a;
  --pricing-daily-badge-text: #92400e;
  --pricing-save-badge-bg: #d1fae5;
  --pricing-save-badge-border: #a7f3d0;
  --pricing-save-badge-text: #065f46;
  --pricing-btn-pro-bg: #0f172a;
  --pricing-btn-pro-text: #ffffff;
  --pricing-btn-pro-border: #0f172a;
  --pricing-btn-pro-hover: #1e293b;
  --pricing-btn-free-bg: transparent;
  --pricing-btn-free-text: #0f172a;
  --pricing-btn-free-border: #0f172a;
  --pricing-btn-free-hover: #f1f5f9;
  --pricing-section-alt-bg: #f8fafc;
  --pricing-table-header-bg: #0f172a;
  --pricing-table-row-bg: #ffffff;
  --pricing-table-alt-row-bg: #f8fafc;
  --pricing-table-highlight-bg: #f0fdf4;
  --pricing-table-border: #e2e8f0;
  --pricing-faq-card-bg: #ffffff;
  --pricing-faq-border: #e2e8f0;
  --pricing-faq-button-bg: #ffffff;
  --pricing-faq-body-text: #475569;
  --pricing-modal-box-bg: #f8fafc;
  --pricing-modal-box-border: #e2e8f0;
}

[data-bs-theme="dark"] {
  --pricing-bg: transparent;
  --pricing-card-bg: #1a1e24;
  --pricing-card-border: rgba(255, 255, 255, 0.12);
  --pricing-pro-card-bg: #1e2530;
  --pricing-pro-card-border: var(--color-default, {{ $settings->color_default }});
  --pricing-pro-badge-bg: var(--color-default, {{ $settings->color_default }});
  --pricing-pro-badge-text: #051b11;
  --pricing-text-primary: #f8fafc;
  --pricing-text-secondary: #e2e8f0;
  --pricing-text-muted: #94a3b8;
  --pricing-text-disabled: #64748b;
  --pricing-hero-badge-bg: rgba(245, 158, 11, 0.16);
  --pricing-hero-badge-border: rgba(245, 158, 11, 0.4);
  --pricing-hero-badge-text: #f8fafc;
  --pricing-badge-bg: rgba(255, 255, 255, 0.08);
  --pricing-badge-text: #e2e8f0;
  --pricing-badge-border: rgba(255, 255, 255, 0.14);
  --pricing-daily-badge-bg: rgba(245, 158, 11, 0.18);
  --pricing-daily-badge-border: rgba(245, 158, 11, 0.4);
  --pricing-daily-badge-text: #fbbf24;
  --pricing-save-badge-bg: rgba(16, 185, 129, 0.18);
  --pricing-save-badge-border: rgba(16, 185, 129, 0.4);
  --pricing-save-badge-text: #34d399;
  --pricing-btn-pro-bg: var(--color-default, {{ $settings->color_default }});
  --pricing-btn-pro-text: #051b11;
  --pricing-btn-pro-border: var(--color-default, {{ $settings->color_default }});
  --pricing-btn-pro-hover: var(--color-default, {{ $settings->color_default }});
  --pricing-btn-free-bg: transparent;
  --pricing-btn-free-text: #f8fafc;
  --pricing-btn-free-border: rgba(255, 255, 255, 0.35);
  --pricing-btn-free-hover: rgba(255, 255, 255, 0.1);
  --pricing-section-alt-bg: #14171b;
  --pricing-table-header-bg: #0f1317;
  --pricing-table-row-bg: #1a1e24;
  --pricing-table-alt-row-bg: #161a20;
  --pricing-table-highlight-bg: rgba(0, 214, 144, 0.08);
  --pricing-table-border: rgba(255, 255, 255, 0.08);
  --pricing-faq-card-bg: #1a1e24;
  --pricing-faq-border: rgba(255, 255, 255, 0.1);
  --pricing-faq-button-bg: #1a1e24;
  --pricing-faq-body-text: #cbd5e1;
  --pricing-modal-box-bg: #161a20;
  --pricing-modal-box-border: rgba(255, 255, 255, 0.12);
}

/* Typography & Hero */
.pricing-hero-section {
  padding-top: 110px !important;
}
.pricing-hero-badge {
  background: var(--pricing-hero-badge-bg);
  border: 1px solid var(--pricing-hero-badge-border);
  max-width: 100%;
}
.pricing-hero-badge-text {
  color: var(--pricing-hero-badge-text);
  font-size: 0.85rem;
}
.pricing-hero-stars {
  font-size: 0.78rem;
  gap: 2px;
}
@media (max-width: 575.98px) {
  .pricing-hero-badge {
    padding: 5px 12px !important;
    gap: 0.35rem !important;
  }
  .pricing-hero-badge-text {
    font-size: 0.775rem;
    line-height: 1.3;
  }
  .pricing-hero-stars {
    font-size: 0.68rem;
    gap: 1.5px;
  }
}
.pricing-hero-title {
  color: var(--pricing-text-primary);
  font-size: clamp(2rem, 4vw, 3.25rem);
  letter-spacing: -0.02em;
}
.pricing-hero-subtitle {
  color: var(--pricing-text-muted);
  font-size: 1.15rem;
  line-height: 1.6;
}
.pricing-toggle-label {
  color: var(--pricing-text-primary) !important;
}
.text-color-default,
.text-theme-default,
.text-mint {
  color: var(--color-default, {{ $settings->color_default }}) !important;
}

/* Base Pricing Cards */
.pricing-card {
  background-color: var(--pricing-card-bg) !important;
  border-color: var(--pricing-card-border) !important;
  color: var(--pricing-text-secondary);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.pricing-card:hover {
  transform: translateY(-4px);
}
.pricing-pro-card {
  background-color: var(--pricing-pro-card-bg) !important;
  border: 2px solid var(--pricing-pro-card-border) !important;
  color: var(--pricing-text-secondary);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.pricing-pro-card:hover {
  transform: translateY(-4px);
}
[data-bs-theme="dark"] .pricing-pro-card {
  box-shadow: 0 10px 30px -5px rgba(0, 214, 144, 0.2) !important;
}

.pricing-title {
  color: var(--pricing-text-primary) !important;
}
.pricing-subtitle {
  color: var(--pricing-text-muted) !important;
}
.pricing-bullet-text {
  color: var(--pricing-text-secondary);
  font-size: 0.95rem;
  line-height: 1.5;
}
.pricing-bullet-strong {
  color: var(--pricing-text-primary);
  font-weight: 600;
}
.pricing-disabled-item {
  color: var(--pricing-text-disabled);
}
.pricing-disabled-text {
  color: var(--pricing-text-disabled) !important;
  font-size: 0.95rem;
}
.pricing-disabled-icon {
  color: var(--pricing-text-disabled) !important;
}

/* Badges */
.pricing-badge {
  background-color: var(--pricing-badge-bg) !important;
  color: var(--pricing-badge-text) !important;
  border: 1px solid var(--pricing-badge-border) !important;
}
.pricing-pro-badge {
  background-color: var(--pricing-pro-badge-bg) !important;
  color: var(--pricing-pro-badge-text) !important;
}
.pricing-daily-badge {
  background-color: var(--pricing-daily-badge-bg) !important;
  border: 1px solid var(--pricing-daily-badge-border) !important;
  color: var(--pricing-daily-badge-text) !important;
  white-space: normal;
  display: inline-block;
  line-height: 1.4;
}
.pricing-save-badge {
  background-color: var(--pricing-save-badge-bg) !important;
  border: 1px solid var(--pricing-save-badge-border) !important;
  color: var(--pricing-save-badge-text) !important;
  white-space: normal;
  display: inline-block;
  line-height: 1.4;
}

/* Buttons */
.pricing-btn-pro {
  background-color: var(--pricing-btn-pro-bg) !important;
  color: var(--pricing-btn-pro-text) !important;
  border: 1px solid var(--pricing-btn-pro-border) !important;
  transition: all 0.2s ease;
}
.pricing-btn-pro:hover {
  background-color: var(--pricing-btn-pro-hover) !important;
  border-color: var(--pricing-btn-pro-hover) !important;
  color: var(--pricing-btn-pro-text) !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
.pricing-btn-free {
  background-color: var(--pricing-btn-free-bg) !important;
  color: var(--pricing-btn-free-text) !important;
  border: 1px solid var(--pricing-btn-free-border) !important;
  transition: all 0.2s ease;
}
.pricing-btn-free:hover {
  background-color: var(--pricing-btn-free-hover) !important;
  color: var(--pricing-btn-free-text) !important;
  transform: translateY(-2px);
}

/* Section Backgrounds */
.pricing-section-alt {
  background-color: var(--pricing-section-alt-bg) !important;
  border-color: var(--pricing-table-border) !important;
}

/* Comparison Table */
.pricing-table-wrap {
  background-color: var(--pricing-table-row-bg) !important;
  border: 1px solid var(--pricing-table-border) !important;
}
.pricing-table {
  color: var(--pricing-text-secondary);
}
.pricing-th {
  background-color: var(--pricing-table-header-bg) !important;
  color: #ffffff !important;
  border-color: var(--pricing-table-border) !important;
}
.pricing-th-pro {
  background-color: var(--color-default, {{ $settings->color_default }}) !important;
  color: #051b11 !important;
  border-color: var(--color-default, {{ $settings->color_default }}) !important;
  font-weight: 700;
}
.pricing-table td {
  border-color: var(--pricing-table-border) !important;
}
.pricing-table-title {
  color: var(--pricing-text-primary) !important;
}
.pricing-table-val {
  color: var(--pricing-text-muted) !important;
}
.pricing-table-pro-val {
  background-color: var(--pricing-table-highlight-bg) !important;
  color: var(--pricing-text-primary) !important;
}

/* FAQ Accordion */
.pricing-faq-card {
  background-color: var(--pricing-faq-card-bg) !important;
  border: 1px solid var(--pricing-faq-border) !important;
}
.pricing-faq-btn {
  background-color: var(--pricing-faq-button-bg) !important;
  color: var(--pricing-text-primary) !important;
  box-shadow: none !important;
}
.pricing-faq-btn:not(.collapsed) {
  background-color: var(--pricing-faq-button-bg) !important;
  color: var(--pricing-text-primary) !important;
}
[data-bs-theme="dark"] .pricing-faq-btn::after {
  filter: invert(1) brightness(1.5);
}
.pricing-faq-body {
  color: var(--pricing-faq-body-text) !important;
}
.pricing-faq-body ul {
  list-style-type: disc !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
}
.pricing-faq-body ol {
  list-style-type: decimal !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
}
.pricing-faq-body ul > li {
  list-style-type: disc !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}
.pricing-faq-body ol > li {
  list-style-type: decimal !important;
  margin-bottom: 0.4rem;
  line-height: 1.65;
}
.pricing-faq-body li::marker {
  color: var(--color-default, #00d690);
}

/* Closing CTA Card */
.pricing-closing-card {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
}

/* Checkout Modal */
.pricing-modal-content {
  background-color: var(--pricing-card-bg) !important;
  color: var(--pricing-text-primary) !important;
  border: 1px solid var(--pricing-card-border) !important;
}
.pricing-modal-box {
  background-color: var(--pricing-modal-box-bg) !important;
  border: 1px solid var(--pricing-modal-box-border) !important;
  color: var(--pricing-text-primary) !important;
}
.pricing-modal-radio {
  background-color: var(--pricing-modal-box-bg) !important;
  border: 1px solid var(--pricing-modal-box-border) !important;
  color: var(--pricing-text-primary) !important;
}
.pricing-modal-radio label {
  color: var(--pricing-text-primary) !important;
}
.pricing-modal-title {
  color: var(--pricing-text-primary) !important;
}
.pricing-modal-text {
  color: var(--pricing-text-primary) !important;
}
.pricing-modal-muted {
  color: var(--pricing-text-muted) !important;
}

/* Mobile Optimizations */
@media (max-width: 767.98px) {
  .pricing-hero-section {
    padding-top: 95px !important;
  }
  .pricing-hero-title {
    font-size: 1.85rem !important;
    line-height: 1.25 !important;
  }
  .pricing-hero-subtitle {
    font-size: 1rem !important;
  }
  .pricing-card, .pricing-pro-card {
    padding: 1.5rem 1.25rem !important;
  }
  .pricing-daily-badge, .pricing-save-badge {
    font-size: 0.76rem !important;
    padding: 6px 10px !important;
  }
  .pricing-table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .pricing-closing-card {
    padding: 2.25rem 1.25rem !important;
  }
}
</style>
@endsection

@section('content')
@php
  $isIndia = Helper::isIndia();
  $curr = Helper::currentCurrency();
  $testimonials = $testimonials ?? \App\Models\Testimonial::active()->ordered()->get();
  $clientLogos = $clientLogos ?? \App\Models\ClientLogo::active()->ordered()->get();
  $totalUsers = $totalUsers ?? cache()->remember('active_users_count', 3600, fn() => \App\Models\User::where('status', 'active')->count());
@endphp

<section class="section section-sm pricing-hero-section pb-5" style="padding-top: 110px !important;">
  <div class="container">

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show mt-3 shadow-sm rounded-3" role="alert">
        <i class="bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show mt-3 shadow-sm rounded-3" role="alert">
        <i class="bi-check2 me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- HERO HEADER SECTION -->
    <div class="row justify-content-center">
      <div class="col-lg-9 col-md-11 text-center py-4">
        
        <!-- Social Proof Pill Badge -->
        <div class="pricing-hero-badge d-inline-flex flex-wrap align-items-center justify-content-center gap-1 gap-sm-2 px-2.5 px-sm-3 py-1.5 rounded-pill mb-3 shadow-xs">
          <div class="d-inline-flex align-items-center gap-1 flex-shrink-0">
            <div class="d-inline-flex text-warning pricing-hero-stars">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
            </div>
            <strong class="pricing-hero-badge-text">4.9 / 5<span class="d-none d-sm-inline"> Rating</span></strong>
          </div>
          <span class="pricing-hero-bullet text-muted opacity-50">&bull;</span>
          <span class="pricing-hero-badge-text fw-medium">
            Trusted by <strong>{{ number_format($totalUsers) }}+</strong> Creators<span class="d-none d-sm-inline">, Ecom Brands & Agencies</span>
          </span>
        </div>

        <h1 class="pricing-hero-title fw-bold mb-3">
          Studio-Grade AI Photoshoots <br class="d-none d-md-block">
          <span class="text-color-default font-serif-italic">For Less Than a Cup of Coffee</span>
        </h1>
        <p class="pricing-hero-subtitle mx-auto mb-4" style="max-width: 680px;">
          Unlock 100 daily production-grade AI prompts with camera lenses, studio lighting recipes, multi-angle variations, and full commercial client rights.
        </p>

        <!-- Monthly / Yearly Billing Toggle Switch -->
        <div class="d-flex justify-content-center align-items-center mb-2">
          <div class="form-check form-switch form-switch-md flex-row d-flex align-items-center p-0 m-0">
            <label class="c-pointer fw-semibold pricing-toggle-label fs-6" for="plan">{{ __('misc.monthly') }}</label>
            <input class="form-check-input mx-3" value="mo" type="checkbox" id="plan" style="cursor: pointer; width: 48px; height: 26px;">
            <label class="c-pointer fw-semibold pricing-toggle-label fs-6 d-flex align-items-center" for="plan">
              {{ __('misc.yearly') }}
              <span class="badge bg-success text-white rounded-pill ms-2 px-2.5 py-1 font-monospace" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                SAVE 25% + 2 MO FREE
              </span>
            </label>
          </div>
        </div>

      </div>
    </div><!-- row -->

    <!-- PRICING CARDS ROW -->
    <div class="row justify-content-center g-4 mb-5">

      <!-- FREE STARTER CARD -->
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card h-100 rounded-4 shadow-sm p-4 border pricing-card position-relative">
          <div class="card-header py-3 bg-transparent border-bottom-0 text-center">
            <span class="w-100 mb-2 d-block">
              <span class="badge rounded-pill pricing-badge px-3 py-1.5 fw-semibold">Free Forever</span>
            </span>
            <h2 class="my-0 fw-bold pricing-title">Starter Free</h2>
            <p class="pricing-subtitle small mt-2 mb-0">Explore and try basic AI photography prompts</p>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="text-center mb-4">
              <h1 class="pricing-title my-0">
                <sup class="h4 fw-bold lh-1">{{ $curr['symbol'] }}</sup>0
                <small class="pricing-subtitle fw-light f-size-18">/forever</small>
              </h1>
              <div class="mt-2">
                <span class="badge pricing-badge rounded-pill px-3 py-1 small fw-normal">
                  No credit card required
                </span>
              </div>
            </div>

            <ul class="list-unstyled mb-4 flex-grow-1">
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2 flex-shrink-0"></i>
                <span class="pricing-bullet-text">Access to all <strong class="pricing-bullet-strong">Free AI Prompts</strong></span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2 flex-shrink-0"></i>
                <span class="pricing-bullet-text">Copy up to <strong class="pricing-bullet-strong">{{ isset($settings->daily_limit_prompts) && $settings->daily_limit_prompts == 0 ? trans('admin.unlimited') : ($settings->daily_limit_prompts ?? 3) }} free prompts</strong> / day</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2 flex-shrink-0"></i>
                <span class="pricing-bullet-text">Up to <strong class="pricing-bullet-strong">{{ isset($settings->daily_limit_downloads) && $settings->daily_limit_downloads == 0 ? trans('admin.unlimited') : ($settings->daily_limit_downloads ?? 3) }} free photo downloads</strong> / day</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2 flex-shrink-0"></i>
                <span class="pricing-bullet-text">Compatible with Midjourney, Gemini & ChatGPT</span>
              </li>
              <li class="mb-3 d-flex align-items-center pricing-disabled-item">
                <i class="bi bi-x fs-4 pricing-disabled-icon me-2 flex-shrink-0"></i>
                <span class="text-decoration-line-through pricing-disabled-text">Unlock Premium AI Prompts</span>
              </li>
              <li class="mb-3 d-flex align-items-center pricing-disabled-item">
                <i class="bi bi-x fs-4 pricing-disabled-icon me-2 flex-shrink-0"></i>
                <span class="text-decoration-line-through pricing-disabled-text">Studio Camera & Lens Parameters (85mm, f/1.4)</span>
              </li>
              <li class="mb-3 d-flex align-items-center pricing-disabled-item">
                <i class="bi bi-x fs-4 pricing-disabled-icon me-2 flex-shrink-0"></i>
                <span class="text-decoration-line-through pricing-disabled-text">High-Resolution Pro Downloads (100/day)</span>
              </li>
              <li class="mb-3 d-flex align-items-center pricing-disabled-item">
                <i class="bi bi-x fs-4 pricing-disabled-icon me-2 flex-shrink-0"></i>
                <span class="text-decoration-line-through pricing-disabled-text">Commercial Client License</span>
              </li>
            </ul>

            <div class="mt-auto">
              @if (!auth()->check())
                <a href="{{ url('register') }}" class="w-100 btn btn-lg pricing-btn-free rounded-pill py-3 fw-semibold">
                  Get Started Free
                </a>
              @elseif (!$getSubscription)
                <button type="button" class="w-100 btn btn-lg btn-outline-secondary rounded-pill py-3 fw-semibold disabled" disabled>
                  <i class="bi bi-check2 me-1"></i> Current Plan
                </button>
              @else
                <button type="button" class="w-100 btn btn-lg btn-outline-secondary rounded-pill py-3 fw-semibold disabled" disabled>
                  Free Tier
                </button>
              @endif
            </div>

          </div>
        </div>
      </div>

      <!-- PRO PLAN CARD(S) -->
      @foreach ($plans->whereDownloadableContent('images')->get() as $plan)
        @php
          $isCurrentExactPlan = auth()->check() && $getSubscription && ($getSubscription->stripe_price == $plan->plan_id || preg_replace('/_(month|year).*$/', '', $getSubscription->stripe_price) == $plan->plan_id);
          $monthlyPrice = $isIndia ? ($plan->price_inr ?: 284.00) : ($plan->price ?: 3.00);
          $yearlyPrice  = $isIndia ? ($plan->price_year_inr ?: 2550.00) : ($plan->price_year ?: 27.00);
          $planDiscount = Helper::calculateSubscriptionDiscount($monthlyPrice, $yearlyPrice);
          $dailyMonthlyPrice = number_format($monthlyPrice / 30, 2);
          $dailyYearlyPrice  = number_format($yearlyPrice / 365, 2);
        @endphp
        <div class="col-lg-5 col-md-6 col-12">
          <div class="card h-100 rounded-4 p-4 position-relative pricing-pro-card shadow-lg">
            
            <!-- Most Popular Badge -->
            <div class="card-header py-3 bg-transparent border-bottom-0 text-center">
              <span class="w-100 mb-2 d-block">
                <span class="badge rounded-pill pricing-pro-badge px-3 py-1.5 shadow-sm fw-bold">
                  <i class="bi bi-stars me-1"></i> MOST POPULAR
                </span>
              </span>
              <h2 class="my-0 fw-bold pricing-title">
                {{ $plan->name }}
                @if ($planDiscount > 0)
                  <small class="badge bg-success rounded-pill display-none planYearly fs-small align-middle ms-1">
                    {{ $planDiscount }}% {{ __('misc.discount') }}
                  </small>
                @endif
              </h2>
              <p class="pricing-subtitle small mt-2 mb-0">Unlimited creative firepower with full camera parameters & commercial rights</p>
            </div>

            <div class="card-body d-flex flex-column">
              
              <!-- Pricing & Micro-Pricing Banner -->
              <div class="text-center mb-4">
                <!-- Monthly Price -->
                <div class="planMonthly">
                  <h1 class="pricing-title my-0">
                    <sup class="h4 fw-bold lh-1">{{ $curr['symbol'] }}</sup><span>{{ $isIndia ? number_format($monthlyPrice, 0) : number_format($monthlyPrice, 2) }}</span>
                    <small class="pricing-subtitle fw-light f-size-18">/{{ __('misc.mo') }}</small>
                  </h1>
                  <div class="mt-2">
                    <span class="badge pricing-daily-badge rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                      <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Just {{ $curr['symbol'] }}{{ $dailyMonthlyPrice }}/day &bull; {{ $isIndia ? 'Less than a cup of chai' : 'Less than a stick of gum' }}
                    </span>
                  </div>
                </div>

                <!-- Yearly Price -->
                <div class="planYearly display-none">
                  <h1 class="pricing-title my-0">
                    <sup class="h4 fw-bold lh-1">{{ $curr['symbol'] }}</sup><span>{{ $isIndia ? number_format($yearlyPrice, 0) : number_format($yearlyPrice, 2) }}</span>
                    <small class="pricing-subtitle fw-light f-size-18">/{{ __('misc.yr') }}</small>
                  </h1>
                  <div class="mt-2">
                    <span class="badge pricing-save-badge rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                      <i class="bi bi-check-circle-fill me-1"></i> Best Value &bull; Just {{ $curr['symbol'] }}{{ $dailyYearlyPrice }}/day &bull; 2 Months Free
                    </span>
                  </div>
                </div>
              </div>

              <!-- High-Converting Benefit Bullets -->
              <ul class="list-unstyled mb-4 flex-grow-1">
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text"><strong class="pricing-bullet-strong">Unlock & copy ALL Premium AI Prompts</strong> (100% Unlocked)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text">Copy up to <strong class="pricing-bullet-strong">{{ $plan->download_limits ?: 100 }} prompts per day</strong> (Free + Premium)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text"><strong class="pricing-bullet-strong">{{ number_format($plan->downloads_per_month) }} High-Res Downloads</strong> / month (up to {{ $plan->download_limits ?: 100 }}/day)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text"><strong class="pricing-bullet-strong">Studio Camera & Lighting Parameters</strong> (Sony A7R, 85mm f/1.4, Profoto)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text">Multi-angle <strong class="pricing-bullet-strong">Generated Example Outputs</strong> gallery for every shoot</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text"><strong class="pricing-bullet-strong">100% Commercial Client License</strong> (Ads, Client Work, Shopify/Amazon)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text">Compatible with <strong class="pricing-bullet-strong">Midjourney v6+, Google Gemini & ChatGPT</strong></span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2 flex-shrink-0"></i>
                  <span class="pricing-bullet-text"><strong class="pricing-bullet-strong">1-Click Instant Cancellation</strong> anytime from your dashboard</span>
                </li>
              </ul>

              <!-- CTA Button -->
              <div class="mt-auto">
                <a
                  data-plan-id="{{ $plan->plan_id }}"
                  data-plan-name="{{ __('misc.plan_name', ['plan' => $plan->name]) }}"
                  data-price="{{ Helper::formatPrice($monthlyPrice, $curr['code']) }}"
                  data-price-total="{{ Helper::formatPrice($monthlyPrice, $curr['code'], true) }}"
                  data-price-gross="{{ $monthlyPrice }}"
                  data-price-year="{{ Helper::formatPrice($yearlyPrice, $curr['code']) }}"
                  data-price-year-gross="{{ $yearlyPrice }}"
                  data-price-year-total="{{ Helper::formatPrice($yearlyPrice, $curr['code'], true) }}"
                  href="@auth javascript:void(0); @else{{ url('/login') }}@endauth"
                  @if (auth()->check()) data-bs-toggle="modal" data-bs-target="#checkout" @endif
                  class="w-100 btn btn-lg rounded-pill py-3 fw-bold pricing-btn-pro shadow-sm">
                  @if ($isCurrentExactPlan)
                    <i class="bi bi-check2 me-1"></i> {{ __('misc.active') }} (Switch)
                  @else
                    <i class="bi bi-stars me-1"></i> Upgrade to Pro
                  @endif
                </a>
                <small class="pricing-subtitle d-block text-center mt-2" style="font-size: 0.8rem;">
                  <i class="bi bi-shield-lock text-success me-1"></i> 256-Bit SSL Secure Checkout &bull; Instant Access &bull; Cancel Anytime
                </small>
              </div>

            </div>
          </div>
        </div>
      @endforeach

    </div>

    <div class="d-block text-center w-100 fst-italic mb-4">
      <small class="pricing-subtitle">
        <i class="bi bi-info-circle me-1"></i> {{ __('misc.prices_and_includes_tax', ['currency' => $curr['code']]) }}
      </small>
    </div>

  </div><!-- container -->
</section>

<!-- CLIENT LOGOS SLIDER SECTION -->
@if (isset($clientLogos) && $clientLogos->isNotEmpty())
  <div class="mb-5">
    @include('includes.client-logos-slider', ['clientLogos' => $clientLogos])
  </div>
@endif

<!-- VISUAL TRANSFORMATION PROOF: BEFORE / AFTER SHOWCASE -->
<div class="mb-5">
  @include('includes.transformation-section')
</div>

<!-- FREE VS PRO FEATURE COMPARISON TABLE -->
<section class="section py-5 pricing-section-alt border-top border-bottom">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge rounded-pill pricing-badge px-3 py-1.5 fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        SIDE-BY-SIDE
      </span>
      <h2 class="display-6 fw-bold title-custom mb-3">Compare Plans & Features</h2>
      <p class="pricing-hero-subtitle lead mx-auto" style="max-width: 620px;">
        See exactly what you get when you step up to ImagineBuddy Pro.
      </p>
    </div>

    <!-- Mobile Scroll Hint -->
    <div class="d-md-none text-center mb-3 pricing-subtitle small">
      <i class="bi bi-arrow-left-right text-primary me-1"></i> Scroll horizontally to compare features
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden pricing-table-wrap">
          <div class="table-responsive">
            <table class="table align-middle mb-0 pricing-table" style="font-size: 0.95rem;">
              <thead>
                <tr>
                  <th scope="col" class="py-3 ps-4 pricing-th" style="width: 48%; min-width: 220px;">Feature / Capability</th>
                  <th scope="col" class="py-3 text-center pricing-th" style="width: 26%; min-width: 130px;">Starter Free</th>
                  <th scope="col" class="py-3 text-center pricing-th-pro" style="width: 26%; min-width: 140px;">
                    <i class="bi bi-stars text-warning me-1"></i> Pro Plan
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-copy text-primary me-2"></i> Daily Prompt Copies
                  </td>
                  <td class="text-center pricing-table-val">
                    {{ isset($settings->daily_limit_prompts) && $settings->daily_limit_prompts == 0 ? 'Unlimited' : ($settings->daily_limit_prompts ?? 3) }} / day
                  </td>
                  <td class="text-center fw-bold pricing-table-pro-val">
                    <span class="badge pricing-save-badge px-2.5 py-1">100 / day</span>
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-collection text-primary me-2"></i> Premium Prompts Access
                  </td>
                  <td class="text-center pricing-table-val">
                    <span class="text-danger fw-semibold"><i class="bi bi-x-circle me-1"></i> Locked</span>
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-check-circle-fill me-1"></i> 100% Full Library
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-camera text-primary me-2"></i> Real Studio Camera Parameters (Lens, Aperture, ISO)
                  </td>
                  <td class="text-center pricing-table-val">
                    <i class="bi bi-dash fs-5"></i>
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-check-circle-fill me-1"></i> Included
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-brightness-high text-primary me-2"></i> Studio Lighting Setups (Softbox, Rim, Spot)
                  </td>
                  <td class="text-center pricing-table-val">
                    <i class="bi bi-dash fs-5"></i>
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-check-circle-fill me-1"></i> Included
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-download text-primary me-2"></i> Daily High-Res Photo Downloads
                  </td>
                  <td class="text-center pricing-table-val">
                    {{ isset($settings->daily_limit_downloads) && $settings->daily_limit_downloads == 0 ? 'Unlimited' : ($settings->daily_limit_downloads ?? 3) }} / day
                  </td>
                  <td class="text-center fw-bold pricing-table-pro-val">
                    100 / day <span class="small pricing-subtitle font-monospace">(3,000/mo)</span>
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-grid-3x3 text-primary me-2"></i> Multi-Angle Generated Output Gallery
                  </td>
                  <td class="text-center pricing-table-val">
                    <i class="bi bi-dash fs-5"></i>
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-check-circle-fill me-1"></i> Included
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-briefcase text-primary me-2"></i> Commercial Client Rights (Ads, Amazon, Shopify)
                  </td>
                  <td class="text-center pricing-table-val small">
                    Personal use only
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-shield-check me-1"></i> 100% Commercial Clearance
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-cpu text-primary me-2"></i> Multi-Model Optimization (Midjourney, Gemini, ChatGPT)
                  </td>
                  <td class="text-center pricing-table-val">
                    Basic
                  </td>
                  <td class="text-center fw-bold text-success pricing-table-pro-val">
                    <i class="bi bi-check-circle-fill me-1"></i> Model-Specific Parameters
                  </td>
                </tr>

                <tr>
                  <td class="py-3 ps-4 fw-semibold pricing-table-title">
                    <i class="bi bi-headset text-primary me-2"></i> Customer Support
                  </td>
                  <td class="text-center pricing-table-val">
                    Standard
                  </td>
                  <td class="text-center fw-bold pricing-table-pro-val">
                    <span class="badge bg-primary text-white px-2.5 py-1">Priority Fast-Track</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CREATOR TESTIMONIALS SLIDER SECTION -->
@if (isset($testimonials) && $testimonials->isNotEmpty())
  <section class="section py-5 py-large testimonials-section border-bottom">
    <div class="container">
      <div class="text-center mb-5">
        <span class="badge rounded-pill pricing-badge px-3 py-1.5 fw-bold text-uppercase mb-2" style="font-size: 0.75rem;">
          <i class="bi bi-chat-heart text-warning me-1"></i> REAL STORIES
        </span>
        <h2 class="display-6 fw-bold title-custom mb-3">{{ __('misc.creators_testimonials_heading') }}</h2>
        <p class="pricing-hero-subtitle lead mx-auto" style="max-width: 620px;">
          {{ __('misc.creators_testimonials_subtitle') }}
        </p>
      </div>

      @include('includes.testimonials-slider', ['testimonials' => $testimonials])
    </div>
  </section>
@endif

<!-- MODERNIZED OBJECTION-BUSTING AI PHOTOSHOOT FAQS -->
<section class="section py-5 py-large faq-section position-relative pricing-section-alt border-bottom" id="pricingFaq">
  <div class="container">
    <div class="row g-4 g-lg-5">
      
      <!-- Left Column: FAQ Sticky Header -->
      <div class="col-12 col-lg-5">
        <div class="faq-sticky-header">
          <span class="badge rounded-pill pricing-badge px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            HAVE QUESTIONS?
          </span>
          <h2 class="display-5 fw-bold title-custom mb-4" style="line-height: 1.15;">
            Frequently Asked <br>
            <span class="font-serif-italic text-color-default">Questions</span>
          </h2>
          <p class="pricing-hero-subtitle lead mb-3" style="font-size: 1.05rem;">
            Everything you need to know about our studio prompts and subscription before getting started.
          </p>
          <p class="pricing-subtitle small">
            Have a different question? <a href="{{ url('contact') }}" class="title-custom fw-bold text-decoration-underline">Contact our support team</a>.
          </p>
        </div>
      </div>

      <!-- Right Column: Accordion Items -->
      <div class="col-12 col-lg-7">
        <div class="accordion" id="pricingFaqAccordion">

          <!-- FAQ 1 -->
          <div class="card border-0 rounded-4 mb-3 shadow-xs overflow-hidden pricing-faq-card">
            <h2 class="accordion-header" id="headingFaq1">
              <button class="accordion-button pricing-faq-btn fw-bold fs-6 py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq1" aria-expanded="true" aria-controls="collapseFaq1">
                Do these prompts work on Midjourney, ChatGPT, and Google Gemini?
              </button>
            </h2>
            <div id="collapseFaq1" class="accordion-collapse collapse show" aria-labelledby="headingFaq1" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body px-4 pb-4 pt-1 pricing-faq-body" style="line-height: 1.7;">
                Yes! Every single prompt in our library is thoroughly tested and optimized for all leading image models, including <strong>Midjourney (v6+)</strong>, <strong>Google Gemini / Imagen 3</strong>, and <strong>ChatGPT / DALL·E 3</strong>. Each prompt includes recommended aspect ratios, stylize parameters, and lighting recipes so you get consistent, artifact-free studio results on your very first generation.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="card border-0 rounded-4 mb-3 shadow-xs overflow-hidden pricing-faq-card">
            <h2 class="accordion-header" id="headingFaq2">
              <button class="accordion-button collapsed pricing-faq-btn fw-bold fs-6 py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq2" aria-expanded="false" aria-controls="collapseFaq2">
                Why should I get ImagineBuddy Pro instead of writing my own prompts?
              </button>
            </h2>
            <div id="collapseFaq2" class="accordion-collapse collapse" aria-labelledby="headingFaq2" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body px-4 pb-4 pt-1 pricing-faq-body" style="line-height: 1.7;">
                Writing realistic commercial prompts from scratch typically takes hours of trial-and-error, wasted AI generation credits, and often results in fake, plastic-looking renders. Our prompt recipes are engineered by professional commercial photographers with exact optical physics (focal lengths like 85mm f/1.4, sensor sizes, Profoto softbox diffusion, and reflection management) giving you instant magazine-quality results without guesswork.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="card border-0 rounded-4 mb-3 shadow-xs overflow-hidden pricing-faq-card">
            <h2 class="accordion-header" id="headingFaq3">
              <button class="accordion-button collapsed pricing-faq-btn fw-bold fs-6 py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq3" aria-expanded="false" aria-controls="collapseFaq3">
                Can I use generated images for commercial client work and paid ads?
              </button>
            </h2>
            <div id="collapseFaq3" class="accordion-collapse collapse" aria-labelledby="headingFaq3" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body px-4 pb-4 pt-1 pricing-faq-body" style="line-height: 1.7;">
                Absolutely. The Pro Photography Plan includes a full commercial license. You can freely use all generated visuals in commercial client campaigns, social media advertisements, Shopify or Amazon storefronts, billboards, and print collateral with zero royalties and no attribution required.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="card border-0 rounded-4 mb-3 shadow-xs overflow-hidden pricing-faq-card">
            <h2 class="accordion-header" id="headingFaq4">
              <button class="accordion-button collapsed pricing-faq-btn fw-bold fs-6 py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq4" aria-expanded="false" aria-controls="collapseFaq4">
                How does the daily limit of 100 prompts work?
              </button>
            </h2>
            <div id="collapseFaq4" class="accordion-collapse collapse" aria-labelledby="headingFaq4" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body px-4 pb-4 pt-1 pricing-faq-body" style="line-height: 1.7;">
                As a Pro subscriber, you receive 100 prompt copies every single day, covering both free and premium prompts. Your limit resets automatically each day at midnight, giving you virtually unlimited headroom for client brainstorming, brand campaigns, and rapid creative iteration.
              </div>
            </div>
          </div>

          <!-- FAQ 5 -->
          <div class="card border-0 rounded-4 mb-3 shadow-xs overflow-hidden pricing-faq-card">
            <h2 class="accordion-header" id="headingFaq5">
              <button class="accordion-button collapsed pricing-faq-btn fw-bold fs-6 py-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq5" aria-expanded="false" aria-controls="collapseFaq5">
                Can I cancel anytime? How does cancellation work?
              </button>
            </h2>
            <div id="collapseFaq5" class="accordion-collapse collapse" aria-labelledby="headingFaq5" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body px-4 pb-4 pt-1 pricing-faq-body" style="line-height: 1.7;">
                Yes, there are zero commitments and no contracts. You can cancel your subscription in seconds with a single click directly from your Account Dashboard. If you cancel, your Pro access and downloads remain fully active until the end of your current billing period.
              </div>
            </div>
          </div>

        </div><!-- /.accordion -->
      </div>
    </div>
  </div>
</section>

<!-- FINAL RISK-FREE CLOSING CTA CARD -->
<section class="section py-5 py-large">
  <div class="container">
    <div class="card border-0 rounded-4 p-4 p-md-5 text-white text-center shadow-lg position-relative overflow-hidden pricing-closing-card">
      
      <!-- Background subtle glow -->
      <div class="position-absolute top-50 start-50 translate-middle w-100 h-100" style="background: radial-gradient(circle at center, rgba(56, 189, 248, 0.12) 0%, transparent 70%); pointer-events: none;"></div>

      <div class="position-relative z-1 py-3" style="max-width: 680px; margin: 0 auto;">
        <span class="badge rounded-pill bg-light text-dark px-3 py-1.5 fw-bold mb-3 shadow-xs" style="font-size: 0.8rem;">
          <i class="bi bi-stars text-warning me-1"></i> RISK-FREE CREATIVE FREEDOM
        </span>
        <h2 class="display-5 fw-bold text-white mb-3">
          Ready to Create Studio-Grade Visuals?
        </h2>
        <p class="lead text-white-50 mb-4" style="font-size: 1.15rem; line-height: 1.6;">
          Join {{ number_format($totalUsers) }}+ creators, brands, and agencies elevating their photography for just {{ $curr['symbol'] }}{{ $isIndia ? '8' : '0.10' }}/day.
        </p>

        @if ($plans->whereDownloadableContent('images')->count() > 0)
          @php
            $firstPlan = $plans->whereDownloadableContent('images')->first();
            $mPrice = $isIndia ? ($firstPlan->price_inr ?: 284.00) : ($firstPlan->price ?: 3.00);
            $yPrice = $isIndia ? ($firstPlan->price_year_inr ?: 2550.00) : ($firstPlan->price_year ?: 27.00);
          @endphp
          <div class="mb-4">
            <a
              data-plan-id="{{ $firstPlan->plan_id }}"
              data-plan-name="{{ __('misc.plan_name', ['plan' => $firstPlan->name]) }}"
              data-price="{{ Helper::formatPrice($mPrice, $curr['code']) }}"
              data-price-total="{{ Helper::formatPrice($mPrice, $curr['code'], true) }}"
              data-price-gross="{{ $mPrice }}"
              data-price-year="{{ Helper::formatPrice($yPrice, $curr['code']) }}"
              data-price-year-gross="{{ $yPrice }}"
              data-price-year-total="{{ Helper::formatPrice($yPrice, $curr['code'], true) }}"
              href="@auth javascript:void(0); @else{{ url('/login') }}@endauth"
              @if (auth()->check()) data-bs-toggle="modal" data-bs-target="#checkout" @endif
              class="btn btn-warning btn-lg rounded-pill px-5 py-3 fw-bold text-dark shadow">
              <i class="bi bi-stars me-1"></i> Get Started with Pro Today
            </a>
          </div>
        @endif

        <!-- Trust Badges Strip -->
        <div class="row g-2 justify-content-center pt-2">
          <div class="col-auto">
            <span class="small text-white-50 d-inline-flex align-items-center">
              <i class="bi bi-shield-check text-success me-1"></i> 256-Bit SSL Checkout
            </span>
          </div>
          <div class="col-auto d-none d-sm-inline text-white-50">&bull;</div>
          <div class="col-auto">
            <span class="small text-white-50 d-inline-flex align-items-center">
              <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Instant Activation
            </span>
          </div>
          <div class="col-auto d-none d-sm-inline text-white-50">&bull;</div>
          <div class="col-auto">
            <span class="small text-white-50 d-inline-flex align-items-center">
              <i class="bi bi-check-circle-fill text-primary me-1"></i> 1-Click Cancel Anytime
            </span>
          </div>
          <div class="col-auto d-none d-sm-inline text-white-50">&bull;</div>
          <div class="col-auto">
            <span class="small text-white-50 d-inline-flex align-items-center">
              <i class="bi bi-briefcase-fill text-info me-1"></i> Commercial Rights
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- CHECKOUT MODAL (For Logged-in Users) -->
@if (auth()->check())
<div class="modal fade" tabindex="-1" id="checkout">
  <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
    <div class="modal-content rounded-4 border-0 shadow-lg pricing-modal-content">
      <div class="modal-body p-lg-4">
        <h5 class="mb-3 fw-bold pricing-modal-title">
          <i class="bi bi-cart2 me-1"></i> {{ __('misc.checkout') }}

          <span class="float-end c-pointer" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </span>
        </h5>
        
        <div class="container-fluid p-0">
          <div class="row g-4">

            <!-- Payment Options Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <strong class="pricing-modal-text">{{ __('misc.payments_options') }}</strong>
              </div>

              <form method="post" action="{{url('buy/subscription')}}" class="d-inline" id="formBuySubscription">
                @csrf

                <input type="hidden" id="interval" name="interval" value="month">
                <input type="hidden" id="planId" name="plan" value="">

              @php
                $allGateways = PaymentGateways::whereEnabled('1')->whereSubscription('1')->orderBy('type', 'DESC')->get();
                if ($isIndia) {
                  $availableGateways = $allGateways->filter(function($p) {
                    return $p->name === 'Razorpay';
                  });
                } else {
                  // International: Allow Razorpay (Card) and PayPal/Stripe (if enabled)
                  $availableGateways = $allGateways->filter(function($p) {
                    return in_array($p->name, ['Razorpay', 'PayPal', 'Stripe']);
                  });
                }
              @endphp

              @forelse ($availableGateways as $payment)
                <div class="form-check custom-radio mb-2 p-3 border rounded-3 pricing-modal-radio">
                  <input name="payment_gateway" value="{{$payment->id}}" id="payment_radio{{$payment->id}}" class="form-check-input radio-bws" type="radio" @if($loop->first) checked @endif>
                  <label class="form-check-label ps-2" for="payment_radio{{$payment->id}}">
                    <span><img class="me-1 rounded" src="{{ url('public/img/payments', $payment->logo) }}" width="20" /> <strong class="pricing-modal-text">{{ $payment->name }}</strong></span>
                    <small class="w-100 d-block pricing-modal-muted mt-1">
                      @if ($payment->name == 'Razorpay')
                        @if ($isIndia)
                          UPI, Google Pay, PhonePe, Paytm, RuPay & Cards
                        @else
                          Credit / Debit Cards (Visa, Mastercard, Amex)
                        @endif
                      @elseif ($payment->name == 'PayPal')
                        PayPal, Credit/Debit Cards, Apple Pay
                      @elseif ($payment->type == 'card')
                        {{ __('misc.debit_credit_card') }}
                      @endif
                    </small>
                  </label>
                </div>
              @empty
                @if ($isIndia)
                  <div class="alert alert-warning py-2 mb-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i> Razorpay gateway is currently unavailable.
                  </div>
                @else
                  <div class="alert alert-warning py-2 mb-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i> Payment gateway is currently unavailable.
                  </div>
                @endif
              @endforelse

            </div>

            <!-- Order Summary Column -->
            <div class="col-md-6">
              <div class="p-3 rounded-4 border pricing-modal-box">
                <div class="mb-2">
                  <strong class="pricing-modal-text">{{ __('misc.order_summary') }}</strong>
                </div>

                <ul class="list-group list-group-flush bg-transparent">
                  <li class="list-group-item py-2 px-0 bg-transparent">
                    <div class="row">
                      <div class="col">
                        <span class="d-block w-100 fw-bold pricing-modal-text" id="summaryPlan"></span>
                        <small class="planMonthly pricing-modal-muted">{{ __('misc.billed_monthly') }}</small>
                        <small class="planYearly display-none pricing-modal-muted">{{ __('misc.billed_yearly') }}</small>
                      </div>
                    </div>
                  </li>

                  <li class="list-group-item py-2 px-0 bg-transparent">
                    <div class="row">
                      <div class="col">
                        <small class="pricing-modal-muted">{{ __('misc.subtotal') }}:</small>
                      </div>
                      <div class="col-auto">
                        <small class="fw-bold pricing-modal-text" id="subtotal"></small>
                      </div>
                    </div>
                  </li>

                @if (auth()->user()->isTaxable()->count())
                  @foreach (auth()->user()->isTaxable() as $tax)
                    <li class="list-group-item py-2 px-0 bg-transparent isTaxable">
                      <div class="row">
                        <div class="col">
                          <small class="pricing-modal-muted">{{ $tax->name }} {{ $tax->percentage }}%:</small>
                        </div>
                        <div class="col-auto percentageAppliedTax{{$loop->iteration}}" data="{{ $tax->percentage }}">
                          <small class="fw-bold pricing-modal-text">
                            {{ $curr['symbol'] }}<span class="amount{{$loop->iteration}}"></span>
                          </small>
                        </div>
                      </div>
                    </li>
                  @endforeach
                @endif

                  <li class="list-group-item py-2 px-0 bg-transparent border-top">
                    <div class="row">
                      <div class="col">
                        <span class="fw-bold pricing-modal-text">{{ __('misc.total') }}:</span>
                      </div>
                      <div class="col-auto fw-bold">
                        <span class="pricing-modal-text" id="total"></span> {{ $curr['code'] }}
                      </div>
                    </div>
                  </li>
                </ul>

                <small class="d-block my-3 pricing-modal-muted" style="font-size: 0.78rem;">
                  {!! __('misc.agree_subscription', ['terms' => '<a href="'.$settings->link_terms.'" target="_blank" class="text-decoration-underline pricing-modal-text fw-bold">'. __('misc.terms_services') .'</a>']) !!}
                </small>

                <div class="alert alert-danger py-2 display-none" id="errorPurchase">
                  <ul class="list-unstyled m-0" id="showErrorsPurchase"></ul>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill py-2.5 fw-bold shadow-sm" id="subscribe">
                  <i></i> {{ __('misc.pay') }}
                </button>
                <div class="w-100 d-block text-center mt-2">
                  <button type="button" class="btn btn-link e-none text-decoration-none pricing-modal-muted small" data-bs-dismiss="modal">
                    {{ __('admin.cancel') }}
                  </button>
                </div>
              </div>
            </div>

            </form>
          </div><!-- row -->
        </div><!-- container -->

      </div><!-- modal-body -->
    </div><!-- modal-content -->
  </div><!-- modal-dialog -->
</div><!-- modal -->
@endif

@endsection

@section('javascript')
  @auth
    <script src="{{ asset('public/js/subscription.js') }}?v={{$settings->version}}"></script>
  @endauth

  <script type="text/javascript">
  $('#plan').change(function () {
	  if ($(this).is(":checked")) {
	    $('.planMonthly').hide();
	    $('.planYearly').show();
	    $('#interval').val('year');
	  } else {
	    $('.planMonthly').show();
	    $('.planYearly').hide();
	    $('#interval').val('month');
	  }
	});
  </script>
@endsection
