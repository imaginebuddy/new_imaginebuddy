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

      <!-- CARD 1: Photoshoot Information & Metadata -->
			<div class="card shadow-custom border-0 mb-4">
				<div class="card-header bg-white py-3">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-pencil-square me-2 text-primary"></i>1. Edit Photoshoot Metadata
          </h5>
        </div>
				<div class="card-body p-lg-4">

          <form method="POST" action="{{ url('panel/admin/photoshoots/update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $data->id }}">

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

              <div class="col-md-12">
                <div class="form-floating">
                  <textarea class="form-control" name="description" id="description" placeholder="Description" style="height: 90px;">{{ $data->description }}</textarea>
                  <label for="description">Description (Optional)</label>
                </div>
              </div>
            </div>

            <div class="mt-3 text-end">
              <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-save me-1"></i> Save Changes
              </button>
            </div>
          </form>

        </div>
      </div>

      <!-- CARD 2: Add Prompt to Photoshoot (Live AJAX Search) -->
			<div class="card shadow-custom border-0 mb-4">
				<div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>2. Add Prompts to this Photoshoot
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
  });

});
</script>
@endsection
