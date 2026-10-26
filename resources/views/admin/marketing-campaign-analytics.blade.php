@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/email-marketing/campaigns') }}">Campaigns</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Campaign Analytics &amp; Delivery Log</span>

    <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-arrow-left me-1"></i> Back to Campaigns
    </a>

    @if($campaign->failed_count > 0)
    <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/retry-failed') }}" class="d-inline float-lg-end mt-1 mt-lg-0 ms-2" onsubmit="return confirm('Re-queue delivery for {{ $campaign->failed_count }} failed recipients?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="bi-arrow-repeat me-1"></i> Retry Failed ({{ number_format($campaign->failed_count) }})
        </button>
    </form>
    @endif
</h5>

<div class="content">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Campaign Header Card -->
    <div class="card shadow-custom border-0 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-bold mb-0 text-dark">{{ $campaign->name }}</h4>
                    <span id="campaignStatusBadge">
                        @if($campaign->status === 'sent')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Sent</span>
                        @elseif($campaign->status === 'sending')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1">
                                <span class="spinner-border spinner-border-sm me-1"></span> Sending... (<span id="liveProgressText">{{ $campaign->progress_percentage }}%</span>)
                            </span>
                        @elseif($campaign->status === 'cancelled')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                <i class="bi-pause-circle me-1"></i> Paused / Halted (Kill-Switch)
                            </span>
                        @elseif($campaign->status === 'draft')
                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-1">Draft</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border px-3 py-1">{{ ucfirst($campaign->status) }}</span>
                        @endif
                    </span>
                </div>
                <p class="text-muted mb-0 small">
                    <i class="bi-envelope me-1"></i> <strong>Subject:</strong> {{ $campaign->subject }}
                    &bull; <i class="bi-people me-1"></i> <strong>Segment:</strong> <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', ucfirst($campaign->target_segment)) }}</span>
                    &bull; <i class="bi-clock me-1"></i> <strong>Dispatched:</strong> {{ $campaign->sent_at ? $campaign->sent_at->format('M d, Y H:i:s') : 'N/A' }}
                </p>
            </div>

            <div class="text-end d-flex align-items-center gap-2">
                @if($campaign->status === 'cancelled' && !App\Models\AdminSettings::isBulkEmailKillSwitchActive() && $campaign->processed_count < $campaign->recipients_count)
                <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/resume') }}" onsubmit="return confirm('Resume sending remaining unsent recipients for this campaign?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi-play-fill me-1"></i> Resume Send
                    </button>
                </form>
                @endif
                @if($campaign->status === 'sending')
                <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/pause') }}" onsubmit="return confirm('Pause sending this campaign?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning text-dark">
                        <i class="bi-pause-fill me-1"></i> Pause
                    </button>
                </form>
                @endif
                @if(in_array($campaign->status, ['cancelled', 'sending']) && $campaign->processed_count > 0 && $campaign->processed_count < $campaign->recipients_count)
                <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/mark-completed') }}" onsubmit="return confirm('Mark this broadcast as complete at {{ number_format($campaign->processed_count) }} recipients?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="bi-check-all me-1"></i> Mark Done
                    </button>
                </form>
                @endif
                <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/edit') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi-pencil-square me-1"></i> Edit / View Content
                </a>
            </div>
        </div>

        <!-- Real-Time Progress Bar -->
        <div class="mt-3">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Sending Progress (<span id="liveProcessedCount">{{ number_format($campaign->processed_count) }}</span> / <span id="liveTotalCount">{{ number_format($campaign->recipients_count) }}</span> recipients)</span>
                <span class="fw-bold text-dark" id="liveProgressPercentBadge">{{ $campaign->progress_percentage }}%</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped @if($campaign->status === 'sending') progress-bar-animated bg-primary @else bg-success @endif" 
                     role="progressbar" 
                     id="liveProgressBar" 
                     style="width: {{ $campaign->progress_percentage }}%;" 
                     aria-valuenow="{{ $campaign->progress_percentage }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Metrics Overview Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Intended Audience -->
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Intended Audience</span>
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi-people-fill"></i></span>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ number_format($campaign->recipients_count) }}</h3>
                <small class="text-muted">Target Segment Size</small>
            </div>
        </div>

        <!-- 2. Delivered Rate -->
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Delivered</span>
                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi-check2-circle"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-0">
                    <h3 class="fw-bold mb-0 text-dark" id="metricDelivered">{{ number_format($campaign->delivered_count) }}</h3>
                    <span class="text-success small fw-semibold" id="metricDeliveredRate">({{ $campaign->delivery_rate }}%)</span>
                </div>
                <small class="text-muted">SES Delivery Confirmation</small>
            </div>
        </div>

        <!-- 3. Unique Opens -->
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Unique Opens</span>
                    <span class="badge bg-info-subtle text-info p-2 rounded-circle"><i class="bi-envelope-open-fill"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-0">
                    <h3 class="fw-bold mb-0 text-dark" id="metricOpens">{{ number_format($campaign->unique_opens_count) }}</h3>
                    <span class="text-info small fw-semibold" id="metricOpenRate">({{ $campaign->open_rate }}%)</span>
                </div>
                <small class="text-muted">Total Opens: <strong id="metricTotalOpens">{{ number_format($campaign->total_opens_count) }}</strong></small>
            </div>
        </div>

        <!-- 4. Unique Clicks -->
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Unique Clicks</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis p-2 rounded-circle"><i class="bi-cursor-fill"></i></span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-0">
                    <h3 class="fw-bold mb-0 text-dark" id="metricClicks">{{ number_format($campaign->unique_clicks_count) }}</h3>
                    <span class="text-warning-emphasis small fw-semibold" id="metricClickRate">({{ $campaign->click_rate }}%)</span>
                </div>
                <small class="text-muted">Total Clicks: <strong id="metricTotalClicks">{{ number_format($campaign->total_clicks_count) }}</strong></small>
            </div>
        </div>
    </div>

    <!-- Second Row: Bounces, Complaints, Failures -->
    <div class="row g-3 mb-4">
        <!-- Bounces -->
        <div class="col-md-4">
            <div class="card shadow-custom border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold text-uppercase">Bounces</span>
                    <i class="bi-exclamation-octagon text-danger fs-5"></i>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h4 class="fw-bold mb-0 text-danger" id="metricBounces">{{ number_format($campaign->bounced_count) }}</h4>
                    <span class="small text-muted" id="metricBounceRate">({{ $campaign->bounce_rate }}%)</span>
                </div>
                <small class="text-muted mt-1">Automatic suppression list synced</small>
            </div>
        </div>

        <!-- Complaints -->
        <div class="col-md-4">
            <div class="card shadow-custom border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold text-uppercase">Spam Complaints</span>
                    <i class="bi-shield-slash text-warning fs-5"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark" id="metricComplaints">{{ number_format($campaign->complained_count) }}</h4>
                <small class="text-muted mt-1">SES Feedback Loop notifications</small>
            </div>
        </div>

        <!-- Failures -->
        <div class="col-md-4">
            <div class="card shadow-custom border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold text-uppercase">Queue / Delivery Errors</span>
                    <i class="bi-x-circle text-secondary fs-5"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark" id="metricFailed">{{ number_format($campaign->failed_count) }}</h4>
                <small class="text-muted mt-1">Jobs interrupted or dropped</small>
            </div>
        </div>
    </div>

    <!-- Top Clicked Links (if any) -->
    @if($topClicks && $topClicks->count() > 0)
    <div class="card shadow-custom border-0 p-4 mb-4">
        <h6 class="fw-bold mb-3 text-dark"><i class="bi-link-45deg text-primary me-1"></i> Top Clicked Links</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Destination URL</th>
                        <th class="text-center" style="width: 140px;">Unique Clicks</th>
                        <th class="text-center" style="width: 140px;">Total Clicks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topClicks as $link)
                    <tr>
                        <td class="text-truncate" style="max-width: 480px;">
                            <a href="{{ $link->target_url }}" target="_blank" class="text-decoration-underline text-dark fw-medium small">
                                {{ $link->target_url }}
                            </a>
                        </td>
                        <td class="text-center fw-bold">{{ number_format($link->unique_clicks) }}</td>
                        <td class="text-center text-muted">{{ number_format($link->clicks_count) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recipient Delivery Log Table -->
    <div class="card shadow-custom border-0 p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi-card-list text-primary me-1"></i> Recipient Delivery &amp; Engagement Log
            </h6>

            <!-- Search Form -->
            <form method="GET" action="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search recipient email..." value="{{ $search }}" style="width: 220px;">
                <button type="submit" class="btn btn-sm btn-outline-dark"><i class="bi-search"></i></button>
                @if(!empty($search))
                <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => $statusFilter]) }}" class="btn btn-sm btn-light border"><i class="bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        <!-- Filter Pills Bar -->
        <div class="d-flex flex-wrap gap-1 mb-3">
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'all', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'all') btn-dark @else btn-outline-secondary @endif">
                All ({{ number_format($campaign->recipients()->count()) }})
            </a>
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'delivered', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'delivered') btn-success @else btn-outline-success @endif">
                Delivered ({{ number_format($campaign->delivered_count) }})
            </a>
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'opened', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'opened') btn-info text-white @else btn-outline-info @endif">
                Opened ({{ number_format($campaign->unique_opens_count) }})
            </a>
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'clicked', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'clicked') btn-warning @else btn-outline-warning @endif">
                Clicked ({{ number_format($campaign->unique_clicks_count) }})
            </a>
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'hard_bounced', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'hard_bounced') btn-danger @else btn-outline-danger @endif">
                Bounced ({{ number_format($campaign->bounced_count) }})
            </a>
            <a href="{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/analytics', ['status' => 'failed', 'q' => $search]) }}" 
               class="btn btn-xs @if($statusFilter === 'failed') btn-secondary @else btn-outline-secondary @endif">
                Failed ({{ number_format($campaign->failed_count) }})
            </a>
        </div>

        <!-- Recipients Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Delivered</th>
                        <th>Engagement</th>
                        <th>SES Message ID / Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recipients as $r)
                    <tr>
                        <td>
                            <strong class="d-block text-dark">{{ $r->recipient_email }}</strong>
                            <small class="text-muted">{{ $r->recipient_name ?: 'Creator' }}</small>
                        </td>
                        <td>
                            @if($r->status === 'delivered')
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi-check2"></i> Delivered</span>
                            @elseif($r->status === 'sent')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi-send"></i> Sent</span>
                            @elseif($r->status === 'queued')
                                <span class="badge bg-light text-muted border"><i class="bi-clock"></i> Queued</span>
                            @elseif($r->status === 'sending')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><span class="spinner-border spinner-border-sm me-1"></span> Sending</span>
                            @elseif($r->status === 'hard_bounced')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi-exclamation-triangle"></i> Hard Bounce</span>
                            @elseif($r->status === 'soft_bounced')
                                <span class="badge bg-warning-subtle text-warning-emphasis border"><i class="bi-exclamation-circle"></i> Soft Bounce</span>
                            @elseif($r->status === 'complained')
                                <span class="badge bg-dark-subtle text-dark border"><i class="bi-shield-slash"></i> Complaint</span>
                            @elseif($r->status === 'failed')
                                <span class="badge bg-danger text-white"><i class="bi-x"></i> Failed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $r->sent_at ? $r->sent_at->format('M d, H:i') : '-' }}</td>
                        <td class="small text-muted">{{ $r->delivered_at ? $r->delivered_at->format('M d, H:i') : '-' }}</td>
                        <td>
                            @if($r->open_count > 0)
                                <span class="badge bg-info-subtle text-info me-1" title="Opened: {{ $r->opened_at ? $r->opened_at->format('M d H:i') : '' }}">
                                    <i class="bi-eye"></i> {{ $r->open_count }}x
                                </span>
                            @endif
                            @if($r->click_count > 0)
                                <span class="badge bg-warning-subtle text-warning-emphasis" title="Clicked: {{ $r->clicked_at ? $r->clicked_at->format('M d H:i') : '' }}">
                                    <i class="bi-cursor"></i> {{ $r->click_count }}x
                                </span>
                            @endif
                            @if($r->open_count == 0 && $r->click_count == 0)
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="small">
                            @if(!empty($r->ses_message_id))
                                <code class="text-truncate d-inline-block text-muted" style="max-width: 180px;" title="{{ $r->ses_message_id }}">
                                    {{ Str::limit($r->ses_message_id, 24) }}
                                </code>
                            @elseif(!empty($r->error_message))
                                <span class="text-danger small" title="{{ $r->error_message }}">{{ Str::limit($r->error_message, 40) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No recipient records found for this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $recipients->links() }}
        </div>
    </div>
</div>

@if($campaign->status === 'sending' && !App\Models\AdminSettings::isBulkEmailKillSwitchActive())
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pollInterval = setInterval(function () {
        fetch("{{ url('panel/admin/email-marketing/campaigns/' . $campaign->id . '/progress') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('liveProcessedCount').textContent = new Intl.NumberFormat().format(data.processed_count);
                document.getElementById('liveTotalCount').textContent = new Intl.NumberFormat().format(data.recipients_count);
                document.getElementById('liveProgressPercentBadge').textContent = data.progress_percentage + '%';

                const progressBar = document.getElementById('liveProgressBar');
                progressBar.style.width = data.progress_percentage + '%';
                progressBar.setAttribute('aria-valuenow', data.progress_percentage);

                if (document.getElementById('liveProgressText')) {
                    document.getElementById('liveProgressText').textContent = data.progress_percentage + '%';
                }

                if (data.is_completed || data.status !== 'sending') {
                    clearInterval(pollInterval);
                    window.location.reload();
                }
            })
            .catch(() => {});
    }, 3000);
});
</script>
@endif
@endsection
