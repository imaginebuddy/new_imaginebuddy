@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Search Overview Metric Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Total Searches</span>
          <h3 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($totalSearches) }}</h3>
          <small class="text-muted">Search executions in period</small>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Unique Search Queries</span>
          <h3 class="fw-bold mb-0 mt-1 text-info">{{ number_format($uniqueQueries) }}</h3>
          <small class="text-muted">Distinct terms searched</small>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Zero-Result Searches</span>
          <h3 class="fw-bold mb-0 mt-1 text-danger">{{ number_format($zeroResultSearches) }}</h3>
          <small class="text-muted">Queries returning 0 results (content gaps)</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Filter Bar -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
        <input type="hidden" name="range" value="{{ request('range', '30d') }}">
        @if(request('start_date')) <input type="hidden" name="start_date" value="{{ request('start_date') }}"> @endif
        @if(request('end_date')) <input type="hidden" name="end_date" value="{{ request('end_date') }}"> @endif
        <input type="hidden" name="bot_filter" value="{{ request('bot_filter', 'exclude') }}">
        <input type="hidden" name="user_type" value="{{ request('user_type', 'all') }}">

        <div class="col-md-8">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Search specific keyword or query..." value="{{ request('q') }}">
          </div>
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-dark px-3">Filter</button>
          @if(request('q'))
            <a href="{{ url('panel/admin/analytics/searches?range=' . request('range', '30d')) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
          @endif
        </div>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <!-- Top Searched Queries -->
    <div class="col-lg-8">
      <div class="card shadow-custom border-0">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-search me-2 text-primary"></i> Most Searched Queries</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Query</th>
                  <th class="text-center">Avg. Results Found</th>
                  <th class="text-end">Search Count</th>
                  <th class="text-end">Last Searched</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topQueries as $item)
                  <tr>
                    <td>
                      <span class="fw-bold">{{ $item->query }}</span>
                      <a href="{{ url('search?q=' . urlencode($item->query)) }}" target="_blank" class="text-muted ms-1 small" title="View search results">
                        <i class="bi bi-box-arrow-up-right"></i>
                      </a>
                    </td>
                    <td class="text-center">
                      @if($item->avg_results == 0)
                        <span class="badge bg-danger-subtle text-danger">0 results</span>
                      @else
                        <span class="badge bg-light text-dark border">{{ round($item->avg_results) }}</span>
                      @endif
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->count) }}</td>
                    <td class="text-end small text-muted">{{ $item->last_searched ? \Carbon\Carbon::parse($item->last_searched)->diffForHumans() : '-' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">No search queries found in this period.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if($topQueries->hasPages())
          <div class="card-footer bg-transparent border-0 py-3">
            {{ $topQueries->appends(request()->all())->links() }}
          </div>
        @endif
      </div>
    </div>

    <!-- Zero-Result Queries (Content Gaps) -->
    <div class="col-lg-4">
      <div class="card shadow-custom border-0">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-exclamation-triangle text-danger me-2"></i> Zero-Result Queries</h5>
          <small class="text-muted">Content demands with 0 search matches</small>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Query</th>
                  <th class="text-end">Unanswered Searches</th>
                </tr>
              </thead>
              <tbody>
                @forelse($zeroResults as $item)
                  <tr>
                    <td>
                      <span class="fw-medium text-danger">{{ $item->query }}</span>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="text-center text-muted py-4">No zero-result searches!</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
