@extends('admin.layout')

@section('css')
<style>
input[type="file"].custom-file,
.custom-file {
  opacity: 1 !important;
  position: static !important;
  visibility: visible !important;
  display: block !important;
  width: 100% !important;
  min-height: calc(1.5em + 0.75rem + 2px) !important;
  cursor: pointer !important;
  font-size: 0.9rem !important;
  background-color: #ffffff !important;
}
.file-preview-thumbnail {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #dee2e6;
}
</style>
@endsection

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ route('admin.prompts.index') }}">Prompt Collections</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Edit: {{ $collection->title }}</span>

    <a href="{{ url('prompts/' . $collection->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0">
      <i class="bi bi-box-arrow-up-right me-1"></i> View Live Page
    </a>
  </h5>

<div class="content">
	<div class="row">
		<div class="col-lg-12">

      <div id="ajaxAlertContainer"></div>

			@if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check2 me-1"></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

			@if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

			<form method="POST" action="{{ route('admin.prompts.update', $collection->id) }}" enctype="multipart/form-data" id="formEditCollection">
        @csrf

        <!-- CARD 1: Core Collection Details -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-folder-check me-2 text-primary"></i>1. Collection Overview
            </h5>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="title" class="form-label fw-bold">Collection Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $collection->title) }}" required>
              </div>

              <div class="col-md-4">
                <label for="category_id" class="form-label fw-bold">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">Select Category</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $collection->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-8">
                <label for="slug" class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted">{{ url('prompts') }}/</span>
                  <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $collection->slug) }}" required>
                </div>
              </div>

              <div class="col-md-4">
                <label for="ai_engine" class="form-label fw-bold">AI Engine Compatibility</label>
                <input type="text" name="ai_engine" id="ai_engine" class="form-control" value="{{ old('ai_engine', $collection->ai_engine) }}">
              </div>

              <div class="col-12">
                <label for="summary" class="form-label fw-bold">Summary / Value Proposition</label>
                <textarea name="summary" id="summary" rows="3" class="form-control">{{ old('summary', $collection->summary) }}</textarea>
              </div>

              <div class="col-md-6">
                <label for="cover_image" class="form-label fw-bold">Cover Banner Image</label>
                <small class="text-muted d-block mb-1">Displays as the ambient hero background banner on this collection's page.</small>
                @if ($collection->cover_image)
                  <div class="mb-2 d-flex align-items-center gap-3">
                    <img src="{{ $collection->cover_image_url }}" alt="Cover" class="rounded border shadow-xs" style="width: 80px; height: 50px; object-fit: cover;">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1">
                      <label class="form-check-label text-danger small fw-semibold" for="remove_cover_image">
                        <i class="bi bi-trash"></i> Remove Cover Banner
                      </label>
                    </div>
                  </div>
                @endif
                <input type="file" name="cover_image" id="cover_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                <div id="cover_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                  <img id="cover_image_preview_img" src="" alt="New Cover Preview" class="file-preview-thumbnail shadow-xs" style="width: 80px; height: 50px;">
                  <div>
                    <span class="badge bg-primary mb-1">New Cover Selected</span>
                    <small class="text-muted d-block" id="cover_image_preview_name"></small>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <label for="status" class="form-label fw-bold">Publication Status</label>
                <select name="status" id="status" class="form-select">
                  <option value="active" {{ old('status', $collection->status) == 'active' ? 'selected' : '' }}>Active (Public)</option>
                  <option value="draft" {{ old('status', $collection->status) == 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                </select>
              </div>

              <div class="col-md-3 d-flex align-items-center mt-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $collection->is_featured) ? 'checked' : '' }}>
                  <label class="form-check-label fw-bold" for="is_featured">Featured on Hub</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD 2: The Shortcut & Before/After Studio Showcase -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-arrow-left-right me-2 text-success"></i>2. The Shortcut &amp; Before/After Studio Section
            </h5>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="show_shortcut_section" id="show_shortcut_section" value="1" {{ old('show_shortcut_section', $collection->show_shortcut_section ?? true) ? 'checked' : '' }}>
              <label class="form-check-label fw-bold" for="show_shortcut_section">Enable Section</label>
            </div>
          </div>
          <div class="card-body p-lg-4">
            <p class="text-muted small mb-4">
              Configures the high-converting conversion card displayed below the prompt cards ("You don’t need to learn prompting..."). Upload a raw product photo (Before) and its AI studio render (After) to showcase the interactive transformation slider.
            </p>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold">Eyebrow Tag</label>
                <input type="text" name="shortcut_eyebrow" class="form-control" value="{{ old('shortcut_eyebrow', $collection->shortcut_eyebrow ?? 'THE SHORTCUT') }}" placeholder="THE SHORTCUT">
              </div>

              <div class="col-md-8">
                <label class="form-label fw-bold">Main Heading</label>
                <input type="text" name="shortcut_title" class="form-control" value="{{ old('shortcut_title', $collection->shortcut_title ?? 'You don’t need to learn prompting to get the images you want') }}" placeholder="You don’t need to learn prompting to get the images you want">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Description / Value Proposition</label>
                <textarea name="shortcut_description" rows="3" class="form-control" placeholder="Explain the friction of manual prompting vs the ease of automated studio generation...">{{ old('shortcut_description', $collection->shortcut_description ?? 'Every prompt above invents a new image that isn\'t yours, and re-rolling gives you a different one each time. Upload a photo of what you actually sell and ImagineBuddy does the rest — no prompt, no re-rolls, no studio.') }}</textarea>
              </div>

              <!-- Before Image Upload -->
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <label class="form-label fw-bold d-block text-dark">
                    <i class="bi bi-camera me-1 text-secondary"></i> Before Image (Raw Product Snapshot)
                  </label>
                  @if (!empty($collection->raw_image))
                    <div class="mb-2 d-flex align-items-center gap-2">
                      <img src="{{ $collection->raw_image_url }}" alt="Before Image" class="rounded border shadow-xs file-preview-thumbnail">
                      <div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="remove_raw_image" id="remove_raw_image" value="1">
                          <label class="form-check-label text-danger small fw-bold" for="remove_raw_image">
                            Remove Before Image
                          </label>
                        </div>
                        <small class="text-muted d-block mt-1">Leave unchecked to keep current image</small>
                      </div>
                    </div>
                  @endif
                  <input type="file" name="raw_image" id="raw_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                  <div id="raw_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                    <img id="raw_image_preview_img" src="" alt="New Before Preview" class="file-preview-thumbnail shadow-xs">
                    <div>
                      <span class="badge bg-primary mb-1">New Before Image</span>
                      <small class="text-muted d-block" id="raw_image_preview_name"></small>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1">Unedited product snapshot taken on simple background or phone.</small>
                </div>
              </div>

              <!-- After Image Upload -->
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <label class="form-label fw-bold d-block text-dark">
                    <i class="bi bi-stars me-1 text-success"></i> After Image (AI Studio Transformed)
                  </label>
                  @if (!empty($collection->transformed_image))
                    <div class="mb-2 d-flex align-items-center gap-2">
                      <img src="{{ $collection->transformed_image_url }}" alt="After Image" class="rounded border shadow-xs file-preview-thumbnail">
                      <div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="remove_transformed_image" id="remove_transformed_image" value="1">
                          <label class="form-check-label text-danger small fw-bold" for="remove_transformed_image">
                            Remove After Image
                          </label>
                        </div>
                        <small class="text-muted d-block mt-1">Leave unchecked to keep current image</small>
                      </div>
                    </div>
                  @endif
                  <input type="file" name="transformed_image" id="transformed_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                  <div id="transformed_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                    <img id="transformed_image_preview_img" src="" alt="New After Preview" class="file-preview-thumbnail shadow-xs">
                    <div>
                      <span class="badge bg-success mb-1">New After Image</span>
                      <small class="text-muted d-block" id="transformed_image_preview_name"></small>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1">Final commercial photoshoot visual generated by AI.</small>
                </div>
              </div>

              <!-- 4 Feature Bullet Points -->
              @php
                $feats = $collection->shortcut_feature_items;
              @endphp
              <div class="col-12 mt-3">
                <label class="form-label fw-bold">4 Feature Points (With Checkmarks)</label>
                <div class="row g-2">
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.0', $feats[0] ?? 'Try for free before choosing a plan') }}" placeholder="Feature 1">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.1', $feats[1] ?? 'No prompt engineering required') }}" placeholder="Feature 2">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.2', $feats[2] ?? 'Studio-quality in under 60 seconds') }}" placeholder="Feature 3">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.3', $feats[3] ?? 'Marketplace-ready exports') }}" placeholder="Feature 4">
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">CTA Button Text</label>
                <input type="text" name="shortcut_cta_text" class="form-control" value="{{ old('shortcut_cta_text', $collection->shortcut_cta_text ?? 'Try ImagineBuddy Free') }}" placeholder="Try ImagineBuddy Free">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">CTA Button URL</label>
                <input type="text" name="shortcut_cta_url" class="form-control" value="{{ old('shortcut_cta_url', $collection->shortcut_cta_url ?? 'register') }}" placeholder="register or URL">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Bottom Microcopy Subtext</label>
                <input type="text" name="shortcut_subtext" class="form-control" value="{{ old('shortcut_subtext', $collection->shortcut_subtext ?? 'Free to start · No card needed · Works on web, iOS and Android') }}" placeholder="Free to start · No card needed · Works on web, iOS and Android">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Comparison Caption (Below Image Slider)</label>
                <input type="text" name="shortcut_compare_caption" class="form-control" value="{{ old('shortcut_compare_caption', $collection->shortcut_compare_caption ?? 'Hover to compare — both frames generated by ImagineBuddy from one uploaded photo.') }}" placeholder="Hover to compare — both frames generated by ImagineBuddy from one uploaded photo.">
              </div>

            </div>
          </div>
        </div>

        <!-- CARD: Proven Impact & Metrics (Social Proof Grid) -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-graph-up-arrow me-2 text-success"></i>Proven Impact &amp; Metrics (Social Proof Grid)
              </h5>
              <small class="text-muted">Section below "The Shortcut" showing data-driven outcomes across brands.</small>
            </div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" name="show_metrics_section" id="show_metrics_section" value="1" {{ old('show_metrics_section', $collection->show_metrics_section ?? true) ? 'checked' : '' }}>
              <label class="form-check-label fw-bold text-dark small" for="show_metrics_section">Enable Section</label>
            </div>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold">Eyebrow Badge Text</label>
                <input type="text" name="metrics_eyebrow" class="form-control" value="{{ old('metrics_eyebrow', $collection->metrics_eyebrow ?? 'PROVEN IMPACT') }}" placeholder="PROVEN IMPACT">
              </div>

              <div class="col-md-8">
                <label class="form-label fw-bold">Main Heading</label>
                <input type="text" name="metrics_title" class="form-control" value="{{ old('metrics_title', $collection->metrics_title ?? 'Measurable Results Across Fashion Brands & Agencies') }}" placeholder="Measurable Results Across Fashion Brands & Agencies">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Subtitle / Description</label>
                <textarea name="metrics_subtitle" rows="2" class="form-control" placeholder="Real metrics reported by D2C apparel houses, independent fashion designers, and commercial creative agencies switching to Imaginebuddy's adaptive prompt workflows.">{{ old('metrics_subtitle', $collection->metrics_subtitle ?? "Real metrics reported by D2C apparel houses, independent fashion designers, and commercial creative agencies switching to Imaginebuddy's adaptive prompt workflows.") }}</textarea>
              </div>

              <!-- 4 Metric Cards -->
              @php
                $metricItems = $collection->metrics_item_list;
              @endphp
              <div class="col-12 mt-2">
                <label class="form-label fw-bold d-block">4 Metric Showcase Cards</label>
                <div class="row g-3">
                  @for ($i = 0; $i < 4; $i++)
                    @php $item = $metricItems[$i] ?? ['stat' => '', 'title' => '', 'description' => '']; @endphp
                    <div class="col-md-6 col-lg-3">
                      <div class="p-3 border rounded-3 bg-light h-100">
                        <div class="fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                          <span>Card #{{ $i + 1 }}</span>
                          <span class="badge bg-success bg-opacity-10 text-success">Metric</span>
                        </div>
                        <div class="mb-2">
                          <label class="form-label small text-muted mb-1">Stat / Number (e.g. 12x, 90%)</label>
                          <input type="text" name="metrics_items[{{ $i }}][stat]" class="form-control form-control-sm fw-bold text-success" value="{{ old("metrics_items.$i.stat", $item['stat'] ?? '') }}" placeholder="e.g. 12x">
                        </div>
                        <div class="mb-2">
                          <label class="form-label small text-muted mb-1">Card Title</label>
                          <input type="text" name="metrics_items[{{ $i }}][title]" class="form-control form-control-sm fw-bold" value="{{ old("metrics_items.$i.title", $item['title'] ?? '') }}" placeholder="e.g. Faster Campaign Turnaround">
                        </div>
                        <div>
                          <label class="form-label small text-muted mb-1">Description</label>
                          <textarea name="metrics_items[{{ $i }}][description]" rows="3" class="form-control form-control-sm small" placeholder="e.g. Produce entire editorial lookbooks in hours...">{{ old("metrics_items.$i.description", $item['description'] ?? '') }}</textarea>
                        </div>
                      </div>
                    </div>
                  @endfor
                </div>
              </div>

            </div>
          </div>
        </div>



        <!-- CARD 3: Frequently Asked Questions (FAQ Repeater) -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-question-circle me-2 text-primary"></i>3. Frequently Asked Questions (JSON-LD FAQ Schema)
            </h5>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddFaq">
              <i class="bi bi-plus-lg me-1"></i> Add FAQ
            </button>
          </div>
          <div class="card-body p-lg-4" id="faqContainer">
            @php
              $currentFaqs = !empty($collection->faqs) && is_array($collection->faqs) && count($collection->faqs) > 0 
                ? $collection->faqs 
                : $collection->getDefaultFaqs();
            @endphp

            @foreach ($currentFaqs as $index => $faq)
              <div class="border rounded p-3 mb-3 bg-light faq-item" id="faqItem-{{ $index }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-bold text-dark small"><i class="bi bi-chat-left-text me-1 text-primary"></i> FAQ #{{ $index + 1 }}</span>
                  <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0 btnRemoveFaq">
                    <i class="bi bi-trash"></i> Remove
                  </button>
                </div>
                <div class="mb-2">
                  <input type="text" name="faqs[{{ $index }}][question]" class="form-control form-control-sm mb-1" placeholder="Question" value="{{ $faq['question'] ?? '' }}" required>
                </div>
                <div>
                  <textarea name="faqs[{{ $index }}][answer]" rows="2" class="form-control form-control-sm" placeholder="Answer" required>{{ $faq['answer'] ?? '' }}</textarea>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- CARD 4: SEO Metadata -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-globe me-2 text-primary"></i>4. SEO &amp; Social Metadata
            </h5>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-12">
                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $collection->meta_title) }}">
              </div>

              <div class="col-12">
                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $collection->meta_description) }}</textarea>
              </div>

              <div class="col-12">
                <label for="meta_keywords" class="form-label fw-bold">Meta Keywords</label>
                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $collection->meta_keywords) }}">
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <a href="{{ route('admin.prompts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Collections
          </a>
          <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
            <i class="bi bi-check2-circle me-1"></i> Update Collection Details
          </button>
        </div>

      </form>

      <!-- ================================================================= -->
      <!-- CARD 5: ADD PROMPTS TO THIS COLLECTION (LIVE AJAX SEARCH)        -->
      <!-- MIRRORING EDIT-PHOTOSHOOT PATTERN EXACTLY                        -->
      <!-- ================================================================= -->
      <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>5. Add Prompts to this Collection
          </h5>
          <small class="text-muted">Search existing prompts by title, slug, or ID</small>
        </div>
        <div class="card-body p-lg-4">

          <div class="position-relative mb-3">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="searchPromptInput" class="form-control border-start-0 ps-0" placeholder="Type prompt title, slug, or ID (e.g. 'Saree', 'Silk', 'Jewelry')...">
            </div>
          </div>

          <!-- Live Search Results Container -->
          <div id="searchResultsBox" class="rounded-3 border p-3 bg-light display-none mb-3" style="display: none;">
            <h6 class="fw-bold text-dark mb-2" id="searchResultsHeader">Search Results</h6>
            <div id="searchResultsList" class="list-group list-group-flush">
              <!-- Dynamically populated -->
            </div>
          </div>

        </div>
      </div>

      <!-- CARD 6: Prompts currently in this Collection -->
      <div class="card shadow-custom border-0 mb-5">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-images me-2 text-primary"></i>6. Prompts in this Collection (<span id="promptCountBadge">{{ $collection->items->count() }}</span>)
            </h5>
            <span id="reorderStatusBadge" class="badge ms-2 d-none shadow-xs py-2 px-3 fw-medium"></span>
          </div>
          <small class="text-muted d-flex align-items-center">
            <i class="bi bi-grip-vertical me-1 text-primary"></i> Drag handle on left to reorder
          </small>
        </div>
        <div class="card-body p-lg-4">

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle mb-0" id="tableAssociatedPrompts">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 45px;" class="text-center" title="Drag to reorder">
                    <i class="bi bi-arrow-down-up text-muted"></i>
                  </th>
                  <th scope="col" style="width: 70px;">ID</th>
                  <th scope="col" style="width: 70px;">Preview</th>
                  <th scope="col">Prompt Title</th>
                  <th scope="col" style="width: 100px;">Tier</th>
                  <th scope="col" style="width: 100px;">Status</th>
                  <th scope="col" style="width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody id="associatedPromptsBody">
                @if ($collection->items->count() != 0)
                  @foreach ($collection->items as $item)
                    @php $img = $item->image; @endphp
                    @if ($img)
                      <tr id="promptRow-{{ $img->id }}" data-id="{{ $img->id }}" data-item-id="{{ $item->id }}" class="prompt-sortable-row">
                        <td class="drag-handle text-center text-muted" style="cursor: grab; width: 45px;" title="Drag to reorder">
                          <i class="bi bi-grip-vertical fs-5"></i>
                        </td>
                        <td class="fw-bold text-muted">{{ $img->id }}</td>
                        <td>
                          <img src="{{ Storage::url(config('path.thumbnail') . $img->thumbnail) }}" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" onerror="this.src='{{ asset('public/img/thumbnail-default.jpg') }}';" />
                        </td>
                        <td>
                          <a href="{{ url('prompt', $img->slug) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                            {{ $img->title }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                          </a>
                          <small class="text-muted d-block"><code>{{ $img->slug }}</code></small>
                        </td>
                        <td>
                          <span class="badge bg-{{ $img->item_for_sale == 'sale' ? 'warning' : 'secondary' }}">
                            {{ $img->item_for_sale == 'sale' ? 'Premium' : 'Free' }}
                          </span>
                        </td>
                        <td>
                          <span class="badge bg-{{ $img->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($img->status) }}
                          </span>
                        </td>
                        <td>
                          <button type="button" class="btn btn-link text-danger e-none fs-5 p-0 border-0 btnRemovePrompt" data-id="{{ $img->id }}" title="Remove from Collection">
                            <i class="bi-trash-fill"></i>
                          </button>
                        </td>
                      </tr>
                    @endif
                  @endforeach
                @else
                  <tr id="noPromptsRow">
                    <td colspan="7" class="text-center p-5 text-muted fw-light">
                      No prompts currently in this collection. Use the search box above to add existing prompts.
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Floating Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
  <div id="adminToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
        <i class="bi bi-check-circle-fill text-success fs-5" id="toastIcon"></i>
        <span id="toastMessage" class="fw-medium">Action completed successfully.</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
  var collectionId = {{ $collection->id }};
  var searchTimeout = null;

  var toastEl = document.getElementById('adminToast');
  var adminToast = (toastEl && typeof bootstrap !== 'undefined') ? new bootstrap.Toast(toastEl, { delay: 3500 }) : null;

  function showToast(message, type) {
    type = type || 'success';
    $('#toastMessage').text(message);
    if (type === 'success') {
      $('#toastIcon').attr('class', 'bi bi-check-circle-fill text-success fs-5');
    } else if (type === 'danger' || type === 'error') {
      $('#toastIcon').attr('class', 'bi bi-exclamation-triangle-fill text-danger fs-5');
    } else {
      $('#toastIcon').attr('class', 'bi bi-info-circle-fill text-primary fs-5');
    }
    if (adminToast) {
      adminToast.show();
    } else {
      $('#adminToast').addClass('show').fadeIn();
      setTimeout(function() { $('#adminToast').removeClass('show').fadeOut(); }, 3500);
    }
  }

  // Alias showAlert to showToast to avoid top-scrolling and page reload
  function showAlert(message, type) {
    showToast(message, type);
  }

  // Live Image Previews for File Inputs
  function bindImagePreview(inputId, previewBoxId, previewImgId, nameId) {
    $('#' + inputId).on('change', function() {
      var file = this.files && this.files[0];
      if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#' + previewImgId).attr('src', e.target.result);
          $('#' + previewBoxId).removeClass('d-none').addClass('d-flex');
          if (nameId) {
            $('#' + nameId).text(file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)');
          }
        };
        reader.readAsDataURL(file);
      } else {
        $('#' + previewBoxId).removeClass('d-flex').addClass('d-none');
      }
    });
  }

  bindImagePreview('raw_image', 'raw_image_preview_box', 'raw_image_preview_img', 'raw_image_preview_name');
  bindImagePreview('transformed_image', 'transformed_image_preview_box', 'transformed_image_preview_img', 'transformed_image_preview_name');
  bindImagePreview('cover_image', 'cover_image_preview_box', 'cover_image_preview_img', 'cover_image_preview_name');

  // FAQ Dynamic Repeater
  $('#btnAddFaq').on('click', function() {
    var count = $('.faq-item').length;
    var html = '<div class="border rounded p-3 mb-3 bg-light faq-item" id="faqItem-' + count + '">' +
      '<div class="d-flex justify-content-between align-items-center mb-2">' +
        '<span class="fw-bold text-dark small"><i class="bi bi-chat-left-text me-1 text-primary"></i> FAQ #' + (count + 1) + '</span>' +
        '<button type="button" class="btn btn-sm btn-link text-danger p-0 border-0 btnRemoveFaq"><i class="bi bi-trash"></i> Remove</button>' +
      '</div>' +
      '<div class="mb-2"><input type="text" name="faqs[' + count + '][question]" class="form-control form-control-sm mb-1" placeholder="Question" required></div>' +
      '<div><textarea name="faqs[' + count + '][answer]" rows="2" class="form-control form-control-sm" placeholder="Answer" required></textarea></div>' +
    '</div>';
    $('#faqContainer').append(html);
  });

  $(document).on('click', '.btnRemoveFaq', function() {
    $(this).closest('.faq-item').remove();
  });

  // Reordering Sortable AJAX
  function sendReorderRequest(order) {
    var $badge = $('#reorderStatusBadge');
    $badge.removeClass('d-none bg-success bg-danger').addClass('bg-primary text-white').html('<span class="spinner-border spinner-border-sm me-1"></span> Saving order...');

    $.ajax({
      url: "{{ route('admin.prompts.reorder') }}",
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        collection_id: collectionId,
        order: order
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          $badge.removeClass('bg-primary').addClass('bg-success text-white').html('<i class="bi bi-check2 me-1"></i> Saved!');
          setTimeout(function() { $badge.fadeOut(400, function() { $(this).addClass('d-none').show(); }); }, 2000);
        }
      },
      error: function() {
        $badge.removeClass('bg-primary').addClass('bg-danger text-white').html('<i class="bi bi-x-circle me-1"></i> Failed to save order');
      }
    });
  }

  // HTML5 Drag and Drop Reordering
  function initSortablePrompts() {
    var draggedRow = null;

    $('#associatedPromptsBody').on('dragstart', '.prompt-sortable-row', function(e) {
      draggedRow = this;
      if (e.originalEvent && e.originalEvent.dataTransfer) {
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
      }
      $(this).addClass('opacity-50');
    });

    $('#associatedPromptsBody').on('dragend', '.prompt-sortable-row', function() {
      $(this).removeClass('opacity-50');
      $('.prompt-sortable-row').removeClass('border-top border-primary');
      draggedRow = null;
    });

    $('#associatedPromptsBody').on('dragover', '.prompt-sortable-row', function(e) {
      e.preventDefault();
      if (e.originalEvent && e.originalEvent.dataTransfer) {
        e.originalEvent.dataTransfer.dropEffect = 'move';
      }
      if (this !== draggedRow) {
        $(this).addClass('border-top border-primary');
      }
    });

    $('#associatedPromptsBody').on('dragleave', '.prompt-sortable-row', function() {
      $(this).removeClass('border-top border-primary');
    });

    $('#associatedPromptsBody').on('drop', '.prompt-sortable-row', function(e) {
      e.preventDefault();
      $(this).removeClass('border-top border-primary');
      if (draggedRow && this !== draggedRow) {
        $(draggedRow).insertBefore(this);
        var order = [];
        $('#associatedPromptsBody tr.prompt-sortable-row').each(function() {
          var id = $(this).data('id');
          if (id) { order.push(id); }
        });
        sendReorderRequest(order);
      }
    });

    $('#associatedPromptsBody').on('mousedown', '.drag-handle', function() {
      $(this).closest('tr.prompt-sortable-row').attr('draggable', 'true');
    });

    $('#associatedPromptsBody').on('mouseup mouseleave', '.drag-handle', function() {
      $(this).closest('tr.prompt-sortable-row').removeAttr('draggable');
    });
  }

  initSortablePrompts();

  // Live AJAX Search for Prompts
  $('#searchPromptInput').on('keyup input', function() {
    var query = $.trim($(this).val());
    clearTimeout(searchTimeout);

    if (query.length < 2) {
      $('#searchResultsBox').hide();
      return;
    }

    searchTimeout = setTimeout(function() {
      $.ajax({
        url: "{{ route('admin.prompts.search') }}",
        type: 'GET',
        data: { q: query, collection_id: collectionId },
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            var $list = $('#searchResultsList');
            $list.empty();

            if (res.prompts && res.prompts.length > 0) {
              $.each(res.prompts, function(i, prompt) {
                var isAlreadyAdded = prompt.is_already_added;

                var itemHtml = '<div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-white mb-1 rounded border">' +
                  '<div class="d-flex align-items-center gap-3">' +
                    '<img src="' + prompt.thumb_url + '" class="rounded shadow-sm" width="40" height="40" style="object-fit: cover;" />' +
                    '<div>' +
                      '<strong class="d-block text-dark small">' + prompt.title + (isAlreadyAdded ? ' <span class="badge bg-success ms-1">Already Added</span>' : '') + '</strong>' +
                      '<small class="text-muted" style="font-size: 0.75rem;">ID: ' + prompt.id + ' | Model: ' + prompt.ai_model + ' | Tier: ' + prompt.tier + '</small>' +
                    '</div>' +
                  '</div>' +
                  '<div>' +
                    (isAlreadyAdded 
                      ? '<button class="btn btn-sm btn-secondary disabled rounded-pill px-3" disabled>Added</button>'
                      : '<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnAddPrompt" data-id="' + prompt.id + '"><i class="bi bi-plus-lg me-1"></i> Add to Collection</button>'
                    ) +
                  '</div>' +
                '</div>';

                $list.append(itemHtml);
              });
              $('#searchResultsHeader').text('Search Results (' + res.prompts.length + ' found)');
              $('#searchResultsBox').slideDown();
            } else {
              $list.html('<div class="p-3 text-center text-muted small">No matching prompts found.</div>');
              $('#searchResultsHeader').text('Search Results');
              $('#searchResultsBox').slideDown();
            }
          }
        }
      });
    }, 300);
  });

  // Add Prompt to Collection AJAX
  $(document).on('click', '.btnAddPrompt', function(e) {
    e.preventDefault();
    var promptId = $(this).data('id');
    var $btn = $(this);

    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Adding...');

    $.ajax({
      url: "{{ route('admin.prompts.attach') }}",
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        collection_id: collectionId,
        prompt_id: promptId
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          showToast(res.message, 'success');

          // Smoothly update the button in the search result to 'Added' without jumping or re-querying
          $btn.removeClass('btn-primary btnAddPrompt')
              .addClass('btn-secondary disabled')
              .prop('disabled', true)
              .html('Added');

          var $itemRow = $btn.closest('.list-group-item');
          var $strongTitle = $itemRow.find('strong');
          if ($strongTitle.length && $strongTitle.find('.badge').length === 0) {
            $strongTitle.append(' <span class="badge bg-success ms-1">Already Added</span>');
          }

          $('#noPromptsRow').remove();

          if ($('#promptRow-' + res.item.prompt_id).length === 0) {
            var p = res.item;
            var newRow = '<tr id="promptRow-' + p.prompt_id + '" data-id="' + p.prompt_id + '" data-item-id="' + p.id + '" class="prompt-sortable-row">' +
              '<td class="drag-handle text-center text-muted" style="cursor: grab; width: 45px;" title="Drag to reorder"><i class="bi bi-grip-vertical fs-5"></i></td>' +
              '<td class="fw-bold text-muted">' + p.prompt_id + '</td>' +
              '<td><img src="' + p.thumb_url + '" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" onerror="this.src=\'{{ asset("public/img/thumbnail-default.jpg") }}\';" /></td>' +
              '<td><a href="' + p.prompt_url + '" target="_blank" class="fw-bold text-dark text-decoration-none">' + p.title + ' <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i></a><small class="text-muted d-block"><code>' + p.slug + '</code></small></td>' +
              '<td><span class="badge bg-' + (p.tier === 'Premium' ? 'warning' : 'secondary') + '">' + p.tier + '</span></td>' +
              '<td><span class="badge bg-success">Active</span></td>' +
              '<td><button type="button" class="btn btn-link text-danger e-none fs-5 p-0 border-0 btnRemovePrompt" data-id="' + p.prompt_id + '" title="Remove from Collection"><i class="bi-trash-fill"></i></button></td>' +
            '</tr>';
            $('#associatedPromptsBody').append(newRow);

            var currentCount = parseInt($('#promptCountBadge').text()) || 0;
            $('#promptCountBadge').text(currentCount + 1);
          }
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html('<i class="bi bi-plus-lg me-1"></i> Add to Collection');
        var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error adding prompt to collection.';
        showToast(errMsg, 'danger');
      }
    });
  });

  // Remove Prompt from Collection AJAX
  $(document).on('click', '.btnRemovePrompt', function(e) {
    e.preventDefault();
    var promptId = $(this).data('id');
    var $btn = $(this);

    if (confirm('Remove this prompt from this collection? Note: The prompt itself will NOT be deleted.')) {
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

      $.ajax({
        url: "{{ route('admin.prompts.remove') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          collection_id: collectionId,
          prompt_id: promptId
        },
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            showToast(res.message, 'success');
            $('#promptRow-' + promptId).fadeOut(300, function() {
              $(this).remove();
              if ($('#associatedPromptsBody tr.prompt-sortable-row').length === 0) {
                $('#associatedPromptsBody').html('<tr id="noPromptsRow"><td colspan="7" class="text-center p-5 text-muted fw-light">No prompts currently in this collection. Use the search box above to add existing prompts.</td></tr>');
              }
            });

            var currentCount = parseInt($('#promptCountBadge').text()) || 1;
            $('#promptCountBadge').text(Math.max(0, currentCount - 1));

            // If prompt is in current search results, revert its button back to "Add to Collection"
            var $searchItem = $('#searchResultsList .list-group-item').filter(function() {
              return $(this).find('small:contains("ID: ' + promptId + ' ")').length > 0;
            });
            if ($searchItem.length) {
              $searchItem.find('.badge.bg-success').remove();
              $searchItem.find('button').replaceWith(
                '<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnAddPrompt" data-id="' + promptId + '">' +
                '<i class="bi bi-plus-lg me-1"></i> Add to Collection</button>'
              );
            }
          }
        },
        error: function(xhr) {
          $btn.prop('disabled', false).html('<i class="bi-trash-fill"></i>');
          var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error removing prompt from collection.';
          showToast(errMsg, 'danger');
        }
      });
    }
  });

});
</script>
@endsection
