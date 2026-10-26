@extends('layouts.app')

@section('title'){{ __('users.title_email_verify') . ' - ' }}@endsection

@section('content')
<div class="container-fluid">
  <div class="row">

    <div class="col-sm-6 px-0 d-none d-sm-block bg-auth"></div>

    <div class="col-sm-6 login-section-wrapper">
      <a href="{{ url('/') }}" class="mb-4 mb-lg-0 logo-login">
        <img src="{{ url('public/img', $settings->logo) }}" class="logoMain" alt="logo" width="150">
        <img src="{{ url('public/img', $settings->logo_light) }}" class="logoLight" alt="logo" width="150">
      </a>

      <div class="login-wrapper my-auto py-3">

        @include('errors.errors-forms')

        @if (session('status'))
          <div class="alert alert-success text-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
          </div>
        @endif

        @if (session('warning'))
          <div class="alert alert-warning text-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
          </div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger text-center mb-4">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
          </div>
        @endif

        <!-- Envelope Illustration Badge -->
        <div class="text-center mb-3">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
               style="width: 86px; height: 86px; background: rgba(99, 102, 241, 0.12); border: 2px solid rgba(99, 102, 241, 0.3); box-shadow: 0 8px 24px rgba(99, 102, 241, 0.18);">
            <i class="bi bi-envelope-paper-heart-fill" style="font-size: 40px; color: #6366f1;"></i>
          </div>
        </div>

        <h3 class="login-title text-center mb-2">{{ __('users.title_email_verify') }}</h3>

        <p class="text-muted text-center mb-4" style="font-size: 15px; line-height: 1.6;">
          @if (!empty($email))
            We've sent a confirmation email to <strong class="text-dark fw-bold">{{ $email }}</strong>.
          @else
            We've sent a confirmation email to your registered email address.
          @endif
          <br>Please check your inbox and click the verification link to activate your account.
        </p>

        <!-- Quick 3-step guide -->
        <div class="card border-0 mb-4" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.08) !important; border-radius: 12px;">
          <div class="card-body p-3">
            <div class="d-flex align-items-start mb-2">
              <span class="badge bg-primary rounded-circle me-2 mt-1" style="width: 20px; height: 20px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">1</span>
              <span class="small text-muted">Open your email inbox (look for an email from <strong>Imagine Buddy</strong>).</span>
            </div>
            <div class="d-flex align-items-start mb-2">
              <span class="badge bg-primary rounded-circle me-2 mt-1" style="width: 20px; height: 20px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">2</span>
              <span class="small text-muted">Click the <strong>"Verify Email Address"</strong> button in the message.</span>
            </div>
            <div class="d-flex align-items-start">
              <span class="badge bg-warning text-dark rounded-circle me-2 mt-1" style="width: 20px; height: 20px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">!</span>
              <span class="small text-muted">Can't find it? Check your <strong>Spam</strong> or <strong>Promotions</strong> folder.</span>
            </div>
          </div>
        </div>

        <!-- Resend Form -->
        <form method="POST" action="{{ route('register.verify.resend') }}" id="resendVerificationForm" class="mb-3">
          @csrf
          @if (!empty($email))
            <input type="hidden" name="email" value="{{ $email }}">
          @else
            <div class="form-floating mb-3">
              <input type="email" required class="form-control" id="resend_email_input" name="email" placeholder="name@example.com">
              <label for="resend_email_input">{{ __('auth.email') }}</label>
            </div>
          @endif

          <button type="submit" class="btn btn-outline-primary btn-lg w-100" id="btnResendVerification">
            <i class="bi bi-arrow-repeat me-1"></i> Resend Confirmation Email
          </button>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <a href="{{ url('login') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> {{ __('auth.login') }}
          </a>
          <a href="{{ url('register') }}" class="text-muted small text-decoration-none">
            {{ __('auth.sign_up') }} with another email
          </a>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
  $('#resendVerificationForm').on('submit', function() {
    var btn = $('#btnResendVerification');
    btn.prop('disabled', true);
    btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending confirmation...');
  });
</script>
@endsection
