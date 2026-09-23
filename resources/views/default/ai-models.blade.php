@extends('layouts.app')

@section('title'){{ __('misc.ai_models') ?? 'AI Models' }} - @endsection
@section('description_custom'){{ 'Browse curated, tested AI prompts across Gemini, ChatGPT, Midjourney, Flux and more. Generate commercial product visuals on ' . config('settings.title', 'Imagine Buddy') . '.' }}@endsection
@section('keywords_custom'){{ 'AI models, Gemini prompts, ChatGPT prompts, Midjourney prompts, Flux prompts, AI image prompts, product photography' }}@endsection

@section('content')
<section class="section section-sm">
  <div class="container">
    <div class="row">

      {{-- Page Header --}}
      <div class="col-lg-12 py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-2">
          <div>
            <h1 class="mb-0 fw-bold title-custom" id="modelsHeading">
              {{ __('misc.ai_models') ?? 'AI Models' }}
            </h1>
            <p class="lead text-muted mt-0 mb-0" id="modelsSubheading">
              {{ __('misc.browse_by_ai_model') ?? 'Browse tested prompts by AI generator model' }}
            </p>
          </div>
        </div>
      </div>

      {{-- Models Search Bar & Content --}}
      <div class="col-md-12">
        <div class="row mb-3">
          <div class="col-12 col-sm-8 col-md-5 col-lg-3">
            <div class="position-relative">
              <input type="text" id="modelSearchInput" class="form-control form-control-sm rounded-pill bg-card-custom border" style="font-size: 0.85rem; padding-left: 2.35rem !important; padding-right: 2.2rem !important;" placeholder="{{ trans('misc.search') }} {{ strtolower(__('misc.ai_models') ?? 'AI models') }}..." autocomplete="off">
              <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="font-size: 0.825rem; left: 14px; pointer-events: none; z-index: 5;"></i>
              <button id="clearModelSearch" class="btn btn-sm text-muted position-absolute top-50 translate-middle-y p-0 d-none" type="button" style="border:none; background:none; font-size: 0.85rem; right: 12px; z-index: 5;">
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="row" id="aiModelsContainer">
          @if (isset($models) && count($models) > 0)
            @include('includes.ai-models-listing')
          @else
            <div class="col-12 py-5 text-center">
              <h3 class="margin-top-none text-center no-result text-muted fw-light">
                {{ trans('misc.no_results_found') }}
              </h3>
            </div>
          @endif
        </div><!-- row -->

        {{-- Loader Spinner --}}
        <div id="modelsLoader" class="col-12 text-center py-4 d-none">
          <div class="spinner-border" role="status" style="width: 2.2rem; height: 2.2rem; color: #00d690 !important;">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div><!-- /col-md-12 -->

    </div><!-- row -->
  </div><!-- container -->
</section>
@endsection

@section('javascript')
<script type="text/javascript">
(function($) {
  "use strict";

  var modelsState = {
    page: {{ $modelsNextPage ?? 2 }},
    loading: false,
    hasMore: {{ isset($modelsHasMore) && $modelsHasMore ? 'true' : 'false' }},
    query: ''
  };

  var modelSearchTimer = null;

  // Search input listener
  $('#modelSearchInput').on('input', function() {
    var val = $(this).val().trim();
    if (val !== '') {
      $('#clearModelSearch').removeClass('d-none');
    } else {
      $('#clearModelSearch').addClass('d-none');
    }

    clearTimeout(modelSearchTimer);
    modelSearchTimer = setTimeout(function() {
      modelsState.query = val;
      modelsState.page = 1;
      modelsState.hasMore = true;
      fetchModels(true);
    }, 300);
  });

  $('#clearModelSearch').on('click', function() {
    $('#modelSearchInput').val('');
    $(this).addClass('d-none');
    modelsState.query = '';
    modelsState.page = 1;
    modelsState.hasMore = true;
    fetchModels(true);
  });

  function checkScrollLoad() {
    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var docHeight = $(document).height();

    if (scrollTop + windowHeight >= docHeight - 350) {
      fetchModels(false);
    }
  }

  function fetchModels(reset) {
    if (modelsState.loading || (!reset && !modelsState.hasMore)) return;

    modelsState.loading = true;
    $('#modelsLoader').removeClass('d-none');

    $.ajax({
      url: '{{ url("ai-models") }}',
      type: 'GET',
      data: { page: modelsState.page, q: modelsState.query },
      dataType: 'json',
      success: function(response) {
        if (reset) {
          $('#aiModelsContainer').html(response.html && response.html.trim() !== '' ? response.html : '<div class="col-12 py-5 text-center"><h3 class="margin-top-none text-center no-result text-muted fw-light">{{ trans("misc.no_results_found") }}</h3></div>');
        } else if (response.html && response.html.trim() !== '') {
          $('#aiModelsContainer').append(response.html);
        }

        modelsState.page = response.nextPage;
        modelsState.hasMore = response.hasMore;
        modelsState.loading = false;
        $('#modelsLoader').addClass('d-none');
      },
      error: function() {
        modelsState.loading = false;
        $('#modelsLoader').addClass('d-none');
      }
    });
  }

  $(window).on('scroll resize', checkScrollLoad);
})(jQuery);
</script>
@endsection
