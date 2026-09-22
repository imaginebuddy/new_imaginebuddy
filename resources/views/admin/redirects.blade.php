@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">URL Redirects ({{ $data->total() }})</span>

      <a href="{{ url('panel/admin/redirects/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
        <i class="bi-plus-lg"></i> Add New Redirect
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

      @if (session('error_message'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      @endif

      <!-- Quick Summary Stats -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Redirects</small>
            <h4 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($stats['total']) }}</h4>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Active</small>
            <h4 class="fw-bold mb-0 mt-1 text-success">{{ number_format($stats['active']) }}</h4>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Inactive</small>
            <h4 class="fw-bold mb-0 mt-1 text-secondary">{{ number_format($stats['inactive']) }}</h4>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">301 Permanent</small>
            <h4 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($stats['permanent']) }}</h4>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">302 Temporary</small>
            <h4 class="fw-bold mb-0 mt-1 text-warning">{{ number_format($stats['temporary']) }}</h4>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-custom p-3 text-center h-100">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Hits</small>
            <h4 class="fw-bold mb-0 mt-1 text-info">{{ number_format($stats['total_hits']) }}</h4>
          </div>
        </div>
      </div>

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <!-- Filters & Search Form -->
          <form class="mb-4" role="search" autocomplete="off" action="{{ url('panel/admin/redirects') }}" method="get">
            <div class="row g-2 align-items-center">
              <div class="col-md-5 position-relative">
                <i class="bi bi-search btn-search bar-search"></i>
                <input type="text" name="q" class="form-control ps-5" value="{{ request('q') }}" placeholder="Search source, destination, or notes...">
              </div>

              <div class="col-6 col-md-2">
                <select name="type" class="form-select">
                  <option value="">All Types</option>
                  <option value="301" {{ request('type') == '301' ? 'selected' : '' }}>301 Permanent</option>
                  <option value="302" {{ request('type') == '302' ? 'selected' : '' }}>302 Temporary</option>
                </select>
              </div>

              <div class="col-6 col-md-2">
                <select name="status" class="form-select">
                  <option value="">All Statuses</option>
                  <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>

              <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100">
                  <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if (request()->hasAny(['q', 'type', 'status']))
                  <a href="{{ url('panel/admin/redirects') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-circle"></i>
                  </a>
                @endif
              </div>
            </div>
          </form>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 60px;">ID</th>
                  <th scope="col">Source URL</th>
                  <th scope="col" style="width: 30px;"></th>
                  <th scope="col">Destination URL</th>
                  <th scope="col" style="width: 100px;">Type</th>
                  <th scope="col" style="width: 110px;">Status</th>
                  <th scope="col" style="width: 120px;">Hits</th>
                  <th scope="col" style="width: 120px;">Created</th>
                  <th scope="col" style="width: 110px;" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>

                @if ($data->total() != 0 && $data->count() != 0)
                  @foreach ($data as $redirect)
                    <tr>
                      <td class="fw-bold text-muted">{{ $redirect->id }}</td>
                      
                      <!-- Source URL -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <code class="fw-semibold text-break" style="font-size: 0.88rem;">{{ $redirect->source_url }}</code>
                          <a href="{{ url(ltrim($redirect->source_url, '/')) }}" target="_blank" class="text-muted ms-1" title="Test this link">
                            <i class="bi bi-box-arrow-up-right fs-7"></i>
                          </a>
                        </div>
                        @if ($redirect->notes)
                          <small class="text-muted d-block mt-0.5"><i class="bi bi-chat-left-text me-1"></i>{{ $redirect->notes }}</small>
                        @endif
                      </td>

                      <!-- Arrow -->
                      <td class="text-center text-muted px-0">
                        <i class="bi bi-arrow-right text-primary"></i>
                      </td>

                      <!-- Destination URL -->
                      <td>
                        @php
                          $destLink = preg_match('#^https?://#i', $redirect->destination_url) ? $redirect->destination_url : url($redirect->destination_url);
                        @endphp
                        <div class="d-flex align-items-center gap-1">
                          <a href="{{ $destLink }}" target="_blank" class="text-decoration-none fw-medium text-dark text-break" style="font-size: 0.88rem;">
                            {{ $redirect->destination_url }}
                          </a>
                          <i class="bi bi-box-arrow-up-right text-muted fs-7 ms-1"></i>
                        </div>
                        @if ($redirect->preserve_query)
                          <small class="badge bg-light text-secondary border mt-1" style="font-size: 0.68rem;">
                            <i class="bi bi-link-45deg me-0.5"></i> Preserves Query
                          </small>
                        @endif
                      </td>

                      <!-- Type -->
                      <td>
                        @if ($redirect->redirect_type == 301)
                          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.78rem;">
                            301 Permanent
                          </span>
                        @else
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.78rem;">
                            302 Temporary
                          </span>
                        @endif
                      </td>

                      <!-- Status Toggle -->
                      <td>
                        <form method="POST" action="{{ url('panel/admin/redirects/toggle', $redirect->id) }}" class="d-inline toggle-status-form">
                          @csrf
                          <button type="submit" class="btn btn-sm {{ $redirect->status ? 'btn-success' : 'btn-secondary' }} rounded-pill px-3 py-0.5 fw-semibold" style="font-size: 0.76rem;" title="Click to {{ $redirect->status ? 'disable' : 'enable' }}">
                            <i class="bi {{ $redirect->status ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i> {{ $redirect->status ? 'Active' : 'Inactive' }}
                          </button>
                        </form>
                      </td>

                      <!-- Hits -->
                      <td>
                        <span class="fw-bold text-dark">{{ number_format($redirect->hits_count) }}</span>
                        @if ($redirect->last_used_at)
                          <small class="text-muted d-block" style="font-size: 0.72rem;" title="{{ $redirect->last_used_at }}">
                            Last: {{ $redirect->last_used_at->diffForHumans() }}
                          </small>
                        @else
                          <small class="text-muted d-block" style="font-size: 0.72rem;">Never triggered</small>
                        @endif
                      </td>

                      <!-- Created -->
                      <td>
                        <small class="text-muted">{{ Helper::formatDate($redirect->created_at) }}</small>
                      </td>

                      <!-- Actions -->
                      <td class="text-end">
                        <div class="d-inline-flex align-items-center gap-2">
                          <a href="{{ url('panel/admin/redirects/edit', $redirect->id) }}" class="btn btn-link text-reset fs-5 p-0" title="Edit Redirect">
                            <i class="far fa-edit"></i>
                          </a>

                          <form method="POST" action="{{ url('panel/admin/redirects/delete', $redirect->id) }}" class="d-inline-block m-0">
                            @csrf
                            <button class="btn btn-link text-danger e-none fs-5 p-0 border-0 actionDelete" type="button" title="Delete Redirect">
                              <i class="bi-trash-fill"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="9" class="text-center py-5">
                      <div class="text-muted">
                        <i class="bi-arrow-left-right display-4 d-block mb-3 opacity-50"></i>
                        <h6 class="fw-bold">No URL redirects found</h6>
                        <p class="small mb-3">Create your first redirect rule to automatically forward old URLs to new destinations.</p>
                        <a href="{{ url('panel/admin/redirects/add') }}" class="btn btn-sm btn-dark">
                          <i class="bi-plus-lg me-1"></i> Add New Redirect
                        </a>
                      </div>
                    </td>
                  </tr>
                @endif

              </tbody>
            </table>
          </div>

          @if ($data->hasPages())
            <div class="mt-4">
              {{ $data->appends(['q' => request('q'), 'type' => request('type'), 'status' => request('status')])->onEachSide(1)->links() }}
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
