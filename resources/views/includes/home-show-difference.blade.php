<!-- Show the Difference Section -->
<section class="section py-4 py-md-5 show-difference-section position-relative border-top border-bottom">
  <div class="container px-3 px-md-4">
    <!-- Header -->
    <div class="mb-4 mb-md-5 text-center mx-auto w-100" style="max-width: 100%;">
      <div class="text-uppercase fw-bold text-muted small mb-2 show-diff-eyebrow" style="letter-spacing: 0.14em; font-size: clamp(0.72rem, 1.8vw, 0.8rem);">
        WHAT MAKES IMAGINEBUDDY DIFFERENT?
      </div>
      <h2 class="fw-bold title-custom mb-3 show-diff-title">
        One Photography Style. <span class="font-serif-italic">Adapted to</span> Your Product.
      </h2>
      <p class="text-muted mb-2 mx-auto" style="line-height: 1.7; font-size: clamp(0.95rem, 2vw, 1.02rem); max-width: 860px;">
        Generic prompts often need manual adjustments for different products. ImagineBuddy's adaptive prompts are designed to guide AI to create product-focused visuals based on the product you upload.
      </p>
      <p class="text-muted mb-0 mx-auto" style="line-height: 1.7; font-size: clamp(0.92rem, 2vw, 0.98rem); max-width: 860px;">
        Whether it's skincare, jewellery, fashion, food, or home d&eacute;cor, explore creative photography concepts tailored to your product.
      </p>
    </div>

    <!-- Comparison Cards -->
    <div class="row g-3 g-md-4 align-items-stretch mb-3 mb-md-4">
      <!-- Card 1: Generic Prompts -->
      <div class="col-12 col-md-6">
        <div class="card h-100 p-4 p-lg-5 diff-card-generic shadow-xs">
          <h3 class="fw-bold title-custom mb-3 mb-md-4" style="font-size: clamp(1.2rem, 2.4vw, 1.35rem);">Generic Prompts</h3>
          <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="diff-cross-icon fw-semibold flex-shrink-0">✕</span>
              <span class="diff-text-muted">
                Often require manual edits for different products.
              </span>
            </li>
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="diff-cross-icon fw-semibold flex-shrink-0">✕</span>
              <span class="diff-text-muted">
                May need adjustments to colours, props, backgrounds, and lighting.
              </span>
            </li>
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="diff-cross-icon fw-semibold flex-shrink-0">✕</span>
              <span class="diff-text-muted">
                Can require repeated experimentation to achieve the desired look.
              </span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Card 2: Adaptive Prompts by ImagineBuddy -->
      <div class="col-12 col-md-6">
        <div class="card h-100 p-4 p-lg-5 border-0 text-white diff-card-adaptive" style="background-color: #00d690 !important;">
          <h3 class="fw-bold text-white mb-3 mb-md-4" style="font-size: clamp(1.2rem, 2.4vw, 1.35rem);">Adaptive Prompts by ImagineBuddy</h3>
          <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="text-white fw-bold flex-shrink-0" style="font-size: 1.1rem; line-height: 1.3;">✓</span>
              <span class="text-white" style="line-height: 1.6; font-size: 0.95rem; opacity: 0.98;">
                Designed to respond to the uploaded product.
              </span>
            </li>
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="text-white fw-bold flex-shrink-0" style="font-size: 1.1rem; line-height: 1.3;">✓</span>
              <span class="text-white" style="line-height: 1.6; font-size: 0.95rem; opacity: 0.98;">
                Guide product-focused styling, lighting, props, and backgrounds.
              </span>
            </li>
            <li class="d-flex align-items-start gap-2 gap-sm-3">
              <span class="text-white fw-bold flex-shrink-0" style="font-size: 1.1rem; line-height: 1.3;">✓</span>
              <span class="text-white" style="line-height: 1.6; font-size: 0.95rem; opacity: 0.98;">
                Help you explore different creative directions with less manual prompt editing.
              </span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- CTA & Subtext Row matching exact layout from reference image -->
    <div class="diff-cta-row d-flex flex-wrap align-items-center justify-content-center text-center gap-3 gap-md-4 mt-4 pt-1">
      <a href="{{ url('latest') }}" class="btn-explore-adaptive">
        Explore Adaptive Prompts
      </a>
      <span class="diff-disclaimer-text">
        Results depend on the AI image generator, the prompt, and the uploaded product image.
      </span>
    </div>
  </div>
</section>

<style>
.show-difference-section {
  background-color: #fafbfc;
  border-color: #e9ecef !important;
}

.diff-card-generic {
  background-color: #ffffff;
  border: 1px solid #e5e7eb !important;
  border-radius: 20px !important;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.diff-card-generic:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
}

.diff-card-adaptive {
  border-radius: 20px !important;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  box-shadow: 0 10px 30px rgba(0, 214, 144, 0.2) !important;
}

.diff-card-adaptive:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 35px rgba(0, 214, 144, 0.3) !important;
}

.diff-cross-icon {
  color: #4b5563;
  font-size: 1.05rem;
  line-height: 1.3;
}

.diff-text-muted {
  color: #4b5563;
  line-height: 1.6;
  font-size: 0.95rem;
}

/* Exact Pill Button Matching Screenshot */
.btn-explore-adaptive {
  background-color: #00d690 !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 50rem !important;
  padding: 10px 26px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  line-height: 1.5 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  text-decoration: none !important;
  white-space: nowrap !important;
  box-shadow: none !important;
  transition: background-color 0.2s ease, transform 0.15s ease !important;
}

.btn-explore-adaptive:hover {
  background-color: #00bf80 !important;
  color: #ffffff !important;
  transform: translateY(-1px);
}

.diff-disclaimer-text {
  font-size: 13.5px;
  color: #6b7280;
  font-style: italic;
  line-height: 1.5;
}

.show-diff-title {
  font-size: clamp(1.4rem, 2.6vw, 2.35rem);
  line-height: 1.25;
}

@media (min-width: 992px) {
  .show-diff-title {
    white-space: nowrap !important;
  }
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .show-difference-section {
  background-color: #12151a !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .diff-card-generic {
  background-color: #1a1e24 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .diff-card-generic:hover {
  border-color: rgba(0, 214, 144, 0.3) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
}

[data-bs-theme="dark"] .diff-cross-icon,
[data-bs-theme="dark"] .diff-text-muted {
  color: #94a3b8 !important;
}

[data-bs-theme="dark"] .diff-disclaimer-text {
  color: #94a3b8 !important;
}
</style>
