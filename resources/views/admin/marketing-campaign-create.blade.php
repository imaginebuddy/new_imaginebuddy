@extends('admin.layout')

@section('content')
<style>
.preset-btn {
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #1e293b !important;
    font-weight: 500;
    transition: all 0.2s ease;
}
.preset-btn:hover {
    background-color: #111827 !important;
    color: #ffffff !important;
    border-color: #111827 !important;
}
.preset-btn:hover i {
    color: #ffffff !important;
}
.preset-btn.active {
    background-color: #111827 !important;
    color: #ffffff !important;
    border-color: #111827 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
.preset-btn.active i {
    color: #00d690 !important;
}
</style>

<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/email-marketing/campaigns') }}">Campaigns</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Create Broadcast Campaign</span>

    <a href="{{ url('panel/admin/email-marketing/campaigns') }}" class="btn btn-sm btn-outline-dark float-lg-end mt-1 mt-lg-0 ms-2">
        <i class="bi-arrow-left me-1"></i> Back to Campaigns
    </a>
    <button type="button" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0" data-bs-toggle="collapse" data-bs-target="#composerGuide" aria-expanded="false" aria-controls="composerGuide">
        <i class="bi-book-half me-1"></i> Composer Guide &amp; Tags
    </button>
</h5>

<div class="content">
    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('errors.errors-forms')

    <!-- Collapsible Composer Quick Guide (Feature 9) -->
    <div class="collapse mb-4" id="composerGuide">
        <div class="card shadow-custom border-0 bg-body-tertiary">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 text-body">
                        <i class="bi-palette text-primary me-2"></i> Visual Composer Guide &amp; Dynamic Tags Reference (Feature 9)
                    </h6>
                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#composerGuide">
                        <i class="bi-x-lg"></i>
                    </button>
                </div>
                <div class="row g-3 small">
                    <div class="col-md-4">
                        <div class="p-3 bg-body rounded border h-100">
                            <strong class="text-primary d-block mb-1"><i class="bi-magic me-1"></i> 1-Click Template Presets</strong>
                            <p class="text-muted mb-0">Clicking any preset button instantly replaces the subject line and loads a studio-tested responsive HTML layout optimized for high deliverability.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-body rounded border h-100">
                            <strong class="text-primary d-block mb-1"><i class="bi-braces me-1"></i> Personalization Tags</strong>
                            <ul class="text-muted mb-0 ps-3">
                                <li><code>{name}</code> &rarr; Customer's first name (fallback: "Friend").</li>
                                <li><code>{email}</code> &rarr; Customer's email address.</li>
                                <li><code>{vaultUrl}</code> &rarr; Direct link to 50+ Prompts Vault.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-body rounded border h-100">
                            <strong class="text-primary d-block mb-1"><i class="bi-phone me-1"></i> Device Preview &amp; Test Sending</strong>
                            <p class="text-muted mb-0">Toggle Desktop (600px) and Mobile (375px) in the live preview pane. Always use the <strong>"Send Test"</strong> box to inspect email rendering in your own inbox before broadcasting.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1-Click Preset Template Pickers -->
    <div class="card shadow-custom border-0 mb-4 p-3 bg-light">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="fw-bold small text-uppercase text-muted">
                <i class="bi-magic me-1 text-primary"></i> 1-Click Email Template Presets:
            </span>
            <small class="text-muted">Click any preset to instantly populate subject &amp; responsive layout</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($presets as $key => $preset)
            <button type="button" class="btn btn-sm preset-btn @if($loop->first) active @endif" data-preset="{{ $key }}">
                <i class="bi-file-earmark-text me-1"></i> {{ $preset['name'] }}
            </button>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ url('panel/admin/email-marketing/campaigns') }}" id="campaignForm">
        @csrf
        <input type="hidden" name="template_preset" id="templatePresetInput" value="prompts_showcase">

        <div class="row g-4">
            <!-- Left Column: Settings, Content & CKEditor -->
            <div class="col-lg-7">
                <div class="card shadow-custom border-0 p-4 mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi-pencil-square me-1"></i> Campaign Details</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Internal Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="campaignName" class="form-control" placeholder="e.g. October 2026 Commercial Photoshoot Drop" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Email Subject Line <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="campaignSubject" class="form-control" placeholder="Subject shown in recipient's inbox" value="{{ old('subject') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Preview / Pre-Header Snippet</label>
                            <input type="text" name="preview_text" id="campaignPreview" class="form-control" placeholder="Secondary snippet text in inbox" value="{{ old('preview_text') }}">
                        </div>
                    </div>

                    <!-- Target Segment Selection & Live Counter -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Audience Segment <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="target_segment" id="targetSegmentSelect" class="form-select">
                                <option value="all_verified" selected>All Deliverable Audience (Google OAuth + Double Opt-In)</option>
                                <option value="google_users">Google OAuth Opted-In Users Only</option>
                                <option value="newsletter_subscribers">Double Opt-In Newsletter Subscribers Only</option>
                                <option value="paying_subscribers">Active Paid Subscribers Only</option>
                                <option value="test_list">Admin Test Email Only</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button" id="previewSegmentBtn" title="Inspect Segment Recipients">
                                <i class="bi-people me-1"></i> Preview Segment (<span id="segmentCountBadge">{{ number_format($totalAudience) }}</span>)
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="bi-shield-check text-success me-1"></i> All suppressed, bounced, and unsubscribed emails are automatically excluded.
                        </small>
                    </div>

                    <!-- Personalization Tags Pill Bar -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1">Email Body Content <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-2">
                            <span class="small text-muted me-1">Insert Dynamic Tag:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 tag-btn" data-tag="{name}">{name}</button>
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 tag-btn" data-tag="{email}">{email}</button>
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 tag-btn" data-tag="{vaultUrl}">{vaultUrl}</button>
                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 tag-btn" data-tag="{unsubscribe_url}">{unsubscribe_url}</button>
                        </div>
                        <!-- CKEditor Textarea -->
                        <textarea name="content" id="campaignContent" class="form-control" rows="14">{{ old('content', $presets['prompts_showcase']['html']) }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-3">
                        <button type="submit" name="action" value="save_draft" class="btn btn-outline-secondary">
                            <i class="bi-save me-1"></i> Save Draft
                        </button>
                        <button type="submit" name="action" value="send_now" class="btn btn-success fw-bold px-4" id="sendNowBtn">
                            <i class="bi-send-fill me-1"></i> Send Campaign Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Device Preview & Instant Test Sender -->
            <div class="col-lg-5">
                <!-- Device Preview Container -->
                <div class="card shadow-custom border-0 p-3 mb-4 sticky-top" style="top: 20px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0"><i class="bi-eye me-1"></i> Live Email Preview</h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-dark active" id="previewDesktopBtn">
                                <i class="bi-display me-1"></i> Desktop
                            </button>
                            <button type="button" class="btn btn-outline-dark" id="previewMobileBtn">
                                <i class="bi-phone me-1"></i> Mobile
                            </button>
                        </div>
                    </div>

                    <!-- Preview Frame Wrapper -->
                    <div class="bg-dark rounded p-2 text-center" style="min-height: 440px; overflow-y: auto;">
                        <div id="previewFrameContainer" style="width: 100%; max-width: 600px; margin: 0 auto; background: #0b0f19; border-radius: 8px; border: 1px solid #1e293b; text-align: left; transition: all 0.25s ease;">
                            <div class="p-3 border-bottom border-secondary text-muted small" style="background:#111827;">
                                <div class="text-white fw-bold mb-1" id="previewSubjectText">🔥 3 New AI Product Photoshoot Prompts Ready to Copy</div>
                                <div class="text-secondary small" style="font-size:11px;">From: Imagine Buddy &lt;hello@updates.imaginebuddy.com&gt;</div>
                            </div>
                            <div class="p-3 text-light" id="previewBodyContent" style="font-size: 14px; line-height: 1.5; color: #cbd5e1;">
                                {!! $presets['prompts_showcase']['html'] !!}
                            </div>
                            <div class="p-3 text-center border-top border-secondary text-secondary small" style="font-size: 11px;">
                                &copy; {{ date('Y') }} Imagine Buddy. <a href="#" class="text-muted">Unsubscribe with 1 click</a>
                            </div>
                        </div>
                    </div>

                    <!-- Live Test Dispatch Box -->
                    <div class="mt-3 pt-3 border-top">
                        <label class="form-label fw-semibold small mb-1">
                            <i class="bi-lightning-charge-fill text-warning me-1"></i> Send Real Test Preview to Your Inbox:
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="email" id="testEmailInput" class="form-control" placeholder="your-email@example.com" value="{{ auth()->user()->email }}">
                            <button class="btn btn-dark" type="button" id="sendTestBtn">
                                <span class="test-btn-text">Send Test</span>
                                <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                            </button>
                        </div>
                        <div id="testAlertBox" class="mt-2 small d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Segment Previewer -->
<div class="modal fade" id="segmentModal" tabindex="-1" aria-labelledby="segmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="segmentModalLabel">
                    <i class="bi-people-fill text-primary me-2"></i> Segment Audience Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Eligible Recipients:</span>
                        <h4 class="mb-0 fw-bold text-dark" id="modalSegmentCount">0</h4>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi-shield-check me-1"></i> 100% Deliverable (Zero Suppressed)
                    </span>
                </div>

                <h6 class="fw-bold mb-2 small text-uppercase text-muted">Sample Recipients (First 8):</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Account Source</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody id="modalSampleTable">
                            <tr><td colspan="4" class="text-center py-3 text-muted">Loading segment sample...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const presets = @json($presets);

    // Initialize CKEditor on campaignContent textarea
    let editorInstance = null;
    if (typeof CKEDITOR !== 'undefined') {
        editorInstance = CKEDITOR.replace('campaignContent', {
            height: 380,
            contentsCss: ['{{ asset("public/css/bootstrap.min.css") }}'],
        });

        editorInstance.on('change', function () {
            updateLivePreview();
        });
    }

    const nameInput = document.getElementById('campaignName');
    const subjectInput = document.getElementById('campaignSubject');
    const previewInput = document.getElementById('campaignPreview');
    const previewSubjectText = document.getElementById('previewSubjectText');
    const previewBodyContent = document.getElementById('previewBodyContent');
    const templatePresetInput = document.getElementById('templatePresetInput');

    // 1-Click Template Presets Loader
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const presetKey = this.getAttribute('data-preset');
            const p = presets[presetKey];
            if (!p) return;

            document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            templatePresetInput.value = presetKey;
            if (!nameInput.value) nameInput.value = p.name;
            subjectInput.value = p.subject;
            previewInput.value = p.preview;

            if (editorInstance) {
                editorInstance.setData(p.html);
            } else {
                document.getElementById('campaignContent').value = p.html;
            }

            updateLivePreview();
        });
    });

    // Subject real-time preview sync
    subjectInput.addEventListener('input', function () {
        previewSubjectText.textContent = this.value || 'Email Subject Line';
    });

    function updateLivePreview() {
        const html = editorInstance ? editorInstance.getData() : document.getElementById('campaignContent').value;
        previewBodyContent.innerHTML = html.replace(/\{name\}/g, 'Creator')
                                           .replace(/\{email\}/g, 'creator@example.com')
                                           .replace(/\{vaultUrl\}/g, '{{ url("prompts/free") }}')
                                           .replace(/\{unsubscribe_url\}/g, '#');
    }

    // Dynamic Tag Inserter
    document.querySelectorAll('.tag-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const tag = this.getAttribute('data-tag');
            if (editorInstance) {
                editorInstance.insertText(tag);
            }
        });
    });

    // Desktop vs Mobile Device Preview Toggles
    const frameContainer = document.getElementById('previewFrameContainer');
    document.getElementById('previewDesktopBtn').addEventListener('click', function () {
        this.classList.add('active');
        document.getElementById('previewMobileBtn').classList.remove('active');
        frameContainer.style.maxWidth = '600px';
    });

    document.getElementById('previewMobileBtn').addEventListener('click', function () {
        this.classList.add('active');
        document.getElementById('previewDesktopBtn').classList.remove('active');
        frameContainer.style.maxWidth = '375px';
    });

    // Segment Selector & Live Counter
    const segmentSelect = document.getElementById('targetSegmentSelect');
    const segmentCountBadge = document.getElementById('segmentCountBadge');

    segmentSelect.addEventListener('change', function () {
        fetchSegmentCount(this.value);
    });

    function fetchSegmentCount(segment) {
        segmentCountBadge.textContent = '...';
        fetch("{{ url('panel/admin/email-marketing/segment-preview') }}?segment=" + encodeURIComponent(segment))
            .then(res => res.json())
            .then(data => {
                segmentCountBadge.textContent = data.formatted_count;
            })
            .catch(() => {
                segmentCountBadge.textContent = '0';
            });
    }

    // Modal: Segment Preview
    const segmentModal = new bootstrap.Modal(document.getElementById('segmentModal'));
    document.getElementById('previewSegmentBtn').addEventListener('click', function () {
        const segment = segmentSelect.value;
        const modalTable = document.getElementById('modalSampleTable');
        const modalCount = document.getElementById('modalSegmentCount');

        modalTable.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading audience...</td></tr>';
        modalCount.textContent = '...';
        segmentModal.show();

        fetch("{{ url('panel/admin/email-marketing/segment-preview') }}?segment=" + encodeURIComponent(segment))
            .then(res => res.json())
            .then(data => {
                modalCount.textContent = data.formatted_count;
                if (!data.sample || data.sample.length === 0) {
                    modalTable.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">No recipients found in this segment.</td></tr>';
                    return;
                }
                modalTable.innerHTML = data.sample.map(r => `
                    <tr>
                        <td class="fw-semibold">${r.name}</td>
                        <td>${r.email}</td>
                        <td><span class="badge bg-secondary-subtle text-dark border">${r.source}</span></td>
                        <td class="small text-muted">${r.joined_at}</td>
                    </tr>
                `).join('');
            });
    });

    // Real Live Test Email Dispatcher
    const sendTestBtn = document.getElementById('sendTestBtn');
    const testEmailInput = document.getElementById('testEmailInput');
    const testAlertBox = document.getElementById('testAlertBox');
    const testBtnText = sendTestBtn.querySelector('.test-btn-text');
    const testSpinner = sendTestBtn.querySelector('.spinner-border');

    sendTestBtn.addEventListener('click', function () {
        const testEmail = testEmailInput.value.trim();
        const subject = subjectInput.value.trim();
        const content = editorInstance ? editorInstance.getData() : document.getElementById('campaignContent').value;

        if (!testEmail) {
            showTestAlert('Please enter a test email address.', 'alert-danger');
            return;
        }

        if (!subject || !content) {
            showTestAlert('Please enter subject and content before testing.', 'alert-danger');
            return;
        }

        sendTestBtn.disabled = true;
        testSpinner.classList.remove('d-none');
        testBtnText.textContent = 'Sending...';
        testAlertBox.classList.add('d-none');

        fetch("{{ url('panel/admin/email-marketing/test-preview') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                test_email: testEmail,
                subject: subject,
                preview_text: previewInput.value,
                content: content
            })
        })
        .then(res => res.json())
        .then(data => {
            showTestAlert('✅ ' + data.message, 'alert-success');
        })
        .catch(() => {
            showTestAlert('Failed to send test. Check SES configuration.', 'alert-danger');
        })
        .finally(() => {
            sendTestBtn.disabled = false;
            testSpinner.classList.add('d-none');
            testBtnText.textContent = 'Send Test';
        });
    });

    function showTestAlert(msg, cls) {
        testAlertBox.className = 'mt-2 small alert p-2 ' + cls;
        testAlertBox.textContent = msg;
        testAlertBox.classList.remove('d-none');
    }

    // Send Now Confirmation
    document.getElementById('sendNowBtn').addEventListener('click', function (e) {
        const count = segmentCountBadge.textContent;
        if (!confirm('Are you sure you want to broadcast this campaign to ' + count + ' recipients now?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
