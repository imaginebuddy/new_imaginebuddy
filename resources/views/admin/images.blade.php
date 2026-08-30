@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.images') }} ({{$data->total()}})</span>
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

      @if (session('info_message'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('info_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="d-lg-flex justify-content-lg-between align-items-center mb-3 w-100">

          @if ($data->count() != 0)
						@if (! request()->get('q'))
							<select class="form-select d-inline-block w-auto filter">
	              <option @if ($sort == '') selected="selected" @endif value="{{ url()->current() }}">{{ trans('admin.sort_id') }}</option>
	              <option @if ($sort == 'pending') selected="selected" @endif value="{{ url()->current() }}?sort=pending">{{ trans('admin.pending') }}</option>
	              <option @if ($sort == 'featured') selected="selected" @endif value="{{ url()->current() }}?sort=featured">Featured Prompts</option>
	              <option @if ($sort == 'title') selected="selected" @endif value="{{ url()->current() }}?sort=title">{{ trans('admin.sort_title') }}</option>
	              <option @if ($sort == 'likes') selected="selected" @endif value="{{ url()->current() }}?sort=likes">{{ trans('admin.sort_likes') }}</option>
	              <option @if ($sort == 'downloads') selected="selected" @endif value="{{ url()->current() }}?sort=downloads">{{ trans('admin.sort_downloads') }}</option>
	        			</select>
						@endif

						<!-- form -->
            <form class="mt-lg-0 mt-2 position-relative" role="search" autocomplete="off" action="{{ url('panel/admin/images') }}" method="get">
							<i class="bi bi-search btn-search bar-search"></i>
              <input type="text" name="q" class="form-control ps-5 w-auto" value="{{ request()->get('q') }}" placeholder="{{ __('misc.search') }}">
            </form><!-- form -->
					@endif
          </div>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 60px;">ID</th>
                  <th scope="col" style="width: 70px;">{{ trans('misc.thumbnail') }}</th>
                  <th scope="col">{{ trans('admin.title') }}</th>
                  <th scope="col">{{ trans('misc.uploaded_by') }}</th>
                  <th scope="col" style="width: 90px;">{{ trans('admin.type') }}</th>
                  <th scope="col" style="width: 110px;">Featured</th>
                  <th scope="col" style="width: 80px;">{{ trans('misc.likes') }}</th>
                  <th scope="col" style="width: 90px;">{{ trans('misc.downloads') }}</th>
                  <th scope="col" style="width: 120px;">{{ trans('admin.date') }}</th>
                  <th scope="col" style="width: 100px;">{{ trans('admin.status') }}</th>
                  <th scope="col" style="width: 100px;">{{ trans('admin.actions') }}</th>
                </tr>
              </thead>
              <tbody>

                @if ($data->total() != 0 && $data->count() != 0)
                  @foreach ($data as $image)
                    <tr>
                      <td class="fw-bold text-muted">{{ $image->id }}</td>
                      <td><img src="{{ Storage::url(config('path.thumbnail') . $image->thumbnail) }}" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" /></td>
                      <td>
                        <a href="{{ url('prompt', $image->slug) }}" title="{{ $image->title }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                          {{ str_limit($image->title, 25, '...') }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                        </a>
                      </td>
                      <td><span class="text-secondary small">{{ $image->user->username ?? 'N/A' }}</span></td>
                      <td>
                        <span class="badge bg-{{ $image->item_for_sale == 'sale' ? 'warning' : 'secondary' }}">
                          {{ $image->item_for_sale == 'sale' ? trans('misc.sale') : trans('misc.free') }}
                        </span>
                      </td>
                      <td>
                        @if ($image->featured == 'yes')
                          <span class="badge rounded-pill bg-primary" title="Featured">
                            <i class="bi bi-star-fill text-warning me-1"></i> Featured
                          </span>
                        @else
                          <span class="badge rounded-pill bg-light text-muted border">No</span>
                        @endif
                      </td>
                      <td>{{ $image->likes()->count() }}</td>
                      <td>{{ $image->downloads()->count() }}</td>
                      <td><small class="text-muted">{{ Helper::formatDate($image->date) }}</small></td>
                      <td>
                        <span class="badge rounded-pill bg-{{ $image->status == 'active' ? 'success' : 'warning' }}">
                          {{ $image->status == 'active' ? trans('admin.active') : trans('admin.pending') }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <a href="{{ url('panel/admin/images', $image->id) }}" class="text-reset fs-5" title="Edit Prompt">
                            <i class="far fa-edit"></i>
                          </a>

                          {!! Form::open(['method' => 'POST', 'url' => 'panel/admin/images/delete', 'class' => 'd-inline-block m-0']) !!}
                            {!! Form::hidden('id', $image->id); !!}
                            {!! Form::button('<i class="bi-trash-fill"></i>', ['data-url' => $image->id, 'class' => 'btn btn-link text-danger e-none fs-5 p-0 actionDelete', 'title' => 'Delete Prompt']) !!}
                          {!! Form::close() !!}
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="11" class="text-center p-5 text-muted fw-light">
                      {{ trans('misc.no_results_found') }}
                      @if (isset($query) || isset($sort))
                        <div class="d-block w-100 mt-2">
                          <a href="{{ url('panel/admin/images') }}"><i class="bi-arrow-left me-1"></i> {{ trans('auth.back') }}</a>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endif

              </tbody>
            </table>
          </div><!-- /.table responsive -->
        </div><!-- card-body -->
      </div><!-- card  -->

      <div class="mt-3">
        {{ $data->appends(['q' => $query, 'sort' => $sort])->onEachSide(0)->links() }}
      </div>
    </div><!-- col-lg-12 -->

  </div><!-- end row -->
</div><!-- end content -->
@endsection
