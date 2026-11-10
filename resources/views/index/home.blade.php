@extends('layouts.app')

@section('content')
@php
  $homeContent = $homeContent ?? \App\Models\HomepageSetting::getSettings();
  $heroBgUrl = $homeContent->hero_background_image_url ?? asset('public/img/' . $settings->image_header);
@endphp
<div class="container-fluid home-cover position-relative overflow-hidden text-center d-flex flex-column justify-content-center" style="background-image: url('{{ $heroBgUrl }}') !important; background-size: cover !important; background-position: center center !important; min-height: 100vh; padding-top: 120px; padding-bottom: 70px;">
  
  {{-- Atmospheric Gradient Overlay to preserve 100% text and search bar legibility --}}
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 50% 40%, rgba(3, 10, 6, 0.25) 0%, rgba(3, 10, 6, 0.65) 100%), linear-gradient(180deg, rgba(3, 10, 6, 0.25) 0%, transparent 40%, rgba(3, 10, 6, 0.75) 100%); pointer-events: none; z-index: 1;"></div>

  <div class="position-relative w-100 my-auto" style="z-index: 2;">
    <div class="container px-3 px-md-4">

      @if ($settings->announcement != '' && $settings->announcement_show == 'all'
          || $settings->announcement != '' && $settings->announcement_show == 'users' && auth()->check())
        <div class="alert alert-{{$settings->type_announcement}} announcements display-none alert-dismissible fade show text-start mb-4 mx-auto" style="max-width: 820px;" role="alert">
          <h4 class="alert-heading"><i class="bi-megaphone me-2"></i> {{ __('admin.announcements') }}</h4>
          <p class="update-text">
            {!! $settings->announcement !!}
          </p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="closeAnnouncements">
            <i class="bi bi-x-lg"></i>
          </button>
        </div><!-- end announcements -->
      @endif

      <!-- Main Monumental Headline (H1) -->
      <h1 class="home-hero-headline text-white">
        {{ $homeContent->getVal('hero_title', __('seo.welcome_text')) }}
      </h1>

      <!-- Value Proposition Subhead -->
      <p class="home-hero-subhead">
        {{ $homeContent->getVal('hero_subtitle', __('seo.welcome_subtitle')) }}
      </p>

      <!-- Integrated Luxury Search Bar (Matching Design from Second Image) -->
      <div class="prompt-search-wrapper">
        <form action="{{ url('search') }}" method="get" class="position-relative">
          <div class="prompt-search-card">
            <span class="ps-3 pe-2 text-white-50"><i class="bi bi-search fs-5"></i></span>
            <input type="text" 
                   name="q" 
                   class="form-control prompt-search-input" 
                   value="{{ \App\Services\SearchSecurityGuard::sanitize(request('q')) }}" 
                   placeholder="{{ $homeContent->getVal('search_placeholder', 'Search prompt, photoshoot, brand...') }}"
                   autocomplete="off"
                   required
                   minlength="3">
            <button type="submit" class="btn prompt-search-btn rounded-pill px-4 py-2.5 fw-bold">
              Search Prompts <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        <!-- Popular Tags Row (Matching Design in Second Image) -->
        @if (isset($popularCategories) && $popularCategories->isNotEmpty())
          <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 mt-3 pt-1">
            <span class="small text-white-50 me-1">{{ rtrim(__('misc.popular_categories'), ':') }}:</span>
            @foreach ($popularCategories as $popCat)
              @php
                $popCatName = Lang::has('categories.' . $popCat->slug) ? __('categories.' . $popCat->slug) : $popCat->name;
              @endphp
              <a href="{{ url('category', $popCat->slug) }}" class="home-popular-pill">
                {{ $popCatName }}
              </a>
            @endforeach
          </div>
        @elseif ($categoryPopular)
          <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 mt-3 pt-1">
            <span class="small text-white-50 me-1">{{ rtrim(__('misc.popular_categories'), ':') }}:</span>
            {!! $categoryPopular !!}
          </div>
        @endif
      </div>

    </div>
  </div>
</div><!-- container-fluid -->

    {{-- Client Logos Slider Section --}}
    @include('includes.client-logos-slider', ['clientLogos' => $clientLogos])

