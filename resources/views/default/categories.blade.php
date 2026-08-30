@extends('layouts.app')

@section('title'){{ trans('misc.categories').' - ' }}@endsection

@section('content')
<section class="section section-sm">
  <div class="container">
    <div class="row">

      {{-- Page Header & Tabs --}}
      <div class="col-lg-12 py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-2">
          <div>
            <h1 class="mb-0 fw-bold title-custom" id="tabHeading">
              {{ ($activeTab ?? 'categories') == 'tags' ? __('misc.tags') : trans('misc.categories') }}
            </h1>
            <p class="lead text-muted mt-0 mb-0" id="tabSubheading">
              {{ ($activeTab ?? 'categories') == 'tags' ? __('misc.tags_desc') : trans('misc.browse_by_category') }}
            </p>
          </div>

          {{-- Pill Tabs Container --}}
          <div class="nav nav-pills category-pills p-1 bg-subtle-custom rounded-pill border" id="pills-tab" role="tablist">
            <button class="nav-link rounded-pill px-4 py-2 fw-bold {{ ($activeTab ?? 'categories') == 'categories' ? 'active' : '' }}" 
                    id="pills-categories-tab" 
                    data-bs-toggle="pill" 
                    data-bs-target="#pills-categories" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-categories" 
                    aria-selected="{{ ($activeTab ?? 'categories') == 'categories' ? 'true' : 'false' }}"
                    data-title="{{ trans('misc.categories') }}"
                    data-subtitle="{{ trans('misc.browse_by_category') }}">
              <i class="bi bi-grid-fill me-1"></i> {{ trans('misc.categories') }}
            </button>
            <button class="nav-link rounded-pill px-4 py-2 fw-bold {{ ($activeTab ?? 'categories') == 'tags' ? 'active' : '' }}" 
                    id="pills-tags-tab" 
                    data-bs-toggle="pill" 
                    data-bs-target="#pills-tags" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-tags" 
                    aria-selected="{{ ($activeTab ?? 'categories') == 'tags' ? 'true' : 'false' }}"
                    data-title="{{ __('misc.tags') }}"
                    data-subtitle="{{ __('misc.tags_desc') }}">
              <i class="bi bi-tags-fill me-1"></i> {{ __('misc.tags') }}
            </button>
          </div>
        </div>
      </div>

      {{-- Tab Content Container --}}
      <div class="col-md-12">
        <div class="tab-content" id="pills-tabContent">
          
          {{-- Categories Tab Panel --}}
          <div class="tab-pane fade {{ ($activeTab ?? 'categories') == 'categories' ? 'show active' : '' }}" id="pills-categories" role="tabpanel" aria-labelledby="pills-categories-tab">
            
            {{-- Category Search Bar --}}
            <div class="row mb-3">
              <div class="col-12 col-sm-8 col-md-5 col-lg-3">
                <div class="position-relative">
                  <input type="text" id="categorySearchInput" class="form-control form-control-sm rounded-pill bg-card-custom border" style="font-size: 0.85rem; padding-left: 2.35rem !important; padding-right: 2.2rem !important;" placeholder="{{ trans('misc.search') }} {{ strtolower(trans('misc.categories')) }}..." autocomplete="off">
                  <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="font-size: 0.825rem; left: 14px; pointer-events: none; z-index: 5;"></i>
                  <button id="clearCategorySearch" class="btn btn-sm text-muted position-absolute top-50 translate-middle-y p-0 d-none" type="button" style="border:none; background:none; font-size: 0.85rem; right: 12px; z-index: 5;">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="row" id="categoriesContainer">
              @if (isset($categories) && count($categories) > 0)
                @include('includes.categories-listing')
              @else
                <div class="col-12 py-5 text-center">
                  <h3 class="margin-top-none text-center no-result text-muted fw-light">
                    {{ trans('misc.no_results_found') }}
                  </h3>
                </div>
              @endif
            </div><!-- row -->

            {{-- Categories Loader Spinner --}}
            <div id="categoriesLoader" class="col-12 text-center py-4 d-none">
              <div class="spinner-border" role="status" style="width: 2.2rem; height: 2.2rem; color: #00d690 !important;">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div><!-- /categories tab-pane -->

          {{-- Tags Tab Panel --}}
          <div class="tab-pane fade {{ ($activeTab ?? 'categories') == 'tags' ? 'show active' : '' }}" id="pills-tags" role="tabpanel" aria-labelledby="pills-tags-tab">
            
            {{-- Tag Search Bar --}}
            <div class="row mb-3">
              <div class="col-12 col-sm-8 col-md-5 col-lg-3">
                <div class="position-relative">
                  <input type="text" id="tagSearchInput" class="form-control form-control-sm rounded-pill bg-card-custom border" style="font-size: 0.85rem; padding-left: 2.35rem !important; padding-right: 2.2rem !important;" placeholder="{{ trans('misc.search') }} {{ strtolower(__('misc.tags')) }}..." autocomplete="off">
                  <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="font-size: 0.825rem; left: 14px; pointer-events: none; z-index: 5;"></i>
                  <button id="clearTagSearch" class="btn btn-sm text-muted position-absolute top-50 translate-middle-y p-0 d-none" type="button" style="border:none; background:none; font-size: 0.85rem; right: 12px; z-index: 5;">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="row" id="tagsContainer">
              @if (isset($tags) && count($tags) > 0)
                @include('includes.tags-listing')
              @else
                <div class="col-12 py-5 text-center">
                  <h3 class="margin-top-none text-center no-result text-muted fw-light">
                    {{ __('misc.no_results_found') }}
                  </h3>
                </div>
              @endif
            </div><!-- row -->

            {{-- Tags Loader Spinner --}}
            <div id="tagsLoader" class="col-12 text-center py-4 d-none">
              <div class="spinner-border" role="status" style="width: 2.2rem; height: 2.2rem; color: #00d690 !important;">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div><!-- /tags tab-pane -->

        </div><!-- tab-content -->
      </div><!-- /COL MD -->

    </div><!-- row -->
  </div><!-- container wrap-ui -->
