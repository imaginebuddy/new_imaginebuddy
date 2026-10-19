@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/members') }}">{{ __('admin.members') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.add_new') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form class="form-horizontal" method="POST" action="{{ url('panel/admin/members/create') }}" autocomplete="off">
             @csrf

             <!-- Full Name -->
		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.name') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('name') }}" name="name" type="text" class="form-control" placeholder="e.g. John Doe">
		          </div>
		        </div>

            <!-- Username -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('auth.username') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('username') }}" name="username" required type="text" class="form-control" placeholder="e.g. johndoe">
                <small class="text-muted d-block mt-1">3–25 characters, letters, numbers, dashes and underscores only.</small>
		          </div>
		        </div>

            <!-- Email -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('auth.email') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('email') }}" name="email" required type="email" class="form-control" placeholder="e.g. john@example.com">
		          </div>
		        </div>

            <!-- Password -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('auth.password') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <div class="input-group">
                  <input name="password" required minlength="8" type="password" id="inputPassword" class="form-control" placeholder="Minimum 8 characters">
                  <button class="btn btn-outline-secondary" type="button" id="btnTogglePassword" title="Show/Hide Password">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                  </button>
                  <button class="btn btn-outline-secondary" type="button" id="btnGeneratePassword" title="Generate Secure Password">
                    <i class="bi bi-shuffle me-1"></i> Generate
                  </button>
                </div>
		          </div>
		        </div>

            <!-- Status -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.status') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <select name="status" class="form-select">
                  <option @if (old('status', 'active') == 'active') selected="selected" @endif value="active">{{ trans('admin.active') }} (Default)</option>
                  <option @if (old('status') == 'pending') selected="selected" @endif value="pending">{{ trans('admin.pending') }}</option>
                  <option @if (old('status') == 'suspended') selected="selected" @endif value="suspended">{{ trans('admin.suspended') }}</option>
		           </select>
		          </div>
		        </div>

            <!-- Role -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">{{ trans('admin.role') }} <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select name="role" class="form-select">
				          <option @if (old('role', '0') == '0') selected="selected" @endif value="0">{{ trans('admin.normal') }} (Default)</option>
					        @foreach ($roles as $role)
                    @if (Auth::user()->id == 1 || $role->permissions != 'full_access')
						          <option @if (old('role') == $role->id) selected="selected" @endif value="{{ $role->id }}">{{ $role->name }}</option>
                    @endif
					        @endforeach
               </select>
              </div>
            </div>

            <hr class="my-4">

            <!-- Special Pro Test Grant Section -->
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">
                <i class="bi bi-star-fill text-warning me-1"></i> Pro Plan Access
              </label>
              <div class="col-sm-10">
                <div class="card bg-light border-0 p-3 rounded-3">
                  <div class="form-check form-switch form-switch-md mb-2">
                    <input class="form-check-input" type="checkbox" name="grant_test_pro" id="grantTestProSwitch" value="yes" role="switch" @if (old('grant_test_pro') == 'yes') checked="checked" @endif>
                    <label class="form-check-label fw-bold" for="grantTestProSwitch">
                      Grant Pro Photography Plan (Test Access)
                    </label>
                  </div>
                  <small class="text-muted d-block mb-3">
                    Enables full Pro Photography features (prompt copies, daily quotas, and high-res downloads) for testing purposes without generating invoices or charging payment gateways.
                  </small>

                  <div id="testProOptions" style="{{ old('grant_test_pro') == 'yes' ? '' : 'display: none;' }}">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label small fw-semibold">Test Duration</label>
                        <select name="test_pro_duration" class="form-select form-select-sm">
                          <option value="7" @if (old('test_pro_duration') == 7) selected @endif>7 Days (Quick Test)</option>
                          <option value="14" @if (old('test_pro_duration') == 14) selected @endif>14 Days (Two Weeks)</option>
                          <option value="30" @if (old('test_pro_duration', 30) == 30) selected @endif>30 Days (1 Month - Default)</option>
                          <option value="60" @if (old('test_pro_duration') == 60) selected @endif>60 Days (2 Months)</option>
                          <option value="90" @if (old('test_pro_duration') == 90) selected @endif>90 Days (Quarterly)</option>
                          <option value="365" @if (old('test_pro_duration') == 365) selected @endif>365 Days (1 Year)</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label small fw-semibold">Admin Notes (Optional)</label>
                        <input type="text" name="test_notes" value="{{ old('test_notes') }}" class="form-control form-control-sm" placeholder="e.g. Partner review, QA testing">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit -->
						<div class="row mb-3 mt-4">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark px-5 me-2">
                  <i class="bi bi-person-plus-fill me-1"></i> Create Member
                </button>
                <a href="{{ url('panel/admin/members') }}" class="btn btn-outline-secondary px-4">
                  {{ __('admin.cancel') }}
                </a>
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
  document.addEventListener('DOMContentLoaded', function () {
    var switchEl = document.getElementById('grantTestProSwitch');
    var optionsEl = document.getElementById('testProOptions');

    if (switchEl && optionsEl) {
      switchEl.addEventListener('change', function () {
        if (this.checked) {
          optionsEl.style.display = 'block';
        } else {
          optionsEl.style.display = 'none';
        }
      });
    }

    // Toggle password visibility
    var btnToggle = document.getElementById('btnTogglePassword');
    var inputPass = document.getElementById('inputPassword');
    var eyeIcon = document.getElementById('eyeIcon');
    if (btnToggle && inputPass && eyeIcon) {
      btnToggle.addEventListener('click', function () {
        if (inputPass.type === 'password') {
          inputPass.type = 'text';
          eyeIcon.classList.remove('bi-eye');
          eyeIcon.classList.add('bi-eye-slash');
        } else {
          inputPass.type = 'password';
          eyeIcon.classList.remove('bi-eye-slash');
          eyeIcon.classList.add('bi-eye');
        }
      });
    }

    // Generate random secure password
    var btnGen = document.getElementById('btnGeneratePassword');
    if (btnGen && inputPass) {
      btnGen.addEventListener('click', function () {
        var chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
        var generated = '';
        for (var i = 0; i < 12; i++) {
          generated += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        inputPass.value = generated;
        inputPass.type = 'text';
        if (eyeIcon) {
          eyeIcon.classList.remove('bi-eye');
          eyeIcon.classList.add('bi-eye-slash');
        }
      });
    }
  });
</script>
@endsection
