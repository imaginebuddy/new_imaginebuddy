@extends('layouts.app')

@section('title'){{ $currentCategory ? $currentCategory->name . ' Photoshoots - ' : 'AI Product Photoshoots & Sets - ' }}@endsection

@section('content')
<section class="section section-sm py-4" style="padding-top: 95px !important;">
  <div class="container">
    
    <!-- Hero Header -->
    <div class="text-center pt-3 pt-md-4 pb-3 mb-4">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.75rem;">
        <i class="bi bi-collection-play me-1"></i> Photoshoot Sets
      </span>
      <h1 class="fw-bold text-dark title-custom display-5 mb-3">
        @if ($currentCategory)
          {{ $currentCategory->name }} Photoshoots
        @else
          AI Product Photography Sets
        @endif
      </h1>
      <p class="lead text-muted mx-auto" style="max-width: 680px;">
        Explore curated, multi-prompt photoshoot batches designed for high-impact commercial visual workflows.
      </p>
    </div>

    <!-- Category Filter Scrollable Bar -->
    <div class="photoshoot-filter-wrapper mb-5 position-relative">
      <div class="photoshoot-category-scroll d-flex align-items-center gap-2 overflow-x-auto py-2 px-1">
        <a href="{{ url('photoshoots') }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 {{ empty($categorySlug) ? 'btn-dark fw-bold' : 'btn-outline-custom' }}">
          All Categories
        </a>
        @foreach ($categories as $cat)
          <a href="{{ url('photoshoots') }}?category={{ $cat->slug }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 {{ $categorySlug == $cat->slug ? 'btn-dark fw-bold' : 'btn-outline-custom' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <!-- Photoshoots Grid Container -->
    @if ($photoshoots->total() != 0)
      <div class="row g-4" id="photoshootsContainer">
        @foreach ($photoshoots as $photoshoot)
          @include('includes.photoshoot-card', ['photoshoot' => $photoshoot])
        @endforeach
      </div>

      <!-- Infinite Scroll Loader -->
      <div id="photoshootsLoader" class="text-center py-5 d-none">
        <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
          <span class="visually-hidden">Loading more...</span>
        </div>
        <p class="text-muted small mt-2">Loading more photoshoots...</p>
      </div>

    @else
      <div class="text-center py-5 my-5 bg-card-custom rounded-4 border border-custom p-5">
        <div class="mb-3">
          <i class="bi bi-collection-play text-muted opacity-50" style="font-size: 4rem;"></i>
        </div>
        <h4 class="fw-bold text-dark title-custom mb-2">No photoshoots found</h4>
        <p class="text-muted mb-4">
          @if ($currentCategory)
            No photoshoot sets available in <strong>{{ $currentCategory->name }}</strong> yet.
          @else
            No photoshoot sets available at the moment.
          @endif
        </p>
        @if ($categorySlug)
          <a href="{{ url('photoshoots') }}" class="btn btn-dark rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> View All Photoshoots
          </a>
        @endif
      </div>
    @endif

  </div>
</section>

<style>
.photoshoot-category-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(150, 150, 150, 0.3) transparent;
  -webkit-overflow-scrolling: touch;
}
.photoshoot-category-scroll::-webkit-scrollbar {
  height: 4px;
}
.photoshoot-category-scroll::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.3);
  border-radius: 4px;
}
.photoshoot-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.1) !important;
}
.photoshoot-card:hover .photoshoot-cover-img {
  transform: scale(1.05);
}
.text-mint {
  color: #00d690 !important;
}
</style>

@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  var state = {
    page: {{ $photoshoots->hasMorePages() ? 2 : 'null' }},
    hasMore: {{ $photoshoots->hasMorePages() ? 'true' : 'false' }},
    loading: false,
    category: '{{ $categorySlug }}'
  };

  function checkScrollLoad() {
    if (state.loading || !state.hasMore || !state.page) return;

    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    if (scrollTop + windowHeight >= docHeight - 400) {
      fetchNextPage();
    }
  }

  function fetchNextPage() {
    state.loading = true;
    $('#photoshootsLoader').removeClass('d-none');

    $.ajax({
      url: '{{ url("photoshoots") }}',
      type: 'GET',
      data: {
        category: state.category,
        page: state.page
      },
      dataType: 'json',
      success: function(response) {
        if (response.html && response.html.trim() !== '') {
          $('#photoshootsContainer').append(response.html);
        }

        state.hasMore = response.hasMore;
        state.page = response.nextPage;
        state.loading = false;
        $('#photoshootsLoader').addClass('d-none');
      },
      error: function() {
        state.loading = false;
        $('#photoshootsLoader').addClass('d-none');
      }
    });
  }

  $(window).on('scroll resize', checkScrollLoad);
})(jQuery);
</script>
@endsection
