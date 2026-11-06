@extends('admin.layout')

@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <div class="d-flex align-items-center gap-2">
            <h1 class="m-0 text-dark fw-bold">
              {{ $page->title }}
            </h1>
            <span class="badge bg-info text-dark rounded-pill">
              {{ $page->route_prefix ? ucwords(str_replace('-', ' ', $page->route_prefix)) : 'Guide' }}
            </span>
          </div>
          <p class="text-muted small mb-0">
            Public URL: <a href="{{ $page->public_url }}" target="_blank" class="text-decoration-none">{{ $page->public_url }}</a>
          </p>
        </div>
        <div class="col-sm-6 text-end">
          <a href="{{ $page->preview_url }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 me-1">
            <i class="bi bi-box-arrow-up-right me-1"></i> Live Preview
          </a>
          <a href="{{ url('panel/admin/page-manager') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> All Pages
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content Tabs -->
  <section class="content">
    <div class="container-fluid">

      @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> {{ session('success_message') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

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

      <div class="card card-primary card-outline card-outline-tabs shadow-sm">
        <div class="card-header p-0 border-bottom-0 bg-light">
          <ul class="nav nav-tabs" id="pageManagerTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active fw-bold py-3 px-4" id="tab-general-link" data-bs-toggle="pill" href="#tab-general" role="tab">
                <i class="bi bi-sliders me-1"></i> 1. General & Routing
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold py-3 px-4" id="tab-sections-link" data-bs-toggle="pill" href="#tab-sections" role="tab">
                <i class="bi bi-layers me-1"></i> 2. Section Builder ({{ $page->sections->count() }})
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold py-3 px-4" id="tab-assets-link" data-bs-toggle="pill" href="#tab-assets" role="tab">
                <i class="bi bi-images me-1"></i> 3. Connected Prompts ({{ $page->items->count() }})
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold py-3 px-4" id="tab-seo-link" data-bs-toggle="pill" href="#tab-seo" role="tab">
                <i class="bi bi-google me-1"></i> 4. SEO & Social Meta
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body">
          <div class="tab-content" id="pageManagerTabsContent">

            <!-- TAB 1: General & Routing -->
            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
              <form action="{{ url('panel/admin/page-manager/update/' . $page->id) }}" method="POST" enctype="multipart/form-data" id="pageGeneralForm">
                @csrf
                <input type="hidden" name="active_tab" value="tab-general">
                <div class="row g-4">
                  <div class="col-lg-8">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label fw-bold">Vertical Silo</label>
                        <select name="route_prefix" class="form-select">
                          <option value="use-case" {{ old('route_prefix', $page->route_prefix) === 'use-case' ? 'selected' : '' }}>Use Case (/use-case)</option>
                          <option value="industry" {{ old('route_prefix', $page->route_prefix) === 'industry' ? 'selected' : '' }}>Industry (/industry)</option>
                          <option value="product" {{ old('route_prefix', $page->route_prefix) === 'product' ? 'selected' : '' }}>Product (/product)</option>
                          <option value="garment" {{ old('route_prefix', $page->route_prefix) === 'garment' ? 'selected' : '' }}>Garment (/garment)</option>
                          <option value="marketplace" {{ old('route_prefix', $page->route_prefix) === 'marketplace' ? 'selected' : '' }}>Marketplace (/marketplace)</option>
                          <option value="platform-ai-ads" {{ old('route_prefix', $page->route_prefix) === 'platform-ai-ads' ? 'selected' : '' }}>Platform AI Ads (/platform-ai-ads)</option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-bold">Page Type</label>
                        <select name="page_type" class="form-select">
                          <option value="solution" {{ old('page_type', $page->page_type) === 'solution' ? 'selected' : '' }}>Solution Detail</option>
                          <option value="hub" {{ old('page_type', $page->page_type) === 'hub' ? 'selected' : '' }}>Hub Directory</option>
                        </select>
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-bold">Page Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $page->title) }}" required>
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-bold">URL Slug</label>
                        <div class="input-group">
                          <span class="input-group-text bg-light text-muted">/{{ $page->route_prefix ? $page->route_prefix . '/' : 'guides/' }}</span>
                          <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" required>
                        </div>
                        <div class="form-text text-warning"><i class="bi bi-info-circle"></i> Changing a published slug will automatically generate a 301 redirect.</div>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-bold">Eyebrow Badge</label>
                        <input type="text" name="header_badge" class="form-control" value="{{ old('header_badge', $page->header_badge) }}">
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-bold">Card Icon / Emoji</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $page->icon) }}">
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-bold">Header CTA Text</label>
                        <input type="text" name="header_cta_text" class="form-control" value="{{ old('header_cta_text', $page->header_cta_text) }}">
                      </div>

                      <div class="col-md-6">
                        <label class="form-label fw-bold">Header CTA URL</label>
                        <input type="text" name="header_cta_url" class="form-control" value="{{ old('header_cta_url', $page->header_cta_url) }}">
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-bold">Header Subtext</label>
                        <input type="text" name="header_cta_subtext" class="form-control" value="{{ old('header_cta_subtext', $page->header_cta_subtext) }}">
                      </div>

                      <div class="col-12">
                        <label class="form-label fw-bold">Excerpt / Summary</label>
                        <textarea name="excerpt" rows="3" class="form-control">{{ old('excerpt', $page->excerpt) }}</textarea>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                      <h6 class="fw-bold mb-3">Publishing Status</h6>
                      <div class="mb-3">
                        <select name="status" class="form-select">
                          <option value="published" {{ $page->status === 'published' ? 'selected' : '' }}>Published</option>
                          <option value="draft" {{ $page->status === 'draft' ? 'selected' : '' }}>Draft</option>
                          <option value="scheduled" {{ $page->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                          <option value="archived" {{ $page->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label small fw-bold">Author <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                          @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ ($page->user_id == $author->id || $loop->first) ? 'selected' : '' }}>
                              {{ $author->name ?: $author->username }}
                            </option>
                          @endforeach
                        </select>
                      </div>

                      <div class="mb-3">
                        <label class="form-label small fw-bold">Search Intent</label>
                        <select name="search_intent" class="form-select">
                          <option value="commercial" {{ $page->search_intent === 'commercial' ? 'selected' : '' }}>Commercial</option>
                          <option value="transactional" {{ $page->search_intent === 'transactional' ? 'selected' : '' }}>Transactional</option>
                          <option value="informational" {{ $page->search_intent === 'informational' ? 'selected' : '' }}>Informational</option>
                        </select>
                      </div>
                    </div>

                    <!-- Before / After Transformation Media Card -->
                    <div class="card card-outline card-secondary shadow-sm mb-3">
                      <div class="card-header bg-light d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold m-0"><i class="bi bi-arrow-left-right text-primary me-1"></i> Before / After Hero Slider</h6>
                        <span class="badge bg-mint text-dark">Interactive</span>
                      </div>
                      <div class="card-body p-3">
                        <p class="text-muted small mb-3">Upload two images to display an interactive Before / After slider in the hero section.</p>

                        <!-- 1. Before Image (Raw Snapshot) -->
                        <div class="p-2 bg-light rounded-3 border mb-3">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold m-0 text-dark">
                              <i class="bi bi-camera me-1"></i> Before Image (Raw)
                            </label>
                            <input type="text" name="before_label" class="form-control form-control-sm w-50" placeholder="Raw Snapshot" value="{{ old('before_label', $page->before_label ?: 'Raw Snapshot') }}" title="Badge Label">
                          </div>

                          @if (!empty($page->before_image))
                            <div class="mb-2 text-center p-2 bg-white rounded border">
                              <img src="{{ $page->before_image_url }}" alt="Before Snapshot" class="img-fluid rounded border shadow-xs mb-1" style="max-height: 110px; object-fit: cover;">
                              <div class="form-check d-flex justify-content-center align-items-center gap-1">
                                <input class="form-check-input" type="checkbox" name="remove_before_image" value="1" id="removeBeforeCheck">
                                <label class="form-check-label text-danger small" for="removeBeforeCheck">Remove before image</label>
                              </div>
                            </div>
                          @endif
                          <input type="file" name="before_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg">
                          <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Flat-lay, smartphone snapshot, or unedited packshot.</small>
                        </div>

                        <!-- 2. After Image (Campaign Ready) -->
                        <div class="p-2 bg-light rounded-3 border">
                          <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold m-0 text-dark">
                              <i class="bi bi-stars text-mint me-1"></i> After Image (Hero)
                            </label>
                            <input type="text" name="after_label" class="form-control form-control-sm w-50" placeholder="Campaign Ready" value="{{ old('after_label', $page->after_label ?: 'Campaign Ready') }}" title="Badge Label">
                          </div>

                          @php
                            $currentAfter = $page->after_image ?: $page->featured_image;
                          @endphp
                          @if (!empty($currentAfter))
                            <div class="mb-2 text-center p-2 bg-white rounded border">
                              <img src="{{ $page->after_image_url }}" alt="Campaign Ready" class="img-fluid rounded border shadow-xs mb-1" style="max-height: 110px; object-fit: cover;">
                              <div class="form-check d-flex justify-content-center align-items-center gap-1">
                                <input class="form-check-input" type="checkbox" name="remove_after_image" value="1" id="removeAfterCheck">
                                <label class="form-check-label text-danger small" for="removeAfterCheck">Remove after image</label>
                              </div>
                            </div>
                          @endif
                          <input type="file" name="after_image" class="form-control custom-file" accept="image/jpeg,image/png,image/webp,image/jpg">
                          <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">AI-generated commercial lifestyle or on-model visual.</small>
                        </div>
                      </div>
                    </div>

                    <div class="d-grid mt-4">
                      <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Save Changes
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- TAB 2: Section Builder -->
            <div class="tab-pane fade" id="tab-sections" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-layers me-1"></i> Page Sections Sequence</h5>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                  <i class="bi bi-plus-lg me-1"></i> Add Section
                </button>
              </div>

              <div class="list-group" id="sortableSections">
                @forelse ($page->sections as $sec)
                  <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 rounded-3 border mb-2 shadow-sm bg-white" data-id="{{ $sec->id }}">
                    <div class="d-flex align-items-center gap-3">
                      <span class="text-muted cursor-move"><i class="bi bi-grip-vertical fs-5"></i></span>
                      <span class="badge bg-secondary text-uppercase">{{ $sec->type }}</span>
                      <div>
                        <div class="fw-bold text-dark">{{ $sec->heading ?: 'Untitled Section' }}</div>
                        <div class="small text-muted">{{ $sec->subheading ?: 'No subheading' }}</div>
                      </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                      <span class="badge {{ $sec->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $sec->is_active ? 'Active' : 'Hidden' }}
                      </span>
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSectionModal-{{ $sec->id }}">
                        <i class="bi bi-pencil"></i> Edit
                      </button>
                      <form action="{{ url('panel/admin/page-manager/' . $page->id . '/section/delete/' . $sec->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-sm btn-outline-danger actionDelete" title="Remove Section" data-confirm-title="Remove this section?" data-confirm-text="This section will be removed from this page.">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </div>

                  <!-- Edit Section Modal -->
                  <div class="modal fade" id="editSectionModal-{{ $sec->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <form action="{{ url('panel/admin/page-manager/' . $page->id . '/section/save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="section_id" value="{{ $sec->id }}">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit {{ ucfirst(str_replace('_', ' ', $sec->type)) }} Section</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row g-3">
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Section Type</label>
                                <select name="type" class="form-select" required>
                                  <option value="hero" {{ $sec->type === 'hero' ? 'selected' : '' }}>Hero Block</option>
                                  <option value="problem_solution" {{ $sec->type === 'problem_solution' ? 'selected' : '' }}>Why Choose / Value Cards</option>
                                  <option value="role_grid" {{ $sec->type === 'role_grid' ? 'selected' : '' }}>Built for Every Role Cards</option>
                                  <option value="alternating_rows" {{ $sec->type === 'alternating_rows' ? 'selected' : '' }}>Alternating Feature Rows</option>
                                  <option value="specs_table" {{ $sec->type === 'specs_table' ? 'selected' : '' }}>Compliance & Specs Table</option>
                                  <option value="metrics_counter" {{ $sec->type === 'metrics_counter' ? 'selected' : '' }}>Results & Stats Counter</option>
                                  <option value="prompt_grid" {{ $sec->type === 'prompt_grid' ? 'selected' : '' }}>Prompts Showcase Gallery</option>
                                  <option value="testimonials" {{ $sec->type === 'testimonials' ? 'selected' : '' }}>Client Testimonials Carousel</option>
                                  <option value="faq_accordion" {{ $sec->type === 'faq_accordion' ? 'selected' : '' }}>FAQ Accordion</option>
                                  <option value="cta_banner" {{ $sec->type === 'cta_banner' ? 'selected' : '' }}>Bottom CTA Banner</option>
                                  <option value="rich_text" {{ $sec->type === 'rich_text' ? 'selected' : '' }}>Standard Rich Text Card</option>
                                </select>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-bold">Eyebrow Badge</label>
                                <input type="text" name="badge" class="form-control" value="{{ $sec->badge }}">
                              </div>
                              <div class="col-12">
                                <label class="form-label fw-bold">Heading</label>
                                <input type="text" name="heading" class="form-control" value="{{ $sec->heading }}">
                              </div>
                              <div class="col-12">
                                <label class="form-label fw-bold">Subheading</label>
                                <textarea name="subheading" rows="3" class="form-control" placeholder="Explanatory subtitle or descriptive paragraph">{{ $sec->subheading }}</textarea>
                              </div>
                              <div class="col-12">
                                <label class="form-label fw-bold">Rich Text / Custom HTML Content</label>
                                <textarea name="content" rows="4" class="form-control">{{ $sec->content }}</textarea>
                              </div>

                              {{-- Visual Card / Step Media Manager for Alternating Rows & Card Sections --}}
                              @php
                                $isCardSection = in_array($sec->type, ['prompt_grid', 'problem_solution', 'role_grid', 'alternating_rows']) || (!empty($sec->cards) && count($sec->cards) > 0);
                                $sectionCards = $sec->cards ?? [];
                              @endphp

                              @if ($isCardSection)
                                <div class="col-12">
                                  <div class="card card-outline card-secondary border mb-2">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                      <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-grid-3x3-gap text-primary me-1"></i>
                                        @if ($sec->type === 'prompt_grid')
                                          Curated Prompt Showcase Cards
                                        @elseif ($sec->type === 'problem_solution')
                                          Value / Advantage Cards
                                        @elseif ($sec->type === 'role_grid')
                                          Role & Audience Cards
                                        @else
                                          Section Cards & Visual Media
                                        @endif
                                      </h6>
                                      <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-mint text-dark" id="cardCountBadge_{{ $sec->id }}">{{ count($sectionCards) }} Cards</span>
                                        <button type="button" class="btn btn-xs btn-outline-primary btn-add-card" data-sec-id="{{ $sec->id }}" data-sec-type="{{ $sec->type }}">
                                          <i class="bi bi-plus-lg me-1"></i>Add Card
                                        </button>
                                      </div>
                                    </div>
                                    <div class="card-body p-3">
                                      <p class="small text-muted mb-3">
                                        Edit titles, prompts/descriptions, badges, and upload custom images for each card. Edits automatically sync to the Structured JSON Data below.
                                      </p>
                                      
                                      <div class="row g-3 card-list-container" id="cardListContainer_{{ $sec->id }}">
                                        @forelse ($sectionCards as $cIndex => $card)
                                          <div class="col-md-6 card-item-wrapper" data-index="{{ $cIndex }}">
                                            <div class="p-3 border rounded-3 bg-white h-100 shadow-xs position-relative">
                                              <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="badge bg-dark text-white small fw-bold card-index-label">Card #{{ $cIndex + 1 }}</span>
                                                <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none btn-remove-card" data-sec-id="{{ $sec->id }}" data-index="{{ $cIndex }}" title="Remove Card">
                                                  <i class="bi bi-trash"></i> Remove
                                                </button>
                                              </div>

                                              <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1">Badge</label>
                                                <input type="text" name="cards[{{ $cIndex }}][badge]" class="form-control form-control-sm card-input-badge" data-sec-id="{{ $sec->id }}" data-index="{{ $cIndex }}" value="{{ $card['badge'] ?? '' }}" placeholder="e.g. Amazon Best Seller">
                                              </div>

                                              <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1">Title</label>
                                                <input type="text" name="cards[{{ $cIndex }}][title]" class="form-control form-control-sm card-input-title" data-sec-id="{{ $sec->id }}" data-index="{{ $cIndex }}" value="{{ $card['title'] ?? '' }}" placeholder="Card Title">
                                              </div>

                                              <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1">
                                                  {{ $sec->type === 'prompt_grid' ? 'Prompt Blueprint' : 'Description / Content' }}
                                                </label>
                                                <textarea name="cards[{{ $cIndex }}][prompt]" rows="3" class="form-control form-control-sm card-input-prompt" data-sec-id="{{ $sec->id }}" data-index="{{ $cIndex }}" placeholder="Enter prompt or description...">{{ $card['prompt'] ?? ($card['description'] ?? '') }}</textarea>
                                              </div>
                                              
                                              @if (!empty($card['image']))
                                                <div class="mb-2 text-center p-2 bg-light rounded border">
                                                  <img src="{{ $card['image'] }}" alt="Card Preview" class="img-fluid rounded" style="max-height: 90px; object-fit: cover;">
                                                  <div class="form-check mt-1 d-flex justify-content-center">
                                                    <input class="form-check-input me-1" type="checkbox" name="remove_card_image[{{ $cIndex }}]" value="1" id="removeCardImg_{{ $sec->id }}_{{ $cIndex }}">
                                                    <label class="form-check-label text-danger small" for="removeCardImg_{{ $sec->id }}_{{ $cIndex }}">Remove Image</label>
                                                  </div>
                                                </div>
                                              @endif
                                              
                                              <label class="form-label small fw-bold mb-1">
                                                <i class="bi bi-upload me-1"></i> Upload Image File
                                              </label>
                                              <input type="file" name="card_images[{{ $cIndex }}]" class="form-control form-control-sm custom-file mb-2" accept="image/jpeg,image/png,image/webp,image/jpg">
                                              
                                              <label class="form-label small text-muted mb-1">Or Image URL</label>
                                              <input type="text" name="card_image_urls[{{ $cIndex }}]" class="form-control form-control-sm card-input-image" data-sec-id="{{ $sec->id }}" data-index="{{ $cIndex }}" placeholder="https://... or /public/img/..." value="{{ $card['image'] ?? '' }}">
                                              <input type="hidden" name="card_image_urls_orig[{{ $cIndex }}]" value="{{ $card['image'] ?? '' }}">
                                            </div>
                                          </div>
                                        @empty
                                          <div class="col-12 text-center py-4 text-muted empty-cards-notice" id="emptyNotice_{{ $sec->id }}">
                                            <i class="bi bi-inbox display-6 d-block mb-2 text-muted"></i>
                                            <p class="mb-2">No structured cards defined yet for this section.</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-add-card" data-sec-id="{{ $sec->id }}" data-sec-type="{{ $sec->type }}">
                                              <i class="bi bi-plus-lg me-1"></i>Add First Card
                                            </button>
                                          </div>
                                        @endforelse
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              @endif

                              <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                  <label class="form-label fw-bold m-0">Structured JSON Data (Advanced)</label>
                                  <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success json-status-badge" id="jsonStatus_{{ $sec->id }}">
                                      <i class="bi bi-check-circle me-1"></i>Valid JSON
                                    </span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-format-json" data-sec-id="{{ $sec->id }}">
                                      <i class="bi bi-magic me-1"></i>Beautify JSON
                                    </button>
                                  </div>
                                </div>
                                <textarea name="settings_json" id="settingsJson_{{ $sec->id }}" rows="10" class="form-control font-monospace small settings-json-editor" style="font-size: 13px; line-height: 1.45; tab-size: 2;" placeholder='{"cards": [...]}'>{{ json_encode($sec->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                                <div class="form-text small text-muted">
                                  You can configure and customize any raw JSON attributes directly here. Changes to cards above automatically update this JSON, and direct edits here are saved to the database.
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="form-check form-switch">
                                  <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active-{{ $sec->id }}" {{ $sec->is_active ? 'checked' : '' }}>
                                  <label class="form-check-label fw-bold" for="active-{{ $sec->id }}">Section is visible on live page</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Section</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                @empty
                  <div class="text-center py-5 text-muted">
                    No sections added yet. Click "Add Section" above to start building the page.
                  </div>
                @endforelse
              </div>
            </div>

            <!-- TAB 3: Connected Prompts & Photoshoots -->
            <div class="tab-pane fade" id="tab-assets" role="tabpanel">
              <div class="row g-4">
                <div class="col-md-5">
                  <div class="card bg-light border p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-1"></i> Attach Prompt from Library</h6>
                    <form action="{{ url('panel/admin/page-manager/' . $page->id . '/items/attach') }}" method="POST">
                      @csrf
                      <input type="hidden" name="item_type" value="prompt">
                      <div class="mb-3">
                        <label class="form-label small fw-bold">Prompt ID</label>
                        <input type="number" name="item_id" class="form-control" placeholder="Enter Prompt ID from library" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-bold">Custom Badge (Optional)</label>
                        <input type="text" name="custom_badge" class="form-control" placeholder="e.g. Amazon Best Seller">
                      </div>
                      <button type="submit" class="btn btn-primary rounded-pill w-100">Attach Prompt</button>
                    </form>
                  </div>
                </div>

                <div class="col-md-7">
                  <h6 class="fw-bold mb-3">Attached Prompts Showcase ({{ $page->items->where('item_type', 'prompt')->count() }})</h6>
                  <div class="row g-3">
                    @forelse ($page->items->where('item_type', 'prompt') as $item)
                      @php
                        $promptPreviewUrl = $item->preview_image_url ?: ($item->prompt ? $item->prompt->preview_url : null);
                      @endphp
                      <div class="col-md-6">
                        <div class="card p-2 border shadow-sm">
                          @if ($promptPreviewUrl)
                            <img src="{{ $promptPreviewUrl }}" class="img-fluid rounded mb-2" style="max-height: 120px; width: 100%; object-fit: cover;" alt="{{ $item->prompt->title ?? 'Prompt preview' }}" loading="lazy">
                          @else
                            <div class="bg-light rounded mb-2 d-flex align-items-center justify-content-center text-muted" style="height: 90px;">
                              <i class="bi bi-image fs-3"></i>
                            </div>
                          @endif
                          <div class="small fw-bold text-truncate">{{ $item->prompt->title ?? ('Prompt #' . $item->item_id) }}</div>
                          @if ($item->custom_badge)
                            <span class="badge bg-mint text-dark w-auto mt-1">{{ $item->custom_badge }}</span>
                          @endif
                          <form action="{{ url('panel/admin/page-manager/' . $page->id . '/items/detach/' . $item->id) }}" method="POST" class="mt-2 text-end">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Detach</button>
                          </form>
                        </div>
                      </div>
                    @empty
                      <div class="col-12 text-muted">No prompts attached yet.</div>
                    @endforelse
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 4: SEO & Social Meta -->
            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
              <form action="{{ url('panel/admin/page-manager/update/' . $page->id) }}" method="POST" id="pageSeoForm">
                @csrf
                <input type="hidden" name="active_tab" value="tab-seo">

                <!-- Google SERP Live Snippet Preview -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                  <div class="small text-muted fw-bold mb-2">GOOGLE SEARCH RESULT PREVIEW</div>
                  <div class="p-3 bg-white rounded border">
                    <div class="small text-success text-truncate mb-1">{{ $page->public_url }}</div>
                    <h5 class="text-primary mb-1 text-truncate" id="serp-title">{{ $page->meta_title ?: $page->title }}</h5>
                    <p class="small text-muted mb-0" id="serp-desc">{{ $page->meta_description ?: ($page->excerpt ?: 'No description configured yet.') }}</p>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label fw-bold">Meta Title</label>
                    <input type="text" name="meta_title" id="input_meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-bold">Meta Description</label>
                    <textarea name="meta_description" id="input_meta_desc" rows="3" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-bold">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Primary Focus Keyword</label>
                    <input type="text" name="primary_keyword" class="form-control" placeholder="e.g. saree product photography" value="{{ old('primary_keyword', $page->primary_keyword) }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Schema.org Type</label>
                    <select name="schema_type" class="form-select">
                      <option value="Service" {{ old('schema_type', $page->schema_type) === 'Service' ? 'selected' : '' }}>Service</option>
                      <option value="Product" {{ old('schema_type', $page->schema_type) === 'Product' ? 'selected' : '' }}>Product</option>
                      <option value="WebPage" {{ old('schema_type', $page->schema_type) === 'WebPage' ? 'selected' : '' }}>WebPage</option>
                      <option value="HowTo" {{ old('schema_type', $page->schema_type) === 'HowTo' ? 'selected' : '' }}>HowTo</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Canonical URL (Optional override)</label>
                    <input type="url" name="canonical_url" class="form-control" placeholder="{{ $page->public_url }}" value="{{ old('canonical_url', $page->canonical_url) }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold">Robots Tag</label>
                    <select name="robots" class="form-select">
                      <option value="index, follow" {{ $page->robots === 'index, follow' ? 'selected' : '' }}>index, follow</option>
                      <option value="noindex, follow" {{ $page->robots === 'noindex, follow' ? 'selected' : '' }}>noindex, follow</option>
                      <option value="noindex, nofollow" {{ $page->robots === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                    </select>
                  </div>

                  <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                      <i class="bi bi-save me-1"></i> Save SEO Settings
                    </button>
                  </div>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<!-- Modal: Add Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ url('panel/admin/page-manager/' . $page->id . '/section/save') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add Section to Page</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Section Type <span class="text-danger">*</span></label>
              <select name="type" class="form-select" required>
                <option value="hero">Hero Block (Header & Dual CTAs)</option>
                <option value="problem_solution">Why Choose / Value Cards Grid</option>
                <option value="role_grid">Built for Every Role Grid</option>
                <option value="alternating_rows">Alternating Feature Rows (4 Zig-Zag Blocks)</option>
                <option value="specs_table">Compliance & Technical Specs Table</option>
                <option value="metrics_counter">Results That Matter (Stats Counter)</option>
                <option value="prompt_grid">Curated Live Prompts Showcase</option>
                <option value="testimonials">Client Testimonials Carousel</option>
                <option value="faq_accordion">FAQ Accordion (with FAQPage Schema)</option>
                <option value="cta_banner">High-Impact Bottom CTA Banner</option>
                <option value="rich_text">Standard Rich Text Content</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Eyebrow Badge</label>
              <input type="text" name="badge" class="form-control" placeholder="e.g. COMPLIANCE READY">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Heading</label>
              <input type="text" name="heading" class="form-control" placeholder="Section title">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Subheading</label>
              <textarea name="subheading" rows="3" class="form-control" placeholder="Optional explanatory subtitle or descriptive paragraph"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Content (Optional)</label>
              <textarea name="content" rows="4" class="form-control" placeholder="Rich text or HTML content..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add Section</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('javascript')
<script>
(function() {
  // Live JSON validation helper
  function validateJsonTextarea(textarea) {
    if (!textarea) return true;
    var secId = textarea.id.replace('settingsJson_', '');
    var statusBadge = document.getElementById('jsonStatus_' + secId);
    var val = textarea.value.trim();

    if (!val) {
      if (statusBadge) {
        statusBadge.className = 'badge bg-secondary json-status-badge';
        statusBadge.innerHTML = '<i class="bi bi-dash-circle me-1"></i>Empty';
      }
      textarea.classList.remove('is-invalid');
      return true;
    }

    try {
      // Normalize curly quotes and trailing commas
      var cleaned = val.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
      cleaned = cleaned.replace(/,\s*([\]}])/g, '$1');
      JSON.parse(cleaned);

      if (statusBadge) {
        statusBadge.className = 'badge bg-success json-status-badge';
        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Valid JSON';
      }
      textarea.classList.remove('is-invalid');
      return true;
    } catch (e) {
      if (statusBadge) {
        statusBadge.className = 'badge bg-danger json-status-badge';
        statusBadge.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Syntax Error';
        statusBadge.title = e.message;
      }
      textarea.classList.add('is-invalid');
      return false;
    }
  }

  // Parse JSON helper with fault-tolerance
  function parseSettingsJson(secId) {
    var textarea = document.getElementById('settingsJson_' + secId);
    if (!textarea) return { cards: [] };
    var val = textarea.value.trim();
    if (!val) return { cards: [] };

    try {
      var cleaned = val.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
      cleaned = cleaned.replace(/,\s*([\]}])/g, '$1');
      var parsed = JSON.parse(cleaned);
      if (Array.isArray(parsed)) {
        return { cards: parsed };
      }
      if (!parsed.cards || !Array.isArray(parsed.cards)) {
        parsed.cards = [];
      }
      return parsed;
    } catch (e) {
      return null; // Signals invalid JSON
    }
  }

  // Write JSON back to textarea and validate
  function writeSettingsJson(secId, obj) {
    var textarea = document.getElementById('settingsJson_' + secId);
    if (!textarea) return;
    textarea.value = JSON.stringify(obj, null, 2);
    validateJsonTextarea(textarea);
  }

  // Bind live validation to all settings JSON textareas
  $(document).on('input paste change', '.settings-json-editor', function() {
    validateJsonTextarea(this);
  });

  // Modal shown: validate immediately
  $('.modal').on('shown.bs.modal', function() {
    var textarea = this.querySelector('.settings-json-editor');
    if (textarea) {
      validateJsonTextarea(textarea);
    }
  });

  // Beautify JSON button
  $(document).on('click', '.btn-format-json', function() {
    var secId = $(this).data('sec-id');
    var textarea = document.getElementById('settingsJson_' + secId);
    if (!textarea) return;
    var val = textarea.value.trim();
    if (!val) return;

    try {
      var cleaned = val.replace(/[\u201C\u201D]/g, '"').replace(/[\u2018\u2019]/g, "'");
      cleaned = cleaned.replace(/,\s*([\]}])/g, '$1');
      var parsed = JSON.parse(cleaned);
      textarea.value = JSON.stringify(parsed, null, 2);
      validateJsonTextarea(textarea);
    } catch (e) {
      alert('Unable to format invalid JSON: ' + e.message);
    }
  });

  // Sync visual card inputs directly into Structured JSON
  $(document).on('input change', '.card-input-badge, .card-input-title, .card-input-prompt, .card-input-image', function() {
    var secId = $(this).data('sec-id');
    var index = parseInt($(this).data('index'), 10);
    var obj = parseSettingsJson(secId);
    if (!obj) return; // Do not overwrite if user has invalid manual JSON syntax

    while (obj.cards.length <= index) {
      obj.cards.push({});
    }

    var wrapper = $(this).closest('.card-item-wrapper');
    var badge = wrapper.find('.card-input-badge').val() || '';
    var title = wrapper.find('.card-input-title').val() || '';
    var prompt = wrapper.find('.card-input-prompt').val() || '';
    var image = wrapper.find('.card-input-image').val() || '';

    obj.cards[index].badge = badge;
    obj.cards[index].title = title;
    obj.cards[index].prompt = prompt;
    if (obj.cards[index].description !== undefined || !obj.cards[index].prompt) {
      obj.cards[index].description = prompt;
    }
    obj.cards[index].image = image;

    writeSettingsJson(secId, obj);
  });

  // Add Card dynamically
  $(document).on('click', '.btn-add-card', function() {
    var secId = $(this).data('sec-id');
    var secType = $(this).data('sec-type') || 'prompt_grid';
    var container = document.getElementById('cardListContainer_' + secId);
    var emptyNotice = document.getElementById('emptyNotice_' + secId);
    var countBadge = document.getElementById('cardCountBadge_' + secId);

    var obj = parseSettingsJson(secId);
    if (!obj) {
      alert('Please fix JSON syntax errors in the Structured JSON box below before adding new cards.');
      return;
    }

    var newIndex = obj.cards.length;
    var defaultTitle = secType === 'prompt_grid' ? 'Commercial Studio Concept' : 'Key Advantage ' + (newIndex + 1);
    var defaultBadge = secType === 'prompt_grid' ? 'Amazon Listing' : 'FEATURE';
    var defaultPrompt = secType === 'prompt_grid' 
      ? 'Commercial product photography blueprint with balanced studio lighting, crisp highlights, and marketplace-compliant aesthetic.' 
      : 'Highlight high-impact product performance or visual capability.';

    var newCard = {
      badge: defaultBadge,
      title: defaultTitle,
      prompt: defaultPrompt,
      description: defaultPrompt,
      image: '',
      aspect_ratio: '1 / 1'
    };

    obj.cards.push(newCard);
    writeSettingsJson(secId, obj);

    if (emptyNotice) {
      emptyNotice.style.display = 'none';
    }

    if (countBadge) {
      countBadge.textContent = obj.cards.length + ' Cards';
    }

    // Append card HTML
    var cardHtml = '<div class="col-md-6 card-item-wrapper" data-index="' + newIndex + '">' +
      '<div class="p-3 border rounded-3 bg-white h-100 shadow-xs position-relative">' +
        '<div class="d-flex align-items-center justify-content-between mb-2">' +
          '<span class="badge bg-dark text-white small fw-bold card-index-label">Card #' + (newIndex + 1) + '</span>' +
          '<button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none btn-remove-card" data-sec-id="' + secId + '" data-index="' + newIndex + '" title="Remove Card">' +
            '<i class="bi bi-trash"></i> Remove' +
          '</button>' +
        '</div>' +
        '<div class="mb-2">' +
          '<label class="form-label small fw-bold mb-1">Badge</label>' +
          '<input type="text" name="cards[' + newIndex + '][badge]" class="form-control form-control-sm card-input-badge" data-sec-id="' + secId + '" data-index="' + newIndex + '" value="' + defaultBadge + '" placeholder="e.g. Amazon Best Seller">' +
        '</div>' +
        '<div class="mb-2">' +
          '<label class="form-label small fw-bold mb-1">Title</label>' +
          '<input type="text" name="cards[' + newIndex + '][title]" class="form-control form-control-sm card-input-title" data-sec-id="' + secId + '" data-index="' + newIndex + '" value="' + defaultTitle + '" placeholder="Card Title">' +
        '</div>' +
        '<div class="mb-2">' +
          '<label class="form-label small fw-bold mb-1">' + (secType === 'prompt_grid' ? 'Prompt Blueprint' : 'Description / Content') + '</label>' +
          '<textarea name="cards[' + newIndex + '][prompt]" rows="3" class="form-control form-control-sm card-input-prompt" data-sec-id="' + secId + '" data-index="' + newIndex + '" placeholder="Enter prompt or description...">' + defaultPrompt + '</textarea>' +
        '</div>' +
        '<label class="form-label small fw-bold mb-1"><i class="bi bi-upload me-1"></i> Upload Image File</label>' +
        '<input type="file" name="card_images[' + newIndex + ']" class="form-control form-control-sm custom-file mb-2" accept="image/jpeg,image/png,image/webp,image/jpg">' +
        '<label class="form-label small text-muted mb-1">Or Image URL</label>' +
        '<input type="text" name="card_image_urls[' + newIndex + ']" class="form-control form-control-sm card-input-image" data-sec-id="' + secId + '" data-index="' + newIndex + '" placeholder="https://... or /public/img/..." value="">' +
        '<input type="hidden" name="card_image_urls_orig[' + newIndex + ']" value="">' +
      '</div>' +
    '</div>';

    if (container) {
      container.insertAdjacentHTML('beforeend', cardHtml);
    }
  });

  // Remove Card dynamically
  $(document).on('click', '.btn-remove-card', function() {
    var secId = $(this).data('sec-id');
    var index = parseInt($(this).data('index'), 10);
    var wrapper = $(this).closest('.card-item-wrapper');
    var container = document.getElementById('cardListContainer_' + secId);
    var countBadge = document.getElementById('cardCountBadge_' + secId);
    var emptyNotice = document.getElementById('emptyNotice_' + secId);

    var obj = parseSettingsJson(secId);
    if (!obj) {
      alert('Please fix JSON syntax errors before removing cards.');
      return;
    }

    if (index >= 0 && index < obj.cards.length) {
      obj.cards.splice(index, 1);
      writeSettingsJson(secId, obj);
    }

    wrapper.remove();

    // Re-index remaining cards in the DOM
    if (container) {
      var remaining = container.querySelectorAll('.card-item-wrapper');
      remaining.forEach(function(el, newIdx) {
        el.setAttribute('data-index', newIdx);
        var label = el.querySelector('.card-index-label');
        if (label) label.textContent = 'Card #' + (newIdx + 1);

        var removeBtn = el.querySelector('.btn-remove-card');
        if (removeBtn) removeBtn.setAttribute('data-index', newIdx);

        el.querySelectorAll('[data-index]').forEach(function(inp) {
          inp.setAttribute('data-index', newIdx);
        });

        // Update form input names
        var badgeInp = el.querySelector('.card-input-badge');
        if (badgeInp) badgeInp.setAttribute('name', 'cards[' + newIdx + '][badge]');

        var titleInp = el.querySelector('.card-input-title');
        if (titleInp) titleInp.setAttribute('name', 'cards[' + newIdx + '][title]');

        var promptInp = el.querySelector('.card-input-prompt');
        if (promptInp) promptInp.setAttribute('name', 'cards[' + newIdx + '][prompt]');

        var fileInp = el.querySelector('input[type="file"]');
        if (fileInp) fileInp.setAttribute('name', 'card_images[' + newIdx + ']');

        var urlInp = el.querySelector('.card-input-image');
        if (urlInp) urlInp.setAttribute('name', 'card_image_urls[' + newIdx + ']');

        var origInp = el.querySelector('input[name^="card_image_urls_orig"]');
        if (origInp) origInp.setAttribute('name', 'card_image_urls_orig[' + newIdx + ']');
      });

      if (countBadge) {
        countBadge.textContent = remaining.length + ' Cards';
      }

      if (remaining.length === 0 && emptyNotice) {
        emptyNotice.style.display = 'block';
      }
    }
  });

  // Modal form submit guard: prevent saving with broken JSON
  $('form[action*="/section/save"]').on('submit', function(e) {
    var textarea = this.querySelector('.settings-json-editor');
    if (textarea) {
      var isValid = validateJsonTextarea(textarea);
      if (!isValid) {
        e.preventDefault();
        alert('Cannot save section: The Structured JSON Data contains syntax errors. Please correct the JSON or click "Beautify JSON" to check for errors.');
        textarea.focus();
        return false;
      }
    }
  });

  // Tab restoration via URL hash & tab tracking
  function initTabManager() {
    var hash = window.location.hash;
    if (hash) {
      var tabBtn = document.querySelector('#pageManagerTabs a[href="' + hash + '"]');
      if (tabBtn) {
        var tabInstance = bootstrap.Tab.getOrCreateInstance(tabBtn);
        tabInstance.show();
      }
    }

    $('#pageManagerTabs a[data-bs-toggle="pill"], #pageManagerTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
      var targetHash = $(e.target).attr('href');
      if (targetHash && history.replaceState) {
        history.replaceState(null, null, targetHash);
      }
    });
  }
  initTabManager();

  // Cross-tab input synchronization on submit to ensure multi-tab edits are never lost
  $('#pageGeneralForm, #pageSeoForm').on('submit', function() {
    var currentForm = this;
    var otherFormId = currentForm.id === 'pageGeneralForm' ? 'pageSeoForm' : 'pageGeneralForm';
    var otherForm = document.getElementById(otherFormId);
    if (!otherForm) return;

    var otherInputs = otherForm.querySelectorAll('input:not([type="file"]):not([type="submit"]):not([type="button"]), textarea, select');
    otherInputs.forEach(function(inp) {
      if (inp.name && !inp.name.startsWith('_') && inp.name !== 'active_tab') {
        if (!currentForm.querySelector('[name="' + inp.name + '"]')) {
          var hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = inp.name;
          hidden.value = inp.value;
          currentForm.appendChild(hidden);
        }
      }
    });
  });

  // Live Google SERP snippet preview synchronization
  $('#input_meta_title, input[name="title"]').on('input', function() {
    var val = $('#input_meta_title').val() || $('input[name="title"]').val();
    $('#serp-title').text(val || 'Title preview');
  });
  $('#input_meta_desc, textarea[name="excerpt"]').on('input', function() {
    var val = $('#input_meta_desc').val() || $('textarea[name="excerpt"]').val();
    $('#serp-desc').text(val || 'No description configured yet.');
  });
})();
</script>
@endsection
