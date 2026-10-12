<!-- Free Prompts and Premium Access Section -->
<section class="section py-4 py-md-5 free-vs-pro-section position-relative">
  <div class="container px-3 px-md-4">
    <!-- Header -->
    <div class="text-center mb-4 mb-md-5">
      <div class="text-uppercase fw-bold text-muted small mb-2" style="letter-spacing: 0.14em; font-size: clamp(0.72rem, 1.8vw, 0.8rem);">
        START CREATING, THEN EXPLORE MORE
      </div>
      <h2 class="fw-bold title-custom mb-3" style="font-size: clamp(1.6rem, 3.8vw, 2.5rem); line-height: 1.25;">
        Find Your Next Product Photography Idea
      </h2>
      <p class="lead text-muted mx-auto mb-0" style="max-width: 720px; font-size: clamp(0.92rem, 2vw, 1.02rem); line-height: 1.65;">
        Not sure which style suits your product? Start with the free prompts, explore the results, and discover the creative possibilities of adaptive AI product photography.
      </p>
    </div>

    <!-- Cards Row -->
    <div class="row g-3 g-md-4 justify-content-center align-items-stretch mb-3">
      <!-- Card 1: Start Free -->
      <div class="col-12 col-md-6 col-lg-5">
        <div class="card h-100 p-3 p-sm-4 p-lg-5 rounded-4 border bg-card-custom shadow-xs d-flex flex-column justify-content-between free-tier-card">
          <div>
            <h3 class="fw-bold title-custom mb-2 mb-md-3" style="font-size: clamp(1.3rem, 2.5vw, 1.5rem);">Start Free</h3>
            <p class="text-muted mb-4" style="line-height: 1.65; font-size: 0.95rem;">
              Explore available free prompts and experiment with product photography concepts using a supported AI image generator.
            </p>
          </div>
          <div class="pt-2">
            <a href="{{ url('prompts/free') }}" class="btn btn-main rounded-pill btn-custom px-4 py-2.5 fw-semibold shadow-sm text-nowrap d-inline-flex align-items-center justify-content-center w-100 w-sm-auto">
              Explore Free Prompts
            </a>
          </div>
        </div>
      </div>

      <!-- Card 2: Explore Pro -->
      <div class="col-12 col-md-6 col-lg-5">
        <div class="card h-100 p-3 p-sm-4 p-lg-5 rounded-4 border-0 shadow-sm d-flex flex-column justify-content-between text-white pro-tier-card" style="background-color: #00d690 !important;">
          <div>
            <h3 class="fw-bold text-white mb-2 mb-md-3" style="font-size: clamp(1.3rem, 2.5vw, 1.5rem);">Explore Pro</h3>
            <p class="text-white mb-4" style="line-height: 1.65; font-size: 0.95rem; opacity: 0.95;">
              Unlock the additional prompt access and creative options included in the Pro plan.
            </p>
          </div>
          <div class="pt-2">
            <a href="{{ url('pricing') }}" class="btn bg-white rounded-pill px-4 py-2.5 fw-bold shadow-sm text-nowrap d-inline-flex align-items-center justify-content-center w-100 w-sm-auto" style="color: #0d1117 !important;">
              View Plans &amp; Pricing
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footnote -->
    <div class="text-center mt-3">
      <small class="text-muted" style="font-style: italic; font-size: 0.82rem;">
        Check the pricing page for the current plan details and included features.
      </small>
    </div>
  </div>
</section>

<style>
.free-tier-card {
  background-color: #ffffff;
  border-color: #e9ecef !important;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.free-tier-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
}

.pro-tier-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  box-shadow: 0 10px 30px rgba(0, 214, 144, 0.25) !important;
}

.pro-tier-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 36px rgba(0, 214, 144, 0.35) !important;
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .free-tier-card {
  background-color: #1a1e24 !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-bs-theme="dark"] .free-tier-card:hover {
  border-color: rgba(0, 214, 144, 0.3) !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
}
</style>
