<section class="cms-section pb-5" id="cta">
  <div class="container">
    <div class="cms-cta-banner">
      <div class="cms-cta-banner-content">
        @if (!empty($section->badge) || !empty($badge))
          <span class="cms-eyebrow-badge mb-2">
            <i class="bi bi-rocket-takeoff-fill"></i> {{ $section->badge ?? ($badge ?? 'START TODAY') }}
          </span>
        @endif

        <h2 class="cms-cta-banner-title">
          {!! $section->heading ?? ($heading ?? 'Ready to Transform Your Product Photography?') !!}
        </h2>

        <p class="cms-cta-banner-sub">
          {{ $section->subheading ?? ($subheading ?? 'Generate compliant, high-resolution catalog and ad imagery in minutes. No studio required.') }}
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="{{ $ctaUrl ?? url('register') }}" class="btn-cms-primary">
            {{ $ctaText ?? 'Start Free Trial' }} <i class="bi bi-arrow-right"></i>
          </a>
          @php
            $secText = $ctaSecondaryText ?? ($secondaryCtaText ?? 'View Pricing Plans');
            $secUrl = $ctaSecondaryUrl ?? ($secondaryCtaUrl ?? url('pricing'));
          @endphp
          @if (!empty($secText))
            <a href="{{ $secUrl }}" class="btn-cms-secondary" style="border-color: rgba(255,255,255,0.25); color: #ffffff !important;">
              {{ $secText }} <i class="bi bi-tag"></i>
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
