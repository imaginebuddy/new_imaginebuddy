@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/blog') }}">Blog</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Categories ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/blog/categories/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }} Category
			</a>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi-x-lg"></i>
        </button>
      </div>
      @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <!-- Search & Filter bar -->
          <div class="row mb-3">
            <div class="col-md-6 col-lg-4">
              <form method="GET" action="{{ url('panel/admin/blog/categories') }}">
                <div class="input-group">
                  <input type="text" name="q" class="form-control form-control-sm" placeholder="Search categories..." value="{{ request('q') }}">
                  <button class="btn btn-sm btn-dark" type="submit">
                    <i class="bi-search"></i>
                  </button>
                  @if (request('q'))
                  <a href="{{ url('panel/admin/blog/categories') }}" class="btn btn-sm btn-outline-secondary" title="Clear search">
                    <i class="bi-x-lg"></i>
                  </a>
                  @endif
                </div>
              </form>
            </div>
          </div>

					<div class="table-responsive p-0">
						<table class="table table-hover align-middle">
						 <tbody>

               @if ($data->count() != 0)
                  <tr>
                     <th class="active">ID</th>
                     <th class="active">{{ trans('admin.name') }}</th>
                     <th class="active">{{ trans('admin.slug') }}</th>
                     <th class="active">Articles</th>
                     <th class="active">{{ trans('admin.sort_order') }}</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $item)
                   <tr>
                     <td>{{ $item->id }}</td>
                     <td>
                       <strong class="d-block text-dark">{{ $item->name }}</strong>
                       @if ($item->description)
                       <small class="text-muted d-block text-truncate" style="max-width: 320px;">
                         {{ $item->description }}
                       </small>
                       @endif
                     </td>
                     <td>
                       <code>{{ $item->slug }}</code>
                     </td>
                     <td>
                       <span class="badge bg-secondary rounded-pill">{{ $item->posts_count }}</span>
                     </td>
                     <td>
                       <span class="badge bg-light text-dark border">{{ $item->sort_order }}</span>
                     </td>
                     <td>
                       <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                         {{ $item->status == 'active' ? 'Active' : 'Inactive' }}
                       </span>
                     </td>
                     <td>
                       <div class="d-flex align-items-center gap-2">
                         <a href="{{ url('panel/admin/blog/categories/edit', $item->id) }}" class="btn btn-success rounded-pill btn-sm">
                           <i class="bi-pencil"></i>
                         </a>

                         <form method="POST" action="{{ url('panel/admin/blog/categories/delete', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this category? Associated articles will remain safe.');" class="d-inline">
                           @csrf
                           <button type="submit" class="btn btn-danger rounded-pill btn-sm" title="Delete">
                             <i class="bi-trash3"></i>
                           </button>
                         </form>
                       </div>
                     </td>
                   </tr>
                 @endforeach

               @else
                  <div class="alert alert-info border-0 mt-3" role="alert">
                    <i class="bi bi-info-circle me-1"></i> No blog categories found.
                  </div>
               @endif

						 </tbody>
						</table>
					</div><!-- /.table-responsive -->

				 </div><!-- card-body -->
 			</div><!-- card  -->

      @if ($data->hasPages())
        <div class="mt-3">
          {{ $data->onEachSide(1)->links() }}
        </div>
      @endif

 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
