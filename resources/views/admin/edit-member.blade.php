@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/members') }}">{{ __('admin.members') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.edit') }}</span>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ $data->username }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

    @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form class="form-horizontal" method="POST" action="{{ url('panel/admin/members/edit', $data->id) }}" enctype="multipart/form-data">
             @csrf
             <input type="hidden" name="id" value="{{$data->id}}">

             <div class="row mb-3">
 		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('misc.avatar') }}</label>
 		          <div class="col-sm-10">
 		            <img src="{{Storage::url(config('path.avatar').$data->avatar)}}" width="80" height="80" class="rounded-circle" />
 		          </div>
 		        </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.name') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->name }}" name="name" type="text" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('auth.username') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->username }}" disabled type="text" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('auth.email') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ $data->email }}" name="email" type="text" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-labe text-lg-end">{{ trans('admin.status') }}</label>
		          <div class="col-sm-10">
		            <select name="status" class="form-select">
                  <option @if ($data->status == 'active') selected="selected" @endif value="active">{{ trans('admin.active') }}</option>
                  <option @if ($data->status == 'pending') selected="selected" @endif value="pending">{{ trans('admin.pending') }}</option>
                  <option @if ($data->status == 'suspended') selected="suspended" @endif value="suspended">{{ trans('admin.suspended') }}</option>
		           </select>
		          </div>
		        </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-labe text-lg-end">{{ trans('admin.role') }}</label>
              <div class="col-sm-10">
                <select name="role" class="form-select">
				<option @if ($data->role == '0') selected="selected" @endif value="0">{{ trans('admin.normal') }}</option>

					@foreach (RolesAndPermissions::where('permissions', '<>', 'full_access')->get() as $role)
						<option @if ($data->role == $role->id) selected="selected" @endif value="{{ $role->id }}">{{ $role->name }}</option>
					@endforeach
               </select>
              </div>
            </div>

			<div class="row mb-3">
				<label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.balance') }}</label>
				<div class="col-sm-10">
					<input value="{{ $data->balance }}" name="balance" type="text" class="form-control isNumber" autocomplete="off">
				</div>
			</div>

		   <div class="row mb-3">
				<label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.funds') }}</label>
				<div class="col-sm-10">
					<input value="{{ $data->funds }}" name="funds" type="text" class="form-control isNumber" autocomplete="off">
				</div>
			</div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ trans('admin.authorized_to_upload') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                 <input class="form-check-input" type="checkbox" name="authorized_to_upload" @if ($data->authorized_to_upload == 'yes') checked="checked" @endif value="yes" role="switch">
               </div>
              </div>
            </fieldset><!-- end row -->

            @php
              $testSub = $data->activeTestSubscription();
              $paidSub = $data->activePaidSubscription();
            @endphp
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">Pro Plan Access</label>
              <div class="col-sm-10">
                @if ($testSub)
                  <div class="alert alert-info border-0 d-flex justify-content-between align-items-center mb-0">
                    <div>
                      <i class="bi bi-star-fill text-warning me-1"></i>
                      <strong>Active Pro Photography Plan (Test Access)</strong>
                      <div class="small text-muted">Expires on {{ Helper::formatDate($testSub->ends_at) }} ({{ $testSub->days_remaining }})</div>
                      @if ($testSub->test_notes)
                        <div class="small text-muted">Note: {{ $testSub->test_notes }}</div>
                      @endif
                    </div>
                  </div>
                @elseif ($paidSub)
                  <div class="alert alert-success border-0 d-flex align-items-center mb-0">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>
                      <strong>Active Paid Subscription</strong>
                      <div class="small">Billed via {{ $paidSub->payment_gateway }} &bull; Ends on {{ Helper::formatDate($paidSub->ends_at) }}</div>
                    </div>
                  </div>
                @else
                  <div class="alert alert-light border d-flex justify-content-between align-items-center mb-0">
                    <span class="text-muted"><i class="bi bi-info-circle me-1"></i> Standard Free Member (No active subscription)</span>
                  </div>
                @endif
              </div>
            </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5 me-2">{{ __('admin.save') }}</button>
                <a href="{{ url($data->username) }}" target="_blank" class="btn btn-link text-reset mt-3 px-3 e-none text-decoration-none">{{ __('admin.view') }} <i class="bi-box-arrow-up-right ms-1"></i></a>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
