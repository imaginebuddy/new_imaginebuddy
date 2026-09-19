@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- Prompt KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Prompt Page Views</span>
          <h3 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($totalViews) }}</h3>
          <small class="text-muted">Total prompt views recorded</small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Successful Prompt Copies</span>
          <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($totalCopies) }}</h3>
          <small class="text-muted">By logged-in members</small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Guest Copy Attempts (Sign-up triggers)</span>
          <h3 class="fw-bold mb-0 mt-1 text-warning">{{ number_format($totalBlockedAttempts) }}</h3>
          <small class="text-muted">Guests blocked by copy auth-wall</small>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0">
        <div class="card-body">
          <span class="text-muted text-uppercase small fw-bold">Copy Conversion Rate</span>
          <h3 class="fw-bold mb-0 mt-1 text-info">
            {{ $totalViews > 0 ? round(($totalCopies / $totalViews) * 100, 1) : 0 }}%
          </h3>
          <small class="text-muted">{{ number_format($uniqueCopiers) }} unique users copied</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables: Most Viewed Prompts & Most Copied Prompts -->
  <div class="row g-4 mb-4">
    <!-- Most Viewed -->
    <div class="col-lg-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-eye text-primary me-2"></i> Most Viewed Prompts</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Prompt</th>
                  <th class="text-center">Unique Viewers</th>
                  <th class="text-end">Total Views</th>
                </tr>
              </thead>
              <tbody>
                @forelse($mostViewed as $item)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($item->image && $item->image->thumbnail)
                          <img src="{{ Storage::url(config('path.thumbnail') . $item->image->thumbnail) }}" width="40" height="40" class="rounded me-2 object-fit-cover" />
                        @endif
                        <div class="text-truncate" style="max-width: 200px;">
                          @if($item->image)
                            <a href="{{ url('prompt', $item->image->slug ?: $item->image->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-block text-truncate">
                              {{ $item->image->title }}
                            </a>
                            <span class="badge bg-light text-muted border small">{{ $item->image->ai_model ?: 'Prompt' }}</span>
                          @else
                            <span class="text-muted">Prompt #{{ $item->image_id }}</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td class="text-center">{{ number_format($item->unique_viewers) }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->views_count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">No prompt views in this period.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Most Copied -->
    <div class="col-lg-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-clipboard-check text-success me-2"></i> Most Copied Prompts</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Prompt</th>
                  <th class="text-center">Unique Copiers</th>
                  <th class="text-end">Copies Count</th>
                </tr>
              </thead>
              <tbody>
                @forelse($mostCopied as $item)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($item->image && $item->image->thumbnail)
                          <img src="{{ Storage::url(config('path.thumbnail') . $item->image->thumbnail) }}" width="40" height="40" class="rounded me-2 object-fit-cover" />
                        @endif
                        <div class="text-truncate" style="max-width: 200px;">
                          @if($item->image)
                            <a href="{{ url('prompt', $item->image->slug ?: $item->image->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-block text-truncate">
                              {{ $item->image->title }}
                            </a>
                            <span class="badge bg-{{ $item->image->item_for_sale == 'sale' ? 'warning text-dark' : 'secondary' }} small">
                              {{ $item->image->item_for_sale == 'sale' ? 'Premium' : 'Free' }}
                            </span>
                          @else
                            <span class="text-muted">Prompt #{{ $item->image_id }}</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td class="text-center">{{ number_format($item->unique_copiers) }}</td>
                    <td class="text-end fw-bold text-success">{{ number_format($item->copies_count) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">No copies recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Real-time Prompt Copy Activity Stream -->
  <div class="card shadow-custom border-0">
    <div class="card-header bg-transparent border-0 pt-4 pb-2">
      <h5 class="card-title m-0 fw-light"><i class="bi bi-clock-history me-2 text-primary"></i> Prompt Copy Activity Stream</h5>
      <small class="text-muted">Chronological log of prompt copy events and guest auth-wall triggers</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small">
            <tr>
              <th>Date / Time</th>
              <th>Prompt</th>
              <th>User / Visitor</th>
              <th>Action</th>
              <th class="text-end">Result</th>
            </tr>
          </thead>
          <tbody>
            @forelse($copyStream as $evt)
              <tr>
                <td>
                  <span class="small fw-medium">{{ $evt->created_at ? $evt->created_at->format('M d, Y H:i') : '-' }}</span>
                </td>
                <td>
                  @if($evt->image)
                    <a href="{{ url('prompt', $evt->image->slug ?: $evt->image->id) }}" target="_blank" class="text-dark fw-medium text-decoration-none">
                      {{ $evt->image->title }}
                    </a>
                  @else
                    <span class="text-muted">Prompt #{{ $evt->image_id }}</span>
                  @endif
                </td>
                <td>
                  @if($evt->user)
                    <a href="{{ url($evt->user->username) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                      {{ $evt->user->username }}
                    </a>
                  @else
                    <span class="text-muted">Guest #{{ substr($evt->visitor_id, 0, 8) }}</span>
                  @endif
                </td>
                <td>
                  @if($evt->event_name == 'prompt_copy')
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Prompt Copied</span>
                  @else
                    <span class="badge bg-warning-subtle text-warning"><i class="bi bi-lock"></i> Guest Clicked Copy</span>
                  @endif
                </td>
                <td class="text-end">
                  @if($evt->event_name == 'prompt_copy')
                    <span class="text-success small fw-bold">Delivered</span>
                  @else
                    <span class="text-warning small fw-bold">Sign-up Prompt Shown</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No copy activity in this period.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($copyStream->hasPages())
      <div class="card-footer bg-transparent border-0 py-3">
        {{ $copyStream->appends(request()->all())->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
