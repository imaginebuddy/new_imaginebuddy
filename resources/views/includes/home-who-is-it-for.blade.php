@php
  $homeContent = $homeContent ?? \App\Models\HomepageSetting::getSettings();
  $personas = $homeContent->getVal('who_is_it_for_items') ?: [
    [
      'title' => 'E-commerce Business Owners',
      'desc' => 'Develop more visual concepts for your product catalogue.'
    ],
    [
      'title' => 'Brand Owners',
      'desc' => 'Explore fresh campaign ideas without planning every concept from scratch.'
    ],
    [
      'title' => 'Digital Marketers',
      'desc' => 'Create starting points for advertising and social media creatives.'
    ],
    [
      'title' => 'Designers & Creative Agencies',
      'desc' => 'Experiment with product-focused visual directions.'
    ],
    [
      'title' => 'Photographers',
      'desc' => 'Use AI to explore moodboards, compositions, and creative references.'
    ],
    [
      'title' => 'Content Creators',
      'desc' => 'Develop product visuals for marketing content and brand collaborations.'
    ]
  ];
@endphp
<!-- Who Is ImagineBuddy For Section -->
<section class="section py-4 py-md-5 text-white position-relative who-is-it-for-section border-top border-bottom" style="background-color: #0b0e14 !important; border-color: rgba(255, 255, 255, 0.08) !important;">
  <div class="container px-3 px-md-4">
    <div class="mb-4 mb-md-5 text-center mx-auto" style="max-width: 1000px;">
      <h2 class="fw-bold text-white mb-2 mb-md-3 who-is-it-for-title">
        {{ $homeContent->getVal('who_is_it_for_heading', 'Built for People Who Need Better Product Visuals') }}
      </h2>
      @if ($homeContent->getVal('who_is_it_for_subheading'))
      <p class="lead mb-0 text-secondary-custom mx-auto" style="font-size: clamp(0.98rem, 1.8vw, 1.1rem); line-height: 1.6; color: #94a3b8 !important; max-width: 760px;">
        {{ $homeContent->getVal('who_is_it_for_subheading') }}
      </p>
      @endif
    </div>

    <div class="row g-3 g-md-4 g-lg-5 mb-4 mb-md-5">
      @foreach ($personas as $persona)
      <div class="col-12 col-sm-6 col-md-4">
        <div class="persona-block pt-3 pt-md-4 border-top" style="border-color: rgba(255, 255, 255, 0.12) !important;">
          <h3 class="fw-bold text-white mb-2" style="font-size: clamp(1.02rem, 2vw, 1.15rem);">
            {{ $persona['title'] ?? '' }}
          </h3>
          <p class="mb-0" style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6;">
            {{ $persona['desc'] ?? '' }}
          </p>
        </div>
      </div>
      @endforeach
    </div>

    <!-- CTA Button -->
    <div class="text-center mt-2">
      <a href="{{ url($homeContent->getVal('who_is_it_for_cta_url', 'latest')) }}" class="btn btn-main rounded-pill btn-custom px-4 px-md-5 py-2.5 arrow shadow-sm fw-semibold">
        {{ $homeContent->getVal('who_is_it_for_cta_text', 'Discover What You Can Create') }}
      </a>
    </div>
  </div>
</section>

<style>
.who-is-it-for-section {
  background-color: #0b0e14 !important;
}

.who-is-it-for-title {
  font-size: clamp(1.45rem, 2.6vw, 2.35rem);
  line-height: 1.25;
}

@media (min-width: 992px) {
  .who-is-it-for-title {
    white-space: nowrap !important;
  }
}

.persona-block {
  transition: transform 0.2s ease;
}

@media (min-width: 992px) {
  .persona-block:hover {
    transform: translateY(-2px);
  }
}
</style>
