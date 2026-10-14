@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/seo-pages') }}">SEO Landing Pages</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Manage: {{ $page->title }}</span>

      <button type="button" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0 ms-2" data-bs-toggle="modal" data-bs-target="#aiJsonUpdateModal">
        <i class="bi bi-robot me-1"></i> Update via AI JSON
      </button>

      @if ($page->status == 'published')
        <a href="{{ url('guides/' . $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0 ms-2">
          <i class="bi-box-arrow-up-right me-1"></i> View Live Page
        </a>
      @else
        <a href="{{ url('guides/preview/' . $page->preview_token) }}" target="_blank" class="btn btn-sm btn-outline-info float-lg-end mt-1 mt-lg-0 ms-2">
          <i class="bi-eye me-1"></i> Preview Draft
        </a>
      @endif
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi-x-lg"></i>
        </button>
      </div>
      @endif

      @include('errors.errors-forms')

      <!-- Container for dynamic AJAX notifications -->
      <div id="ajaxAlertContainer"></div>

      <!-- MAIN EDIT FORM -->
      <form method="POST" action="{{ url('panel/admin/seo-pages/update/' . $page->id) }}" enctype="multipart/form-data">
        @csrf

        <!-- CARD 1: Core Details & URLs -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-gear-wide-connected me-2 text-primary"></i>1. Core Information &amp; Publishing State
            </h5>
            <span class="badge {{ $page->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
              Status: {{ ucfirst($page->status) }}
            </span>
          </div>
          <div class="card-body p-lg-4">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Public Page Title (H1) <span class="text-danger">*</span></label>
                <input type="text" required class="form-control" name="title" value="{{ old('title', $page->title) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                <input type="text" required class="form-control" name="slug" value="{{ old('slug', $page->slug) }}">
                <small class="text-muted d-block mt-1">Live URL: <code>{{ url('guides/' . $page->slug) }}</code> (Changes automatically trigger a 301 redirect)</small>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Internal Reference Name</label>
                <input type="text" class="form-control" name="internal_name" value="{{ old('internal_name', $page->internal_name) }}">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Category</label>
                <select name="category_id" class="form-select">
                  <option value="">General / None</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $page->category_id) == $cat->id)>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Author / Owner <span class="text-danger">*</span></label>
                <select name="user_id" required class="form-select">
                  @foreach ($authors as $author)
                    <option value="{{ $author->id }}" @selected(old('user_id', $page->user_id) == $author->id)>
                      {{ $author->name ?: $author->username }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Excerpt / Summary</label>
                <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $page->excerpt) }}</textarea>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Target Keyword</label>
                <input type="text" class="form-control" name="primary_keyword" value="{{ old('primary_keyword', $page->primary_keyword) }}">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Search Intent</label>
                <select name="search_intent" class="form-select">
                  <option value="commercial" @selected(old('search_intent', $page->search_intent) == 'commercial')>Commercial</option>
                  <option value="informational" @selected(old('search_intent', $page->search_intent) == 'informational')>Informational</option>
                  <option value="transactional" @selected(old('search_intent', $page->search_intent) == 'transactional')>Transactional</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Target Audience</label>
                <input type="text" class="form-control" name="target_audience" value="{{ old('target_audience', $page->target_audience) }}">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Secondary &amp; Related Keywords</label>
                <input type="text" class="form-control" name="secondary_keywords" value="{{ old('secondary_keywords', $page->secondary_keywords) }}">
              </div>

              <!-- Cover Image -->
              <div class="col-md-6">
                <label class="form-label fw-bold">Hero Featured Image</label>
                <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="form-control custom-file">
                @if ($page->featured_image)
                  <div class="mt-2 d-flex align-items-center gap-2">
                    <img src="{{ url('public/uploads/guides/' . $page->featured_image) }}" class="rounded border shadow-xs" width="80" height="50" style="object-fit: cover;">
                    <small class="text-muted">{{ $page->featured_image }}</small>
                  </div>
                @endif
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">Image Alt Text</label>
                <input type="text" class="form-control" name="featured_image_alt" value="{{ old('featured_image_alt', $page->featured_image_alt) }}">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Publishing State</label>
                <select name="status" class="form-select">
                  <option value="published" @selected(old('status', $page->status) == 'published')>Published</option>
                  <option value="draft" @selected(old('status', $page->status) == 'draft')>Draft</option>
                  <option value="scheduled" @selected(old('status', $page->status) == 'scheduled')>Scheduled</option>
                  <option value="archived" @selected(old('status', $page->status) == 'archived')>Archived</option>
                </select>
              </div>

              <div class="col-md-8">
                <div class="form-check form-switch mt-4 pt-2">
                  <input class="form-check-input" type="checkbox" role="switch" id="enable_table_of_contents" name="enable_table_of_contents" value="1" @checked(old('enable_table_of_contents', $page->enable_table_of_contents))>
                  <label class="form-check-label fw-bold" for="enable_table_of_contents">Enable Table of Contents</label>
                </div>
              </div>

            </div>

          </div>
        </div>

        <!-- CARD 2: Advanced SEO, Canonical & Social Metadata -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-search me-2 text-primary"></i>2. Granular SEO, Indexing &amp; Social Meta
            </h5>
          </div>
          <div class="card-body p-lg-4">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Meta Title (SERP Title)</label>
                <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" placeholder="Google Search Title Tag (max ~60 chars)">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">Canonical URL Override</label>
                <input type="url" class="form-control" name="canonical_url" value="{{ old('canonical_url', $page->canonical_url) }}" placeholder="Defaults to self-canonical {{ url('guides/' . $page->slug) }}">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Meta Description</label>
                <textarea name="meta_description" rows="2" class="form-control" placeholder="Search snippet description (recommended 140-160 characters)">{{ old('meta_description', $page->meta_description) }}</textarea>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Robots Indexing</label>
                <select name="robots" class="form-select">
                  <option value="index, follow" @selected(old('robots', $page->robots) == 'index, follow')>index, follow (Standard)</option>
                  <option value="noindex, follow" @selected(old('robots', $page->robots) == 'noindex, follow')>noindex, follow</option>
                  <option value="noindex, nofollow" @selected(old('robots', $page->robots) == 'noindex, nofollow')>noindex, nofollow</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Schema.org Structured Data</label>
                <select name="schema_type" class="form-select">
                  <option value="Article" @selected(old('schema_type', $page->schema_type) == 'Article')>Article</option>
                  <option value="Guide" @selected(old('schema_type', $page->schema_type) == 'Guide')>Guide</option>
                  <option value="CollectionPage" @selected(old('schema_type', $page->schema_type) == 'CollectionPage')>CollectionPage</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Twitter Card Type</label>
                <select name="twitter_card" class="form-select">
                  <option value="summary_large_image">summary_large_image</option>
                  <option value="summary">summary</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">Open Graph Title (OG)</label>
                <input type="text" class="form-control" name="og_title" value="{{ old('og_title', $page->og_title) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">Open Graph Description (OG)</label>
                <input type="text" class="form-control" name="og_description" value="{{ old('og_description', $page->og_description) }}">
              </div>
            </div>

          </div>
        </div>

        <!-- Sticky Form Submit Bar -->
        <div class="card shadow-custom border-0 mb-4 bg-white p-3 d-flex flex-row justify-content-between align-items-center">
          <div class="text-muted small">
            <i class="bi bi-info-circle me-1 text-primary"></i> Save core metadata and SEO configuration before managing sections below.
          </div>
          <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-semibold shadow-sm">
            <i class="bi bi-save me-1"></i> Save Changes
          </button>
        </div>

      </form>

      <!-- CARD 3: Connect Prompts & Photoshoots (Live AJAX Search) -->
      <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images me-2 text-primary"></i>3. Select &amp; Connect Existing Prompts / Photoshoots
          </h5>
          <span class="badge bg-dark rounded-pill px-3 py-1" id="attachedItemsCountBadge">{{ $page->items->count() }} Attached</span>
        </div>
        <div class="card-body p-lg-4">

          <!-- Type Switcher & Search input -->
          <div class="row g-2 mb-3">
            <div class="col-md-3">
              <select id="searchItemType" class="form-select">
                <option value="prompt">Search Prompts</option>
                <option value="photoshoot">Search Photoshoots</option>
              </select>
            </div>
            <div class="col-md-9">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="itemSearchInput" class="form-control" placeholder="Search by title, prompt text, AI model (e.g. 'Jewellery', 'Midjourney')...">
              </div>
            </div>
          </div>

          <!-- Live Search Results Container -->
          <div id="itemSearchResultsBox" class="rounded-3 border p-3 bg-light d-none mb-4">
            <h6 class="fw-bold text-dark mb-2">Available Items (Click to Connect):</h6>
            <div id="itemSearchResultsList" class="row g-3">
              <!-- Dynamically populated via AJAX -->
            </div>
          </div>

          <!-- Attached Items List -->
          <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
            <i class="bi bi-check-all me-1"></i> Items Currently Connected to this Landing Page
          </h6>

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle" id="attachedItemsTable">
              <thead class="table-light">
                <tr>
                  <th style="width: 70px;">Preview</th>
                  <th>Item Title &amp; Details</th>
                  <th>Type</th>
                  <th>Page Badge / Note</th>
                  <th style="width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody id="attachedItemsBody">
                @forelse ($page->items as $item)
                  <tr id="attachedRow-{{ $item->id }}">
                    <td>
                      @if ($item->item_type == 'prompt' && $item->prompt)
                        <img src="{{ Storage::url(config('path.thumbnail') . $item->prompt->thumbnail) }}" class="rounded shadow-xs" width="48" height="48" style="object-fit: cover;">
                      @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                          <i class="bi-collection-play"></i>
                        </div>
                      @endif
                    </td>
                    <td>
                      @if ($item->item_type == 'prompt' && $item->prompt)
                        <div class="fw-bold text-dark">{{ $item->prompt->title }}</div>
                        <small class="text-muted font-monospace d-block text-truncate" style="max-width: 450px;">{{ $item->prompt->prompt }}</small>
                      @elseif ($item->item_type == 'photoshoot' && $item->photoshoot)
                        <div class="fw-bold text-dark">{{ $item->photoshoot->title }}</div>
                        <small class="text-muted">{{ $item->photoshoot->prompts_count }} Prompts</small>
                      @else
                        <span class="text-danger small">Master record removed</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge {{ $item->item_type == 'prompt' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }} border">
                        {{ ucfirst($item->item_type) }}
                      </span>
                    </td>
                    <td>
                      @if ($item->custom_badge)
                        <span class="badge bg-dark text-white me-1">{{ $item->custom_badge }}</span>
                      @endif
                      @if ($item->custom_notes)
                        <small class="text-muted d-block">{{ $item->custom_notes }}</small>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-outline-danger detach-item-btn" data-item-id="{{ $item->id }}">
                        <i class="bi-x-lg"></i> Detach
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr id="noAttachedItemsRow">
                    <td colspan="5" class="text-center py-4 text-muted">
                      No prompts or photoshoots connected yet. Search above to add items to this guide.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>

      <!-- CARD 4: Content Sections Builder -->
      <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi-layout-text-window-reverse me-2 text-primary"></i>4. Dynamic Content Sections Builder
          </h5>
          <button type="button" class="btn btn-sm btn-dark" id="openAddSectionBtn" data-bs-toggle="modal" data-bs-target="#addSectionModal">
            <i class="bi-plus-lg me-1"></i> Add Content Section
          </button>
        </div>
        <div class="card-body p-lg-4">

          <div id="sectionsListContainer" class="d-flex flex-column gap-3">
            @forelse ($page->sections as $sec)
              <div class="card border rounded-3 p-3 bg-white shadow-xs section-card-item" id="sectionCard-{{ $sec->id }}" data-section-id="{{ $sec->id }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark text-white rounded-pill px-3 py-1">{{ strtoupper(str_replace('_', ' ', $sec->type)) }}</span>
                    <h6 class="fw-bold mb-0 text-dark">{{ $sec->heading ?: 'Untitled Section' }}</h6>
                  </div>
                  <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-dark edit-section-btn"
                      data-section-id="{{ $sec->id }}"
                      data-type="{{ $sec->type }}"
                      data-badge="{{ e($sec->badge) }}"
                      data-heading="{{ e($sec->heading) }}"
                      data-subheading="{{ e($sec->subheading) }}"
                      data-content="{{ base64_encode($sec->content) }}"
                      title="Edit Section">
                      <i class="bi-pencil-square me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-section-btn" data-section-id="{{ $sec->id }}" title="Delete Section">
                      <i class="bi-trash"></i>
                    </button>
                  </div>
                </div>
                @if ($sec->subheading)
                  <p class="text-muted small mb-2">{{ $sec->subheading }}</p>
                @endif
                @if ($sec->content)
                  <div class="small text-muted text-truncate" style="max-height: 50px;">
                    {!! strip_tags($sec->content) !!}
                  </div>
                @endif
              </div>
            @empty
              <div id="noSectionsPlaceholder" class="text-center py-4 text-muted border rounded-3">
                <i class="bi-grid display-6 d-block mb-2 text-secondary"></i>
                No custom sections added yet. Click <strong>"Add Content Section"</strong> to add Guides, Prompts Grid, FAQs, or Camera Specs.
              </div>
            @endforelse
          </div>

        </div>
      </div>

		</div>

	</div>
