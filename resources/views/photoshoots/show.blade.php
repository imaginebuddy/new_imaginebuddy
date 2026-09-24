@extends('layouts.app')

@if (!empty($photoshoot->meta_title))
  @section('title'){{ $photoshoot->meta_title }}@endsection
@else
  @section('title'){{ $photoshoot->title }} - AI Photoshoot Set - @endsection
@endif

@if (!empty($photoshoot->meta_description))
  @section('description_override'){{ Helper::removeLineBreak(e($photoshoot->meta_description)) }}@endsection
@elseif ($photoshoot->description)
  @section('description_custom'){{ Helper::removeLineBreak($photoshoot->description) . ' - ' }}@endsection
@endif

@if (!empty($photoshoot->meta_keywords))
  @section('keywords_override'){{ $photoshoot->meta_keywords }}@endsection
@endif

@section('content')
@php
  $coverImage = $images->first();
  $coverUrl = $coverImage ? Storage::url(config('path.preview') . $coverImage->preview) : null;
@endphp

<section class="section section-sm py-4" style="padding-top: 95px !important;">
  <div class="container">
    
    <!-- Top Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <nav aria-label="breadcrumb" style="max-width: 100%;">
        <div class="breadcrumb-pill-box rounded-pill shadow-sm border border-custom px-3 px-md-4 py-2 d-inline-flex align-items-center bg-card-custom">
          <ol class="breadcrumb mb-0 align-items-center list-unstyled">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('photoshoots') }}" class="text-decoration-none text-muted">Photoshoots</a></li>
            @if ($photoshoot->category)
              <li class="breadcrumb-item"><a href="{{ url('photoshoots') }}?category={{ $photoshoot->category->slug }}" class="text-decoration-none text-muted">{{ $photoshoot->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
              <span class="badge bg-custom-mint text-white rounded-pill px-3 py-2 fw-bold" title="{{ $photoshoot->title }}" style="font-size: 0.85rem; letter-spacing: -0.2px;">{{ $photoshoot->title }}</span>
            </li>
          </ol>
        </div>
      </nav>
    </div>

    <!-- Photoshoot Showcase Header Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-card-custom border border-custom position-relative overflow-hidden">
      <div class="row align-items-center g-4">
        
        <!-- Details Column -->
        <div class="col-lg-12">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            @if ($photoshoot->category)
              <a href="{{ url('photoshoots') }}?category={{ $photoshoot->category->slug }}" class="badge bg-subtle-custom text-secondary border border-custom text-decoration-none rounded-pill px-3 py-2 fw-medium" style="font-size: 0.8rem;">
                {{ $photoshoot->category->name }}
              </a>
            @endif
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold" style="font-size: 0.8rem;">
              <i class="bi bi-images me-1 text-mint"></i> {{ $images->total() }} {{ str_plural('Prompt', $images->total()) }} Set
            </span>
          </div>

          <h1 class="fw-bold text-dark title-custom display-7 mb-3">{{ $photoshoot->title }}</h1>

          @if ($photoshoot->description)
            <p class="lead text-muted mb-4" style="font-size: 1.05rem; line-height: 1.6;">
              {{ $photoshoot->description }}
            </p>
          @endif

          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top border-custom mt-4">
            <div class="d-flex align-items-center gap-3 text-muted small">
              <span><i class="bi bi-clock me-1"></i> {{ Helper::formatDate($photoshoot->created_at) }}</span>
              @if ($photoshoot->user)
                <span><i class="bi bi-person me-1"></i> By {{ $photoshoot->user->username }}</span>
              @endif
            </div>

            <a href="{{ url('photoshoots') }}" class="btn btn-sm btn-outline-custom rounded-pill px-4 py-2 fw-semibold">
              <i class="bi bi-arrow-left me-2"></i> All Photoshoots
            </a>
          </div>
        </div>

      </div>
    </div>

    <!-- Prompts Grid Section -->
    <div class="mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-custom gap-2">
        <div>
          <h3 class="fw-bold text-dark title-custom m-0">Prompts in this Photoshoot</h3>
        </div>
        <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-3 py-2 fw-medium" style="font-size: 0.85rem;">
          <i class="bi bi-grid-fill text-mint me-1"></i> Showing <span id="showingCount">{{ $images->count() }}</span> of {{ $images->total() }} {{ str_plural('Prompt', $images->total()) }}
        </span>
      </div>

      @if ($images->total() != 0)
        <div class="dataResult">
          @include('includes.images', ['images' => $images])
          <div id="linkPagination" class="d-none">
            {{ $images->onEachSide(0)->links() }}
          </div>
        </div>

        <!-- Infinite Scroll Loader -->
        <div id="infiniteScrollLoader" class="text-center py-4 my-3 d-none">
          <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted small mt-2 mb-0">Loading more prompts...</p>
        </div>
      @else
        <div class="text-center py-5 my-4 bg-card-custom rounded-4 border border-custom p-5">
          <p class="text-muted m-0">No active prompts found in this photoshoot.</p>
        </div>
      @endif
    </div>

  </div>
</section>

<style>
.text-mint {
  color: #00d690 !important;
}
.backdrop-blur {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}
#linkPagination {
  display: none !important;
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  if ($('#imagesFlex').length && $.fn.flexImages) {
    $('#imagesFlex').flexImages({ rowHeight: 580 });
  }

  var state = {
    page: {{ $images->hasMorePages() ? 2 : 'null' }},
    hasMore: {{ $images->hasMorePages() ? 'true' : 'false' }},
    loading: false
  };

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
          var htmlContent = typeof response === 'object' && response.html ? response.html : response;
          var $wrapper = $('<div>').html(htmlContent);
          var $newItems = $wrapper.find('.item');

          if ($newItems.length > 0) {
            $('#imagesFlex').append($newItems);
            if ($('#imagesFlex').length && $.fn.flexImages) {
              $('#imagesFlex').flexImages({ rowHeight: 580 });
            }
            state.page++;

            if (typeof response === 'object' && typeof response.hasMore !== 'undefined') {
              state.hasMore = response.hasMore;
            } else {
              var hasNext = $wrapper.find('#linkPagination .pagination .next, #linkPagination .pagination [rel="next"]').length > 0;
              state.hasMore = hasNext;
            }

            var currentCount = $('#imagesFlex').find('.item').length;
            $('#showingCount').text(currentCount);
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
