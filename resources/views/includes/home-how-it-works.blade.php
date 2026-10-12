<!-- How It Works Section -->
<section class="section py-4 py-md-5 how-it-works-section position-relative border-top border-bottom">
  <div class="container px-3 px-md-4">
    <!-- Header -->
    <div class="text-center mb-4 mb-md-5">
      <div class="text-uppercase fw-bold text-muted small mb-2 how-it-works-eyebrow" style="letter-spacing: 0.14em; font-size: clamp(0.72rem, 1.8vw, 0.8rem);">
        YOUR CREATIVE WORKFLOW, SIMPLIFIED
      </div>
      <h2 class="fw-bold title-custom mb-3" style="font-size: clamp(1.65rem, 3.8vw, 2.5rem); line-height: 1.25;">
        From Product Photo to <span class="font-serif-italic">AI Photoshoot</span> in 3 Steps
      </h2>
    </div>

    <!-- 3 Steps Cards -->
    <div class="row g-3 g-md-4 justify-content-center mb-4 mb-md-5">
      <!-- Step 1 -->
      <div class="col-12 col-md-4">
        <div class="card h-100 p-3 p-sm-4 p-lg-5 rounded-4 border bg-card-custom shadow-xs how-it-works-card">
          <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
            <span class="step-icon-box d-inline-flex align-items-center justify-content-center rounded-circle fs-4">
              <i class="bi bi-upload"></i>
            </span>
            <span class="badge-step text-muted fw-bold">
              STEP 1
            </span>
          </div>
          <h3 class="fw-bold title-custom mb-2 mb-md-3" style="font-size: clamp(1.1rem, 2.2vw, 1.25rem);">Upload Your Product</h3>
          <p class="text-muted mb-0" style="line-height: 1.65; font-size: 0.92rem;">
            Choose a clear photo of your product. It can be skincare, a handbag, jewellery, food, electronics, or another product.
          </p>
        </div>
      </div>

      <!-- Step 2 -->
      <div class="col-12 col-md-4">
        <div class="card h-100 p-3 p-sm-4 p-lg-5 rounded-4 border bg-card-custom shadow-xs how-it-works-card">
          <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
            <span class="step-icon-box d-inline-flex align-items-center justify-content-center rounded-circle fs-4">
              <i class="bi bi-copy"></i>
            </span>
            <span class="badge-step text-muted fw-bold">
              STEP 2
            </span>
          </div>
          <h3 class="fw-bold title-custom mb-2 mb-md-3" style="font-size: clamp(1.1rem, 2.2vw, 1.25rem);">Copy an Adaptive Prompt</h3>
          <p class="text-muted mb-0" style="line-height: 1.65; font-size: 0.92rem;">
            Explore ImagineBuddy's photography concepts and copy a prompt that matches the style you want to create.
          </p>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="col-12 col-md-4">
        <div class="card h-100 p-3 p-sm-4 p-lg-5 rounded-4 border bg-card-custom shadow-xs how-it-works-card">
          <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
            <span class="step-icon-box d-inline-flex align-items-center justify-content-center rounded-circle fs-4">
              <i class="bi bi-stars"></i>
            </span>
            <span class="badge-step text-muted fw-bold">
              STEP 3
            </span>
          </div>
          <h3 class="fw-bold title-custom mb-2 mb-md-3" style="font-size: clamp(1.1rem, 2.2vw, 1.25rem);">Generate Your Visual</h3>
          <p class="text-muted mb-0" style="line-height: 1.65; font-size: 0.92rem;">
            Upload your product to ChatGPT or Gemini, paste the prompt, and generate a new product photography concept.
          </p>
        </div>
      </div>
    </div>

    <!-- CTA & Subtext -->
    <div class="text-center">
      <a href="{{ url('prompts/free') }}" class="btn btn-main rounded-pill btn-custom px-4 px-md-5 py-2.5 arrow shadow-sm fw-semibold">
        Try Your First Prompt
      </a>
      <p class="text-muted mt-2 mt-md-3 mb-0 small" style="font-style: italic; font-size: 0.85rem;">
        No need to start every creative idea from a blank prompt.
      </p>
    </div>
  </div>
</section>

<style>
.how-it-works-section {
  background-color: #fafbfc;
  border-color: #e9ecef !important;
}

.how-it-works-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  background-color: #ffffff;
  border-color: #e9ecef !important;
}

.how-it-works-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
}

.step-icon-box {
  width: 44px;
  height: 44px;
  background-color: rgba(0, 214, 144, 0.1);
  color: #00d690;
}

.badge-step {
  letter-spacing: 0.08em;
  font-size: 0.72rem;
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .how-it-works-section {
  background-color: #12151a !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .how-it-works-card {
  background-color: #1a1e24 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .how-it-works-card:hover {
  border-color: rgba(0, 214, 144, 0.3) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
}

[data-bs-theme="dark"] .step-icon-box {
  background-color: rgba(0, 214, 144, 0.15) !important;
  color: #00d690 !important;
}

[data-bs-theme="dark"] .badge-step {
  color: #94a3b8 !important;
}
</style>
