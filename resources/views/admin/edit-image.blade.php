@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/images') }}">{{ __('misc.images') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.edit') }}</span>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ $data->title }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

    @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form class="form-horizontal" method="POST" action="{{ url('panel/admin/images/update') }}" enctype="multipart/form-data">
             @csrf
             <input type="hidden" name="id" value="{{$data->id}}">

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.title') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->title }}" name="title" type="text" class="form-control">
		          </div>
		        </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Slug</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->slug }}" name="slug" type="text" class="form-control">
		            <small class="text-muted d-block mt-1">Leave empty to generate automatically from title.</small>
		          </div>
		        </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-labe text-lg-end">{{ trans('misc.category') }}</label>
		          <div class="col-sm-10">
		            <select name="categories_id" class="form-select" id="category">
                  @foreach (Categories::where('mode','on')->orderBy('name')->get() as $category)
                      <option @if($data->categories_id == $category->id) selected="selected" @endif value="{{$category->id}}">{{ $category->name }}</option>
                    @endforeach
		           </select>
		          </div>
		        </div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-labe text-lg-end">{{ trans('misc.subcategory') }}</label>
					<div class="col-sm-10">
					  <select name="subcategories_id" class="form-select">
						<option selected value="" id="subcategory">{{ __('misc.select') }}</option>
					@foreach (Subcategories::where('mode','on')->whereCategoryId($data->categories_id)->orderBy('name')->get() as $subcategory)
						<option class="valuesSub" @if($data->subcategories_id == $subcategory->id) selected="selected" @endif value="{{$subcategory->id}}">{{ $subcategory->name }}</option>
					  @endforeach
					 </select>
					</div>
				  </div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-label text-lg-end">Photoshoot</label>
					<div class="col-sm-10">
						<div class="card bg-white border p-3 rounded-3 shadow-none">
							<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
								<span class="small fw-semibold text-muted">Photoshoot Assignment (Optional)</span>
								<div class="btn-group btn-group-sm" role="group" id="photoshootModeGroup">
									<input type="radio" class="btn-check" name="photoshoot_mode" id="modeNoneAdmin" value="none" @checked(empty($data->photoshoot_id))>
									<label class="btn btn-outline-secondary" for="modeNoneAdmin">No Photoshoot</label>

									<input type="radio" class="btn-check" name="photoshoot_mode" id="modeExistingAdmin" value="existing" @checked(!empty($data->photoshoot_id))>
									<label class="btn btn-outline-primary" for="modeExistingAdmin"><i class="bi bi-search me-1"></i>Search Existing</label>

									<input type="radio" class="btn-check" name="photoshoot_mode" id="modeNewAdmin" value="new">
									<label class="btn btn-outline-success" for="modeNewAdmin"><i class="bi bi-plus-lg me-1"></i>Create New</label>
								</div>
							</div>

							<!-- Hidden photoshoot ID input -->
							<input type="hidden" name="photoshoot_id" id="admin_photoshoot_id_val" value="{{ $data->photoshoot_id }}">

							<!-- Mode: Existing Photoshoot Live Search -->
							<div id="adminBoxExistingPhotoshoot" class="@if(empty($data->photoshoot_id)) display-none @endif mt-2">
								<div class="position-relative">
									<div class="input-group @if(!empty($data->photoshoot_id)) display-none @endif" id="adminSearchInputGroup">
										<span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
										<input type="text" id="adminPhotoshootSearchInput" class="form-control border-start-0 ps-0" placeholder="Type to search photoshoot by title (e.g. 'Skincare', 'Perfume')..." autocomplete="off">
										<span class="input-group-text bg-white border-start-0 display-none" id="adminPhotoshootSearchSpinner">
											<span class="spinner-border spinner-border-sm text-primary" role="status"></span>
										</span>
									</div>

									<!-- Search Results Dropdown List -->
									<div id="adminPhotoshootSearchResults" class="list-group position-absolute w-100 shadow-lg mt-1 display-none" style="z-index: 1050; max-height: 240px; overflow-y: auto;"></div>
								</div>

								<!-- Selected Photoshoot Confirmation Card -->
								<div id="adminSelectedPhotoshootBox" class="@if(empty($data->photoshoot_id)) display-none @endif mt-2">
									<div class="p-2 px-3 rounded-2 border border-success bg-success-subtle d-flex align-items-center justify-content-between">
										<div class="d-flex align-items-center gap-2">
											<i class="bi bi-check-circle-fill text-success fs-5"></i>
											<div>
												<span class="fw-bold text-dark d-block" id="adminSelectedPhotoshootTitle">{{ $data->photoshoot ? $data->photoshoot->title : 'Photoshoot #' . $data->photoshoot_id }}</span>
												<small class="text-muted" id="adminSelectedPhotoshootMeta">
													@if($data->photoshoot)
														ID: #{{ $data->photoshoot->id }} • {{ $data->photoshoot->prompts_count }} prompts
													@endif
												</small>
											</div>
										</div>
										<button type="button" class="btn btn-sm btn-outline-danger" id="adminBtnDeselectPhotoshoot">
											<i class="bi bi-arrow-repeat me-1"></i> Change
										</button>
									</div>
								</div>
								<small class="text-muted d-block mt-1">Search and select an existing photoshoot to assign this prompt to.</small>
							</div>

							<!-- Mode: New Photoshoot -->
							<div id="adminBoxNewPhotoshoot" class="display-none mt-2">
								<input type="text" class="form-control" name="photoshoot_title" id="admin_photoshoot_title" placeholder="e.g. Luxury Skincare Product Set">
								<small class="text-muted d-block mt-1">A new photoshoot will be created with this name and this prompt will be added to it.</small>
							</div>

						</div>
					</div>
				</div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('misc.tags') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->tags }}" name="tags" type="text" class="form-control">
		          </div>
		        </div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-labe text-lg-end">{{ trans('misc.description') }}</label>
					<div class="col-sm-10">
				  <textarea class="form-control" name="description" rows="4">{{ $data->description }}</textarea>
					</div>
				  </div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-label text-lg-end">Meta Title</label>
					<div class="col-sm-10">
					  <input value="{{ $data->meta_title }}" name="meta_title" type="text" class="form-control" placeholder="SEO Title">
					  <small class="text-muted d-block mt-1">Leave empty to generate automatically from prompt title.</small>
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-label text-lg-end">Meta Description</label>
					<div class="col-sm-10">
					  <textarea class="form-control" name="meta_description" rows="2" placeholder="Brief summary for search result snippets...">{{ $data->meta_description }}</textarea>
					  <small class="text-muted d-block mt-1">Leave empty to generate automatically from prompt description.</small>
					</div>
				</div>

				<div class="row mb-3">
					<label class="col-sm-2 col-form-label text-lg-end">Meta Keywords</label>
					<div class="col-sm-10">
					  <input value="{{ $data->meta_keywords }}" name="meta_keywords" type="text" class="form-control" placeholder="keyword1, keyword2, keyword3">
					  <small class="text-muted d-block mt-1">Leave empty to generate automatically from tags.</small>
					</div>
				</div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ trans('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                 <input class="form-check-input" type="checkbox" name="status" @if ($data->status == 'active') checked="checked" @endif value="active" role="switch">
               </div>
              </div>
            </fieldset><!-- end row -->

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ trans('misc.featured') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                 <input class="form-check-input" type="checkbox" name="featured" @if ($data->featured == 'yes') checked="checked" @endif value="yes" role="switch">
               </div>
              </div>
            </fieldset><!-- end row -->

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ trans('misc.item_for_sale') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="item_for_sale" id="itemForSaleSwitch" @if ($data->item_for_sale == 'sale') checked="checked" @endif value="sale" role="switch">
                  <label class="form-check-label fw-semibold" for="itemForSaleSwitch" id="itemForSaleLabel">
                    {{ $data->item_for_sale == 'sale' ? 'Premium (For Sale)' : 'Free Prompt' }}
                  </label>
                </div>
                <small class="text-muted d-block mt-1">Enable to require a Pro subscription plan; disable for free access to all users.</small>
              </div>
            </fieldset><!-- end row -->

            <div class="row mb-4">
              <label class="col-sm-2 col-form-label text-lg-end">Example Images</label>
              <div class="col-sm-10">
                <div class="card border rounded-3 p-3 bg-light shadow-sm">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0">
                      <i class="bi bi-images me-1 text-primary"></i> Generated Example Outputs (Max 5)
                    </h6>
                    <span class="badge bg-secondary rounded-pill" id="editExCountBadge">
                      {{ $data->examples ? $data->examples->count() : 0 }} / 5
                    </span>
                  </div>
                  <p class="small text-muted mb-3">Example output photos showcase results created with this prompt.</p>

                  @if ($data->examples && $data->examples->count() > 0)
                    <p class="small fw-semibold text-dark mb-2">Current Example Images:</p>
                    <div class="row g-3 mb-3" id="editExamplesGrid">
                      @foreach ($data->examples as $ex)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 position-relative" id="edit-ex-card-{{ $ex->id }}">
                          <div class="card h-100 border shadow-sm p-1 bg-white text-center">
                            <a href="{{ Storage::url(config('path.examples') . $ex->file) }}" target="_blank">
                              <img src="{{ Storage::url(config('path.examples') . $ex->file) }}" class="img-fluid rounded mb-2" style="height: 90px; width: 100%; object-fit: cover;">
                            </a>
                            <div class="form-check d-flex align-items-center justify-content-center p-0 m-0">
                              <input class="form-check-input ms-0 me-1" type="checkbox" name="delete_examples[]" value="{{ $ex->id }}" id="delEx{{ $ex->id }}">
                              <label class="form-check-label text-danger fw-semibold small" for="delEx{{ $ex->id }}">Delete</label>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <div class="text-center py-3 border rounded bg-white mb-3 text-muted small">
                      <i class="bi bi-images fs-3 d-block mb-1 opacity-50"></i>
                      No example images uploaded for this prompt yet.
                    </div>
                  @endif

                  @php
                    $curCount = $data->examples ? $data->examples->count() : 0;
                  @endphp

                  @if ($curCount < 5)
                    <div class="mt-2">
                      <label class="form-label small fw-semibold text-dark mb-1" for="editExamplePhotosInput">
                        Add New Example Images ({{ 5 - $curCount }} remaining):
                      </label>
                      <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" name="example_photos[]" id="editExamplePhotosInput" multiple class="custom-file form-control" style="position: static !important; opacity: 1 !important; visibility: visible !important;">
                      <div id="editPreviewsContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>
                  @else
                    <small class="text-muted d-block mt-1">Maximum limit of 5 example images reached. Check "Delete" on existing images and click Save Changes to upload new ones.</small>
                  @endif
                </div>
              </div>
            </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5 me-2">{{ __('admin.save') }}</button>
                <a href="{{ url('prompt', $data->slug) }}" target="_blank" class="btn btn-link text-reset mt-3 px-3 e-none text-decoration-none">{{ __('admin.view') }} <i class="bi-box-arrow-up-right ms-1"></i></a>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
  // Admin Photoshoot Mode switcher
  $('input[name="photoshoot_mode"]').on('change', function() {
    var mode = $(this).val();
    if (mode === 'existing') {
      $('#adminBoxNewPhotoshoot').slideUp();
      $('#admin_photoshoot_title').val('');
      $('#adminBoxExistingPhotoshoot').slideDown();
      if (!$('#admin_photoshoot_id_val').val()) {
        $('#adminSearchInputGroup').show();
        $('#adminPhotoshootSearchInput').show().focus();
        searchAdminPhotoshoots('');
      }
    } else if (mode === 'new') {
      $('#adminBoxExistingPhotoshoot').slideUp();
      $('#admin_photoshoot_id_val').val('new');
      $('#adminSelectedPhotoshootBox').hide();
      $('#adminPhotoshootSearchInput').val('');
      $('#adminBoxNewPhotoshoot').slideDown();
      $('#admin_photoshoot_title').focus();
    } else {
      // none
      $('#adminBoxExistingPhotoshoot').slideUp();
      $('#adminBoxNewPhotoshoot').slideUp();
      $('#admin_photoshoot_id_val').val('');
      $('#admin_photoshoot_title').val('');
      $('#adminSelectedPhotoshootBox').hide();
      $('#adminPhotoshootSearchInput').val('');
    }
  });

  // Admin Photoshoot Search Debounce & AJAX
  var adminPhotoshootSearchTimer = null;
  function searchAdminPhotoshoots(query) {
    $('#adminPhotoshootSearchSpinner').show();
    $.ajax({
      url: "{{ url('ajax/photoshoots/search') }}",
      type: 'GET',
      data: { q: query },
      dataType: 'json',
      success: function(res) {
        $('#adminPhotoshootSearchSpinner').hide();
        var $results = $('#adminPhotoshootSearchResults');
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
        $('#adminPhotoshootSearchSpinner').hide();
      }
    });
  }

  $('#adminPhotoshootSearchInput').on('keyup', function() {
    var q = $(this).val().trim();
    clearTimeout(adminPhotoshootSearchTimer);
    adminPhotoshootSearchTimer = setTimeout(function() {
      searchAdminPhotoshoots(q);
    }, 250);
  });

  $('#adminPhotoshootSearchInput').on('focus', function() {
    searchAdminPhotoshoots($(this).val().trim());
  });

  // Select Photoshoot from Search Results
  $(document).on('click', '#adminPhotoshootSearchResults button', function() {
    var item = $(this).data('photoshoot');
    if (!item) return;
    $('#admin_photoshoot_id_val').val(item.id);
    $('#adminSelectedPhotoshootTitle').text(item.title);
    $('#adminSelectedPhotoshootMeta').text('ID: #' + item.id + ' • ' + item.prompts_count + ' prompts');
    $('#adminSelectedPhotoshootBox').slideDown();
    $('#adminPhotoshootSearchResults').slideUp();
    $('#adminSearchInputGroup').hide();
  });

  // Change selected photoshoot
  $('#adminBtnDeselectPhotoshoot').on('click', function() {
    $('#admin_photoshoot_id_val').val('');
    $('#adminSelectedPhotoshootBox').hide();
    $('#adminSearchInputGroup').show();
    $('#adminPhotoshootSearchInput').val('').show().focus();
    searchAdminPhotoshoots('');
  });

  // Close search results dropdown when clicking outside
  $(document).on('click', function(e) {
    if (!$(e.target).closest('#adminBoxExistingPhotoshoot').length) {
      $('#adminPhotoshootSearchResults').hide();
    }
  });

  $('#itemForSaleSwitch').on('change', function() {
    $('#itemForSaleLabel').text($(this).is(':checked') ? 'Premium (For Sale)' : 'Free Prompt');
  });

  $('#editExamplePhotosInput').on('change', function() {
    var container = $('#editPreviewsContainer');
    container.empty();
    if (!this.files) return;

    Array.from(this.files).forEach(function(file) {
      if (file.type.match('image.*')) {
        var reader = new FileReader();
        reader.onload = function(e) {
          container.append('<div class="border rounded p-1 bg-white shadow-sm" style="width: 60px; height: 60px;"><img src="' + e.target.result + '" class="w-100 h-100 rounded" style="object-fit: cover;" /></div>');
        };
        reader.readAsDataURL(file);
      }
    });
  });
});
</script>
@endsection
