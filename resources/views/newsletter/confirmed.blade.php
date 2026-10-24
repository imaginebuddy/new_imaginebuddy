@extends('layouts.app')

@section('title', 'Subscription Confirmed - ' . $settings->title)

@section('content')
<div class="py-5 bg-dark-2 text-white">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 72px; height: 72px; font-size: 32px;">
                        <i class="bi-check-lg"></i>
                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-3">You're Subscribed!</h1>
                <p class="lead text-light mb-4">
                    Your email address has been verified. Welcome to the Imagine Buddy creator community! We've also dispatched your welcome email with direct vault links.
                </p>

                <div class="card bg-dark border-secondary p-4 text-start mb-4 shadow">
                    <h5 class="text-white mb-2"><i class="bi-stars text-warning me-2"></i> Free Commercial Prompt Sample:</h5>
                    <p class="text-secondary small mb-3">Copy and paste this into Midjourney v6 or SDXL:</p>
                    <div class="p-3 bg-dark-2 rounded border border-secondary font-monospace text-light small user-select-all mb-3">
                        Luxury cosmetic bottle mockup resting on obsidian black marble, surrounded by delicate water ripples and soft morning volumetric mist, studio softbox rim lighting, hyper-realistic, 8k resolution, shot on Hasselblad H6D-100c, 85mm lens, f/1.8 --ar 16:9 --style raw --v 6.0
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ url('prompts/free') }}" class="btn btn-custom rounded-pill px-4">
                            <i class="bi-grid me-1"></i> Browse 1,000+ Free Prompts
                        </a>
                        <a href="{{ url('photoshoots') }}" class="btn btn-outline-light rounded-pill px-4">
                            <i class="bi-camera me-1"></i> Explore AI Photoshoots
                        </a>
                    </div>
                </div>

                <div class="text-secondary small">
                    Need help or have prompt suggestions? Reply directly to any of our weekly emails or <a href="{{ url('contact') }}" class="text-white text-decoration-underline">contact our support team</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
