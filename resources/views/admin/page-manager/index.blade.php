@extends('admin.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark fw-bold">
            <i class="bi bi-files me-2 text-primary"></i> Centralized Page Manager
          </h1>
          <p class="text-muted small mb-0">Manage all website listing hubs, programmatic landing pages, and guides from one central place.</p>
        </div>
        <div class="col-sm-6 text-end">
          <button type="button" class="btn btn-primary rounded-pill px-4 me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#aiJsonImportModal">
            <i class="bi bi-robot me-1"></i> Paste AI Writer JSON
          </button>
          <a href="{{ url('panel/admin/page-manager/create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Add New Page
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">

      @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> {{ session('success_message') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <!-- Vertical Filter Tabs -->
      @php
        $activeVert = request('vertical', 'all');
      @endphp
      <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header p-2 bg-light border-bottom">
          <ul class="nav nav-pills flex-wrap">
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'all' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager') }}">
                All Pages <span class="badge bg-secondary ms-1">{{ $counts['all'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'use-case' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=use-case') }}">
                Use Cases <span class="badge bg-secondary ms-1">{{ $counts['use-case'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'industry' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=industry') }}">
                Industries <span class="badge bg-secondary ms-1">{{ $counts['industry'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'product' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=product') }}">
                Products <span class="badge bg-secondary ms-1">{{ $counts['product'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'garment' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=garment') }}">
                Garments <span class="badge bg-secondary ms-1">{{ $counts['garment'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'marketplace' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=marketplace') }}">
                Marketplaces <span class="badge bg-secondary ms-1">{{ $counts['marketplace'] ?? 0 }}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ $activeVert === 'platform-ai-ads' ? 'active' : '' }}" href="{{ url('panel/admin/page-manager?vertical=platform-ai-ads') }}">
                Platform Ads <span class="badge bg-secondary ms-1">{{ $counts['platform-ai-ads'] ?? 0 }}</span>
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body">
          <!-- Search & Filter Bar -->
          <form method="GET" action="{{ url('panel/admin/page-manager') }}" class="row g-2 mb-3">
            @if(request('vertical'))
              <input type="hidden" name="vertical" value="{{ request('vertical') }}">
            @endif
            <div class="col-md-5">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by title, slug, keyword..." value="{{ request('q') }}">
              </div>
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
              </select>
            </div>
            <div class="col-md-2">
              <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="solution" {{ request('type') === 'solution' ? 'selected' : '' }}>Solution Detail</option>
                <option value="hub" {{ request('type') === 'hub' ? 'selected' : '' }}>Hub Directory</option>
              </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
              <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
              <a href="{{ url('panel/admin/page-manager') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
          </form>

          <!-- Master Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 50px;">ID</th>
                  <th>Page Title & URL</th>
                  <th>Vertical Silo</th>
                  <th>Type</th>
                  <th>Sections</th>
                  <th>Views</th>
                  <th>Status</th>
                  <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($pages as $page)
                  @php
                    $publicUrl = $page->public_url;
                    $previewUrl = $page->preview_url;
                  @endphp
                  <tr>
                    <td class="text-muted fw-bold">#{{ $page->id }}</td>
                    <td>
                      <div class="fw-bold text-dark">
                        {{ $page->title }}
                      </div>
                      <div class="small">
                        <a href="{{ $publicUrl }}" target="_blank" class="text-decoration-none text-muted">
                          <i class="bi bi-link-45deg"></i> /{{ $page->route_prefix ? $page->route_prefix . '/' : 'guides/' }}{{ $page->slug }}
                        </a>
                      </div>
                    </td>
                    <td>
                      @if (!empty($page->route_prefix))
                        <span class="badge bg-info text-dark rounded-pill">
                          {{ ucwords(str_replace('-', ' ', $page->route_prefix)) }}
                        </span>
                      @else
                        <span class="badge bg-secondary rounded-pill">Guide</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border">
                        {{ ucfirst($page->page_type ?? 'solution') }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-light text-secondary border">
                        <i class="bi bi-layers me-1"></i>{{ $page->sections_count ?? 0 }}
                      </span>
                    </td>
                    <td>
                      <span class="text-muted small"><i class="bi bi-eye me-1"></i>{{ number_format($page->views_count) }}</span>
                    </td>
                    <td>
                      @if ($page->status === 'published')
                        <span class="badge bg-success rounded-pill">Published</span>
                      @elseif ($page->status === 'draft')
                        <span class="badge bg-warning text-dark rounded-pill">Draft</span>
                      @elseif ($page->status === 'scheduled')
                        <span class="badge bg-primary rounded-pill">Scheduled</span>
                      @else
                        <span class="badge bg-secondary rounded-pill">Archived</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a href="{{ url('panel/admin/page-manager/edit/' . $page->id) }}" class="btn btn-outline-primary" title="Edit Page">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-secondary" title="Live Preview">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        <form action="{{ url('panel/admin/page-manager/delete/' . $page->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="button" class="btn btn-outline-danger actionDelete" title="Delete Page" data-confirm-title="Are you sure you want to delete this page?" data-confirm-text="This will permanently delete the page and all its dynamic sections.">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                      <i class="bi bi-inbox display-6 d-block mb-2"></i>
                      No pages found matching the current criteria.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-end mt-3">
            {{ $pages->appends(request()->query())->links() }}
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<!-- Modal: Paste AI JSON (Draft Mode) -->
<div class="modal fade" id="aiJsonImportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark">
          <i class="bi bi-robot text-primary me-2"></i>Create Page From AI Writer JSON
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 py-2 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> Paste the structured JSON generated by your AI Writer. The page and all modular content sections will be created automatically in <strong>Draft Mode</strong> so you can review and refine them.
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">AI Output JSON</label>
          <textarea id="aiJsonInput" rows="14" class="form-control font-monospace small" placeholder='{
  "page": {
    "route_prefix": "use-case",
    "title": "AI Product Photography for D2C Brands",
    "primary_keyword": "AI product photography for D2C brands",
    ...
  },
  "sections": [
    ...
  ]
}'></textarea>
        </div>

        <div id="jsonImportAlert" class="alert alert-danger d-none py-2 small"></div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill" id="btnSubmitAiJson">
          <i class="bi bi-magic me-1"></i> Create Page in Draft Mode
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const submitBtn = document.getElementById('btnSubmitAiJson');
  if (submitBtn) {
    submitBtn.addEventListener('click', function() {
      const rawJson = document.getElementById('aiJsonInput').value.trim();
      const alertBox = document.getElementById('jsonImportAlert');

      alertBox.classList.add('d-none');

      if (!rawJson) {
        alertBox.innerText = 'Please paste the JSON payload first.';
        alertBox.classList.remove('d-none');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating Page...';

      fetch('{{ url("panel/admin/seo-pages/import-json") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          json_content: rawJson,
          redirect_to_page_manager: true
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.redirect_url) {
          window.location.href = data.redirect_url;
        } else {
          alertBox.innerText = data.message || 'Error importing JSON.';
          alertBox.classList.remove('d-none');
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Page in Draft Mode';
        }
      })
      .catch(err => {
        alertBox.innerText = 'An unexpected server error occurred. Please verify your JSON format.';
        alertBox.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Page in Draft Mode';
      });
    });
  }
});
</script>
@endsection
