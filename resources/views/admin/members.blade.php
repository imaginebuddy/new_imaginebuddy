@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.members') }} ({{$data->total()}})</span>

      <a href="{{ url('panel/admin/members/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
        <i class="bi-plus-lg me-1"></i> {{ __('misc.add_new') }}
      </a>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @if (session('info_message'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <i class="bi-exclamation-triangle me-1"></i>	{{ session('info_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="d-inline-block mb-2 w-100">

          @if ($data->count() != 0)
            <!-- form -->
            <form role="search" autocomplete="off" action="{{ url('panel/admin/members') }}" method="get" class="position-relative">
							<i class="bi bi-search btn-search bar-search"></i>
             <input type="text" name="q" class="form-control ps-5" placeholder="{{ __('misc.search') }}">
          </form><!-- form -->
            @endif
          </div>

					<div class="table-responsive p-0">
						<table class="table table-hover">
						 <tbody>

               @if ($data->total() !=  0 && $data->count() != 0)
                  <tr>
                     <th class="active">ID</th>
                     <th class="active">{{ trans('admin.name') }} / {{ trans('auth.username') }}</th>
                     <th class="active">{{ trans('auth.email') }}</th>
                     <th class="active">{{ trans('misc.country') }}</th>
                     <th class="active">{{ trans('admin.date') }}</th>
                     <th class="active">{{ trans('admin.role') }}</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $user)
                   @php
                     $activeTestSub = $user->activeTestSubscription();
                     $activePaidSub = $user->activePaidSubscription();
                   @endphp
                   <tr>
                     <td>{{ $user->id }}</td>
                     <td>
                       <div class="d-flex align-items-center">
                         <a href="{{ url($user->username) }}" target="_blank">
                           <img src="{{Storage::url(config('path.avatar').$user->avatar)}}" width="40" height="40" class="rounded-circle me-2" />
                         </a>
                         <div>
                           <a href="{{ url($user->username) }}" target="_blank" class="text-reset text-decoration-none fw-medium d-block lh-sm">
                             {{ $user->name ?: $user->username }}
                           </a>
                           @if ($user->name && $user->name !== $user->username)
                             <small class="text-muted d-block">&#64;{{ $user->username }}</small>
                           @endif

                           @if ($activeTestSub)
                             <span class="badge rounded-pill mt-1" style="background-color: #f3e8ff; color: #7e22ce; font-size: 0.72rem; border: 1px solid #d8b4fe;" title="Test Pro Plan active until {{ Helper::formatDate($activeTestSub->ends_at) }}">
                               <i class="bi bi-star-fill me-1"></i>Pro (Test) &bull; {{ $activeTestSub->days_remaining }}
                             </span>
                           @elseif ($activePaidSub)
                             <span class="badge bg-success rounded-pill mt-1" style="font-size: 0.72rem;" title="Paid Pro Plan active">
                               <i class="bi bi-check-circle-fill me-1"></i>Pro Paid
                             </span>
                           @endif
                         </div>
                       </div>
                     </td>
                     <td>{{ $user->email }}</td>
                     <td>{{ $user->countryRelation ? $user->countryRelation->country_name : ($user->country() ? $user->country()->country_name : trans('misc.not_available')) }}</td>
                     <td>{{ Helper::formatDate($user->date) }}</td>                     
                     <td>
                      @foreach (RolesAndPermissions::all() as $role)

                      @if ($user->role == $role->id)
                        <span class="badge bg-success">{{ $role->name }}</span>
                      @endif

                     @endforeach
                     
                     @if ($user->role == '0')
                     <span class="badge bg-secondary">{{ trans('admin.normal') }}</span>
                     @endif
                     </td>

                    @php if ($user->status == 'pending') {
                  $mode    = 'info';
                 $_status = trans('admin.pending');
                           } elseif ($user->status == 'active') {
                  $mode = 'success';
                 $_status = trans('admin.active');
                           } else {
                 $mode = 'warning';
                 $_status = trans('admin.suspended');
                         }
                       @endphp

                     <td><span class="badge bg-{{$mode}}">{{ $_status }}</span></td>
                     <td>

                      <!-- View User Details Modal Trigger -->
                      <button type="button" class="btn btn-link text-primary e-none fs-5 p-0 me-2 btnViewMember" 
                        data-id="{{ $user->id }}" 
                        title="{{ __('admin.view') }}">
                        <i class="bi-eye"></i>
                      </button>

                    @if ($user->id <> auth()->user()->id && $user->id <> 1)

                      <!-- Pro Test Modal Trigger -->
                      <button type="button" class="btn btn-link text-warning e-none fs-5 p-0 me-2 btnManageProTest" 
                        data-id="{{ $user->id }}" 
                        data-username="{{ $user->username }}" 
                        data-name="{{ $user->name ?: $user->username }}"
                        data-has-test="{{ $activeTestSub ? 'yes' : 'no' }}"
                        data-ends="{{ $activeTestSub ? Helper::formatDate($activeTestSub->ends_at) : '' }}"
                        data-remaining="{{ $activeTestSub ? $activeTestSub->days_remaining : '' }}"
                        data-notes="{{ $activeTestSub ? $activeTestSub->test_notes : '' }}"
                        data-grant-url="{{ url('panel/admin/members/' . $user->id . '/grant-test-pro') }}"
                        data-revoke-url="{{ url('panel/admin/members/' . $user->id . '/revoke-test-pro') }}"
                        title="{{ $activeTestSub ? 'Manage / Revoke Pro Test Access' : 'Grant Pro Test Access' }}">
                        <i class="bi bi-star{{ $activeTestSub ? '-fill' : '' }}"></i>
                      </button>

                  <a href="{{ url('panel/admin/members/edit', $user->id) }}" class="text-reset fs-5 me-2" title="{{ __('admin.edit') }}">
                         <i class="far fa-edit"></i>
                       </a>

                  {!! Form::open([
                 'method' => 'POST',
                 'route' => ['user.destroy', $user->id],
                 'id' => 'form'.$user->id,
                 'class' => 'd-inline-block align-top'
               ]) !!}
               {!! Form::button('<i class="bi-trash-fill"></i>', ['data-url' => $user->id, 'class' => 'btn btn-link text-danger e-none fs-5 p-0 actionDelete', 'title' => __('misc.delete')]) !!}
           {!! Form::close() !!}

        @else
         ------------
                         @endif
                       </td>

                   </tr><!-- /.TR -->
                   @endforeach

									@else
										<h5 class="text-center p-5 text-muted fw-light m-0">{{ trans('misc.no_results_found') }}

                      @if (isset($query))
                        <div class="d-block w-100 mt-2">
                          <a href="{{url('panel/admin/members')}}"><i class="bi-arrow-left me-1"></i> {{ trans('auth.back') }}</a>
                        </div>
                      @endif
                    </h5>
									@endif

								</tbody>
								</table>
							</div><!-- /.box-body -->

				 </div><!-- card-body -->
 			</div><!-- card  -->

			{{ $data->appends(['q' => $query])->onEachSide(0)->links() }}
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->

<!-- Pro Test Access Modal -->
<div class="modal fade" id="modalManageProTest" tabindex="-1" aria-labelledby="modalManageProTestLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalManageProTestLabel">
          <i class="bi bi-star-fill text-warning me-1"></i> Pro Plan Test Access
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
          <i class="bi bi-person-circle fs-3 text-muted me-2"></i>
          <div>
            <div class="fw-bold text-dark" id="modalTargetName"></div>
            <small class="text-muted" id="modalTargetUsername"></small>
          </div>
        </div>

        <!-- Section: Active Test Access -->
        <div id="sectionActiveTest" style="display: none;">
          <div class="alert alert-info border-0 d-flex align-items-center mb-3">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>
              <strong>Active Test Access</strong><br>
              <small>Expires on <span id="modalCurrentEnds" class="fw-semibold"></span> (<span id="modalCurrentRemaining"></span>)</small>
              <small class="d-block text-muted" id="modalCurrentNotes"></small>
            </div>
          </div>

          <!-- Extend Duration Form -->
          <form id="formExtendTestPro" method="POST" action="">
            @csrf
            <label class="form-label small fw-semibold">Extend Duration</label>
            <div class="input-group mb-2">
              <select name="duration" class="form-select form-select-sm">
                <option value="7">+7 Days</option>
                <option value="14">+14 Days</option>
                <option value="30" selected>+30 Days (1 Month)</option>
                <option value="60">+60 Days</option>
                <option value="90">+90 Days</option>
                <option value="365">+365 Days (1 Year)</option>
              </select>
              <button type="submit" class="btn btn-dark btn-sm px-3">
                <i class="bi bi-calendar-plus me-1"></i> Extend
              </button>
            </div>
            <input type="text" name="notes" class="form-control form-control-sm mb-3" placeholder="Optional note for extension">
          </form>

          <hr>

          <!-- Revoke Access Form -->
          <form id="formRevokeTestPro" method="POST" action="">
            @csrf
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="fw-semibold text-danger small d-block">Revoke Test Access</span>
                <small class="text-muted">Clears test quota and ends Pro features immediately.</small>
              </div>
              <button type="button" class="btn btn-outline-danger btn-sm px-3 actionRevokeTest">
                <i class="bi bi-x-circle me-1"></i> Revoke
              </button>
            </div>
          </form>
        </div>

        <!-- Section: Grant New Test Access -->
        <div id="sectionGrantTest" style="display: none;">
          <form id="formGrantTestPro" method="POST" action="">
            @csrf
            <p class="small text-muted mb-3">
              Grant test access to the <strong>Pro Photography Plan</strong>. This will enable full prompt copying, daily quotas, and high-res downloads without generating invoices or charging payment gateways.
            </p>

            <div class="mb-3">
              <label class="form-label small fw-semibold">Duration <span class="text-danger">*</span></label>
              <select name="duration" class="form-select form-select-sm" required>
                <option value="7">7 Days (Quick Test)</option>
                <option value="14">14 Days (Two Weeks)</option>
                <option value="30" selected>30 Days (1 Month - Recommended)</option>
                <option value="60">60 Days (2 Months)</option>
                <option value="90">90 Days (Quarterly)</option>
                <option value="365">365 Days (1 Year)</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-semibold">Admin Notes (Optional)</label>
              <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Partner review, QA testing">
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2">
              <i class="bi bi-star-fill text-warning me-1"></i> Grant Pro Test Access
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Member Detail Modal -->
<div class="modal fade" id="modalMemberDetail" tabindex="-1" aria-labelledby="modalMemberDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow" id="modalMemberDetailContent">
      <!-- Loaded via AJAX -->
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
  $(document).on('click', '.btnViewMember', function(e) {
    e.preventDefault();
    var userId = $(this).data('id');
    var modalEl = new bootstrap.Modal(document.getElementById('modalMemberDetail'));

    $('#modalMemberDetailContent').html(
      '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
    );
    modalEl.show();

    $.ajax({
      url: '{{ url("panel/admin/members") }}/' + userId + '/detail',
      type: 'GET',
      dataType: 'html',
      success: function(response) {
        $('#modalMemberDetailContent').html(response);
      },
      error: function() {
        $('#modalMemberDetailContent').html('<div class="p-4 text-center text-danger"><i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i> Failed to load user details. Please try again.</div>');
      }
    });
  });
  $(document).on('click', '.btnManageProTest', function(e) {
    e.preventDefault();
    var btn = $(this);
    var name = btn.data('name');
    var username = btn.data('username');
    var hasTest = btn.data('has-test');
    var ends = btn.data('ends');
    var remaining = btn.data('remaining');
    var notes = btn.data('notes');
    var grantUrl = btn.data('grant-url');
    var revokeUrl = btn.data('revoke-url');

    $('#modalTargetName').text(name);
    $('#modalTargetUsername').text('@' + username);

    if (hasTest === 'yes') {
      $('#sectionActiveTest').show();
      $('#sectionGrantTest').hide();
      $('#modalCurrentEnds').text(ends);
      $('#modalCurrentRemaining').text(remaining);
      if (notes) {
        $('#modalCurrentNotes').text('Note: ' + notes).show();
      } else {
        $('#modalCurrentNotes').hide();
      }
      $('#formExtendTestPro').attr('action', grantUrl);
      $('#formRevokeTestPro').attr('action', revokeUrl);
    } else {
      $('#sectionActiveTest').hide();
      $('#sectionGrantTest').show();
      $('#formGrantTestPro').attr('action', grantUrl);
    }

    var modal = new bootstrap.Modal(document.getElementById('modalManageProTest'));
    modal.show();
  });

  $(document).on('click', '.actionRevokeTest', function(e) {
    e.preventDefault();
    var form = $(this).closest('form');
    $(this).blur();

    swal({
      title: "{{ trans('misc.delete_confirm') }}",
      text: "Are you sure you want to revoke Pro Plan test access for this user?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, revoke it!",
      cancelButtonText: "{{ trans('misc.cancel_confirm') }}",
      closeOnConfirm: false,
      showLoaderOnConfirm: true
    }, function(isConfirm) {
      if (isConfirm) {
        form.submit();
      }
    });
  });
</script>
@endsection
