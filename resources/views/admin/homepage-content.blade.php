@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Homepage Content Settings</span>
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

            <form method="POST" action="{{ url('panel/admin/homepage-content') }}" enctype="multipart/form-data" id="homepageContentForm">
                @csrf

                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-house-gear me-2 text-primary"></i> Homepage Content Management</h6>
                            <small class="text-muted">Manage all static copy, titles, descriptions, CTA buttons, and feature sections across the homepage.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Preview Homepage
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                <i class="bi bi-check2-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="homeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-hero-tab" data-bs-toggle="pill" data-bs-target="#tab-hero" type="button" role="tab">
                                    <i class="bi bi-display me-1"></i> 1. Hero & Search
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-diff-tab" data-bs-toggle="pill" data-bs-target="#tab-diff" type="button" role="tab">
                                    <i class="bi bi-layers me-1"></i> 2. Difference
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-photoshoots-tab" data-bs-toggle="pill" data-bs-target="#tab-photoshoots" type="button" role="tab">
                                    <i class="bi bi-camera me-1"></i> 3. Photoshoots
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-how-tab" data-bs-toggle="pill" data-bs-target="#tab-how" type="button" role="tab">
                                    <i class="bi bi-123 me-1"></i> 4. How It Works
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-who-tab" data-bs-toggle="pill" data-bs-target="#tab-who" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i> 5. Who Is It For
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-plans-tab" data-bs-toggle="pill" data-bs-target="#tab-plans" type="button" role="tab">
                                    <i class="bi bi-tags me-1"></i> 6. Free vs Pro
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-promo-tab" data-bs-toggle="pill" data-bs-target="#tab-promo" type="button" role="tab">
                                    <i class="bi bi-grid me-1"></i> 7. Product Concepts
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-trans-tab" data-bs-toggle="pill" data-bs-target="#tab-trans" type="button" role="tab">
                                    <i class="bi bi-sliders me-1"></i> 8. Transformation
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-faq-tab" data-bs-toggle="pill" data-bs-target="#tab-faq" type="button" role="tab">
                                    <i class="bi bi-question-circle me-1"></i> 9. Homepage FAQs
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="homeTabsContent">
                            
                            {{-- TAB 1: HERO & SEARCH --}}
                            <div class="tab-pane fade show active" id="tab-hero" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-display me-2 text-primary"></i> Hero Cover Section Copy</h6>
                                    
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Main Headline (H1)</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" class="form-control">
                                            <small class="text-muted">Main headline shown prominently across the hero cover background.</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Supporting Subtitle</label>
                                        <div class="col-sm-9">
                                            <textarea name="hero_subtitle" rows="3" class="form-control">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                                            <small class="text-muted">Supporting subtitle under the main headline.</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Search Input Placeholder</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="search_placeholder" value="{{ old('search_placeholder', $settings->search_placeholder) }}" class="form-control">
                                            <small class="text-muted">Placeholder text inside the homepage search bar.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 2: SHOW DIFFERENCE --}}
                            <div class="tab-pane fade" id="tab-diff" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-layers me-2 text-primary"></i> "What Makes ImagineBuddy Different?" Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="diff_enabled" value="1" id="diffEnabledSwitch" @checked($settings->diff_enabled)>
                                            <label class="form-check-label fw-semibold" for="diffEnabledSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Eyebrow Tag</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="diff_eyebrow" value="{{ old('diff_eyebrow', $settings->diff_eyebrow) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="diff_heading" value="{{ old('diff_heading', $settings->diff_heading) }}" class="form-control">
                                            <small class="text-muted">HTML tags like <code>&lt;span class="font-serif-italic"&gt;Adapted to&lt;/span&gt;</code> are supported.</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Description Paragraph 1</label>
                                        <div class="col-sm-9">
                                            <textarea name="diff_description_1" rows="2" class="form-control">{{ old('diff_description_1', $settings->diff_description_1) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Description Paragraph 2</label>
                                        <div class="col-sm-9">
                                            <textarea name="diff_description_2" rows="2" class="form-control">{{ old('diff_description_2', $settings->diff_description_2) }}</textarea>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card p-3 border h-100">
                                                <h6 class="fw-bold text-dark mb-2">Card 1: Generic Prompts</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Card Title</label>
                                                    <input type="text" name="diff_card_generic_title" value="{{ old('diff_card_generic_title', $settings->diff_card_generic_title) }}" class="form-control">
                                                </div>
                                                <label class="form-label small fw-semibold">Bullet Points (One per line)</label>
                                                <textarea name="diff_card_generic_items" rows="4" class="form-control">{{ old('diff_card_generic_items', is_array($settings->diff_card_generic_items) ? implode("\n", $settings->diff_card_generic_items) : '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card p-3 border h-100 bg-white">
                                                <h6 class="fw-bold text-mint mb-2">Card 2: Adaptive Prompts (Highlighted)</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Card Title</label>
                                                    <input type="text" name="diff_card_adaptive_title" value="{{ old('diff_card_adaptive_title', $settings->diff_card_adaptive_title) }}" class="form-control">
                                                </div>
                                                <label class="form-label small fw-semibold">Bullet Points (One per line)</label>
                                                <textarea name="diff_card_adaptive_items" rows="4" class="form-control">{{ old('diff_card_adaptive_items', is_array($settings->diff_card_adaptive_items) ? implode("\n", $settings->diff_card_adaptive_items) : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="diff_cta_text" value="{{ old('diff_cta_text', $settings->diff_cta_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="diff_cta_url" value="{{ old('diff_cta_url', $settings->diff_cta_url) }}" class="form-control" placeholder="e.g. latest or https://...">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Disclaimer Subtext</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="diff_disclaimer" value="{{ old('diff_disclaimer', $settings->diff_disclaimer) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 3: PHOTOSHOOTS HEADER --}}
                            <div class="tab-pane fade" id="tab-photoshoots" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-camera me-2 text-primary"></i> Latest Photoshoots Section Header</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="photoshoots_enabled" value="1" id="photoshootsEnabledSwitch" @checked($settings->photoshoots_enabled)>
                                            <label class="form-check-label fw-semibold" for="photoshootsEnabledSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 small">
                                        <i class="bi bi-info-circle me-1"></i> The individual photoshoot cards are automatically pulled from your Photoshoot catalog. Here you can customize the section's intro header and call-to-action button.
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="photoshoots_heading" value="{{ old('photoshoots_heading', $settings->photoshoots_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Description</label>
                                        <div class="col-sm-9">
                                            <textarea name="photoshoots_subheading" rows="3" class="form-control">{{ old('photoshoots_subheading', $settings->photoshoots_subheading) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="photoshoots_cta_text" value="{{ old('photoshoots_cta_text', $settings->photoshoots_cta_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="photoshoots_cta_url" value="{{ old('photoshoots_cta_url', $settings->photoshoots_cta_url) }}" class="form-control" placeholder="photoshoots">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 4: HOW IT WORKS --}}
                            <div class="tab-pane fade" id="tab-how" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-123 me-2 text-primary"></i> "How It Works" Section (3 Steps)</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="how_it_works_enabled" value="1" id="howItWorksSwitch" @checked($settings->how_it_works_enabled)>
                                            <label class="form-check-label fw-semibold" for="howItWorksSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Eyebrow Tag</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="how_it_works_eyebrow" value="{{ old('how_it_works_eyebrow', $settings->how_it_works_eyebrow) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="how_it_works_heading" value="{{ old('how_it_works_heading', $settings->how_it_works_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-dark mt-4 mb-3">Workflow Steps</h6>
                                    @php
                                        $steps = old('how_it_works_steps', $settings->how_it_works_steps ?: []);
                                    @endphp
                                    <div class="row g-3 mb-4">
                                        @for ($i = 0; $i < 3; $i++)
                                        @php
                                            $st = $steps[$i] ?? ['badge' => 'STEP ' . ($i+1), 'icon' => 'bi-stars', 'title' => '', 'desc' => ''];
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="card p-3 h-100 border bg-white">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Step Badge</label>
                                                    <input type="text" name="how_it_works_steps[{{ $i }}][badge]" value="{{ $st['badge'] ?? '' }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Icon Class</label>
                                                    <input type="text" name="how_it_works_steps[{{ $i }}][icon]" value="{{ $st['icon'] ?? 'bi-stars' }}" class="form-control form-control-sm" placeholder="bi-upload">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Step Title</label>
                                                    <input type="text" name="how_it_works_steps[{{ $i }}][title]" value="{{ $st['title'] ?? '' }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Step Description</label>
                                                    <textarea name="how_it_works_steps[{{ $i }}][desc]" rows="3" class="form-control form-control-sm">{{ $st['desc'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="how_it_works_cta_text" value="{{ old('how_it_works_cta_text', $settings->how_it_works_cta_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="how_it_works_cta_url" value="{{ old('how_it_works_cta_url', $settings->how_it_works_cta_url) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Footnote / Subtext</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="how_it_works_subtext" value="{{ old('how_it_works_subtext', $settings->how_it_works_subtext) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 5: WHO IS IT FOR --}}
                            <div class="tab-pane fade" id="tab-who" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-people me-2 text-primary"></i> "Who Is ImagineBuddy For" Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="who_is_it_for_enabled" value="1" id="whoIsItForSwitch" @checked($settings->who_is_it_for_enabled)>
                                            <label class="form-check-label fw-semibold" for="whoIsItForSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="who_is_it_for_heading" value="{{ old('who_is_it_for_heading', $settings->who_is_it_for_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Subtitle</label>
                                        <div class="col-sm-9">
                                            <textarea name="who_is_it_for_subheading" rows="2" class="form-control">{{ old('who_is_it_for_subheading', $settings->who_is_it_for_subheading) }}</textarea>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-dark mt-4 mb-3">Target Personas (6 Items)</h6>
                                    @php
                                        $personas = old('who_is_it_for_items', $settings->who_is_it_for_items ?: []);
                                    @endphp
                                    <div class="row g-3 mb-4">
                                        @for ($i = 0; $i < 6; $i++)
                                        @php
                                            $p = $personas[$i] ?? ['title' => '', 'desc' => ''];
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="card p-3 h-100 border bg-white">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Persona {{ $i + 1 }} Title</label>
                                                    <input type="text" name="who_is_it_for_items[{{ $i }}][title]" value="{{ $p['title'] ?? '' }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Description</label>
                                                    <textarea name="who_is_it_for_items[{{ $i }}][desc]" rows="2" class="form-control form-control-sm">{{ $p['desc'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="who_is_it_for_cta_text" value="{{ old('who_is_it_for_cta_text', $settings->who_is_it_for_cta_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="who_is_it_for_cta_url" value="{{ old('who_is_it_for_cta_url', $settings->who_is_it_for_cta_url) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 6: FREE VS PRO --}}
                            <div class="tab-pane fade" id="tab-plans" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-tags me-2 text-primary"></i> "Free Prompts vs Pro Access" Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="free_vs_pro_enabled" value="1" id="freeVsProSwitch" @checked($settings->free_vs_pro_enabled)>
                                            <label class="form-check-label fw-semibold" for="freeVsProSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Eyebrow Tag</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="free_pro_eyebrow" value="{{ old('free_pro_eyebrow', $settings->free_pro_eyebrow) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="free_pro_heading" value="{{ old('free_pro_heading', $settings->free_pro_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Subtitle</label>
                                        <div class="col-sm-9">
                                            <textarea name="free_pro_subheading" rows="2" class="form-control">{{ old('free_pro_subheading', $settings->free_pro_subheading) }}</textarea>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card p-3 border h-100 bg-white">
                                                <h6 class="fw-bold text-dark mb-2">Card 1: Free Tier</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Card Title</label>
                                                    <input type="text" name="free_card_title" value="{{ old('free_card_title', $settings->free_card_title) }}" class="form-control">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Description</label>
                                                    <textarea name="free_card_desc" rows="3" class="form-control">{{ old('free_card_desc', $settings->free_card_desc) }}</textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Button Text</label>
                                                    <input type="text" name="free_card_cta_text" value="{{ old('free_card_cta_text', $settings->free_card_cta_text) }}" class="form-control">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Button URL</label>
                                                    <input type="text" name="free_card_cta_url" value="{{ old('free_card_cta_url', $settings->free_card_cta_url) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card p-3 border h-100 bg-white">
                                                <h6 class="fw-bold text-mint mb-2">Card 2: Pro Tier</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Card Title</label>
                                                    <input type="text" name="pro_card_title" value="{{ old('pro_card_title', $settings->pro_card_title) }}" class="form-control">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Description</label>
                                                    <textarea name="pro_card_desc" rows="3" class="form-control">{{ old('pro_card_desc', $settings->pro_card_desc) }}</textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Button Text</label>
                                                    <input type="text" name="pro_card_cta_text" value="{{ old('pro_card_cta_text', $settings->pro_card_cta_text) }}" class="form-control">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Button URL</label>
                                                    <input type="text" name="pro_card_cta_url" value="{{ old('pro_card_cta_url', $settings->pro_card_cta_url) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Footnote</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="free_pro_footnote" value="{{ old('free_pro_footnote', $settings->free_pro_footnote) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 7: PRODUCT CONCEPTS / PROMO --}}
                            <div class="tab-pane fade" id="tab-promo" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-grid me-2 text-primary"></i> "More Ways to Showcase Your Product" Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="promo_section_enabled" value="1" id="promoSectionSwitch" @checked($settings->promo_section_enabled)>
                                            <label class="form-check-label fw-semibold" for="promoSectionSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Eyebrow Tag</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="promo_eyebrow" value="{{ old('promo_eyebrow', $settings->promo_eyebrow) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Headline</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="promo_heading" value="{{ old('promo_heading', $settings->promo_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Description Lead</label>
                                        <div class="col-sm-9">
                                            <textarea name="promo_subheading" rows="2" class="form-control">{{ old('promo_subheading', $settings->promo_subheading) }}</textarea>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-dark mt-4 mb-3">Photography Concept List (6 Items)</h6>
                                    @php
                                        $promoItems = old('promo_items', $settings->promo_items ?: []);
                                    @endphp
                                    <div class="row g-3 mb-4">
                                        @for ($i = 0; $i < 6; $i++)
                                        @php
                                            $it = $promoItems[$i] ?? ['title' => '', 'desc' => ''];
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="card p-3 h-100 border bg-white">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Item {{ $i + 1 }} Bold Title</label>
                                                    <input type="text" name="promo_items[{{ $i }}][title]" value="{{ $it['title'] ?? '' }}" class="form-control form-control-sm" placeholder="e.g. E-commerce Product Images:">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Description</label>
                                                    <input type="text" name="promo_items[{{ $i }}][desc]" value="{{ $it['desc'] ?? '' }}" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Advisory Note Callout</label>
                                        <div class="col-sm-9">
                                            <textarea name="promo_note" rows="2" class="form-control">{{ old('promo_note', $settings->promo_note) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="promo_cta_text" value="{{ old('promo_cta_text', $settings->promo_cta_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="promo_cta_url" value="{{ old('promo_cta_url', $settings->promo_cta_url) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 8: TRANSFORMATION BEFORE/AFTER --}}
                            <div class="tab-pane fade" id="tab-trans" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-sliders me-2 text-primary"></i> Transformation Showcase Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="transformation_enabled" value="1" id="transEnabledSwitch" @checked($settings->transformation_enabled)>
                                            <label class="form-check-label fw-semibold" for="transEnabledSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Eyebrow</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_eyebrow" value="{{ old('transformation_eyebrow', $settings->transformation_eyebrow) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Heading</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_heading" value="{{ old('transformation_heading', $settings->transformation_heading) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Subheading</label>
                                        <div class="col-sm-9">
                                            <textarea name="transformation_subheading" rows="2" class="form-control">{{ old('transformation_subheading', $settings->transformation_subheading) }}</textarea>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="fw-bold text-dark mb-3">Split Slider Images & Badges</h6>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card p-3 border bg-white h-100">
                                                <h6 class="fw-bold text-dark mb-2">Before Image (Raw Snapshot)</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Badge Label</label>
                                                    <input type="text" name="transformation_before_label" value="{{ old('transformation_before_label', $settings->transformation_before_label) }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Image Path or URL</label>
                                                    <input type="text" name="transformation_before_image" value="{{ old('transformation_before_image', $settings->transformation_before_image) }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Or Upload New Image</label>
                                                    <input type="file" name="transformation_before_image_file" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card p-3 border bg-white h-100">
                                                <h6 class="fw-bold text-mint mb-2">After Image (Campaign Ready)</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Badge Label</label>
                                                    <input type="text" name="transformation_after_label" value="{{ old('transformation_after_label', $settings->transformation_after_label) }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold">Image Path or URL</label>
                                                    <input type="text" name="transformation_after_image" value="{{ old('transformation_after_image', $settings->transformation_after_image) }}" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">Or Upload New Image</label>
                                                    <input type="file" name="transformation_after_image_file" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="fw-bold text-dark mb-3">Right Column Benefit & Value Matrix</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Category Eyebrow</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_right_badge" value="{{ old('transformation_right_badge', $settings->transformation_right_badge) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Benefit Headline</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_right_headline" value="{{ old('transformation_right_headline', $settings->transformation_right_headline) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Description</label>
                                        <div class="col-sm-9">
                                            <textarea name="transformation_right_description" rows="3" class="form-control">{{ old('transformation_right_description', $settings->transformation_right_description) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card p-3 border bg-white h-100">
                                                <h6 class="fw-bold text-danger mb-2">Traditional Shoot Bullets</h6>
                                                <textarea name="transformation_traditional_items" rows="4" class="form-control form-control-sm mb-2">{{ old('transformation_traditional_items', is_array($settings->transformation_traditional_items) ? implode("\n", $settings->transformation_traditional_items) : '') }}</textarea>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="small fw-semibold">Price INR</label>
                                                        <input type="text" name="transformation_traditional_price_inr" value="{{ old('transformation_traditional_price_inr', $settings->transformation_traditional_price_inr) }}" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small fw-semibold">Price USD</label>
                                                        <input type="text" name="transformation_traditional_price_usd" value="{{ old('transformation_traditional_price_usd', $settings->transformation_traditional_price_usd) }}" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card p-3 border bg-white h-100">
                                                <h6 class="fw-bold text-mint mb-2">Prompt Library Bullets</h6>
                                                <textarea name="transformation_library_items" rows="4" class="form-control form-control-sm mb-2">{{ old('transformation_library_items', is_array($settings->transformation_library_items) ? implode("\n", $settings->transformation_library_items) : '') }}</textarea>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="small fw-semibold">Price INR</label>
                                                        <input type="text" name="transformation_library_price_inr" value="{{ old('transformation_library_price_inr', $settings->transformation_library_price_inr) }}" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small fw-semibold">Price USD</label>
                                                        <input type="text" name="transformation_library_price_usd" value="{{ old('transformation_library_price_usd', $settings->transformation_library_price_usd) }}" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Card Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_cta_title" value="{{ old('transformation_cta_title', $settings->transformation_cta_title) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Card Subtitle</label>
                                        <div class="col-sm-9">
                                            <textarea name="transformation_cta_subtitle" rows="2" class="form-control">{{ old('transformation_cta_subtitle', $settings->transformation_cta_subtitle) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_cta_btn_text" value="{{ old('transformation_cta_btn_text', $settings->transformation_cta_btn_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">CTA Target URL</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="transformation_cta_btn_url" value="{{ old('transformation_cta_btn_url', $settings->transformation_cta_btn_url) }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 9: HOMEPAGE FAQS --}}
                            <div class="tab-pane fade" id="tab-faq" role="tabpanel">
                                <div class="border rounded-3 p-3 p-md-4 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-question-circle me-2 text-primary"></i> Homepage FAQs Section</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="faq_enabled" value="1" id="faqEnabledSwitch" @checked($settings->faq_enabled)>
                                            <label class="form-check-label fw-semibold" for="faqEnabledSwitch">Show Section</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Badge</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_badge" value="{{ old('faq_badge', $settings->faq_badge) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Section Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_title" value="{{ old('faq_title', $settings->faq_title) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Subtitle</label>
                                        <div class="col-sm-9">
                                            <textarea name="faq_subtitle" rows="2" class="form-control">{{ old('faq_subtitle', $settings->faq_subtitle) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Contact Note</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_contact_text" value="{{ old('faq_contact_text', $settings->faq_contact_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">Contact Link</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_contact_url" value="{{ old('faq_contact_url', $settings->faq_contact_url) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">View All Button Text</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_view_all_text" value="{{ old('faq_view_all_text', $settings->faq_view_all_text) }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label fw-semibold text-sm-end">View All Button Link</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="faq_view_all_url" value="{{ old('faq_view_all_url', $settings->faq_view_all_url) }}" class="form-control">
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark m-0">FAQ Items (Question & Answer)</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addFaqBtn">
                                            <i class="bi bi-plus-lg me-1"></i> Add FAQ Item
                                        </button>
                                    </div>

                                    <div id="faqItemsContainer">
                                        @php
                                            $faqs = old('faq_items', $settings->faq_items ?: []);
                                        @endphp
                                        @foreach ($faqs as $idx => $faq)
                                        <div class="card p-3 mb-3 border bg-white faq-item-card">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-secondary">FAQ #<span class="faq-number">{{ $idx + 1 }}</span></span>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-faq-btn" title="Remove FAQ">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold">Question</label>
                                                <input type="text" name="faq_items[{{ $idx }}][question]" value="{{ $faq['question'] ?? '' }}" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-semibold">Answer (HTML supported)</label>
                                                <textarea name="faq_items[{{ $idx }}][answer]" rows="3" class="form-control form-control-sm" required>{{ $faq['answer'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.tab-content -->
                    </div><!-- /.card-body -->

                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-shield-check me-1"></i> Changes take effect immediately on the homepage.</small>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Save Homepage Settings
                        </button>
                    </div>
                </div><!-- /.card -->
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('faqItemsContainer');
    var addBtn = document.getElementById('addFaqBtn');

    if (addBtn && container) {
        addBtn.addEventListener('click', function () {
            var count = container.querySelectorAll('.faq-item-card').length;
            var card = document.createElement('div');
            card.className = 'card p-3 mb-3 border bg-white faq-item-card';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-secondary">FAQ #<span class="faq-number">${count + 1}</span></span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-faq-btn" title="Remove FAQ">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Question</label>
                    <input type="text" name="faq_items[${count}][question]" value="" class="form-control form-control-sm" required>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Answer (HTML supported)</label>
                    <textarea name="faq_items[${count}][answer]" rows="3" class="form-control form-control-sm" required><p></p></textarea>
                </div>
            `;
            container.appendChild(card);
        });

        container.addEventListener('click', function (e) {
            if (e.target.closest('.remove-faq-btn')) {
                var card = e.target.closest('.faq-item-card');
                if (card) {
                    card.remove();
                    // Re-index
                    var cards = container.querySelectorAll('.faq-item-card');
                    cards.forEach(function (c, idx) {
                        c.querySelector('.faq-number').textContent = idx + 1;
                        var qInput = c.querySelector('input[name*="[question]"]');
                        var aInput = c.querySelector('textarea[name*="[answer]"]');
                        if (qInput) qInput.name = 'faq_items[' + idx + '][question]';
                        if (aInput) aInput.name = 'faq_items[' + idx + '][answer]';
                    });
                }
            }
        });
    }
});
</script>
@endsection
