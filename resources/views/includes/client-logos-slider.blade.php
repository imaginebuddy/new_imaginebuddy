@if (isset($clientLogos) && $clientLogos->isNotEmpty())

{{-- Swiper CSS only loaded when sliding carousel is active --}}
@if ($clientLogos->count() > 4)
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endif

<section class="client-logos-section py-4 py-md-5 border-top border-bottom">
  <div class="container px-3 px-md-4">
    
    {{-- Refined Section Micro-Heading Badge --}}
    <div class="text-center mb-4 mb-md-5">
      <span class="client-logos-badge">
        <i class="bi bi-patch-check-fill text-primary me-2"></i>
        {{ __('misc.trusted_by_heading') }}
      </span>
    </div>

    {{-- Layout A: Static Centered Row for 1 to 4 logos --}}
    @if ($clientLogos->count() <= 4)
      <div class="client-logos-static d-flex justify-content-center align-items-center flex-wrap gap-3 gap-md-4">
        @foreach ($clientLogos as $logo)
          @if ($logo->image_url)
            <div class="client-logo-item">
              @if ($logo->website_url)
                <a href="{{ $logo->website_url }}" target="_blank" rel="noopener noreferrer" class="client-logo-card" title="{{ $logo->name }}">
                  <img src="{{ $logo->image_url }}" alt="{{ $logo->name }}" class="client-logo-img" loading="lazy">
                </a>
              @else
                <div class="client-logo-card" title="{{ $logo->name }}">
                  <img src="{{ $logo->image_url }}" alt="{{ $logo->name }}" class="client-logo-img" loading="lazy">
                </div>
              @endif
            </div>
          @endif
        @endforeach
      </div>

    {{-- Layout B: Swiper Infinite Continuous Marquee for 5+ logos --}}
    @else
      @php
        // Ensure enough duplicated slides for a seamless, glitch-free continuous loop in Swiper
        $displayLogos = $clientLogos;
        if ($clientLogos->count() >= 5 && $clientLogos->count() < 14) {
          $displayLogos = $clientLogos->concat($clientLogos);
          if ($displayLogos->count() < 14) {
            $displayLogos = $displayLogos->concat($clientLogos);
          }
        }
      @endphp

      <div class="client-logos-marquee-wrapper">
        <div class="swiper client-logos-swiper">
          <div class="swiper-wrapper align-items-center">
            @foreach ($displayLogos as $logo)
              @if ($logo->image_url)
                <div class="swiper-slide text-center client-logo-slide">
                  @if ($logo->website_url)
                    <a href="{{ $logo->website_url }}" target="_blank" rel="noopener noreferrer" class="client-logo-card" title="{{ $logo->name }}">
                      <img src="{{ $logo->image_url }}" alt="{{ $logo->name }}" class="client-logo-img" loading="lazy">
                    </a>
                  @else
                    <div class="client-logo-card" title="{{ $logo->name }}">
                      <img src="{{ $logo->image_url }}" alt="{{ $logo->name }}" class="client-logo-img" loading="lazy">
                    </div>
                  @endif
                </div>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    @endif

  </div>
</section>

<style>
/* Section Container */
.client-logos-section {
  background-color: var(--bs-body-bg);
  border-color: var(--bs-border-color-translucent) !important;
  position: relative;
}

/* Subtle Section Pill Badge */
.client-logos-badge {
  display: inline-flex;
  align-items: center;
  font-size: 0.76rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--bs-secondary-color, #6c757d);
  background: var(--bs-tertiary-bg, rgba(0, 0, 0, 0.03));
  border: 1px solid var(--bs-border-color-translucent, rgba(0, 0, 0, 0.08));
  padding: 6px 18px;
  border-radius: 50px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

/* Modern Gradient Edge Fading Mask */
.client-logos-marquee-wrapper {
  position: relative;
  overflow: hidden;
  mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
}

.client-logo-slide,
.client-logo-item {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 0;
}

/* Uniform Systemic Logo Card */
.client-logo-card {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 80px;
  width: 100%;
  max-width: 195px;
  padding: 12px 22px;
  border-radius: 14px;
  background: var(--bs-card-bg, #ffffff);
  border: 1px solid var(--bs-border-color-translucent, rgba(0, 0, 0, 0.08));
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  text-decoration: none;
}

.client-logo-card:hover {
  transform: translateY(-2px);
  border-color: rgba(0, 0, 0, 0.16);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.07);
}

/* Balanced Optical Image Constraints */
.client-logo-img {
  max-height: 48px;
  max-width: 138px;
  width: auto;
  height: auto;
  object-fit: contain;
  filter: grayscale(100%) contrast(1.12);
  opacity: 0.75;
  transition: filter 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
}

/* Hover State */
.client-logo-card:hover .client-logo-img {
  filter: grayscale(0%) contrast(1);
  opacity: 1;
  transform: scale(1.05);
}

/* Dark Mode */
[data-bs-theme="dark"] .client-logos-section {
  background-color: var(--bs-dark-bg-subtle, #121417) !important;
}

[data-bs-theme="dark"] .client-logos-badge {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(255, 255, 255, 0.1);
  color: #adb5bd;
}

[data-bs-theme="dark"] .client-logo-card {
  background: rgba(255, 255, 255, 0.03);
  border-color: rgba(255, 255, 255, 0.08);
  box-shadow: none;
}

[data-bs-theme="dark"] .client-logo-card:hover {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}

[data-bs-theme="dark"] .client-logo-img {
  filter: grayscale(100%) invert(0.85) contrast(1.15);
  opacity: 0.78;
}

[data-bs-theme="dark"] .client-logo-card:hover .client-logo-img {
  filter: grayscale(0%) invert(0);
  opacity: 1;
}

/* Continuous Linear Marquee Animation for Swiper */
.client-logos-swiper .swiper-wrapper {
  transition-timing-function: linear !important;
}

/* Mobile Adjustments */
@media (max-width: 575.98px) {
  .client-logo-card {
    height: 64px;
    padding: 8px 16px;
    max-width: 155px;
  }
  .client-logo-img {
    max-height: 38px;
    max-width: 110px;
  }
  .client-logos-badge {
    font-size: 0.7rem;
    padding: 5px 14px;
  }
}
</style>

@if ($clientLogos->count() > 4)
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof Swiper !== 'undefined') {
        const logoSwiper = new Swiper('.client-logos-swiper', {
          slidesPerView: 2.2,
          spaceBetween: 16,
          loop: true,
          freeMode: true,
          speed: 4500,
          autoplay: {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          },
          breakpoints: {
            480: {
              slidesPerView: 3,
              spaceBetween: 20,
            },
            768: {
              slidesPerView: 4,
              spaceBetween: 24,
            },
            992: {
              slidesPerView: 5,
              spaceBetween: 28,
            },
            1200: {
              slidesPerView: 6,
              spaceBetween: 32,
            },
          },
        });
      }
    });
  </script>
@endif

@endif
