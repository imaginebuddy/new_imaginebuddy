@extends('layouts.app')

@section('title', 'Unsubscribed - ' . $settings->title)

@section('content')
<div class="py-5 bg-dark-2 text-white">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 text-center">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-secondary text-white rounded-circle" style="width: 72px; height: 72px; font-size: 32px;">
                        <i class="bi-envelope-x"></i>
                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-3">You've Been Unsubscribed</h1>
                <p class="lead text-light mb-4">
                    {{ $email ? "({$email})" : "" }} You have been removed from our marketing mailing list. You won't receive promotional emails or newsletters from Imagine Buddy.
                </p>
                <div class="card bg-dark border-secondary p-4 text-center mb-4">
                    <p class="text-secondary small mb-3">
                        Note: Essential security notifications (e.g. two-factor verification codes and password resets) will still function if you have an active account.
                    </p>
                    <div>
                        <a href="{{ url('/') }}" class="btn btn-outline-light rounded-pill px-4">
                            <i class="bi-arrow-left me-1"></i> Return to Imagine Buddy Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
