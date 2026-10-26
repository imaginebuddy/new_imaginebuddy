@extends('layouts.app')

@section('title', 'Subscription Confirmed - ' . $settings->title)

@section('content')
<div class="py-5 bg-light-surface" style="background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%); min-height: 75vh; display: flex; align-items: center;">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Main Elevated Light Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 text-center" style="background-color: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06) !important;">
                    
                    <!-- Success Icon Glow Ring -->
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3 mx-auto" style="background-color: #ecfdf5; border: 8px solid #f0fdf4;">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 58px; height: 58px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 30px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                            <i class="bi-check-lg"></i>
                        </span>
                    </div>

                    <!-- Verified Pill -->
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 mb-3 fw-semibold" style="font-size: 13px;">
                            <i class="bi-patch-check-fill me-1"></i> Verified &bull; Welcome Email Dispatched
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="fw-bold mb-3" style="color: #0f172a; font-size: 2.25rem; letter-spacing: -0.5px;">
                        You're Subscribed!
                    </h1>

                    <!-- Description -->
                    <p class="mb-4 mx-auto" style="color: #475569; font-size: 1.05rem; line-height: 1.65; max-width: 620px;">
                        Your email address has been verified. Welcome to the Imagine Buddy creator community! We've also dispatched your welcome email with direct access to your free prompt vault.
                    </p>

                    <!-- Free Commercial Prompt Sample Box -->
                    <div class="card p-3 p-md-4 text-start mb-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <span class="fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                                    <i class="bi-stars text-warning fs-5"></i> Free Commercial Prompt Sample:
                                </span>
                                <small class="text-muted d-block mt-1">Copy and paste this calibrated recipe into Midjourney v6, SDXL, or ChatGPT:</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1_5 d-inline-flex align-items-center gap-1 shadow-sm" id="btnCopyPrompt" onclick="copyPromptSample()">
                                <i class="bi-clipboard" id="iconCopyPrompt"></i>
                                <span id="labelCopyPrompt" class="fw-semibold small">Copy Prompt</span>
                            </button>
                        </div>

                        <!-- Prompt Recipe Monospace Box -->
                        <div class="p-3 rounded font-monospace small user-select-all my-2" id="promptSampleText" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; line-height: 1.6; font-size: 13px;">Luxury cosmetic bottle mockup resting on obsidian black marble, surrounded by delicate water ripples and soft morning volumetric mist, studio softbox rim lighting, hyper-realistic, 8k resolution, shot on Hasselblad H6D-100c, 85mm lens, f/1.8 --ar 16:9 --style raw --v 6.0</div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <a href="{{ url('prompts/free') }}" class="btn btn-custom rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="bi-grid me-1"></i> Browse 1,000+ Free Prompts
                            </a>
                            <a href="{{ url('photoshoots') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                                <i class="bi-camera me-1"></i> Explore AI Photoshoots
                            </a>
                        </div>
                    </div>

                    <!-- Help / Support Footer Note -->
                    <div class="small pt-2" style="color: #64748b;">
                        Need help or have prompt suggestions? Reply directly to any of our weekly emails or <a href="{{ url('contact') }}" class="text-dark fw-semibold text-decoration-underline">contact our support team</a>.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
function copyPromptSample() {
    const textEl = document.getElementById('promptSampleText');
    const labelEl = document.getElementById('labelCopyPrompt');
    const iconEl = document.getElementById('iconCopyPrompt');
    const btnEl = document.getElementById('btnCopyPrompt');

    if (!textEl) return;

    const textToCopy = textEl.innerText.trim();
    navigator.clipboard.writeText(textToCopy).then(function() {
        labelEl.textContent = 'Copied!';
        iconEl.className = 'bi-check2 text-success';
        btnEl.classList.add('border-success', 'bg-success-subtle');

        setTimeout(function() {
            labelEl.textContent = 'Copy Prompt';
            iconEl.className = 'bi-clipboard';
            btnEl.classList.remove('border-success', 'bg-success-subtle');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endsection
