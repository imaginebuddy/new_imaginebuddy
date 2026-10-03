@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/blog') }}">Blog</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.add_new') }} Article</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0 mb-4">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/blog/create') }}" enctype="multipart/form-data">
             @csrf

             <!-- Main Editorial Fields -->
		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.title') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('title') }}" id="postTitle" name="title" required type="text" class="form-control" placeholder="e.g. 10 Best AI Prompt Techniques for Commercial Product Photos">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.slug') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('slug') }}" id="postSlug" name="slug" type="text" class="form-control" placeholder="Leave blank to automatically slugify from title">
                <small class="text-muted d-block mt-1">SEO URL path: <code>{{ url('blog') }}/<span id="slugPreview">article-slug</span></code></small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Category</label>
		          <div class="col-sm-10">
		            <select name="blog_category_id" class="form-select">
                  <option value="">Select Category (Optional)</option>
                  @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('blog_category_id') == $cat->id ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Author <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <select name="user_id" required class="form-select">
                  @foreach ($authors as $author)
                    <option value="{{ $author->id }}" {{ old('user_id', $authors->contains('id', auth()->id()) ? auth()->id() : ($authors->first()->id ?? '')) == $author->id ? 'selected' : '' }}>
                      {{ $author->name ?: $author->username }} ({{ '@' . $author->username }}) &mdash; Super Admin
                    </option>
                  @endforeach
                </select>
                <small class="text-muted d-block mt-1">Author must be a Super Admin.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Status &amp; Schedule <span class="text-danger">*</span></label>
		          <div class="col-sm-5">
                <select name="status" class="form-select" id="statusSelect">
                  <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Save as Draft</option>
                  <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publish Immediately</option>
                  <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Schedule for Later</option>
                </select>
		          </div>
              <div class="col-sm-5">
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="form-control" title="Publication Date &amp; Time">
                <small class="text-muted d-block mt-1">Leave empty to use current time upon publication.</small>
              </div>
		        </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-lg-end">Spotlight Feature</label>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} role="switch" id="featuredSwitch">
                  <label class="form-check-label ms-2" for="featuredSwitch">Feature on Blog Hub Header / Hero Banner</label>
                </div>
              </div>
            </div>

            <!-- Featured Image Upload -->
            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Featured Image</label>
		          <div class="col-lg-6 col-sm-10">
                <div class="input-group mb-1">
                  <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/jpg" class="form-control custom-file rounded-pill" id="featuredImageInput">
                </div>
                <small class="text-muted d-block mt-1">Recommended size: 1200x630px or 16:9 ratio. JPG, WebP, or PNG up to 4MB.</small>

                {{-- Live Image Preview Container --}}
                <div class="mt-3 p-2 border rounded bg-light d-none align-items-center justify-content-center" id="featuredPreviewWrapper" style="max-width: 320px; max-height: 180px; overflow: hidden;">
                  <img id="featuredPreviewImg" src="#" alt="Featured Image Preview" class="rounded" style="max-width: 100%; max-height: 160px; object-fit: cover;">
                </div>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Image Alt Text</label>
		          <div class="col-sm-10">
                <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt') }}" class="form-control" placeholder="Descriptive alternative text for screen readers and Google Image search">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Tags</label>
		          <div class="col-sm-10">
                <input type="text" name="tags" value="{{ old('tags') }}" class="form-control" placeholder="e.g. Midjourney, E-commerce, Lighting, Product Photos">
                <small class="text-muted d-block mt-1">Comma-separated tags for topic grouping and related article matching.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Excerpt / Summary</label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="excerpt" rows="2" placeholder="Brief summary for article cards and social previews (defaults to first paragraph if empty)...">{{ old('excerpt') }}</textarea>
		          </div>
		        </div>

            <!-- CKEditor Content Area -->
						<div class="row mb-4">
		          <label class="col-sm-2 col-form-label text-lg-end">Article Content <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="content" rows="12" id="content" required placeholder="Write your full article here...">{{ old('content') }}</textarea>
		          </div>
		        </div>

            <!-- Collapsible SEO & Social Sharing Panel -->
            <div class="card bg-light border-0 rounded-3 mb-4">
              <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <a class="text-dark fw-bold text-decoration-none d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#seoCollapse" role="button" aria-expanded="false" aria-controls="seoCollapse">
                  <span><i class="bi bi-search me-2 text-primary"></i> Search Engine &amp; Social Media Optimization (SEO)</span>
                  <i class="bi bi-chevron-down"></i>
                </a>
              </div>
              <div class="collapse show p-4" id="seoCollapse">
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">SEO Meta Title</label>
                  <div class="col-sm-9">
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control form-control-sm" placeholder="Custom Google title tag (recommended max 60 chars)">
                    <small class="text-muted d-block mt-1">If blank, defaults to: <code>[Article Title] - ImagineBuddy Blog</code></small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">SEO Meta Description</label>
                  <div class="col-sm-9">
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm" placeholder="Custom snippet for search results (recommended 140-160 chars)">{{ old('meta_description') }}</textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Meta Keywords</label>
                  <div class="col-sm-9">
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control form-control-sm" placeholder="comma, separated, keywords">
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Canonical URL</label>
                  <div class="col-sm-9">
                    <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="form-control form-control-sm" placeholder="Optional. Specify canonical URL if this article was syndicated from another domain">
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Robots Indexing</label>
                  <div class="col-sm-9">
                    <select name="robots" class="form-select form-select-sm">
                      <option value="index, follow" {{ old('robots', 'index, follow') == 'index, follow' ? 'selected' : '' }}>index, follow (Standard / Recommended)</option>
                      <option value="noindex, follow" {{ old('robots') == 'noindex, follow' ? 'selected' : '' }}>noindex, follow</option>
                      <option value="noindex, nofollow" {{ old('robots') == 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Social Share Title (OG)</label>
                  <div class="col-sm-9">
                    <input type="text" name="og_title" value="{{ old('og_title') }}" class="form-control form-control-sm" placeholder="Open Graph title for Facebook &amp; LinkedIn">
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Social Share Description (OG)</label>
                  <div class="col-sm-9">
                    <textarea name="og_description" rows="2" class="form-control form-control-sm" placeholder="Open Graph description">{{ old('og_description') }}</textarea>
                  </div>
                </div>

                <div class="row mb-2">
                  <label class="col-sm-3 col-form-label">Structured Data Schema</label>
                  <div class="col-sm-9">
                    <select name="schema_type" class="form-select form-select-sm">
                      <option value="BlogPosting" {{ old('schema_type', 'BlogPosting') == 'BlogPosting' ? 'selected' : '' }}>BlogPosting (Default / Recommended)</option>
                      <option value="Article" {{ old('schema_type') == 'Article' ? 'selected' : '' }}>Article</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-2 px-5 py-2 fw-semibold">
                  <i class="bi-check2 me-1"></i> Save &amp; Publish Article
                </button>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection

@section('javascript')
<script src="{{ asset('public/js/ckeditor/ckeditor-init.js') }}?v={{$settings->version}}" type="text/javascript"></script>
<script type="text/javascript">
  document.getElementById('postTitle').addEventListener('input', function() {
    var slugInput = document.getElementById('postSlug');
    if (!slugInput.value || slugInput.dataset.manual !== 'true') {
      var slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
      document.getElementById('slugPreview').innerText = slug || 'article-slug';
    }
  });

  document.getElementById('postSlug').addEventListener('input', function() {
    this.dataset.manual = 'true';
    document.getElementById('slugPreview').innerText = this.value || 'article-slug';
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
</script>
@endsection
