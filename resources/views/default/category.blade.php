@extends('layouts.app')

@if (!empty($category->seo_title))
@section('title'){{ $category->seo_title }}@endsection
@else
@section('title'){{ (Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name) . ' - ' }}@endsection
@endif

@if ($category->description != '')
@section('description_custom'){{ Helper::removeLineBreak($category->description) . ' - ' }}@endsection
@endif

@if ($category->keywords != '')
@section('keywords_custom'){{ $category->keywords . ',' }}@endsection
@endif

@section('content')
<section class="section section-sm">

  <div class="container">

    <div class="col-lg-12 py-5">
      <h1 class="mb-0">
        @if (!empty($category->page_title))
          {!! $category->page_title !!}
        @else
          {{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }} - <span style="color: #00d690;">AI Product Photography Prompts</span>
        @endif
      </h1>
      @if ($category->subcategories)
        <div class="my-3">
          @foreach ($category->subcategories as $subcategory)
          <a href="{{ url('category', [$category->slug, $subcategory->slug]) }}" class="mb-2 btn btn-sm rounded-pill btn-outline-custom btn-tags px-4 me-1">
            {{ Lang::has('subcategories.' . $subcategory->slug) ? __('subcategories.' . $subcategory->slug) : $subcategory->name }}
          </a>
          @endforeach
        </div>
        @endif
      <p class="lead text-muted mt-0">
        Explore <span style="color: #00d690;font-weight: bold;">{{ number_format($images->total()) }}</span> curated, tested AI prompts designed for {{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : str($category->name)->lower() }}. Generate high-end commercial product visuals across Gemini, ChatGPT, and more.
        <!-- {{ '('.number_format($images->total()).') '.trans_choice('misc.images_available_category',$images->total()) }} -->
      </p>
    </div>

    <!-- Col MD -->
    <div class="col-md-12">

      <div class="row">

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