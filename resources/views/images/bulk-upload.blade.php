@extends('layouts.app')

@section('title'){{ __('Bulk Upload') . ' - ' }}@endsection

@section('css')
<style type="text/css">
  .bulk-card-dropzone {
    border: 2px dashed #d1d5db;
    background-color: #f8fafc;
    border-radius: 1rem;
    padding: 1.75rem 1.25rem;
    text-align: center;
    transition: all 0.2s ease-in-out;
    position: relative;
  }
  .bulk-card-dropzone:hover {
    border-color: #2563eb;
    background-color: #f0f6ff;
  }
  .bulk-file-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 10;
  }
  .file-status-badge {
    background-color: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 0.5rem;
  }
</style>
@endsection

@section('content')
<section class="section section-sm">
<div class="container pt-4 pb-5">
	<div class="row justify-content-center">

@if (auth()->user()->status == 'active')

@if ($settings->limit_upload_user == 0
    || auth()->user()->dailyUploads() < $settings->limit_upload_user
    || auth()->user()->isSuperAdmin()
    )

    <div class="col-lg-10">
      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>Bulk Prompt Upload
          </h2>
          <p class="text-muted mb-0">Import multiple prompts simultaneously using a CSV file and matching images.</p>
        </div>
        <a href="{{ url('bulk-upload/template') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
          <i class="bi bi-download me-1"></i> Download Sample CSV
        </a>
      </div>

      <form method="POST" action="{{ url('bulk-upload') }}" enctype="multipart/form-data" id="formBulkUpload">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <!-- Card 1: Global Prompt Settings -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-sliders me-2 text-primary"></i>1. Global Settings (Applied to All CSV Rows)
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">

              <!-- Photoshoot / Batch Selection -->
              <div class="col-md-12">
                <div class="card bg-white border shadow-sm p-3 rounded-3">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 text-dark">
                      <i class="bi bi-collection-play me-1 text-primary"></i> Photoshoot / Batch (Optional)
                    </label>
                    <div class="btn-group btn-group-sm" role="group" id="photoshootModeGroup">
                      <input type="radio" class="btn-check" name="photoshoot_mode" id="modeNone" value="none" checked>
                      <label class="btn btn-outline-secondary" for="modeNone">No Photoshoot</label>

                      <input type="radio" class="btn-check" name="photoshoot_mode" id="modeExisting" value="existing">
                      <label class="btn btn-outline-primary" for="modeExisting"><i class="bi bi-search me-1"></i>Search Existing</label>

                      <input type="radio" class="btn-check" name="photoshoot_mode" id="modeNew" value="new">
                      <label class="btn btn-outline-success" for="modeNew"><i class="bi bi-plus-lg me-1"></i>Create New</label>
                    </div>
                  </div>

                  <!-- Hidden photoshoot ID input -->
                  <input type="hidden" name="photoshoot_id" id="photoshoot_id_val" value="">

                  <!-- Mode: Existing Photoshoot Live Search -->
                  <div id="boxExistingPhotoshoot" class="display-none mt-2">
                    <div class="position-relative">
                      <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="photoshootSearchInput" class="form-control border-start-0 ps-0" placeholder="Type to search photoshoot by title (e.g. 'Skincare', 'Perfume')..." autocomplete="off">
                        <span class="input-group-text bg-white border-start-0 display-none" id="photoshootSearchSpinner">
                          <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </span>
                        <button class="btn btn-outline-secondary display-none" type="button" id="btnClearPhotoshootSearch" title="Clear search"><i class="bi bi-x-lg"></i></button>
                      </div>

                      <!-- Search Results Dropdown List -->
                      <div id="photoshootSearchResults" class="list-group position-absolute w-100 shadow-lg mt-1 display-none" style="z-index: 1050; max-height: 240px; overflow-y: auto;"></div>
                    </div>

                    <!-- Selected Photoshoot Confirmation Card -->
                    <div id="selectedPhotoshootBox" class="display-none mt-2">
                      <div class="p-2 px-3 rounded-2 border border-success bg-success-subtle d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                          <i class="bi bi-check-circle-fill text-success fs-5"></i>
                          <div>
                            <span class="fw-bold text-dark d-block" id="selectedPhotoshootTitle"></span>
                            <small class="text-muted" id="selectedPhotoshootMeta"></small>
                          </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeselectPhotoshoot">
                          <i class="bi bi-x-lg me-1"></i> Change
                        </button>
                      </div>
                    </div>
                    <small class="text-muted d-block mt-1">Search and select an existing photoshoot to append these prompts to.</small>
                  </div>

                  <!-- Mode: New Photoshoot -->
                  <div id="boxNewPhotoshoot" class="display-none mt-2">
                    <div class="form-floating">
                      <input type="text" class="form-control" name="photoshoot_title" id="photoshoot_title" placeholder="e.g. Luxury Skincare Product Photography Set">
                      <label for="photoshoot_title">New Photoshoot Name</label>
                    </div>
                    <small class="text-muted d-block mt-1">All prompts imported in this CSV batch will be linked together under this new photoshoot set.</small>
                  </div>

                </div>
              </div>

              <!-- AI Model -->
              <div class="col-md-6">
                <div class="form-floating">
                  <select name="ai_model" required class="form-select" id="ai_model">
                    <option value="">Select AI Model</option>
                    @foreach (App\Models\Images::getAiModels() as $model)
                      <option value="{{ $model }}">{{ $model }}</option>
                    @endforeach
                  </select>
                  <label for="ai_model">AI Model *</label>
                </div>
              </div>

              <!-- Category -->
              <div class="col-md-6">
                <div class="form-floating">
                  <select name="categories_id" required class="form-select" id="category">
                    <option value="">{{ __('misc.please_select_category') }}</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">
                        {{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
                      </option>
                    @endforeach
                  </select>
                  <label for="category">{{ __('misc.category') }} *</label>
                </div>
              </div>

              <!-- Subcategory -->
              <div class="col-md-6">
                <div class="form-floating">
                  <select name="subcategory" class="form-select" id="input-subcategory">
                    <option selected value="" id="subcategory">{{ __('misc.select') }}</option>
                  </select>
                  <label for="input-subcategory">{{ __('misc.subcategory') }}</label>
                </div>
              </div>

              <!-- Access Type / Tier -->
              @if ($settings->sell_option == 'on' && ($settings->who_can_sell == 'all' || ($settings->who_can_sell == 'admin' && auth()->user()->isSuperAdmin())))
              <div class="col-md-6">
                <div class="form-floating">
                  <select name="item_for_sale" class="form-select" id="itemForSale">
                    <option value="free">Free Prompt (Available to Free Users & Subscribers)</option>
                    <option value="sale">Premium Prompt (Requires Subscription Plan)</option>
                  </select>
                  <label for="itemForSale">Prompt Tier / Access Type</label>
                </div>
              </div>
              @endif

              <!-- License / How to use -->
              <div class="col-md-6 options_free @if ($settings->free_photo_upload == 'off') display-none @endif">
                <div class="form-floating">
                  <select name="how_use_image" class="form-select" id="how_use_image">
                    <option value="free">{{ __('misc.use_free') }}</option>
                    <option value="free_personal">{{ __('misc.use_free_personal') }}</option>
                    <option value="editorial_only">{{ __('misc.use_editorial_only') }}</option>
                    <option value="web_only">{{ __('misc.use_web_only') }}</option>
                  </select>
                  <label for="how_use_image">{{ __('misc.how_use_image') }}</label>
                </div>
              </div>

              <!-- Attribution switch -->
              <div class="col-md-6 d-flex align-items-center options_free @if ($settings->free_photo_upload == 'off') display-none @endif">
                <div class="form-check form-switch form-switch-md mb-0">
                  <input class="form-check-input" name="attribution_required" type="checkbox" checked value="yes" id="attributionRequired">
                  <label class="form-check-label text-dark fw-medium ms-2" for="attributionRequired">{{ __('misc.attribution_required') }}</label>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Card 2: Files Upload Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-cloud-arrow-up me-2 text-primary"></i>2. Upload CSV & Images
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">

              <!-- CSV File Dropzone Card -->
              <div class="col-md-6">
                <div class="bulk-card-dropzone h-100">
                  <input type="file" required accept=".csv,text/csv" class="custom-file bulk-file-overlay" name="csv_file" id="csv_file">
                  
                  <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 mb-2" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                      <i class="bi bi-filetype-csv fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">CSV File *</h6>
                    <p class="text-muted small mb-2">Click or drag & drop your <code>.csv</code> file here</p>
                    <span class="btn btn-outline-primary btn-sm rounded-pill px-4">Browse CSV</span>
                    
                    <div id="csvFileInfo" class="file-status-badge display-none"></div>
                    <small class="text-muted d-block mt-3" style="font-size: 0.75rem;">
                      Required headers: <code>prompt</code>, <code>file_name</code>, <code>title</code>, <code>keywords</code>
                    </small>
                  </div>
                </div>
              </div>

              <!-- Image Files / ZIP Archive Dropzone Card -->
              <div class="col-md-6">
                <div class="card border rounded-4 p-3 bg-light h-100">
                  <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-images me-1 text-primary"></i>Image Source *
                  </h6>

                  <!-- Option A: Multiple Images -->
                  <div class="bulk-card-dropzone mb-3 bg-white">
                    <input type="file" accept="image/*" multiple class="custom-file bulk-file-overlay" name="images[]" id="imageFiles">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                      <i class="bi bi-images text-primary fs-4"></i>
                      <div class="text-start">
                        <strong class="d-block text-dark small">Option A: Select Image Files</strong>
                        <span class="text-muted" style="font-size: 0.75rem;">Click to choose multiple image files (JPG, PNG, GIF)</span>
                      </div>
                    </div>
                    <div id="imageFilesInfo" class="file-status-badge display-none mt-2"></div>
                  </div>

                  <div class="text-center text-muted small fw-bold mb-2">OR</div>

                  <!-- Option B: Single ZIP File -->
                  <div class="bulk-card-dropzone bg-white">
                    <input type="file" accept=".zip,application/zip" class="custom-file bulk-file-overlay" name="zip_file" id="zipFile">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                      <i class="bi bi-file-earmark-zip text-primary fs-4"></i>
                      <div class="text-start">
                        <strong class="d-block text-dark small">Option B: Upload ZIP Archive</strong>
                        <span class="text-muted" style="font-size: 0.75rem;">Click to select a single <code>.zip</code> file</span>
                      </div>
                    </div>
                    <div id="zipFileInfo" class="file-status-badge display-none mt-2"></div>
                  </div>

                </div>
              </div>

            </div>

            <!-- Global Error Alert -->
            <div class="alert alert-danger mt-4 display-none" id="dangerAlert">
              <ul class="list-unstyled mb-0" id="showErrors"></ul>
            </div>

            <div class="mt-4 text-end">
              <button type="submit" id="btnSubmitBulk" class="btn btn-primary btn-lg rounded-pill px-5">
                <i class="bi bi-rocket-takeoff-fill me-2"></i> Start Bulk Import
              </button>
            </div>
          </div>
        </div>
      </form>

      <!-- Card 3: Live Progress & Results Report -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 display-none" id="resultsCard">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-check me-2 text-primary"></i>Import Results Summary
          </h5>
          <form method="POST" action="{{ url('bulk-upload/failed-rows') }}" id="formDownloadFailed" class="display-none">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="failed_rows_json" id="failedRowsJson" value="[]">
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
              <i class="bi bi-download me-1"></i> Download Failed Rows CSV
            </button>
          </form>
        </div>
        <div class="card-body p-4">

          <!-- Summary Badges -->
          <div class="row text-center mb-4 g-3">
            <div class="col-md-4">
              <div class="bg-light p-3 rounded-4 border">
                <span class="text-muted d-block mb-1 small fw-medium">Total Rows</span>
                <h3 class="fw-bold mb-0 text-dark" id="statTotal">0</h3>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-success-subtle p-3 rounded-4 border border-success-subtle">
                <span class="text-success d-block mb-1 small fw-medium">Successfully Imported</span>
                <h3 class="fw-bold mb-0 text-success" id="statSuccess">0</h3>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-danger-subtle p-3 rounded-4 border border-danger-subtle">
                <span class="text-danger d-block mb-1 small fw-medium">Failed Rows</span>
                <h3 class="fw-bold mb-0 text-danger" id="statFailed">0</h3>
              </div>
            </div>
          </div>

          <!-- Logs Table -->
          <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0" id="tableLogs">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 70px;">Row</th>
                  <th scope="col">File Name</th>
                  <th scope="col">Title</th>
                  <th scope="col" style="width: 110px;">Status</th>
                  <th scope="col">Details / Action</th>
                </tr>
              </thead>
              <tbody id="logsTableBody">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>

@else
    <div class="col-lg-8 text-center py-5">
      <span class="w-100 d-block mb-4 display-1 text-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </span>
      <h3 class="mt-0 text-center fw-light">{{ __('misc.limit_uploads_user') }}</h3>
    </div>
@endif

@else
    <div class="col-lg-8 text-center py-5">
      <span class="w-100 d-block mb-4 display-1 text-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
      </span>
      <h3 class="mt-0 text-center fw-light">{{ __('misc.confirm_email') }} <span class="fw-bold">{{ auth()->user()->email }}</span></h3>
    </div>
@endif

	</div>
</div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

  // File selection indicators
  $('#csv_file').on('change', function() {
    if (this.files && this.files[0]) {
      $('#csvFileInfo').html('<i class="bi bi-check-circle-fill me-1"></i> Selected: <strong>' + this.files[0].name + '</strong>').show();
    } else {
      $('#csvFileInfo').hide().empty();
    }
  });

  $('#imageFiles').on('change', function() {
    if (this.files && this.files.length > 0) {
      $('#imageFilesInfo').html('<i class="bi bi-check-circle-fill me-1"></i> Selected: <strong>' + this.files.length + ' image file(s)</strong>').show();
      // Clear ZIP selection if images picked
      $('#zipFile').val('');
      $('#zipFileInfo').hide().empty();
    } else {
      $('#imageFilesInfo').hide().empty();
    }
  });

  $('#zipFile').on('change', function() {
    if (this.files && this.files[0]) {
      $('#zipFileInfo').html('<i class="bi bi-check-circle-fill me-1"></i> Selected ZIP: <strong>' + this.files[0].name + '</strong>').show();
      // Clear image files selection if ZIP picked
      $('#imageFiles').val('');
      $('#imageFilesInfo').hide().empty();
    } else {
      $('#zipFileInfo').hide().empty();
    }
  });

  // Dynamic Subcategory Loader
  $('#category').on('change', function() {
    var categoryId = $(this).val();
    $('#input-subcategory').html('<option value="">{{ __("misc.select") }}</option>');

    if (categoryId) {
      $.ajax({
        url: "{{ url('get/subcategories') }}",
        type: 'POST',
        data: {
          id: categoryId,
          _token: "{{ csrf_token() }}"
        },
        dataType: 'json',
        success: function(response) {
          if (response && response.length > 0) {
            $.each(response, function(index, subcat) {
              $('#input-subcategory').append('<option value="' + subcat.id + '">' + subcat.name + '</option>');
            });
          }
        }
      });
    }
  });

  // Photoshoot Mode switcher
  $('input[name="photoshoot_mode"]').on('change', function() {
    var mode = $(this).val();
    if (mode === 'existing') {
      $('#boxNewPhotoshoot').slideUp();
      $('#photoshoot_title').val('');
      $('#boxExistingPhotoshoot').slideDown();
      if (!$('#photoshoot_id_val').val()) {
        $('#photoshootSearchInput').show().focus();
        searchPhotoshoots('');
      }
    } else if (mode === 'new') {
      $('#boxExistingPhotoshoot').slideUp();
      $('#photoshoot_id_val').val('new');
      $('#selectedPhotoshootBox').hide();
      $('#photoshootSearchInput').val('');
      $('#boxNewPhotoshoot').slideDown();
      $('#photoshoot_title').focus();
    } else {
      // none
      $('#boxExistingPhotoshoot').slideUp();
      $('#boxNewPhotoshoot').slideUp();
      $('#photoshoot_id_val').val('');
      $('#photoshoot_title').val('');
      $('#selectedPhotoshootBox').hide();
      $('#photoshootSearchInput').val('');
    }
  });

  // Photoshoot Search Debounce & AJAX
  var photoshootSearchTimer = null;
  function searchPhotoshoots(query) {
    $('#photoshootSearchSpinner').show();
    $.ajax({
      url: "{{ url('ajax/photoshoots/search') }}",
      type: 'GET',
      data: { q: query },
      dataType: 'json',
      success: function(res) {
        $('#photoshootSearchSpinner').hide();
        var $results = $('#photoshootSearchResults');
        $results.empty();
        if (res.results && res.results.length > 0) {
          $.each(res.results, function(i, item) {
            var btn = $('<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-bottom">' +
              '<div><strong class="d-block text-dark small">' + item.title + '</strong>' +
              '<small class="text-muted" style="font-size: 0.75rem;">ID: #' + item.id + '</small></div>' +
              '<span class="badge bg-primary-subtle text-primary rounded-pill">' + item.prompts_count + ' prompts</span>' +
              '</button>');
            btn.data('photoshoot', item);
            $results.append(btn);
          });
          $results.slideDown(150);
        } else {
          $results.html('<div class="p-3 text-center text-muted small">No matching photoshoots found.</div>').slideDown(150);
        }
      },
      error: function() {
        $('#photoshootSearchSpinner').hide();
      }
    });
  }

  $('#photoshootSearchInput').on('keyup', function() {
    var q = $(this).val().trim();
    if (q.length > 0) {
      $('#btnClearPhotoshootSearch').show();
    } else {
      $('#btnClearPhotoshootSearch').hide();
    }
    clearTimeout(photoshootSearchTimer);
    photoshootSearchTimer = setTimeout(function() {
      searchPhotoshoots(q);
    }, 250);
  });

  $('#photoshootSearchInput').on('focus', function() {
    searchPhotoshoots($(this).val().trim());
  });

  $('#btnClearPhotoshootSearch').on('click', function() {
    $('#photoshootSearchInput').val('').focus();
    $(this).hide();
    searchPhotoshoots('');
  });

  // Select Photoshoot from Search Results
  $(document).on('click', '#photoshootSearchResults button', function() {
    var item = $(this).data('photoshoot');
    if (!item) return;
    $('#photoshoot_id_val').val(item.id);
    $('#selectedPhotoshootTitle').text(item.title);
    $('#selectedPhotoshootMeta').text('ID: #' + item.id + ' • ' + item.prompts_count + ' prompts');
    $('#selectedPhotoshootBox').slideDown();
    $('#photoshootSearchResults').slideUp();
    $('#photoshootSearchInput').hide();
    $('#btnClearPhotoshootSearch').hide();

    // Auto-select category if photoshoot has one and user hasn't selected yet
    if (item.categories_id && !$('#category').val()) {
      $('#category').val(item.categories_id).trigger('change');
    }
  });

  // Change selected photoshoot
  $('#btnDeselectPhotoshoot').on('click', function() {
    $('#photoshoot_id_val').val('');
    $('#selectedPhotoshootBox').hide();
    $('#photoshootSearchInput').val('').show().focus();
    searchPhotoshoots('');
  });

  // Close search results dropdown when clicking outside
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#boxExistingPhotoshoot').length) {
      $('#photoshootSearchResults').hide();
    }
  });

  // Toggle Pricing options
  $('#itemForSale').on('change', function() {
    if ($(this).val() == 'sale') {
      $('.options_free').slideUp();
    } else {
      $('.options_free').slideDown();
    }
  });

  // Handle Form Submission via AJAX
  $('#formBulkUpload').on('submit', function(e) {
    e.preventDefault();

    var $btn = $('#btnSubmitBulk');
    var $alert = $('#dangerAlert');
    var $errorsUl = $('#showErrors');
    var $resultsCard = $('#resultsCard');

    $alert.hide();
    $errorsUl.empty();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing Bulk Upload...');

    var formData = new FormData(this);

    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(response) {
        $btn.prop('disabled', false).html('<i class="bi bi-rocket-takeoff-fill me-2"></i> Start Bulk Import');

        if (response.success) {
          $resultsCard.slideDown();
          $('#statTotal').text(response.total_rows);
          $('#statSuccess').text(response.success_count);
          $('#statFailed').text(response.failed_count);

          var $tbody = $('#logsTableBody');
          $tbody.empty();

          if (response.logs && response.logs.length > 0) {
            $.each(response.logs, function(i, log) {
              var badge = (log.status === 'success') 
                ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Success</span>'
                : '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Failed</span>';

              var actionHtml = (log.status === 'success' && log.url)
                ? '<a href="' + log.url + '" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2">View Prompt <i class="bi bi-box-arrow-up-right ms-1"></i></a>'
                : '<span class="text-danger small">' + (log.error || 'Unknown error') + '</span>';

              var rowHtml = '<tr>' +
                '<td class="fw-bold text-muted">' + log.row + '</td>' +
                '<td><code>' + log.file_name + '</code></td>' +
                '<td class="fw-medium text-dark">' + log.title + '</td>' +
                '<td>' + badge + '</td>' +
                '<td>' + actionHtml + '</td>' +
              '</tr>';

              $tbody.append(rowHtml);
            });
          }

          // Show failed rows download button if failed rows exist
          if (response.failed_rows && response.failed_rows.length > 0) {
            $('#failedRowsJson').val(JSON.stringify(response.failed_rows));
            $('#formDownloadFailed').show();
          } else {
            $('#formDownloadFailed').hide();
          }

          // Scroll to results
          $('html, body').animate({
            scrollTop: $resultsCard.offset().top - 80
          }, 500);
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html('<i class="bi bi-rocket-takeoff-fill me-2"></i> Start Bulk Import');

        var errText = 'An error occurred during bulk upload processing.';
        if (xhr.responseJSON) {
          if (xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, function(key, val) {
              $errorsUl.append('<li><i class="bi bi-exclamation-triangle me-2"></i>' + (Array.isArray(val) ? val[0] : val) + '</li>');
            });
          } else if (xhr.responseJSON.message) {
            $errorsUl.append('<li><i class="bi bi-exclamation-triangle me-2"></i>' + xhr.responseJSON.message + '</li>');
          }
        } else {
          $errorsUl.append('<li><i class="bi bi-exclamation-triangle me-2"></i>' + errText + '</li>');
        }
        $alert.slideDown();
      }
    });
  });

});
</script>
@endsection
