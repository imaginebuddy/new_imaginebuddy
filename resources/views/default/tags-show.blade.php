@extends('layouts.app')

@php
  $currentTier     = request()->get('tier');
  $currentSort     = request()->get('sort');
  $currentAiModel  = request()->get('ai_model');
  $currentCategory = request()->get('category');
  $tagUrlSlug      = $tagSlug ?? trim(str_replace(' ', '_', $tags));

  if (!function_exists('buildTagFilterUrl')) {
    function buildTagFilterUrl($slug, $overrides = []) {
      $params = array_merge(request()->only(['tier', 'sort', 'ai_model', 'category']), $overrides);
      $filtered = array_filter($params, function($val) {
        return $val !== null && $val !== '';
      });
      return url('tags', $slug) . (!empty($filtered) ? '?' . http_build_query($filtered) : '');
    }
  }

  $categoriesList = $categories ?? App\Models\Categories::where('mode', 'on')->orderBy('name')->get();
@endphp

@section('title'){{ $title.' - ' }}@endsection
@section('robots', 'noindex, follow')

@section('content')
<section class="section section-sm">

<div class="container">
<div class="row">
  <div class="col-lg-12 py-5">
    <h1 class="mb-0 text-break">
      {{ Str::title($tags) }} - <span style="color: #00d690;">AI Product Photography Prompts</span>
    </h1>

    <p class="lead text-muted mt-0">
      Explore {{$total}} curated, tested AI prompts designed for {{ Str::lower($tags) }} product. Generate high-end commercial visuals across Gemini, ChatGPT, and more.
      <!-- {{trans('misc.tagged_images' )}} ({{$total}}) -->
    </p>

    </div>
<!-- Col MD -->
<div class="col-md-12">

	<!-- Tag Filter Dropdowns (Mobile-Optimized matching Explore & Search Pages) -->
	<div class="explore-filters-wrap search-filters-wrap mb-4">
		<!-- Sort Order Filter -->
		<select class="form-select filter filter-sort" onchange="window.location.href=this.value;" aria-label="Sort Order">
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['sort' => 'latest']) }}" @if(empty($currentSort) || $currentSort == 'latest') selected @endif>{{ trans('misc.latest') }}</option>
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['sort' => 'oldest']) }}" @if($currentSort == 'oldest') selected @endif>{{ trans('misc.oldest') }}</option>
		</select>

		<!-- Category Filter -->
		<select class="form-select filter filter-category" onchange="window.location.href=this.value;" aria-label="Category">
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['category' => '']) }}" @if(empty($currentCategory)) selected @endif>{{ Lang::has('misc.all_categories') ? __('misc.all_categories') : 'All Categories' }}</option>
			@foreach ($categoriesList as $cat)
				<option value="{{ buildTagFilterUrl($tagUrlSlug, ['category' => $cat->slug]) }}" @if($currentCategory == $cat->slug || $currentCategory == (string)$cat->id) selected @endif>{{ Lang::has('categories.' . $cat->slug) ? __('categories.' . $cat->slug) : $cat->name }}</option>
			@endforeach
		</select>

		<!-- Free vs Premium Filter -->
		<select class="form-select filter filter-tier" onchange="window.location.href=this.value;" aria-label="Prompt Tier">
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['tier' => '']) }}" @if(empty($currentTier)) selected @endif>All Prompts</option>
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['tier' => 'free']) }}" @if($currentTier == 'free') selected @endif>Free Prompts</option>
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['tier' => 'premium']) }}" @if($currentTier == 'premium' || $currentTier == 'sale') selected @endif>Premium Prompts</option>
		</select>

		<!-- AI Model Filter -->
		<select class="form-select filter filter-ai-model" onchange="window.location.href=this.value;" aria-label="AI Model">
			<option value="{{ buildTagFilterUrl($tagUrlSlug, ['ai_model' => '']) }}" @if(empty($currentAiModel)) selected @endif>All AI Models</option>
			@foreach (App\Models\Images::getAiModels() as $model)
				<option value="{{ buildTagFilterUrl($tagUrlSlug, ['ai_model' => $model]) }}" @if($currentAiModel == $model) selected @endif>{{ $model }}</option>
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

 </div><!-- /COL MD -->
</div><!-- row -->
 </div><!-- container wrap-ui -->
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
