<header class="cms-detail-hero">
  <div class="container">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="cms-hero-breadcrumb mb-4">
      <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
        @if (!empty($page->route_prefix))
          <li class="breadcrumb-item">
            <a href="{{ url($page->route_prefix) }}" class="text-decoration-none">
              {{ ucwords(str_replace('-', ' ', $page->route_prefix)) }}
            </a>
          </li>
        @endif
        <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 360px;">
          {{ $page->title }}
        </li>
      </ol>
    </nav>

    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-6">
        @if (!empty($page->header_badge) || !empty($section->badge))
          <div class="mb-2">
            <span class="cms-eyebrow-badge">
              <i class="bi bi-patch-check-fill"></i> {{ $page->header_badge ?: $section->badge }}
            </span>
          </div>
        @endif

        <h1 class="cms-detail-hero-title">
          {!! $section->heading ?: $page->title !!}
        </h1>

        @if (!empty($section->subheading) || !empty($page->excerpt))
          <p class="cms-detail-hero-lead">
            {{ $section->subheading ?: $page->excerpt }}
          </p>
        @endif

        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
          <a href="{{ $page->header_cta_url ?: url('register') }}" class="btn-cms-primary">
            {{ $page->header_cta_text ?: 'Get Started Free' }} <i class="bi bi-arrow-right"></i>
          </a>
          <a href="#features" class="btn-cms-secondary">
            Explore Capabilities <i class="bi bi-arrow-down-circle"></i>
          </a>
        </div>

        @if (!empty($page->header_cta_subtext))
          <div class="cms-hero-trust-badges small d-flex align-items-center gap-2">
            <i class="bi bi-shield-check text-mint"></i> {{ $page->header_cta_subtext }}
          </div>
        @else
          <div class="cms-hero-trust-badges small d-flex flex-wrap align-items-center gap-3">
            <span><i class="bi bi-check-circle-fill text-mint me-1"></i> No credit card required</span>
            <span><i class="bi bi-check-circle-fill text-mint me-1"></i> Instant high-res export</span>
            <span><i class="bi bi-check-circle-fill text-mint me-1"></i> Full commercial rights</span>
          </div>
        @endif
      </div>

      <div class="col-lg-6">
        @php
          $sliderBeforeImg = $page->before_image_url;
          $sliderAfterImg = $page->after_image_url;
          $sliderBeforeLabel = $page->before_label ?: 'Raw Snapshot';
          $sliderAfterLabel = $page->after_label ?: 'Campaign Ready';
          $sliderId = 'heroTransformationSlider_' . $page->id;
        @endphp
        <div class="before-after-slider-container shadow-lg position-relative overflow-hidden rounded-4" id="{{ $sliderId }}">
          <!-- Background: Campaign Ready Image (After) -->
          <img 
            src="{{ $sliderAfterImg }}" 
            alt="{{ $page->title }} (After)" 
            class="before-after-img after-img w-100 h-100" 
            draggable="false"
            loading="eager"
          >

          <!-- Foreground: Raw Snapshot Image (Before) -->
          <div class="before-img-clip position-absolute top-0 start-0 w-100 h-100 overflow-hidden" id="{{ $sliderId }}_clip">
            <img 
              src="{{ $sliderBeforeImg }}" 
              alt="{{ $page->title }} (Before)" 
              class="before-after-img before-img position-absolute top-0 start-0 w-100 h-100" 
              draggable="false"
              loading="eager"
            >
          </div>

          <!-- Floating Badges -->
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

          <!-- Draggable Divider & Handle -->
          <div class="slider-divider position-absolute top-0 bottom-0" id="{{ $sliderId }}_divider">
            <div 
              class="slider-handle shadow" 
              id="{{ $sliderId }}_handle" 
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
            id="{{ $sliderId }}_range"
            aria-label="Before and after percentage"
          >
        </div>

        <script>
        (function() {
          function initHeroSlider() {
            const container = document.getElementById('{{ $sliderId }}');
            const clip = document.getElementById('{{ $sliderId }}_clip');
            const divider = document.getElementById('{{ $sliderId }}_divider');
            const handle = document.getElementById('{{ $sliderId }}_handle');
            const rangeInput = document.getElementById('{{ $sliderId }}_range');

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
                }
              });
            }

            updateSlider(50);
          }

          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeroSlider);
          } else {
            initHeroSlider();
          }
        })();
        </script>
      </div>
    </div>
  </div>
</header>
