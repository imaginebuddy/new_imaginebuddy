@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/testimonials') }}">{{ __('admin.testimonials') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.edit_testimonial') }} #{{ $testimonial->id }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/testimonials/update', $testimonial->id) }}" enctype="multipart/form-data">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.name') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('name', $testimonial->name) }}" name="name" required type="text" class="form-control" placeholder="e.g. Sarah Jenkins">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.designation') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('designation', $testimonial->designation) }}" name="designation" type="text" class="form-control" placeholder="e.g. Lead Prompt Engineer">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.company') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('company', $testimonial->company) }}" name="company" type="text" class="form-control" placeholder="e.g. Pixelcraft Studios">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.rating') }}</label>
		          <div class="col-sm-10">
                <select name="rating" class="form-select">
                  <option value="">{{ __('misc.none') }}</option>
                  <option value="5" {{ old('rating', $testimonial->rating) == '5' ? 'selected' : '' }}>5 Stars (★★★★★)</option>
                  <option value="4" {{ old('rating', $testimonial->rating) == '4' ? 'selected' : '' }}>4 Stars (★★★★☆)</option>
                  <option value="3" {{ old('rating', $testimonial->rating) == '3' ? 'selected' : '' }}>3 Stars (★★★☆☆)</option>
                  <option value="2" {{ old('rating', $testimonial->rating) == '2' ? 'selected' : '' }}>2 Stars (★★☆☆☆)</option>
                  <option value="1" {{ old('rating', $testimonial->rating) == '1' ? 'selected' : '' }}>1 Star (★☆☆☆☆)</option>
                </select>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.review_text') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="content" rows="4" required placeholder="What the creator said about Imaginebuddy...">{{ old('content', $testimonial->content) }}</textarea>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.sort_order') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('sort_order', $testimonial->sort_order) }}" name="sort_order" type="number" min="0" class="form-control" style="max-width: 150px;">
                <small class="text-muted d-block mt-1">Lower numbers appear first on the homepage.</small>
		          </div>
		        </div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="status" {{ old('status', $testimonial->status) == 'active' ? 'checked="checked"' : '' }} value="active" role="switch">
                  <label class="form-check-label ms-2">{{ __('admin.active') }} (Published on homepage)</label>
                </div>
              </div>
            </fieldset><!-- end row -->

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.avatar_optional') }}</label>
              <div class="col-lg-6 col-sm-10">
                @if ($testimonial->image_url)
                  <div class="mb-3 d-flex align-items-center">
                    <img src="{{ $testimonial->image_url }}" width="70" height="70" class="rounded-circle border shadow-sm me-3" style="object-fit: cover;" alt="{{ $testimonial->name }}">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                      <label class="form-check-label text-danger" for="removeImage">
                        {{ __('admin.remove_image') }}
                      </label>
                    </div>
                  </div>
                @endif
                <div class="input-group mb-1">
                  <input name="image" type="file" accept="image/*" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block text-muted">Upload a new image to replace the current one (Square, min 200x200px, max 2MB).</small>
              </div>
            </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
