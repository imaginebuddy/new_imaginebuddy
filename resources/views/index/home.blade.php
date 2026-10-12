@extends('layouts.app')

@section('content')
<div class="container-fluid home-cover" style="background-image: url('{{ asset('public/img/' . $settings->image_header) }}') !important; background-size: cover !important; background-position: center center !important;">
      <div class="mb-4 position-relative custom-pt-6">
        <div class="container px-5">

          @if ($settings->announcement != '' && $settings->announcement_show == 'all'
              || $settings->announcement != '' && $settings->announcement_show == 'users' && auth()->check())
            <div class="alert alert-{{$settings->type_announcement}} announcements display-none alert-dismissible fade show" role="alert">
              
              <h4 class="alert-heading"><i class="bi-megaphone me-2"></i> {{ __('admin.announcements') }}</h4>

              <p class="update-text">
                {!! $settings->announcement !!}
              </p>
  
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="closeAnnouncements">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div><!-- end announcements -->
            @endif

          <h1 class="display-3 fw-bold text-white home-hero-title">{{ __('seo.welcome_text') }}</h1>
          <p class="col-md-12 fs-4 fw-bold text-white">{{ __('seo.welcome_subtitle') }}</p>
          <form action="{{ url('search') }}" method="get" class="position-relative">
            <i class="bi bi-search btn-search"></i>
            <input class="form-control form-control-lg ps-5 input-search-lg border-0 search-lg" type="text" name="q" value="{{ \App\Services\SearchSecurityGuard::sanitize(request('q')) }}" autocomplete="off" placeholder="{{__('misc.search')}}" required minlength="3">
          </form>

		  @if ($categoryPopular)
          <p class="mt-2 text-white linkCategoryPopular">
            <strong>{{__('misc.popular_categories')}}</strong> {!! $categoryPopular !!}
          </p>
		  @endif

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
    @include('includes.home-show-difference')

    {{-- 1. Latest Photoshoots Section --}}
    @include('includes.home-latest-photoshoots')

    {{-- 2. How It Works Section --}}
    @include('includes.home-how-it-works')

    {{-- 3. Who Is ImagineBuddy For Section --}}
    @include('includes.home-who-is-it-for')

    {{-- 4. Free Prompts and Premium Access Section --}}
    @include('includes.home-free-vs-pro')

    @if ($images->count() != 0)
    <section class="section py-5 py-large bg-light home-promo-section border-top border-bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-lg-6 text-center mb-4 mb-lg-0 px-3 px-md-5">
            <img src="{{ url('public/img', $settings->img_section) }}" class="img-fluid rounded-4 shadow-sm" alt="What Can You Create?">
          </div>
          <div class="col-12 col-lg-6 text-lg-start text-center ps-lg-4">
            <span class="badge rounded-pill fw-bold mb-3 px-3 py-2 promo-eyebrow" style="background-color: rgba(0, 214, 144, 0.12); color: #00d690; font-size: 0.78rem; letter-spacing: 0.08em;">
              ONE PRODUCT, MORE CREATIVE POSSIBILITIES
            </span>
            <h2 class="fw-bold mb-2 promo-heading" style="font-size: clamp(1.75rem, 2.8vw, 2.5rem); line-height: 1.25;">
              More Ways to Showcase Your Product
            </h2>
            <p class="mb-4 promo-lead" style="font-size: 1rem; line-height: 1.6; color: #64748b;">
              Your product may need more than one image. Explore different photography concepts for different marketing goals.
            </p>

            <ul class="list-unstyled mb-4 text-start promo-concepts-list">
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">E-commerce Product Images:</strong> <span class="promo-item-desc text-secondary-custom">Clean, product-focused visuals for online stores.</span>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">Premium Studio Photography:</strong> <span class="promo-item-desc text-secondary-custom">Polished compositions with professional-looking lighting.</span>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">Lifestyle Product Photography:</strong> <span class="promo-item-desc text-secondary-custom">Place products in visually engaging environments.</span>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">Social Media Creatives:</strong> <span class="promo-item-desc text-secondary-custom">Develop fresh concepts for Instagram and other social platforms.</span>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">Advertising Visuals:</strong> <span class="promo-item-desc text-secondary-custom">Explore campaign-style compositions for marketing creatives.</span>
                </div>
              </li>
              <li class="d-flex align-items-start mb-3">
                <i class="bi bi-check-circle-fill text-mint me-3 mt-1 flex-shrink-0" style="color: #00d690; font-size: 1.15rem; line-height: 1;"></i>
                <div style="font-size: 0.95rem; line-height: 1.6;">
                  <strong class="promo-item-title">Seasonal Campaign Concepts:</strong> <span class="promo-item-desc text-secondary-custom">Experiment with festive, promotional, and occasion-based styling.</span>
                </div>
              </li>
            </ul>

            <div class="mb-4 promo-note p-3 rounded-3" style="border-left: 3px solid #00d690;">
              <p class="small mb-0" style="line-height: 1.6; font-size: 0.88rem;">
                Use these concepts as starting points and review every generated image for product accuracy, readable branding, and realistic details.
              </p>
            </div>

            <div class="mt-2 pt-1">
              <a href="{{ url('categories') }}" class="btn btn-lg btn-main rounded-pill btn-custom px-4 px-md-5 py-2.5 arrow shadow-sm fw-semibold">
                Find Your Photography Style
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
    @include('includes.transformation-section')

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
    @include('includes.faq-section')

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
