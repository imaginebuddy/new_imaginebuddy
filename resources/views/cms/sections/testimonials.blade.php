<section class="cms-section testimonials-section" id="testimonials" style="background-color: var(--cms-bg-subtle, #f8fafc);">
  <div class="container">
    <div class="cms-section-header">
      @if (!empty($section?->badge))
        <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
      @else
        <span class="cms-eyebrow-badge mb-2">COMMUNITY TRUST</span>
      @endif
      <h2 class="cms-section-title">
        {{ $section?->heading ?: __('misc.creators_testimonials_heading') }}
      </h2>
      @if (!empty($section?->subheading))
        <p class="cms-section-sub">{{ $section->subheading }}</p>
      @else
        <p class="cms-section-sub">{{ __('misc.creators_testimonials_subtitle') }}</p>
      @endif
    </div>

    @include('includes.testimonials-slider', ['testimonials' => $testimonials])
  </div>
</section>
