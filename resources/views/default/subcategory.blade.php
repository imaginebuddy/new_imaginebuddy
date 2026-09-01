@extends('layouts.app')

@section('title'){{ Lang::has('subcategories.' . $subcategory->slug) ? __('subcategories.' . $subcategory->slug) : $subcategory->name.' - ' }}@endsection

@if ($subcategory->description != '')
@section('description_custom'){{ Helper::removeLineBreak($subcategory->description) . ' - ' }}@endsection
@endif

@if ($subcategory->keywords != '')
@section('keywords_custom'){{ $subcategory->keywords . ',' }}@endsection
@endif

@section('content')
<section class="section section-sm">

  <div class="container">

    <div class="col-lg-12 py-5">
      <a href="{{ url('category', [$subcategory->category->slug]) }}" class="mb-2 btn btn-sm rounded-pill btn-outline-custom btn-tags px-3 me-1">
        <i class="bi-arrow-left me-2"></i> {{ Lang::has('categories.' . $subcategory->category->slug) ? __('categories.' . $subcategory->category->slug) : $subcategory->category->name }}
      </a>
      <h1 class="mb-0">
        {{ Lang::has('subcategories.' . $subcategory->slug) ? __('subcategories.' . $subcategory->slug) : $subcategory->name }}
      </h1>
      <p class="lead text-muted mt-0">
        {{ '('.number_format($images->total()).') '.trans_choice('misc.images_available_category',$images->total()) }}
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