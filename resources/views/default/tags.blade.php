@extends('layouts.app')

@section('title'){{ __('misc.tags').' - ' }}@endsection

@section('content')
<section class="section section-sm">
  <div class="container">
    <div class="row">

      <div class="col-lg-12 py-5">
        <h1 class="mb-0 fw-bold title-custom">
          {{ __('misc.tags') }}
        </h1>
        <p class="lead text-muted mt-0">{{ __('misc.tags_desc') }}</p>
      </div>

      <!-- Col MD -->
      <div class="col-md-12">
        <div class="row" id="tagsContainer">
          @if (isset($tags) && count($tags) > 0)
            @include('includes.tags-listing')
          @else
            <div class="col-12 py-5 text-center">
              <div class="btn-block text-center mb-3">
                <i class="bi bi-tag ico-no-result text-muted" style="font-size: 3rem;"></i>
              </div>
              <h3 class="margin-top-none text-center no-result text-muted fw-light">
                {{ __('misc.no_results_found') }}
              </h3>
            </div>
          @endif
        </div><!-- row -->

        {{-- Scroll Loader Spinner --}}
        <div id="tagsLoader" class="col-12 text-center py-4 d-none">
          <div class="spinner-border" role="status" style="width: 2.2rem; height: 2.2rem; color: #00d690 !important;">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div><!-- /COL MD -->

    </div><!-- row -->
  </div><!-- container -->
</section>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  var page = {{ $nextPage ?? 2 }};
  var loading = false;
  var hasMore = {{ isset($hasMore) && $hasMore ? 'true' : 'false' }};

  function checkScrollLoad() {
    if (loading || !hasMore) return;

    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    // Trigger load when scrolled within 350px of document bottom
    if (scrollTop + windowHeight >= docHeight - 350) {
      loading = true;
      $('#tagsLoader').removeClass('d-none');

      $.ajax({
        url: '{{ url("tags") }}',
        type: 'GET',
        data: { page: page },
        dataType: 'json',
        success: function(response) {
          if (response.html && response.html.trim() !== '') {
            $('#tagsContainer').append(response.html);
            page = response.nextPage;
            hasMore = response.hasMore;
          } else {
            hasMore = false;
          }
          loading = false;
          $('#tagsLoader').addClass('d-none');
        },
        error: function() {
          loading = false;
          $('#tagsLoader').addClass('d-none');
        }
      });
    }
  }

  $(window).on('scroll resize', checkScrollLoad);
})(jQuery);
</script>
@endsection
