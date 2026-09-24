@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.faqs') }} ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/faqs/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }}
			</a>

      @if ($faqSeo)
      <a href="{{ url('panel/admin/settings/seo/edit', $faqSeo->id) }}" class="btn btn-sm btn-outline-primary float-lg-end me-2 mt-1 mt-lg-0">
        <i class="bi-sliders me-1"></i> FAQ SEO Settings
      </a>
      @endif
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
              <form method="GET" action="{{ url('panel/admin/faqs') }}">
                <div class="input-group">
                  <input type="text" name="q" class="form-control form-control-sm" placeholder="Search questions or categories..." value="{{ request('q') }}">
                  <button class="btn btn-sm btn-dark" type="submit">
                    <i class="bi-search"></i>
                  </button>
                  @if (request('q'))
                  <a href="{{ url('panel/admin/faqs') }}" class="btn btn-sm btn-outline-secondary" title="Clear search">
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
                     <th class="active">{{ trans('admin.question') }}</th>
                     <th class="active">Category</th>
                     <th class="active">{{ trans('admin.sort_order') }}</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $item)
                   <tr>
                     <td>{{ $item->id }}</td>
                     <td style="max-width: 480px;">
                       <div>
                         <strong class="d-block text-dark">{{ $item->question }}</strong>
                         <small class="text-muted d-block text-truncate" style="max-width: 440px;">
                           {{ Str::limit(strip_tags($item->answer), 100) }}
                         </small>
                       </div>
                     </td>
                     <td>
                       @if ($item->category)
                         <span class="badge bg-light text-dark border">{{ $item->category }}</span>
                       @else
                         <span class="text-muted">-</span>
                       @endif
                     </td>
                     <td>
                       <span class="badge bg-light text-dark border">{{ $item->sort_order }}</span>
                     </td>
                     <td>
                       <form method="POST" action="{{ url('panel/admin/faqs/toggle-status', $item->id) }}" class="d-inline">
                         @csrf
                         <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to toggle status">
                           <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                             {{ $item->status == 'active' ? 'Active / Published' : 'Draft / Inactive' }}
                           </span>
                         </button>
                       </form>
                     </td>
                     <td>
                       <a href="{{ url('panel/admin/faqs/edit', $item->id) }}" class="text-reset fs-5 me-2" title="{{ trans('admin.edit') }}">
                         <i class="far fa-edit"></i>
                       </a>

                       <form method="POST" action="{{ url('panel/admin/faqs/delete', $item->id) }}" accept-charset="UTF-8" class="d-inline-block align-top">
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
                    <i class="bi-patch-question display-4 d-block mb-3 opacity-50"></i>
                    {{ trans('admin.no_faqs') }}
                  </div>
								@endif

						 </tbody>
						</table>
					</div><!-- /.table-responsive -->

				 </div><!-- card-body -->
 			</div><!-- card  -->

      @if ($data->lastPage() > 1)
        <div class="d-flex justify-content-center mt-3">
          {{ $data->appends(['q' => request('q')])->onEachSide(0)->links() }}
        </div>
      @endif

 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
