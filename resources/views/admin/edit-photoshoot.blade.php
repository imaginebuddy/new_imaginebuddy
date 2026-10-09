@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/photoshoots') }}">Photoshoots</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Manage: {{ $data->title }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      @endif

      <!-- Alert Container for AJAX notifications -->
      <div id="ajaxAlertContainer"></div>

      <form method="POST" action="{{ url('panel/admin/photoshoots/update') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id }}">

        <!-- CARD 1: Photoshoot Information & Metadata -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-pencil-square me-2 text-primary"></i>1. Photoshoot Metadata &amp; SEO
            </h5>
          </div>
          <div class="card-body p-lg-4">

            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" required class="form-control" id="title" name="title" value="{{ $data->title }}" placeholder="Photoshoot Title">
                  <label for="title">Photoshoot Title *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="slug" name="slug" value="{{ $data->slug }}" placeholder="Photoshoot Slug">
                  <label for="slug">Photoshoot Slug</label>
                </div>
                <small class="text-muted d-block mt-1">* Custom URL identifier. If left empty, title will be used.</small>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select name="categories_id" class="form-select" id="categories_id">
                    <option value="">None / General</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" @selected($data->categories_id == $category->id)>
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
                      <option value="{{ $model }}" @selected(old('ai_model', $data->ai_model) == $model)>
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
                  <textarea class="form-control" name="description" id="description" placeholder="Description" style="height: 90px;">{{ $data->description }}</textarea>
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
                  <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $data->meta_title) }}" placeholder="Meta Title">
                  <label for="meta_title">Meta Title (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Leave empty to use photoshoot title as default.</small>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <textarea class="form-control" name="meta_description" id="meta_description" placeholder="Meta Description" style="height: 80px;">{{ old('meta_description', $data->meta_description) }}</textarea>
                  <label for="meta_description">Meta Description (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Leave empty to use photoshoot description or site default.</small>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $data->meta_keywords) }}" placeholder="Meta Keywords">
                  <label for="meta_keywords">Meta Keywords (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">Comma-separated keywords (e.g. fashion, studio, portrait). Leave empty to use site default.</small>
              </div>
            </div>

          </div>
        </div>

        @php
          $pa = is_array($data->product_adaptability) ? $data->product_adaptability : [];
          $paDefs = $defaultProductAdaptability;
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

            <!-- Section Enable / Disable Live Toggle -->
            <div class="card bg-light border border-2 mb-4 rounded-3 p-3">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                  <div class="form-check form-switch form-switch-md mb-0">
                    <input type="hidden" name="show_product_adaptability" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_product_adaptability" name="show_product_adaptability" value="1" {{ old('show_product_adaptability', $data->show_product_adaptability ? '1' : '0') == '1' ? 'checked' : '' }}>
                  </div>
                  <div>
                    <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="show_product_adaptability">
                      Enable "Universal Category Compatibility" Section on Live Page
                    </label>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Turn ON to display this section on <code>/photoshoots/{{ $data->slug }}</code> when data is added. (Off by default)</small>
                  </div>
                </div>
                <span class="badge {{ old('show_product_adaptability', $data->show_product_adaptability ? '1' : '0') == '1' ? 'bg-success' : 'bg-secondary' }}" id="badge_status_pa">
                  {{ old('show_product_adaptability', $data->show_product_adaptability ? '1' : '0') == '1' ? 'Active / Visible' : 'Disabled (Hidden)' }}
                </span>
              </div>
            </div>

            <!-- Subheading: Editorial Narrative -->
            <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">
              <i class="bi bi-shield-check me-1"></i> Editorial Narrative &amp; Compatibility Context
            </h6>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="pa_badge" name="product_adaptability[badge]" value="{{ old('product_adaptability.badge', $pa['badge'] ?? '') }}" placeholder="Section Badge">
                  <label for="pa_badge">Section Badge</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['badge'] }}</em></small>
              </div>

              <div class="col-md-8">
                <div class="form-floating">
                  <input type="text" class="form-control" id="pa_heading" name="product_adaptability[heading]" value="{{ old('product_adaptability.heading', $pa['heading'] ?? '') }}" placeholder="Section Heading (H2)">
                  <label for="pa_heading">Section Heading (H2)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['heading'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="pa_description" name="product_adaptability[description]" placeholder="Primary Compatibility Narrative" style="height: 100px;">{{ old('product_adaptability.description', $pa['description'] ?? '') }}</textarea>
                  <label for="pa_description">Primary Compatibility Narrative (Explains Demo vs Any Category Product)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['description'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="pa_subtext" name="product_adaptability[subtext]" placeholder="Secondary Adaptability Narrative" style="height: 90px;">{{ old('product_adaptability.subtext', $pa['subtext'] ?? '') }}</textarea>
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
                <input class="form-check-input" type="checkbox" role="switch" id="pa_show_applicable_products" name="product_adaptability[show_applicable_products]" value="1" {{ old('product_adaptability.show_applicable_products', $pa['show_applicable_products'] ?? '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark" for="pa_show_applicable_products">
                  Show "Applicable Product Types" Card in Universal Category Compatibility section
                </label>
                <div class="text-muted small">When enabled, renders a visual card displaying all compatible product formats/styles as interactive badges.</div>
              </div>

              <div class="row g-3">
                <div class="col-md-12">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="pa_applicable_products_title" name="product_adaptability[applicable_products_title]" value="{{ old('product_adaptability.applicable_products_title', $pa['applicable_products_title'] ?? '') }}" placeholder="Card Heading">
                    <label for="pa_applicable_products_title">Card Heading</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['applicable_products_title'] }}</em></small>
                </div>

                <div class="col-md-12">
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_applicable_products" name="product_adaptability[applicable_products]" placeholder="Applicable Product Types" style="height: 85px;">{{ old('product_adaptability.applicable_products', $pa['applicable_products'] ?? '') }}</textarea>
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
                    <input type="text" class="form-control" id="pa_step_1_title" name="product_adaptability[step_1_title]" value="{{ old('product_adaptability.step_1_title', $pa['step_1_title'] ?? '') }}" placeholder="Step 1 Title">
                    <label for="pa_step_1_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_1_desc" name="product_adaptability[step_1_desc]" placeholder="Step 1 Description" style="height: 90px;">{{ old('product_adaptability.step_1_desc', $pa['step_1_desc'] ?? '') }}</textarea>
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
                    <input type="text" class="form-control" id="pa_step_2_title" name="product_adaptability[step_2_title]" value="{{ old('product_adaptability.step_2_title', $pa['step_2_title'] ?? '') }}" placeholder="Step 2 Title">
                    <label for="pa_step_2_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_2_desc" name="product_adaptability[step_2_desc]" placeholder="Step 2 Description" style="height: 90px;">{{ old('product_adaptability.step_2_desc', $pa['step_2_desc'] ?? '') }}</textarea>
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
                    <input type="text" class="form-control" id="pa_step_3_title" name="product_adaptability[step_3_title]" value="{{ old('product_adaptability.step_3_title', $pa['step_3_title'] ?? '') }}" placeholder="Step 3 Title">
                    <label for="pa_step_3_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="pa_step_3_desc" name="product_adaptability[step_3_desc]" placeholder="Step 3 Description" style="height: 90px;">{{ old('product_adaptability.step_3_desc', $pa['step_3_desc'] ?? '') }}</textarea>
                    <label for="pa_step_3_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-1">Default: <em>{{ $paDefs['step_3_title'] }}</em></small>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD 3: Frequently Asked Questions (5 FAQs) -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-question-circle me-2 text-primary"></i>3. Frequently Asked Questions (5 FAQs)
              </h5>
              <small class="text-muted">Manage the 5 FAQs displayed on detail page and synced with Google Schema.org. Leave empty to use system defaults.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAutofillFaqs">
              <i class="bi bi-magic me-1"></i> Autofill Standard 5 FAQs
            </button>
          </div>
          <div class="card-body p-lg-4">

            <!-- Section Enable / Disable Live Toggle -->
            <div class="card bg-light border border-2 mb-4 rounded-3 p-3">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                  <div class="form-check form-switch form-switch-md mb-0">
                    <input type="hidden" name="show_faqs" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_faqs" name="show_faqs" value="1" {{ old('show_faqs', $data->show_faqs ? '1' : '0') == '1' ? 'checked' : '' }}>
                  </div>
                  <div>
                    <label class="form-check-label fw-bold text-dark cursor-pointer mb-0" for="show_faqs">
                      Enable "Frequently Asked Questions" Section on Live Page
                    </label>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">Turn ON to display the FAQ accordion and Schema.org structured data on <code>/photoshoots/{{ $data->slug }}</code>. (Off by default)</small>
                  </div>
                </div>
                <span class="badge {{ old('show_faqs', $data->show_faqs ? '1' : '0') == '1' ? 'bg-success' : 'bg-secondary' }}" id="badge_status_faqs">
                  {{ old('show_faqs', $data->show_faqs ? '1' : '0') == '1' ? 'Active / Visible' : 'Disabled (Hidden)' }}
                </span>
              </div>
            </div>

            @for ($i = 0; $i < 5; $i++)
              @php
                $itemQ = old('faqs.'.$i.'.question', $userFaqs[$i]['question'] ?? '');
                $itemA = old('faqs.'.$i.'.answer', $userFaqs[$i]['answer'] ?? '');
                $defQ = $defaultFaqs[$i]['question'] ?? '';
                $defA = $defaultFaqs[$i]['answer'] ?? '';
              @endphp
              <div class="border rounded-3 p-3 mb-3 bg-light faq-admin-item">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-semibold">FAQ #{{ $i + 1 }}</span>
                  @if (!empty($itemQ) || !empty($itemA))
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                      <i class="bi bi-check-circle me-1"></i> Custom Override
                    </span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1 small">
                      Using System Default
                    </span>
                  @endif
                </div>

                <div class="form-floating mb-2">
                  <input type="text" class="form-control faq-input-q" id="faq_q_{{ $i }}" name="faqs[{{ $i }}][question]" value="{{ $itemQ }}" placeholder="Question #{{ $i + 1 }}">
                  <label for="faq_q_{{ $i }}">Question #{{ $i + 1 }}</label>
                </div>

                <div class="form-floating">
                  <textarea class="form-control faq-input-a" id="faq_a_{{ $i }}" name="faqs[{{ $i }}][answer]" placeholder="Answer #{{ $i + 1 }}" style="height: 85px;">{{ $itemA }}</textarea>
                  <label for="faq_a_{{ $i }}">Answer #{{ $i + 1 }}</label>
                </div>
                <small class="text-muted d-block mt-1">Default Question: <em>{{ $defQ }}</em></small>
              </div>
            @endfor

          </div>
        </div>

        <!-- Sticky/Floating Form Submit Bar -->
        <div class="card shadow-custom border-0 mb-4 bg-white p-3 d-flex flex-row justify-content-between align-items-center">
          <div class="text-muted small">
            <i class="bi bi-info-circle me-1 text-primary"></i> Save metadata, product adaptability, and FAQs for this photoshoot.
          </div>
          <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm">
            <i class="bi bi-save me-1"></i> Save All Changes
          </button>
        </div>

      </form>

      <!-- CARD 4: Add Prompt to Photoshoot (Live AJAX Search) -->
      <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>4. Add Prompts to this Photoshoot
          </h5>
          <small class="text-muted">Search existing prompts by title, slug, or ID</small>
        </div>
				<div class="card-body p-lg-4">

          <div class="position-relative mb-3">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="searchPromptInput" class="form-control border-start-0 ps-0" placeholder="Type prompt title, slug, or ID (e.g. 'Luxury Perfume')...">
            </div>
          </div>

          <!-- Live Search Results Container -->
          <div id="searchResultsBox" class="rounded-3 border p-3 bg-light display-none mb-3">
            <h6 class="fw-bold text-dark mb-2" id="searchResultsHeader">Search Results</h6>
            <div id="searchResultsList" class="list-group list-group-flush">
              <!-- Dynamically populated -->
            </div>
          </div>

        </div>
      </div>

      <!-- CARD 5: Prompts currently in this Photoshoot -->
			<div class="card shadow-custom border-0">
				<div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images me-2 text-primary"></i>5. Prompts in this Photoshoot (<span id="promptCountBadge">{{ $data->allImages->count() }}</span>)
          </h5>
        </div>
				<div class="card-body p-lg-4">

          <div class="table-responsive p-0">
            <table class="table table-hover align-middle mb-0" id="tableAssociatedPrompts">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 70px;">ID</th>
                  <th scope="col" style="width: 70px;">Preview</th>
                  <th scope="col">Prompt Title</th>
                  <th scope="col">Uploaded By</th>
                  <th scope="col" style="width: 100px;">Tier</th>
                  <th scope="col" style="width: 100px;">Status</th>
                  <th scope="col" style="width: 140px;">Actions</th>
                </tr>
              </thead>
              <tbody id="associatedPromptsBody">
                @if ($data->allImages->count() != 0)
                  @foreach ($data->allImages as $image)
                    <tr id="promptRow-{{ $image->id }}">
                      <td class="fw-bold text-muted">{{ $image->id }}</td>
                      <td>
                        <img src="{{ Storage::url(config('path.thumbnail') . $image->thumbnail) }}" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" />
                      </td>
                      <td>
                        <a href="{{ url('prompt', $image->slug) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                          {{ $image->title }} <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                        </a>
                        <small class="text-muted d-block"><code>{{ $image->slug }}</code></small>
                      </td>
                      <td>
                        <span class="text-secondary small">{{ $image->user ? $image->user->username : 'Unknown' }}</span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $image->item_for_sale == 'sale' ? 'warning' : 'secondary' }}">
                          {{ $image->item_for_sale == 'sale' ? 'Premium' : 'Free' }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-{{ $image->status == 'active' ? 'success' : 'danger' }}">
                          {{ ucfirst($image->status) }}
                        </span>
                      </td>
                      <td>
                        <button type="button" class="btn btn-link text-danger e-none fs-5 p-0 border-0 btnRemovePrompt" data-id="{{ $image->id }}" title="Remove from Photoshoot">
                          <i class="bi-trash-fill"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr id="noPromptsRow">
                    <td colspan="7" class="text-center p-5 text-muted fw-light">
                      No prompts currently in this photoshoot. Use the search box above to add existing prompts.
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>

  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

  var photoshootId = {{ $data->id }};
  var searchTimeout = null;

  function showAlert(message, type) {
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show mb-3" role="alert">' +
      '<i class="bi bi-info-circle me-1"></i> ' + message +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
    '</div>';
    $('#ajaxAlertContainer').html(alertHtml);
  }

  // Live AJAX Search for Prompts
  $('#searchPromptInput').on('keyup input', function() {
    var query = $.trim($(this).val());
    clearTimeout(searchTimeout);

    if (query.length < 2) {
      $('#searchResultsBox').hide();
      return;
    }

    searchTimeout = setTimeout(function() {
      $.ajax({
        url: "{{ url('panel/admin/photoshoots/search-prompts') }}",
        type: 'GET',
        data: { q: query },
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            var $list = $('#searchResultsList');
            $list.empty();

            if (res.prompts && res.prompts.length > 0) {
              $.each(res.prompts, function(i, prompt) {
                var isAlreadyInThis = (prompt.photoshoot_id == photoshootId);
                var isAlreadyInOther = (prompt.photoshoot_id && !isAlreadyInThis);

                var badgeInfo = '';
                if (isAlreadyInThis) {
                  badgeInfo = '<span class="badge bg-success ms-2">Already in this photoshoot</span>';
                } else if (isAlreadyInOther) {
                  badgeInfo = '<span class="badge bg-warning text-dark ms-2">In "' + prompt.photoshoot_name + '"</span>';
                }

                var itemHtml = '<div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-white mb-1 rounded border">' +
                  '<div class="d-flex align-items-center gap-3">' +
                    '<img src="' + prompt.thumb_url + '" class="rounded shadow-sm" width="40" height="40" style="object-fit: cover;" />' +
                    '<div>' +
                      '<strong class="d-block text-dark small">' + prompt.title + badgeInfo + '</strong>' +
                      '<small class="text-muted" style="font-size: 0.75rem;">ID: ' + prompt.id + ' | By: ' + prompt.user_name + ' | Tier: ' + prompt.tier + '</small>' +
                    '</div>' +
                  '</div>' +
                  '<div>' +
                    (isAlreadyInThis 
                      ? '<button class="btn btn-sm btn-secondary disabled rounded-pill px-3" disabled>Added</button>'
                      : '<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 btnAddPrompt" data-id="' + prompt.id + '"><i class="bi bi-plus-lg me-1"></i> Add to Photoshoot</button>'
                    ) +
                  '</div>' +
                '</div>';

                $list.append(itemHtml);
              });
              $('#searchResultsHeader').text('Search Results (' + res.prompts.length + ' found)');
              $('#searchResultsBox').slideDown();
            } else {
              $list.html('<div class="p-3 text-center text-muted small">No matching prompts found.</div>');
              $('#searchResultsHeader').text('Search Results');
              $('#searchResultsBox').slideDown();
            }
          }
        }
      });
    }, 300);
  });

  // Add Prompt to Photoshoot AJAX
  $(document).on('click', '.btnAddPrompt', function() {
    var promptId = $(this).data('id');
    var $btn = $(this);

    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
      url: "{{ url('panel/admin/photoshoots/add-prompt') }}",
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        photoshoot_id: photoshootId,
        prompt_id: promptId
      },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          showAlert(res.message, 'success');
          $('#searchPromptInput').trigger('keyup');

          // Remove "No prompts" placeholder if present
          $('#noPromptsRow').remove();

          // Append new prompt row if not already present
          if ($('#promptRow-' + res.prompt.id).length === 0) {
            var p = res.prompt;
            var newRow = '<tr id="promptRow-' + p.id + '">' +
              '<td class="fw-bold text-muted">' + p.id + '</td>' +
              '<td><img src="' + p.thumb_url + '" class="rounded shadow-sm" width="48" height="48" style="object-fit: cover;" /></td>' +
              '<td><a href="{{ url("prompt") }}/' + p.slug + '" target="_blank" class="fw-bold text-dark text-decoration-none">' + p.title + ' <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i></a><small class="text-muted d-block"><code>' + p.slug + '</code></small></td>' +
              '<td><span class="text-secondary small">' + p.user_name + '</span></td>' +
              '<td><span class="badge bg-' + (p.tier === 'Premium' ? 'warning' : 'secondary') + '">' + p.tier + '</span></td>' +
              '<td><span class="badge bg-success">Active</span></td>' +
              '<td><button type="button" class="btn btn-link text-danger e-none fs-5 p-0 border-0 btnRemovePrompt" data-id="' + p.id + '" title="Remove from Photoshoot"><i class="bi-trash-fill"></i></button></td>' +
            '</tr>';
            $('#associatedPromptsBody').prepend(newRow);

            // Update badge count
            var currentCount = parseInt($('#promptCountBadge').text()) || 0;
            $('#promptCountBadge').text(currentCount + 1);
          }
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html('<i class="bi bi-plus-lg me-1"></i> Add to Photoshoot');
        var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error adding prompt to photoshoot.';
        showAlert(errMsg, 'danger');
      }
    });
  });

  // Remove Prompt from Photoshoot AJAX
  $(document).on('click', '.btnRemovePrompt', function(e) {
    e.preventDefault();
    var promptId = $(this).data('id');
    var $btn = $(this);

    swal({
      title: typeof delete_confirm !== 'undefined' ? delete_confirm : "Are you sure?",
      text: "Remove this prompt from photoshoot? Note: The prompt itself will NOT be deleted.",
      type: "warning",
      showLoaderOnConfirm: true,
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: typeof yes_confirm !== 'undefined' ? yes_confirm : "Yes, delete it!",
      cancelButtonText: typeof cancel_confirm !== 'undefined' ? cancel_confirm : "No, cancel!",
      closeOnConfirm: true
    }, function(isConfirm) {
      if (isConfirm) {
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
          url: "{{ url('panel/admin/photoshoots/remove-prompt') }}",
          type: 'POST',
          data: {
            _token: "{{ csrf_token() }}",
            photoshoot_id: photoshootId,
            prompt_id: promptId
          },
          dataType: 'json',
          success: function(res) {
            if (res.success) {
              showAlert(res.message, 'success');
              $('#promptRow-' + promptId).fadeOut(300, function() {
                $(this).remove();
                if ($('#associatedPromptsBody tr').length === 0) {
                  $('#associatedPromptsBody').html('<tr id="noPromptsRow"><td colspan="7" class="text-center p-5 text-muted fw-light">No prompts currently in this photoshoot. Use the search box above to add existing prompts.</td></tr>');
                }
              });

              // Update badge count
              var currentCount = parseInt($('#promptCountBadge').text()) || 1;
              $('#promptCountBadge').text(Math.max(0, currentCount - 1));

              // Refresh live search if open
              if ($('#searchPromptInput').val().length >= 2) {
                $('#searchPromptInput').trigger('keyup');
              }
            }
          },
          error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="bi-trash-fill"></i>');
            var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error removing prompt from photoshoot.';
            showAlert(errMsg, 'danger');
          }
        });
      }
    });
  });

  // Toggle badges
  $('#show_product_adaptability').on('change', function() {
    if ($(this).is(':checked')) {
      $('#badge_status_pa').removeClass('bg-secondary').addClass('bg-success').text('Active / Visible');
    } else {
      $('#badge_status_pa').removeClass('bg-success').addClass('bg-secondary').text('Disabled (Hidden)');
    }
  });

  $('#show_faqs').on('change', function() {
    if ($(this).is(':checked')) {
      $('#badge_status_faqs').removeClass('bg-secondary').addClass('bg-success').text('Active / Visible');
    } else {
      $('#badge_status_faqs').removeClass('bg-success').addClass('bg-secondary').text('Disabled (Hidden)');
    }
  });

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
      $('#show_product_adaptability').prop('checked', true).trigger('change');
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
      $('#show_faqs').prop('checked', true).trigger('change');
    }
  });

});
</script>
@endsection
