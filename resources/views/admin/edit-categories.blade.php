@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/categories') }}">{{ __('misc.categories') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">{{ __('admin.edit') }}</span>
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

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					<form method="post" action="{{ url('panel/admin/categories/update') }}" enctype="multipart/form-data">
             @csrf
             <input type="hidden" name="id" value="{{ $categories->id }}">

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.name') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('name', $categories->name) }}" name="name" type="text" class="form-control" required>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.slug') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('slug', $categories->slug) }}" name="slug" type="text" class="form-control" required>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.page_title') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('page_title', $categories->page_title) }}" name="page_title" type="text" class="form-control" placeholder="AI Product Photography Prompts for {{ $categories->name }}">
                <small class="text-muted d-block mt-1">Tip: e.g. <code>{{ $categories->name }} &lt;span style="color: #00d690;"&gt;AI Product Photography Prompts&lt;/span&gt;</code></small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Hero Subtitle</label>
		          <div class="col-sm-10">
		            <textarea class="form-control" name="hero_lead" rows="2" placeholder="Explore curated, tested AI prompts designed for {{ strtolower($categories->name) }}...">{{ old('hero_lead', $categories->hero_lead) }}</textarea>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.seo_title') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('seo_title', $categories->seo_title) }}" name="seo_title" type="text" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.keywords') }} (SEO)</label>
		          <div class="col-sm-10">
		            <input value="{{ old('keywords', $categories->keywords) }}" name="keywords" type="text" class="form-control">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.description') }} (SEO)</label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="description" rows="4">{{ old('description', $categories->description) }}</textarea>
		          </div>
		        </div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                 <input class="form-check-input" type="checkbox" name="mode" @checked(old('mode', $categories->mode) == 'on') value="on" role="switch">
               </div>
              </div>
            </fieldset>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.thumbnail') }} ({{__('misc.optional')}})</label>
              <div class="col-lg-5 col-sm-10">
                <div class="input-group mb-1">
                  <input name="thumbnail" type="file" class="form-control custom-file rounded-pill">
                </div>
                <small class="d-block text-muted">{{ __('admin.thumbnail_desc') }}</small>
                @if ($categories->thumbnail)
                <div class="mt-2">
                  <img src="{{ asset('public/img-category/' . $categories->thumbnail) }}" alt="{{ $categories->name }}" class="rounded shadow-xs" style="max-height: 80px;">
                </div>
                @endif
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
