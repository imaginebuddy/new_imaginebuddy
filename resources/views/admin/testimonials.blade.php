@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.testimonials') }} ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/testimonials/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
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
                     <th class="active">{{ trans('admin.name') }}</th>
                     <th class="active">{{ trans('admin.designation') }} / {{ trans('admin.company') }}</th>
                     <th class="active">{{ trans('admin.rating') }}</th>
                     <th class="active">{{ trans('admin.sort_order') }}</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $item)
                   <tr>
                     <td>{{ $item->id }}</td>
                     <td>
                       <div class="d-flex align-items-center">
                         @if ($item->image_url)
                           <img src="{{ $item->image_url }}" width="40" height="40" class="rounded-circle me-2 border" style="object-fit: cover;" alt="{{ $item->name }}">
                         @else
                           <div class="rounded-circle me-2 bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                             {{ strtoupper(substr($item->name, 0, 1)) }}
                           </div>
                         @endif
                         <div>
                           <strong>{{ $item->name }}</strong>
                           <small class="d-block text-muted text-truncate" style="max-width: 260px;">"{{ Str::limit($item->content, 60) }}"</small>
                         </div>
                       </div>
                     </td>
                     <td>
                       @if ($item->designation || $item->company)
                         <span>{{ $item->designation }}</span>
                         @if ($item->designation && $item->company) <span class="text-muted">@</span> @endif
                         <span class="text-muted fw-bold">{{ $item->company }}</span>
                       @else
                         <span class="text-muted">-</span>
                       @endif
                     </td>
                     <td>
                       @if ($item->rating)
                         <div class="text-warning text-nowrap">
                           @for ($i = 1; $i <= 5; $i++)
                             @if ($i <= $item->rating)
                               <i class="bi-star-fill"></i>
                             @else
                               <i class="bi-star text-muted opacity-25"></i>
                             @endif
                           @endfor
                           <small class="text-muted ms-1">({{ $item->rating }}/5)</small>
                         </div>
                       @else
                         <span class="text-muted">-</span>
                       @endif
                     </td>
                     <td>
                       <span class="badge bg-light text-dark border">{{ $item->sort_order }}</span>
                     </td>
                     <td>
                       <form method="POST" action="{{ url('panel/admin/testimonials/toggle-status', $item->id) }}" class="d-inline">
                         @csrf
                         <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to toggle status">
                           <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                             {{ ucfirst($item->status) }}
                           </span>
                         </button>
                       </form>
                     </td>
                     <td>
                       <a href="{{ url('panel/admin/testimonials/edit', $item->id) }}" class="text-reset fs-5 me-2" title="{{ trans('admin.edit') }}">
                         <i class="far fa-edit"></i>
                       </a>

                       <form method="POST" action="{{ url('panel/admin/testimonials/delete', $item->id) }}" accept-charset="UTF-8" class="d-inline-block align-top">
                         @csrf
                         <button class="btn btn-link text-danger e-none fs-5 p-0 actionDelete" type="button" title="{{ trans('admin.delete') }}">
                           <i class="bi-trash-fill"></i>
                         </button>
                       </form>
                     </td>
                   </tr><!-- /.TR -->
                   @endforeach

								@else
									<div class="text-center p-5 text-muted fw-light">
                    <i class="bi-chat-square-quote display-4 d-block mb-3 opacity-50"></i>
                    <h5 class="fw-light mb-3">{{ trans('admin.no_testimonials') }}</h5>
                    <a href="{{ url('panel/admin/testimonials/add') }}" class="btn btn-sm btn-dark">
                      <i class="bi-plus-lg me-1"></i> {{ trans('admin.add_testimonial') }}
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
