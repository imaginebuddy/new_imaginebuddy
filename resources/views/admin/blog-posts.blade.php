@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Blog Articles ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/blog/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }} Article
			</a>

      <a href="{{ url('panel/admin/blog/categories') }}" class="btn btn-sm btn-outline-secondary float-lg-end me-2 mt-1 mt-lg-0">
        <i class="bi-folder2-open me-1"></i> Manage Categories
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
          <form method="GET" action="{{ url('panel/admin/blog') }}" class="row g-2 mb-3">
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search by title, excerpt..." value="{{ request('q') }}">
                <button class="btn btn-sm btn-dark" type="submit">
                  <i class="bi-search"></i>
                </button>
              </div>
            </div>

            <div class="col-md-3">
              <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-3">
              <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
              </select>
            </div>

            @if (request()->hasAny(['q', 'category', 'status']))
            <div class="col-md-2">
              <a href="{{ url('panel/admin/blog') }}" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi-x-lg me-1"></i> Reset
              </a>
            </div>
            @endif
          </form>

					<div class="table-responsive p-0">
						<table class="table table-hover align-middle">
						 <tbody>

               @if ($data->count() != 0)
                  <tr>
                     <th class="active">Cover</th>
                     <th class="active">Title &amp; Excerpt</th>
                     <th class="active">Category</th>
                     <th class="active">Author</th>
                     <th class="active">Views</th>
                     <th class="active">{{ trans('admin.status') }}</th>
                     <th class="active">{{ trans('admin.date') }}</th>
                     <th class="active">{{ trans('admin.actions') }}</th>
                   </tr>

                 @foreach ($data as $item)
                   <tr>
                     <td style="width: 70px;">
                       <img src="{{ $item->featured_image_url }}" alt="{{ $item->title }}" class="rounded shadow-xs object-fit-cover" style="width: 60px; height: 42px;">
                     </td>
                     <td style="max-width: 380px;">
                       <div>
                         <a href="{{ url('panel/admin/blog/edit', $item->id) }}" class="text-dark fw-bold text-decoration-none">
                           {{ $item->title }}
                         </a>
                         @if ($item->is_featured)
                           <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;"><i class="bi-star-fill"></i> Featured</span>
                         @endif
                         <small class="text-muted d-block text-truncate mt-1" style="max-width: 360px;">
                           {{ $item->excerpt }}
                         </small>
                       </div>
                     </td>
                     <td>
                       @if ($item->category)
                         <span class="badge bg-light text-dark border">{{ $item->category->name }}</span>
                       @else
                         <span class="text-muted small">-</span>
                       @endif
                     </td>
                     <td>
                       <small class="text-muted">{{ $item->user ? $item->user->username : 'Admin' }}</small>
                     </td>
                     <td>
                       <span class="badge bg-light text-secondary border">{{ number_format($item->views_count) }}</span>
                     </td>
                     <td>
                       <form method="POST" action="{{ url('panel/admin/blog/toggle-status', $item->id) }}" class="d-inline">
                         @csrf
                         <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to toggle status">
                           <span class="badge bg-{{ $item->status == 'published' ? 'success' : ($item->status == 'scheduled' ? 'info text-dark' : 'secondary') }}">
                             {{ ucfirst($item->status) }}
                           </span>
                         </button>
                       </form>
                     </td>
                     <td>
                       <small class="text-muted d-block">{{ $item->published_at ? Helper::formatDate($item->published_at) : 'Draft' }}</small>
                       <small class="text-muted" style="font-size: 0.75rem;">{{ $item->reading_time }} min read</small>
                     </td>
                     <td>
                       <div class="d-flex align-items-center gap-1">
                         <!-- Live view or draft preview -->
                         @if ($item->isLive())
                           <a href="{{ url('blog', $item->slug) }}" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm" title="View live article">
                             <i class="bi-box-arrow-up-right"></i>
                           </a>
                         @else
                           <a href="{{ url('blog/preview', $item->preview_token) }}" target="_blank" class="btn btn-outline-info rounded-pill btn-sm" title="Preview draft">
                             <i class="bi-eye"></i>
                           </a>
                         @endif

                         <a href="{{ url('panel/admin/blog/edit', $item->id) }}" class="btn btn-success rounded-pill btn-sm" title="Edit article">
                           <i class="bi-pencil"></i>
                         </a>

                         <form method="POST" action="{{ url('panel/admin/blog/delete', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this article?');" class="d-inline">
                           @csrf
                           <button type="submit" class="btn btn-danger rounded-pill btn-sm" title="Delete article">
                             <i class="bi-trash3"></i>
                           </button>
                         </form>
                       </div>
                     </td>
                   </tr>
                 @endforeach

               @else
                  <div class="alert alert-info border-0 mt-3" role="alert">
                    <i class="bi bi-info-circle me-1"></i> No articles found.
                  </div>
               @endif

						 </tbody>
						</table>
					</div><!-- /.table-responsive -->

				 </div><!-- card-body -->
 			</div><!-- card  -->

      @if ($data->hasPages())
        <div class="mt-3">
          {{ $data->appends(request()->query())->onEachSide(1)->links() }}
        </div>
      @endif

 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
