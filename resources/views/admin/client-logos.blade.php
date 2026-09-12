@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.client_logos') }} ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/client-logos/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }}
			</a>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

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

					<div class="table-responsive p-0">
						<table class="table table-hover align-middle">
						 <tbody>

               @if ($data->count() != 0)
                  <tr>
                     <th class="active">ID</th>
                     <th class="active">{{ trans('admin.logo_image') }}</th>
                     <th class="active">{{ trans('admin.name') }}</th>
                     <th class="active">{{ trans('admin.website_url') }}</th>
                     <th class="active">{{ trans('admin.sort_order') }}</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $item)
                   <tr>
                     <td>{{ $item->id }}</td>
                     <td>
                       <div class="p-2 border rounded bg-light d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 50px; background-color: #f8f9fa;">
                         @if ($item->image_url)
                           <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                         @else
                           <span class="text-muted small">No Image</span>
                         @endif
                       </div>
                     </td>
                     <td>
                       <strong>{{ $item->name }}</strong>
                     </td>
                     <td>
                       @if ($item->website_url)
                         <a href="{{ $item->website_url }}" target="_blank" rel="noopener noreferrer" class="text-reset text-truncate d-inline-block" style="max-width: 220px;">
                           {{ $item->website_url }} <i class="bi-box-arrow-up-right small text-muted ms-1"></i>
                         </a>
                       @else
                         <span class="text-muted">-</span>
                       @endif
                     </td>
                     <td>
                       <span class="badge bg-light text-dark border">{{ $item->sort_order }}</span>
                     </td>
                     <td>
                       <form method="POST" action="{{ url('panel/admin/client-logos/toggle-status', $item->id) }}" class="d-inline">
                         @csrf
                         <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to toggle status">
                           <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                             {{ ucfirst($item->status) }}
                           </span>
                         </button>
                       </form>
                     </td>
                     <td>
                       <a href="{{ url('panel/admin/client-logos/edit', $item->id) }}" class="text-reset fs-5 me-2" title="{{ trans('admin.edit') }}">
                         <i class="far fa-edit"></i>
                       </a>

                       <form method="POST" action="{{ url('panel/admin/client-logos/delete', $item->id) }}" accept-charset="UTF-8" class="d-inline-block align-top">
                         @csrf
                         <button class="btn btn-link text-danger e-none fs-5 p-0 actionDelete" type="button" title="{{ trans('admin.delete') }}" data-confirm-title="{{ __('admin.delete_client_logo_confirm') }}">
                           <i class="bi-trash-fill"></i>
                         </button>
                       </form>
                     </td>
                   </tr><!-- /.TR -->
                   @endforeach

								@else
									<div class="text-center p-5 text-muted fw-light">
                    <i class="bi-patch-check display-4 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-light mb-3">{{ trans('admin.no_client_logos') }}</h5>
                    <a href="{{ url('panel/admin/client-logos/add') }}" class="btn btn-sm btn-dark">
                      <i class="bi-plus-lg me-1"></i> {{ trans('admin.add_client_logo') }}
                    </a>
                  </div>
								@endif

								</tbody>
								</table>
							</div><!-- /.table-responsive -->

              @if ($data->hasPages())
                <div class="card-footer bg-transparent border-0 pt-3">
                  {{ $data->links() }}
                </div>
              @endif

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
