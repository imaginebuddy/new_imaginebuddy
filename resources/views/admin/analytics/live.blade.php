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
            <small class="fs-5 text-white-50 fw-normal">Total Visitors in Last 5m</small>
          </h2>
          <small class="text-white-50">Active within the last 5 minutes &bull; Auto-refreshes every 10 seconds</small>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
          <div class="bg-white bg-opacity-10 rounded px-3 py-2 text-center" style="min-width: 110px;">
            <span class="d-block small text-white-50">Verified Humans</span>
            <span class="fs-4 fw-bold text-success" id="liveHumansCount">{{ $activeHumans }}</span>
          </div>
          <div class="bg-white bg-opacity-10 rounded px-3 py-2 text-center" style="min-width: 110px;">
            <span class="d-block small text-white-50">Single-Hit / Bots</span>
            <span class="fs-4 fw-bold text-warning" id="liveCrawlersCount">{{ $activeCrawlers }}</span>
          </div>
          <div class="bg-white bg-opacity-10 rounded px-3 py-2 text-center" style="min-width: 110px;">
            <span class="d-block small text-white-50">Members</span>
            <span class="fs-4 fw-bold text-info" id="liveRegisteredCount">{{ $activeRegistered }}</span>
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
    <div class="card-header bg-transparent border-0 pt-4 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="d-flex flex-wrap align-items-center gap-3">
        <h5 class="card-title m-0 fw-light"><i class="bi bi-broadcast text-danger me-2"></i> Active Visitors Stream</h5>

        <!-- Filter Toggle Buttons -->
        <div class="btn-group btn-group-sm" role="group" id="liveFilterGroup">
          <button type="button" class="btn btn-outline-primary active" id="btnFilterAll" onclick="setLiveFilter('all')">
            All Traffic (<span id="badgeFilterAll">{{ $activeTotal }}</span>)
          </button>
          <button type="button" class="btn btn-outline-success" id="btnFilterHumans" onclick="setLiveFilter('humans')">
            <i class="bi bi-person-check-fill me-1"></i> Humans Only (<span id="badgeFilterHumans">{{ $activeHumans }}</span>)
          </button>
          <button type="button" class="btn btn-outline-secondary" id="btnFilterCrawlers" onclick="setLiveFilter('crawlers')">
            <i class="bi bi-robot me-1"></i> Single-Hit Crawlers (<span id="badgeFilterCrawlers">{{ $activeCrawlers }}</span>)
          </button>
        </div>
      </div>

      <span class="badge bg-light text-dark border" id="lastUpdatedBadge">Updated just now</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small">
            <tr>
              <th>Visitor / User</th>
              <th>Traffic Type</th>
              <th>Current Page / Activity</th>
              <th>Device &amp; Browser</th>
              <th>Country</th>
              <th>Time on Site</th>
              <th class="text-end">Last Seen</th>
            </tr>
          </thead>
          <tbody id="liveSessionsTableBody">
            @forelse($liveSessions as $session)
              @php
                $isHuman = !empty($session->user_id) || $session->duration_seconds > 0 || $session->is_engaged == 1 || $session->page_views_count > 1;
              @endphp
              <tr data-is-human="{{ $isHuman ? '1' : '0' }}">
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
                  @if($isHuman)
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                      <i class="bi bi-person-check-fill me-1"></i> Human
                    </span>
                  @else
                    <span class="badge bg-light text-muted border">
                      <i class="bi bi-robot me-1"></i> Crawler
                    </span>
                  @endif
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
                <td colspan="7" class="text-center text-muted py-4">No active visitors right now. Waiting for new traffic...</td>
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
var currentLiveFilter = 'all';
var cachedLiveSessions = [];

function setLiveFilter(filterType) {
  currentLiveFilter = filterType;

  // Update button classes
  document.getElementById('btnFilterAll').classList.toggle('active', filterType === 'all');
  document.getElementById('btnFilterHumans').classList.toggle('active', filterType === 'humans');
  document.getElementById('btnFilterCrawlers').classList.toggle('active', filterType === 'crawlers');

  renderLiveTable();
}

function renderLiveTable() {
  var tbody = document.getElementById('liveSessionsTableBody');
  if (!cachedLiveSessions || cachedLiveSessions.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No active visitors right now.</td></tr>';
    return;
  }

  var filtered = cachedLiveSessions.filter(function(s) {
    if (currentLiveFilter === 'humans') return s.is_human;
    if (currentLiveFilter === 'crawlers') return !s.is_human;
    return true;
  });

  if (filtered.length === 0) {
    var msg = currentLiveFilter === 'humans' 
      ? 'No active human visitors in the last 5 minutes.' 
      : (currentLiveFilter === 'crawlers' ? 'No single-hit crawlers.' : 'No active visitors.');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">' + msg + '</td></tr>';
    return;
  }

  var html = '';
  filtered.forEach(function(s) {
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
    var badgeType = s.is_human
      ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-person-check-fill me-1"></i> Human</span>'
      : '<span class="badge bg-light text-muted border"><i class="bi bi-robot me-1"></i> Crawler</span>';

    html += '<tr>' +
      '<td>' + userHtml + '</td>' +
      '<td>' + badgeType + '</td>' +
      '<td><span class="badge bg-light text-dark border text-truncate" style="max-width: 250px;" title="' + s.current_page + '">' + s.current_page + '</span></td>' +
      '<td><i class="bi ' + deviceIcon + ' text-muted me-1"></i> <span class="small">' + (s.browser || 'Browser') + '</span></td>' +
      '<td><span class="fw-bold">' + s.country_code + '</span></td>' +
      '<td><small class="text-muted">' + s.duration_formatted + '</small></td>' +
      '<td class="text-end"><span class="badge bg-success-subtle text-success">' + s.last_seen_diff + '</span></td>' +
      '</tr>';
  });

  tbody.innerHTML = html;
}

function fetchLiveData() {
  var icon = document.getElementById('refreshIcon');
  if (icon) icon.classList.add('spin-icon');

  fetch('{{ url("panel/admin/analytics/live-data") }}')
    .then(response => response.json())
    .then(data => {
      if (icon) icon.classList.remove('spin-icon');
      document.getElementById('liveTotalCount').textContent = data.total;
      document.getElementById('liveHumansCount').textContent = data.humans;
      document.getElementById('liveCrawlersCount').textContent = data.crawlers;
      document.getElementById('liveRegisteredCount').textContent = data.registered;

      document.getElementById('badgeFilterAll').textContent = data.total;
      document.getElementById('badgeFilterHumans').textContent = data.humans;
      document.getElementById('badgeFilterCrawlers').textContent = data.crawlers;

      var navBadge = document.getElementById('navLiveBadge');
      if (navBadge) navBadge.textContent = data.total;

      document.getElementById('lastUpdatedBadge').textContent = 'Updated ' + new Date().toLocaleTimeString();

      cachedLiveSessions = data.sessions || [];
      renderLiveTable();
    })
    .catch(err => {
      if (icon) icon.classList.remove('spin-icon');
    });
}

// Initial fetch to load cachedLiveSessions for filtering
fetchLiveData();

// Auto-poll every 10 seconds
setInterval(fetchLiveData, 10000);
</script>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
.spin-icon { animation: spin 0.8s linear infinite; }
.btn-group .btn.active { font-weight: 600; }
</style>
@endsection
