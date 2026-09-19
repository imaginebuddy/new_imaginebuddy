@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Breakdown Cards Row: Devices, Browsers, Entry Pages -->
  <div class="row g-4 mb-4">
    <!-- Devices -->
    <div class="col-md-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3">
          <h6 class="m-0 fw-bold"><i class="bi bi-phone me-1 text-primary"></i> Device Types</h6>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($devices as $dev)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                <span class="text-capitalize">
                  @if($dev->device_type == 'mobile') <i class="bi bi-phone text-muted me-1"></i>
                  @elseif($dev->device_type == 'tablet') <i class="bi bi-tablet text-muted me-1"></i>
                  @else <i class="bi bi-laptop text-muted me-1"></i> @endif
                  {{ $dev->device_type ?: 'Desktop' }}
                </span>
                <span class="badge bg-light text-dark border">{{ number_format($dev->count) }}</span>
              </li>
            @empty
              <li class="text-muted small">No device data</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <!-- Top Browsers -->
    <div class="col-md-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3">
          <h6 class="m-0 fw-bold"><i class="bi bi-browser-chrome me-1 text-info"></i> Top Browsers</h6>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($browsers as $b)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                <span>{{ $b->browser ?: 'Unknown' }}</span>
                <span class="badge bg-light text-dark border">{{ number_format($b->count) }}</span>
              </li>
            @empty
              <li class="text-muted small">No browser data</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <!-- Top Operating Systems -->
    <div class="col-md-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3">
          <h6 class="m-0 fw-bold"><i class="bi bi-hdd-network me-1 text-success"></i> Operating Systems</h6>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse($operatingSystems as $os)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                <span>{{ $os->os ?: 'Unknown' }}</span>
                <span class="badge bg-light text-dark border">{{ number_format($os->count) }}</span>
              </li>
            @empty
              <li class="text-muted small">No OS data</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Entry Pages & Geographic Tables -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3">
          <h6 class="m-0 fw-bold"><i class="bi bi-box-arrow-in-right me-1 text-primary"></i> Top Entry Pages</h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr><th>Entry Path</th><th class="text-end">Visits</th></tr>
              </thead>
              <tbody>
                @forelse($entryPages as $ep)
                  <tr>
                    <td><code>{{ $ep->entry_page ?: '/' }}</code></td>
                    <td class="text-end fw-bold">{{ number_format($ep->count) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-center text-muted py-3">No entry page data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3">
          <h6 class="m-0 fw-bold"><i class="bi bi-globe me-1 text-primary"></i> Geographic Distribution</h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr><th>Country</th><th class="text-end">Visits</th></tr>
              </thead>
              <tbody>
                @forelse($countries as $c)
                  <tr>
                    <td><span class="fw-bold">{{ $c->country_code }}</span></td>
                    <td class="text-end fw-bold">{{ number_format($c->count) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-center text-muted py-3">No geographic data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Session Explorer Table -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-title m-0 fw-light"><i class="bi bi-journal-text me-2 text-primary"></i> Session Explorer</h5>
        <small class="text-muted">Chronological visits log with duration and path tracking</small>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small">
            <tr>
              <th>Date / Time</th>
              <th>Visitor / User</th>
              <th>Type</th>
              <th>Entry / Exit</th>
              <th>Referrer / UTM</th>
              <th>Device</th>
              <th>Duration</th>
              <th class="text-end">Pages</th>
            </tr>
          </thead>
          <tbody>
            @forelse($sessions as $s)
              <tr>
                <td>
                  <span class="fw-medium d-block">{{ $s->started_at ? $s->started_at->format('M d, Y') : '-' }}</span>
                  <small class="text-muted">{{ $s->started_at ? $s->started_at->format('H:i:s') : '-' }}</small>
                </td>
                <td>
                  @if($s->user)
                    <a href="{{ url($s->user->username) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                      {{ $s->user->username }}
                    </a>
                    <span class="badge bg-success-subtle text-success ms-1">Member</span>
                  @else
                    <span class="text-muted">#{{ substr($s->visitor_id, 0, 8) }}</span>
                    @if($s->is_new_visitor)
                      <span class="badge bg-info-subtle text-info ms-1">New</span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary ms-1">Returning</span>
                    @endif
                  @endif
                </td>
                <td>
                  @if($s->is_bot == 1)
                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-robot"></i> {{ $s->bot_name ?: 'Bot' }}</span>
                  @elseif($s->is_bot == 2)
                    <span class="badge bg-warning-subtle text-warning">Suspicious</span>
                  @else
                    <span class="badge bg-light text-dark border">Real User</span>
                  @endif
                </td>
                <td>
                  <small class="d-block text-truncate" style="max-width: 140px;" title="Entry: {{ $s->entry_page }}">
                    <i class="bi bi-arrow-right-short text-success"></i> {{ $s->entry_page ?: '/' }}
                  </small>
                  <small class="d-block text-truncate text-muted" style="max-width: 140px;" title="Exit: {{ $s->exit_page }}">
                    <i class="bi bi-arrow-left-short text-danger"></i> {{ $s->exit_page ?: '/' }}
                  </small>
                </td>
                <td>
                  <small class="d-block text-truncate" style="max-width: 120px;" title="{{ $s->referrer ?: 'Direct' }}">
                    {{ $s->referrer_domain ?: ($s->referrer ? 'Referral' : 'Direct') }}
                  </small>
                  @if($s->utm_source)
                    <span class="badge bg-light text-muted border" title="Source: {{ $s->utm_source }}">{{ $s->utm_source }}</span>
                  @endif
                </td>
                <td>
                  <small>{{ ucfirst($s->device_type) }} &bull; {{ $s->browser }}</small>
                </td>
                <td>
                  <small class="fw-bold">{{ gmdate('i:s', $s->duration_seconds) }}</small>
                </td>
                <td class="text-end">
                  <span class="badge bg-light text-dark border">{{ $s->page_views_count }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No sessions found matching filters.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($sessions->hasPages())
      <div class="card-footer bg-transparent border-0 py-3">
        {{ $sessions->appends(request()->all())->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
