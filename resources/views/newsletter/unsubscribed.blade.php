@extends('layouts.app')

@section('title', 'Unsubscribed - ' . $settings->title)

@section('content')
<div class="py-5 bg-light-surface" style="background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%); min-height: 75vh; display: flex; align-items: center;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 text-center">
                <div class="card border-0 shadow-sm p-4 p-md-5" style="background-color: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06) !important;">
                    
                    <!-- Icon -->
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3 mx-auto" style="background-color: #f1f5f9; border: 8px solid #f8fafc;">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 58px; height: 58px; background: #64748b; color: #ffffff; font-size: 28px;">
                            <i class="bi-envelope-x"></i>
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="fw-bold mb-3" style="color: #0f172a; font-size: 2rem; letter-spacing: -0.5px;">
                        You've Been Unsubscribed
                    </h1>

                    <!-- Lead text -->
                    <p class="mb-4" style="color: #475569; font-size: 1.05rem; line-height: 1.65;">
                        @if(!empty($email))
                            <strong>{{ $email }}</strong> has been removed from our marketing and updates mailing list.
                        @else
                            You have been removed from our marketing mailing list.
                        @endif
                        You will no longer receive weekly prompt drops, marketing dispatches, or promotional newsletters from Imagine Buddy.
                    </p>

                    <!-- Informational Box -->
                    <div class="p-3 mb-4 rounded text-start" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px; color: #64748b; line-height: 1.6;">
                        <div class="d-flex gap-2">
                            <i class="bi-info-circle text-primary fs-6 mt-0"></i>
                            <div>
                                <strong>Important Security Note:</strong> Essential transactional messages (such as two-factor verification codes, password resets, and deposit receipts) will continue to function normally if you hold an active account.
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi-arrow-left me-1"></i> Return to Imagine Buddy Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
