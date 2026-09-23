@if (isset($testimonials) && $testimonials->isNotEmpty())
<div class="testimonials-carousel-wrapper position-relative px-md-4">
  
  {{-- Slider Track Container --}}
  <div class="testimonials-track-container overflow-hidden position-relative" id="testimonialsSliderTrack">
    <div class="testimonials-track d-flex" id="testimonialsTrack">
      @foreach ($testimonials as $index => $item)
        <div class="testimonial-slide-item flex-shrink-0" data-index="{{ $index }}">
          <div class="card testimonial-card border-0 shadow-sm rounded-4 h-100 p-4 d-flex flex-column justify-content-between position-relative">
            
            {{-- Top Row: Quote Icon & Rating --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
              <span class="testimonial-quote-icon text-primary opacity-25 fs-1 lh-1">
                <i class="bi bi-quote"></i>
              </span>
              @if ($item->rating)
                <div class="testimonial-rating text-warning" title="{{ $item->rating }} / 5 stars">
                  @for ($s = 1; $s <= 5; $s++)
                    @if ($s <= $item->rating)
                      <i class="bi bi-star-fill small"></i>
                    @else
                      <i class="bi bi-star text-muted opacity-25 small"></i>
                    @endif
                  @endfor
                </div>
              @endif
            </div>

            {{-- Review Content --}}
            <div class="testimonial-body flex-grow-1 mb-4">
              <p class="testimonial-text fs-6 lh-base mb-0">
                “{{ $item->content }}”
              </p>
            </div>

            {{-- Bottom Row: Creator Profile --}}
            <div class="testimonial-author d-flex align-items-center pt-3 border-top border-light-subtle">
              <div class="flex-shrink-0 me-3">
                @if ($item->image_url)
                  <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="rounded-circle border shadow-xs" width="48" height="48" style="object-fit: cover;">
                @else
                  <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 48px; height: 48px; font-size: 1.1rem;">
                    {{ strtoupper(substr($item->name, 0, 1)) }}
                  </div>
                @endif
              </div>
              <div class="testimonial-meta overflow-hidden">
                <h6 class="fw-bold mb-0 title-custom text-truncate">{{ $item->name }}</h6>
                @if ($item->designation || $item->company)
                  <small class="text-muted d-block text-truncate">
                    {{ $item->designation }}
                    @if ($item->designation && $item->company) <span class="opacity-50">·</span> @endif
                    <span class="fw-semibold">{{ $item->company }}</span>
                  </small>
                @endif
              </div>
            </div>

          </div><!-- /.card -->
        </div><!-- /.testimonial-slide-item -->
      @endforeach
    </div><!-- /.testimonials-track -->
  </div><!-- /.testimonials-track-container -->

  {{-- Navigation Controls (Shown if more than 1 testimonial) --}}
  @if ($testimonials->count() > 1)
    <div class="testimonials-controls d-flex justify-content-center align-items-center gap-3 mt-4">
      <button type="button" class="btn btn-sm btn-outline-dark rounded-circle shadow-xs testimonial-nav-btn" id="testimonialBtnPrev" aria-label="Previous testimonial" style="width: 42px; height: 42px;">
        <i class="bi bi-chevron-left fs-6"></i>
      </button>

      {{-- Dot Indicators --}}
      <div class="testimonials-dots d-flex gap-2" id="testimonialDots"></div>

      <button type="button" class="btn btn-sm btn-outline-dark rounded-circle shadow-xs testimonial-nav-btn" id="testimonialBtnNext" aria-label="Next testimonial" style="width: 42px; height: 42px;">
        <i class="bi bi-chevron-right fs-6"></i>
      </button>
    </div>
  @endif

</div><!-- /.testimonials-carousel-wrapper -->

<style>
.testimonials-track-container {
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
}
.testimonials-track-container::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
.testimonials-track {
  gap: 24px;
  padding: 12px 4px 16px 4px;
}
/* Desktop Default (3 cards) */
.testimonial-slide-item {
  width: calc((100% - 48px) / 3);
  scroll-snap-align: start;
}
/* Tablet (2 cards) */
@media (max-width: 991.98px) {
  .testimonial-slide-item {
    width: calc((100% - 24px) / 2);
  }
}
/* Mobile (1 card) */
@media (max-width: 767.98px) {
  .testimonial-slide-item {
    width: 100%;
  }
}

.testimonial-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  background-color: var(--bs-card-bg, #ffffff);
}
.testimonial-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08) !important;
}