<div class="container py-5 py-large">

	@if ($images->count() != 0)

  @if ($settings->show_images_index == 'featured' || $settings->show_images_index == 'both')

  <div class="btn-block text-center mb-5">
    <h2 class="m-0 h3">{{__('misc.featured_photos')}}</h2>
    <p>
      {{ __('misc.featured_desc') }}
    </p>
  </div>

  @include('includes.images', ['images' => $featured, 'imgFeatured' => true])

  <div class="w-100 d-block text-center mt-5">
    <a href="{{ url('featured') }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 arrow px-5">
      {{ __('misc.view_all') }}
    </a>
  </div>
  @endif

  @if ($settings->show_images_index == 'latest' || $settings->show_images_index == 'both')
    <div class="btn-block text-center mb-5 @if ($settings->show_images_index == 'both') mt-5 @endif">
      <h2 class="m-0 h3">{{__('misc.recent_photos')}}</h2>
      <p>
        {{ __('misc.latest_desc') }}
      </p>
  </div>

  @include('includes.images', ['images' => $images->take(6)])

  <div class="w-100 d-block text-center mt-5">
    <a href="{{ url('latest') }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 arrow px-5">
      {{ __('misc.view_all') }}
    </a>
  </div>
  @endif
    
	@else
	<h4 class="text-center">
      <div class="d-block w-100 display-2">
        <i class="bi bi-images"></i>
      </div>

		{{ __('misc.no_images_published') }}
	</h4>

  <div class="w-100 d-block text-center mt-3">
    <a href="{{ url('upload') }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 arrow px-5">
      {{ __('users.upload') }}
    </a>
  </div>
	@endif

  @if ($settings->google_adsense && $settings->google_ads_index == 'on' && $settings->google_adsense_index != '')
    <div class="col-md-12 mt-3">
      {!! $settings->google_adsense_index !!}
    </div>
  @endif
