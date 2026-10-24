@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Email Templates &amp; Configuration</span>

    <a href="{{ url('panel/admin/email-deliverability') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-shield-check me-1"></i> Deliverability Center &rarr;
    </a>
    <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-megaphone me-1"></i> Marketing Campaigns &rarr;
    </a>
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
                    <h6 class="mb-0 fw-bold text-body">Transactional &amp; Authentication Email Center (auth.imaginebuddy.com)</h6>
                    <small class="text-muted">
                        Customize email layouts, subject lines, buttons, and sender identities for critical security, verification, and billing messages. All changes automatically update outgoing emails while preserving hardcoded fail-safes.
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-center px-3 py-2">
                    <i class="bi-shield-lock-fill me-1"></i> Amazon SES v2 Protected
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($tab === 'templates') active fw-bold @endif" href="{{ url('panel/admin/email-templates?tab=templates') }}">
                <i class="bi-card-text me-1"></i> Templates Library ({{ $counts['all'] }})
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
                        <a href="{{ url('panel/admin/email-templates?category=all') }}" class="btn btn-sm @if($category === 'all') btn-dark @else btn-light border @endif">
                            All Templates ({{ $counts['all'] }})
                        </a>
                        <a href="{{ url('panel/admin/email-templates?category=security') }}" class="btn btn-sm @if($category === 'security') btn-dark @else btn-light border @endif">
                            <i class="bi-shield-lock text-danger me-1"></i> Security ({{ $counts['security'] }})
                        </a>
                        <a href="{{ url('panel/admin/email-templates?category=authentication') }}" class="btn btn-sm @if($category === 'authentication') btn-dark @else btn-light border @endif">
                            <i class="bi-person-check text-primary me-1"></i> Auth ({{ $counts['authentication'] }})
                        </a>
                        <a href="{{ url('panel/admin/email-templates?category=billing') }}" class="btn btn-sm @if($category === 'billing') btn-dark @else btn-light border @endif">
                            <i class="bi-credit-card text-success me-1"></i> Billing ({{ $counts['billing'] }})
                        </a>
                        <a href="{{ url('panel/admin/email-templates?category=notifications') }}" class="btn btn-sm @if($category === 'notifications') btn-dark @else btn-light border @endif">
                            <i class="bi-bell text-warning me-1"></i> System ({{ $counts['notifications'] }})
                        </a>
                    </div>
                </div>
                <div class="col-md-5">
                    <form method="GET" action="{{ url('panel/admin/email-templates') }}" class="d-flex gap-2">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search template name, subject...">
                        <button type="submit" class="btn btn-sm btn-dark">Search</button>
                        @if(!empty($q))
                            <a href="{{ url('panel/admin/email-templates?category=' . $category) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>Category</th>
                            <th>Subject Line</th>
                            <th>Sender Stream</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $tmpl)
                        <tr>
                            <td>
                                <div class="fw-bold text-body">{{ $tmpl->name }}</div>
                                <code class="small text-muted">{{ $tmpl->slug }}</code>
                            </td>
                            <td>
                                @if($tmpl->category === 'security')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Security OTP</span>
                                @elseif($tmpl->category === 'authentication')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Authentication</span>
                                @elseif($tmpl->category === 'billing')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Billing &amp; Plans</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">System Alert</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-body">{{ Str::limit($tmpl->subject, 48) }}</span>
                            </td>
                            <td>
                                <small class="text-muted d-block">
                                    {{ $tmpl->sender_email ?: ($settings->email_sender_address_auth ?? 'noreply@auth.imaginebuddy.com') }}
                                </small>
                            </td>
                            <td>
                                @if($tmpl->is_enabled)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                                @if($tmpl->is_system)
                                    <span class="badge bg-light text-dark border ms-1" title="Core System Template"><i class="bi-lock-fill"></i></span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ url('panel/admin/email-templates/' . $tmpl->id . '/preview') }}" class="btn btn-sm btn-outline-secondary me-1" target="_blank" title="View Full Preview">
                                    <i class="bi-eye me-1"></i> Preview
                                </a>
                                <a href="{{ url('panel/admin/email-templates/' . $tmpl->id . '/edit') }}" class="btn btn-sm btn-dark">
                                    <i class="bi-pencil me-1"></i> Edit
                                </a>
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

    <!-- TAB 2: MASTER BRANDING & SENDER CONFIG -->
    @if($tab === 'branding')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-custom border-0 p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="bi-palette me-1 text-primary"></i> Master Email Branding &amp; Visual Settings</h6>
                <p class="text-muted small mb-4">
                    These settings apply globally to all authentication and transactional email layouts sent through <code>auth.imaginebuddy.com</code>.
                </p>

                <form method="POST" action="{{ url('panel/admin/email-templates/branding') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Logo Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email Header Logo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 border rounded bg-light text-center" style="min-width: 120px; max-width: 200px;">
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

                    <!-- Colors -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Accent Color (Links &amp; Accents)</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_primary_color ?: '#4f46e5' }}" id="primaryColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_primary_color" id="primaryColorText" class="form-control" value="{{ $settings->email_primary_color ?: '#4f46e5' }}" required>
                            </div>
                            <small class="text-muted">Applied to hyperlink text and callout borders.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Button Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings->email_button_color ?: '#0f172a' }}" id="buttonColorPicker" style="max-width: 50px;">
                                <input type="text" name="email_button_color" id="buttonColorText" class="form-control" value="{{ $settings->email_button_color ?: '#0f172a' }}" required>
                            </div>
                            <small class="text-muted">Applied to main action CTA buttons (Verify, Reset, Renew).</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Default Sender Identities -->
                    <h6 class="fw-bold mb-3"><i class="bi-shield-check me-1 text-success"></i> Default Authentication Sender (auth.imaginebuddy.com)</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Sender Name <span class="text-danger">*</span></label>
                            <input type="text" name="email_sender_name_auth" class="form-control" value="{{ $settings->email_sender_name_auth ?: 'Imagine Buddy Security' }}" required>
                            <small class="text-muted">e.g. <em>Imagine Buddy Security</em> or <em>Imagine Buddy Support</em></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Sender Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email_sender_address_auth" class="form-control" value="{{ $settings->email_sender_address_auth ?: 'noreply@auth.imaginebuddy.com' }}" required>
                            <small class="text-muted">Must belong to verified identity <code>auth.imaginebuddy.com</code></small>
                        </div>
                    </div>

                    <!-- Footer Legal Disclaimer -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Custom Email Footer Disclaimer</label>
                        <textarea name="email_footer_text" class="form-control" rows="3" placeholder="&copy; 2026 Imagine Buddy Inc. All rights reserved. 123 Tech Lane, Suite 400.">{{ $settings->email_footer_text }}</textarea>
                        <small class="text-muted">Displayed at the very bottom of all transactional and authentication emails.</small>
                    </div>

                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi-check-lg me-1"></i> Save Branding Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side: Live Visual Preview Info -->
        <div class="col-lg-4">
            <div class="card shadow-custom border-0 p-4 mb-4">
                <h6 class="fw-bold mb-2"><i class="bi-info-circle me-1 text-primary"></i> Light-Mode Theme Details</h6>
                <p class="small text-muted mb-3">
                    In compliance with your specifications, all authentication emails render in an ultra-clean **light-mode responsive theme**:
                </p>
                <ul class="small text-muted ps-3 mb-0">
                    <li>Pure white card background (<code>#ffffff</code>) with soft border styling.</li>
                    <li>Slate dark heading typography (<code>#0f172a</code>) for maximum readability.</li>
                    <li>High-inboxing layout tested across Gmail, Apple Mail, and Outlook.</li>
                    <li>Automatic responsive adaptation on iPhone and Android screens.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const primaryPicker = document.getElementById('primaryColorPicker');
    const primaryText = document.getElementById('primaryColorText');
    if (primaryPicker && primaryText) {
        primaryPicker.addEventListener('input', function() { primaryText.value = primaryPicker.value; });
        primaryText.addEventListener('input', function() { primaryPicker.value = primaryText.value; });
    }

    const buttonPicker = document.getElementById('buttonColorPicker');
    const buttonText = document.getElementById('buttonColorText');
    if (buttonPicker && buttonText) {
        buttonPicker.addEventListener('input', function() { buttonText.value = buttonPicker.value; });
        buttonText.addEventListener('input', function() { buttonPicker.value = buttonText.value; });
    }
});
</script>
@endsection
