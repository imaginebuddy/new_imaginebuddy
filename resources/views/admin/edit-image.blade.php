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