</div><!-- container photos -->

    {{-- Show the Difference Section ("WHAT MAKES IMAGINEBUDDY DIFFERENT?") --}}
    @if ($homeContent->diff_enabled)
      @include('includes.home-show-difference')
    @endif

    {{-- 1. Latest Photoshoots Section --}}
    @if ($homeContent->photoshoots_enabled)
      @include('includes.home-latest-photoshoots')
    @endif

    {{-- 2. How It Works Section --}}
    @if ($homeContent->how_it_works_enabled)
      @include('includes.home-how-it-works')
    @endif

    {{-- 3. Who Is ImagineBuddy For Section --}}
    @if ($homeContent->who_is_it_for_enabled)
      @include('includes.home-who-is-it-for')
    @endif

    {{-- 4. Free Prompts and Premium Access Section --}}
    @if ($homeContent->free_vs_pro_enabled)
      @include('includes.home-free-vs-pro')
    @endif

    @if ($images->count() != 0 && $homeContent->promo_section_enabled)
    @php
      $promoItems = $homeContent->getVal('promo_items') ?: [
        ['title' => 'E-commerce Product Images:', 'desc' => 'Clean, product-focused visuals for online stores.'],
        ['title' => 'Premium Studio Photography:', 'desc' => 'Polished compositions with professional-looking lighting.'],
        ['title' => 'Lifestyle Product Photography:', 'desc' => 'Place products in visually engaging environments.'],
        ['title' => 'Social Media Creatives:', 'desc' => 'Develop fresh concepts for Instagram and other social platforms.'],
        ['title' => 'Advertising Visuals:', 'desc' => 'Explore campaign-style compositions for marketing creatives.'],
        ['title' => 'Seasonal Campaign Concepts:', 'desc' => 'Experiment with festive, promotional, and occasion-based styling.'],
      ];
    @endphp
    <section class="section py-5 py-large bg-light home-promo-section border-top border-bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-lg-6 text-center mb-4 mb-lg-0 px-3 px-md-5">
            <img src="{{ url('public/img', $settings->img_section) }}" class="img-fluid rounded-4 shadow-sm" alt="What Can You Create?">
          </div>
          <div class="col-12 col-lg-6 text-lg-start text-center ps-lg-4">
            <span class="badge rounded-pill fw-bold mb-3 px-3 py-2 promo-eyebrow" style="background-color: rgba(0, 214, 144, 0.12); color: #00d690; font-size: 0.78rem; letter-spacing: 0.08em;">
              {{ $homeContent->getVal('promo_eyebrow', 'ONE PRODUCT, MORE CREATIVE POSSIBILITIES') }}
            </span>
            <h2 class="fw-bold mb-2 promo-heading" style="font-size: clamp(1.75rem, 2.8vw, 2.5rem); line-height: 1.25;">
              {{ $homeContent->getVal('promo_heading', 'More Ways to Showcase Your Product') }}
            </h2>
            @if ($homeContent->getVal('promo_subheading'))
            <p class="mb-4 promo-lead" style="font-size: 1rem; line-height: 1.6; color: #64748b;">
              {{ $homeContent->getVal('promo_subheading') }}
            </p>
            @endif

            <ul class="list-unstyled mb-4 text-start promo-concepts-list">
              @foreach ($promoItems as $pItem)
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">{{ $pItem['title'] ?? '' }}</strong> <span class="promo-item-desc text-secondary-custom">{{ $pItem['desc'] ?? '' }}</span>
                </div>
              </li>
              @endforeach
            </ul>

            @if ($homeContent->getVal('promo_note'))
            <div class="mb-4 promo-note p-3 rounded-3" style="border-left: 3px solid #00d690;">
              <p class="small mb-0" style="line-height: 1.6; font-size: 0.88rem;">
                {{ $homeContent->getVal('promo_note') }}
              </p>
            </div>
            @endif

            <div class="mt-2 pt-1">
              <a href="{{ url($homeContent->getVal('promo_cta_url', 'categories')) }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 px-md-5 py-2.5 arrow shadow-sm fw-semibold">
                {{ $homeContent->getVal('promo_cta_text', 'Find Your Photography Style') }}
              </a>
            </div>
        </div>
      </div>
    </section>
    @endif

    @if ($settings->show_counter == 'on')
    <section class="section py-2 bg-dark text-white counter-stats">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-people align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ $userCount }}</span></h3>
                <h5>{{__('misc.members')}}</h5>
              </div>
            </div>

          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-download align-baseline"></i></span>
              <div>
                <h3 class="mb-0"><span class="counter">{{ $downloadsCount }}</span></h3>
                <h5 class="font-weight-light">{{__('misc.downloads')}}</h5>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex py-3 my-1 my-lg-0 justify-content-center">
              <span class="me-3 display-4"><i class="bi bi-images align-baseline"></i></span>
              <div>
                <h3 class="mb-0"> <span class="counterStats">{{ $imagesCount }}</span></h3>
                <h5 class="font-weight-light">{{__('misc.stock_photos')}}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

    {{-- Transformation Before/After Showcase Section --}}
    @if ($homeContent->transformation_enabled)
      @include('includes.transformation-section')
    @endif

    @if (isset($testimonials) && $testimonials->isNotEmpty())
    <section class="section py-5 py-large bg-light testimonials-section">
      <div class="container">
        <div class="btn-block text-center mb-5">
          <h2 class="m-0 h3">{{ __('misc.creators_testimonials_heading') }}</h2>
          <p class="text-muted mt-2">
            {{ __('misc.creators_testimonials_subtitle') }}
          </p>
        </div>

        @include('includes.testimonials-slider', ['testimonials' => $testimonials])
      </div>
    </section>
    @endif

	@if ($settings->show_categories_index == 'on')
    <section class="section py-5 py-large">
      <div class="container">
        <div class="btn-block text-center mb-5">
          <h2 class="m-0 h3">{{__('misc.categories')}}</h2>
          <p>
            {{__('misc.browse_by_category')}}
          </p>
        </div>

        <div class="row">
		@include('includes.categories-listing')
        </div>

    @if ($categoriesCount > 4)
    <div class="w-100 d-block text-center mt-4">
      <a href="{{ url('categories') }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 arrow px-5">
        {{ __('misc.view_all') }}
      </a>
    </div>
    @endif

      </div>
    </section>
    @endif

    {{-- FAQ Section for Prompt Library --}}
    @if ($homeContent->faq_enabled)
      @include('includes.faq-section')
    @endif

<style>
.home-hero-title {
  font-size: clamp(1.85rem, 3.5vw, 3.5rem);
  line-height: 1.2;
}
@media (min-width: 992px) {
  .home-hero-title {
    white-space: nowrap !important;
  }
}

/* Home Promo Section ("What Can You Create? / More Ways to Showcase Your Product") */
.home-promo-section {
  background-color: #fafbfc !important;
  border-color: #edf2f7 !important;
}
.home-promo-section .promo-heading {
  color: #0f172a !important;
  font-size: clamp(1.5rem, 2.3vw, 2.25rem) !important;
}
@media (min-width: 992px) {
  .home-promo-section .promo-heading {
    white-space: nowrap !important;
  }
}
.home-promo-section .promo-lead {
  color: #475569 !important;
}
.home-promo-section .promo-item-title {
  color: #1e293b !important;
}
.home-promo-section .promo-item-desc {
  color: #64748b !important;
}
.home-promo-section .promo-note {
  color: #64748b !important;
  background-color: rgba(0, 214, 144, 0.05);
  padding: 8px 12px;
  border-radius: 0 6px 6px 0;
}

