@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/categories') }}">{{ __('misc.categories') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Listing Page Content Settings (/categories)</span>

    <a href="{{ url('categories') }}" target="_blank" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0">
        <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Categories Page
    </a>
</h5>

<div class="content">
	<div class="row">
		<div class="col-lg-12">

      @if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
          <div>
            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i> Categories Hub Page Content Management</h6>
            <small class="text-muted">Manage the static titles, subtitles, search placeholders, SEO meta, and promotional banners for the main <code>/categories</code> listing page.</small>
          </div>
        </div>

				<div class="card-body p-lg-5">

					<form method="post" action="{{ url('panel/admin/categories/listing-settings') }}">
             @csrf

             {{-- 1. CATEGORIES TAB HEADER --}}
             <h6 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill me-2 text-primary"></i> 1. Categories Tab Header</h6>

		        <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Categories Tab Title (H1)</label>
		          <div class="col-sm-9">
		            <input value="{{ old('page_title', $settings->page_title) }}" name="page_title" type="text" class="form-control" placeholder="Categories">
                <small class="text-muted">Headline displayed when the "Categories" tab is selected.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Categories Tab Subtitle</label>
		          <div class="col-sm-9">
		            <textarea class="form-control" name="page_subtitle" rows="2" placeholder="Browse by Category">{{ old('page_subtitle', $settings->page_subtitle) }}</textarea>
		          </div>
		        </div>

            <div class="row mb-4">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Categories Search Placeholder</label>
		          <div class="col-sm-9">
		            <input value="{{ old('category_search_placeholder', $settings->category_search_placeholder) }}" name="category_search_placeholder" type="text" class="form-control" placeholder="Search categories...">
		          </div>
		        </div>

            <hr class="my-4">

            {{-- 2. TAGS TAB HEADER --}}
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-tags-fill me-2 text-primary"></i> 2. Tags Tab Header</h6>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Tags Tab Title</label>
		          <div class="col-sm-9">
		            <input value="{{ old('tags_title', $settings->tags_title) }}" name="tags_title" type="text" class="form-control" placeholder="Tags">
                <small class="text-muted">Headline displayed when switching to the "Tags" tab.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Tags Tab Subtitle</label>
		          <div class="col-sm-9">
		            <textarea class="form-control" name="tags_subtitle" rows="2" placeholder="Explore by popular keywords and tags">{{ old('tags_subtitle', $settings->tags_subtitle) }}</textarea>
		          </div>
		        </div>

            <div class="row mb-4">
		          <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Tags Search Placeholder</label>
		          <div class="col-sm-9">
		            <input value="{{ old('tag_search_placeholder', $settings->tag_search_placeholder) }}" name="tag_search_placeholder" type="text" class="form-control" placeholder="Search tags...">
		          </div>
		        </div>

            <hr class="my-4">

            {{-- 3. PROMOTIONAL CTA BANNER ON /CATEGORIES --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold text-dark m-0"><i class="bi bi-megaphone me-2 text-primary"></i> 3. Promotional CTA Banner on /categories (Optional)</h6>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="cta_banner_enabled" value="1" id="listCtaSwitch" @checked(old('cta_banner_enabled', $settings->cta_banner_enabled))>
                <label class="form-check-label fw-semibold" for="listCtaSwitch">Show CTA Banner on /categories</label>
              </div>
            </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end">Banner Headline</label>
		          <div class="col-sm-9">
		            <input value="{{ old('cta_banner_title', $settings->cta_banner_title) }}" name="cta_banner_title" type="text" class="form-control" placeholder="e.g. Need Custom AI Photoshoots For Your Brand?">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end">Banner Description</label>
		          <div class="col-sm-9">
		            <textarea class="form-control" name="cta_banner_desc" rows="2" placeholder="e.g. Hire our team to create bespoke commercial photoshoot concepts.">{{ old('cta_banner_desc', $settings->cta_banner_desc) }}</textarea>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end">CTA Button Text</label>
		          <div class="col-sm-9">
		            <input value="{{ old('cta_banner_btn_text', $settings->cta_banner_btn_text) }}" name="cta_banner_btn_text" type="text" class="form-control" placeholder="e.g. Explore Custom Service">
		          </div>
		        </div>

            <div class="row mb-4">
		          <label class="col-sm-3 col-form-label text-lg-end">CTA Button Link</label>
		          <div class="col-sm-9">
		            <input value="{{ old('cta_banner_btn_url', $settings->cta_banner_btn_url) }}" name="cta_banner_btn_url" type="text" class="form-control" placeholder="e.g. hire">
		          </div>
		        </div>

            <hr class="my-4">

            {{-- 4. SEO METADATA --}}
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-search me-2 text-primary"></i> 4. SEO Metadata</h6>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end">{{ __('admin.seo_title') }}</label>
		          <div class="col-sm-9">
		            <input value="{{ old('meta_title', $settings->meta_title) }}" name="meta_title" type="text" class="form-control" placeholder="Categories - AI Product Photography Prompts | ImagineBuddy">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-3 col-form-label text-lg-end">{{ __('admin.keywords') }} (SEO)</label>
		          <div class="col-sm-9">
		            <input value="{{ old('meta_keywords', $settings->meta_keywords) }}" name="meta_keywords" type="text" class="form-control" placeholder="keyword1, keyword2, ...">
		          </div>
		        </div>

						<div class="row mb-4">
		          <label class="col-sm-3 col-form-label text-lg-end">{{ __('admin.description') }} (SEO)</label>
		          <div class="col-sm-9">
                <textarea class="form-control" name="meta_description" rows="3" placeholder="Meta description for search engine previews...">{{ old('meta_description', $settings->meta_description) }}</textarea>
		          </div>
		        </div>

						<div class="row mb-3">
		          <div class="col-sm-9 offset-sm-3">
		            <button type="submit" class="btn btn-dark px-5 py-2">
                  <i class="bi bi-check2-circle me-1"></i> Save Listing Settings
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
