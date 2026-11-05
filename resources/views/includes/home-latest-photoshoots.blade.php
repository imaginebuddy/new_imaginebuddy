@php
  $homeContent = $homeContent ?? \App\Models\HomepageSetting::getSettings();
@endphp
@if (isset($latestPhotoshoots) && $latestPhotoshoots->isNotEmpty())
<section class="section py-4 py-md-5 latest-photoshoots-section position-relative">
  <div class="container px-3 px-md-4">
    <div class="btn-block text-center mb-4 mb-md-5">
      <h2 class="m-0 fw-bold title-custom" style="font-size: clamp(1.45rem, 3.2vw, 1.85rem);">
        {{ $homeContent->getVal('photoshoots_heading', 'Latest Photoshoots') }}
      </h2>
      @if ($homeContent->getVal('photoshoots_subheading'))
      <p class="text-muted mt-2 mx-auto small-on-mobile" style="max-width: 680px; font-size: 0.98rem; line-height: 1.6;">
        {{ $homeContent->getVal('photoshoots_subheading') }}
      </p>
      @endif
    </div>

    <div class="row g-3 g-md-4">
      @foreach ($latestPhotoshoots->take(6) as $photoshoot)
        @include('includes.photoshoot-card', ['photoshoot' => $photoshoot])
      @endforeach
    </div>

    <div class="w-100 d-block text-center mt-4 mt-md-5">
      <a href="{{ url($homeContent->getVal('photoshoots_cta_url', 'photoshoots')) }}" class="btn btn-main rounded-pill btn-custom px-4 px-md-5 py-2.5 arrow shadow-sm fw-semibold">
        {{ $homeContent->getVal('photoshoots_cta_text', __('misc.view_all')) }}
      </a>
    </div>
  </div>
</section>

<style>
@media (max-width: 575.98px) {
  .small-on-mobile {
    font-size: 0.9rem !important;
  }
}
</style>
@endif
