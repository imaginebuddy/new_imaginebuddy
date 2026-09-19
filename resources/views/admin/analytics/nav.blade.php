<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
  <div class="mb-2 mb-md-0">
    <h4 class="m-0 fw-light">
      <i class="bi bi-graph-up text-primary me-2"></i> Analytics &amp; User Activity
    </h4>
    <small class="text-muted">{{ $rangeLabel ?? 'Last 30 Days' }}</small>
  </div>

  <div class="d-flex flex-wrap gap-2 align-items-center">
    <!-- Time Range Filter Form -->
    <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center" id="analyticsFilterForm">
      @if(request('q'))
        <input type="hidden" name="q" value="{{ request('q') }}">
      @endif

      <!-- Bot Filter -->
      <select name="bot_filter" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
        <option value="exclude" @if(request('bot_filter', 'exclude') == 'exclude') selected @endif>Verified Humans (Real Users)</option>
        <option value="all" @if(request('bot_filter') == 'all') selected @endif>All Traffic (Inc. Crawlers)</option>
        <option value="only" @if(request('bot_filter') == 'only') selected @endif>Crawlers &amp; Bots Only</option>
      </select>

      <!-- User Type Filter -->
      <select name="user_type" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
        <option value="all" @if(request('user_type', 'all') == 'all') selected @endif>All Users</option>
        <option value="logged_in" @if(request('user_type') == 'logged_in') selected @endif>Logged-In Only</option>
        <option value="anonymous" @if(request('user_type') == 'anonymous') selected @endif>Anonymous Only</option>
      </select>

      <!-- Range Preset Filter -->
      <select name="range" class="form-select form-select-sm" onchange="handleRangeChange(this.value)" style="width: auto;">
        <option value="today" @if(request('range') == 'today') selected @endif>Today</option>
        <option value="yesterday" @if(request('range') == 'yesterday') selected @endif>Yesterday</option>
        <option value="7d" @if(request('range') == '7d') selected @endif>Last 7 Days</option>
        <option value="30d" @if(request('range', '30d') == '30d') selected @endif>Last 30 Days</option>
        <option value="custom" @if(request('range') == 'custom') selected @endif>Custom Range</option>
      </select>

      <div id="customDateInputs" class="d-flex gap-1 align-items-center @if(request('range') != 'custom') d-none @endif">
        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $start ?? '') }}">
        <span class="text-muted">-</span>
        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $end ?? '') }}">
        <button type="submit" class="btn btn-sm btn-dark"><i class="bi bi-filter"></i></button>
      </div>
    </form>
  </div>
</div>

<!-- Secondary Navigation Tabs -->
<ul class="nav nav-pills mb-4 gap-2 flex-wrap">
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics')) active @endif" href="{{ url('panel/admin/analytics') }}">
      <i class="bi bi-speedometer2 me-1"></i> Overview
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/live')) active @endif" href="{{ url('panel/admin/analytics/live') }}">
      <i class="bi bi-broadcast me-1 text-danger"></i> Live Active Users
      <span class="badge rounded-pill bg-danger ms-1 animate-pulse" id="navLiveBadge">{{ $liveUsersCount ?? '' }}</span>
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/traffic')) active @endif" href="{{ url('panel/admin/analytics/traffic') }}">
      <i class="bi bi-compass me-1"></i> Visits &amp; Traffic
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/searches')) active @endif" href="{{ url('panel/admin/analytics/searches') }}">
      <i class="bi bi-search me-1"></i> Search Analytics
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/prompts')) active @endif" href="{{ url('panel/admin/analytics/prompts') }}">
      <i class="bi bi-card-text me-1"></i> Prompt Analytics &amp; Copies
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/funnel')) active @endif" href="{{ url('panel/admin/analytics/funnel') }}">
      <i class="bi bi-funnel me-1"></i> Pricing &amp; Funnel
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(request()->is('panel/admin/analytics/users')) active @endif" href="{{ url('panel/admin/analytics/users') }}">
      <i class="bi bi-people me-1"></i> Users &amp; Retention
    </a>
  </li>
</ul>

<script>
function handleRangeChange(val) {
  if (val === 'custom') {
    document.getElementById('customDateInputs').classList.remove('d-none');
  } else {
    document.getElementById('customDateInputs').classList.add('d-none');
    document.getElementById('analyticsFilterForm').submit();
  }
}
</script>
