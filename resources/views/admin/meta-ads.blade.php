@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
	<a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
	<i class="bi-chevron-right me-1 fs-6"></i>
	<span class="text-muted">{{ __('admin.meta_ads') }}</span>
</h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<i class="bi bi-check2 me-1"></i> {{ session('success_message') }}

				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
					<i class="bi-x-lg"></i>
				</button>
			</div>
			@endif

			@include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					<form method="POST" action="{{ url('panel/admin/meta-ads') }}">
						@csrf

						<fieldset class="row mb-4">
							<legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
							<div class="col-sm-10">
								<div class="form-check form-switch form-switch-md">
									<input class="form-check-input" type="checkbox" name="meta_pixel_status" @if ($settings->meta_pixel_status) checked="checked" @endif value="1" role="switch" id="metaPixelStatus">
									<label class="form-check-label" for="metaPixelStatus">
										{{ __('admin.meta_pixel_status') }}
									</label>
								</div>
								<small class="text-muted d-block mt-1">When enabled, the Meta Pixel script will be injected into the &lt;head&gt; on frontend pages.</small>
							</div>
						</fieldset>

						<div class="row mb-3">
							<label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.meta_pixel_code') }}</label>
							<div class="col-sm-10">
								<textarea class="form-control font-monospace" name="meta_pixel" id="meta_pixel"
									rows="10" placeholder="Paste your Meta Pixel code here...">{{ $settings->meta_pixel }}</textarea>
								<small class="text-muted d-block mt-2">
									Paste the complete Meta Pixel code from your Meta Events Manager.
									<a href="https://adsmanager.facebook.com/events_manager2" target="_blank" rel="noopener noreferrer">
										Open Meta Events Manager <i class="bi-box-arrow-up-right ms-1"></i>
									</a>
								</small>
								@if (!empty($settings->meta_pixel_id))
								<small class="text-muted d-block mt-1">
									Detected Meta Pixel ID: <strong>{{ $settings->meta_pixel_id }}</strong>
								</small>
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
