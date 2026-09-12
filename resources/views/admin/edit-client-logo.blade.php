@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/client-logos') }}">{{ __('admin.client_logos') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.edit') }} ({{ $logo->name }})</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/client-logos/update', $logo->id) }}" enctype="multipart/form-data">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.client_name') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('name', $logo->name) }}" name="name" required type="text" class="form-control" placeholder="e.g. OpenAI, Figma, Canva">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.logo_image') }}</label>
		          <div class="col-lg-6 col-sm-10">
                @if ($logo->image_url)
                  <div class="mb-2 p-3 border rounded bg-light d-inline-flex align-items-center justify-content-center" style="max-width: 250px; height: 90px;">
                    <img src="{{ $logo->image_url }}" alt="{{ $logo->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                  </div>
                  <small class="d-block text-muted mb-2">Upload a new file below if you want to replace this logo.</small>
                @endif

                <div class="input-group mb-1">
                  <input name="image" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="form-control custom-file rounded-pill" id="logoFileInput">
                </div>
                <small class="d-block text-muted">{{ __('admin.logo_help_text') }}</small>

                {{-- Live Image Preview Container --}}
                <div class="mt-3 p-3 border rounded bg-light d-none align-items-center justify-content-center" id="logoPreviewWrapper" style="max-width: 250px; height: 90px;">
                  <img id="logoPreviewImg" src="#" alt="New Logo Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.website_url') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('website_url', $logo->website_url) }}" name="website_url" type="url" class="form-control" placeholder="https://example.com">
                <small class="text-muted d-block mt-1">When clicked on the home page, users will be taken to this address.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.sort_order') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('sort_order', $logo->sort_order) }}" name="sort_order" type="number" min="0" class="form-control" style="max-width: 150px;">
                <small class="text-muted d-block mt-1">Lower numbers appear first on the home page slider.</small>
		          </div>
		        </div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="status" {{ old('status', $logo->status) == 'active' ? 'checked="checked"' : '' }} value="active" role="switch">
                  <label class="form-check-label ms-2">{{ __('admin.active') }} (Displayed in home page slider)</label>
                </div>
              </div>
            </fieldset><!-- end row -->

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

@section('javascript')
<script>
  document.getElementById('logoFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewWrapper = document.getElementById('logoPreviewWrapper');
    const previewImg = document.getElementById('logoPreviewImg');
    
    if (file) {
      const reader = new FileReader();
      reader.onload = function(evt) {
        previewImg.src = evt.target.result;
        previewWrapper.classList.remove('d-none');
        previewWrapper.classList.add('d-flex');
      }
      reader.readAsDataURL(file);
    } else {
      previewWrapper.classList.remove('d-flex');
      previewWrapper.classList.add('d-none');
    }
  });
</script>
@endsection
