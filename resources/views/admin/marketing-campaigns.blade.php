@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Marketing Campaigns &amp; Broadcasts</span>

    <a href="{{ url('panel/admin/email-marketing/campaigns/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-plus-lg me-1"></i> Create New Campaign
    </a>
    <a href="{{ url('panel/admin/email-deliverability') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-shield-check me-1"></i> Deliverability &amp; Audience &rarr;
    </a>
    <button type="button" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0" data-bs-toggle="collapse" data-bs-target="#marketingAdminGuide" aria-expanded="false" aria-controls="marketingAdminGuide">
        <i class="bi-book-half me-1"></i> Marketing Admin Guide (Features 9 &amp; 10)
    </button>
</h5>

<div class="content">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2-circle me-1"></i> {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info_message'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-1"></i> {{ session('info_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cross-Section Contextual Link Card -->
    <div class="card border-0 mb-4 shadow-custom bg-body-tertiary">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex p-2 rounded-circle bg-primary-subtle text-primary border border-primary-subtle">
                    <i class="bi-info-circle fs-5"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-body">Marketing Campaigns Studio &amp; Automated Drips Center</h6>
                    <small class="text-muted">
                        Compose broadcasts with CKEditor &amp; presets, manage the Emergency Kill-Switch, and review automated lifecycle drip schedules (Features 9 &amp; 10).
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#marketingAdminGuide">
                    <i class="bi-book-half me-1"></i> View Marketing Guide
                </button>
                <a href="{{ url('panel/admin/email-deliverability') }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi-shield-check me-1"></i> Deliverability &amp; Audience (Features 1–8) &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Collapsible Marketing Admin Guide (Features 9 & 10) -->
    <div class="collapse mb-4" id="marketingAdminGuide">
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1 text-body">
                            <i class="bi-book-half text-primary me-2"></i> Marketing Studio &amp; Lifecycle Automations Admin Guide
                        </h5>
                        <p class="text-muted small mb-0">
                            Practical documentation explaining campaign creation, visual presets, device previewers, the Emergency Kill-Switch, and hands-off automated drips.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url('panel/admin/email-deliverability?tab=guide') }}" class="btn btn-sm btn-outline-dark">
                            <i class="bi-shield-check me-1"></i> Deliverability Guide (Features 1–8) &rarr;
                        </a>
                        <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#marketingAdminGuide">
                            <i class="bi-x-lg"></i> Close Guide
                        </button>
                    </div>
                </div>

                <div class="accordion" id="marketingGuideAccordion">

                    <!-- FEATURE 9 -->
                    <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-9">
                        <h2 class="accordion-header" id="headingF9">
                            <button class="accordion-button fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF9" aria-expanded="true" aria-controls="collapseF9">
                                <i class="bi-palette-fill text-primary me-2 fs-5"></i> Feature 9: Admin Marketing Studio, Visual Composer &amp; Emergency Kill-Switch
                            </button>
                        </h2>
                        <div id="collapseF9" class="accordion-collapse collapse show" aria-labelledby="headingF9" data-bs-parent="#marketingGuideAccordion">
                            <div class="accordion-body">
                                <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                                <p class="small text-muted mb-3">
                                    A self-hosted, visual marketing studio embedded directly into your admin panel:
                                    <br>&bull; <strong>Visual CKEditor:</strong> Rich-text composer with support for headings, buttons, images, and dynamic tags (<code>{name}</code>, <code>{email}</code>, <code>{vaultUrl}</code>).
                                    <br>&bull; <strong>4 1-Click Presets:</strong> AI Prompts Showcase, Product Announcement, Promo Discount/Sale, and Minimalist Letter.
                                    <br>&bull; <strong>Dual-Device Preview:</strong> Real-time toggle between Desktop (600px) and Mobile (375px) responsive frames.
                                    <br>&bull; <strong>Audience Segment Estimator:</strong> Live count of eligible recipients with sample previews.
                                    <br>&bull; <strong>Emergency Kill-Switch:</strong> Top-level safety control to immediately halt marketing broadcasts.
                                </p>

                                <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                                <p class="small text-muted mb-3">
                                    <strong>Why:</strong> Eliminates monthly subscription fees for external email services (Mailchimp/Klaviyo). Enables administrators to compose and broadcast responsive emails with zero coding.<br>
                                    <strong>When:</strong> Use whenever sending new model announcements, prompt showcases, or seasonal discounts. Use the Emergency Kill-Switch if an accidental send occurs.
                                </p>

                                <h6 class="fw-bold text-primary mb-2">3. How to Use It Step by Step</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border h-100">
                                            <strong class="d-block text-body mb-2"><i class="bi-send me-1 text-primary"></i> Creating &amp; Broadcasting a Campaign:</strong>
                                            <ol class="small text-muted ps-3 mb-0">
                                                <li>Click <strong>Create New Campaign</strong> above.</li>
                                                <li>Click a 1-click preset (e.g. <em>AI Prompts Showcase</em>) to populate layout and subject.</li>
                                                <li>Enter your campaign title, subject, and preview snippet.</li>
                                                <li>Choose your audience segment and click <strong>Preview Segment</strong>.</li>
                                                <li>Customize content in CKEditor; use dynamic tags like <code>{name}</code>.</li>
                                                <li>Check Desktop and Mobile preview toggles.</li>
                                                <li>Send a real test email to your inbox and inspect it.</li>
                                                <li>Click <strong>Send Campaign Now</strong> or <strong>Save Draft</strong>.</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border h-100">
                                            <strong class="d-block text-danger mb-2"><i class="bi-slash-circle me-1"></i> Using the Emergency Kill-Switch:</strong>
                                            <ol class="small text-muted ps-3 mb-0">
                                                <li>In the red/green banner above, click <strong>Engage Emergency Kill-Switch</strong>.</li>
                                                <li>Confirm the action prompt.</li>
                                                <li><strong>Result:</strong> All bulk marketing dispatches freeze immediately and pending jobs in <code>emails-bulk</code> are purged.</li>
                                                <li><strong>Safety Guarantee:</strong> Transactional login OTPs and password resets on <code>emails-urgent</code> continue functioning normally.</li>
                                                <li>To restore normal marketing capability, click <strong>Release Kill-Switch &amp; Resume</strong>.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                                <ul class="small text-muted mb-3 ps-3">
                                    <li>Always dispatch a test email to your personal inbox and click every link before broadcasting.</li>
                                    <li>Keep subject lines under 50 characters to prevent mobile truncation.</li>
                                    <li>Use the "Minimalist Letter" preset when communicating high-importance updates for maximum deliverability.</li>
                                </ul>

                                <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                                <p class="small text-muted mb-0">
                                    <strong>"Kill-Switch Engaged" warning:</strong> If you cannot create or send campaigns, check the top banner. If the kill-switch was left engaged, click "Release Kill-Switch &amp; Resume".
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FEATURE 10 -->
                    <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden" id="guide-feature-10">
                        <h2 class="accordion-header" id="headingF10">
                            <button class="accordion-button collapsed fw-bold text-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseF10" aria-expanded="false" aria-controls="collapseF10">
                                <i class="bi-cpu-fill text-info me-2 fs-5"></i> Feature 10: Automated Lifecycle Campaigns &amp; Customer Drips
                            </button>
                        </h2>
                        <div id="collapseF10" class="accordion-collapse collapse" aria-labelledby="headingF10" data-bs-parent="#marketingGuideAccordion">
                            <div class="accordion-body">
                                <h6 class="fw-bold text-primary mb-2">1. What It Does</h6>
                                <p class="small text-muted mb-3">
                                    Hands-off background marketing sequences that automatically engage, convert, and retain users across their lifecycle:
                                    <br>&bull; <strong>Day 3 Welcome Drip:</strong> Teaches 3 studio lighting secrets to drive prompt copying.
                                    <br>&bull; <strong>Tuesday Curated Prompt Drop:</strong> Top 3 trending photoshoots sent every Tuesday at 10:00 AM UTC.
                                    <br>&bull; <strong>3-Day Expiration Alert:</strong> Notifies users 72 to 48 hours before their subscription expires to renew.
                                    <br>&bull; <strong>Monthly Quota Refilled:</strong> Alerts paying members when their download balance resets.
                                    <br>&bull; <strong>Abandoned Checkout Recovery:</strong> Follows up with cart abandoners 2–24 hours post-intent (with 30-day frequency cap).
                                    <br>&bull; <strong>30-Day Inactive Re-engagement:</strong> Re-engages dormant members monthly on the 1st.
                                </p>

                                <h6 class="fw-bold text-primary mb-2">2. Why &amp; When to Use It</h6>
                                <p class="small text-muted mb-3">
                                    <strong>Why:</strong> Recaptures lost sales automatically, reduces subscriber churn, and maintains high user activity without manual weekly effort.<br>
                                    <strong>When:</strong> Runs 24/7 on predefined cron schedules in the background.
                                </p>

                                <h6 class="fw-bold text-primary mb-2">3. How to Use &amp; Monitor Step by Step</h6>
                                <ol class="small text-muted mb-3 ps-3">
                                    <li>Lifecycle dispatches appear automatically in the <strong>Campaign History</strong> table below.</li>
                                    <li>Automations are scheduled in <code>app/Console/Kernel.php</code> and execute via standard server cron.</li>
                                    <li>Administrators with terminal access can test any automation manually:
                                        <br><code>php artisan email:send-weekly-drop</code>
                                        <br><code>php artisan email:send-welcome-drip</code>
                                        <br><code>php artisan email:send-abandoned-checkout</code>
                                        <br><code>php artisan email:send-quota-alerts</code>
                                    </li>
                                </ol>

                                <h6 class="fw-bold text-primary mb-2">4. Best Practices &amp; Recommended Settings</h6>
                                <ul class="small text-muted mb-3 ps-3">
                                    <li>Keep your newest prompts and photoshoots high quality so weekly prompt drops showcase your best creative work.</li>
                                    <li>All lifecycle emails respect the suppression list and 1-click unsubscribe headers automatically.</li>
                                </ul>

                                <h6 class="fw-bold text-primary mb-2">5. Common Mistakes &amp; Troubleshooting</h6>
                                <p class="small text-muted mb-0">
                                    <strong>Duplicate email prevention:</strong> Built-in database timestamp tracking (<code>welcome_drip_sent_at</code>, <code>last_expiration_alert_sent_at</code>, and <code>abandoned_checkout_logs</code>) ensures users never receive duplicate messages. If emails stop triggering, check that your server cron <code>php artisan schedule:run</code> is executing every minute.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Cross-Link to Deliverability Section -->
                <div class="alert alert-secondary border-0 bg-body-tertiary p-3 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h6 class="fw-bold text-body mb-1">Looking for Deliverability Health, DNS or Bounce Suppression?</h6>
                        <small class="text-muted">
                            Features 1 through 8 (Amazon SES, SPF/DKIM/DMARC, Double Opt-In, 2FA OTP, SNS Suppression) are located under Deliverability &amp; Audience.
                        </small>
                    </div>
                    <a href="{{ url('panel/admin/email-deliverability?tab=guide') }}" class="btn btn-sm btn-dark">
                        <i class="bi-shield-check me-1"></i> Open Deliverability Admin Guide &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- EMERGENCY KILL-SWITCH BANNER -->
    <div class="card border-0 mb-4 shadow-sm @if($killSwitchActive) bg-danger text-white @else bg-dark-subtle border-start border-4 border-success @endif">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex p-2 rounded-circle @if($killSwitchActive) bg-white text-danger @else bg-success text-white @endif">
                    <i class="bi-{{ $killSwitchActive ? 'slash-circle-fill' : 'broadcast' }} fs-5"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold @if($killSwitchActive) text-white @else text-dark @endif">
                        Emergency Bulk Email Kill-Switch: 
                        <span class="badge @if($killSwitchActive) bg-white text-danger @else bg-success text-white @endif">
                            {{ $killSwitchActive ? 'ENGAGED (ALL BULK SENDS PAUSED)' : 'ACTIVE (NORMAL)' }}
                        </span>
                    </h6>
                    <small class="@if($killSwitchActive) text-white-50 @else text-muted @endif">
                        {{ $killSwitchActive ? 'No bulk marketing emails will be sent. Pending queues are frozen. Transactional 2FA codes continue unaffected.' : 'Bulk queues are operational. Click emergency stop to immediately freeze outgoing marketing blasts.' }}
                    </small>
                </div>
            </div>
            <div>
                <form method="POST" action="{{ url('panel/admin/email-marketing/kill-switch') }}" onsubmit="return confirm('{{ $killSwitchActive ? 'Resume bulk marketing email sends?' : 'WARNING: Engage Emergency Kill-Switch to freeze all bulk sends and purge pending bulk jobs?' }}');">
                    @csrf
                    <input type="hidden" name="activate" value="{{ $killSwitchActive ? '0' : '1' }}">
                    <button type="submit" class="btn btn-sm @if($killSwitchActive) btn-light text-danger fw-bold @else btn-outline-danger @endif">
                        <i class="bi-{{ $killSwitchActive ? 'play-fill' : 'stop-circle-fill' }} me-1"></i>
                        {{ $killSwitchActive ? 'Release Kill-Switch & Resume' : 'Engage Emergency Kill-Switch' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Section Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'campaigns' ? 'active bg-dark text-white' : 'bg-white text-dark border' }} fw-semibold px-3 py-2 shadow-sm" href="{{ route('email_campaigns', ['tab' => 'campaigns']) }}">
                <i class="bi bi-megaphone me-1"></i> Broadcast Campaigns
                <span class="badge {{ $tab === 'campaigns' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ number_format($campaigns->total()) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'weekly-drops' ? 'active bg-dark text-white' : 'bg-white text-dark border' }} fw-semibold px-3 py-2 shadow-sm" href="{{ route('email_campaigns', ['tab' => 'weekly-drops']) }}">
                <i class="bi bi-camera-reels me-1"></i> Weekly Photoshoot Drop History
                <span class="badge {{ $tab === 'weekly-drops' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ number_format($totalWeeklyDrops) }}</span>
            </a>
        </li>
    </ul>

    @if($tab === 'weekly-drops')
        <!-- Weekly Photoshoot Drop KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Total Weekly Drops</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalWeeklyDrops) }}</h3>
                    <small class="text-muted"><i class="bi bi-clock-history me-1"></i> Historical digests</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Photoshoots Featured</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalPhotoshootsFeatured) }}</h3>
                    <small class="text-muted"><i class="bi bi-images me-1"></i> Curated creative looks</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Subscribers Reached</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($totalWeeklyRecipients) }}</h3>
                    <small class="text-success"><i class="bi bi-send-check me-1"></i> Delivered in batches</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Avg Delivery &amp; Opens</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $avgDeliveryRate }}%</h3>
                    <small class="text-muted">Avg opens: <strong class="text-dark">{{ $avgOpenRate }}%</strong></small>
                </div>
            </div>
        </div>

        <!-- Weekly Photoshoot Drop History Table -->
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-camera-reels me-1"></i> Weekly Photoshoot Drop History</h6>
                        <small class="text-muted">Automated weekly digests showcasing newly published photoshoots delivered to subscribers in batches.</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAllWeeklyHistoryModal" {{ $totalWeeklyDrops === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-trash3 me-1"></i> Delete All History
                        </button>
                    </div>
                </div>

                <div class="table-responsive p-0">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Reporting Period</th>
                                <th>Featured Photoshoots</th>
                                <th>Campaign &amp; Subject</th>
                                <th>Status</th>
                                <th>Delivery &amp; Analytics</th>
                                <th>Dispatched Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weeklyDrops as $drop)
                            <tr>
                                <td>
                                    <strong class="text-dark">{{ $drop->period_label }}</strong>
                                    <div class="small text-muted font-monospace" style="font-size: 11px;">{{ $drop->period_key }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border mb-1">
                                        <i class="bi bi-images me-1"></i> {{ $drop->photoshoots_count }} Photoshoot{{ $drop->photoshoots_count == 1 ? '' : 's' }}
                                    </span>
                                    @if($drop->photoshoots->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @foreach($drop->photoshoots->take(3) as $ps)
                                                <a href="{{ url('photoshoot', $ps->slug ?? $ps->id) }}" target="_blank" class="badge bg-light text-dark border text-decoration-none" title="{{ $ps->title }}">
                                                    {{ Str::limit($ps->title, 18) }} &nearr;
                                                </a>
                                            @endforeach
                                            @if($drop->photoshoots_count > 3)
                                                <span class="small text-muted align-self-center">+{{ $drop->photoshoots_count - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($drop->campaign)
                                        <div class="fw-semibold text-dark">{{ $drop->campaign->name }}</div>
                                        <div class="small text-muted">{{ Str::limit($drop->campaign->subject, 36) }}</div>
                                    @else
                                        <span class="text-muted fst-italic">No campaign created (0 photoshoots)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($drop->status === 'dispatched')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Dispatched</span>
                                    @elseif($drop->status === 'no_photoshoots' || $drop->status === 'skipped_no_photoshoots')
                                        <span class="badge bg-secondary"><i class="bi bi-dash-circle me-1"></i> No Photoshoots</span>
                                    @elseif($drop->status === 'stopped' || $drop->status === 'failed' || ($drop->campaign && in_array($drop->campaign->status, ['stopped', 'cancelled'])))
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Stopped</span>
                                    @elseif($drop->status === 'sending' || $drop->status === 'processing')
                                        <span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1"></span> Sending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($drop->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($drop->campaign)
                                        <div><strong>{{ number_format($drop->recipients_count) }}</strong> recipients</div>
                                        <div class="small text-muted" style="font-size: 11px;">
                                            <span class="text-success"><i class="bi bi-check2"></i> {{ $drop->campaign->delivery_rate }}% delivered</span>
                                            @if($drop->campaign->unique_opens_count > 0)
                                                &bull; <span class="text-info"><i class="bi bi-eye"></i> {{ $drop->campaign->open_rate }}% opens</span>
                                            @endif
                                            @if($drop->campaign->unique_clicks_count > 0)
                                                &bull; <span class="text-primary"><i class="bi bi-cursor"></i> {{ $drop->campaign->click_rate }}% clicks</span>
                                            @endif
                                        </div>
                                    @else
                                        <div>{{ number_format($drop->recipients_count) }} recipients</div>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $drop->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($drop->campaign)
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $drop->campaign->id . '/analytics') }}" class="btn btn-sm btn-info text-white me-1" title="View Detailed Campaign Analytics &amp; Recipient Delivery Log">
                                            <i class="bi bi-graph-up me-1"></i> Analytics
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('email_campaigns.weekly_drops.delete_single', $drop->id) }}" onsubmit="return confirm('Delete this weekly drop history record for {{ addslashes($drop->period_label) }}?');" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete record">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-camera-reels text-muted fs-2 d-block mb-2"></i>
                                    No weekly photoshoot drop history records yet.
                                    <br>
                                    <small class="text-muted">Weekly digests are automatically sent each week via the scheduler, or can be tested manually using <code>php artisan email:send-weekly-drop</code>.</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $weeklyDrops->links() }}
                </div>
            </div>
        </div>

    @else

        <!-- KPI Metric Cards for Broadcast Campaigns -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Deliverable Audience</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalAudience) }}</h3>
                    <small class="text-success"><i class="bi-check-circle-fill me-1"></i> Verified Google + Double Opt-In</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Total Campaigns Sent</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalCampaignsSent) }}</h3>
                    <small class="text-muted">Broadcast history</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-custom border-0 p-3 h-100">
                    <span class="text-muted small text-uppercase fw-bold mb-1">Total Dispatched Emails</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($totalEmailsDispatched) }}</h3>
                    <small class="text-muted">Sent through Amazon SES</small>
                </div>
            </div>
        </div>

        <!-- Broadcast Campaigns List Table -->
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi-clock-history me-1"></i> Campaign History</h6>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0" data-bs-toggle="collapse" data-bs-target="#marketingAdminGuide">
                        <i class="bi-question-circle me-1"></i> Need help with campaigns or drips?
                    </button>
                </div>

                <div class="table-responsive p-0">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign Name</th>
                                <th>Subject</th>
                                <th>Target Segment</th>
                                <th>Status</th>
                                <th>Recipients</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->name }}</td>
                                <td>{{ Str::limit($c->subject, 40) }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        {{ str_replace('_', ' ', ucfirst($c->target_segment)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($c->status === 'sent')
                                        <span class="badge bg-success"><i class="bi-check-circle me-1"></i> Sent</span>
                                    @elseif($c->status === 'sending')
                                        <span class="badge bg-warning text-dark" id="statusBadge-{{ $c->id }}">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Sending (<span class="progress-pct">{{ $c->progress_percentage }}%</span>)
                                        </span>
                                    @elseif($c->status === 'cancelled' || $c->status === 'stopped' || $c->status === 'failed')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> Stopped
                                        </span>
                                    @elseif($c->status === 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($c->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($c->status === 'sending')
                                        <div class="d-flex align-items-center gap-2 mb-1" id="progressInfo-{{ $c->id }}">
                                            <span class="small fw-semibold"><span class="processed-val">{{ number_format($c->processed_count) }}</span> / {{ number_format($c->recipients_count) }}</span>
                                            <span class="badge bg-warning-subtle text-warning-emphasis"><span class="progress-pct">{{ $c->progress_percentage }}%</span></span>
                                        </div>
                                        <div class="progress" style="height: 6px; width: 120px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="progressBar-{{ $c->id }}" style="width: {{ $c->progress_percentage }}%;"></div>
                                        </div>
                                    @elseif($c->status === 'cancelled' || $c->status === 'stopped' || $c->status === 'failed')
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="small fw-semibold text-danger">{{ number_format($c->processed_count) }} / {{ number_format($c->recipients_count) }}</span>
                                            <span class="badge bg-danger-subtle text-danger">Stopped</span>
                                        </div>
                                        <div class="progress" style="height: 6px; width: 120px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $c->progress_percentage }}%;"></div>
                                        </div>
                                    @elseif($c->status === 'sent')
                                        <div>{{ number_format($c->sent_count) }} / {{ number_format($c->recipients_count) }}</div>
                                        <div class="small text-muted" style="font-size: 11px;">
                                            <span class="text-success"><i class="bi-check2"></i> {{ $c->delivery_rate }}% delivered</span>
                                            @if($c->unique_opens_count > 0)
                                                &bull; <span class="text-info"><i class="bi-eye"></i> {{ $c->open_rate }}% opens</span>
                                            @endif
                                        </div>
                                    @else
                                        <div>0 / {{ number_format($c->recipients_count) }}</div>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $c->sent_at ? $c->sent_at->format('M d, Y H:i') : $c->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($c->status === 'draft')
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/edit') }}" class="btn btn-sm btn-primary me-1" title="Edit and resume draft">
                                            <i class="bi-pencil-square me-1"></i> Edit &amp; Send
                                        </a>
                                        <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/send') }}" onsubmit="return confirm('Are you sure you want to broadcast \'{{ addslashes($c->name) }}\' to all recipients now?');" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Send broadcast immediately">
                                                <i class="bi-send-fill me-1"></i> Send Now
                                            </button>
                                        </form>
                                    @elseif($c->status === 'sending')
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/analytics') }}" class="btn btn-sm btn-info text-white me-1" title="View Full Analytics &amp; Delivery Log">
                                            <i class="bi-graph-up me-1"></i> Analytics
                                        </a>
                                        <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/pause') }}" onsubmit="return confirm('Pause sending this campaign?');" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning text-dark me-1" title="Pause broadcast">
                                                <i class="bi-pause-fill me-1"></i> Pause
                                            </button>
                                        </form>
                                    @elseif($c->status === 'cancelled')
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/analytics') }}" class="btn btn-sm btn-info text-white me-1" title="View Full Analytics &amp; Delivery Log">
                                            <i class="bi-graph-up me-1"></i> Analytics
                                        </a>
                                        @if(!$killSwitchActive && $c->processed_count < $c->recipients_count)
                                        <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/resume') }}" onsubmit="return confirm('Resume sending remaining unsent recipients for \'{{ addslashes($c->name) }}\'?');" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Resume sending remaining recipients">
                                                <i class="bi-play-fill me-1"></i> Resume Send
                                            </button>
                                        </form>
                                        @endif
                                        @if($c->processed_count > 0 && $c->processed_count < $c->recipients_count)
                                        <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/mark-completed') }}" onsubmit="return confirm('Mark this broadcast as complete at {{ number_format($c->processed_count) }} recipients?');" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary me-1" title="Mark as Completed">
                                                <i class="bi-check-all me-1"></i> Mark Done
                                            </button>
                                        </form>
                                        @endif
                                    @else
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/analytics') }}" class="btn btn-sm btn-info text-white me-1" title="View Full Analytics &amp; Delivery Log">
                                            <i class="bi-graph-up me-1"></i> Analytics
                                        </a>
                                        <a href="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/edit') }}" class="btn btn-sm btn-outline-secondary me-1" title="View details">
                                            <i class="bi-eye me-1"></i> View
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns/' . $c->id . '/delete') }}" onsubmit="return confirm('Delete this campaign record?');" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete record">
                                            <i class="bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi-megaphone text-muted fs-2 d-block mb-2"></i>
                                    No broadcast campaigns created yet. Click <strong>Create New Campaign</strong> above to compose your first broadcast!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>

    @endif

    <!-- Modal: Delete All Weekly Drop History -->
    <div class="modal fade" id="deleteAllWeeklyHistoryModal" tabindex="-1" aria-labelledby="deleteAllWeeklyHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('email_campaigns.weekly_drops.delete_all') }}" id="deleteAllWeeklyDropsForm">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="deleteAllWeeklyHistoryModalLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Delete All Weekly Drop History
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger d-flex align-items-center mb-3">
                            <i class="bi bi-shield-exclamation fs-3 me-3 flex-shrink-0"></i>
                            <div>
                                <strong>Warning: This action is permanent and irreversible!</strong>
                                <div class="small mt-1">
                                    All <strong>{{ number_format($totalWeeklyDrops) }}</strong> stored weekly photoshoot drop history records will be permanently removed.
                                </div>
                            </div>
                        </div>

                        <p class="small text-muted mb-2"><strong>Database Safety &amp; Scoping:</strong></p>
                        <ul class="small text-muted mb-3 ps-3">
                            <li><strong>Scoped exclusively:</strong> Only historical records in the <code>weekly_digest_history</code> table are cleared.</li>
                            <li><strong>Safe &amp; preserved:</strong> Photoshoot posts, prompt data, email broadcast campaigns, subscriber accounts, and deliverability logs are <strong>not</strong> modified or deleted.</li>
                        </ul>

                        <div class="mb-3">
                            <label for="confirmDeleteAllInput" class="form-label small fw-bold text-dark">
                                Type <code class="text-danger fw-bold">DELETE</code> to confirm:
                            </label>
                            <input type="text" class="form-control" id="confirmDeleteAllInput" placeholder="Type DELETE to enable confirmation" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="submitDeleteAllBtn" disabled>
                            <span class="spinner-border spinner-border-sm d-none me-1" id="deleteAllSpinner" role="status" aria-hidden="true"></span>
                            <span id="deleteAllBtnText"><i class="bi bi-trash3 me-1"></i> Yes, Delete All History</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmInput = document.getElementById('confirmDeleteAllInput');
    const submitBtn = document.getElementById('submitDeleteAllBtn');
    const deleteForm = document.getElementById('deleteAllWeeklyDropsForm');
    const spinner = document.getElementById('deleteAllSpinner');
    const btnText = document.getElementById('deleteAllBtnText');

    if (confirmInput && submitBtn) {
        confirmInput.addEventListener('input', function () {
            if (this.value.trim().toUpperCase() === 'DELETE') {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }
        });
    }

    if (deleteForm && submitBtn) {
        deleteForm.addEventListener('submit', function () {
            submitBtn.setAttribute('disabled', 'disabled');
            if (spinner) spinner.classList.remove('d-none');
            if (btnText) btnText.innerHTML = 'Deleting history...';
        });
    }
});
</script>

@if($campaigns->contains('status', 'sending') && !$killSwitchActive)
<script>
document.addEventListener('DOMContentLoaded', function () {
    @php
        $sendingIds = $campaigns->where('status', 'sending')->pluck('id')->values();
    @endphp
    const sendingCampaignIds = {!! json_encode($sendingIds) !!};

    if (sendingCampaignIds.length > 0) {
        const pollTimer = setInterval(function () {
            sendingCampaignIds.forEach(id => {
                fetch("{{ url('panel/admin/email-marketing/campaigns') }}/" + id + "/progress")
                    .then(res => res.json())
                    .then(data => {
                        const progressBar = document.getElementById('progressBar-' + id);
                        const progressInfo = document.getElementById('progressInfo-' + id);
                        if (progressBar) {
                            progressBar.style.width = data.progress_percentage + '%';
                        }
                        if (progressInfo) {
                            const pctEls = progressInfo.querySelectorAll('.progress-pct');
                            pctEls.forEach(el => el.textContent = data.progress_percentage + '%');
                            const procEl = progressInfo.querySelector('.processed-val');
                            if (procEl) procEl.textContent = new Intl.NumberFormat().format(data.processed_count);
                        }
                        if (data.is_completed || data.status !== 'sending') {
                            clearInterval(pollTimer);
                            window.location.reload();
                        }
                    })
                    .catch(() => {});
            });
        }, 4000);
    }
});
</script>
@endif
@endsection
