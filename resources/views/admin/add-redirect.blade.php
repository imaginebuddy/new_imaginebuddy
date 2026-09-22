@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/redirects') }}">URL Redirects</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Add New Redirect</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@include('errors.errors-forms')

			<div class="card shadow-custom border-0 mb-4">
				<div class="card-header bg-white py-3">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi-plus-circle me-2 text-primary"></i>Create URL Redirection Rule
          </h5>
        </div>
				<div class="card-body p-lg-4">

          <form method="POST" action="{{ url('panel/admin/redirects/add') }}">
            @csrf

            <div class="row g-4">
              
              <!-- Source URL -->
              <div class="col-md-6">
                <label for="source_url" class="form-label fw-bold">Source URL (Incoming Request) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted"><i class="bi bi-box-arrow-in-right"></i></span>
                  <input type="text" required class="form-control font-monospace" id="source_url" name="source_url" value="{{ old('source_url') }}" placeholder="/old-category or old-page">
                </div>
                <small class="text-muted d-block mt-1">
                  Enter relative path (e.g. <code>/old-path</code>) or full URL. Leading slash is added automatically. Case-insensitive.
                </small>
              </div>

              <!-- Destination URL -->
              <div class="col-md-6">
                <label for="destination_url" class="form-label fw-bold">Destination URL (Redirect Target) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted"><i class="bi bi-box-arrow-up-right"></i></span>
                  <input type="text" required class="form-control font-monospace" id="destination_url" name="destination_url" value="{{ old('destination_url') }}" placeholder="/new-category or https://example.com/page">
                </div>
                <small class="text-muted d-block mt-1">
                  Can be an internal path (e.g. <code>/prompts/free</code>) or external URL starting with <code>https://</code>.
                </small>
              </div>

              <!-- Redirect Type & Explanations -->
              <div class="col-md-6">
                <label class="form-label fw-bold">Redirect Type (HTTP Status Code) <span class="text-danger">*</span></label>
                <div class="card bg-light border p-3">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="redirect_type" id="type_301" value="301" {{ old('redirect_type', '301') == '301' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark" for="type_301">
                      <span class="badge bg-primary rounded-pill me-1">301</span> Moved Permanently (Recommended for SEO)
                    </label>
                    <small class="text-muted d-block ms-4">
                      Passes link equity / SEO rank to the new URL. Browsers and search engines cache this permanently.
                    </small>
                  </div>
                  <hr class="my-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="redirect_type" id="type_302" value="302" {{ old('redirect_type') == '302' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark" for="type_302">
                      <span class="badge bg-warning text-dark rounded-pill me-1">302</span> Found / Temporary Redirect
                    </label>
                    <small class="text-muted d-block ms-4">
                      Use for temporary maintenance, A/B tests, or seasonal promotions. Search engines keep the original URL indexed.
                    </small>
                  </div>
                </div>
              </div>

              <!-- Options: Status & Preserve Query -->
              <div class="col-md-6">
                <label class="form-label fw-bold">Behavior &amp; Options</label>
                <div class="card bg-light border p-3 h-100">
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="status">Enable Redirect Immediately (Active)</label>
                    <small class="text-muted d-block">Uncheck to save as a disabled draft without redirecting traffic.</small>
                  </div>
                  <hr class="my-2">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="preserve_query" name="preserve_query" value="1" {{ old('preserve_query', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="preserve_query">Preserve &amp; Forward Query Parameters</label>
                    <small class="text-muted d-block">E.g., forwards <code>?ref=promo&amp;utm_source=ad</code> from the incoming request onto the destination.</small>
                  </div>
                </div>
              </div>

              <!-- Notes / Documentation -->
              <div class="col-md-12">
                <label for="notes" class="form-label fw-bold">Internal Admin Notes (Optional)</label>
                <input type="text" class="form-control" id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. Migrated old blog post URL to new category format">
                <small class="text-muted">Brief context for your team explaining why this redirect was created.</small>
              </div>

              <!-- Live Preview Card -->
              <div class="col-md-12">
                <div class="alert alert-secondary border d-flex align-items-center gap-3 py-3 px-4 mb-0">
                  <div class="fs-4 text-primary"><i class="bi bi-signpost-split"></i></div>
                  <div class="flex-grow-1 overflow-hidden">
                    <strong class="d-block text-dark small text-uppercase">Live Redirect Preview:</strong>
                    <div class="d-flex align-items-center gap-2 flex-wrap font-monospace text-dark mt-1">
                      <span class="badge bg-dark text-white px-2 py-1" id="preview_source">{{ url('/old-page') }}</span>
                      <i class="bi bi-arrow-right text-primary fs-5"></i>
                      <span class="badge bg-primary text-white px-2 py-1" id="preview_dest">{{ url('/new-page') }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="col-md-12 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4">
                  <i class="bi bi-check2-circle me-1"></i> Save Redirect
                </button>
                <a href="{{ url('panel/admin/redirects') }}" class="btn btn-outline-secondary px-4">
                  Cancel
                </a>
              </div>

            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sourceInput = document.getElementById('source_url');
  const destInput = document.getElementById('destination_url');
  const previewSource = document.getElementById('preview_source');
  const previewDest = document.getElementById('preview_dest');
  const baseUrl = "{{ url('/') }}";

  function updatePreview() {
    let sVal = sourceInput.value.trim();
    let dVal = destInput.value.trim();

    if (!sVal) {
      previewSource.textContent = baseUrl + '/old-page';
    } else {
      if (sVal.startsWith('http://') || sVal.startsWith('https://')) {
        previewSource.textContent = sVal;
      } else {
        previewSource.textContent = baseUrl + '/' + sVal.replace(/^\/+/, '');
      }
    }

    if (!dVal) {
      previewDest.textContent = baseUrl + '/new-page';
    } else {
      if (dVal.startsWith('http://') || dVal.startsWith('https://')) {
        previewDest.textContent = dVal;
      } else {
        previewDest.textContent = baseUrl + '/' + dVal.replace(/^\/+/, '');
      }
    }
  }

  sourceInput.addEventListener('input', updatePreview);
  destInput.addEventListener('input', updatePreview);
  updatePreview();
});
</script>
@endsection
