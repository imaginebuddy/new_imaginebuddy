@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/seo-pages') }}">SEO Landing Pages</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.add_new') }} Landing Page</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0 mb-4">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/seo-pages/create') }}" enctype="multipart/form-data">
             @csrf

             <!-- Step Header with AI JSON Import Button -->
             <div class="border-bottom pb-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
               <div>
                 <h5 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Create New SEO Landing Page</h5>
                 <p class="text-muted small mb-0">Fill in core page details manually or import full content instantly from the AI Writer JSON.</p>
               </div>
               <div>
                 <button type="button" class="btn btn-outline-primary fw-bold shadow-xs d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#aiJsonImportModal">
                   <i class="bi bi-robot fs-5"></i>
                   <span>Paste AI JSON (Draft Mode)</span>
                 </button>
               </div>
             </div>

             <!-- Main Info -->
             <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Public Page Title <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('title') }}" id="postTitle" name="title" required type="text" class="form-control form-control-lg" placeholder="e.g. Best Jewellery Product Photography Prompts for Navaratri Season">
                <small class="text-muted d-block mt-1">This will be the main H1 heading and default SEO title.</small>
		          </div>
		        </div>

             <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Internal Reference Name</label>
		          <div class="col-sm-10">
		            <input value="{{ old('internal_name') }}" name="internal_name" type="text" class="form-control" placeholder="Optional. Internal label for admin dashboard tracking">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">URL Slug</label>
		          <div class="col-sm-10">
		            <input value="{{ old('slug') }}" id="postSlug" name="slug" type="text" class="form-control" placeholder="Leave blank to auto-generate from title">
                <small class="text-muted d-block mt-1">Public URL will be: <code>{{ url('guides') }}/<span id="slugPreview">jewellery-photography-prompts</span></code></small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Category</label>
		          <div class="col-sm-10">
		            <select name="category_id" class="form-select">
                  <option value="">Select Category (Optional)</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Author / Owner <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <select name="user_id" required class="form-select">
                  @foreach ($authors as $author)
                    <option value="{{ $author->id }}" {{ old('user_id', auth()->id()) == $author->id ? 'selected' : '' }}>
                      {{ $author->name ?: $author->username }} (@<span>{{ $author->username }}</span>)
                    </option>
                  @endforeach
                </select>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Excerpt / Summary</label>
		          <div class="col-sm-10">
		            <textarea name="excerpt" rows="3" class="form-control" placeholder="Short introductory summary for search snippet cards and social sharing">{{ old('excerpt') }}</textarea>
		          </div>
		        </div>

            <!-- Keyword & Intent -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Target Keyword</label>
		          <div class="col-sm-6">
		            <input value="{{ old('primary_keyword') }}" name="primary_keyword" type="text" class="form-control" placeholder="e.g. jewellery photography prompts navaratri">
                <small class="text-muted d-block mt-1">Primary organic search phrase being targeted.</small>
		          </div>
              <div class="col-sm-4">
                <select name="search_intent" class="form-select">
                  <option value="commercial" {{ old('search_intent', 'commercial') == 'commercial' ? 'selected' : '' }}>Commercial Intent</option>
                  <option value="informational" {{ old('search_intent') == 'informational' ? 'selected' : '' }}>Informational / Guide</option>
                  <option value="transactional" {{ old('search_intent') == 'transactional' ? 'selected' : '' }}>Transactional / Prompt Sale</option>
                </select>
                <small class="text-muted d-block mt-1">Search Intent classification</small>
              </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Secondary Keywords</label>
		          <div class="col-sm-10">
		            <input value="{{ old('secondary_keywords') }}" name="secondary_keywords" type="text" class="form-control" placeholder="e.g. gold necklace prompt, festive jewellery photoshoot, indian festival ai photos">
                <small class="text-muted d-block mt-1">Comma-separated related queries and long-tail variants.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Target Audience</label>
		          <div class="col-sm-10">
		            <input value="{{ old('target_audience') }}" name="target_audience" type="text" class="form-control" placeholder="e.g. Jewellery Brands, E-commerce Store Owners, AI Artists">
		          </div>
		        </div>

            <!-- Featured Image -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Hero Featured Image</label>
		          <div class="col-sm-10">
		            <input type="file" id="featuredImageInput" name="featured_image" accept="image/jpeg,image/png,image/webp,image/jpg" class="form-control custom-file">
                <small class="text-muted d-block mt-1">Recommended for Google Discover: 1200x675 or larger (16:9). JPG, PNG, or WebP up to 4MB.</small>
                <div id="featuredPreviewWrapper" class="mt-2 d-none align-items-center gap-2">
                  <img id="featuredPreviewImg" src="" alt="Preview" class="rounded border shadow-xs" style="max-height: 120px; object-fit: cover;">
                </div>
		          </div>
		        </div>

            <div class="row mb-4">
		          <label class="col-sm-2 col-form-label text-lg-end">Image Alt Text</label>
		          <div class="col-sm-10">
		            <input value="{{ old('featured_image_alt') }}" name="featured_image_alt" type="text" class="form-control" placeholder="Descriptive alt text for accessibility &amp; Google Images">
		          </div>
		        </div>

            <!-- Publishing Configuration -->
            <div class="row mb-4">
		          <label class="col-sm-2 col-form-label text-lg-end">Publishing State <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
                <div class="d-flex align-items-center gap-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusDraft" value="draft" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="statusDraft">Draft</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusPublished" value="published" {{ old('status') == 'published' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-success" for="statusPublished">Publish Immediately</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusScheduled" value="scheduled" {{ old('status') == 'scheduled' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-primary" for="statusScheduled">Schedule Publication</label>
                  </div>
                </div>
		          </div>
		        </div>

            <!-- Submit -->
						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold">
                  <i class="bi-arrow-right-circle me-1"></i> Save &amp; Continue to Section Builder
                </button>
		          </div>
		        </div>

          </form>

        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal: Paste AI JSON (Draft Mode) -->
