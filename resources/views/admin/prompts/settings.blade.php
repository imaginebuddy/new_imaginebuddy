@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ route('admin.prompts.index') }}">Prompt Collections</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Hub Page Settings (/prompts)</span>
  </h5>

<div class="content">
	<div class="row justify-content-center">
		<div class="col-lg-9">

			@if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check2 me-1"></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

			<form method="POST" action="{{ route('admin.prompts.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- SECTION 1: Hero Banner -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-window me-2 text-primary"></i>1. Hero Header &amp; Value Proposition
            </h5>
            <a href="{{ url('prompts') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-box-arrow-up-right me-1"></i> Preview /prompts
            </a>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="hero_badge" class="form-label fw-bold">Hero Badge / Pill</label>
                <input type="text" name="hero_badge" id="hero_badge" class="form-control" value="{{ old('hero_badge', $settings->hero_badge) }}" placeholder="Curated AI Prompt Collections">
              </div>

              <div class="col-12">
                <label for="hero_title" class="form-label fw-bold">Hero Headline (H1) <span class="text-danger">*</span></label>
                <input type="text" name="hero_title" id="hero_title" class="form-control" value="{{ old('hero_title', $settings->hero_title) }}" placeholder="Better prompts for better commercial product work." required>
              </div>

              <div class="col-12">
                <label for="hero_subtitle" class="form-label fw-bold">Hero Sub-Headline / Description</label>
                <textarea name="hero_subtitle" id="hero_subtitle" rows="3" class="form-control" placeholder="Description of your prompt collections directory...">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
              </div>

              <div class="col-12">
                <label for="cover_image" class="form-label fw-bold">Cover Banner Image (Optional)</label>
                <small class="text-muted d-block mb-2">Displays as the ambient hero background banner on the <code>/prompts</code> hub directory page.</small>
                
                @if (!empty($settings->cover_image))
                  <div class="mb-3 d-flex align-items-center gap-3 p-2 bg-light rounded border">
                    <img src="{{ $settings->cover_image_url }}" alt="Hub Cover" class="file-preview-thumbnail shadow-xs">
                    <div>
                      <span class="badge bg-secondary mb-1">Current Hub Banner</span>
                      <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="remove_cover_image" id="remove_cover_image" value="1">
                        <label class="form-check-label text-danger small fw-semibold" for="remove_cover_image">
                          <i class="bi bi-trash"></i> Remove Cover Banner
                        </label>
                      </div>
                    </div>
                  </div>
                @endif

                <input type="file" name="cover_image" id="cover_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif">
                <div id="cover_image_preview_box" class="mt-2 d-none align-items-center gap-2">
                  <img id="cover_image_preview_img" src="" alt="New Cover Preview" class="file-preview-thumbnail shadow-xs">
                  <div>
                    <span class="badge bg-primary mb-1">New Banner Selected</span>
                    <small class="text-muted d-block" id="cover_image_preview_name"></small>
                  </div>
                </div>
                <small class="text-muted d-block mt-1">Recommended size: 1920x600px or wider landscape image (JPEG, PNG, or WebP up to 10MB). If omitted, the default dark ambient green radial glow will be displayed.</small>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Conversion / Bridge Banner -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-lightning-charge me-2 text-primary"></i>2. Product-Led Conversion Banner (Mid/Bottom Funnel)
            </h5>
            <small class="text-muted">Drives users from manual prompt tweaking into your 1-click automated AI studio tool.</small>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-12">
                <label for="banner_title" class="form-label fw-bold">Banner Headline</label>
                <input type="text" name="banner_title" id="banner_title" class="form-control" value="{{ old('banner_title', $settings->banner_title) }}" placeholder="Done with manual prompts? Let AI do the studio work.">
              </div>

              <div class="col-12">
                <label for="banner_subtitle" class="form-label fw-bold">Banner Subtitle / Value Pitch</label>
                <textarea name="banner_subtitle" id="banner_subtitle" rows="2" class="form-control">{{ old('banner_subtitle', $settings->banner_subtitle) }}</textarea>
              </div>

              <div class="col-md-6">
                <label for="banner_cta_text" class="form-label fw-bold">Button CTA Label</label>
                <input type="text" name="banner_cta_text" id="banner_cta_text" class="form-control" value="{{ old('banner_cta_text', $settings->banner_cta_text) }}" placeholder="Try ImagineBuddy AI Free">
              </div>

              <div class="col-md-6">
                <label for="banner_cta_url" class="form-label fw-bold">Button CTA Destination URL</label>
                <input type="text" name="banner_cta_url" id="banner_cta_url" class="form-control" value="{{ old('banner_cta_url', $settings->banner_cta_url) }}" placeholder="{{ url('register') }}">
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 3: SEO & Meta Tags -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-search me-2 text-primary"></i>3. Hub SEO &amp; Meta Tags
            </h5>
          </div>
          <div class="card-body p-lg-4">
            <div class="row g-3">
              <div class="col-12">
                <label for="meta_title" class="form-label fw-bold">SEO Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $settings->meta_title) }}">
              </div>

              <div class="col-12">
                <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $settings->meta_description) }}</textarea>
              </div>

              <div class="col-12">
                <label for="meta_keywords" class="form-label fw-bold">Meta Keywords</label>
                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $settings->meta_keywords) }}">
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-5">
          <a href="{{ route('admin.prompts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Collections
          </a>
          <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
            <i class="bi bi-check2-circle me-1"></i> Save Hub Page Settings
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<style>
  /* Ensure file inputs are fully visible, interactive, and properly styled */
  .custom-file,
  input[type="file"].custom-file,
  input[type="file"].form-control {
    position: relative !important;
    opacity: 1 !important;
    visibility: visible !important;
    width: 100% !important;
    height: auto !important;
    cursor: pointer !important;
    display: block !important;
    z-index: 1 !important;
    background-color: #fff !important;
    border: 1px solid #ced4da !important;
    padding: .375rem .75rem !important;
  }
  .file-preview-thumbnail {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #dee2e6;
  }
</style>
@endsection

@section('javascript')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('cover_image');
    const previewBox = document.getElementById('cover_image_preview_box');
    const previewImg = document.getElementById('cover_image_preview_img');
    const previewName = document.getElementById('cover_image_preview_name');

    if (fileInput) {
      fileInput.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (evt) {
            previewImg.src = evt.target.result;
            previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            previewBox.classList.remove('d-none');
            previewBox.classList.add('d-flex');
          };
          reader.readAsDataURL(file);
        } else {
          previewBox.classList.remove('d-flex');
          previewBox.classList.add('d-none');
        }
      });
    }
  });
</script>
@endsection
