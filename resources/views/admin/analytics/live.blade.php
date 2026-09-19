@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Real-Time Top Status Card -->
  <div class="card shadow-custom border-0 mb-4 bg-dark text-white">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
          <div class="d-flex align-items-center mb-2">
            <span class="spinner-grow spinner-grow-sm text-danger me-2" role="status"></span>
            <span class="text-uppercase tracking-wider small fw-bold text-danger">Real-Time Traffic Monitor</span>
          </div>
          <h2 class="display-5 fw-bold mb-0">
            <span id="liveTotalCount">{{ $activeTotal }}</span>
            <small class="fs-5 text-white-50 fw-normal">Active Users on Site Right Now</small>
          </h2>
          <small class="text-white-50">Active within the last 5 minutes &bull; Auto-refreshes every 10 seconds</small>
        </div>

        <div class="d-flex gap-3 mt-3 mt-md-0">
          <div class="bg-white bg-opacity-10 rounded px-4 py-2 text-center">
            <span class="d-block small text-white-50">Logged-In Members</span>
            <span class="fs-4 fw-bold text-success" id="liveRegisteredCount">{{ $activeRegistered }}</span>
          </div>
          <div class="bg-white bg-opacity-10 rounded px-4 py-2 text-center">
            <span class="d-block small text-white-50">Anonymous Visitors</span>
            <span class="fs-4 fw-bold text-info" id="liveAnonymousCount">{{ $activeAnonymous }}</span>
          </div>
          <button class="btn btn-outline-light d-flex align-items-center" id="btnManualRefresh" onclick="fetchLiveData()">
            <i class="bi bi-arrow-clockwise me-1" id="refreshIcon"></i> Refresh
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Real-Time Active Sessions Feed Table -->
  <div class="card shadow-custom border-0">
    <div class="card-header bg-transparent border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
      <h5 class="card-title m-0 fw-light"><i class="bi bi-broadcast text-danger me-2"></i> Active Visitors Stream</h5>
      <span class="badge bg-light text-dark border" id="lastUpdatedBadge">Updated just now</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small">
            <tr>
              <th>Visitor / User</th>
              <th>Current Page / Activity</th>
              <th>Device &amp; Browser</th>
              <th>Country</th>
              <th>Time on Site</th>
              <th class="text-end">Last Seen</th>
            </tr>
          </thead>
          <tbody id="liveSessionsTableBody">
            @forelse($liveSessions as $session)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    @if($session->user)
                      <img src="{{ Storage::url(config('path.avatar') . $session->user->avatar) }}" width="32" height="32" class="rounded-circle me-2" />
                      <div>
                        <a href="{{ url($session->user->username) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-block">
                          {{ $session->user->name ?: $session->user->username }}
                        </a>
                        <small class="text-success"><i class="bi bi-check-circle-fill"></i> Member</small>
                      </div>
                    @else
                      <div class="avatar-placeholder rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person"></i>
                      </div>
                      <div>
                        <span class="fw-medium text-dark">Anonymous Visitor</span>
                        <small class="d-block text-muted">#{{ substr($session->visitor_id, 0, 8) }}</small>
                      </div>
                    @endif
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border text-truncate" style="max-width: 250px;" title="{{ $session->exit_page }}">
                    {{ $session->exit_page ?: '/' }}
                  </span>
                </td>
                <td>
                  <span class="me-1">
                    @if($session->device_type == 'mobile')
                      <i class="bi bi-phone text-muted" title="Mobile"></i>
                    @elseif($session->device_type == 'tablet')
                      <i class="bi bi-tablet text-muted" title="Tablet"></i>
                    @else
                      <i class="bi bi-laptop text-muted" title="Desktop"></i>
                    @endif
                  </span>
                  <span class="small">{{ $session->browser }}</span>
                </td>
                <td>
                  <span class="fw-bold">{{ $session->country_code ?: 'UN' }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ gmdate('H:i:s', $session->duration_seconds) }}</small>
                </td>
                <td class="text-end">
                  <span class="badge bg-success-subtle text-success">{{ $session->last_activity_at ? $session->last_activity_at->diffForHumans() : 'Just now' }}</span>
                </td>
              </tr>
            @empty
              <tr id="emptyLiveRow">
                <td colspan="6" class="text-center text-muted py-4">No active visitors right now. Waiting for new traffic...</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script>
function fetchLiveData() {
  var icon = document.getElementById('refreshIcon');
  if (icon) icon.classList.add('spin-icon');

  fetch('{{ url("panel/admin/analytics/live-data") }}')
    .then(response => response.json())
    .then(data => {
      if (icon) icon.classList.remove('spin-icon');
      document.getElementById('liveTotalCount').textContent = data.total;
      document.getElementById('liveRegisteredCount').textContent = data.registered;
      document.getElementById('liveAnonymousCount').textContent = data.anonymous;

      var navBadge = document.getElementById('navLiveBadge');
      if (navBadge) navBadge.textContent = data.total;

      document.getElementById('lastUpdatedBadge').textContent = 'Updated ' + new Date().toLocaleTimeString();

      var tbody = document.getElementById('liveSessionsTableBody');
      if (data.sessions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No active visitors right now.</td></tr>';
        return;
      }

      var html = '';
      data.sessions.forEach(function(s) {
        var userHtml = '';
        if (s.is_logged_in) {
          userHtml = '<div class="d-flex align-items-center">' +
            (s.user_avatar ? '<img src="' + s.user_avatar + '" width="32" height="32" class="rounded-circle me-2" />' : '') +
            '<div><a href="' + s.user_url + '" target="_blank" class="fw-bold text-dark text-decoration-none d-block">' + s.user_name + '</a>' +
            '<small class="text-success"><i class="bi bi-check-circle-fill"></i> Member</small></div></div>';
        } else {
          userHtml = '<div class="d-flex align-items-center">' +
            '<div class="avatar-placeholder rounded-circle bg-light border text-muted d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;"><i class="bi bi-person"></i></div>' +
            '<div><span class="fw-medium text-dark">' + s.user_name + '</span></div></div>';
        }

        var deviceIcon = s.device_type === 'Mobile' ? 'bi-phone' : (s.device_type === 'Tablet' ? 'bi-tablet' : 'bi-laptop');

        html += '<tr>' +
          '<td>' + userHtml + '</td>' +
          '<td><span class="badge bg-light text-dark border text-truncate" style="max-width: 250px;">' + s.current_page + '</span></td>' +
          '<td><i class="bi ' + deviceIcon + ' text-muted me-1"></i> <span class="small">' + (s.browser || 'Browser') + '</span></td>' +
          '<td><span class="fw-bold">' + s.country_code + '</span></td>' +
          '<td><small class="text-muted">' + s.duration_formatted + '</small></td>' +
          '<td class="text-end"><span class="badge bg-success-subtle text-success">' + s.last_seen_diff + '</span></td>' +
          '</tr>';
      });

      tbody.innerHTML = html;
    })
    .catch(err => {
      if (icon) icon.classList.remove('spin-icon');
    });
}

// Auto-poll every 10 seconds
setInterval(fetchLiveData, 10000);
</script>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
.spin-icon { animation: spin 0.8s linear infinite; }
</style>
@endsection
