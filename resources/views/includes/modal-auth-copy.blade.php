@guest
<!-- Guest Copy Prompt Conversion Modal -->
<div class="modal fade" id="authCopyModal" tabindex="-1" aria-labelledby="authCopyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
      <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pt-2 pb-4 text-center">
        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 64px; height: 64px;">
          <i class="bi bi-stars text-warning fs-2"></i>
        </div>
        <div class="h4 fw-bold text-dark mb-2" id="authCopyModalLabel">Sign Up to View & Copy This Prompt</div>
        <p class="text-secondary small mb-4 px-2" style="line-height: 1.6;">
          Join {{ $settings->title ?? 'ImagineBuddy' }} for free to view and copy full prompts, unlock <strong>{{ isset($settings->daily_limit_prompts) && $settings->daily_limit_prompts == 0 ? strtolower(trans('admin.unlimited')) : (isset($settings->daily_limit_prompts) ? $settings->daily_limit_prompts : 10) }} free prompt {{ (isset($settings->daily_limit_prompts) && $settings->daily_limit_prompts == 1) ? 'copy' : 'copies' }} daily</strong>, and discover thousands of top-tier AI prompts.
        </p>

        @php
          $returnUrl = url()->full();
        @endphp

        @if (isset($settings->google_login) && $settings->google_login == 'on')
          <div class="mb-3">
            <a href="{{ url('oauth/google') }}?return={{ urlencode($returnUrl) }}" id="authCopyGoogleBtn" class="btn btn-lg btn-outline-dark w-100 rounded-pill py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size: 0.95rem; font-weight: 600; border-color: #e2e8f0;">
              <img src="{{ url('public/img/google.svg') }}" width="20" height="20" alt="Google">
              <span>Continue with Google</span>
            </a>
          </div>
        @endif

        <div class="d-grid gap-2">
          <a href="{{ url('register') }}?return={{ urlencode($returnUrl) }}" id="authCopyRegisterBtn" class="btn btn-dark btn-lg rounded-pill py-2.5 fw-bold shadow-sm" style="font-size: 0.95rem;">
            Create Free Account
          </a>
          <a href="{{ url('login') }}?return={{ urlencode($returnUrl) }}" id="authCopyLoginBtn" class="btn btn-light btn-lg rounded-pill py-2.5 fw-semibold border" style="font-size: 0.95rem;">
            Log In
          </a>
        </div>

        <div class="mt-4 pt-2 border-top text-muted small">
          <i class="bi bi-shield-check text-success me-1"></i> Free forever &bull; No credit card required
        </div>
      </div>
    </div>
  </div>
</div>
@endguest
