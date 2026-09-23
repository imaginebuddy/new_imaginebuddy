@extends('layouts.app')

@php
  $currentQ       = request()->get('q');
  $currentTier    = request()->get('tier');
  $currentSort    = request()->get('sort');
  $currentAiModel = request()->get('ai_model');

  if (!function_exists('buildSearchFilterUrl')) {
    function buildSearchFilterUrl($overrides = []) {
      $params = array_merge(request()->only(['q', 'tier', 'sort', 'ai_model']), $overrides);
      $filtered = array_filter($params, function($val) {
        return $val !== null && $val !== '';
      });
      return url('search') . '?' . http_build_query($filtered);
    }
  }
@endphp

@section('title'){{ e($title) }}@endsection

@section('content')
<section class="section section-sm">

<div class="container">
	<div class="row">

    <div class="col-lg-12 py-5">
  		<h1 class="mb-0 text-break">
  			@if (!empty($q))
  				{{ trans('misc.result_of') }} "{{ $q }}"
  			@else
  				{{ trans('misc.result_of') }}
  			@endif
  		</h1>
  		<p class="lead text-muted mt-0">{{ $total }} {{ trans_choice('misc.images_plural',$total) }}</p>
  	  </div>

		<div class="col-md-12">
			<!-- Search Filter Dropdowns (Clean Design matching Explore Page) -->
			<div class="explore-filters-wrap mb-4">
				<!-- Sort Order Filter -->
				<select class="form-select filter filter-primary" onchange="window.location.href=this.value;">
					<option value="{{ buildSearchFilterUrl(['sort' => 'latest']) }}" @if(empty($currentSort) || $currentSort == 'latest') selected @endif>{{ trans('misc.latest') }}</option>
					<option value="{{ buildSearchFilterUrl(['sort' => 'oldest']) }}" @if($currentSort == 'oldest') selected @endif>{{ trans('misc.oldest') }}</option>
				</select>

				<!-- Free vs Premium Filter -->
				<select class="form-select filter" onchange="window.location.href=this.value;">
					<option value="{{ buildSearchFilterUrl(['tier' => '']) }}" @if(empty($currentTier)) selected @endif>All Prompts</option>
					<option value="{{ buildSearchFilterUrl(['tier' => 'free']) }}" @if($currentTier == 'free') selected @endif>Free Prompts</option>
					<option value="{{ buildSearchFilterUrl(['tier' => 'premium']) }}" @if($currentTier == 'premium' || $currentTier == 'sale') selected @endif>Premium Prompts</option>
				</select>

				<!-- AI Model Filter -->
				<select class="form-select filter" onchange="window.location.href=this.value;">
					<option value="{{ buildSearchFilterUrl(['ai_model' => '']) }}" @if(empty($currentAiModel)) selected @endif>All AI Models</option>
					@foreach (App\Models\Images::getAiModels() as $model)
						<option value="{{ buildSearchFilterUrl(['ai_model' => $model]) }}" @if($currentAiModel == $model) selected @endif>{{ $model }}</option>
					@endforeach
				</select>
			</div>

			@if ($images->total() != 0)

				<div class="dataResult">
			     @include('includes.images')
					 @include('includes.pagination-links')
				 </div>

				<!-- Infinite Scroll Loader -->
				<div id="infiniteScrollLoader" class="text-center py-4 my-3 d-none">
					<div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
						<span class="visually-hidden">Loading...</span>
					</div>
					<p class="text-muted small mt-2 mb-0">Loading more prompts...</p>
				</div>

	  @else
	    		<h3 class="mt-0 fw-light">
	    		{{ trans('misc.no_results_found') }}
	    	</h3>
	    	@endif

		</div><!-- col-md-12 -->
	</div><!-- row -->
</div><!-- container -->
</section>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  $('#imagesFlex').flexImages({ rowHeight: 580 });

  var state = {
    page: {{ $images->hasMorePages() ? 2 : 'null' }},
    hasMore: {{ $images->hasMorePages() ? 'true' : 'false' }},
    loading: false
  };

  // Hide numbered pagination container
  $('#linkPagination').hide();

  function checkScrollLoad() {
    if (state.loading || !state.hasMore || !state.page) return;

    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    if (scrollTop + windowHeight >= docHeight - 500) {
      loadNextPage();
    }
  }

  function loadNextPage() {
    state.loading = true;
    $('#infiniteScrollLoader').removeClass('d-none');

    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('page', state.page);

    $.ajax({
      url: currentUrl.toString(),
      type: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response) {
          var $wrapper = $('<div>').html(response);
          var $newItems = $wrapper.find('.item');

          if ($newItems.length > 0) {
            $('#imagesFlex').append($newItems);
            state.page++;

            var hasNext = $wrapper.find('#linkPagination .pagination .next, #linkPagination .pagination [rel="next"]').length > 0;
            if (!hasNext) {
              state.hasMore = false;
            }
          } else {
            state.hasMore = false;
          }
        } else {
          state.hasMore = false;
        }

        state.loading = false;
        $('#infiniteScrollLoader').addClass('d-none');
        $('#linkPagination').hide();
      },
      error: function() {
        state.loading = false;
        $('#infiniteScrollLoader').addClass('d-none');
      }
    });
  }

  $(window).on('scroll resize', checkScrollLoad);

  $(document).ready(function() {
    $('#linkPagination').hide();
    checkScrollLoad();
  });
})(jQuery);
</script>
@endsection