<div class="modal fade" id="aiJsonImportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark">
          <i class="bi bi-robot text-primary me-2"></i>Create Guide From AI Writer JSON
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 py-2 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> Paste the structured JSON generated by your AI Writer. The page and all modular content sections will be created automatically in <strong>Draft Mode</strong> so you can review and refine them.
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Select Category (Optional)</label>
          <select id="modalJsonCategory" class="form-select">
            <option value="">Auto-detect or None</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">AI Output JSON</label>
          <textarea id="aiJsonInput" rows="12" class="form-control font-monospace small" placeholder='{
  "page": {
    "title": "Best Jewellery Product Photography Prompts...",
    "primary_keyword": "jewellery photography prompts",
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
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSubmitAiJson">
          <i class="bi bi-magic me-1"></i> Create Guide in Draft Mode
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
  const csrfToken = '{{ csrf_token() }}';

  document.getElementById('postTitle').addEventListener('input', function() {
    var slugInput = document.getElementById('postSlug');
    if (!slugInput.value || slugInput.dataset.manual !== 'true') {
      var slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
      document.getElementById('slugPreview').innerText = slug || 'jewellery-photography-prompts';
    }
  });

  document.getElementById('postSlug').addEventListener('input', function() {
    this.dataset.manual = 'true';
    document.getElementById('slugPreview').innerText = this.value || 'jewellery-photography-prompts';
  });

  const featuredInput = document.getElementById('featuredImageInput');
  if (featuredInput) {
    featuredInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      const previewWrapper = document.getElementById('featuredPreviewWrapper');
      const previewImg = document.getElementById('featuredPreviewImg');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
          previewImg.src = evt.target.result;
          previewWrapper.classList.remove('d-none');
          previewWrapper.classList.add('d-flex');
        }
        reader.readAsDataURL(file);
      } else if (previewWrapper) {
        previewWrapper.classList.remove('d-flex');
        previewWrapper.classList.add('d-none');
      }
    });
  }

  // Handle AI JSON Import
  document.getElementById('btnSubmitAiJson').addEventListener('click', function() {
    const rawJson = document.getElementById('aiJsonInput').value.trim();
    const alertBox = document.getElementById('jsonImportAlert');
    const submitBtn = this;

    alertBox.classList.add('d-none');

    if (!rawJson) {
      alertBox.innerText = 'Please paste the JSON payload first.';
      alertBox.classList.remove('d-none');
      return;
    }

    // Pre-validate on browser side to catch syntax errors early
    let parsedJsonObj = null;
    try {
      parsedJsonObj = JSON.parse(rawJson);
    } catch (clientErr) {
      let cleaned = rawJson
        .replace(/^```json\s*/i, '')
        .replace(/^```\s*/i, '')
        .replace(/\s*```$/i, '')
        .trim();

      // Fix typographic/smart quotes
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
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Importing...';

    fetch('{{ url("panel/admin/seo-pages/import-json") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        json_content: rawJson,
        category_id: document.getElementById('modalJsonCategory').value || null
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
        submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Guide in Draft Mode';
      }
    })
    .catch(err => {
      alertBox.innerText = 'An unexpected server error occurred. Please verify your JSON format.';
      alertBox.classList.remove('d-none');
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="bi bi-magic me-1"></i> Create Guide in Draft Mode';
    });
  });
</script>
@endsection
