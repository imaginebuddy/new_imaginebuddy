@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Photoshoots ({{ $data->total() }})</span>

      <a href="{{ url('panel/admin/photoshoots/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
        <i class="bi-plus-lg"></i> Add New Photoshoot
      </a>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="d-lg-flex justify-content-lg-between align-items-center mb-3 w-100">
            <div>
              <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-collection-play me-2 text-primary"></i>All Photoshoots</h5>
              <p class="text-muted small mb-0">Manage prompt photoshoots, edit titles, and add/remove associated prompts.</p>
            </div>

            <!-- Search Form -->
            <form class="mt-lg-0 mt-2 position-relative" role="search" autocomplete="off" action="{{ url('panel/admin/photoshoots') }}" method="get">
              <i class="bi bi-search btn-search bar-search"></i>
              <input type="text" name="q" class="form-control ps-5 w-auto" value="{{ request()->get('q') }}" placeholder="Search photoshoot title...">
            </form>
          </div>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 70px;">ID</th>
                  <th scope="col">Photoshoot Title</th>
                  <th scope="col">Category</th>
                  <th scope="col">Creator</th>
                  <th scope="col" style="width: 140px;">Prompts Count</th>
                  <th scope="col">Created Date</th>
                  <th scope="col" style="width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody>

                @if ($data->total() != 0 && $data->count() != 0)
                  @foreach ($data as $photoshoot)
                    <tr>
                      <td class="fw-bold text-muted">{{ $photoshoot->id }}</td>
                      <td>
                        <strong class="d-block text-dark">{{ $photoshoot->title }}</strong>
                        <small class="text-muted"><code>{{ $photoshoot->slug }}</code></small>
                      </td>
                      <td>
                        @if ($photoshoot->category)
                          <span class="badge bg-light text-dark border">{{ $photoshoot->category->name }}</span>
                        @else
                          <span class="text-muted small">N/A</span>
                        @endif
                      </td>
                      <td>
                        <span class="fw-medium text-secondary">{{ $photoshoot->user ? $photoshoot->user->username : 'System / Admin' }}</span>
                      </td>
                      <td>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                          <i class="bi bi-images me-1"></i> {{ $photoshoot->prompts_count }} Prompts
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">{{ Helper::formatDate($photoshoot->created_at) }}</small>
                      </td>
                      <td class="align-middle">
                        <div class="d-flex align-items-center gap-2">
                          <a href="{{ url('panel/admin/photoshoots/edit', $photoshoot->id) }}" class="text-reset fs-5" title="Edit Photoshoot & Manage Prompts">
                            <i class="far fa-edit"></i>
                          </a>

                          <form method="POST" action="{{ url('panel/admin/photoshoots/delete', $photoshoot->id) }}" class="d-inline-block m-0">
                            @csrf
                            <button class="btn btn-link text-danger e-none fs-5 p-0 border-0 actionDelete" type="button" title="Delete Photoshoot">
                              <i class="bi-trash-fill"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="7" class="text-center p-5 text-muted fw-light">
                      No photoshoots found.
                      @if (request()->get('q'))
                        <div class="mt-2">
                          <a href="{{ url('panel/admin/photoshoots') }}"><i class="bi-arrow-left me-1"></i> Back to all photoshoots</a>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endif

              </tbody>
            </table>
          </div>

        </div>
      </div>

      <div class="mt-3">
        {{ $data->appends(['q' => $query])->onEachSide(0)->links() }}
      </div>

    </div>

  </div>
</div>
@endsection
