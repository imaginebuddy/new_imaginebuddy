@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Primary Metric Cards Row 1: Visitors & Engagement -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted text-uppercase small fw-bold">Unique Visitors</span>
            <span class="badge bg-primary-subtle text-primary"><i class="bi bi-people-fill"></i></span>
          </div>
          <h3 class="fw-bold mb-1">{{ number_format($sessionStats->unique_visitors ?? 0) }}</h3>
          <div class="small text-muted">
            <span class="text-success"><i class="bi bi-arrow-up-right"></i> {{ number_format($sessionStats->new_visitors ?? 0) }} new</span> &bull;
            <span>{{ number_format($sessionStats->returning_visitors ?? 0) }} returning</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted text-uppercase small fw-bold">Total Pageviews</span>
            <span class="badge bg-info-subtle text-info"><i class="bi bi-eye-fill"></i></span>
          </div>
          <h3 class="fw-bold mb-1">{{ number_format($sessionStats->total_page_views ?? 0) }}</h3>
          <div class="small text-muted">
            <span>{{ number_format($sessionStats->total_sessions ?? 0) }} sessions</span> &bull;
            <span>{{ $sessionStats->total_sessions ? number_format($sessionStats->total_page_views / $sessionStats->total_sessions, 1) : 0 }} pages/session</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted text-uppercase small fw-bold">Avg. Session Duration</span>
            <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock-history"></i></span>
          </div>
          <h3 class="fw-bold mb-1">{{ gmdate('i:s', (int)($sessionStats->avg_duration ?? 0)) }}</h3>
          <div class="small text-muted">
            <span>{{ number_format($sessionStats->engaged_sessions ?? 0) }} engaged visits</span> &bull;
            <span class="text-muted">{{ $sessionStats->total_sessions ? round(($sessionStats->engaged_sessions / $sessionStats->total_sessions) * 100) : 0 }}% engaged</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted text-uppercase small fw-bold">Audience Split</span>
            <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-person-badge"></i></span>
          </div>
          <h3 class="fw-bold mb-1">{{ number_format($sessionStats->logged_in_users ?? 0) }} <small class="fs-6 text-muted">members</small></h3>
          <div class="small text-muted">
            <span>{{ number_format($sessionStats->anonymous_users ?? 0) }} anonymous visitors</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Primary Metric Cards Row 2: Prompt Interactions & Conversion -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 border-start border-4 border-primary">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Prompt Page Views</span>
          <h3 class="fw-bold mb-1">{{ number_format($eventStats->prompt_views ?? 0) }}</h3>
          <small class="text-muted">Views on prompt detail pages</small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 border-start border-4 border-success">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Prompt Copies</span>
          <h3 class="fw-bold mb-1 text-success">{{ number_format($eventStats->prompt_copies ?? 0) }}</h3>
          <small class="text-muted">
            +{{ number_format($eventStats->prompt_copy_attempts ?? 0) }} guest sign-up triggers
          </small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 border-start border-4 border-info">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Search Volume</span>
          <h3 class="fw-bold mb-1">{{ number_format($totalSearches) }}</h3>
          <small class="text-muted">Search queries executed</small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 border-start border-4 border-warning">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Paid Subscriptions</span>
          <h3 class="fw-bold mb-1 text-warning">{{ number_format($eventStats->payment_successes ?? 0) }}</h3>
          <small class="text-muted">
            {{ number_format($eventStats->pricing_views ?? 0) }} pricing views &bull; {{ number_format($eventStats->payment_attempts ?? 0) }} attempts
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Timeline Trends Chart -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title m-0 fw-light"><i class="bi bi-graph-up text-primary me-2"></i> Traffic Trend (Visitors vs Pageviews)</h5>
      </div>
      <div style="height: 320px; position: relative;">
        <canvas id="chartVisitorsTrend"></canvas>
      </div>
    </div>
  </div>

  <!-- Tables Row: Top Prompts, Searches, Referrers -->
  <div class="row g-4">
    <!-- Top Viewed Prompts -->
    <div class="col-lg-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 fw-bold"><i class="bi bi-card-image me-1 text-primary"></i> Top Prompts Viewed</h6>
          <a href="{{ url('panel/admin/analytics/prompts') }}" class="small text-muted">View all</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Prompt</th>
                  <th class="text-end">Views</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topPrompts as $item)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($item->image && $item->image->thumbnail)
                          <img src="{{ Storage::url(config('path.thumbnail') . $item->image->thumbnail) }}" width="36" height="36" class="rounded me-2 object-fit-cover" />
                        @endif
                        <div class="text-truncate" style="max-width: 180px;">
                          @if($item->image)
                            <a href="{{ url('prompt', $item->image->slug ?: $item->image->id) }}" target="_blank" class="text-decoration-none fw-medium text-dark text-truncate d-block">
                              {{ $item->image->title }}
                            </a>
                            <small class="badge bg-light text-muted border">{{ $item->image->ai_model ?: 'Prompt' }}</small>
                          @else
                            <span class="text-muted">Prompt #{{ $item->image_id }}</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->views_count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="text-center text-muted py-3">No prompt views in this range</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Searches -->
    <div class="col-lg-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 fw-bold"><i class="bi bi-search me-1 text-info"></i> Top Searched Queries</h6>
          <a href="{{ url('panel/admin/analytics/searches') }}" class="small text-muted">View all</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Query</th>
                  <th class="text-end">Searches</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topSearches as $item)
                  <tr>
                    <td>
                      <i class="bi bi-search text-muted me-1 small"></i>
                      <span class="fw-medium">{{ $item->query }}</span>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="text-center text-muted py-3">No searches in this range</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Traffic Sources -->
    <div class="col-lg-4">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 fw-bold"><i class="bi bi-compass me-1 text-success"></i> Top Referrers</h6>
          <a href="{{ url('panel/admin/analytics/traffic') }}" class="small text-muted">View all</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Source Domain</th>
                  <th class="text-end">Visits</th>
                </tr>
              </thead>
              <tbody>
                @forelse($trafficSources as $item)
                  <tr>
                    <td>
                      <i class="bi bi-box-arrow-up-right text-muted me-1 small"></i>
                      <span class="fw-medium">{{ $item->referrer_domain ?: 'Direct / Organic Search' }}</span>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="text-center text-muted py-3">No referrer data in this range</td>
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

@section('javascript')
<script src="{{ asset('public/js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('chartVisitorsTrend').getContext('2d');
  
  var gradientVisitors = ctx.createLinearGradient(0, 0, 0, 300);
  gradientVisitors.addColorStop(0, 'rgba(13, 110, 253, 0.3)');
  gradientVisitors.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

  var gradientViews = ctx.createLinearGradient(0, 0, 0, 300);
  gradientViews.addColorStop(0, 'rgba(13, 202, 240, 0.25)');
  gradientViews.addColorStop(1, 'rgba(13, 202, 240, 0.0)');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: [{!! $chartDates !!}],
      datasets: [
        {
          label: 'Unique Visitors',
          data: [{!! $chartVisitors !!}],
          borderColor: '#0d6efd',
          backgroundColor: gradientVisitors,
          borderWidth: 2.5,
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 6
        },
        {
          label: 'Total Pageviews',
          data: [{!! $chartPageviews !!}],
          borderColor: '#0dcaf0',
          backgroundColor: gradientViews,
          borderWidth: 2,
          fill: true,
          tension: 0.35,
          pointRadius: 2,
          pointHoverRadius: 5
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            precision: 0
          }
        }],
        xAxes: [{
          gridLines: {
            display: false
          }
        }]
      }
    }
  });
});
</script>
@endsection
