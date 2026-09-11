@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/settings') }}">{{ __('admin.general_settings') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">SEO Settings</span>
</h5>

<div class="content">
    <div class="row">
        <div class="col-lg-12 mb-4">
            @if (session('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi-check2 me-1"></i> {{ session('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Static Pages Table Card -->
            <div class="card shadow-custom border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi-file-earmark-code me-2"></i> Static & Listing Pages SEO</h6>
                        <small class="text-muted">Manage title tags, meta descriptions, and indexing directives for key platform pages.</small>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $staticPages->count() }} Configured Pages</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 220px;">Page Name</th>
                                    <th style="width: 180px;">URL / Route</th>
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-end pe-3" style="width: 110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staticPages as $page)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">{{ $page->page_name }}</div>
                                        <code class="small text-muted">{{ $page->page_key }}</code>
                                    </td>
                                    <td>
                                        <a href="{{ url($page->path ?: '/') }}" target="_blank" class="text-decoration-none text-primary small d-inline-flex align-items-center">
                                            <span>{{ $page->path ?: '/' }}</span>
                                            <i class="bi-box-arrow-up-right ms-1" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($page->meta_title)
                                            <div class="text-truncate" style="max-width: 240px;" title="{{ $page->meta_title }}">
                                                {{ $page->meta_title }}
                                            </div>
                                            <small class="text-muted">{{ strlen($page->meta_title) }} chars</small>
                                        @else
                                            <span class="badge bg-light text-muted border">Using Default</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($page->meta_description)
                                            <div class="text-truncate" style="max-width: 320px;" title="{{ $page->meta_description }}">
                                                {{ $page->meta_description }}
                                            </div>
                                            <small class="text-muted">{{ strlen($page->meta_description) }} chars</small>
                                        @else
                                            <span class="badge bg-light text-muted border">Using Default</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($page->is_active)
                                            <span class="badge bg-success-soft text-success px-2 py-1"><i class="bi-check-circle me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-secondary-soft text-secondary px-2 py-1">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ url('panel/admin/settings/seo/edit', $page->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                            <i class="bi-pencil me-1"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dynamic Pattern Templates Card -->
            <div class="card shadow-custom border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi-magic me-2"></i> Programmatic Dynamic Patterns (Categories & Prompts)</h6>
                    <small class="text-muted">Global automated pattern formulas applied dynamically when an individual prompt or category has no custom metadata.</small>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ url('panel/admin/settings/seo/patterns') }}">
                        @csrf

                        <div class="alert alert-info border-0 rounded-3 mb-4">
                            <h6 class="alert-heading fw-bold mb-1"><i class="bi-info-circle me-1"></i> Available Dynamic Tokens</h6>
                            <p class="small mb-0">
                                You can use these tokens in your patterns:
                                <code>{title}</code> (item title),
                                <code>{category}</code> (category name),
                                <code>{tags}</code> (keywords/tags),
                                <code>{author}</code> (creator username),
                                <code>{site_name}</code> (website title),
                                <code>{year}</code> (current year).
                            </p>
                        </div>

                        <!-- Prompt Detail Template -->
                        <div class="border rounded-3 p-3 mb-4 bg-light">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi-images me-1 text-primary"></i> Prompt Detail Pages Pattern (<code>/prompt/{slug}</code>)
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Meta Title Pattern</label>
                                    <input type="text" name="prompt_title" class="form-control" value="{{ $promptTemplate ? $promptTemplate->meta_title : '{title} - AI Prompt & Image | {site_name}' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Meta Keywords Pattern</label>
                                    <input type="text" name="prompt_keywords" class="form-control" value="{{ $promptTemplate ? $promptTemplate->meta_keywords : '{tags}, {category}, AI prompt' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Meta Description Pattern</label>
                                    <textarea name="prompt_description" class="form-control" rows="2">{{ $promptTemplate ? $promptTemplate->meta_description : 'Copy the prompt for "{title}" in {category}. Tested prompt recipe with tags, creative parameters, and variation ideas on {site_name}.' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Category Pages Template -->
                        <div class="border rounded-3 p-3 mb-4 bg-light">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="bi-grid me-1 text-success"></i> Category Pages Pattern (<code>/category/{slug}</code>)
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Meta Title Pattern</label>
                                    <input type="text" name="category_title" class="form-control" value="{{ $categoryTemplate ? $categoryTemplate->meta_title : 'Best {category} AI Prompts & Images | {site_name}' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Meta Keywords Pattern</label>
                                    <input type="text" name="category_keywords" class="form-control" value="{{ $categoryTemplate ? $categoryTemplate->meta_keywords : '{category} prompts, {category} AI art, prompt engineering' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Meta Description Pattern</label>
                                    <textarea name="category_description" class="form-control" rows="2">{{ $categoryTemplate ? $categoryTemplate->meta_description : 'Discover and copy top {category} prompts. Download free and premium high-resolution AI art and prompt engineering inspiration on {site_name}.' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-dark px-4 rounded-pill">
                                <i class="bi-save me-1"></i> Save Dynamic Patterns
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.bg-success-soft { background-color: rgba(25, 135, 84, 0.12); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.12); }
.shadow-custom { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>
@endsection
