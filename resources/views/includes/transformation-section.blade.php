@php
  $homeContent = $homeContent ?? \App\Models\HomepageSetting::getSettings();
  $isIndia = Helper::isIndia();
  $curr = Helper::currentCurrency();

  $defaultBeforeImage = asset('public/img/White-Oud-Perfume-01.webp');
  $defaultAfterImage = asset('public/img/premium-white-oud-perfume-ad-02.webp');

  $cfgBeforeImg = $homeContent->getVal('transformation_before_image');
  $cfgAfterImg = $homeContent->getVal('transformation_after_image');

  if (!empty($customBeforeImg)) {
    $sliderBeforeImg = $customBeforeImg;
  } elseif (!empty($cfgBeforeImg)) {
    $sliderBeforeImg = \Illuminate\Support\Str::startsWith($cfgBeforeImg, ['http://', 'https://']) ? $cfgBeforeImg : asset($cfgBeforeImg);
  } else {
    $sliderBeforeImg = $defaultBeforeImage;
  }

  if (!empty($customAfterImg)) {
    $sliderAfterImg = $customAfterImg;
  } elseif (!empty($cfgAfterImg)) {
    $sliderAfterImg = \Illuminate\Support\Str::startsWith($cfgAfterImg, ['http://', 'https://']) ? $cfgAfterImg : asset($cfgAfterImg);
  } else {
    $sliderAfterImg = $defaultAfterImage;
  }

  $sliderBeforeLabel = !empty($customBeforeLabel) ? $customBeforeLabel : $homeContent->getVal('transformation_before_label', 'Raw Snapshot');
  $sliderAfterLabel = !empty($customAfterLabel) ? $customAfterLabel : $homeContent->getVal('transformation_after_label', 'Campaign Ready');

  $sliderBeforeAlt = !empty($customBeforeAlt) ? $customBeforeAlt : 'Raw Product Snapshot (Before)';
  $sliderAfterAlt = !empty($customAfterAlt) ? $customAfterAlt : 'Campaign Ready Commercial Visual (After)';

  $sectionEyebrow = !empty($customEyebrow) ? $customEyebrow : $homeContent->getVal('transformation_eyebrow', 'THE TRANSFORMATION');
  $sectionHeading = !empty($customHeading) ? $customHeading : $homeContent->getVal('transformation_heading', 'See the <span class="font-serif-italic">Difference Yourself</span>');
  $sectionSubheading = !empty($customSubheading) ? $customSubheading : $homeContent->getVal('transformation_subheading', 'Witness how simple, unedited product snapshots are instantly elevated into highly polished, premium brand campaigns.');

  $rightBadge = $homeContent->getVal('transformation_right_badge', 'AI Photoshoot Prompt Library');
  $rightBadgeSub = $homeContent->getVal('transformation_right_badge_sub', 'Unlimited Creative Ideation');
  $rightHeadline = $homeContent->getVal('transformation_right_headline', 'Studio-Quality Product Concepts. <br class="d-none d-sm-inline"><span class="text-mint">Without the Photoshoot Overhead.</span>');
  $rightDescription = $homeContent->getVal('transformation_right_description', 'Create premium product photography concepts without the cost, logistics, and delays of traditional photoshoots. Access battle-tested AI prompt recipes tailored for ecommerce, ads, and product launches—generating new visual directions on demand without booking a studio for every new campaign or SKU.');

  $tradItems = $homeContent->getVal('transformation_traditional_items') ?: [
    'Photographer & studio rental',
    'Props, models & set styling',
    'Shipping logistics & days of delay',
    'Repeated cost for every single SKU',
  ];
  $tradPrice = $isIndia 
    ? $homeContent->getVal('transformation_traditional_price_inr', '₹35,000 – ₹75,000+ / shoot')
    : $homeContent->getVal('transformation_traditional_price_usd', '$500 – $2,500+ / shoot');

  $libItems = $homeContent->getVal('transformation_library_items') ?: [
    'Unlimited prompt access',
    'Multi-angle & commercial aesthetics',
    'Instant concept iterations in minutes',
    'Zero studio bookings or physical logistics',
  ];
  $libPrice = $isIndia 
    ? $homeContent->getVal('transformation_library_price_inr', '₹250 /month')
    : $homeContent->getVal('transformation_library_price_usd', '$3 /month');

  $ctaTitle = $homeContent->getVal('transformation_cta_title', 'Never Pay Per Prompt');
  $ctaSubtitle = $homeContent->getVal('transformation_cta_subtitle');
  $ctaBtnText = $homeContent->getVal('transformation_cta_btn_text', 'Explore Premium Prompts');
  $ctaBtnUrl = $homeContent->getVal('transformation_cta_btn_url', 'pricing');