[data-bs-theme="dark"] .home-promo-section {
  background-color: #12151a !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .home-promo-section .promo-heading {
  color: #ffffff !important;
}
[data-bs-theme="dark"] .home-promo-section .promo-lead {
  color: #94a3b8 !important;
}
[data-bs-theme="dark"] .home-promo-section .promo-item-title {
  color: #f1f5f9 !important;
}
[data-bs-theme="dark"] .home-promo-section .promo-item-desc {
  color: #94a3b8 !important;
}
[data-bs-theme="dark"] .home-promo-section .promo-note {
  color: #94a3b8 !important;
  background-color: rgba(0, 214, 144, 0.08);
}

/* Testimonials Section Dark Mode */
[data-bs-theme="dark"] .testimonials-section {
  background-color: #0b0e14 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-bs-theme="dark"] .testimonials-section h2 {
  color: #ffffff !important;
}
[data-bs-theme="dark"] .testimonials-section p.text-muted {
  color: #94a3b8 !important;
}

/* --- Modern Luxury Hero Styles (Matching /prompts design) --- */
.home-hero-headline {
  font-size: clamp(1.6rem, 2.9vw, 2.85rem);
  font-weight: 800;
  line-height: 1.2;
  letter-spacing: -0.025em;
  color: #FFFFFF !important;
  margin-top: 18px;
  margin-bottom: 18px;
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.70);
}

@media (min-width: 992px) {
  .home-hero-headline {
    white-space: nowrap;
  }
}

.home-hero-subhead {
  font-size: clamp(1rem, 1.4vw, 1.2rem);
  line-height: 1.65;
  color: rgba(255, 255, 255, 0.90);
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 32px;
  text-shadow: 0 1px 8px rgba(0, 0, 0, 0.75);
}

.prompt-search-wrapper {
  max-width: 680px;
  margin-left: auto;
  margin-right: auto;
}

.prompt-search-card {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 9999px;
  padding: 6px;
  display: flex;
  align-items: center;
  box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.45);
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.prompt-search-card:focus-within {
  border-color: #00DF81;
  box-shadow: 0 0 28px -4px rgba(0, 223, 129, 0.35);
}

.prompt-search-input {
  background: transparent !important;
  border: none !important;
  color: #FFFFFF !important;
  font-size: 1rem;
  padding-left: 12px;
}

.prompt-search-input::placeholder {
  color: rgba(255, 255, 255, 0.50);
}

.prompt-search-input:focus {
  outline: none !important;
  box-shadow: none !important;
}

.prompt-search-btn {
  background: #00DF81;
  color: #041209;
  border: none;
  font-weight: 700;
  font-size: 0.95rem;
  padding: 10px 24px;
  border-radius: 9999px;
  white-space: nowrap;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.prompt-search-btn:hover {
  background: #00bf6f;
  color: #041209;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0, 223, 129, 0.4);
}

.home-popular-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 14px;
  border-radius: 9999px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  color: rgba(255, 255, 255, 0.85) !important;
  font-size: 0.82rem;
  text-decoration: none !important;
  transition: all 0.2s ease;
}

.home-popular-pill:hover {
  background: rgba(0, 223, 129, 0.18);
  border-color: #00DF81;
  color: #FFFFFF !important;
  transform: translateY(-1px);
}

@media (max-width: 576px) {
  .prompt-search-card {
    border-radius: 18px;
    flex-direction: column;
    padding: 10px;
    gap: 10px;
  }
  .prompt-search-btn {
    width: 100%;
  }
}
</style>
@endsection

@section('javascript')
	<script type="text/javascript">

		@if (session('success_verify'))
		swal({
			title: "{{ __('misc.welcome') }}",
			text: "{{ __('users.account_validated') }}",
			type: "success",
			confirmButtonText: "{{ __('users.ok') }}"
			});
		@endif

		@if (session('error_verify'))
		swal({
			title: "{{ __('misc.error_oops') }}",
			text: "{{ __('users.code_not_valid') }}",
			type: "error",
			confirmButtonText: "{{ __('users.ok') }}"
			});
		@endif

	</script>
@endsection