.testimonial-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: rgba(150, 150, 150, 0.4);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 0;
  padding: 0;
}
.testimonial-dot.active {
  width: 24px;
  border-radius: 12px;
  background-color: var(--bs-dark, #212529);
}

.testimonial-text {
  color: #1e293b;
}

/* Dark mode compatibility */
[data-bs-theme="dark"] .testimonial-card {
  background-color: #1a1e21 !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .testimonial-text {
  color: #e2e8f0 !important;
}
[data-bs-theme="dark"] .testimonial-nav-btn {
  border-color: rgba(255, 255, 255, 0.25) !important;
  color: #ffffff !important;
}
[data-bs-theme="dark"] .testimonial-nav-btn:hover {
  background-color: rgba(255, 255, 255, 0.1) !important;
}
[data-bs-theme="dark"] .testimonial-dot.active {
  background-color: #ffffff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('testimonialsSliderTrack');
  const track = document.getElementById('testimonialsTrack');
  const prevBtn = document.getElementById('testimonialBtnPrev');
  const nextBtn = document.getElementById('testimonialBtnNext');
  const dotsContainer = document.getElementById('testimonialDots');

  if (!container || !track) return;

  const items = track.querySelectorAll('.testimonial-slide-item');
  if (items.length <= 1) return;

  function getVisibleCount() {
    if (window.innerWidth >= 992) return 3;
    if (window.innerWidth >= 768) return 2;
    return 1;
  }

  let totalPages = Math.max(1, items.length - getVisibleCount() + 1);
  let currentIndex = 0;
  let autoplayTimer = null;

  function renderDots() {
    if (!dotsContainer) return;
    dotsContainer.innerHTML = '';
    totalPages = Math.max(1, items.length - getVisibleCount() + 1);

    for (let i = 0; i < totalPages; i++) {
      const dot = document.createElement('button');
      dot.className = 'testimonial-dot' + (i === currentIndex ? ' active' : '');
      dot.setAttribute('aria-label', 'Go to testimonial page ' + (i + 1));
      dot.addEventListener('click', function() {
        goToSlide(i);
      });
      dotsContainer.appendChild(dot);
    }
  }

  function updateDots() {
    if (!dotsContainer) return;
    const dots = dotsContainer.querySelectorAll('.testimonial-dot');
    dots.forEach((dot, idx) => {
      if (idx === currentIndex) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });
  }

  function getSlideDistance() {
    if (items.length === 0) return 0;
    const itemWidth = items[0].offsetWidth;
    const gap = 24; // matches CSS gap
    return itemWidth + gap;
  }

  function goToSlide(index) {
    totalPages = Math.max(1, items.length - getVisibleCount() + 1);
    if (index >= totalPages) {
      currentIndex = 0;
    } else if (index < 0) {
      currentIndex = totalPages - 1;
    } else {
      currentIndex = index;
    }

    const scrollAmount = currentIndex * getSlideDistance();
    container.scrollTo({
      left: scrollAmount,
      behavior: 'smooth'
    });
    updateDots();
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      goToSlide(currentIndex + 1);
      resetAutoplay();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      goToSlide(currentIndex - 1);
      resetAutoplay();
    });
  }

  // Touch Swipe Support
  let touchStartX = 0;
  let touchEndX = 0;

  container.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoplay();
  }, { passive: true });

  container.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    startAutoplay();
  }, { passive: true });

  function handleSwipe() {
    const swipeDistance = touchStartX - touchEndX;
    if (Math.abs(swipeDistance) > 40) {
      if (swipeDistance > 0) {
        goToSlide(currentIndex + 1);
      } else {
        goToSlide(currentIndex - 1);
      }
    }
  }

  // Autoplay
  function startAutoplay() {
    if (items.length <= getVisibleCount()) return;
    stopAutoplay();
    autoplayTimer = setInterval(function() {
      goToSlide(currentIndex + 1);
    }, 6000);
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function resetAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  container.addEventListener('mouseenter', stopAutoplay);
  container.addEventListener('mouseleave', startAutoplay);

  // Resize handler
  let resizeTimeout;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
      renderDots();
      goToSlide(Math.min(currentIndex, totalPages - 1));
    }, 150);
  });

  renderDots();
  startAutoplay();
});
</script>
@endif
