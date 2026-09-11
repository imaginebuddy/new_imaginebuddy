@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/settings/seo') }}">SEO Settings</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Edit: {{ $page->page_name }}</span>
</h5>

<div class="content">
    <div class="row">
        <!-- Edit Form Column -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi-pencil-square me-2"></i> SEO Metadata Configuration</h6>
                        <small class="text-muted">Page Key: <code>{{ $page->page_key }}</code></small>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" form="seoEditForm" value="1" {{ $page->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="isActiveSwitch">Active</label>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('panel/admin/settings/seo/update', $page->id) }}" id="seoEditForm">
                        @csrf

                        <!-- Page Label & Path -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Page Label</label>
                                <input type="text" name="page_name" class="form-control" value="{{ old('page_name', $page->page_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Target URL Path</label>
                                <div class="input-group">
                                    <span class="input-group-text small bg-light">{{ url('/') }}</span>
                                    <input type="text" class="form-control" value="{{ $page->path ?: '/' }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <!-- Meta Title -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold small mb-0">Meta Title</label>
                                <span class="badge" id="titleCountBadge">0 / 60 chars</span>
                            </div>
                            <input type="text" name="meta_title" id="metaTitleInput" class="form-control" value="{{ old('meta_title', $page->meta_title) }}" placeholder="e.g. Pricing Plans & Credit Packages - ImagineBuddy">
                            <small class="text-muted">Recommended: 50–60 characters. Appears as the clickable headline in search engine results.</small>
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold small mb-0">Meta Description</label>
                                <span class="badge" id="descCountBadge">0 / 160 chars</span>
                            </div>
                            <textarea name="meta_description" id="metaDescInput" class="form-control" rows="3" placeholder="e.g. Choose the perfect subscription plan or credit package for high-resolution AI downloads...">{{ old('meta_description', $page->meta_description) }}</textarea>
                            <small class="text-muted">Recommended: 130–160 characters. A concise summary for search snippets and AI answer engines.</small>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="keyword1, keyword2, keyword3">
                            <small class="text-muted">Comma-separated terms. Used for internal search and secondary indexing.</small>
                        </div>

                        <!-- Advanced Directives Accordion -->
                        <div class="accordion mb-4" id="advancedSeoAccordion">
                            <div class="accordion-item border rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="headingAdv">
                                    <button class="accordion-button collapsed bg-light py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdv">
                                        <i class="bi-sliders me-2"></i> Advanced Directives (Robots, Canonical & Social Cards)
                                    </button>
                                </h2>
                                <div id="collapseAdv" class="accordion-collapse collapse" data-bs-parent="#advancedSeoAccordion">
                                    <div class="accordion-body p-3 bg-white">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Robots Meta Directive</label>
                                                <select name="robots" class="form-select">
                                                    <option value="index, follow" {{ old('robots', $page->robots) == 'index, follow' ? 'selected' : '' }}>index, follow (Default - Allow indexing)</option>
                                                    <option value="noindex, follow" {{ old('robots', $page->robots) == 'noindex, follow' ? 'selected' : '' }}>noindex, follow (Hide page, follow links)</option>
                                                    <option value="noindex, nofollow" {{ old('robots', $page->robots) == 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (Complete block)</option>
                                                    <option value="index, nofollow" {{ old('robots', $page->robots) == 'index, nofollow' ? 'selected' : '' }}>index, nofollow (Index page, ignore links)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Schema.org Structured Type</label>
                                                <select name="schema_type" class="form-select">
                                                    <option value="WebPage" {{ old('schema_type', $page->schema_type) == 'WebPage' ? 'selected' : '' }}>WebPage (Generic)</option>
                                                    <option value="WebSite" {{ old('schema_type', $page->schema_type) == 'WebSite' ? 'selected' : '' }}>WebSite (Homepage)</option>
                                                    <option value="PricingPage" {{ old('schema_type', $page->schema_type) == 'PricingPage' ? 'selected' : '' }}>PricingPage</option>
                                                    <option value="ContactPage" {{ old('schema_type', $page->schema_type) == 'ContactPage' ? 'selected' : '' }}>ContactPage</option>
                                                    <option value="CollectionPage" {{ old('schema_type', $page->schema_type) == 'CollectionPage' ? 'selected' : '' }}>CollectionPage</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Custom Canonical URL Override</label>
                                                <input type="url" name="canonical_url" class="form-control" value="{{ old('canonical_url', $page->canonical_url) }}" placeholder="Leave empty for auto-generated clean canonical URL">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Open Graph Title (Social)</label>
                                                <input type="text" name="og_title" class="form-control" value="{{ old('og_title', $page->og_title) }}" placeholder="Defaults to Meta Title if blank">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Twitter Card Format</label>
                                                <select name="twitter_card" class="form-select">
                                                    <option value="summary_large_image" {{ old('twitter_card', $page->twitter_card) == 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Large preview card)</option>
                                                    <option value="summary" {{ old('twitter_card', $page->twitter_card) == 'summary' ? 'selected' : '' }}>summary (Small thumbnail card)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small">Open Graph Description (Social)</label>
                                                <textarea name="og_description" class="form-control" rows="2" placeholder="Defaults to Meta Description if blank">{{ old('og_description', $page->og_description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ url('panel/admin/settings/seo') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-dark rounded-pill px-5">
                                <i class="bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Google SERP Preview Column -->
        <div class="col-lg-5">
            <div class="card shadow-custom border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi-google me-2 text-primary"></i> Live Google Search Preview</h6>
                    <small class="text-muted">Simulates how your page snippet will appear in Google desktop and mobile search results.</small>
                </div>

                <div class="card-body p-4">
                    <!-- Google SERP Card Container -->
                    <div class="serp-preview-box p-3 rounded-3 border bg-white mb-4">
                        <!-- URL / Breadcrumb line -->
                        <div class="d-flex align-items-center mb-1">
                            <div class="google-favicon me-2 rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px;">
                                <img src="{{ url('public/img', $settings->favicon) }}" width="14" height="14" alt="favicon" onerror="this.src='{{ url('public/img/logo.png') }}'">
                            </div>
                            <div class="small text-truncate" style="font-family: Arial, sans-serif; font-size: 13px; color: #202124;">
                                <span class="fw-semibold">{{ config('settings.title', 'ImagineBuddy') }}</span>
                                <span class="text-muted ms-1" style="font-size: 12px;">{{ url($page->path ?: '/') }}</span>
                            </div>
                        </div>

                        <!-- Title Line -->
                        <h6 class="serp-title mb-1 text-truncate" id="previewTitle" style="font-family: Arial, sans-serif; font-size: 18px; color: #1a0dab; cursor: pointer; text-decoration: none;">
                            {{ $page->meta_title ?: config('settings.title', 'ImagineBuddy') }}
                        </h6>

                        <!-- Description Line -->
                        <p class="serp-desc mb-0" id="previewDesc" style="font-family: Arial, sans-serif; font-size: 14px; color: #4d5156; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $page->meta_description ?: 'Discover thousands of curated AI image prompts, creative stock photos, and prompt engineering assets.' }}
                        </p>
                    </div>

                    <!-- SEO Scorecard -->
                    <h6 class="fw-bold small text-uppercase text-muted mb-3">Snippet Quality Metrics</h6>

                    <!-- Title Metric -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span>Title Length</span>
                            <span id="titleMetricText" class="fw-semibold text-muted">0 / 60 chars</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" id="titleProgressBar" role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- Description Metric -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span>Description Length</span>
                            <span id="descMetricText" class="fw-semibold text-muted">0 / 160 chars</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" id="descProgressBar" role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-muted mb-0 mt-3">
                        <i class="bi-lightbulb me-1 text-warning"></i>
                        <strong>Pro Tip:</strong> Keep titles under 60 characters and descriptions between 130–160 characters so Google and AI search engines don't truncate your snippets.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.shadow-custom { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
.serp-preview-box { box-shadow: 0 1px 6px rgba(32,33,36,0.18); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('metaTitleInput');
    const descInput = document.getElementById('metaDescInput');
    const previewTitle = document.getElementById('previewTitle');
    const previewDesc = document.getElementById('previewDesc');
    const titleBadge = document.getElementById('titleCountBadge');
    const descBadge = document.getElementById('descCountBadge');
    const titleProgressBar = document.getElementById('titleProgressBar');
    const descProgressBar = document.getElementById('descProgressBar');
    const titleMetricText = document.getElementById('titleMetricText');
    const descMetricText = document.getElementById('descMetricText');

    function updateTitle() {
        const val = titleInput.value.trim();
        const len = val.length;
        previewTitle.textContent = val || "{{ config('settings.title', 'ImagineBuddy') }}";

        titleBadge.textContent = len + ' / 60 chars';
        titleMetricText.textContent = len + ' / 60 chars';
        const percent = Math.min(100, (len / 60) * 100);
        titleProgressBar.style.width = percent + '%';

        if (len === 0) {
            titleBadge.className = 'badge bg-secondary';
            titleProgressBar.className = 'progress-bar bg-secondary';
        } else if (len >= 45 && len <= 60) {
            titleBadge.className = 'badge bg-success';
            titleProgressBar.className = 'progress-bar bg-success';
        } else if (len > 60) {
            titleBadge.className = 'badge bg-danger';
            titleProgressBar.className = 'progress-bar bg-danger';
        } else {
            titleBadge.className = 'badge bg-warning text-dark';
            titleProgressBar.className = 'progress-bar bg-warning';
        }
    }

    function updateDesc() {
        const val = descInput.value.trim();
        const len = val.length;
        previewDesc.textContent = val || 'No description configured. Fallback global description will be used.';

        descBadge.textContent = len + ' / 160 chars';
        descMetricText.textContent = len + ' / 160 chars';
        const percent = Math.min(100, (len / 160) * 100);
        descProgressBar.style.width = percent + '%';

        if (len === 0) {
            descBadge.className = 'badge bg-secondary';
            descProgressBar.className = 'progress-bar bg-secondary';
        } else if (len >= 120 && len <= 160) {
            descBadge.className = 'badge bg-success';
            descProgressBar.className = 'progress-bar bg-success';
        } else if (len > 160) {
            descBadge.className = 'badge bg-danger';
            descProgressBar.className = 'progress-bar bg-danger';
        } else {
            descBadge.className = 'badge bg-warning text-dark';
            descProgressBar.className = 'progress-bar bg-warning';
        }
    }

    titleInput.addEventListener('input', updateTitle);
    descInput.addEventListener('input', updateDesc);

    // Initial run
    updateTitle();
    updateDesc();
});
</script>
@endsection
