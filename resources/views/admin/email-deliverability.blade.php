@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Email Deliverability &amp; Audience</span>

    <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-megaphone me-1"></i> Marketing Campaigns &rarr;
    </a>
    <a href="{{ url('panel/admin/email-deliverability?tab=guide') }}" class="btn btn-sm @if($tab === 'guide') btn-primary text-white @else btn-outline-primary @endif float-lg-end mt-1 mt-lg-0">
        <i class="bi-book-half me-1"></i> Deliverability Admin Guide
    </a>
</h5>

<div class="content">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2-circle me-1"></i> {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('errors.errors-forms')

    <!-- Cross-Section Contextual Link Card -->
    <div class="card border-0 mb-3 shadow-custom bg-body-tertiary">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex p-2 rounded-circle bg-primary-subtle text-primary border border-primary-subtle">
                    <i class="bi-info-circle fs-5"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-body">Email Deliverability, Audience &amp; Suppression Center</h6>
                    <small class="text-muted">
                        Manages your Amazon SES infrastructure, reputation firewalls, subscriber lists, and DNS authentication (Features 1–8).
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-dark">
                    <i class="bi-broadcast me-1"></i> Go to Marketing Campaigns &amp; Lifecycle Drips &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Unified Deliverability Synchronization & Protection Banner -->
    <div class="card border-0 mb-4 shadow-sm border-start border-4 border-primary bg-light">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex p-2 rounded-circle bg-white text-primary shadow-sm">
                    <i class="bi-arrow-repeat fs-5" id="syncBannerIcon"></i>
                </span>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h6 class="mb-0 fw-bold text-dark">Unified Email Suppression &amp; Deliverability Sync</h6>
                        @if($lastSync && $lastSync->status === 'completed')
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi-check-circle me-1"></i> Synchronized</span>
                        @elseif($lastSync && $lastSync->status === 'running')
                            <span class="badge bg-warning-subtle text-warning border"><span class="spinner-border spinner-border-sm me-1"></span> In Progress...</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Ready</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        <strong>Last Sync:</strong> {{ $lastSync ? $lastSync->created_at->format('M d, Y H:i') : 'Never' }} 
                        &bull; <strong>Next Run:</strong> Daily at 03:00 AM (Automated Cron)
                        &bull; <strong>Protected Channels:</strong> <code>auth.imaginebuddy.com</code> (Transactional) &amp; <code>updates.imaginebuddy.com</code> (Marketing)
                        &bull; <strong>Disposables Blocked:</strong> {{ number_format($suppressionsDisposable) }}
                    </small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-bold btn-sm shadow-sm px-3" id="syncNowBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="syncBtnSpinner" role="status" aria-hidden="true"></span>
                    <i class="bi-arrow-clockwise me-1" id="syncBtnIcon"></i>
                    <span id="syncBtnText">Sync Suppressions &amp; Audit Now</span>
                </button>
            </div>
        </div>
    </div>

    <div id="syncFeedbackAlert" class="alert d-none mb-4 alert-dismissible fade show" role="alert">
        <span id="syncFeedbackText"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Deliverability KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-bold">Deliverable Audience</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Zero-Cost</span>
                </div>
                <h3 class="fw-bold mb-1 text-body">{{ number_format($totalAudience) }}</h3>
                <small class="text-muted"><i class="bi-shield-check text-success me-1"></i> Pre-verified Google + Double Opt-in</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-custom border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-bold">Google OAuth Users</span>
                    <i class="bi-google text-primary fs-5"></i>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ number_format($googleVerifiedCount) }}</h3>
                <small class="text-muted">Opted-in registered accounts</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-custom border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-bold">Newsletter Subs</span>
                    <i class="bi-envelope-check text-info fs-5"></i>
                </div>
                <h3 class="fw-bold mb-1 text-info">{{ number_format($newsletterActiveCount) }}</h3>
                <small class="text-muted">Confirmed via Double Opt-In</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-custom border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-bold">Suppressed Emails</span>
                    <i class="bi-slash-circle text-danger fs-5"></i>
                </div>
                <h3 class="fw-bold mb-1 text-danger">{{ number_format($suppressionsTotal) }}</h3>
                <small class="text-muted">
                    {{ $suppressionsBounces }} bounces &bull; {{ $suppressionsComplaints }} complaints &bull; {{ $suppressionsDisposable }} disposable &bull; {{ $suppressionsUnsubscribes }} unsub
                </small>
            </div>
        </div>
    </div>

    <!-- Live Test & Actions Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-custom border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0 text-body"><i class="bi-send me-1 text-primary"></i> Send Live Test Weekly Photoshoot Drop</h6>
                    <a href="{{ url('panel/admin/email-deliverability?tab=guide#guide-feature-3') }}" class="badge bg-light text-secondary border text-decoration-none">
                        <i class="bi-question-circle me-1"></i> Test SPF/DKIM Guide
                    </a>
                </div>
                <p class="text-muted small mb-3">
                    Dispatches a real live preview of the weekly photoshoot digest email to your inbox through Amazon SES (<code>hello@updates.imaginebuddy.com</code>) to verify photoshoot cards, links, SPF/DKIM authentication, and spam score.
                </p>
                <form method="POST" action="{{ url('panel/admin/email-deliverability/test-send') }}" class="row g-2">
                    @csrf
                    <div class="col-sm-8">
                        <input type="email" name="test_email" class="form-control" placeholder="admin@imaginebuddy.com" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi-lightning-charge me-1"></i> Send Test Photoshoot Drop
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-custom border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0 text-body"><i class="bi-shield-slash me-1 text-danger"></i> Manually Block / Suppress Email</h6>
                    <a href="{{ url('panel/admin/email-deliverability?tab=guide#guide-feature-7') }}" class="badge bg-light text-secondary border text-decoration-none">
                        <i class="bi-question-circle me-1"></i> Firewall Guide
                    </a>
                </div>
                <p class="text-muted small mb-3">Preemptively prevent marketing emails from ever dispatching to a specific address or domain.</p>
                <form method="POST" action="{{ url('panel/admin/email-deliverability/suppressions') }}" class="row g-2">
                    @csrf
                    <div class="col-12">
                        <input type="email" name="email" class="form-control form-control-sm mb-2" placeholder="user@example.com" required>
                    </div>
                    <div class="col-sm-7">
                        <select name="reason" class="form-select form-select-sm">
                            <option value="admin_suppressed">Admin Suppressed</option>
                            <option value="hard_bounce">Hard Bounce</option>
                            <option value="complaint">Spam Complaint</option>
                            <option value="manual_unsubscribe">Manual Unsubscribe</option>
                            <option value="disposable">Disposable Domain</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi-plus-lg me-1"></i> Suppress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($tab === 'suppressions') active fw-bold @endif" href="{{ url('panel/admin/email-deliverability?tab=suppressions') }}">
                <i class="bi-slash-circle me-1"></i> Suppressions List ({{ $suppressionsTotal }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'subscribers') active fw-bold @endif" href="{{ url('panel/admin/email-deliverability?tab=subscribers') }}">
                <i class="bi-people me-1"></i> Newsletter Subscribers ({{ $newsletterActiveCount }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'sync-logs') active fw-bold @endif" href="{{ url('panel/admin/email-deliverability?tab=sync-logs') }}">
                <i class="bi-clock-history me-1"></i> Sync &amp; Audit Logs ({{ $syncLogs->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'guide') active fw-bold @endif" href="{{ url('panel/admin/email-deliverability?tab=guide') }}">
                <i class="bi-book-half me-1 text-primary"></i> Deliverability Admin Guide (Features 1–8)
            </a>
        </li>
    </ul>

    <!-- Tab 1: Suppressions Table -->
    @if($tab === 'suppressions')
    <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
            <!-- Contextual Guidance Banner -->
            <div class="alert alert-secondary border-0 bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi-shield-check text-success fs-5"></i>
                    <span class="small text-muted">
                        <strong>Automated Firewall Active:</strong> Hard bounces and spam complaints received via Amazon SNS webhooks are automatically added here and blocked from future broadcasts.
                    </span>
                </div>
                <a href="{{ url('panel/admin/email-deliverability?tab=guide#guide-feature-7') }}" class="btn btn-sm btn-outline-secondary py-0">
                    Learn How Suppression Works &rarr;
                </a>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ url('panel/admin/email-deliverability') }}" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="suppressions">
                        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search suppressed email...">
                        <select name="reason" class="form-select form-select-sm" style="max-width: 170px;">
                            <option value="">All Reasons</option>
                            <option value="hard_bounce" @if($reason === 'hard_bounce') selected @endif>Hard Bounce</option>
                            <option value="complaint" @if($reason === 'complaint') selected @endif>Complaint</option>
                            <option value="manual_unsubscribe" @if($reason === 'manual_unsubscribe') selected @endif>Unsubscribe</option>
                            <option value="disposable" @if($reason === 'disposable') selected @endif>Disposable Domain</option>
                            <option value="admin_suppressed" @if($reason === 'admin_suppressed') selected @endif>Admin Suppressed</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                        @if(!empty($q) || !empty($reason))
                            <a href="{{ url('panel/admin/email-deliverability?tab=suppressions') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Email</th>
                            <th>Reason</th>
                            <th>Channel Scope</th>
                            <th>Stream</th>
                            <th>Suppressed On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppressions as $item)
                        <tr>
                            <td class="fw-semibold text-body">{{ $item->email }}</td>
                            <td>
                                @if($item->reason === 'hard_bounce')
                                    <span class="badge bg-danger">Hard Bounce</span>
                                @elseif($item->reason === 'soft_bounce')
                                    <span class="badge bg-warning text-dark">Soft Bounce ({{ $item->soft_bounce_count }}/3)</span>
                                @elseif($item->reason === 'complaint')
                                    <span class="badge bg-warning text-dark">Complaint</span>
                                @elseif($item->reason === 'disposable')
                                    <span class="badge bg-danger">Disposable Domain</span>
                                @elseif($item->reason === 'manual_unsubscribe')
                                    <span class="badge bg-secondary">Unsubscribed</span>
                                @else
                                    <span class="badge bg-dark">Admin Suppressed</span>
                                @endif
                            </td>
                            <td>
                                @if($item->channel_scope === 'all')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" title="Blocked on all channels (Auth + Updates)">
                                        <i class="bi-slash-circle me-1"></i> All Channels
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle" title="Blocked on marketing updates only; transactional security emails permitted">
                                        <i class="bi-shield-check me-1"></i> Marketing Only
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->source_stream }}</span></td>
                            <td class="text-muted small">{{ $item->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ url('panel/admin/email-deliverability/suppressions/' . $item->id . '/delete') }}" onsubmit="return confirm('Unblock this email address? It will become eligible to receive emails again.');" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi-check-lg me-1"></i> Unblock
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi-check2-circle text-success fs-3 d-block mb-1"></i>
                                No suppressed emails found. Your deliverability health is pristine!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppressions)
            <div class="mt-3">
                {{ $suppressions->links() }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Tab 2: Newsletter Subscribers Table -->
    @if($tab === 'subscribers')
    <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
            <!-- Contextual Guidance Banner -->
            <div class="alert alert-secondary border-0 bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi-envelope-check text-info fs-5"></i>
                    <span class="small text-muted">
                        <strong>Double Opt-In Leads:</strong> Website visitors who submit the footer form receive an inbox confirmation link. Once verified, they automatically receive the 50+ Prompts Vault lead magnet.
                    </span>
                </div>
                <a href="{{ url('panel/admin/email-deliverability?tab=guide#guide-feature-5') }}" class="btn btn-sm btn-outline-secondary py-0">
                    Lead Magnet &amp; Double Opt-In Guide &rarr;
                </a>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-md-5">
                    <form method="GET" action="{{ url('panel/admin/email-deliverability') }}" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="subscribers">
                        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search subscriber email...">
                        <button type="submit" class="btn btn-sm btn-dark">Search</button>
                        @if(!empty($q))
                            <a href="{{ url('panel/admin/email-deliverability?tab=subscribers') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Source Page</th>
                            <th>IP Address</th>
                            <th>Confirmed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $sub)
                        <tr>
                            <td class="fw-semibold text-body">{{ $sub->email }}</td>
                            <td>
                                @if($sub->suppression)
                                    @if($sub->suppression->reason === 'hard_bounce')
                                        <span class="badge bg-danger" title="Hard Bounce (SES)">Hard Bounce</span>
                                    @elseif($sub->suppression->reason === 'soft_bounce')
                                        <span class="badge bg-warning text-dark" title="Soft Bounce">Soft Bounce</span>
                                    @elseif($sub->suppression->reason === 'complaint')
                                        <span class="badge bg-danger" title="Spam Complaint">Complaint</span>
                                    @elseif($sub->suppression->reason === 'disposable')
                                        <span class="badge bg-secondary" title="Disposable Domain">Disposable</span>
                                    @elseif($sub->suppression->reason === 'admin_suppressed')
                                        <span class="badge bg-dark" title="Admin Blocked">Admin Blocked</span>
                                    @else
                                        <span class="badge bg-secondary" title="Suppressed ({{ $sub->suppression->reason }})">Suppressed</span>
                                    @endif
                                @elseif($sub->status === 'subscribed')
                                    <span class="badge bg-success">Subscribed</span>
                                @elseif($sub->status === 'pending_verification')
                                    <span class="badge bg-warning text-dark">Pending Opt-In</span>
                                    @if($sub->reminder_sent_at)
                                        <span class="badge bg-light text-muted border ms-1" title="Reminder sent {{ $sub->reminder_sent_at->format('M d, H:i') }}">Reminder Sent</span>
                                    @endif
                                @elseif($sub->status === 'expired')
                                    <span class="badge bg-dark">Expired</span>
                                @else
                                    <span class="badge bg-secondary">Unsubscribed</span>
                                @endif
                            </td>
                            <td><code>{{ $sub->source_page ?: 'footer' }}</code></td>
                            <td class="small text-muted">{{ $sub->ip_address ?: 'N/A' }}</td>
                            <td class="text-muted small">
                                {{ $sub->confirmed_at ? $sub->confirmed_at->format('M d, Y H:i') : 'Pending' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No subscribers found matching query.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers)
            <div class="mt-3">
                {{ $subscribers->links() }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Tab 3: Sync & Reconciliation Logs -->
    @if($tab === 'sync-logs')
    <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
            <div class="alert alert-secondary border-0 bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi-shield-check text-primary fs-5"></i>
                    <span class="small text-muted">
                        <strong>Reconciliation Engine:</strong> Synchronizes bounce events from Amazon SES v2, checks threshold on soft bounces, and audits user accounts for disposable email addresses.
                    </span>
                </div>
            </div>

            <div class="table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Execution Time</th>
                            <th>Trigger</th>
                            <th>Status</th>
                            <th>Bounces Reconciled</th>
                            <th>Disposables Detected</th>
                            <th>Newly Suppressed</th>
                            <th>Duration</th>
                            <th>Notes / Warnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($syncLogs as $log)
                        <tr>
                            <td class="fw-semibold text-body">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                @if($log->triggered_by === 'admin')
                                    <span class="badge bg-primary-subtle text-primary border"><i class="bi-person me-1"></i> Admin ({{ $log->admin ? $log->admin->name : 'Manual' }})</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-dark border"><i class="bi-cpu me-1"></i> Automated Cron</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status === 'completed')
                                    <span class="badge bg-success"><i class="bi-check-circle me-1"></i> Completed</span>
                                @elseif($log->status === 'running')
                                    <span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1"></span> Running</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi-x-circle me-1"></i> Failed</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($log->bounces_synced) }}</td>
                            <td class="text-center">{{ number_format($log->disposables_detected) }}</td>
                            <td class="text-center fw-bold text-danger">{{ number_format($log->newly_suppressed) }}</td>
                            <td class="small text-muted">{{ $log->duration_seconds }}s</td>
                            <td class="small text-muted" style="max-width: 250px;">{{ $log->error_details ?: 'Clean execution' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No synchronization logs recorded yet. Click <strong>Sync Suppressions &amp; Audit Now</strong> above to run your first synchronization.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 3: Deliverability & Audience Admin Guide (Features 1 to 8) -->
    @if($tab === 'guide')
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-body p-lg-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-body">
                        <i class="bi-book-half text-primary me-2"></i> Deliverability, Infrastructure &amp; Audience Admin Guide
                    </h5>
                    <p class="text-muted small mb-0">
                        Practical reference manual explaining system mechanics, step-by-step procedures, best practices, and troubleshooting for Features 1 through 8.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-outline-dark">
                        <i class="bi-megaphone me-1"></i> Marketing Campaigns Guide (Features 9 &amp; 10) &rarr;
                    </a>
                </div>
            </div>

            <!-- Quick Jump Navigation Pills -->
            <div class="mb-4 d-flex flex-wrap gap-2">
                <a href="#guide-feature-1" class="btn btn-sm btn-light border text-body"><i class="bi-cloud-check me-1"></i> F1: SES Cloud Transport</a>
                <a href="#guide-feature-2" class="btn btn-sm btn-light border text-body"><i class="bi-layers me-1"></i> F2: 3-Tier Queues</a>
                <a href="#guide-feature-3" class="btn btn-sm btn-light border text-body"><i class="bi-shield-check me-1"></i> F3: SPF, DKIM, DMARC</a>
                <a href="#guide-feature-4" class="btn btn-sm btn-light border text-body"><i class="bi-fingerprint me-1"></i> F4: Consent &amp; Opt-In Audit</a>
                <a href="#guide-feature-5" class="btn btn-sm btn-light border text-body"><i class="bi-envelope-paper me-1"></i> F5: Newsletter Lead Magnet</a>
                <a href="#guide-feature-6" class="btn btn-sm btn-light border text-body"><i class="bi-key me-1"></i> F6: Hardened 2FA OTP</a>
                <a href="#guide-feature-7" class="btn btn-sm btn-light border text-body"><i class="bi-shield-slash me-1"></i> F7: Bounce &amp; Complaint Firewall</a>
                <a href="#guide-feature-8" class="btn btn-sm btn-light border text-body"><i class="bi-box-arrow-right me-1"></i> F8: 1-Click Unsubscribe</a>
            </div>

            <!-- Accordion of All 8 Deliverability Features -->
            <div class="accordion" id="deliverabilityGuideAccordion">

                <!-- FEATURE 1 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-1">
                    <h2 class="accordion-header" id="headingF1">
                        <button class="accordion-button fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF1" aria-expanded="true" aria-controls="collapseF1">
                            <i class="bi-cloud-check-fill text-primary me-2 fs-5"></i> Feature 1: Cloud Email Transport Engine (Amazon SES v2)
                        </button>
                    </h2>
                    <div id="collapseF1" class="accordion-collapse collapse show" aria-labelledby="headingF1" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Replaces traditional webmail or shared SMTP with an enterprise cloud API via Amazon SES in the <code>ap-south-1</code> (Mumbai) region. It isolates platform traffic into two separate subdomains:
                                <br>&bull; <strong><code>auth.imaginebuddy.com</code></strong>: Dedicated exclusively to transactional emails (2FA OTPs, password resets, account activations).
                                <br>&bull; <strong><code>updates.imaginebuddy.com</code></strong>: Dedicated to marketing campaigns, weekly prompt drops, and newsletters.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Prevents marketing emails from ever jeopardizing the deliverability of login codes or password resets. Removes the 500-email/day limits of Gmail and shared web hosting.<br>
                                <strong>When:</strong> Operates automatically 24/7 in the background for every outgoing email generated by the platform.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Navigate to <strong>Admin Panel &gt; Settings &gt; Email Settings</strong> (<code>/panel/admin/settings/email</code>).</li>
                                <li>Confirm system addresses: <code>noreply@auth.imaginebuddy.com</code> for auth and <code>hello@updates.imaginebuddy.com</code> for updates.</li>
                                <li>Changes are applied instantly with zero code modifications needed.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Never send bulk promotional emails from the <code>auth.</code> subdomain. The platform routes this automatically.</li>
                                <li>Ensure your AWS SES account remains in "Production Access" mode (pre-approved).</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Emails failing to send:</strong> If emails stop sending, check server error logs at <code>storage/logs/laravel.log</code>. Verify that outbound port 443 (HTTPS) to AWS SES endpoints is open on your web host.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 2 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-2">
                    <h2 class="accordion-header" id="headingF2">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF2" aria-expanded="false" aria-controls="collapseF2">
                            <i class="bi-layers-fill text-info me-2 fs-5"></i> Feature 2: Prioritized 3-Tier Delivery Queues (Delivery Asynchrony)
                        </button>
                    </h2>
                    <div id="collapseF2" class="accordion-collapse collapse" aria-labelledby="headingF2" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Decouples email sending from web requests. Web pages respond instantaneously (&lt;0.1s) while emails process asynchronously across three priority speed lanes:
                                <br>&bull; <strong><code>emails-urgent</code></strong>: 2FA OTPs, password resets (processed first).
                                <br>&bull; <strong><code>default</code></strong>: Image generations, payment receipts.
                                <br>&bull; <strong><code>emails-bulk</code></strong>: Marketing campaigns, prompt drops, digests.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Prevents page freezes when users click "Register" or "Login". Ensures urgent login codes jump to the front of the line even during a 10,000-subscriber marketing campaign.<br>
                                <strong>When:</strong> Handled automatically by the server scheduler every minute.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Runs automatically via the server cron command: <code>* * * * * php artisan schedule:run</code>.</li>
                                <li>The worker command executed is: <code>php artisan queue:work --queue=emails-urgent,default,emails-bulk --stop-when-empty --tries=3 --timeout=120</code>.</li>
                                <li>Administrators can test throughput anytime using the "Send Test Drop" button on this page.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Keep the 120-second worker timeout to prevent hung processes from consuming memory.</li>
                                <li>Ensure the server cron job is active in your hosting control panel (hPanel/cPanel).</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Emails stuck in queue:</strong> If emails are queued but not received, check if the cron job is active on your server. Run <code>php artisan queue:restart</code> in terminal to clear any hung workers.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 3 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-3">
                    <h2 class="accordion-header" id="headingF3">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF3" aria-expanded="false" aria-controls="collapseF3">
                            <i class="bi-shield-fill-check text-success me-2 fs-5"></i> Feature 3: Domain Authentication &amp; Reputation Guard (SPF, DKIM, DMARC)
                        </button>
                    </h2>
                    <div id="collapseF3" class="accordion-collapse collapse" aria-labelledby="headingF3" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Cryptographically signs and authenticates all outgoing emails to prove they legitimately originate from Imagine Buddy:
                                <br>&bull; <strong>DKIM:</strong> 2048-bit digital signature on every email header via 3 CNAME DNS records per subdomain.
                                <br>&bull; <strong>SPF:</strong> Authorizes Amazon SES to send mail on behalf of <code>mail.auth</code> and <code>mail.updates</code>.
                                <br>&bull; <strong>DMARC (<code>p=quarantine</code>):</strong> Instructs Gmail and Yahoo to quarantine spoofed or forged emails.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Mandatory since February 2024 by Google and Yahoo for inbox delivery. Prevents emails from landing in Spam.<br>
                                <strong>When:</strong> Configured on Cloudflare DNS and automatically verified on every email sent.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Use the <strong>Send Live Test Weekly Prompt Drop</strong> box at the top of this page.</li>
                                <li>Send a test to your personal Gmail address.</li>
                                <li>Open the email in Gmail, click the three dots, and select <strong>"Show Original"</strong>.</li>
                                <li>Verify that SPF, DKIM, and DMARC all show green <strong>PASS</strong> status.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>In Cloudflare DNS, always leave the Easy-DKIM CNAME records set to <strong>DNS Only</strong> (Grey Cloud icon). Never enable Cloudflare Proxy for mail DNS records.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Gmail shows "via amazonses.com":</strong> This means the Custom MAIL FROM MX record is missing or proxied. Verify that <code>mail.updates.imaginebuddy.com</code> points to <code>feedback-smtp.ap-south-1.amazonses.com</code>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 4 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-4">
                    <h2 class="accordion-header" id="headingF4">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF4" aria-expanded="false" aria-controls="collapseF4">
                            <i class="bi-fingerprint text-warning me-2 fs-5"></i> Feature 4: Consent &amp; Opt-In Audit Trail
                        </button>
                    </h2>
                    <div id="collapseF4" class="accordion-collapse collapse" aria-labelledby="headingF4" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Captures and preserves immutable legal proof of customer marketing consent:
                                <br>&bull; <strong>Standard Registration:</strong> Explicit opt-in checkbox on <code>/register</code>.
                                <br>&bull; <strong>Google OAuth Login:</strong> Legally binding pre-click consent notice.
                                <br>&bull; <strong>Audit Log:</strong> Stores <code>marketing_opt_in</code>, timestamp <code>marketing_opt_in_at</code>, and client IP <code>marketing_opt_in_ip</code>.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Provides zero-cost audience growth by safely utilizing pre-verified Google accounts (1,794+ users) while ensuring strict GDPR, CAN-SPAM, and CCPA legal compliance.<br>
                                <strong>When:</strong> Captured automatically on every signup and checked before any marketing campaign dispatches.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Navigate to <strong>Admin Panel &gt; Members</strong> (<code>/panel/admin/members</code>) to inspect user verification.</li>
                                <li>When composing marketing campaigns, select the audience segment (e.g. "All Deliverable Audience"). The system automatically filters for only opted-in, unsuppressed users.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Never manually force a user's opt-in status without their explicit consent.</li>
                                <li>Always honor customer unsubscribe requests promptly.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Customer disputes consent:</strong> You can pull up their signup timestamp and IP address from the database to prove compliance.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 5 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-5">
                    <h2 class="accordion-header" id="headingF5">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF5" aria-expanded="false" aria-controls="collapseF5">
                            <i class="bi-envelope-paper-fill text-danger me-2 fs-5"></i> Feature 5: High-Converting Newsletter &amp; Lead Magnet Capture
                        </button>
                    </h2>
                    <div id="collapseF5" class="accordion-collapse collapse" aria-labelledby="headingF5" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Captures visitor emails directly from your website footer without requiring account registration. Executes a secure **Double Opt-In** loop:
                                <br>&bull; Visitor submits their email &rarr; Confirmation link sent &rarr; Visitor clicks link &rarr; System marks them as <code>subscribed</code> and auto-delivers the <strong>50+ Master Prompts Vault</strong> download.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Turns casual website visitors into active email leads. Double opt-in ensures no typos, fake addresses, or spam traps enter your database.<br>
                                <strong>When:</strong> Operates 24/7 on all public pages.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Click the <strong>Newsletter Subscribers</strong> tab on this page.</li>
                                <li>View active subscribers, confirmation states (<code>Subscribed</code> vs <code>Pending Opt-In</code>), and signup source page.</li>
                                <li>Confirmed subscribers are automatically included when targeting "Newsletter Subscribers" or "All Deliverable Audience" in campaigns.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Periodically refresh the prompts featured in the 50+ Prompts Vault email to keep lead engagement high.</li>
                                <li>Never send bulk promotional blasts to subscribers who remain in `Pending Opt-In` status.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Subscriber didn't receive confirmation email:</strong> Check the <strong>Suppressions List</strong> tab to verify their address isn't blocked. If suppressed, click "Unblock".
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 6 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-6">
                    <h2 class="accordion-header" id="headingF6">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF6" aria-expanded="false" aria-controls="collapseF6">
                            <i class="bi-key-fill text-secondary me-2 fs-5"></i> Feature 6: Hardened Two-Factor Authentication (2FA OTP)
                        </button>
                    </h2>
                    <div id="collapseF6" class="accordion-collapse collapse" aria-labelledby="headingF6" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Secures accounts and email sending quotas with banking-grade OTP controls:
                                <br>&bull; 6-digit cryptographic code via <code>random_int(100000, 999999)</code>.
                                <br>&bull; 10-minute expiration (<code>expires_at</code>).
                                <br>&bull; Brute-force lockout (invalidated after 5 failed attempts).
                                <br>&bull; 60-second cooldown between resend requests.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Prevents account takeovers, credential stuffing, and bot scripts from flooding your Amazon SES quota with automated login emails.<br>
                                <strong>When:</strong> Triggered whenever a 2FA-enabled customer or administrator logs in.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>All administrators should enable 2FA in their account settings.</li>
                                <li>If a user reports being locked out, confirm their email is valid. After 60 seconds, they can request a fresh code on the login page.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Always enforce 2FA for all administrative accounts.</li>
                                <li>2FA OTP emails route through <code>emails-urgent</code> and <code>auth.imaginebuddy.com</code> for instant 1–3 second delivery.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>"Code has expired":</strong> Codes expire after 10 minutes. The user must click "Resend Code" on the verification screen.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 7 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-7">
                    <h2 class="accordion-header" id="headingF7">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF7" aria-expanded="false" aria-controls="collapseF7">
                            <i class="bi-shield-slash-fill text-danger me-2 fs-5"></i> Feature 7: Automated Bounce &amp; Complaint Suppression Firewall (AWS SNS)
                        </button>
                    </h2>
                    <div id="collapseF7" class="accordion-collapse collapse" aria-labelledby="headingF7" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Autonomous deliverability firewall connected to Amazon SES via SNS webhooks (<code>POST /api/webhooks/aws-ses</code>):
                                <br>&bull; <strong>Hard Bounces:</strong> Automatically added to <code>email_suppressions</code> (reason: <code>hard_bounce</code>).
                                <br>&bull; <strong>Spam Complaints:</strong> Automatically suppressed (reason: <code>complaint</code>).
                                <br>&bull; <strong>Account Sync:</strong> Instantly deactivates <code>marketing_opt_in = 0</code> on the user profile.
                                <br>&bull; <strong>Pre-Send Blocker:</strong> Every outgoing marketing email checks the suppression list before sending.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Protects your Amazon SES account from suspension. AWS strictly requires bounce rates below 5% and complaint rates below 0.1%. This firewall ensures dead or complaining emails are never messaged twice.<br>
                                <strong>When:</strong> Operates 24/7 automatically on every webhook notification.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>Review the KPI counters at the top of this page (Hard Bounces, Complaints, Unsubscribes).</li>
                                <li>To find a specific address, search the <strong>Suppressions List</strong> tab.</li>
                                <li>To unblock a customer whose inbox is fixed, click the green <strong>Unblock</strong> button.</li>
                                <li>To manually block an address, use the <strong>Manually Block / Suppress Email</strong> form above.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Never unblock an address that registered a hard bounce (non-existent mailbox) or spam complaint unless the owner confirms in writing that the problem is resolved.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>Webhook not receiving events:</strong> In AWS SNS Console, ensure the topic subscription for Bounces &amp; Complaints is pointing to <code>https://imaginebuddy.com/api/webhooks/aws-ses</code> and shows status "Confirmed".
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FEATURE 8 -->
                <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-8">
                    <h2 class="accordion-header" id="headingF8">
                        <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF8" aria-expanded="false" aria-controls="collapseF8">
                            <i class="bi-box-arrow-right text-dark me-2 fs-5"></i> Feature 8: RFC 8058 One-Click Unsubscribe Engine
                        </button>
                    </h2>
                    <div id="collapseF8" class="accordion-collapse collapse" aria-labelledby="headingF8" data-bs-parent="#deliverabilityGuideAccordion">
                        <div class="accordion-body">
                            <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                            <p class="small text-muted mb-3">
                                Injects RFC 8058 compliant headers into every marketing dispatch:
                                <br>&bull; <code>List-Unsubscribe: &lt;https://imaginebuddy.com/newsletter/unsubscribe/{token}&gt;</code>
                                <br>&bull; <code>List-Unsubscribe-Post: List-Unsubscribe=One-Click</code>
                                <br>Renders a prominent, native "Unsubscribe" button at the top of Gmail and Yahoo email apps, allowing users to opt out in 1 click without passwords.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                            <p class="small text-muted mb-3">
                                <strong>Why:</strong> Mandatory since February 2024 for all bulk senders. When users cannot find an unsubscribe button, they click "Report as Spam". A 1-click unsubscribe saves your domain reputation.<br>
                                <strong>When:</strong> Handled automatically on every broadcast and automated drip.
                            </p>

                            <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                            <ol class="small text-muted mb-3 ps-3">
                                <li>The headers are automatically injected into all marketing emails.</li>
                                <li>When clicked, the webhook/endpoint marks the user as suppressed (<code>reason: manual_unsubscribe</code>) and turns off their opt-in flag.</li>
                                <li>Review unsubscribed recipients anytime in the <strong>Suppressions List</strong> tab.</li>
                            </ol>

                            <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li>Always maintain the visible unsubscribe link in your email footers as well (built into all 4 template presets).</li>
                                <li>Never attempt to conceal or remove unsubscribe links.</li>
                            </ul>

                            <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                            <p class="small text-muted mb-0">
                                <strong>User says they unsubscribed but still got mail:</strong> Verify if they have another account registered under a secondary email address.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Cross-Link to Marketing Section -->
            <div class="alert alert-primary border-0 bg-primary-subtle p-3 mt-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h6 class="fw-bold text-primary mb-1">Looking for Campaign Composer &amp; Lifecycle Drip Guides?</h6>
                    <small class="text-body-secondary">
                        Features 9 (Admin Marketing Studio &amp; Kill-Switch) and 10 (Automated Lifecycle Drips) are located under Marketing Campaigns.
                    </small>
                </div>
                <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-primary text-white">
                    <i class="bi-megaphone me-1"></i> Open Marketing Campaigns Guide &rarr;
                </a>
            </div>

        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const syncBtn = document.getElementById('syncNowBtn');
    const syncSpinner = document.getElementById('syncBtnSpinner');
    const syncIcon = document.getElementById('syncBtnIcon');
    const syncText = document.getElementById('syncBtnText');
    const feedbackAlert = document.getElementById('syncFeedbackAlert');
    const feedbackText = document.getElementById('syncFeedbackText');

    if (syncBtn) {
        syncBtn.addEventListener('click', function () {
            syncBtn.disabled = true;
            syncSpinner.classList.remove('d-none');
            syncIcon.classList.add('d-none');
            syncText.textContent = 'Syncing Deliverability & Suppressions...';

            fetch("{{ route('email_deliverability.sync') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    feedbackAlert.className = 'alert alert-success mb-4 alert-dismissible fade show';
                    feedbackText.innerHTML = `<strong>✅ Deliverability Synchronization Completed (${data.duration_seconds}s):</strong> ` +
                        `Reconciled ${data.bounces_synced} bounces, flagged ${data.disposables_detected} disposable addresses, and updated ${data.newly_suppressed} suppression records.`;
                    feedbackAlert.classList.remove('d-none');
                    setTimeout(() => { window.location.reload(); }, 1800);
                } else {
                    feedbackAlert.className = 'alert alert-danger mb-4 alert-dismissible fade show';
                    feedbackText.innerHTML = `<strong>❌ Synchronization Error:</strong> ${data.error || 'Unknown error'}`;
                    feedbackAlert.classList.remove('d-none');
                    syncBtn.disabled = false;
                    syncSpinner.classList.add('d-none');
                    syncIcon.classList.remove('d-none');
                    syncText.textContent = 'Sync Suppressions & Audit Now';
                }
            })
            .catch(err => {
                feedbackAlert.className = 'alert alert-danger mb-4 alert-dismissible fade show';
                feedbackText.innerHTML = `<strong>❌ Network Error:</strong> Unable to connect to server.`;
                feedbackAlert.classList.remove('d-none');
                syncBtn.disabled = false;
                syncSpinner.classList.add('d-none');
                syncIcon.classList.remove('d-none');
                syncText.textContent = 'Sync Suppressions & Audit Now';
            });
        });
    }
});
</script>
@endsection
