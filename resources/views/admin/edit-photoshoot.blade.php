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
          $cd = is_array($data->creative_direction) ? $data->creative_direction : [];
          $cdDefs = $defaultCreativeDirection;
        @endphp

        <!-- CARD 2: Creative Direction & Studio Blueprint -->
        <div class="card shadow-custom border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-camera me-2 text-primary"></i>2. Creative Direction &amp; Camera Settings
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
                  <input type="text" class="form-control" id="cd_badge" name="creative_direction[badge]" value="{{ old('creative_direction.badge', $cd['badge'] ?? '') }}" placeholder="Section Badge">
                  <label for="cd_badge">Section Badge</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['badge'] }}</em></small>
              </div>

              <div class="col-md-8">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_heading" name="creative_direction[heading]" value="{{ old('creative_direction.heading', $cd['heading'] ?? '') }}" placeholder="Main Section Heading">
                  <label for="cd_heading">Section Heading (H2)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['heading'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="cd_description" name="creative_direction[description]" placeholder="Lead Description" style="height: 100px;">{{ old('creative_direction.description', $cd['description'] ?? '') }}</textarea>
                  <label for="cd_description">Editorial Narrative (Paragraph 1)</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['description'] }}</em></small>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="cd_subtext" name="creative_direction[subtext]" placeholder="Target Audience / Subtext" style="height: 75px;">{{ old('creative_direction.subtext', $cd['subtext'] ?? '') }}</textarea>
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
                  <input type="text" class="form-control" id="cd_blueprint_title" name="creative_direction[blueprint_title]" value="{{ old('creative_direction.blueprint_title', $cd['blueprint_title'] ?? '') }}" placeholder="Blueprint Title">
                  <label for="cd_blueprint_title">Blueprint Card Title</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['blueprint_title'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_lighting" name="creative_direction[lighting]" value="{{ old('creative_direction.lighting', $cd['lighting'] ?? '') }}" placeholder="Lighting Key">
                  <label for="cd_lighting">Lighting Key</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['lighting'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_lenses" name="creative_direction[lenses]" value="{{ old('creative_direction.lenses', $cd['lenses'] ?? '') }}" placeholder="Optical Lenses">
                  <label for="cd_lenses">Optical Lenses</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['lenses'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_target_ai" name="creative_direction[target_ai]" value="{{ old('creative_direction.target_ai', $cd['target_ai'] ?? '') }}" placeholder="Target AI Models">
                  <label for="cd_target_ai">Target AI Models</label>
                </div>
                <small class="text-muted d-block mt-1">Default: <em>{{ $cdDefs['target_ai'] }}</em></small>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cd_commercial" name="creative_direction[commercial]" value="{{ old('creative_direction.commercial', $cd['commercial'] ?? '') }}" placeholder="Commercial Rights">
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
                    <input type="text" class="form-control" id="cd_pillar_1_title" name="creative_direction[pillar_1_title]" value="{{ old('creative_direction.pillar_1_title', $cd['pillar_1_title'] ?? '') }}" placeholder="Pillar 1 Title">
                    <label for="cd_pillar_1_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_1_desc" name="creative_direction[pillar_1_desc]" placeholder="Pillar 1 Description" style="height: 110px;">{{ old('creative_direction.pillar_1_desc', $cd['pillar_1_desc'] ?? '') }}</textarea>
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
                    <input type="text" class="form-control" id="cd_pillar_2_title" name="creative_direction[pillar_2_title]" value="{{ old('creative_direction.pillar_2_title', $cd['pillar_2_title'] ?? '') }}" placeholder="Pillar 2 Title">
                    <label for="cd_pillar_2_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_2_desc" name="creative_direction[pillar_2_desc]" placeholder="Pillar 2 Description" style="height: 110px;">{{ old('creative_direction.pillar_2_desc', $cd['pillar_2_desc'] ?? '') }}</textarea>
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
                    <input type="text" class="form-control" id="cd_pillar_3_title" name="creative_direction[pillar_3_title]" value="{{ old('creative_direction.pillar_3_title', $cd['pillar_3_title'] ?? '') }}" placeholder="Pillar 3 Title">
                    <label for="cd_pillar_3_title">Title</label>
                  </div>
                  <div class="form-floating">
                    <textarea class="form-control" id="cd_pillar_3_desc" name="creative_direction[pillar_3_desc]" placeholder="Pillar 3 Description" style="height: 110px;">{{ old('creative_direction.pillar_3_desc', $cd['pillar_3_desc'] ?? '') }}</textarea>
                    <label for="cd_pillar_3_desc">Description</label>
                  </div>
                  <small class="text-muted d-block mt-2">Default: <em>{{ $cdDefs['pillar_3_title'] }}</em></small>
                </div>
              </div>
            </div>

          </div>
        </div>

        @php
          $userFaqs = is_array($data->faqs) ? $data->faqs : [];
        @endphp

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
            <i class="bi bi-info-circle me-1 text-primary"></i> Save metadata, creative direction, and FAQs for this photoshoot.
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

      <!-- CARD 3: Prompts currently in this Photoshoot -->
			<div class="card shadow-custom border-0">
				<div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images me-2 text-primary"></i>3. Prompts in this Photoshoot (<span id="promptCountBadge">{{ $data->allImages->count() }}</span>)
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
