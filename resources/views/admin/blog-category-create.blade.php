@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/blog') }}">Blog</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/blog/categories') }}">Categories</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.add_new') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/blog/categories/create') }}">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.name') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('name') }}" name="name" required type="text" class="form-control" placeholder="e.g. Prompt Engineering Guides">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.slug') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('slug') }}" name="slug" type="text" class="form-control" placeholder="Leave blank to automatically generate from name">
                <small class="text-muted d-block mt-1">Unique URL identifier (e.g. <code>prompt-engineering-guides</code>)</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.description') }}</label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="description" rows="3" placeholder="Short description of this category topic for users and search engines...">{{ old('description') }}</textarea>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">SEO Meta Title</label>
		          <div class="col-sm-10">
		            <input value="{{ old('seo_title') }}" name="seo_title" type="text" class="form-control" placeholder="Optional. Custom title tag for this category archive page">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">SEO Meta Description</label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="seo_description" rows="2" placeholder="Optional. Custom meta description for search engines...">{{ old('seo_description') }}</textarea>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.sort_order') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('sort_order', 0) }}" name="sort_order" type="number" min="0" class="form-control" style="max-width: 150px;">
		          </div>
		        </div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="status" checked="checked" value="active" role="switch" id="statusSwitch">
                  <label class="form-check-label ms-2" for="statusSwitch">Active (Visible in category filters and archive)</label>
                </div>
              </div>
            </fieldset>

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
