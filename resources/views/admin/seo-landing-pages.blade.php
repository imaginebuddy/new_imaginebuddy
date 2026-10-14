@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">SEO Landing Pages &amp; Guides ({{ $data->total() }})</span>

			<a href="{{ url('panel/admin/seo-pages/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }} Landing Page
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
          <form method="GET" action="{{ url('panel/admin/seo-pages') }}" class="row g-2 mb-3">
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search by title, keyword, slug..." value="{{ request('q') }}">
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
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
              </select>
            </div>

            @if (request()->hasAny(['q', 'category', 'status']))
            <div class="col-md-2">
              <a href="{{ url('panel/admin/seo-pages') }}" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi-x-lg me-1"></i> Reset Filters
              </a>
            </div>
            @endif
          </form>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 60px;">ID</th>
                  <th scope="col" style="width: 70px;">Cover</th>
                  <th scope="col">Page Title &amp; Target Keyword</th>
                  <th scope="col">Category</th>
                  <th scope="col">Blocks / Items</th>
                  <th scope="col">Status</th>
                  <th scope="col">SEO Health</th>
                  <th scope="col">Updated</th>
                  <th scope="col" style="width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody>

                @if ($data->total() != 0 && $data->count() != 0)
                  @foreach ($data as $page)
                    <tr>
                      <td class="text-muted fw-bold">{{ $page->id }}</td>
                      <td>
                        @if (!empty($page->featured_image))
                          <img src="{{ url('public/uploads/guides/' . $page->featured_image) }}" class="rounded shadow-sm" width="50" height="50" style="object-fit: cover;" />
                        @else
                          <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 50px; height: 50px;">
                            <i class="bi-image fs-5"></i>
                          </div>
                        @endif
                      </td>
                      <td>
                        <a href="{{ url('panel/admin/seo-pages/edit/' . $page->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                          {{ $page->title }}
                        </a>
                        <div class="small text-muted mt-1">
                          <code>/guides/{{ $page->slug }}</code>
                          @if ($page->primary_keyword)
                            <span class="badge bg-light text-dark border ms-2">
                              <i class="bi-key me-1"></i> {{ $page->primary_keyword }}
                            </span>
                          @endif
                        </div>
                      </td>
                      <td>
                        @if ($page->category)
                          <span class="badge bg-secondary-subtle text-secondary border">{{ $page->category->name }}</span>
                        @else
                          <span class="text-muted small">General</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge bg-primary-subtle text-primary border me-1">{{ $page->sections_count }} Sections</span>
                        <span class="badge bg-dark-subtle text-dark border">{{ $page->items_count }} Items</span>
                      </td>
                      <td>
                        @if ($page->status == 'published')
                          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                            <i class="bi-check-circle me-1"></i> Published
                          </span>
                        @elseif ($page->status == 'draft')
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">
                            <i class="bi-pencil me-1"></i> Draft
                          </span>
                        @elseif ($page->status == 'scheduled')
                          <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1">
                            <i class="bi-clock me-1"></i> Scheduled
                          </span>
                        @else
                          <span class="badge bg-light text-muted border rounded-pill px-2 py-1">Archived</span>
                        @endif
                      </td>
                      <td>
                        @php
                          $seoScore = 100;
                          $issues = [];
                          if (empty($page->meta_title)) { $seoScore -= 20; $issues[] = 'Missing meta title'; }
                          if (empty($page->meta_description)) { $seoScore -= 20; $issues[] = 'Missing meta description'; }
                          if (empty($page->featured_image)) { $seoScore -= 15; $issues[] = 'Missing cover image'; }
                          if ($page->items_count == 0) { $seoScore -= 25; $issues[] = 'No attached prompts'; }
                          if (empty($page->primary_keyword)) { $seoScore -= 20; $issues[] = 'No target keyword'; }
                        @endphp
                        @if ($seoScore >= 80)
                          <span class="badge bg-success" title="High SEO readiness"><i class="bi-shield-check me-1"></i> {{ $seoScore }}%</span>
                        @elseif ($seoScore >= 50)
                          <span class="badge bg-warning text-dark" title="{{ implode(', ', $issues) }}"><i class="bi-exclamation-triangle me-1"></i> {{ $seoScore }}%</span>
                        @else
                          <span class="badge bg-danger" title="{{ implode(', ', $issues) }}"><i class="bi-x-circle me-1"></i> {{ $seoScore }}%</span>
                        @endif
                      </td>
                      <td>
                        <small class="text-muted d-block">{{ $page->updated_at ? $page->updated_at->diffForHumans() : '-' }}</small>
                      </td>
                      <td>
                        <div class="d-flex gap-1">
                          <a href="{{ url('panel/admin/seo-pages/edit/' . $page->id) }}" class="btn btn-sm btn-outline-dark" title="Edit Landing Page">
                            <i class="bi-pencil"></i>
                          </a>
                          @if ($page->status == 'published')
                            <a href="{{ url('guides/' . $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Live Page">
                              <i class="bi-box-arrow-up-right"></i>
                            </a>
                          @else
                            <a href="{{ url('guides/preview/' . $page->preview_token) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Preview Draft">
                              <i class="bi-eye"></i>
                            </a>
                          @endif
                          <form method="POST" action="{{ url('panel/admin/seo-pages/delete/' . $page->id) }}" onsubmit="return confirm('Are you sure you want to delete this landing page? Existing prompts will remain safe.');" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Page">
                              <i class="bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                      <i class="bi-file-earmark-richtext display-6 d-block mb-3 text-secondary"></i>
                      <h6>No SEO Landing Pages Found</h6>
                      <p class="small mb-3">Create your first keyword-focused guide using existing prompts and photoshoots.</p>
                      <a href="{{ url('panel/admin/seo-pages/create') }}" class="btn btn-sm btn-dark">
                        <i class="bi-plus-lg me-1"></i> Create Landing Page
                      </a>
                    </td>
                  </tr>
                @endif

              </tbody>
            </table>
          </div>

          @if ($data->hasPages())
            <div class="d-flex justify-content-center mt-3">
              {{ $data->links() }}
            </div>
          @endif

				</div>
			</div>
		</div>

	</div>
</div>
@endsection
