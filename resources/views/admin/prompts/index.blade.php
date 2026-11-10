@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Prompt Collections ({{ $collections->total() }})</span>

    <div class="float-lg-end mt-1 mt-lg-0 d-flex gap-2">
      <a href="{{ route('admin.prompts.settings') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-gear me-1"></i> Hub Page Settings
      </a>
      <a href="{{ route('admin.prompts.create') }}" class="btn btn-sm btn-dark">
        <i class="bi-plus-lg me-1"></i> Create Collection
      </a>
    </div>
  </h5>

<div class="content">
	<div class="row">
		<div class="col-lg-12">

			@if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="d-lg-flex justify-content-lg-between align-items-center mb-3 w-100 gap-3">
            <div>
              <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-folder2-open me-2 text-primary"></i>All Prompt Collections</h5>
              <p class="text-muted small mb-0">Programmatic SEO prompt landing pages (e.g. <code>/prompts/saree-photography-prompts</code>) and hub directory.</p>
            </div>

            <!-- Search & Filter Form -->
            <form class="mt-lg-0 mt-2 d-flex gap-2 flex-wrap" role="search" autocomplete="off" action="{{ route('admin.prompts.index') }}" method="get">
              <select name="category" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>

              <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
              </select>

              <div class="position-relative">
                <input type="text" name="q" class="form-control form-control-sm ps-4" value="{{ request('q') }}" placeholder="Search collection...">
                <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 7px; font-size: 12px;"></i>
              </div>
              <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
          </div>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 60px;">Cover</th>
                  <th scope="col">Title &amp; Slug</th>
                  <th scope="col">Category</th>
                  <th scope="col">AI Engine</th>
                  <th scope="col" style="width: 130px;">Attached Prompts</th>
                  <th scope="col" style="width: 100px;">Status</th>
                  <th scope="col" style="width: 130px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($collections as $col)
                  <tr>
                    <td>
                      <img src="{{ $col->cover_image_url }}" alt="{{ $col->title }}" class="rounded shadow-xs border" style="width: 48px; height: 48px; object-fit: cover;">
                    </td>
                    <td>
                      <a href="{{ route('admin.prompts.edit', $col->id) }}" class="fw-bold text-dark text-decoration-none">
                        {{ $col->title }}
                      </a>
                      @if ($col->is_featured)
                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Featured</span>
                      @endif
                      <small class="text-muted d-block">
                        <code>/prompts/{{ $col->slug }}</code>
                        <a href="{{ url('prompts/' . $col->slug) }}" target="_blank" class="text-muted ms-1" title="View live page">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                      </small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border">{{ $col->category ? $col->category->name : 'Uncategorized' }}</span>
                    </td>
                    <td>
                      <small class="text-muted">{{ $col->ai_engine ?: 'Midjourney & Flux' }}</small>
                    </td>
                    <td>
                      <span class="badge bg-primary rounded-pill px-3 py-1">
                        <i class="bi bi-images me-1"></i> {{ $col->items_count }} {{ str_plural('Prompt', $col->items_count) }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-{{ $col->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($col->status) }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.prompts.edit', $col->id) }}" class="btn btn-sm btn-outline-primary" title="Edit &amp; Add Prompts">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.prompts.destroy', $col->id) }}" method="POST" onsubmit="return confirm('Delete this prompt collection? Linked prompts will not be deleted.');">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Collection">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center p-5 text-muted">
                      <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                      No prompt collections found. 
                      <a href="{{ route('admin.prompts.create') }}" class="d-block mt-2 fw-bold text-primary">Create your first collection &rarr;</a>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $collections->links() }}
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
