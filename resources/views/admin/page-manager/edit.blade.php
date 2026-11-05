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
              <form action="{{ url('panel/admin/page-manager/update/' . $page->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                              @if (!empty($sec->cards) && count($sec->cards) > 0)
                                <div class="col-12">
                                  <div class="card card-outline card-secondary border mb-2">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                      <h6 class="fw-bold mb-0 text-dark">
                                        <i class="bi bi-images text-primary me-1"></i> Step Cards & Visual Media
                                      </h6>
                                      <span class="badge bg-mint text-dark">{{ count($sec->cards) }} Steps</span>
                                    </div>
                                    <div class="card-body p-3">
                                      <p class="small text-muted mb-3">Upload a custom illustration/screenshot for each step or enter an image URL. If left empty, a placeholder camera preview is displayed.</p>
                                      
                                      <div class="row g-3">
                                        @foreach ($sec->cards as $cIndex => $card)
                                          <div class="col-md-6">
                                            <div class="p-2 border rounded-3 bg-white h-100 shadow-xs">
                                              <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="badge bg-light text-dark border small fw-bold">{{ $card['badge'] ?? ('Step ' . ($cIndex + 1)) }}</span>
                                                <small class="text-truncate text-muted fw-semibold" style="max-width: 170px;" title="{{ $card['title'] ?? '' }}">
                                                  {{ $card['title'] ?? '' }}
                                                </small>
                                              </div>
                                              
                                              @if (!empty($card['image']))
                                                <div class="mb-2 text-center p-2 bg-light rounded border">
                                                  <img src="{{ $card['image'] }}" alt="Step Preview" class="img-fluid rounded" style="max-height: 90px; object-fit: cover;">
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
                                              
                                              <label class="form-label small text-muted mb-0">Or Image URL</label>
                                              <input type="text" name="card_image_urls[{{ $cIndex }}]" class="form-control form-control-sm" placeholder="https://... or /public/img/..." value="{{ $card['image'] ?? '' }}">
                                            </div>
                                          </div>
                                        @endforeach
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              @endif

                              <div class="col-12">
                                <label class="form-label fw-bold">Structured JSON Data (Advanced)</label>
                                <textarea name="settings_json" rows="3" class="form-control font-monospace small" placeholder='{"cards": [...]}'>{{ json_encode($sec->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                                <div class="form-text">Advanced: you can also configure raw JSON properties here.</div>
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
                      <div class="col-md-6">
                        <div class="card p-2 border shadow-sm">
                          @if ($item->prompt && $item->prompt->preview)
                            <img src="{{ url('public/uploads/preview', $item->prompt->preview) }}" class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover;">
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
              <form action="{{ url('panel/admin/page-manager/update/' . $page->id) }}" method="POST">
                @csrf
                <input type="hidden" name="title" value="{{ $page->title }}">
                <input type="hidden" name="slug" value="{{ $page->slug }}">
                <input type="hidden" name="page_type" value="{{ $page->page_type }}">
                <input type="hidden" name="route_prefix" value="{{ $page->route_prefix }}">
                <input type="hidden" name="user_id" value="{{ $page->user_id }}">
                <input type="hidden" name="status" value="{{ $page->status }}">
                <input type="hidden" name="search_intent" value="{{ $page->search_intent }}">

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
