@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-bold">
    <a href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a> 
    <i class="bi-chevron-right fs-6 mx-2"></i> 
    <span class="text-muted">Email Templates &amp; Master Branding</span>
</h5>

<div class="content">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2-circle me-1"></i> {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('errors.errors-forms')

    <!-- Contextual Explanation Banner -->
    <div class="card border-0 mb-4 shadow-custom bg-body-tertiary">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex p-2 rounded-circle bg-primary-subtle text-primary border border-primary-subtle">
                    <i class="bi-envelope-check fs-5"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-body">Centralized Email Templates &amp; Branding Management</h6>
                    <small class="text-muted">
                        Manage all outgoing emails across both <code>auth.imaginebuddy.com</code> (critical authentication &amp; billing) and <code>updates.imaginebuddy.com</code> (marketing, prompt drops &amp; lifecycle automations) in a unified, production-ready Light-Mode design system.
                    </small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill align-self-center px-3 py-2">
                    <i class="bi-shield-lock-fill me-1"></i> auth.imaginebuddy.com
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-center px-3 py-2">
                    <i class="bi-megaphone-fill me-1"></i> updates.imaginebuddy.com
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($tab === 'templates' && $channel === 'all') active fw-bold @endif" href="{{ url('panel/admin/email-templates?tab=templates&channel=all') }}">
                <i class="bi-card-text me-1"></i> All Templates ({{ $counts['all'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'templates' && $channel === 'auth') active fw-bold @endif" href="{{ url('panel/admin/email-templates?tab=templates&channel=auth') }}">
                <i class="bi-shield-check text-primary me-1"></i> Auth Channel ({{ $counts['auth'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'templates' && $channel === 'updates') active fw-bold @endif" href="{{ url('panel/admin/email-templates?tab=templates&channel=updates') }}">
                <i class="bi-megaphone text-success me-1"></i> Updates Channel ({{ $counts['updates'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'branding') active fw-bold @endif" href="{{ url('panel/admin/email-templates?tab=branding') }}">
                <i class="bi-palette me-1"></i> Master Branding &amp; Sender Config
            </a>
        </li>
    </ul>

    <!-- TAB 1: TEMPLATES LIBRARY -->
    @if($tab === 'templates')
    <div class="card shadow-custom border-0">
        <div class="card-body p-lg-4">
            <!-- Filter & Search Bar -->
            <div class="row align-items-center mb-4 g-3">
                <div class="col-md-7">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=all') }}" class="btn btn-sm @if($category === 'all') btn-dark @else btn-light border @endif">
                            All Categories ({{ $counts['all'] }})
                        </a>
                        @if($channel === 'all' || $channel === 'auth')
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=security') }}" class="btn btn-sm @if($category === 'security') btn-dark @else btn-light border @endif">
                                <i class="bi-shield-lock text-danger me-1"></i> Security ({{ $counts['security'] }})
                            </a>
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=authentication') }}" class="btn btn-sm @if($category === 'authentication') btn-dark @else btn-light border @endif">
                                <i class="bi-person-check text-primary me-1"></i> Auth ({{ $counts['authentication'] }})
                            </a>
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=billing') }}" class="btn btn-sm @if($category === 'billing') btn-dark @else btn-light border @endif">
                                <i class="bi-credit-card text-success me-1"></i> Billing ({{ $counts['billing'] }})
                            </a>
                        @endif
                        @if($channel === 'all' || $channel === 'updates')
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=newsletters') }}" class="btn btn-sm @if($category === 'newsletters') btn-dark @else btn-light border @endif">
                                <i class="bi-envelope-paper text-info me-1"></i> Newsletters ({{ $counts['newsletters'] }})
                            </a>
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=marketing') }}" class="btn btn-sm @if($category === 'marketing') btn-dark @else btn-light border @endif">
                                <i class="bi-broadcast text-warning me-1"></i> Marketing ({{ $counts['marketing'] }})
                            </a>
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=lifecycle') }}" class="btn btn-sm @if($category === 'lifecycle') btn-dark @else btn-light border @endif">
                                <i class="bi-arrow-repeat text-success me-1"></i> Lifecycle ({{ $counts['lifecycle'] }})
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-5">
                    <form method="GET" action="{{ url('panel/admin/email-templates') }}" class="d-flex gap-2">
                        <input type="hidden" name="channel" value="{{ $channel }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search template name, subject...">
                        <button type="submit" class="btn btn-sm btn-dark">Search</button>
                        @if(!empty($q))
                            <a href="{{ url('panel/admin/email-templates?channel=' . $channel . '&category=' . $category) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Templates List Table -->
            <div class="table-responsive p-0">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Template Name</th>
                            <th class="text-nowrap">Channel</th>
                            <th class="text-nowrap">Category</th>
                            <th>Subject Line</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-end text-nowrap" style="min-width: 175px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $tmpl)
                        <tr>
                            <td>
                                <div class="fw-bold text-body">{{ $tmpl->name }}</div>
                                <code class="small text-muted">{{ $tmpl->slug }}</code>
                            </td>
                            <td class="text-nowrap">
                                @if($tmpl->channel === 'updates')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi-megaphone me-1"></i> updates
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        <i class="bi-shield-lock me-1"></i> auth
                                    </span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($tmpl->category === 'security')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Security OTP</span>
                                @elseif($tmpl->category === 'authentication')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Authentication</span>
                                @elseif($tmpl->category === 'billing')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Billing &amp; Plans</span>
                                @elseif($tmpl->category === 'newsletters')
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">Newsletter</span>
                                @elseif($tmpl->category === 'marketing')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Marketing Broadcast</span>
                                @elseif($tmpl->category === 'lifecycle')
                                    <span class="badge bg-purple-subtle text-primary border rounded-pill" style="background-color: #f3e8ff;">Lifecycle Automation</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">Notification</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-body fw-medium text-truncate" style="max-width: 280px;" title="{{ $tmpl->subject }}">
                                    {{ $tmpl->subject }}
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($tmpl->is_enabled)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Disabled</span>
                                    @endif
                                    @if($tmpl->is_system)
                                        <span class="badge bg-light text-secondary border" title="Core System Workflow (Protected)">
                                            <i class="bi-lock-fill"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ url('panel/admin/email-templates/' . $tmpl->id . '/preview') }}" class="btn btn-sm btn-outline-secondary px-2 py-1 d-inline-flex align-items-center" target="_blank" title="View Preview in New Tab">
                                        <i class="bi-eye me-1"></i> Preview
                                    </a>
                                    <a href="{{ url('panel/admin/email-templates/' . $tmpl->id . '/edit') }}" class="btn btn-sm btn-dark px-3 py-1 d-inline-flex align-items-center">
                                        <i class="bi-pencil me-1"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi-envelope-x fs-2 d-block mb-2"></i>
                                No templates found matching your query.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    @endif

    <!-- TAB 2: MASTER BRANDING & SENDER CONFIG (DECOUPLED FOR AUTH & UPDATES) -->
    @if($tab === 'branding')
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ url('panel/admin/email-templates/branding') }}" enctype="multipart/form-data">
                @csrf

                <!-- Header Logo Upload -->
                <div class="card shadow-custom border-0 p-4 mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi-image me-1 text-primary"></i> Shared Header Brand Logo</h6>
                    <p class="text-muted small mb-3">
                        Displayed at the top of all email layouts across both <code>auth.imaginebuddy.com</code> and <code>updates.imaginebuddy.com</code>.
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 border rounded bg-light text-center" style="min-width: 140px; max-width: 220px;">
                            @if(!empty($settings->email_logo))
                                <img src="{{ url($settings->email_logo) }}" alt="Current Logo" style="max-height: 45px; max-width: 100%;">
                            @elseif(!empty($settings->logo))
                                <img src="{{ url('public/img', $settings->logo) }}" alt="Default Logo" style="max-height: 45px; max-width: 100%;">
                            @else
                                <span class="fw-bold">{{ $settings->title }}</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" name="email_logo" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted">Recommended: Transparent PNG or SVG, max width 220px, height ~45px.</small>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1: AUTHENTICATION CHANNEL SETTINGS -->
                <div class="card shadow-custom border-0 p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 text-primary">
                            <i class="bi-shield-lock-fill me-1"></i> Authentication Channel Settings (auth.imaginebuddy.com)
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            ses-transactional
                        </span>
                    </div>
                    <p class="text-muted small mb-4">
                        Handles 2FA OTP codes, account verification, password resets, and critical billing invoices.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Sender Name <span class="text-danger">*</span></label>
                            <input type="text" name="email_sender_name_auth" class="form-control" value="{{ $settings->email_sender_name_auth ?: 'Imagine Buddy Security' }}" required>
                            <small class="text-muted">e.g. <em>Imagine Buddy Security</em></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Sender Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email_sender_address_auth" class="form-control" value="{{ $settings->email_sender_address_auth ?: 'noreply@auth.imaginebuddy.com' }}" required>
                            <small class="text-muted">Verified domain: <code>auth.imaginebuddy.com</code></small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Accent Color (Links &amp; Accents)</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_primary_color ?: '#4f46e5' }}" id="authPrimaryColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_primary_color" id="authPrimaryColorText" class="form-control" value="{{ $settings->email_primary_color ?: '#4f46e5' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Button Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_button_color ?: '#0f172a' }}" id="authButtonColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_button_color" id="authButtonColorText" class="form-control" value="{{ $settings->email_button_color ?: '#0f172a' }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Security Footer Disclaimer</label>
                        <textarea name="email_footer_text" class="form-control" rows="2" placeholder="This security email was sent automatically from a verified, notification-only address.">{{ $settings->email_footer_text }}</textarea>
                    </div>
                </div>

                <!-- SECTION 2: UPDATES & MARKETING CHANNEL SETTINGS -->
                <div class="card shadow-custom border-0 p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 text-success">
                            <i class="bi-megaphone-fill me-1"></i> Updates &amp; Marketing Channel Settings (updates.imaginebuddy.com)
                        </h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            ses-marketing
                        </span>
                    </div>
                    <p class="text-muted small mb-4">
                        Handles newsletter broadcasts, weekly prompt drops, onboarding drips, and promotional announcements.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Marketing Sender Name <span class="text-danger">*</span></label>
                            <input type="text" name="email_sender_name_updates" class="form-control" value="{{ $settings->email_sender_name_updates ?: 'Imagine Buddy Updates' }}" required>
                            <small class="text-muted">e.g. <em>Imagine Buddy Updates</em> or <em>Imagine Buddy Team</em></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Marketing Sender Address <span class="text-danger">*</span></label>
                            <input type="email" name="email_sender_address_updates" class="form-control" value="{{ $settings->email_sender_address_updates ?: 'hello@updates.imaginebuddy.com' }}" required>
                            <small class="text-muted">Verified domain: <code>updates.imaginebuddy.com</code></small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marketing Reply-To Address</label>
                            <input type="email" name="email_reply_to_updates" class="form-control" value="{{ $settings->email_reply_to_updates ?: 'support@imaginebuddy.com' }}">
                            <small class="text-muted">Where creator replies are routed (e.g. support helpdesk).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marketing Button Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_updates_button_color ?: '#0f172a' }}" id="updatesButtonColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_updates_button_color" id="updatesButtonColorText" class="form-control" value="{{ $settings->email_updates_button_color ?: '#0f172a' }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marketing Accent / Link Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_updates_primary_color ?: '#4f46e5' }}" id="updatesPrimaryColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_updates_primary_color" id="updatesPrimaryColorText" class="form-control" value="{{ $settings->email_updates_primary_color ?: '#4f46e5' }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Marketing Footer Disclaimer &amp; Notice</label>
                        <textarea name="email_updates_footer_text" class="form-control" rows="2" placeholder="You are receiving this email because you opted in to updates and marketing on Imagine Buddy.">{{ $settings->email_updates_footer_text }}</textarea>
                        <small class="text-muted">One-click unsubscribe links and terms are automatically appended.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark px-4 py-2">
                    <i class="bi-check-lg me-1"></i> Save All Master Settings
                </button>
            </form>
        </div>

        <!-- Right Side: Guidelines & Architecture Info -->
        <div class="col-lg-4">
            <div class="card shadow-custom border-0 p-4 mb-4">
                <h6 class="fw-bold mb-2 text-body"><i class="bi-shield-check me-1 text-success"></i> Channel Isolation Guarantee</h6>
                <p class="small text-muted mb-3">
                    Your two sending identities are strictly isolated to guarantee maximum domain reputation and deliverability:
                </p>
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="fw-bold small text-primary mb-1">🔒 auth.imaginebuddy.com</div>
                    <p class="small text-muted mb-0">
                        Dedicated solely to instant security OTP codes, account verification, and billing receipts. Never affected by marketing volume.
                    </p>
                </div>
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="fw-bold small text-success mb-1">📢 updates.imaginebuddy.com</div>
                    <p class="small text-muted mb-0">
                        Handles weekly prompt drops, onboarding drips, and marketing blasts with RFC-compliant <code>List-Unsubscribe=One-Click</code> headers.
                    </p>
                </div>

                <h6 class="fw-bold mb-2 text-body"><i class="bi-sun me-1 text-warning"></i> Mandatory Light-Mode Theme</h6>
                <p class="small text-muted mb-0">
                    All templates render in a crisp, light-mode design with high-contrast slate typography (<code>#0f172a</code>) tested across Gmail, Apple Mail, and Outlook.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auth color pickers
    const authPrimaryPicker = document.getElementById('authPrimaryColorPicker');
    const authPrimaryText = document.getElementById('authPrimaryColorText');
    if (authPrimaryPicker && authPrimaryText) {
        authPrimaryPicker.addEventListener('input', function() { authPrimaryText.value = authPrimaryPicker.value; });
        authPrimaryText.addEventListener('input', function() { authPrimaryPicker.value = authPrimaryText.value; });
    }

    const authButtonPicker = document.getElementById('authButtonColorPicker');
    const authButtonText = document.getElementById('authButtonColorText');
    if (authButtonPicker && authButtonText) {
        authButtonPicker.addEventListener('input', function() { authButtonText.value = authButtonPicker.value; });
        authButtonText.addEventListener('input', function() { authButtonPicker.value = authButtonText.value; });
    }

    // Updates color pickers
    const updatesPrimaryPicker = document.getElementById('updatesPrimaryColorPicker');
    const updatesPrimaryText = document.getElementById('updatesPrimaryColorText');
    if (updatesPrimaryPicker && updatesPrimaryText) {
        updatesPrimaryPicker.addEventListener('input', function() { updatesPrimaryText.value = updatesPrimaryPicker.value; });
        updatesPrimaryText.addEventListener('input', function() { updatesPrimaryPicker.value = updatesPrimaryText.value; });
    }

    const updatesButtonPicker = document.getElementById('updatesButtonColorPicker');
    const updatesButtonText = document.getElementById('updatesButtonColorText');
    if (updatesButtonPicker && updatesButtonText) {
        updatesButtonPicker.addEventListener('input', function() { updatesButtonText.value = updatesButtonPicker.value; });
        updatesButtonText.addEventListener('input', function() { updatesButtonPicker.value = updatesButtonText.value; });
    }
});
</script>
@endsection
