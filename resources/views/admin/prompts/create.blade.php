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
    <span class="text-muted">Create Collection</span>
  </h5>

<div class="content">
	<div class="row justify-content-center">
		<div class="col-lg-10">

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

			<form method="POST" action="{{ route('admin.prompts.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- CARD 1: Core Collection Details -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-folder-plus me-2 text-primary"></i>1. Collection Overview
            </h5>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-md-8">
                <label for="title" class="form-label fw-bold">Collection Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Saree Photography Prompts" required>
                <small class="text-muted">Target keyword phrase (e.g. "Saree Photography Prompts", "Luxury Watch Photography")</small>
              </div>

              <div class="col-md-4">
                <label for="category_id" class="form-label fw-bold">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                  <option value="">Select Category</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-8">
                <label for="slug" class="form-label fw-bold">URL Slug (Optional)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted">{{ url('prompts') }}/</span>
                  <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="saree-photography-prompts">
                </div>
                <small class="text-muted">Auto-generated from title if left blank.</small>
              </div>

              <div class="col-md-4">
                <label for="ai_engine" class="form-label fw-bold">AI Engine Compatibility</label>
                <input type="text" name="ai_engine" id="ai_engine" class="form-control" value="{{ old('ai_engine', 'Midjourney, Google Gemini & Flux') }}" placeholder="Midjourney & Flux">
              </div>

              <div class="col-12">
                <label for="summary" class="form-label fw-bold">Summary / Value Proposition</label>
                <textarea name="summary" id="summary" rows="3" class="form-control" placeholder="Short description used on the /prompts hub card and in the hero snippet...">{{ old('summary') }}</textarea>
              </div>

              <div class="col-md-6">
                <label for="cover_image" class="form-label fw-bold">Cover Banner Image (Optional)</label>
                <small class="text-muted d-block mb-1">Displays as the ambient hero background banner on this collection's page.</small>
                <input type="file" name="cover_image" id="cover_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                <div id="cover_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                  <img id="cover_image_preview_img" src="" alt="Cover Preview" class="file-preview-thumbnail shadow-xs" style="width: 80px; height: 50px;">
                  <div>
                    <span class="badge bg-primary mb-1">Cover Preview</span>
                    <small class="text-muted d-block" id="cover_image_preview_name"></small>
                  </div>
                </div>
                <small class="text-muted">If omitted, the default dark ambient gradient is used.</small>
              </div>

              <div class="col-md-3">
                <label for="status" class="form-label fw-bold">Publication Status</label>
                <select name="status" id="status" class="form-select">
                  <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active (Public)</option>
                  <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                </select>
              </div>

              <div class="col-md-3 d-flex align-items-center mt-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
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
              <input class="form-check-input" type="checkbox" name="show_shortcut_section" id="show_shortcut_section" value="1" checked>
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
                <input type="text" name="shortcut_eyebrow" class="form-control" value="{{ old('shortcut_eyebrow', 'THE SHORTCUT') }}" placeholder="THE SHORTCUT">
              </div>

              <div class="col-md-8">
                <label class="form-label fw-bold">Main Heading</label>
                <input type="text" name="shortcut_title" class="form-control" value="{{ old('shortcut_title', 'You don’t need to learn prompting to get the images you want') }}" placeholder="You don’t need to learn prompting to get the images you want">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Description / Value Proposition</label>
                <textarea name="shortcut_description" rows="3" class="form-control" placeholder="Explain the friction of manual prompting vs the ease of automated studio generation...">{{ old('shortcut_description', 'Every prompt above invents a new image that isn\'t yours, and re-rolling gives you a different one each time. Upload a photo of what you actually sell and ImagineBuddy does the rest — no prompt, no re-rolls, no studio.') }}</textarea>
              </div>

              <!-- Before Image Upload -->
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <label class="form-label fw-bold d-block text-dark">
                    <i class="bi bi-camera me-1 text-secondary"></i> Before Image (Raw Product Snapshot)
                  </label>
                  <input type="file" name="raw_image" id="raw_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                  <div id="raw_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                    <img id="raw_image_preview_img" src="" alt="Before Preview" class="file-preview-thumbnail shadow-xs">
                    <div>
                      <span class="badge bg-primary mb-1">Before Preview</span>
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
                  <input type="file" name="transformed_image" id="transformed_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                  <div id="transformed_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                    <img id="transformed_image_preview_img" src="" alt="After Preview" class="file-preview-thumbnail shadow-xs">
                    <div>
                      <span class="badge bg-success mb-1">After Preview</span>
                      <small class="text-muted d-block" id="transformed_image_preview_name"></small>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1">Final commercial photoshoot visual generated by AI.</small>
                </div>
              </div>

              <!-- 4 Feature Bullet Points -->
              <div class="col-12 mt-3">
                <label class="form-label fw-bold">4 Feature Points (With Checkmarks)</label>
                <div class="row g-2">
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.0', 'Try for free before choosing a plan') }}" placeholder="Feature 1">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.1', 'No prompt engineering required') }}" placeholder="Feature 2">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.2', 'Studio-quality in under 60 seconds') }}" placeholder="Feature 3">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="shortcut_features[]" class="form-control" value="{{ old('shortcut_features.3', 'Marketplace-ready exports') }}" placeholder="Feature 4">
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">CTA Button Text</label>
                <input type="text" name="shortcut_cta_text" class="form-control" value="{{ old('shortcut_cta_text', 'Try ImagineBuddy Free') }}" placeholder="Try ImagineBuddy Free">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">CTA Button URL</label>
                <input type="text" name="shortcut_cta_url" class="form-control" value="{{ old('shortcut_cta_url', 'register') }}" placeholder="register or URL">
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold">Bottom Microcopy Subtext</label>
                <input type="text" name="shortcut_subtext" class="form-control" value="{{ old('shortcut_subtext', 'Free to start · No card needed · Works on web, iOS and Android') }}" placeholder="Free to start · No card needed · Works on web, iOS and Android">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Comparison Caption (Below Image Slider)</label>
                <input type="text" name="shortcut_compare_caption" class="form-control" value="{{ old('shortcut_compare_caption', 'Hover to compare — both frames generated by ImagineBuddy from one uploaded photo.') }}" placeholder="Hover to compare — both frames generated by ImagineBuddy from one uploaded photo.">
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
              <input class="form-check-input" type="checkbox" role="switch" name="show_metrics_section" id="show_metrics_section" value="1" {{ old('show_metrics_section', '1') ? 'checked' : '' }}>
              <label class="form-check-label fw-bold text-dark small" for="show_metrics_section">Enable Section</label>
            </div>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold">Eyebrow Badge Text</label>
                <input type="text" name="metrics_eyebrow" class="form-control" value="{{ old('metrics_eyebrow', 'PROVEN IMPACT') }}" placeholder="PROVEN IMPACT">
              </div>

              <div class="col-md-8">
                <label class="form-label fw-bold">Main Heading</label>
                <input type="text" name="metrics_title" class="form-control" value="{{ old('metrics_title', 'Measurable Results Across Fashion Brands & Agencies') }}" placeholder="Measurable Results Across Fashion Brands & Agencies">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Subtitle / Description</label>
                <textarea name="metrics_subtitle" rows="2" class="form-control" placeholder="Real metrics reported by D2C apparel houses, independent fashion designers, and commercial creative agencies switching to Imaginebuddy's adaptive prompt workflows.">{{ old('metrics_subtitle', "Real metrics reported by D2C apparel houses, independent fashion designers, and commercial creative agencies switching to Imaginebuddy's adaptive prompt workflows.") }}</textarea>
              </div>

              <!-- 4 Metric Cards -->
              @php
                $defaultMetrics = [
                  ['stat' => '12x', 'title' => 'Faster Campaign Turnaround', 'description' => 'Produce entire editorial lookbooks in hours instead of four weeks of production planning'],
                  ['stat' => '90%', 'title' => 'Production Cost Reduction', 'description' => 'Eliminate model agency bookings, location permits, gear hire, and crew day rates'],
                  ['stat' => '3.8x', 'title' => 'Higher Paid Social CTR', 'description' => 'Driven by vibrant on-model editorial storytelling and high-frequency ad creative refreshes'],
                  ['stat' => '99.8%', 'title' => 'Catalog Acceptance Rate', 'description' => 'Zero image suppressions across Amazon Fashion, Myntra, ASOS, and Shopify channels'],
                ];
              @endphp
              <div class="col-12 mt-2">
                <label class="form-label fw-bold d-block">4 Metric Showcase Cards</label>
                <div class="row g-3">
                  @for ($i = 0; $i < 4; $i++)
                    @php $item = $defaultMetrics[$i]; @endphp
                    <div class="col-md-6 col-lg-3">
                      <div class="p-3 border rounded-3 bg-light h-100">
                        <div class="fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                          <span>Card #{{ $i + 1 }}</span>
                          <span class="badge bg-success bg-opacity-10 text-success">Metric</span>
                        </div>
                        <div class="mb-2">
                          <label class="form-label small text-muted mb-1">Stat / Number (e.g. 12x, 90%)</label>
                          <input type="text" name="metrics_items[{{ $i }}][stat]" class="form-control form-control-sm fw-bold text-success" value="{{ old("metrics_items.$i.stat", $item['stat']) }}" placeholder="e.g. 12x">
                        </div>
                        <div class="mb-2">
                          <label class="form-label small text-muted mb-1">Card Title</label>
                          <input type="text" name="metrics_items[{{ $i }}][title]" class="form-control form-control-sm fw-bold" value="{{ old("metrics_items.$i.title", $item['title']) }}" placeholder="e.g. Faster Campaign Turnaround">
                        </div>
                        <div>
                          <label class="form-label small text-muted mb-1">Description</label>
                          <textarea name="metrics_items[{{ $i }}][description]" rows="3" class="form-control form-control-sm small" placeholder="e.g. Produce entire editorial lookbooks in hours...">{{ old("metrics_items.$i.description", $item['description']) }}</textarea>
                        </div>
                      </div>
                    </div>
                  @endfor
                </div>
              </div>

            </div>
          </div>
        </div>



        <!-- CARD 3: SEO Metadata -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-globe me-2 text-primary"></i>3. SEO &amp; Social Metadata
            </h5>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-12">
                <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="e.g. 50+ Best Saree Photography AI Prompts for Midjourney &amp; Flux">
              </div>

              <div class="col-12">
                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="form-control" placeholder="Target 150-160 characters for maximum Google click-through rate...">{{ old('meta_description') }}</textarea>
              </div>

              <div class="col-12">
                <label for="meta_keywords" class="form-label fw-bold">Meta Keywords</label>
                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords') }}" placeholder="saree prompts, ethnic wear photography, midjourney saree shoot, commercial ai photography">
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-5">
          <a href="{{ route('admin.prompts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Collections
          </a>
          <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
            <i class="bi bi-check2-circle me-1"></i> Save &amp; Attach Prompts &rarr;
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
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
});
</script>
@endsection