</div>

<!-- Modal: Add / Edit Content Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="addSectionModalTitle"><i class="bi-plus-circle text-primary me-2"></i>Add Content Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newSectionForm">
          <input type="hidden" name="section_id" id="sectionIdInput" value="">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Section Type</label>
              <select name="type" id="sectionTypeSelect" class="form-select" required>
                <option value="rich_text">Rich Text / Instructional Guide</option>
                <option value="prompt_grid">Prompt Showcase Grid (Displays Selected Prompts)</option>
                <option value="camera_guide">Camera Angles, Lenses &amp; Studio Lighting Specs</option>
                <option value="product_adaptability">Universal Product Adaptability Spec</option>
                <option value="faq_accordion">FAQ Accordion (Q&amp;A Rich Snippets)</option>
                <option value="cta_banner">Call-to-Action Banner</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Section Badge (e.g. "Pro Tip", "Studio Spec")</label>
              <input type="text" name="badge" id="sectionBadgeInput" class="form-control" placeholder="Optional pill tag">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Section Heading (H2)</label>
              <input type="text" name="heading" id="sectionHeadingInput" required class="form-control" placeholder="e.g. How to Setup Lighting for Gold Jewellery in Midjourney">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Subheading / Context</label>
              <input type="text" name="subheading" id="sectionSubheadingInput" class="form-control" placeholder="Optional subtitle context">
            </div>
            <div class="col-12" id="sectionContentWrapper">
              <label class="form-label fw-bold">Content Narrative (HTML / Text)</label>
              <textarea name="content" id="sectionContentInput" rows="5" class="form-control" placeholder="Write comprehensive guide instructions, tips, lighting tokens..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-dark" id="submitSectionBtn">
          <i class="bi-check2 me-1"></i> Save Section
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Update Guide via AI JSON -->
<div class="modal fade" id="aiJsonUpdateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark">
          <i class="bi bi-robot text-primary me-2"></i>Update Guide From AI Writer JSON
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 py-2 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> Paste the updated AI Writer JSON. This will update the title, meta tags, and modular sections for this guide and automatically switch the status to <strong>Draft Mode</strong> for review.
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="updateReplaceSectionsCheck" checked>
          <label class="form-check-label small fw-bold" for="updateReplaceSectionsCheck">
            Replace existing content sections with new sections from JSON
          </label>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">AI Output JSON</label>
          <textarea id="aiJsonUpdateInput" rows="12" class="form-control font-monospace small" placeholder='{
  "page": {
    "title": "{{ addslashes($page->title) }}",
    "primary_keyword": "{{ addslashes($page->primary_keyword) }}",
    ...
  },
  "sections": [
    ...
  ]
}'></textarea>
        </div>

        <div id="jsonUpdateAlert" class="alert alert-danger d-none py-2 small"></div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSubmitUpdateAiJson">
          <i class="bi bi-magic me-1"></i> Update Guide in Draft Mode
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
  const pageId = {{ $page->id }};
  const csrfToken = '{{ csrf_token() }}';

  // 1. AJAX Item Search
  let searchTimeout = null;
  const searchInput = document.getElementById('itemSearchInput');
  const searchType = document.getElementById('searchItemType');
  const resultsBox = document.getElementById('itemSearchResultsBox');
  const resultsList = document.getElementById('itemSearchResultsList');

  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    if (query.length < 2) {
      resultsBox.classList.add('d-none');
      return;
    }

    searchTimeout = setTimeout(function() {
      const endpoint = searchType.value === 'prompt' 
        ? '{{ url("panel/admin/seo-pages/search/prompts") }}'
        : '{{ url("panel/admin/seo-pages/search/photoshoots") }}';

      fetch(`${endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          resultsList.innerHTML = '';
          if (data.data && data.data.length > 0) {
            resultsBox.classList.remove('d-none');
            data.data.forEach(item => {
              const col = document.createElement('div');
              col.className = 'col-md-6';
              col.innerHTML = `
                <div class="card p-2 border shadow-xs d-flex flex-row align-items-center gap-2">
                  <img src="${item.thumbnail}" class="rounded" width="48" height="48" style="object-fit:cover;">
                  <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-truncate small">${item.title}</div>
                    <span class="badge bg-secondary-subtle text-secondary small">${item.ai_model || 'AI'}</span>
                  </div>
                  <button type="button" class="btn btn-sm btn-dark connect-item-btn" data-id="${item.id}" data-type="${searchType.value}" data-title="${item.title}">
                    <i class="bi-plus"></i> Connect
                  </button>
                </div>
              `;
              resultsList.appendChild(col);
            });
          } else {
            resultsList.innerHTML = '<div class="col-12 text-muted small p-2">No matching items found.</div>';
            resultsBox.classList.remove('d-none');
          }
        });
    }, 300);
  });

  // Connect Item Click
  document.addEventListener('click', function(e) {
    if (e.target.closest('.connect-item-btn')) {
      const btn = e.target.closest('.connect-item-btn');
      const itemId = btn.dataset.id;
      const itemType = btn.dataset.type;

      fetch(`{{ url("panel/admin/seo-pages") }}/${pageId}/items/attach`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          item_type: itemType,
          item_id: itemId
        })
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          window.location.reload();
        }
      });
    }

    // Detach Item Click
    if (e.target.closest('.detach-item-btn')) {
      if (!confirm('Remove this item from the landing page? (Master item remains safe).')) return;
      const btn = e.target.closest('.detach-item-btn');
      const itemId = btn.dataset.itemId;

      fetch(`{{ url("panel/admin/seo-pages") }}/${pageId}/items/detach/${itemId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken }
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          document.getElementById(`attachedRow-${itemId}`).remove();
        }
      });
    }

    // Edit Section Click
    if (e.target.closest('.edit-section-btn')) {
      const btn = e.target.closest('.edit-section-btn');
      const sectionId = btn.dataset.sectionId;
      const type = btn.dataset.type || 'rich_text';
      const badge = btn.dataset.badge || '';
      const heading = btn.dataset.heading || '';
      const subheading = btn.dataset.subheading || '';
      const rawBase64 = btn.dataset.content || '';
      let content = '';
      if (rawBase64) {
        try {
          content = decodeURIComponent(escape(window.atob(rawBase64)));
        } catch (err) {
          try {
            content = window.atob(rawBase64);
          } catch (err2) {
            content = rawBase64;
          }
        }
      }

      // Populate Form
      document.getElementById('sectionIdInput').value = sectionId;
      document.getElementById('sectionTypeSelect').value = type;
      document.getElementById('sectionBadgeInput').value = badge;
      document.getElementById('sectionHeadingInput').value = heading;
      document.getElementById('sectionSubheadingInput').value = subheading;
      document.getElementById('sectionContentInput').value = content;

      // Update Modal Header & Button
      document.getElementById('addSectionModalTitle').innerHTML = '<i class="bi-pencil-square text-primary me-2"></i>Edit Content Section';
      document.getElementById('submitSectionBtn').innerHTML = '<i class="bi-check2 me-1"></i> Update Section';

      const modalEl = document.getElementById('addSectionModal');
      if (window.bootstrap && bootstrap.Modal) {
        const sectionModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        sectionModal.show();
      } else if (window.$ && $.fn.modal) {
        $(modalEl).modal('show');
      }
    }

    // Delete Section Click
    if (e.target.closest('.delete-section-btn')) {
      if (!confirm('Delete this content section?')) return;
      const btn = e.target.closest('.delete-section-btn');
      const sectionId = btn.dataset.sectionId;

      fetch(`{{ url("panel/admin/seo-pages") }}/${pageId}/section/delete/${sectionId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken }
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          document.getElementById(`sectionCard-${sectionId}`).remove();
        }
      });
    }
  });

  // Reset modal when clicking "Add Content Section"
  const openAddBtn = document.getElementById('openAddSectionBtn');
  if (openAddBtn) {
    openAddBtn.addEventListener('click', function() {
      document.getElementById('sectionIdInput').value = '';
      document.getElementById('newSectionForm').reset();
      document.getElementById('addSectionModalTitle').innerHTML = '<i class="bi-plus-circle text-primary me-2"></i>Add Content Section';
      document.getElementById('submitSectionBtn').innerHTML = '<i class="bi-check2 me-1"></i> Save Section';
    });
  }

  // Section Save Submission
  document.getElementById('submitSectionBtn').addEventListener('click', function() {
    const form = document.getElementById('newSectionForm');
    if (!form.reportValidity()) {
      return;
    }
    const formData = new FormData(form);
    const saveBtn = this;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch(`{{ url("panel/admin/seo-pages") }}/${pageId}/section/save`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      if (res.success) {
        window.location.reload();
      } else {
        alert(res.message || 'Error saving section.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi-check2 me-1"></i> Save Section';
      }
    })
    .catch(err => {
      alert('An unexpected error occurred. Please try again.');
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="bi-check2 me-1"></i> Save Section';
    });
  });

  // Handle AI JSON Update
  const btnSubmitUpdateAiJson = document.getElementById('btnSubmitUpdateAiJson');
  if (btnSubmitUpdateAiJson) {
    btnSubmitUpdateAiJson.addEventListener('click', function() {
      const rawJson = document.getElementById('aiJsonUpdateInput').value.trim();
      const alertBox = document.getElementById('jsonUpdateAlert');
      const replaceSections = document.getElementById('updateReplaceSectionsCheck').checked;
      const submitBtn = this;

      alertBox.classList.add('d-none');

      if (!rawJson) {
        alertBox.innerText = 'Please paste the JSON payload first.';
        alertBox.classList.remove('d-none');
        return;
      }

      // Pre-validate on browser side to catch trailing commas, unescaped quotes, or syntax anomalies early
      let parsedJsonObj = null;
      try {
        parsedJsonObj = JSON.parse(rawJson);
      } catch (clientErr) {
        // If direct parse fails, try checking if markdown code fences or smart quotes are present
        let cleaned = rawJson
          .replace(/^```json\s*/i, '')
          .replace(/^```\s*/i, '')
          .replace(/\s*```$/i, '')
          .trim();

        // Also fix typographic/smart curly quotes if pasted from rich text apps
        cleaned = cleaned
          .replace(/[\u201C\u201D]/g, '"')
          .replace(/[\u2018\u2019]/g, "'");

        try {
          parsedJsonObj = JSON.parse(cleaned);
        } catch (secondErr) {
          alertBox.innerHTML = '<strong>Invalid JSON Syntax:</strong> ' + clientErr.message + 
            '<br><small class="text-white-50">Tip: Check for unescaped double quotes (\") inside your HTML/text, missing commas between items, or trailing commas.</small>';
          alertBox.classList.remove('d-none');
          return;
        }
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

      fetch('{{ url("panel/admin/seo-pages/import-json") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          json_content: rawJson,
          page_id: pageId,
          replace_sections: replaceSections
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alertBox.innerText = data.message || 'Error updating JSON.';
          alertBox.classList.remove('d-none');
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi-magic me-1"></i> Update Guide in Draft Mode';
        }
      })
      .catch(err => {
        alertBox.innerText = 'An unexpected server error occurred. Please verify your JSON format.';
        alertBox.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi-magic me-1"></i> Update Guide in Draft Mode';
      });
    });
  }
</script>
@endsection