@endphp

<!-- Transformation Showcase Section -->
<section class="section py-5 py-large transformation-section position-relative">
  <div class="container">
    
    <!-- Section Header -->
    <div class="text-center mb-5">
      <div class="transformation-eyebrow text-uppercase fw-bold mb-2">
        <span class="eyebrow-dash me-1">—</span> {{ $sectionEyebrow }}
      </div>
      <h2 class="transformation-heading display-5 fw-bold title-custom mb-3">
        {!! $sectionHeading !!}
      </h2>
      <p class="transformation-subheading lead text-muted mx-auto" style="max-width: 660px;">
        {{ $sectionSubheading }}
      </p>
    </div>

    <!-- Main Transformation Card -->
    <div class="card border-0 shadow-lg rounded-4 p-3 p-md-5 bg-card-custom transformation-card border-custom">
      <div class="row align-items-center g-4 g-lg-5">
        
        <!-- Left Column: Interactive Before / After Split Slider -->
        <div class="col-12 col-lg-6">
          <div class="before-after-slider-container shadow-sm position-relative overflow-hidden rounded-4" id="transformationSlider">
            
            <!-- Background: Campaign Ready Image (After) -->
            <img 
              src="{{ $sliderAfterImg }}" 
              alt="{{ $sliderAfterAlt }}" 
              class="before-after-img after-img w-100 h-100" 
              draggable="false"
              loading="lazy"
              onerror="if (this.src !== '{{ $defaultAfterImage }}') this.src='{{ $defaultAfterImage }}';"
            >

            <!-- Foreground: Raw Snapshot Image (Before) -->
            <div class="before-img-clip position-absolute top-0 start-0 w-100 h-100 overflow-hidden" id="beforeImgClip">
              <img 
                src="{{ $sliderBeforeImg }}" 
                alt="{{ $sliderBeforeAlt }}" 
                class="before-after-img before-img position-absolute top-0 start-0 w-100 h-100" 
                draggable="false"
                loading="lazy"
                onerror="if (this.src !== '{{ $defaultBeforeImage }}') this.src='{{ $defaultBeforeImage }}';"
              >
            </div>

            <!-- Floating Pill Badges -->
            <div class="slider-badge slider-badge-before position-absolute">
              <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-camera me-1"></i> {{ $sliderBeforeLabel }}
              </span>
            </div>
            <div class="slider-badge slider-badge-after position-absolute">
              <span class="badge rounded-pill badge-campaign-ready px-3 py-2 fw-bold shadow-sm">
                <i class="bi bi-stars me-1"></i> {{ $sliderAfterLabel }}
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
          <div class="text-center mt-2 d-md-none">
            <small class="text-muted"><i class="bi bi-arrows-expand me-1"></i> Drag slider to compare</small>
          </div>
        </div>

        <!-- Right Column: AI Product Photoshoot Premium Prompt Library Positioning -->
        <div class="col-12 col-lg-6">
          <div class="transformation-details">
            
            <!-- Category & Value Eyebrow Badge -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
              <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                <i class="bi bi-collection-play text-mint me-1"></i> {{ $rightBadge }}
              </span>
              @if ($rightBadgeSub)
              <span class="text-muted small">
                • {{ $rightBadgeSub }}
              </span>
              @endif
            </div>

            <!-- Benefit-Driven Headline -->
            <h3 class="fw-bold title-custom display-6 mb-3" style="font-size: 1.95rem; line-height: 1.25;">
              {!! $rightHeadline !!}
            </h3>

            <!-- Supporting Description -->
            <p class="text-muted mb-4 lead" style="font-size: 1.02rem; line-height: 1.65;">
              {{ $rightDescription }}
            </p>

            <!-- Cost, Speed & Flexibility Comparison Matrix -->
            <div class="comparison-matrix-card p-3 rounded-4 bg-subtle-custom border border-custom mb-4">
              <div class="row g-3">
                <!-- Traditional Photoshoot Column -->
                <div class="col-12 col-sm-6 border-end-sm border-custom">
                  <div class="pe-sm-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <span class="fw-bold text-dark title-custom small text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="bi bi-x-circle text-danger me-1"></i> Traditional Shoot
                      </span>
                    </div>
                    <ul class="list-unstyled mb-2 small text-muted" style="font-size: 0.82rem; line-height: 1.5;">
                      @foreach ($tradItems as $item)
                      <li class="mb-1">• {{ $item }}</li>
                      @endforeach
                    </ul>
                    <div class="pt-2 border-top border-custom">
                      <small class="text-muted d-block" style="font-size: 0.75rem;">Est. Production Cost:</small>
                      <span class="text-danger fw-bold text-decoration-line-through small">
                        {{ $tradPrice }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- AI Prompt Library Column -->
                <div class="col-12 col-sm-6">
                  <div class="ps-sm-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <span class="fw-bold text-mint small text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="bi bi-check-circle-fill text-mint me-1"></i> Prompt Library
                      </span>
                    </div>
                    <ul class="list-unstyled mb-2 small text-muted" style="font-size: 0.82rem; line-height: 1.5;">
                      @foreach ($libItems as $item)
                      <li class="mb-1 text-dark title-custom fw-semibold">• <strong>{{ $item }}</strong></li>
                      @endforeach
                    </ul>
                    <div class="pt-2 border-top border-custom">
                      <small class="text-muted d-block" style="font-size: 0.75rem;">Full Unlimited Access:</small>
                      <span class="text-mint fw-bolder fs-5 lh-1">
                        {{ $libPrice }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Unlimited Access Highlight & Conversion CTA Box -->
            <div class="pricing-cta-card p-3 p-md-4 rounded-4 bg-card-custom border border-custom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 shadow-sm">
              <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                  <span class="badge badge-tier rounded-pill px-2 py-1 fw-bold">
                    <i class="bi bi-infinity me-1"></i> Unlimited Access
                  </span>
                  <span class="fw-bold title-custom fs-6">{{ $ctaTitle }}</span>
                </div>
                <small class="text-muted d-block">
                  @if (!empty($ctaSubtitle))
                    {{ $ctaSubtitle }}
                  @else
                    Unlock the full commercial photoshoot prompt library for just @if ($isIndia) <strong>₹250/mo</strong> (or ₹2,550/yr) @else <strong>$3/mo</strong> (or $27/yr) @endif.
                  @endif
                </small>
              </div>
              
              <div class="flex-shrink-0">
                <a href="{{ url($ctaBtnUrl) }}" class="btn btn-main rounded-pill px-4 py-2.5 fw-bold arrow shadow-sm text-nowrap d-inline-flex align-items-center">
                  <span>{{ $ctaBtnText }}</span>
                </a>
              </div>
            </div>

          </div><!-- /.transformation-details -->
        </div><!-- /.col-lg-6 -->

      </div><!-- /.row -->
    </div><!-- /.transformation-card -->

  </div><!-- /.container -->
</section>

<!-- Scoped Interactive Script for Before/After Drag Slider -->
<script>
(function() {
  function initTransformationSlider() {
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
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTransformationSlider);
  } else {
    initTransformationSlider();
  }
})();
</script>
