@extends('admin.layout')

@section('css')
<style>
/* Custom Actions Styling matching design mockup */
.action-capsule-btn {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 48px;
  border: 1.5px solid #0d6efd;
  border-radius: 20px;
  background-color: transparent;
  padding: 3px 2px;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  text-decoration: none;
  gap: 2px;
}
.action-capsule-btn:hover {
  background-color: rgba(13, 110, 253, 0.08);
  border-color: #0a58ca;
  transform: translateY(-1px);
}
.action-capsule-btn:active {
  transform: translateY(0);
}
.action-capsule-icon {
  font-size: 14px;
  line-height: 1;
  color: #0d6efd;
}
.action-capsule-badge {
  background-color: #0d6efd;
  color: #ffffff;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
  box-shadow: 0 1px 2px rgba(13, 110, 253, 0.25);
}
.action-icon-btn {
  font-size: 1.35rem;
  line-height: 1;
  transition: transform 0.15s ease, opacity 0.15s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  border: none;
  background: transparent;
  padding: 4px;
}
.action-icon-btn:hover {
  transform: scale(1.15);
}
.action-icon-edit {
  color: #1a1a1a;
}
.action-icon-edit:hover {
  color: #000000;
}
.action-icon-delete {
  color: #e63946;
}
.action-icon-delete:hover {
  color: #c9182b;
}

