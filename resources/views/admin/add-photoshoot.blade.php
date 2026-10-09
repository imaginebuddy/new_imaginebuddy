@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/photoshoots') }}">Photoshoots</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Add New Photoshoot</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@include('errors.errors-forms')

      <form method="POST" action="{{ url('panel/admin/photoshoots/add') }}">
        @csrf

        <!-- CARD 1: Photoshoot Basic Info & SEO -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi-plus-circle me-2 text-primary"></i>1. Photoshoot Metadata &amp; SEO
            </h5>
          </div>
          <div class="card-body p-lg-4">

            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" required class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Photoshoot Title">
                  <label for="title">Photoshoot Title *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="Photoshoot Slug">
                  <label for="slug">Photoshoot Slug (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">* Custom URL identifier. If left empty, title will be used as slug.</small>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select name="categories_id" class="form-select" id="categories_id">
                    <option value="">None / General</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" @selected(old('categories_id') == $category->id)>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  <label for="categories_id">Category</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select name="ai_model" class="form-select" id="ai_model">
                    <option value="">None / General</option>
                    @foreach ($aiModels as $model)
                      <option value="{{ $model }}" @selected(old('ai_model') == $model)>
                        {{ $model }}
                      </option>
                    @endforeach
                  </select>
                  <label for="ai_model">AI Model (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Configured in panel/admin/settings (e.g. ChatGPT, Google Gemini, Midjourney)</small>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <textarea class="form-control" name="description" id="description" placeholder="Description" style="height: 90px;">{{ old('description') }}</textarea>
                  <label for="description">Description (Optional)</label>
                </div>
              </div>

              <div class="col-12 mt-4">
                <h6 class="fw-bold text-muted text-uppercase small mb-2 border-bottom pb-2">
                  <i class="bi bi-search me-1"></i> SEO &amp; Search Engine Optimization
                </h6>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" placeholder="Meta Title">
                  <label for="meta_title">Meta Title (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Leave empty to use photoshoot title as default.</small>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <textarea class="form-control" name="meta_description" id="meta_description" placeholder="Meta Description" style="height: 80px;">{{ old('meta_description') }}</textarea>
                  <label for="meta_description">Meta Description (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Leave empty to use photoshoot description or site default.</small>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="Meta Keywords">
                  <label for="meta_keywords">Meta Keywords (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Comma-separated keywords (e.g. fashion, studio, portrait). Leave empty to use site default.</small>
              </div>
            </div>

          </div>
        </div>

        @php
          $paDefs = $defaultProductAdaptability;
          $cdDefs = $defaultCreativeDirection;
        @endphp

        <!-- CARD 2: Product Adaptability & Category Compatibility -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-boxes me-2 text-primary"></i>2. Product Adaptability &amp; Category Compatibility
              </h5>
              <small class="text-muted">Communicate how prompts adapt to any product in this category with zero manual editing. Leave blank to use defaults.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAutofillPa">
              <i class="bi bi-magic me-1"></i> Autofill Defaults
            </button>
          </div>
          <div class="card-body p-lg-4">

            <!-- Subheading: Editorial Narrative -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-shield-check me-1"></i> Editorial Narrative &amp; Compatibility Context
            </h6>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="pa_badge" name="product_adaptability[badge]" value="{{ old('product_adaptability.badge') }}" placeholder="Section Badge">
                  <label for="pa_badge">Section Badge</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['badge'] }}</em></small>
              </div>

              <div class="col-md-8">
                <div class="form-floating">
                  <input type="text" class="form-control" id="pa_heading" name="product_adaptability[heading]" value="{{ old('product_adaptability.heading') }}" placeholder="Section Heading (H2)">
                  <label for="pa_heading">Section Heading (H2)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['heading'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="pa_description" name="product_adaptability[description]" placeholder="Primary Compatibility Narrative" style="height: 100px;">{{ old('product_adaptability.description') }}</textarea>
                  <label for="pa_description">Primary Compatibility Narrative (Explains Demo vs Any Category Product)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['description'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="pa_subtext" name="product_adaptability[subtext]" placeholder="Secondary Adaptability Narrative" style="height: 90px;">{{ old('product_adaptability.subtext') }}</textarea>
                  <label for="pa_subtext">Secondary Narrative (How Prompts Auto-Adapt Colors, Props &amp; Lighting)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['subtext'] }}</em></small>
              </div>
            </div>



            <!-- Subheading: Applicable Product Types Card Setting -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-tags me-1"></i> Applicable Product Types Card Setting
            </h6>

            <div class="card bg-light border-0 rounded-3 p-3 mb-4">
              <div class="form-check form-switch mb-3">
                <input type="hidden" name="product_adaptability[show_applicable_products]" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="pa_show_applicable_products" name="product_adaptability[show_applicable_products]" value="1" {{ old('product_adaptability.show_applicable_products', $paDefs['show_applicable_products'] ?? '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark" for="pa_show_applicable_products">
                  Show "Applicable Product Types" Card in Universal Category Compatibility section
                </label>
                <div class="text-muted small">When enabled, renders a visual card displaying all compatible product formats/styles as interactive badges.</div>
              </div>

              <div class="row g-3">
                <div class="col-md-12">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="pa_applicable_products_title" name="product_adaptability[applicable_products_title]" value="{{ old('product_adaptability.applicable_products_title') }}" placeholder="Card Heading">
                    <label for="pa_applicable_products_title">Card Heading</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['applicable_products_title'] }}</em></small>
                </div>

                <div class="col-md-12">
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_applicable_products" name="product_adaptability[applicable_products]" placeholder="Applicable Product Types" style="height: 85px;">{{ old('product_adaptability.applicable_products') }}</textarea>
                    <label for="pa_applicable_products">Applicable Product Types (Comma-separated styles/sub-categories)</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['applicable_products'] }}</em></small>
                </div>
              </div>
            </div>

            <!-- Subheading: The 3 Frictionless Steps -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-123 me-1"></i> Three-Step Zero-Editing Workflow
            </h6>

            <div class="row g-3 mb-2">
              <!-- Step 1 -->
              <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2 fw-semibold">Step 01</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="pa_step_1_title" name="product_adaptability[step_1_title]" value="{{ old('product_adaptability.step_1_title') }}" placeholder="Step 1 Title">
                    <label for="pa_step_1_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_1_desc" name="product_adaptability[step_1_desc]" placeholder="Step 1 Description" style="height: 90px;">{{ old('product_adaptability.step_1_desc') }}</textarea>
                    <label for="pa_step_1_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['step_1_title'] }}</em></small>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2 fw-semibold">Step 02</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="pa_step_2_title" name="product_adaptability[step_2_title]" value="{{ old('product_adaptability.step_2_title') }}" placeholder="Step 2 Title">
                    <label for="pa_step_2_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_2_desc" name="product_adaptability[step_2_desc]" placeholder="Step 2 Description" style="height: 90px;">{{ old('product_adaptability.step_2_desc') }}</textarea>
                    <label for="pa_step_2_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['step_2_title'] }}</em></small>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2 fw-semibold">Step 03</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="pa_step_3_title" name="product_adaptability[step_3_title]" value="{{ old('product_adaptability.step_3_title') }}" placeholder="Step 3 Title">
                    <label for="pa_step_3_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_3_desc" name="product_adaptability[step_3_desc]" placeholder="Step 3 Description" style="height: 90px;">{{ old('product_adaptability.step_3_desc') }}</textarea>
                    <label for="pa_step_3_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['step_3_title'] }}</em></small>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD 3: Creative Direction & Studio Blueprint -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-camera me-2 text-primary"></i>3. Creative Direction &amp; Camera Settings
              </h5>
              <small class="text-muted">Manage editorial narrative, camera blueprint, and 3 pillars. Leave fields blank to use system defaults.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAutofillCd">
              <i class="bi bi-magic me-1"></i> Autofill Defaults
            </button>
          </div>
          <div class="card-body p-lg-4">

            <!-- Subheading: Editorial Narrative -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-card-text me-1"></i> Editorial Context &amp; Search Authority
            </h6>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_badge" name="creative_direction[badge]" value="{{ old('creative_direction.badge') }}" placeholder="Section Badge">
                  <label for="cd_badge">Section Badge</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['badge'] }}</em></small>
              </div>

              <div class="col-md-8">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_heading" name="creative_direction[heading]" value="{{ old('creative_direction.heading') }}" placeholder="Main Section Heading">
                  <label for="cd_heading">Section Heading (H2)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['heading'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="cd_description" name="creative_direction[description]" placeholder="Lead Description" style="height: 100px;">{{ old('creative_direction.description') }}</textarea>
                  <label for="cd_description">Editorial Narrative (Paragraph 1)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['description'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="cd_subtext" name="creative_direction[subtext]" placeholder="Target Audience / Subtext" style="height: 75px;">{{ old('creative_direction.subtext') }}</textarea>
                  <label for="cd_subtext">Audience &amp; Use Cases (Paragraph 2)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['subtext'] }}</em></small>
              </div>
            </div>

            <!-- Subheading: Camera & Studio Blueprint (4 Specs) -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-sliders me-1"></i> Camera &amp; Studio Blueprint (Technical Specs)
            </h6>

            <div class="row g-3 mb-4">
              <div class="col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_blueprint_title" name="creative_direction[blueprint_title]" value="{{ old('creative_direction.blueprint_title') }}" placeholder="Blueprint Title">
                  <label for="cd_blueprint_title">Blueprint Card Title</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['blueprint_title'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_lighting" name="creative_direction[lighting]" value="{{ old('creative_direction.lighting') }}" placeholder="Lighting Key">
                  <label for="cd_lighting">Lighting Key</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['lighting'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_lenses" name="creative_direction[lenses]" value="{{ old('creative_direction.lenses') }}" placeholder="Optical Lenses">
                  <label for="cd_lenses">Optical Lenses</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['lenses'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_target_ai" name="creative_direction[target_ai]" value="{{ old('creative_direction.target_ai') }}" placeholder="Target AI Models">
                  <label for="cd_target_ai">Target AI Models</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['target_ai'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_commercial" name="creative_direction[commercial]" value="{{ old('creative_direction.commercial') }}" placeholder="Commercial Rights">
                  <label for="cd_commercial">Commercial Rights</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['commercial'] }}</em></small>
              </div>
            </div>

            <!-- Subheading: 3 Pillars -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-grid-3x3-gap me-1"></i> 3 Creative Direction Feature Pillars
            </h6>

            <div class="row g-3">
              <!-- Pillar 1 -->
              <div class="col-lg-4">
                <div class="border rounded-3 p-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 mb-2">Pillar 01</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="cd_pillar_1_title" name="creative_direction[pillar_1_title]" value="{{ old('creative_direction.pillar_1_title') }}" placeholder="Pillar 1 Title">
                    <label for="cd_pillar_1_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_1_desc" name="creative_direction[pillar_1_desc]" placeholder="Pillar 1 Description" style="height: 110px;">{{ old('creative_direction.pillar_1_desc') }}</textarea>
                    <label for="cd_pillar_1_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-2">Default: <em>{{ $cdDefs['pillar_1_title'] }}</em></small>
                </div>
              </div>

              <!-- Pillar 2 -->
              <div class="col-lg-4">
                <div class="border rounded-3 p-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 mb-2">Pillar 02</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="cd_pillar_2_title" name="creative_direction[pillar_2_title]" value="{{ old('creative_direction.pillar_2_title') }}" placeholder="Pillar 2 Title">
                    <label for="cd_pillar_2_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_2_desc" name="creative_direction[pillar_2_desc]" placeholder="Pillar 2 Description" style="height: 110px;">{{ old('creative_direction.pillar_2_desc') }}</textarea>
                    <label for="cd_pillar_2_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-2">Default: <em>{{ $cdDefs['pillar_2_title'] }}</em></small>
                </div>
              </div>

              <!-- Pillar 3 -->
              <div class="col-lg-4">
                <div class="border rounded-3 p-3 bg-light h-100">
                  <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 mb-2">Pillar 03</span>
                  <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="cd_pillar_3_title" name="creative_direction[pillar_3_title]" value="{{ old('creative_direction.pillar_3_title') }}" placeholder="Pillar 3 Title">
                    <label for="cd_pillar_3_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_3_desc" name="creative_direction[pillar_3_desc]" placeholder="Pillar 3 Description" style="height: 110px;">{{ old('creative_direction.pillar_3_desc') }}</textarea>
                    <label for="cd_pillar_3_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-2">Default: <em>{{ $cdDefs['pillar_3_title'] }}</em></small>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD 4: Frequently Asked Questions (5 FAQs) -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-question-circle me-2 text-primary"></i>4. Frequently Asked Questions (5 FAQs)
              </h5>
              <small class="text-muted">Manage the 5 FAQs displayed on detail page and synced with Google Schema.org. Leave empty to use system defaults.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAutofillFaqs">
              <i class="bi bi-magic me-1"></i> Autofill Standard 5 FAQs
            </button>
          </div>
          <div class="card-body p-lg-4">

            @for ($i = 0; $i < 5; $i++)
              @php
                $itemQ = old('faqs.'.$i.'.question');
                $itemA = old('faqs.'.$i.'.answer');
                $defQ = $defaultFaqs[$i]['question'] ?? '';
                $defA = $defaultFaqs[$i]['answer'] ?? '';
              @endphp
              <div class="border rounded-3 p-3 mb-3 bg-light faq-admin-item">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-semibold">FAQ #{{ $i + 1 }}</span>
                  <small class="text-muted">Default Question: <em>{{ $defQ }}</em></small>
                </div>

                <div class="form-floating mb-2">
                  <input type="text" class="form-control faq-input-q" id="faq_q_{{ $i }}" name="faqs[{{ $i }}][question]" value="{{ $itemQ }}" placeholder="Question #{{ $i + 1 }}">
                  <label for="faq_q_{{ $i }}">Question #{{ $i + 1 }}</label>
                </div>

                <div class="form-floating">
                  <textarea class="form-control faq-input-a" id="faq_a_{{ $i }}" name="faqs[{{ $i }}][answer]" placeholder="Answer #{{ $i + 1 }}" style="height: 85px;">{{ $itemA }}</textarea>
                  <label for="faq_a_{{ $i }}">Answer #{{ $i + 1 }}</label>
                </div>
              </div>
            @endfor

          </div>
        </div>

        <div class="card shadow-custom border-0 mb-4 bg-white p-3 d-flex flex-row justify-content-between align-items-center">
          <a href="{{ url('panel/admin/photoshoots') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
          <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm">
            <i class="bi bi-check-lg me-1"></i> Create Photoshoot
          </button>
        </div>

      </form>

    </div>

  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
  // Autofill Product Adaptability Defaults
  var paDefaults = @json($defaultProductAdaptability);
  $('#btnAutofillPa').on('click', function() {
    if (confirm('Autofill standard product adaptability & category compatibility settings?')) {
      for (var key in paDefaults) {
        var $input = $('#pa_' + key);
        if ($input.length) {
          if ($input.is(':checkbox')) {
            $input.prop('checked', paDefaults[key] == '1' || paDefaults[key] === true);
          } else {
            $input.val(paDefaults[key]);
          }
        }
      }
    }
  });

  // Autofill Creative Direction Defaults
  var cdDefaults = @json($defaultCreativeDirection);
  $('#btnAutofillCd').on('click', function() {
    if (confirm('Autofill standard creative direction and camera settings?')) {
      for (var key in cdDefaults) {
        var $input = $('#cd_' + key);
        if ($input.length) {
          $input.val(cdDefaults[key]);
        }
      }
    }
  });

  // Autofill Standard 5 FAQs
  var defaultFaqs = @json($defaultFaqs);
  $('#btnAutofillFaqs').on('click', function() {
    if (confirm('Autofill standard 5 FAQs?')) {
      for (var i = 0; i < defaultFaqs.length; i++) {
        $('#faq_q_' + i).val(defaultFaqs[i].question);
        $('#faq_a_' + i).val(defaultFaqs[i].answer);
      }
    }
  });
});
</script>
@endsection
