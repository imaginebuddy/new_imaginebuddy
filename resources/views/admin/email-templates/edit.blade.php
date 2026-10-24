@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/email-templates') }}">Email Templates</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Edit: {{ $template->name }}</span>

    <a href="{{ url('panel/admin/email-templates') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0">
        <i class="bi-arrow-left me-1"></i> Back to Templates
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

    <div class="row g-4">
        <!-- LEFT COLUMN: TEMPLATE EDITOR FORM -->
        <div class="col-lg-7">
            <div class="card shadow-custom border-0 p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-0 text-body"><i class="bi-pencil-square me-1 text-primary"></i> Template Details</h6>
                        <small class="text-muted">{{ $template->description }}</small>
                    </div>
                    <span class="badge bg-light text-dark border">
                        Slug: <code>{{ $template->slug }}</code>
                    </span>
                </div>

                <form method="POST" action="{{ url('panel/admin/email-templates/' . $template->id) }}" id="templateEditForm">
                    @csrf

                    <!-- Subject Line -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Subject Line <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required>
                        <small class="text-muted">You can use dynamic variables in the subject line (e.g. <code>{code}</code> or <code>{app_name}</code>).</small>
                    </div>

                    <!-- Sender Overrides (Optional) -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Sender Name Override</label>
                            <input type="text" name="sender_name" class="form-control form-control-sm" value="{{ old('sender_name', $template->sender_name) }}" placeholder="{{ $settings->email_sender_name_auth ?: 'Imagine Buddy Security' }}">
                            <small class="text-muted" style="font-size: 11px;">Leave empty to use global default.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Sender Email Address Override</label>
                            <input type="email" name="sender_email" class="form-control form-control-sm" value="{{ old('sender_email', $template->sender_email) }}" placeholder="{{ $settings->email_sender_address_auth ?: 'noreply@auth.imaginebuddy.com' }}">
                            <small class="text-muted" style="font-size: 11px;">Must be verified under <code>auth.imaginebuddy.com</code>.</small>
                        </div>
                    </div>

                    <!-- Dynamic Variables Pallet (Click to Copy / Insert) -->
                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-uppercase text-muted">
                                <i class="bi-braces me-1 text-primary"></i> Available Dynamic Variables:
                            </span>
                            <small class="text-muted" style="font-size: 11px;">Click variable pill to copy</small>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if(!empty($template->variables))
                                @foreach($template->variables as $var)
                                    @php $isRequired = in_array($var, $requiredVars); @endphp
                                    <button type="button" class="btn btn-sm btn-white border var-pill text-body @if($isRequired) border-danger @endif" data-var="{{ $var }}" title="{{ $isRequired ? 'MANDATORY: Must be present in email content' : 'Click to copy' }}">
                                        <code>{{ $var }}</code>
                                        @if($isRequired)
                                            <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 10px;">Required</span>
                                        @endif
                                    </button>
                                @endforeach
                            @endif
                        </div>
                        @if(!empty($requiredVars))
                            <small class="text-danger d-block mt-2" style="font-size: 11px;">
                                <i class="bi-exclamation-circle me-1"></i> Required tokens ({{ implode(', ', $requiredVars) }}) must remain in the content to ensure verification and security workflows never break.
                            </small>
                        @endif
                    </div>

                    <!-- Visual Editor (CKEditor) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">HTML Content Body <span class="text-danger">*</span></label>
                        <textarea name="content_html" id="contentHtmlEditor" class="form-control" rows="12">{{ old('content_html', $template->content_html) }}</textarea>
                    </div>

                    <!-- Plain Text Fallback -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Plain Text Fallback (Optional)</label>
                        <textarea name="content_plain" class="form-control form-control-sm" rows="3" placeholder="Leave empty to automatically generate from HTML.">{{ old('content_plain', $template->content_plain) }}</textarea>
                    </div>

                    <!-- Status Toggle -->
                    <div class="mb-4">
                        @if($template->is_system && in_array($template->slug, ['two-factor-code', 'verify-account', 'password-reset']))
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label text-muted small">
                                    <i class="bi-lock-fill text-warning me-1"></i>
                                    Always Active (Critical System Security Workflow)
                                </label>
                            </div>
                        @else
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="isEnabledCheck" value="1" @if(old('is_enabled', $template->is_enabled)) checked @endif>
                                <label class="form-check-label fw-semibold small" for="isEnabledCheck">Enable this custom template</label>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <button type="submit" class="btn btn-dark px-4">
                            <i class="bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ url('panel/admin/email-templates') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: LIVE DEVICE PREVIEW & REAL TEST SENDER -->
        <div class="col-lg-5">
            <!-- Real Test Email Dispatch Box -->
            <div class="card shadow-custom border-0 p-3 mb-4">
                <h6 class="fw-bold mb-1 text-body"><i class="bi-send me-1 text-primary"></i> Send Real Test Preview</h6>
                <small class="text-muted mb-3 d-block">
                    Dispatches an exact replica with sample test data through Amazon SES (<code>{{ $template->sender_email ?: ($settings->email_sender_address_auth ?? 'noreply@auth.imaginebuddy.com') }}</code>) to verify real inbox rendering.
                </small>
                <form method="POST" action="{{ url('panel/admin/email-templates/' . $template->id . '/test-send') }}" class="row g-2">
                    @csrf
                    <div class="col-8">
                        <input type="email" name="test_email" class="form-control form-control-sm" placeholder="admin@imaginebuddy.com" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-sm btn-dark w-100">
                            <i class="bi-lightning-charge me-1"></i> Send Test
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Responsive Device Previewer -->
            <div class="card shadow-custom border-0 p-3">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="fw-bold small text-body">
                        <i class="bi-eye me-1 text-primary"></i> Live Template Preview:
                    </span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="btnPreviewDesktop" title="Desktop 600px">
                            <i class="bi-display me-1"></i> Desktop
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnPreviewMobile" title="Mobile 375px">
                            <i class="bi-phone me-1"></i> Mobile
                        </button>
                    </div>
                </div>

                <!-- Iframe Preview Container -->
                <div class="preview-container text-center bg-light p-2 rounded border" style="min-height: 520px; overflow-x: auto;">
                    <iframe id="emailPreviewFrame" src="{{ url('panel/admin/email-templates/' . $template->id . '/preview') }}" style="width: 100%; max-width: 600px; height: 560px; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; transition: max-width 0.25s ease;" frameborder="0"></iframe>
                </div>
                <small class="text-muted text-center d-block mt-2" style="font-size: 11px;">
                    Preview renders with sample variables inside your Light-Mode master layout.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize CKEditor for HTML Content
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('contentHtmlEditor')) {
        CKEDITOR.replace('contentHtmlEditor', {
            height: 300,
            allowedContent: true,
            toolbar: [
                { name: 'clipboard', items: ['Undo', 'Redo'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Table', 'HorizontalRule'] },
                { name: 'tools', items: ['Maximize', 'Source'] }
            ]
        });
    }

    // 2. Clickable Dynamic Variable Badges
    document.querySelectorAll('.var-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            const varText = this.getAttribute('data-var');
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.contentHtmlEditor) {
                CKEDITOR.instances.contentHtmlEditor.insertText(varText);
            } else {
                navigator.clipboard.writeText(varText);
            }
            // Flash pill visual feedback
            const originalBg = pill.style.backgroundColor;
            pill.style.backgroundColor = '#dcfce7';
            setTimeout(() => { pill.style.backgroundColor = originalBg; }, 400);
        });
    });

    // 3. Responsive Device Preview Toggles
    const frame = document.getElementById('emailPreviewFrame');
    const btnDesktop = document.getElementById('btnPreviewDesktop');
    const btnMobile = document.getElementById('btnPreviewMobile');

    if (btnDesktop && btnMobile && frame) {
        btnDesktop.addEventListener('click', function() {
            btnDesktop.classList.add('active');
            btnMobile.classList.remove('active');
            frame.style.maxWidth = '600px';
        });

        btnMobile.addEventListener('click', function() {
            btnMobile.classList.add('active');
            btnDesktop.classList.remove('active');
            frame.style.maxWidth = '375px';
        });
    }
});
</script>
@endsection
