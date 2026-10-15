@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Hire Service Settings</span>
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
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-briefcase me-2"></i> Custom AI Photoshoot Service & Pricing Settings</h6>
                    <div>
                        <a href="{{ url('panel/admin/hire/inquiries') }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-inbox me-1"></i> View Inquiries / Leads
                        </a>
                        <a href="{{ url('hire') }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Preview Hire Page
                        </a>
                    </div>
                </div>

                <div class="card-body p-lg-5">
                    <form method="POST" action="{{ url('panel/admin/hire/settings') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Status Toggle -->
                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end fw-semibold">Hire Page Status</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="status" value="on" id="statusSwitch" @checked($settings->status == 'on')>
                                    <label class="form-check-label" for="statusSwitch">Enable "Hire" Service Page & Navigation Item</label>
                                </div>
                                <small class="text-muted">When disabled, the Hire page displays a maintenance notice and hides navigation link.</small>
                            </div>
                        </div>

                        <!-- 1. Hero Section Content -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-stars text-warning me-2"></i> 1. Hero Section Copy</h5>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Hero Eyebrow</label>
                            <div class="col-sm-9">
                                <input type="text" name="hero_eyebrow" value="{{ old('hero_eyebrow', $settings->hero_eyebrow) }}" class="form-control" required>
                                <small class="text-muted">Appears in small uppercase above the main headline.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Hero Headline</label>
                            <div class="col-sm-9">
                                <input type="text" name="hero_headline" value="{{ old('hero_headline', $settings->hero_headline) }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Hero Supporting Paragraph</label>
                            <div class="col-sm-9">
                                <textarea name="hero_subheadline" rows="4" class="form-control">{{ old('hero_subheadline', $settings->hero_subheadline) }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Hero Trust Statement</label>
                            <div class="col-sm-9">
                                <input type="text" name="hero_badge_text" value="{{ old('hero_badge_text', $settings->hero_badge_text) }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end">Custom Hero Image</label>
                            <div class="col-sm-9">
                                @if($settings->hero_image)
                                <div class="mb-2">
                                    <img src="{{ asset('public/img/' . $settings->hero_image) }}" alt="Hero image" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                                @endif
                                <input type="file" name="hero_image" class="form-control" accept="image/*">
                                <small class="text-muted">Optional custom campaign visual for hero right column (Leave empty to use curated portfolio grid).</small>
                            </div>
                        </div>

                        <!-- 2. Dynamic Pricing Settings -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-currency-exchange text-success me-2"></i> 2. Dynamic Pricing Guidance (USD vs INR)</h5>
                        <p class="text-muted small mb-4">
                            Visitors detected outside India see <strong>USD ($)</strong> prices. Visitors from India automatically see <strong>INR (₹)</strong> prices. You can configure exact rates for both currencies here.
                        </p>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-currency-dollar me-1"></i> International Pricing (USD $)</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">1 SKU Package Price ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="price_1_sku_usd" value="{{ old('price_1_sku_usd', $settings->price_1_sku_usd) }}" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">3–5 SKUs Tier Price / SKU ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="price_3_5_sku_usd" value="{{ old('price_3_5_sku_usd', $settings->price_3_5_sku_usd) }}" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">6+ SKUs Scale Price / SKU ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="price_6_plus_sku_usd" value="{{ old('price_6_plus_sku_usd', $settings->price_6_plus_sku_usd) }}" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-currency-rupee me-1"></i> India Domestic Pricing (INR ₹)</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">1 SKU Package Price (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="price_1_sku_inr" value="{{ old('price_1_sku_inr', $settings->price_1_sku_inr) }}" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">3–5 SKUs Tier Price / SKU (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="price_3_5_sku_inr" value="{{ old('price_3_5_sku_inr', $settings->price_3_5_sku_inr) }}" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">6+ SKUs Scale Price / SKU (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="price_6_plus_sku_inr" value="{{ old('price_6_plus_sku_inr', $settings->price_6_plus_sku_inr) }}" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end">Custom Photoshoot Quote Note</label>
                            <div class="col-sm-9">
                                <textarea name="custom_quote_text" rows="2" class="form-control">{{ old('custom_quote_text', $settings->custom_quote_text) }}</textarea>
                                <small class="text-muted">Clarifies that custom creative direction and complex campaigns receive a personalized quote based on scope.</small>
                            </div>
                        </div>

                        <!-- 3. Service Intro Section -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-card-text text-info me-2"></i> 3. Service Introduction Section</h5>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Section Heading</label>
                            <div class="col-sm-9">
                                <input type="text" name="service_intro_title" value="{{ old('service_intro_title', $settings->service_intro_title) }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end">Section Description</label>
                            <div class="col-sm-9">
                                <textarea name="service_intro_subtitle" rows="3" class="form-control">{{ old('service_intro_subtitle', $settings->service_intro_subtitle) }}</textarea>
                            </div>
                        </div>

                        <!-- 4. Form Options Configuration -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-ui-checks-grid text-primary me-2"></i> 4. Inquiry Form Manageable Options (One per line)</h5>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Product Categories</label>
                            <div class="col-sm-9">
                                <textarea name="form_categories" rows="5" class="form-control">{{ is_array($settings->form_categories) ? implode("\n", $settings->form_categories) : $settings->form_categories }}</textarea>
                                <small class="text-muted">Options shown in the "Product Category" dropdown on the inquiry form.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Photoshoot Types</label>
                            <div class="col-sm-9">
                                <textarea name="form_shoot_types" rows="5" class="form-control">{{ is_array($settings->form_shoot_types) ? implode("\n", $settings->form_shoot_types) : $settings->form_shoot_types }}</textarea>
                                <small class="text-muted">Options for the "Type of Photoshoot" checkboxes.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Intended Uses</label>
                            <div class="col-sm-9">
                                <textarea name="form_intended_uses" rows="5" class="form-control">{{ is_array($settings->form_intended_uses) ? implode("\n", $settings->form_intended_uses) : $settings->form_intended_uses }}</textarea>
                                <small class="text-muted">Options for the "Intended Use" options.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Budget Ranges (USD $)</label>
                            <div class="col-sm-9">
                                <textarea name="form_budgets_usd" rows="4" class="form-control">{{ is_array($settings->form_budgets_usd) ? implode("\n", $settings->form_budgets_usd) : $settings->form_budgets_usd }}</textarea>
                                <small class="text-muted">Selectable budget options when currency is USD.</small>
                            </div>
                        </div>

                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end">Budget Ranges (INR ₹)</label>
                            <div class="col-sm-9">
                                <textarea name="form_budgets_inr" rows="4" class="form-control">{{ is_array($settings->form_budgets_inr) ? implode("\n", $settings->form_budgets_inr) : $settings->form_budgets_inr }}</textarea>
                                <small class="text-muted">Selectable budget options when currency is INR.</small>
                            </div>
                        </div>

                        <!-- 5. Notification & Email Settings -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-envelope-at text-danger me-2"></i> 5. Notification & Lead Alerts</h5>

                        <div class="row mb-4 pb-3 border-bottom">
                            <label class="col-sm-3 col-form-label text-lg-end">Notification Recipient Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="notification_email" value="{{ old('notification_email', $settings->notification_email) }}" class="form-control" placeholder="{{ config('settings.email_admin') }}">
                                <small class="text-muted">Email address that receives alert notifications whenever a client submits a new photoshoot inquiry. Defaults to site admin email if left blank.</small>
                            </div>
                        </div>

                        <!-- 6. SEO & Meta Tags -->
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-search text-secondary me-2"></i> 6. SEO & Social Metadata</h5>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">SEO Page Title</label>
                            <div class="col-sm-9">
                                <input type="text" name="meta_title" value="{{ old('meta_title', $settings->meta_title) }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-lg-end">Meta Description</label>
                            <div class="col-sm-9">
                                <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $settings->meta_description) }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-sm-3 col-form-label text-lg-end">Meta Keywords</label>
                            <div class="col-sm-9">
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings->meta_keywords) }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-dark px-4 py-2">
                                    <i class="bi bi-save me-1"></i> Save Hire Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