/* Upload Dropzone Styling */
.upload-dropzone {
  transition: all 0.2s ease-in-out;
  background-color: #f8faff;
  border: 2px dashed #0d6efd !important;
  cursor: pointer;
}
.upload-dropzone:hover, .upload-dropzone.dragover {
  background-color: #edf3ff !important;
  border-color: #0b5ed7 !important;
}
.upload-dropzone input[type="file"].custom-file {
  position: absolute !important;
  inset: 0 !important;
  width: 100% !important;
  height: 100% !important;
  opacity: 0 !important;
  cursor: pointer !important;
  z-index: 10 !important;
  visibility: visible !important;
  display: block !important;
}
.preview-thumb-item {
  position: relative;
  width: 75px;
  height: 75px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #dee2e6;
  background: #ffffff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.preview-thumb-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.preview-thumb-item .btn-remove-preview {
  position: absolute;
  top: 2px;
  right: 2px;
  width: 18px;
  height: 18px;
  background: rgba(0,0,0,0.65);
  color: #fff;
  border-radius: 50%;
  border: none;
  font-size: 11px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 0;
}
.preview-thumb-item .btn-remove-preview:hover {
  background: #dc3545;
}
</style>
@endsection

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.images') }} ({{$data->total()}})</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @if (session('info_message'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('info_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="d-lg-flex justify-content-lg-between align-items-center mb-3 w-100">

          @if ($data->count() != 0)
						@if (! request()->get('q'))
							<select class="form-select d-inline-block w-auto filter">
	              <option @if ($sort == '') selected="selected" @endif value="{{ url()->current() }}">{{ trans('admin.sort_id') }}</option>
	              <option @if ($sort == 'pending') selected="selected" @endif value="{{ url()->current() }}?sort=pending">{{ trans('admin.pending') }}</option>
	              <option @if ($sort == 'featured') selected="selected" @endif value="{{ url()->current() }}?sort=featured">Featured Prompts</option>
	              <option @if ($sort == 'title') selected="selected" @endif value="{{ url()->current() }}?sort=title">{{ trans('admin.sort_title') }}</option>
	              <option @if ($sort == 'likes') selected="selected" @endif value="{{ url()->current() }}?sort=likes">{{ trans('admin.sort_likes') }}</option>
	              <option @if ($sort == 'downloads') selected="selected" @endif value="{{ url()->current() }}?sort=downloads">{{ trans('admin.sort_downloads') }}</option>
	        			</select>
						@endif

						<!-- form -->
            <form class="mt-lg-0 mt-2 position-relative" role="search" autocomplete="off" action="{{ url('panel/admin/images') }}" method="get">
							<i class="bi bi-search btn-search bar-search"></i>
              <input type="text" name="q" class="form-control ps-5 w-auto" value="{{ request()->get('q') }}" placeholder="{{ __('misc.search') }}">
            </form><!-- form -->
					@endif
          </div>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 50px;">ID</th>
                  <th scope="col" style="width: 60px;">{{ trans('misc.thumbnail') }}</th>
                  <th scope="col">{{ trans('admin.title') }}</th>
                  <th scope="col" style="width: 130px;">{{ trans('misc.category') }}</th>
                  <th scope="col" style="width: 140px;">Type (Free/Sale)</th>
                  <th scope="col" style="width: 110px;">Featured</th>
                  <th scope="col" style="width: 80px;">{{ trans('misc.likes') }}</th>
                  <th scope="col" style="width: 90px;">{{ trans('misc.downloads') }}</th>
                  <th scope="col" style="width: 110px;">{{ trans('admin.date') }}</th>
                  <th scope="col" style="width: 95px;">{{ trans('admin.status') }}</th>
                  <th scope="col" style="width: 150px;">{{ trans('admin.actions') }}</th>
                </tr>
              </thead>
              <tbody>

                @if ($data->total() != 0 && $data->count() != 0)
                  @foreach ($data as $image)
                    <tr id="prompt-row-{{ $image->id }}">
                      <td class="fw-bold text-muted">{{ $image->id }}</td>
                      <td><img src="{{ Storage::url(config('path.thumbnail') . $image->thumbnail) }}" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" /></td>
                      <td>
                        <a href="{{ url('prompt', $image->slug) }}" title="{{ $image->title }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                          {{ str_limit($image->title, 25, '...') }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                        </a>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">By: {{ $image->user->username ?? 'N/A' }}</small>
                      </td>
                      <td>
                        @if ($image->category)
                          <span class="badge bg-light text-dark border">{{ str_limit($image->category->name, 18) }}</span>
                        @else
                          <span class="text-muted small">N/A</span>
                        @endif
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="form-check form-switch form-switch-md m-0">
                            <input class="form-check-input toggleSaleSwitch" 
                                   type="checkbox" 
                                   role="switch" 
                                   data-id="{{ $image->id }}" 
                                   @if ($image->item_for_sale == 'sale') checked @endif 
                                   title="Toggle Free / For Sale">
                          </div>
                          <span class="badge saleBadge-{{ $image->id }} bg-{{ $image->item_for_sale == 'sale' ? 'warning' : 'secondary' }}">
                            {{ $image->item_for_sale == 'sale' ? trans('misc.sale') : trans('misc.free') }}
                          </span>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="form-check form-switch form-switch-md m-0">
                            <input class="form-check-input toggleFeaturedSwitch" 
                                   type="checkbox" 
                                   role="switch" 
                                   data-id="{{ $image->id }}" 
                                   @if ($image->featured == 'yes') checked @endif 
                                   title="Toggle Featured">
                          </div>
                          <span class="featuredIcon-{{ $image->id }} {{ $image->featured == 'yes' ? 'text-warning' : 'text-muted opacity-25' }}">
                            <i class="bi bi-star-fill"></i>
                          </span>
                        </div>
                      </td>
                      <td>{{ $image->likes()->count() }}</td>
                      <td>{{ $image->downloads()->count() }}</td>
                      <td><small class="text-muted">{{ Helper::formatDate($image->date) }}</small></td>
                      <td>
                        <span class="badge rounded-pill bg-{{ $image->status == 'active' ? 'success' : 'warning' }}">
                          {{ $image->status == 'active' ? trans('admin.active') : trans('admin.pending') }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-3">
                          <button type="button" 
                                  class="action-capsule-btn btnOpenExamplesModal" 
                                  data-id="{{ $image->id }}" 
                                  data-title="{{ $image->title }}" 
                                  title="Manage Example Images">
                            <i class="bi bi-images action-capsule-icon"></i>
                            <span class="action-capsule-badge ex-count-badge-{{ $image->id }}">{{ $image->examples_count }}</span>
                          </button>

                          <a href="{{ url('panel/admin/images', $image->id) }}" class="action-icon-btn action-icon-edit" title="{{ __('admin.edit') }}">
                            <i class="bi bi-pencil-square"></i>
                          </a>

                          {!! Form::open(['method' => 'POST', 'url' => 'panel/admin/images/delete', 'class' => 'd-inline-block m-0']) !!}
                            {!! Form::hidden('id', $image->id); !!}
                            {!! Form::button('<i class="bi bi-trash3"></i>', ['data-url' => $image->id, 'class' => 'btn btn-link action-icon-btn action-icon-delete e-none p-0 actionDelete', 'title' => __('misc.delete')]) !!}
                          {!! Form::close() !!}
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="11" class="text-center p-5 text-muted fw-light">
                      {{ trans('misc.no_results_found') }}
                      @if (isset($query) || isset($sort))
                        <div class="d-block w-100 mt-2">
                          <a href="{{ url('panel/admin/images') }}"><i class="bi-arrow-left me-1"></i> {{ trans('auth.back') }}</a>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endif

              </tbody>
            </table>
          </div><!-- /.table responsive -->
        </div><!-- card-body -->
      </div><!-- card  -->

      <div class="mt-3">
        {{ $data->appends(['q' => $query, 'sort' => $sort])->onEachSide(0)->links() }}
      </div>
    </div><!-- col-lg-12 -->

  </div><!-- end row -->
</div><!-- end content -->

<!-- Example Images Modal -->
<div class="modal fade" id="exampleImagesModal" tabindex="-1" aria-labelledby="exampleImagesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header border-bottom py-3">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="exampleImagesModalLabel">
            <i class="bi bi-images me-2 text-primary"></i>Example Images
          </h5>
          <small class="text-muted" id="modalPromptSubtitle">Manage example output photos for this prompt</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <div id="modalAlertContainer"></div>

        <!-- Slot Counter -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0 text-dark">
            Associated Output Examples <span class="badge bg-secondary rounded-pill ms-1" id="modalCountBadge">0 / 5</span>
          </h6>
          <small class="text-muted">Maximum 5 example images</small>
        </div>

        <!-- Loading State -->
        <div id="modalLoadingSpinner" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="text-muted small mt-2 mb-0">Loading example images...</p>
        </div>

        <!-- Empty State -->
        <div id="modalEmptyState" class="text-center py-4 border rounded-3 bg-light mb-4 d-none">
          <i class="bi bi-images text-muted fs-1 d-block mb-2 opacity-50"></i>
          <p class="text-muted mb-1 fw-medium">No example images uploaded yet.</p>
          <small class="text-muted">Upload generated outputs below to showcase this prompt.</small>
        </div>

        <!-- Images Grid -->
        <div id="modalImagesGrid" class="row g-3 mb-4 d-none"></div>

        <hr class="my-4">

        <!-- Upload Form -->
        <div id="modalUploadSection">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-cloud-arrow-up me-1 text-primary"></i> Upload New Examples
            </h6>
            <span class="small text-muted" id="modalSlotsRemainingText">3 slots available</span>
          </div>

          <form id="modalUploadForm" enctype="multipart/form-data">
            <!-- Interactive Dropzone -->
            <div class="upload-dropzone rounded-3 p-4 text-center position-relative mb-3" id="dropzoneBox">
              <input type="file" 
                     id="modalFileInput" 
                     name="files[]" 
                     multiple 
                     accept="image/jpeg,image/png,image/jpg,image/webp" 
                     class="custom-file">
              <div class="py-2" style="pointer-events: none;">
                <i class="bi bi-cloud-arrow-up text-primary fs-1 d-block mb-2"></i>
                <div class="fw-bold text-dark mb-1">Click to browse or drag &amp; drop images here</div>
                <div class="text-muted small">Supports JPG, PNG, WebP &bull; Max 10MB per file</div>
              </div>
            </div>

            <!-- Client-side Preview Container -->
            <div id="uploadPreviewsContainer" class="d-flex flex-wrap gap-2 mb-3"></div>

            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary px-4" id="modalUploadSubmitBtn" disabled>
                <span class="spinner-border spinner-border-sm me-1 d-none" id="modalUploadSpinner" role="status"></span>
                <i class="bi bi-upload me-1" id="modalUploadIcon"></i> Upload Images
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
  <div id="liveAdminToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill text-success fs-5" id="toastIcon"></i>
        <span id="toastMessage">Action completed successfully.</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

  var currentPromptId = null;
  var currentExamplesCount = 0;
  var maxAllowedExamples = 5;
  var exampleModal = new bootstrap.Modal(document.getElementById('exampleImagesModal'));
  var adminToast = new bootstrap.Toast(document.getElementById('liveAdminToast'), { delay: 3000 });

  function showToast(message, isSuccess = true) {
    $('#toastMessage').text(message);
    if (isSuccess) {
      $('#toastIcon').attr('class', 'bi bi-check-circle-fill text-success fs-5');
    } else {
      $('#toastIcon').attr('class', 'bi bi-exclamation-triangle-fill text-danger fs-5');
    }
    adminToast.show();
  }

  function showModalAlert(message, type = 'danger') {
    var icon = (type === 'success') ? 'bi-check-circle' : 'bi-exclamation-triangle';
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show mb-3" role="alert">' +
      '<i class="bi ' + icon + ' me-2"></i>' + message +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
      '</div>';
    $('#modalAlertContainer').html(alertHtml);
  }

  // 1. Toggle Featured Status (AJAX)
  $(document).on('change', '.toggleFeaturedSwitch', function() {
    var $switch = $(this);
    var imageId = $switch.data('id');
    var isChecked = $switch.is(':checked');
    var $star = $('.featuredIcon-' + imageId);

    $switch.prop('disabled', true);

    $.ajax({
      url: "{{ url('panel/admin/images/toggle-featured') }}",
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { id: imageId },
      dataType: 'json',
      success: function(res) {
        $switch.prop('disabled', false);
        if (res.success) {
          if (res.is_featured) {
            $switch.prop('checked', true);
            $star.removeClass('text-muted opacity-25').addClass('text-warning');
          } else {
            $switch.prop('checked', false);
            $star.removeClass('text-warning').addClass('text-muted opacity-25');
          }
          showToast(res.message, true);
        } else {
          $switch.prop('checked', !isChecked);
          showToast(res.message || 'Failed to update featured status.', false);
        }
      },
      error: function(xhr) {
        $switch.prop('disabled', false);
        $switch.prop('checked', !isChecked);
        var err = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
        showToast(err, false);
      }
    });
  });

  // 2. Toggle Free / For Sale Status (AJAX)
  $(document).on('change', '.toggleSaleSwitch', function() {
    var $switch = $(this);
    var imageId = $switch.data('id');
    var isChecked = $switch.is(':checked');
    var $badge = $('.saleBadge-' + imageId);

    $switch.prop('disabled', true);

    $.ajax({
      url: "{{ url('panel/admin/images/toggle-sale') }}",
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { id: imageId },
      dataType: 'json',
      success: function(res) {
        $switch.prop('disabled', false);
        if (res.success) {
          $badge.removeClass('bg-warning bg-secondary').addClass(res.badge_class).text(res.label);
          $switch.prop('checked', res.is_sale);
          showToast(res.message, true);
        } else {
          $switch.prop('checked', !isChecked);
          showToast(res.message || 'Failed to update item sale status.', false);
        }
      },
      error: function(xhr) {
        $switch.prop('disabled', false);
        $switch.prop('checked', !isChecked);
        var err = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
        showToast(err, false);
      }
    });
  });

  // 3. Open Example Images Modal
  $(document).on('click', '.btnOpenExamplesModal', function() {
    currentPromptId = $(this).data('id');
    var promptTitle = $(this).data('title');

    $('#modalPromptSubtitle').text('Prompt: "' + promptTitle + '" (ID: #' + currentPromptId + ')');
    $('#modalAlertContainer').empty();
    $('#modalFileInput').val('');
    $('#uploadPreviewsContainer').empty();
    $('#modalUploadSubmitBtn').prop('disabled', true);
    $('#modalLoadingSpinner').removeClass('d-none');
    $('#modalEmptyState').addClass('d-none');
    $('#modalImagesGrid').addClass('d-none').empty();

    exampleModal.show();
    loadExampleImages(currentPromptId);
  });

  function loadExampleImages(id) {
    $.ajax({
      url: "{{ url('panel/admin/images') }}/" + id + "/examples",
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        $('#modalLoadingSpinner').addClass('d-none');
        if (res.success) {
          currentExamplesCount = res.count;
          maxAllowedExamples = res.max || 5;
          updateModalCounter();

          if (res.examples && res.examples.length > 0) {
            $('#modalEmptyState').addClass('d-none');
            $('#modalImagesGrid').removeClass('d-none').empty();

            $.each(res.examples, function(idx, item) {
              renderExampleCard(item);
            });
          } else {
            $('#modalEmptyState').removeClass('d-none');
            $('#modalImagesGrid').addClass('d-none').empty();
          }
        } else {
          showModalAlert(res.message || 'Failed to load example images.', 'danger');
        }
      },
      error: function() {
        $('#modalLoadingSpinner').addClass('d-none');
        showModalAlert('Unable to load example images from server.', 'danger');
      }
    });
  }

  function renderExampleCard(item) {
    var cardHtml = '<div class="col-6 col-md-4 col-lg-3" id="example-card-' + item.id + '">' +
      '<div class="card h-100 shadow-sm border position-relative">' +
        '<a href="' + item.url + '" target="_blank" class="d-block overflow-hidden rounded-top" style="height: 140px; background-color: #f8f9fa;">' +
          '<img src="' + item.url + '" class="w-100 h-100" style="object-fit: cover;" alt="Example output" />' +
        '</a>' +
        '<div class="card-body p-2 d-flex justify-content-between align-items-center bg-white">' +
          '<small class="text-muted text-truncate me-2" style="font-size: 0.75rem;" title="' + (item.created_at || item.file) + '">' +
            (item.created_at ? item.created_at : '#' + item.id) +
          '</small>' +
          '<button type="button" class="btn btn-sm btn-outline-danger p-1 py-0 btnDeleteExample" data-id="' + item.id + '" title="Delete this example">' +
            '<i class="bi bi-trash"></i>' +
          '</button>' +
        '</div>' +
      '</div>' +
    '</div>';

    $('#modalImagesGrid').append(cardHtml);
  }

  function updateModalCounter() {
    $('#modalCountBadge').text(currentExamplesCount + ' / ' + maxAllowedExamples);
    $('.ex-count-badge-' + currentPromptId).text(currentExamplesCount);

    var remaining = maxAllowedExamples - currentExamplesCount;
    if (remaining > 0) {
      $('#modalSlotsRemainingText').text(remaining + ' slot' + (remaining > 1 ? 's' : '') + ' available');
      $('#dropzoneBox').show();
      $('#modalFileInput').prop('disabled', false);
    } else {
      $('#modalSlotsRemainingText').text('Max limit reached');
      $('#dropzoneBox').hide();
      $('#modalFileInput').prop('disabled', true);
      $('#modalUploadSubmitBtn').prop('disabled', true);
      showModalAlert('Maximum capacity reached (' + maxAllowedExamples + ' of ' + maxAllowedExamples + '). Delete an existing example to upload new ones.', 'warning');
    }
  }

  // Drag & drop styling
  $('#dropzoneBox').on('dragover dragenter', function() {
    $(this).addClass('dragover');
  });
  $('#dropzoneBox').on('dragleave dragend drop', function() {
    $(this).removeClass('dragover');
  });

  // 4. File input change & previews
  $('#modalFileInput').on('change', function() {
    var files = this.files;
    $('#uploadPreviewsContainer').empty();

    if (!files || files.length === 0) {
      $('#modalUploadSubmitBtn').prop('disabled', true);
      return;
    }

    var remaining = maxAllowedExamples - currentExamplesCount;
    if (files.length > remaining) {
      showModalAlert('You selected ' + files.length + ' image(s), but only ' + remaining + ' slot(s) remain. Only the first ' + remaining + ' will be uploaded.', 'warning');
    }

    var validCount = 0;
    Array.from(files).slice(0, remaining).forEach(function(file, index) {
      if (file.type.match('image.*')) {
        validCount++;
        var reader = new FileReader();
        reader.onload = function(e) {
          var previewHtml = '<div class="preview-thumb-item shadow-sm" title="' + file.name + '">' +
            '<img src="' + e.target.result + '" alt="' + file.name + '" />' +
          '</div>';
          $('#uploadPreviewsContainer').append(previewHtml);
        };
        reader.readAsDataURL(file);
      }
    });

    $('#modalUploadSubmitBtn').prop('disabled', validCount === 0);
  });

  // 5. Upload form submit
  $('#modalUploadForm').on('submit', function(e) {
    e.preventDefault();
    if (!currentPromptId) return;

    var fileInput = document.getElementById('modalFileInput');
    if (!fileInput.files || fileInput.files.length === 0) return;

    var formData = new FormData();
    var remaining = maxAllowedExamples - currentExamplesCount;
    var filesToUpload = Array.from(fileInput.files).slice(0, remaining);

    for (var i = 0; i < filesToUpload.length; i++) {
      formData.append('files[]', filesToUpload[i]);
    }

    $('#modalUploadSubmitBtn').prop('disabled', true);
    $('#modalUploadSpinner').removeClass('d-none');
    $('#modalUploadIcon').addClass('d-none');

    $.ajax({
      url: "{{ url('panel/admin/images') }}/" + currentPromptId + "/examples/upload",
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        $('#modalUploadSpinner').addClass('d-none');
        $('#modalUploadIcon').removeClass('d-none');
        $('#modalFileInput').val('');
        $('#uploadPreviewsContainer').empty();

        if (res.success) {
          showModalAlert(res.message, 'success');
          showToast(res.message, true);

          $('#modalEmptyState').addClass('d-none');
          $('#modalImagesGrid').removeClass('d-none');

          if (res.uploaded && res.uploaded.length > 0) {
            $.each(res.uploaded, function(i, item) {
              renderExampleCard(item);
            });
          }

          currentExamplesCount = res.total_count;
          updateModalCounter();
        } else {
          $('#modalUploadSubmitBtn').prop('disabled', false);
          showModalAlert(res.message || 'Upload failed.', 'danger');
        }
      },
      error: function(xhr) {
        $('#modalUploadSpinner').addClass('d-none');
        $('#modalUploadIcon').removeClass('d-none');
        $('#modalUploadSubmitBtn').prop('disabled', false);
        var err = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Upload failed. Ensure images are valid and under 10MB.';
        showModalAlert(err, 'danger');
      }
    });
  });

  // 6. Delete Example Image (AJAX)
  $(document).on('click', '.btnDeleteExample', function() {
    var exampleId = $(this).data('id');
    var $btn = $(this);

    swal({
      title: "{{ trans('misc.delete_confirm') }}",
      text: "This example image will be permanently removed from storage and this prompt.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "{{ trans('misc.yes_confirm') }}",
      cancelButtonText: "{{ trans('misc.cancel_confirm') }}",
      closeOnConfirm: true
    }, function(isConfirm) {
      if (isConfirm) {
        $btn.prop('disabled', true);

        $.ajax({
          url: "{{ url('panel/admin/images/examples') }}/" + exampleId + "/delete",
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              $('#example-card-' + exampleId).fadeOut(300, function() {
                $(this).remove();
                if ($('#modalImagesGrid').children().length === 0) {
                  $('#modalEmptyState').removeClass('d-none');
                  $('#modalImagesGrid').addClass('d-none');
                }
              });

              currentExamplesCount = res.remaining_count;
              updateModalCounter();
              showToast(res.message, true);
              $('#modalAlertContainer').empty();
            } else {
              $btn.prop('disabled', false);
              showModalAlert(res.message || 'Unable to delete example image.', 'danger');
            }
          },
          error: function() {
            $btn.prop('disabled', false);
            showModalAlert('Error contacting server to delete example.', 'danger');
          }
        });
      }
    });
  });

});
</script>
@endsection
