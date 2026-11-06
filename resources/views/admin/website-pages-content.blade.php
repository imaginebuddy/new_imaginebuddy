@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Website Pages Content Settings</span>
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
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-layout-text-window-reverse me-2 text-primary"></i> Page-Wise Static Content Management</h6>
                    <small class="text-muted">Manage all hardcoded headings, descriptions, subtitles, badges, and SEO metadata across multiple public website pages.</small>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="pageSettingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'ai_models') active @endif" id="tab-ai-models-tab" data-bs-toggle="pill" data-bs-target="#tab-ai-models" type="button" role="tab">
                                <i class="bi bi-cpu me-1"></i> 1. AI Models Listing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'ai_model_detail') active @endif" id="tab-ai-model-detail-tab" data-bs-toggle="pill" data-bs-target="#tab-ai-model-detail" type="button" role="tab">
                                <i class="bi bi-boxes me-1"></i> 2. AI Model Detail (/ai-model/{slug})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'photoshoots') active @endif" id="tab-photoshoots-tab" data-bs-toggle="pill" data-bs-target="#tab-photoshoots" type="button" role="tab">
                                <i class="bi bi-camera-reels me-1"></i> 3. Photoshoots (/photoshoots)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'prompts_free') active @endif" id="tab-prompts-free-tab" data-bs-toggle="pill" data-bs-target="#tab-prompts-free" type="button" role="tab">
                                <i class="bi bi-unlock me-1"></i> 4. Free Prompts
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'prompts_premium') active @endif" id="tab-prompts-premium-tab" data-bs-toggle="pill" data-bs-target="#tab-prompts-premium" type="button" role="tab">
                                <i class="bi bi-gem me-1"></i> 5. Premium Prompts
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'popular') active @endif" id="tab-popular-tab" data-bs-toggle="pill" data-bs-target="#tab-popular" type="button" role="tab">
                                <i class="bi bi-fire me-1"></i> 6. Popular
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'latest') active @endif" id="tab-latest-tab" data-bs-toggle="pill" data-bs-target="#tab-latest" type="button" role="tab">
                                <i class="bi bi-clock-history me-1"></i> 7. Latest
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'most_viewed') active @endif" id="tab-most-viewed-tab" data-bs-toggle="pill" data-bs-target="#tab-most-viewed" type="button" role="tab">
                                <i class="bi bi-eye me-1"></i> 8. Most Viewed
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'most_downloads') active @endif" id="tab-most-downloads-tab" data-bs-toggle="pill" data-bs-target="#tab-most-downloads" type="button" role="tab">
                                <i class="bi bi-download me-1"></i> 9. Most Downloads
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'most_copied') active @endif" id="tab-most-copied-tab" data-bs-toggle="pill" data-bs-target="#tab-most-copied" type="button" role="tab">
                                <i class="bi bi-clipboard-check me-1"></i> 10. Most Copied
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'pricing') active @endif" id="tab-pricing-tab" data-bs-toggle="pill" data-bs-target="#tab-pricing" type="button" role="tab">
                                <i class="bi bi-tag me-1"></i> 11. Pricing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'hire') active @endif" id="tab-hire-tab" data-bs-toggle="pill" data-bs-target="#tab-hire" type="button" role="tab">
                                <i class="bi bi-briefcase me-1"></i> 12. Hire Page
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'marketplace') active @endif" id="tab-marketplace-tab" data-bs-toggle="pill" data-bs-target="#tab-marketplace" type="button" role="tab">
                                <i class="bi bi-shop me-1"></i> 13. Marketplace
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'use_case' || $activeTab === 'use-case') active @endif" id="tab-use-case-tab" data-bs-toggle="pill" data-bs-target="#tab-use-case" type="button" role="tab">
                                <i class="bi bi-bullseye me-1"></i> 14. Use Case
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'industry') active @endif" id="tab-industry-tab" data-bs-toggle="pill" data-bs-target="#tab-industry" type="button" role="tab">
                                <i class="bi bi-building me-1"></i> 15. Industry
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'product') active @endif" id="tab-product-tab" data-bs-toggle="pill" data-bs-target="#tab-product" type="button" role="tab">
                                <i class="bi bi-box-seam me-1"></i> 16. Product
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'garment') active @endif" id="tab-garment-tab" data-bs-toggle="pill" data-bs-target="#tab-garment" type="button" role="tab">
                                <i class="bi bi-person-standing me-1"></i> 17. Garment
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($activeTab === 'platform_ai_ads' || $activeTab === 'platform-ai-ads') active @endif" id="tab-platform-ai-ads-tab" data-bs-toggle="pill" data-bs-target="#tab-platform-ai-ads" type="button" role="tab">
                                <i class="bi bi-badge-ad me-1"></i> 18. Platform AI Ads
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pageSettingsTabContent">
                        
                        {{-- ======================================================== --}}
                        {{-- 1. AI MODELS LISTING TAB (/ai-models) --}}
                        {{-- ======================================================== --}}
                        <div class="tab-pane fade @if($activeTab === 'ai_models') show active @endif" id="tab-ai-models" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">AI Models Listing Page Settings</h6>
                                    <small class="text-muted">Target live URL: <code>{{ url('ai-models') }}</code></small>
                                </div>
                                <a href="{{ url('ai-models') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <form method="POST" action="{{ url('panel/admin/website-pages-content/update/ai_models') }}">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Page Title (H1)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $pages['ai_models']->page_title) }}">
                                        <small class="text-muted d-block mt-1">Tip: e.g. <code>AI Models &lt;span style="color: #00d690;"&gt;Prompts&lt;/span&gt;</code></small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Subtitle</label>
                                    <div class="col-sm-10">
                                        <textarea name="subtitle" class="form-control" rows="2">{{ old('subtitle', $pages['ai_models']->subtitle) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Search Placeholder</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[search_placeholder]" class="form-control" value="{{ old('extra_data.search_placeholder', $pages['ai_models']->getExtra('search_placeholder')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $pages['ai_models']->seo_title) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $pages['ai_models']->meta_keywords) }}">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                                    <div class="col-sm-10">
                                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $pages['ai_models']->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check2-circle me-1"></i> Save AI Models Listing Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- 2. AI MODEL DETAIL TAB (/ai-model/{slug}) --}}
                        {{-- ======================================================== --}}
                        <div class="tab-pane fade @if($activeTab === 'ai_model_detail') show active @endif" id="tab-ai-model-detail" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">Individual AI Model Detail Pages (/ai-model/{slug})</h6>
                                    <small class="text-muted">Manage the H1 heading, hero lead subtitle, and SEO metadata independently for each individual AI Model.</small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>AI Model Name</th>
                                            <th>Live Slug</th>
                                            <th>Prompts Count</th>
                                            <th>Content Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allModelsList as $model)
                                        <tr>
                                            <td class="fw-semibold">
                                                {{ $model['name'] }}
                                            </td>
                                            <td>
                                                <code>/ai-model/{{ $model['slug'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ number_format($model['count']) }} prompts</span>
                                            </td>
                                            <td>
                                                @if($model['is_customized'])
                                                    <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check2"></i> Customized</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-muted border">Default Formula</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ url('ai-model', $model['slug']) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Preview live page">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                                <a href="{{ url('panel/admin/website-pages-content/ai-model', $model['slug']) }}" class="btn btn-sm btn-dark">
                                                    <i class="bi bi-pencil me-1"></i> Edit Content
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- 3. PHOTOSHOOTS TAB (/photoshoots) --}}
                        {{-- ======================================================== --}}
                        <div class="tab-pane fade @if($activeTab === 'photoshoots') show active @endif" id="tab-photoshoots" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">Photoshoots Listing Page Settings</h6>
                                    <small class="text-muted">Target live URL: <code>{{ url('photoshoots') }}</code></small>
                                </div>
                                <a href="{{ url('photoshoots') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <form method="POST" action="{{ url('panel/admin/website-pages-content/update/photoshoots') }}">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Page Title (H1)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $pages['photoshoots']->page_title) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Subtitle</label>
                                    <div class="col-sm-10">
                                        <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $pages['photoshoots']->subtitle) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Category Filter Label</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[all_categories_label]" class="form-control" value="{{ old('extra_data.all_categories_label', $pages['photoshoots']->getExtra('all_categories_label', 'All Categories')) }}">
                                        <small class="text-muted">First pill shown in the category filter bar.</small>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-1 text-primary"></i> Photoshoot Guide & Benefits Section (Topical Authority)</h6>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Guide Badge</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[guide_badge]" class="form-control" value="{{ old('extra_data.guide_badge', $pages['photoshoots']->getExtra('guide_badge')) }}">
                                        <small class="text-muted">e.g. <code>MULTI-ANGLE PROMPT CONSISTENCY</code></small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Guide Heading</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[guide_title]" class="form-control" value="{{ old('extra_data.guide_title', $pages['photoshoots']->getExtra('guide_title')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Paragraph 1</label>
                                    <div class="col-sm-10">
                                        <textarea name="extra_data[guide_desc_1]" class="form-control" rows="3">{{ old('extra_data.guide_desc_1', $pages['photoshoots']->getExtra('guide_desc_1')) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Paragraph 2</label>
                                    <div class="col-sm-10">
                                        <textarea name="extra_data[guide_desc_2]" class="form-control" rows="4">{{ old('extra_data.guide_desc_2', $pages['photoshoots']->getExtra('guide_desc_2')) }}</textarea>
                                        <small class="text-muted">HTML links allowed (e.g. category links and strong tags).</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Side Card Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[guide_card_title]" class="form-control" value="{{ old('extra_data.guide_card_title', $pages['photoshoots']->getExtra('guide_card_title')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Point 1 (Title / Desc)</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="extra_data[guide_bullet_1_title]" class="form-control" placeholder="Point 1 Title" value="{{ old('extra_data.guide_bullet_1_title', $pages['photoshoots']->getExtra('guide_bullet_1_title')) }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="extra_data[guide_bullet_1_desc]" class="form-control" placeholder="Point 1 Description" value="{{ old('extra_data.guide_bullet_1_desc', $pages['photoshoots']->getExtra('guide_bullet_1_desc')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Point 2 (Title / Desc)</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="extra_data[guide_bullet_2_title]" class="form-control" placeholder="Point 2 Title" value="{{ old('extra_data.guide_bullet_2_title', $pages['photoshoots']->getExtra('guide_bullet_2_title')) }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="extra_data[guide_bullet_2_desc]" class="form-control" placeholder="Point 2 Description" value="{{ old('extra_data.guide_bullet_2_desc', $pages['photoshoots']->getExtra('guide_bullet_2_desc')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Point 3 (Title / Desc)</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="extra_data[guide_bullet_3_title]" class="form-control" placeholder="Point 3 Title" value="{{ old('extra_data.guide_bullet_3_title', $pages['photoshoots']->getExtra('guide_bullet_3_title')) }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="extra_data[guide_bullet_3_desc]" class="form-control" placeholder="Point 3 Description" value="{{ old('extra_data.guide_bullet_3_desc', $pages['photoshoots']->getExtra('guide_bullet_3_desc')) }}">
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-search me-1 text-primary"></i> SEO Metadata</h6>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $pages['photoshoots']->seo_title) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $pages['photoshoots']->meta_keywords) }}">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                                    <div class="col-sm-10">
                                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $pages['photoshoots']->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check2-circle me-1"></i> Save Photoshoots Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- 4 to 10: EXPLORE FEEDS (Free, Premium, Popular, Latest, Most Viewed, Most Downloads, Most Copied) --}}
                        {{-- ======================================================== --}}
                        @php
                            $explorePages = [
                                'prompts_free' => ['name' => 'Free Prompts', 'url' => url('prompts/free')],
                                'prompts_premium' => ['name' => 'Premium Prompts', 'url' => url('prompts/premium')],
                                'popular' => ['name' => 'Popular Prompts', 'url' => url('popular')],
                                'latest' => ['name' => 'Latest Prompts', 'url' => url('latest')],
                                'most_viewed' => ['name' => 'Most Viewed Prompts', 'url' => url('most/viewed')],
                                'most_downloads' => ['name' => 'Most Downloaded Prompts', 'url' => url('most/downloads')],
                                'most_copied' => ['name' => 'Most Copied Prompts', 'url' => url('most/copied')],
                            ];
                        @endphp

                        @foreach($explorePages as $pageKey => $pageMeta)
                        <div class="tab-pane fade @if($activeTab === $pageKey) show active @endif" id="tab-{{ str_replace('_', '-', $pageKey) }}" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $pageMeta['name'] }} Page Settings</h6>
                                    <small class="text-muted">Target live URL: <code>{{ $pageMeta['url'] }}</code></small>
                                </div>
                                <a href="{{ $pageMeta['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <form method="POST" action="{{ url('panel/admin/website-pages-content/update', $pageKey) }}">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Page Title (H1)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $pages[$pageKey]->page_title) }}">
                                        <small class="text-muted d-block mt-1">Tip: e.g. <code>{{ $pageMeta['name'] }} &lt;span style="color: #00d690;"&gt;Prompts&lt;/span&gt;</code></small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Subtitle</label>
                                    <div class="col-sm-10">
                                        <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $pages[$pageKey]->subtitle) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $pages[$pageKey]->seo_title) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $pages[$pageKey]->meta_keywords) }}">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                                    <div class="col-sm-10">
                                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $pages[$pageKey]->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check2-circle me-1"></i> Save {{ $pageMeta['name'] }} Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach

                        {{-- ======================================================== --}}
                        {{-- 11. PRICING TAB (/pricing) --}}
                        {{-- ======================================================== --}}
                        <div class="tab-pane fade @if($activeTab === 'pricing') show active @endif" id="tab-pricing" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">Pricing Page Settings</h6>
                                    <small class="text-muted">Target live URL: <code>{{ url('pricing') }}</code></small>
                                </div>
                                <a href="{{ url('pricing') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <form method="POST" action="{{ url('panel/admin/website-pages-content/update/pricing') }}">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Hero Badge Text</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[hero_badge_text]" class="form-control" value="{{ old('extra_data.hero_badge_text', $pages['pricing']->getExtra('hero_badge_text')) }}">
                                        <small class="text-muted">Shown inside the star rating pill at the top of the pricing hero.</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Page Title (H1)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $pages['pricing']->page_title) }}">
                                        <small class="text-muted d-block mt-1">HTML allowed (e.g. <code>&lt;span class="text-color-default font-serif-italic"&gt;For Less Than a Cup of Coffee&lt;/span&gt;</code>).</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Subtitle</label>
                                    <div class="col-sm-10">
                                        <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $pages['pricing']->subtitle) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Save Badge Text</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[save_badge_text]" class="form-control" value="{{ old('extra_data.save_badge_text', $pages['pricing']->getExtra('save_badge_text', 'Save up to 40%')) }}">
                                        <small class="text-muted">Discount badge shown beside yearly billing toggle.</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Comparison Table Heading</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[table_heading]" class="form-control" value="{{ old('extra_data.table_heading', $pages['pricing']->getExtra('table_heading', 'Compare Plans & Feature Matrix')) }}">
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-megaphone me-1 text-primary"></i> Bottom CTA Banner</h6>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">CTA Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[cta_title]" class="form-control" value="{{ old('extra_data.cta_title', $pages['pricing']->getExtra('cta_title', 'Ready to Elevate Your Commercial Visuals?')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">CTA Subtitle</label>
                                    <div class="col-sm-10">
                                        <textarea name="extra_data[cta_subtitle]" class="form-control" rows="2">{{ old('extra_data.cta_subtitle', $pages['pricing']->getExtra('cta_subtitle', 'Join thousands of top e-commerce sellers, agencies, and creators.')) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">CTA Button Label</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[cta_btn_text]" class="form-control" value="{{ old('extra_data.cta_btn_text', $pages['pricing']->getExtra('cta_btn_text', 'Get Started Now')) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Guarantee Note</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="extra_data[cta_guarantee]" class="form-control" value="{{ old('extra_data.cta_guarantee', $pages['pricing']->getExtra('cta_guarantee', 'Cancel anytime with one click. No questions asked.')) }}">
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-search me-1 text-primary"></i> SEO Metadata</h6>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $pages['pricing']->seo_title) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $pages['pricing']->meta_keywords) }}">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                                    <div class="col-sm-10">
                                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $pages['pricing']->meta_description) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check2-circle me-1"></i> Save Pricing Page Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- 12. HIRE PAGE TAB (/hire) --}}
                        {{-- ======================================================== --}}
                        <div class="tab-pane fade @if($activeTab === 'hire') show active @endif" id="tab-hire" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1">Hire Service Content Settings</h6>
                                    <small class="text-muted">Target live URL: <code>{{ url('hire') }}</code></small>
                                </div>
                                <a href="{{ url('hire') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <div class="card bg-light border p-4 text-center my-3">
                                <div class="my-3">
                                    <i class="bi bi-briefcase text-primary fs-1"></i>
                                    <h5 class="fw-bold mt-3">Hire Service Dedicated Settings Hub</h5>
                                    <p class="text-muted mx-auto" style="max-width: 650px;">
                                        All static headings, badges, SKU tier pricing matrices (USD & INR), creative process steps, service introductions, and SEO metadata for the Hire page are fully managed in the dedicated Hire Settings panel.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ url('panel/admin/hire/settings') }}" class="btn btn-dark px-4 py-2 me-2">
                                            <i class="bi bi-gear-wide-connected me-1"></i> Open Hire Service Settings Panel
                                        </a>
                                        <a href="{{ url('panel/admin/hire/inquiries') }}" class="btn btn-outline-secondary px-4 py-2">
                                            <i class="bi bi-envelope-paper me-1"></i> View Inquiries CRM
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- 13 to 18: CMS HUBS CONTENT SETTINGS (Marketplace, Use Case, Industry, Product, Garment, Platform AI Ads) --}}
                        {{-- ======================================================== --}}
                        @php
                            $hubCmsPages = [
                                'marketplace' => [
                                    'title' => 'Marketplace Content Settings',
                                    'target_url' => url('marketplace'),
                                    'tab_id' => 'tab-marketplace',
                                    'icon' => 'bi-shop',
                                ],
                                'use_case' => [
                                    'title' => 'Use Case Content Settings',
                                    'target_url' => url('use-case'),
                                    'tab_id' => 'tab-use-case',
                                    'icon' => 'bi-bullseye',
                                ],
                                'industry' => [
                                    'title' => 'Industry Content Settings',
                                    'target_url' => url('industry'),
                                    'tab_id' => 'tab-industry',
                                    'icon' => 'bi-building',
                                ],
                                'product' => [
                                    'title' => 'Product Content Settings',
                                    'target_url' => url('product'),
                                    'tab_id' => 'tab-product',
                                    'icon' => 'bi-box-seam',
                                ],
                                'garment' => [
                                    'title' => 'Garment Content Settings',
                                    'target_url' => url('garment'),
                                    'tab_id' => 'tab-garment',
                                    'icon' => 'bi-person-standing',
                                ],
                                'platform_ai_ads' => [
                                    'title' => 'Platform AI Ads Content Settings',
                                    'target_url' => url('platform-ai-ads'),
                                    'tab_id' => 'tab-platform-ai-ads',
                                    'icon' => 'bi-badge-ad',
                                ],
                            ];
                        @endphp

                        @foreach ($hubCmsPages as $hubKey => $hubMeta)
                        <div class="tab-pane fade @if($activeTab === $hubKey || $activeTab === str_replace('_', '-', $hubKey)) show active @endif" id="{{ $hubMeta['tab_id'] }}" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="bi {{ $hubMeta['icon'] }} me-1 text-primary"></i> {{ $hubMeta['title'] }}</h6>
                                    <small class="text-muted">Target live URL: <code>{{ $hubMeta['target_url'] }}</code></small>
                                </div>
                                <a href="{{ $hubMeta['target_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
                                </a>
                            </div>

                            <form method="POST" action="{{ url('panel/admin/website-pages-content/update', $hubKey) }}">
                                @csrf

                                {{-- Section 1: Hero Section --}}
                                <div class="bg-light p-3 rounded mb-4 border">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-stars me-1 text-primary"></i> 1. Hero Section</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Eyebrow Badge</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[hero_badge]" class="form-control" value="{{ old('extra_data.hero_badge', $pages[$hubKey]->getExtra('hero_badge')) }}">
                                            <small class="text-muted">Small pill badge above the hero title (e.g. <code>COMPLIANCE GUARANTEED</code>).</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Hero Heading (H1)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $pages[$hubKey]->page_title) }}">
                                            <small class="text-muted">HTML tags allowed, e.g. <code>&lt;span class="cms-heading-gradient-mint"&gt;Highlighted Text&lt;/span&gt;</code></small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Hero Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $pages[$hubKey]->subtitle) }}</textarea>
                                            <small class="text-muted">Subheading paragraph directly below the main hero heading.</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Primary CTA Button</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-fonts"></i></span>
                                                <input type="text" name="extra_data[hero_cta_text]" class="form-control" placeholder="Button Label (e.g. Explore Solutions)" value="{{ old('extra_data.hero_cta_text', $pages[$hubKey]->getExtra('hero_cta_text', 'Explore Solutions')) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                                <input type="text" name="extra_data[hero_cta_url]" class="form-control" placeholder="Button URL / Anchor (e.g. #catalog)" value="{{ old('extra_data.hero_cta_url', $pages[$hubKey]->getExtra('hero_cta_url', '#catalog')) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 2: Catalog / Directory Grid --}}
                                <div class="bg-light p-3 rounded mb-4 border">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-grid-3x3-gap me-1 text-primary"></i> 2. Solutions Directory Grid Section</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Grid Badge</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[grid_badge]" class="form-control" value="{{ old('extra_data.grid_badge', $pages[$hubKey]->getExtra('grid_badge', 'CATALOG')) }}">
                                            <small class="text-muted">Small pill badge above the catalog heading (e.g. <code>CATALOG</code>).</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Grid Heading (H2)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[grid_heading]" class="form-control" value="{{ old('extra_data.grid_heading', $pages[$hubKey]->getExtra('grid_heading')) }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Grid Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="extra_data[grid_subheading]" class="form-control" rows="2">{{ old('extra_data.grid_subheading', $pages[$hubKey]->getExtra('grid_subheading')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 3: Social Proof / Client Logos --}}
                                <div class="bg-light p-3 rounded mb-4 border">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-patch-check me-1 text-primary"></i> 3. Client Logos & Social Proof Section</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Social Proof Badge</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[social_proof_badge]" class="form-control" value="{{ old('extra_data.social_proof_badge', $pages[$hubKey]->getExtra('social_proof_badge', 'TRUSTED BY 500+ D2C BRANDS & AGENCIES')) }}">
                                            <small class="text-muted">Badge text displayed directly above the client brand logos slider.</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 4: Bottom CTA Banner --}}
                                <div class="bg-light p-3 rounded mb-4 border">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-rocket-takeoff me-1 text-primary"></i> 4. Bottom Floating CTA Banner Section</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">CTA Eyebrow Badge</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[cta_badge]" class="form-control" value="{{ old('extra_data.cta_badge', $pages[$hubKey]->getExtra('cta_badge', 'GET STARTED')) }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">CTA Heading (H2)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="extra_data[cta_heading]" class="form-control" value="{{ old('extra_data.cta_heading', $pages[$hubKey]->getExtra('cta_heading', 'Scale Your Product Visuals with AI')) }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">CTA Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="extra_data[cta_subheading]" class="form-control" rows="2">{{ old('extra_data.cta_subheading', $pages[$hubKey]->getExtra('cta_subheading', 'Join thousands of brands producing studio-quality e-commerce and ad imagery without physical photoshoots.')) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Primary Button</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-fonts"></i></span>
                                                <input type="text" name="extra_data[cta_btn_text]" class="form-control" placeholder="Label (e.g. Create Free Account)" value="{{ old('extra_data.cta_btn_text', $pages[$hubKey]->getExtra('cta_btn_text', 'Create Free Account')) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                                <input type="text" name="extra_data[cta_btn_url]" class="form-control" placeholder="URL (e.g. https://... or register)" value="{{ old('extra_data.cta_btn_url', $pages[$hubKey]->getExtra('cta_btn_url', url('register'))) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Secondary Button</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-fonts"></i></span>
                                                <input type="text" name="extra_data[cta_secondary_btn_text]" class="form-control" placeholder="Label (e.g. View Pricing Plans)" value="{{ old('extra_data.cta_secondary_btn_text', $pages[$hubKey]->getExtra('cta_secondary_btn_text', 'View Pricing Plans')) }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                                <input type="text" name="extra_data[cta_secondary_btn_url]" class="form-control" placeholder="URL (e.g. https://... or pricing)" value="{{ old('extra_data.cta_secondary_btn_url', $pages[$hubKey]->getExtra('cta_secondary_btn_url', url('pricing'))) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 5: SEO & Social Metadata --}}
                                <div class="bg-light p-3 rounded mb-4 border">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-search me-1 text-primary"></i> 5. SEO & Social Metadata</h6>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $pages[$hubKey]->seo_title) }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $pages[$hubKey]->meta_keywords) }}">
                                            <small class="text-muted">Comma-separated keywords.</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                                        <div class="col-sm-10">
                                            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $pages[$hubKey]->meta_description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check2-circle me-1"></i> Save {{ $hubMeta['title'] }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div><!-- card-body -->
            </div><!-- card -->
        </div><!-- col-lg-12 -->
    </div><!-- row -->
</div>
@endsection
