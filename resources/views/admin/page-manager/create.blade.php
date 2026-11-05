@extends('admin.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark fw-bold">
            <i class="bi bi-file-earmark-plus me-2 text-primary"></i> Create New Page
          </h1>
          <p class="text-muted small mb-0">Publish a new vertical solution page, listing hub, or guide.</p>
        </div>
        <div class="col-sm-6 text-end">
          <button type="button" class="btn btn-primary rounded-pill px-4 me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#aiJsonImportModal">
            <i class="bi bi-robot me-1"></i> Paste AI Writer JSON
          </button>
          <a href="{{ url('panel/admin/page-manager') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to All Pages
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Form -->
  <section class="content">
    <div class="container-fluid">

      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form action="{{ url('panel/admin/page-manager/create') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
          <!-- Left Column (Core Details & Content) -->
          <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm mb-4">
              <div class="card-header bg-light">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-info-circle me-1"></i> Core Page Attributes</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Target Vertical / Silo <span class="text-danger">*</span></label>
                    <select name="route_prefix" id="route_prefix" class="form-select" required>
                      <option value="use-case" {{ old('route_prefix') === 'use-case' ? 'selected' : '' }}>Use Case (/use-case)</option>
                      <option value="industry" {{ old('route_prefix') === 'industry' ? 'selected' : '' }}>Industry (/industry)</option>
                      <option value="product" {{ old('route_prefix') === 'product' ? 'selected' : '' }}>Product Content (/product)</option>
                      <option value="garment" {{ old('route_prefix') === 'garment' ? 'selected' : '' }}>Garment & Apparel (/garment)</option>
                      <option value="marketplace" {{ old('route_prefix') === 'marketplace' ? 'selected' : '' }}>Marketplace (/marketplace)</option>
                      <option value="platform-ai-ads" {{ old('route_prefix') === 'platform-ai-ads' ? 'selected' : '' }}>Platform AI Ads (/platform-ai-ads)</option>
                    </select>
                    <div class="form-text">Determines the public URL hierarchy prefix.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Page Type <span class="text-danger">*</span></label>
                    <select name="page_type" class="form-select" required>
                      <option value="solution" {{ old('page_type', 'solution') === 'solution' ? 'selected' : '' }}>Solution Detail (High-Converting Landing)</option>
                      <option value="hub" {{ old('page_type') === 'hub' ? 'selected' : '' }}>Hub Directory (Listing Index)</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="page_title" class="form-control form-control-lg" placeholder="e.g. AI Saree Product Photography" value="{{ old('title') }}" required>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-bold">URL Slug</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted" id="url_prefix_label">/use-case/</span>
                      <input type="text" name="slug" id="page_slug" class="form-control" placeholder="saree-product-photography" value="{{ old('slug') }}">
                    </div>
                    <div class="form-text">Leave blank to auto-generate from the title.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Eyebrow Badge</label>
                    <input type="text" name="header_badge" class="form-control" placeholder="e.g. APPAREL & DRAPE FIDELITY" value="{{ old('header_badge') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Card Icon / Emoji</label>
                    <input type="text" name="icon" class="form-control" placeholder="e.g. 👗 or bi-camera-fill" value="{{ old('icon') }}">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-bold">Short Excerpt / Value Proposition</label>
                    <textarea name="excerpt" rows="3" class="form-control" placeholder="Brief summary of the solution's core business outcome...">{{ old('excerpt') }}</textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- SEO & Meta Card -->
            <div class="card card-outline card-info shadow-sm mb-4">
              <div class="card-header bg-light">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-google me-1"></i> SEO & Structured Data</h5>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label fw-bold">Primary Focus Keyword</label>
                    <input type="text" name="primary_keyword" class="form-control" placeholder="e.g. saree product photography" value="{{ old('primary_keyword') }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="Leave blank to use Page Title - ImagineBuddy" value="{{ old('meta_title') }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control" placeholder="Search engine description (150-160 characters)...">{{ old('meta_description') }}</textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Schema.org Type</label>
                    <select name="schema_type" class="form-select">
                      <option value="Service" {{ old('schema_type') === 'Service' ? 'selected' : '' }}>Service</option>
                      <option value="Product" {{ old('schema_type') === 'Product' ? 'selected' : '' }}>Product</option>
                      <option value="WebPage" {{ old('schema_type') === 'WebPage' ? 'selected' : '' }}>WebPage</option>
                      <option value="HowTo" {{ old('schema_type') === 'HowTo' ? 'selected' : '' }}>HowTo</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold">Robots Directive</label>
                    <select name="robots" class="form-select">
                      <option value="index, follow" selected>index, follow</option>
                      <option value="noindex, follow">noindex, follow</option>
                      <option value="noindex, nofollow">noindex, nofollow</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column (Publishing & Media) -->
          <div class="col-lg-4">
            <div class="card card-outline card-success shadow-sm mb-4">
              <div class="card-header bg-light">
                <h5 class="card-title fw-bold m-0"><i class="bi bi-send me-1"></i> Publishing Settings</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label fw-bold">Publishing Status <span class="text-danger">*</span></label>
                  <select name="status" class="form-select" required>
                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Publish Date</label>
                  <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Author <span class="text-danger">*</span></label>
                  <select name="user_id" class="form-select" required>
                    @foreach ($authors as $author)
                      <option value="{{ $author->id }}" {{ old('user_id', auth()->id()) == $author->id ? 'selected' : '' }}>
                        {{ $author->name ?: $author->username }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Search Intent</label>
                  <select name="search_intent" class="form-select">
                    <option value="commercial" selected>Commercial (Buyer looking for tools)</option>
                    <option value="transactional">Transactional (Ready to convert)</option>
                    <option value="informational">Informational (Educational/Guide)</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Before / After Transformation Media Card -->
            <div class="card card-outline card-secondary shadow-sm mb-4">
              <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h6 class="fw-bold m-0"><i class="bi bi-arrow-left-right text-primary me-1"></i> Before / After Hero Slider</h6>
                <span class="badge bg-mint text-dark">Interactive</span>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold m-0 text-dark"><i class="bi bi-camera me-1"></i> Before Image (Raw)</label>
                    <input type="text" name="before_label" class="form-control form-control-sm w-50" placeholder="Raw Snapshot" value="{{ old('before_label', 'Raw Snapshot') }}" title="Badge Label">
                  </div>
                  <input type="file" name="before_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg">
                  <div class="form-text" style="font-size: 0.72rem;">Flat-lay, smartphone snapshot, or unedited packshot.</div>
                </div>

                <div class="mb-2">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold m-0 text-dark"><i class="bi bi-stars text-mint me-1"></i> After Image (Hero)</label>
                    <input type="text" name="after_label" class="form-control form-control-sm w-50" placeholder="Campaign Ready" value="{{ old('after_label', 'Campaign Ready') }}" title="Badge Label">
                  </div>
                  <input type="file" name="after_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg">
                  <div class="form-text" style="font-size: 0.72rem;">AI-generated commercial lifestyle or on-model visual.</div>
                </div>
              </div>
            </div>

            <!-- Submit Action -->
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success btn-lg rounded-pill shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Create Page & Launch Builder
              </button>
            </div>
          </div>
        </div>
      </form>

    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const prefixSelect = document.getElementById('route_prefix');
  const prefixLabel = document.getElementById('url_prefix_label');
  const titleInput = document.getElementById('page_title');
  const slugInput = document.getElementById('page_slug');

  function updatePrefixLabel() {
    const val = prefixSelect.value;
    prefixLabel.textContent = val ? '/' + val + '/' : '/guides/';
  }

  prefixSelect.addEventListener('change', updatePrefixLabel);
  updatePrefixLabel();

  // Auto-slugify on title input if slug is empty
  titleInput.addEventListener('input', function() {
    if (!slugInput.dataset.manuallyEdited) {
      slugInput.value = titleInput.value
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    }
  });

  slugInput.addEventListener('input', function() {
    slugInput.dataset.manuallyEdited = "true";
  });

  // AI Writer JSON Import Handler
  const submitBtn = document.getElementById('btnSubmitAiJson');
  if (submitBtn) {
    submitBtn.addEventListener('click', function() {
      const rawJson = document.getElementById('aiJsonInput').value.trim();
      const alertBox = document.getElementById('jsonImportAlert');

      alertBox.classList.add('d-none');

      if (!rawJson) {
        alertBox.innerText = 'Please paste the JSON payload first.';
        alertBox.classList.remove('d-none');
        return;
      }

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating Page...';

      fetch('{{ url("panel/admin/seo-pages/import-json") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          json_content: rawJson,
          redirect_to_page_manager: true
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.redirect_url) {
          window.location.href = data.redirect_url;
        } else {
          alertBox.innerText = data.message || 'Error importing JSON.';
          alertBox.classList.remove('d-none');
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Page in Draft Mode';
        }
      })
      .catch(err => {
        alertBox.innerText = 'An unexpected server error occurred. Please verify your JSON format.';
        alertBox.classList.remove('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Page in Draft Mode';
      });
    });
  }
});
</script>

<!-- Modal: Paste AI JSON (Draft Mode) -->
<div class="modal fade" id="aiJsonImportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark">
          <i class="bi bi-robot text-primary me-2"></i>Create Page From AI Writer JSON
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 py-2 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> Paste the structured JSON generated by your AI Writer. The page and all modular content sections will be created automatically in <strong>Draft Mode</strong> so you can review and refine them.
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">AI Output JSON</label>
          <textarea id="aiJsonInput" rows="14" class="form-control font-monospace small" placeholder='{
  "page": {
    "route_prefix": "use-case",
    "title": "AI Product Photography for D2C Brands",
    "primary_keyword": "AI product photography for D2C brands",
    ...
  },
  "sections": [
    ...
  ]
}'></textarea>
        </div>

        <div id="jsonImportAlert" class="alert alert-danger d-none py-2 small"></div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill" id="btnSubmitAiJson">
          <i class="bi bi-magic me-1"></i> Create Page in Draft Mode
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
