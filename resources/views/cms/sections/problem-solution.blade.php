<section class="cms-section" id="why-choose">
  <div class="container">
    <div class="cms-section-header">
      @if (!empty($section->badge))
        <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
      @endif
      <h2 class="cms-section-title">
        {{ $section->heading ?: 'Why Brands Choose Imagine Buddy' }}
      </h2>
      @if (!empty($section->subheading))
        <p class="cms-section-sub">{{ $section->subheading }}</p>
      @endif
    </div>

    @php
      $cards = $section->cards;
    @endphp

    <div class="row g-4">
      @if (!empty($cards) && count($cards) > 0)
        @foreach ($cards as $idx => $card)
          <div class="col-lg-4 col-md-6">
            <div class="cms-feature-card">
              <div class="cms-feature-icon">
                @if (!empty($card['icon']))
                  <i class="{{ $card['icon'] }}"></i>
                @else
                  <i class="bi bi-lightning-charge-fill"></i>
                @endif
              </div>
              <h3 class="cms-feature-title">{{ $card['title'] ?? 'Core Advantage' }}</h3>
              <p class="cms-feature-desc">{{ $card['description'] ?? '' }}</p>
            </div>
          </div>
        @endforeach
      @elseif (!empty($section->content))
        <div class="col-12">
          <div class="cms-feature-card">
            {!! $section->content !!}
          </div>
        </div>
      @else
        {{-- Default standard benefit cards --}}
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-clock-history"></i></div>
            <h3 class="cms-feature-title">Faster Time to Market</h3>
            <p class="cms-feature-desc">Eliminate weeks of studio booking and model coordination. Generate production-ready assets in minutes.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-cash-stack"></i></div>
            <h3 class="cms-feature-title">80% Cost Reduction</h3>
            <p class="cms-feature-desc">Drastically reduce photography budgets by replacing physical studios, sample shipping, and retakes with AI.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-palette-fill"></i></div>
            <h3 class="cms-feature-title">Consistent Brand Aesthetics</h3>
            <p class="cms-feature-desc">Ensure uniform lighting, true-to-life colors, and standardized camera angles across your entire product catalog.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-aspect-ratio-fill"></i></div>
            <h3 class="cms-feature-title">Multi-Platform Readiness</h3>
            <p class="cms-feature-desc">Instantly export in compliant formats for Amazon, Shopify, Myntra, Instagram Ads, and Google Display.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-layers-fill"></i></div>
            <h3 class="cms-feature-title">Infinite Style Variations</h3>
            <p class="cms-feature-desc">Test dozens of model demographics, outdoor settings, and lifestyle props without physical reshoots.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="cms-feature-card">
            <div class="cms-feature-icon"><i class="bi bi-shield-check"></i></div>
            <h3 class="cms-feature-title">Commercial License Protection</h3>
            <p class="cms-feature-desc">Every generated visual asset includes full commercial usage rights ready for global advertising campaigns.</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>