</section>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  var currentTab = '{{ $activeTab ?? "categories" }}';

  var categoriesState = {
    page: {{ $categoriesNextPage ?? 2 }},
    loading: false,
    hasMore: {{ isset($categoriesHasMore) && $categoriesHasMore ? 'true' : 'false' }},
    query: ''
  };

  var tagsState = {
    page: {{ $tagsNextPage ?? 2 }},
    loading: false,
    hasMore: {{ isset($tagsHasMore) && $tagsHasMore ? 'true' : 'false' }},
    query: ''
  };

  var categorySearchTimer = null;
  var tagSearchTimer = null;

  // Search input event listeners
  $('#categorySearchInput').on('input', function() {
    var val = $(this).val().trim();
    if (val !== '') {
      $('#clearCategorySearch').removeClass('d-none');
    } else {
      $('#clearCategorySearch').addClass('d-none');
    }

    clearTimeout(categorySearchTimer);
    categorySearchTimer = setTimeout(function() {
      categoriesState.query = val;
      categoriesState.page = 1;
      categoriesState.hasMore = true;
      fetchCategories(true);
    }, 300);
  });

  $('#clearCategorySearch').on('click', function() {
    $('#categorySearchInput').val('');
    $(this).addClass('d-none');
    categoriesState.query = '';
    categoriesState.page = 1;
    categoriesState.hasMore = true;
    fetchCategories(true);
  });

  $('#tagSearchInput').on('input', function() {
    var val = $(this).val().trim();
    if (val !== '') {
      $('#clearTagSearch').removeClass('d-none');
    } else {
      $('#clearTagSearch').addClass('d-none');
    }

    clearTimeout(tagSearchTimer);
    tagSearchTimer = setTimeout(function() {
      tagsState.query = val;
      tagsState.page = 1;
      tagsState.hasMore = true;
      fetchTags(true);
    }, 300);
  });

  $('#clearTagSearch').on('click', function() {
    $('#tagSearchInput').val('');
    $(this).addClass('d-none');
    tagsState.query = '';
    tagsState.page = 1;
    tagsState.hasMore = true;
    fetchTags(true);
  });

  // Tab switch handler
  $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
    var target = $(e.target).attr('id');
    var title = $(e.target).data('title');
    var subtitle = $(e.target).data('subtitle');

    if (title) $('#tabHeading').text(title);
    if (subtitle) $('#tabSubheading').text(subtitle);

    if (target === 'pills-tags-tab') {
      currentTab = 'tags';
    } else {
      currentTab = 'categories';
    }

    checkScrollLoad();
  });

  function checkScrollLoad() {
    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    if (scrollTop + windowHeight >= docHeight - 350) {
      if (currentTab === 'categories') {
        fetchCategories(false);
      } else if (currentTab === 'tags') {
        fetchTags(false);
      }
    }
  }

  function fetchCategories(reset) {
    if (categoriesState.loading || (!reset && !categoriesState.hasMore)) return;

    categoriesState.loading = true;
    $('#categoriesLoader').removeClass('d-none');

    $.ajax({
      url: '{{ url("categories") }}',
      type: 'GET',
      data: { tab: 'categories', page: categoriesState.page, q: categoriesState.query },
      dataType: 'json',
      success: function(response) {
        if (reset) {
          $('#categoriesContainer').html(response.html && response.html.trim() !== '' ? response.html : '<div class="col-12 py-5 text-center"><h3 class="margin-top-none text-center no-result text-muted fw-light">{{ trans("misc.no_results_found") }}</h3></div>');
        } else if (response.html && response.html.trim() !== '') {
          $('#categoriesContainer').append(response.html);
        }

        categoriesState.page = response.nextPage;
        categoriesState.hasMore = response.hasMore;
        categoriesState.loading = false;
        $('#categoriesLoader').addClass('d-none');
      },
      error: function() {
        categoriesState.loading = false;
        $('#categoriesLoader').addClass('d-none');
      }
    });
  }

  function fetchTags(reset) {
    if (tagsState.loading || (!reset && !tagsState.hasMore)) return;

    tagsState.loading = true;
    $('#tagsLoader').removeClass('d-none');

    $.ajax({
      url: '{{ url("categories") }}',
      type: 'GET',
      data: { tab: 'tags', page: tagsState.page, q: tagsState.query },
      dataType: 'json',
      success: function(response) {
        if (reset) {
          $('#tagsContainer').html(response.html && response.html.trim() !== '' ? response.html : '<div class="col-12 py-5 text-center"><h3 class="margin-top-none text-center no-result text-muted fw-light">{{ __("misc.no_results_found") }}</h3></div>');
        } else if (response.html && response.html.trim() !== '') {
          $('#tagsContainer').append(response.html);
        }

        tagsState.page = response.nextPage;
        tagsState.hasMore = response.hasMore;
        tagsState.loading = false;
        $('#tagsLoader').addClass('d-none');
      },
      error: function() {
        tagsState.loading = false;
        $('#tagsLoader').addClass('d-none');
      }
    });
  }

  $(window).on('scroll resize', checkScrollLoad);
})(jQuery);
</script>
@endsection
