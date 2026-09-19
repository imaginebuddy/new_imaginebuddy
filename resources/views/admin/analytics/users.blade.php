@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Registrations Overview Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">New Registrations in Period</span>
          <h3 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($newUsers) }}</h3>
          <small class="text-muted">Accounts registered between {{ $rangeLabel }}</small>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Total Platform Members</span>
          <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($totalRegistered) }}</h3>
          <small class="text-muted">All-time active &amp; pending users</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Registration Trend Chart -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title m-0 fw-light"><i class="bi bi-person-plus text-primary me-2"></i> Registration Growth Over Time</h5>
      </div>
      <div style="height: 280px; position: relative;">
        <canvas id="chartRegistrationTrend"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Registrations Table -->
  <div class="card shadow-custom border-0">
    <div class="card-header bg-transparent border-0 pt-4 pb-2">
      <h5 class="card-title m-0 fw-light"><i class="bi bi-people me-2 text-primary"></i> Newly Registered Members</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small">
            <tr>
              <th>Member</th>
              <th>Email</th>
              <th>Country</th>
              <th>Status</th>
              <th class="text-end">Registered At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentUsers as $user)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="{{ Storage::url(config('path.avatar') . $user->avatar) }}" width="36" height="36" class="rounded-circle me-2" />
                    <div>
                      <a href="{{ url($user->username) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                        {{ $user->name ?: $user->username }}
                      </a>
                      <small class="text-muted d-block">&#64;{{ $user->username }}</small>
                    </div>
                  </div>
                </td>
                <td><code>{{ $user->email }}</code></td>
                <td>{{ $user->country() ? $user->country()->country_name : '-' }}</td>
                <td>
                  <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'warning' }}">
                    {{ ucfirst($user->status) }}
                  </span>
                </td>
                <td class="text-end small text-muted">
                  {{ Helper::formatDate($user->date) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No new registrations in this period.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($recentUsers->hasPages())
      <div class="card-footer bg-transparent border-0 py-3">
        {{ $recentUsers->appends(request()->all())->links() }}
      </div>
    @endif
  </div>

</div>
@endsection

@section('javascript')
<script src="{{ asset('public/js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('chartRegistrationTrend').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [{!! $chartDates !!}],
      datasets: [{
        label: 'New Registrations',
        data: [{!! $chartUsers !!}],
        backgroundColor: '#0d6efd',
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            precision: 0
          }
        }],
        xAxes: [{
          gridLines: { display: false }
        }]
      }
    }
  });
});
</script>
@endsection
