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
  $currentCategory  = request()->get('category');
  $currentSort      = request()->get('sort');

  if (!function_exists('buildExploreFilterUrl')) {
    function buildExploreFilterUrl($overrides = []) {
      $current = request()->only(['tier', 'ai_model', 'timeframe', 'category', 'sort']);
      $merged = array_merge($current, $overrides);
      $filtered = array_filter($merged, function($v) {
        return $v !== null && $v !== '';
      });
      return url()->current() . ($filtered ? '?' . http_build_query($filtered) : '');
    }
  }

  $categoriesList = $categories ?? App\Models\Categories::where('mode', 'on')->orderBy('name')->get();
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

	@if (request()->is(['prompts/free', 'prompts/premium']))
	<!-- 4 Dropdown Filters for Free/Premium Prompts (Mobile-Optimized matching Search & Tags Pages) -->
	<div class="explore-filters-wrap search-filters-wrap mb-4">
		<!-- 1. Sort Order Filter -->
		<select class="form-select filter filter-sort" onchange="window.location.href=this.value;" aria-label="Sort Order">
			<option value="{{ buildExploreFilterUrl(['sort' => 'latest']) }}" @if(empty($currentSort) || $currentSort == 'latest') selected @endif>{{ trans('misc.latest') }}</option>
			<option value="{{ buildExploreFilterUrl(['sort' => 'oldest']) }}" @if($currentSort == 'oldest') selected @endif>{{ trans('misc.oldest') }}</option>
		</select>

		<!-- 2. Category Filter -->
		<select class="form-select filter filter-category" onchange="window.location.href=this.value;" aria-label="Category">
			<option value="{{ buildExploreFilterUrl(['category' => '']) }}" @if(empty($currentCategory)) selected @endif>{{ Lang::has('misc.all_categories') ? __('misc.all_categories') : 'All Categories' }}</option>
			@foreach ($categoriesList as $cat)
				<option value="{{ buildExploreFilterUrl(['category' => $cat->slug]) }}" @if($currentCategory == $cat->slug || $currentCategory == (string)$cat->id) selected @endif>{{ Lang::has('categories.' . $cat->slug) ? __('categories.' . $cat->slug) : $cat->name }}</option>
			@endforeach
		</select>

		<!-- 3. Prompt Tier Filter -->
		<select class="form-select filter filter-tier" onchange="window.location.href=this.value;" aria-label="Prompt Tier">
			@php
				$sharedParams = array_filter(request()->only(['sort', 'category', 'ai_model']));
				$allPromptsUrl = url('latest') . (!empty($sharedParams) ? '?' . http_build_query($sharedParams) : '');
				$freePromptsUrl = url('prompts/free') . (!empty($sharedParams) ? '?' . http_build_query($sharedParams) : '');
				$premiumPromptsUrl = url('prompts/premium') . (!empty($sharedParams) ? '?' . http_build_query($sharedParams) : '');
			@endphp
			<option value="{{ $allPromptsUrl }}">All Prompts</option>
			<option value="{{ $freePromptsUrl }}" @if(request()->is('prompts/free')) selected @endif>Free Prompts</option>
			<option value="{{ $premiumPromptsUrl }}" @if(request()->is('prompts/premium')) selected @endif>Premium Prompts</option>
		</select>

		<!-- 4. AI Model Filter -->
		<select class="form-select filter filter-ai-model" onchange="window.location.href=this.value;" aria-label="AI Model">
			<option value="{{ buildExploreFilterUrl(['ai_model' => '']) }}" @if(empty($currentAiModel)) selected @endif>All AI Models</option>
			@foreach (App\Models\Images::getAiModels() as $model)
				<option value="{{ buildExploreFilterUrl(['ai_model' => $model]) }}" @if($currentAiModel == $model) selected @endif>{{ $model }}</option>
			@endforeach
		</select>
	</div>
	@elseif (request()->is(['latest', 'featured', 'popular', 'most/commented', 'most/viewed', 'most/downloads', 'most/copied']))
	<div class="explore-filters-wrap {{ request()->is(['latest', 'most/copied']) ? 'no-timeframe' : 'has-timeframe' }} mb-4">

		<!-- 1. Explore Page Navigation Dropdown -->
		@php
			$sharedExploreParams = array_filter(request()->only(['category', 'tier', 'ai_model']));
			$buildExploreNavUrl = function($path) use ($sharedExploreParams) {
				return url($path) . (!empty($sharedExploreParams) ? '?' . http_build_query($sharedExploreParams) : '');
			};
		@endphp
		<select class="form-select filter filter-explore filter-primary" onchange="window.location.href=this.value;" aria-label="Explore Navigation">
			<option @if (request()->is('latest')) selected @endif value="{{ $buildExploreNavUrl('latest') }}">{{__('misc.latest')}}</option>
			<option @if (request()->is('featured')) selected @endif value="{{ $buildExploreNavUrl('featured') }}">{{__('misc.featured')}}</option>
			<option @if (request()->is('popular')) selected @endif value="{{ $buildExploreNavUrl('popular') }}">{{__('misc.popular')}}</option>
			@if ($settings->comments)
			<option @if (request()->is('most/commented')) selected @endif value="{{ $buildExploreNavUrl('most/commented') }}">{{__('misc.most_commented')}}</option>
			@endif
			<option @if (request()->is('most/viewed')) selected @endif value="{{ $buildExploreNavUrl('most/viewed') }}">{{__('misc.most_viewed')}}</option>
			<option @if (request()->is('most/downloads')) selected @endif value="{{ $buildExploreNavUrl('most/downloads') }}">{{__('misc.most_downloads')}}</option>
			<option @if (request()->is('most/copied')) selected @endif value="{{ $buildExploreNavUrl('most/copied') }}">Most Copied Prompts</option>
		</select>

		<!-- 2. Category Filter Dropdown -->
		<select class="form-select filter filter-category" onchange="window.location.href=this.value;" aria-label="Category">
			<option value="{{ buildExploreFilterUrl(['category' => '']) }}" @if(empty($currentCategory)) selected @endif>{{ Lang::has('misc.all_categories') ? __('misc.all_categories') : 'All Categories' }}</option>
			@foreach ($categoriesList as $cat)
				<option value="{{ buildExploreFilterUrl(['category' => $cat->slug]) }}" @if($currentCategory == $cat->slug || $currentCategory == (string)$cat->id) selected @endif>{{ Lang::has('categories.' . $cat->slug) ? __('categories.' . $cat->slug) : $cat->name }}</option>
			@endforeach
		</select>

		<!-- 3. Free vs Premium Tier Filter Dropdown -->
		<select class="form-select filter filter-tier" onchange="window.location.href=this.value;" aria-label="Prompt Tier">
			<option value="{{ buildExploreFilterUrl(['tier' => '']) }}" @if(empty($currentTier)) selected @endif>All Prompts</option>
			<option value="{{ buildExploreFilterUrl(['tier' => 'free']) }}" @if($currentTier == 'free') selected @endif>Free Prompts</option>
			<option value="{{ buildExploreFilterUrl(['tier' => 'premium']) }}" @if($currentTier == 'premium' || $currentTier == 'sale') selected @endif>Premium Prompts</option>
		</select>

		<!-- 4. AI Model Filter Dropdown -->
		<select class="form-select filter filter-ai-model" onchange="window.location.href=this.value;" aria-label="AI Model">
			<option value="{{ buildExploreFilterUrl(['ai_model' => '']) }}" @if(empty($currentAiModel)) selected @endif>All AI Models</option>
			@foreach (App\Models\Images::getAiModels() as $model)
				<option value="{{ buildExploreFilterUrl(['ai_model' => $model]) }}" @if($currentAiModel == $model) selected @endif>{{ $model }}</option>
			@endforeach
		</select>

		<!-- 5. Timeframe Filter Dropdown -->
		@if (!request()->is(['latest', 'most/copied']))
		<select class="form-select filter filter-timeframe" onchange="window.location.href=this.value;" aria-label="Timeframe">
			<option value="{{ buildExploreFilterUrl(['timeframe' => '']) }}" @if(empty($currentTimeframe)) selected @endif>{{__('misc.all_time')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'today']) }}" @if($currentTimeframe == 'today') selected @endif>{{__('misc.today')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'week']) }}" @if($currentTimeframe == 'week') selected @endif>{{__('misc.this_week')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'month']) }}" @if($currentTimeframe == 'month') selected @endif>{{__('misc.this_month')}}</option>
			<option value="{{ buildExploreFilterUrl(['timeframe' => 'year']) }}" @if($currentTimeframe == 'year') selected @endif>{{__('misc.this_year')}}</option>
		</select>
		@endif
	</div>
	@endif

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
