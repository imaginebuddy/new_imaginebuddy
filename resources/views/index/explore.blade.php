@extends('layouts.app')

@php
switch(request()->get('timeframe')) {
	case 'today':
		$timeframe_text = ' '.__('misc.today');
		break;
	case 'week':
			$timeframe_text = ' '.__('misc.this_week');
		break;
	case 'month':
			$timeframe_text = ' '.__('misc.this_month');
		break;
	case 'year':
			$timeframe_text = ' '.__('misc.this_year');
		break;
	default:
		$timeframe_text = null;
	}

  $currentTier      = request()->get('tier');
  $currentAiModel   = request()->get('ai_model');
  $currentTimeframe = request()->get('timeframe');

  function buildExploreFilterUrl($overrides = []) {
    $current = request()->only(['tier', 'ai_model', 'timeframe']);
    $merged = array_merge($current, $overrides);
    $filtered = array_filter($merged, function($v) {
      return $v !== null && $v !== '';
    });
    return url()->current() . ($filtered ? '?' . http_build_query($filtered) : '');
  }
@endphp

@section('title'){{ $title.$timeframe_text.' - ' }}@endsection

@section('content')

<section class="section section-sm">

<div class="container">
	<div class="row">

		<div class="col-lg-12 py-5">
			<h1 class="mb-0">
				{{ $title }}
			</h1>
			<p class="lead text-muted mt-0">{{ $description }}</p>
		  </div>

	<!-- col-md-12 -->
	<div class="col-md-12">

		@if ($images->total() != 0)

	@if (request()->is(['latest', 'featured', 'popular', 'most/commented', 'most/viewed', 'most/downloads', 'most/copied']))
	<div class="d-block w-100 mb-3 text-end">

		<!-- 1. Explore Page Navigation Dropdown -->
		<select class="ms-2 form-select d-inline-block w-auto me-2 filter filter-explore">
			<option @if (request()->is('latest')) selected @endif value="{{ url('latest') }}">{{__('misc.latest')}}</option>
			<option @if (request()->is('featured')) selected @endif value="{{ url('featured') }}">{{__('misc.featured')}}</option>
			<option @if (request()->is('popular')) selected @endif value="{{ url('popular') }}">{{__('misc.popular')}}</option>
			@if ($settings->comments)
			<option @if (request()->is('most/commented')) selected @endif value="{{ url('most/commented') }}">{{__('misc.most_commented')}}</option>
			@endif
			<option @if (request()->is('most/viewed')) selected @endif value="{{ url('most/viewed') }}">{{__('misc.most_viewed')}}</option>
			<option @if (request()->is('most/downloads')) selected @endif value="{{ url('most/downloads') }}">{{__('misc.most_downloads')}}</option>
			<option @if (request()->is('most/copied')) selected @endif value="{{ url('most/copied') }}">Most Copied Prompts</option>
		</select>

		<!-- 2. Free vs Premium Tier Filter Dropdown -->
		<select class="ms-2 form-select d-inline-block w-auto me-2" onchange="window.location.href=this.value;">
			<option value="{{ buildExploreFilterUrl(['tier' => '']) }}" @if(empty($currentTier)) selected @endif>All Prompts</option>
			<option value="{{ buildExploreFilterUrl(['tier' => 'free']) }}" @if($currentTier == 'free') selected @endif>Free Prompts</option>
			<option value="{{ buildExploreFilterUrl(['tier' => 'premium']) }}" @if($currentTier == 'premium' || $currentTier == 'sale') selected @endif>Premium Prompts</option>
		</select>

		<!-- 3. AI Model Filter Dropdown -->
		<select class="ms-2 form-select d-inline-block w-auto me-2" onchange="window.location.href=this.value;">
			<option value="{{ buildExploreFilterUrl(['ai_model' => '']) }}" @if(empty($currentAiModel)) selected @endif>All AI Models</option>
			@foreach (App\Models\Images::$aiModels as $model)
				<option value="{{ buildExploreFilterUrl(['ai_model' => $model]) }}" @if($currentAiModel == $model) selected @endif>{{ $model }}</option>
			@endforeach
		</select>

		<!-- 4. Timeframe Filter Dropdown -->
		@if (!request()->is(['latest', 'most/copied']))
		<select class="ms-2 form-select d-inline-block w-auto" onchange="window.location.href=this.value;">
			<option value="{{ buildExploreFilterUrl(['timeframe' => '']) }}" @if(empty($currentTimeframe)) selected @endif>{{__('misc.all_time')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'today']) }}" @if($currentTimeframe == 'today') selected @endif>{{__('misc.today')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'week']) }}" @if($currentTimeframe == 'week') selected @endif>{{__('misc.this_week')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'month']) }}" @if($currentTimeframe == 'month') selected @endif>{{__('misc.this_month')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'year']) }}" @if($currentTimeframe == 'year') selected @endif>{{__('misc.this_year')}}</option>
		</select>
		@endif
	</div>
	@endif

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
			{{ __('misc.no_results_found') }}
		</h3>
	  @endif

		</div><!-- col-md-12-->

	</div><!-- row -->
</div><!-- container -->
</section>
@endsection

@section('javascript')

<script type="text/javascript">
(function($) {
  "use strict";

  $('#imagesFlex').flexImages({ rowHeight: 680 });

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
