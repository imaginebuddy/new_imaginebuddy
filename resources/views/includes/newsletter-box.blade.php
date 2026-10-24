<style>
/* ==========================================================================
   Imagine Buddy - Premium Newsletter Component Styles
   ========================================================================== */
:root {
  --ib-mint: #00d690;
  --ib-mint-hover: #00e69d;
  --ib-mint-glow: rgba(0, 214, 144, 0.25);
  --ib-mint-soft: rgba(0, 214, 144, 0.12);
  --ib-mint-border: rgba(0, 214, 144, 0.28);
}

.ib-newsletter-card {
  position: relative;
  border-radius: 20px;
  background: linear-gradient(135deg, #171b22 0%, #1e242d 55%, #15181e 100%);
  border: 1px solid rgba(255, 255, 255, 0.09);
  box-shadow: 0 20px 45px -12px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
  padding: 2.5rem 2.75rem;
  overflow: hidden;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Subtle ambient backdrop illumination */
.ib-newsletter-card::before {
  content: "";
  position: absolute;
  top: -80px;
  right: -60px;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle, rgba(0, 214, 144, 0.13) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

.ib-newsletter-card::after {
  content: "";
  position: absolute;
  bottom: -90px;
  left: 20%;
  width: 280px;
  height: 280px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

.ib-newsletter-inner {
  position: relative;
  z-index: 1;
}

/* Badge & pulse indicator */
.ib-newsletter-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--ib-mint-soft);
  border: 1px solid var(--ib-mint-border);
  color: var(--ib-mint);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 0.35rem 0.85rem;
  border-radius: 50px;
}

.ib-pulse-dot {
  width: 7px;
  height: 7px;
  background-color: var(--ib-mint);
  border-radius: 50%;
  box-shadow: 0 0 0 0 rgba(0, 214, 144, 0.7);
  animation: ib-pulse 2s infinite;
}

@keyframes ib-pulse {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(0, 214, 144, 0.7);
  }
  70% {
    transform: scale(1);
    box-shadow: 0 0 0 7px rgba(0, 214, 144, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(0, 214, 144, 0);
  }
}

/* Typography */
.ib-newsletter-title {
  color: #ffffff;
  font-size: clamp(1.4rem, 2.5vw, 1.85rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  line-height: 1.25;
  margin-bottom: 0.65rem;
}

.ib-newsletter-desc {
  color: #a0aec0;
  font-size: 0.95rem;
  line-height: 1.6;
  max-width: 520px;
  margin-bottom: 1.25rem;
}

/* Trust indicators */
.ib-trust-badge {
  display: inline-flex;
  align-items: center;
  font-size: 0.8rem;
  color: #cbd5e1;
  font-weight: 500;
}

.ib-trust-badge i {
  color: var(--ib-mint);
  margin-right: 0.35rem;
  font-size: 0.9rem;
}

/* Form & Input Capsule */
.ib-input-capsule {
  position: relative;
  display: flex;
  align-items: center;
  background: rgba(14, 18, 24, 0.75);
  border: 1.5px solid rgba(255, 255, 255, 0.12);
  border-radius: 50px;
  padding: 5px;
  backdrop-filter: blur(8px);
  transition: all 0.25s ease;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
}

.ib-input-capsule:focus-within {
  border-color: var(--ib-mint);
  box-shadow: 0 0 0 4px var(--ib-mint-glow), 0 6px 20px rgba(0, 0, 0, 0.35);
  background: rgba(14, 18, 24, 0.95);
}

.ib-icon-prefix {
  position: absolute;
  left: 18px;
  color: #718096;
  font-size: 1.1rem;
  pointer-events: none;
  transition: color 0.2s ease;
  z-index: 2;
}

.ib-input-capsule:focus-within .ib-icon-prefix {
  color: var(--ib-mint);
}

.ib-email-input {
  flex: 1;
  height: 48px;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  color: #ffffff !important;
  font-size: 0.95rem;
  padding-left: 48px;
  padding-right: 12px;
}

.ib-email-input::placeholder {
  color: #8896a6 !important;
  opacity: 1;
  font-size: 0.92rem;
}

.ib-email-input:focus {
  outline: none !important;
  box-shadow: none !important;
}

/* Subscribe Action Button */
.ib-btn-subscribe {
  height: 48px;
  padding: 0 1.6rem;
  background: var(--ib-mint) !important;
  color: #062316 !important;
  border: none !important;
  border-radius: 50px !important;
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: -0.01em;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 14px rgba(0, 214, 144, 0.35);
}

.ib-btn-subscribe:hover {
  background: var(--ib-mint-hover) !important;
  color: #041b11 !important;
  transform: translateY(-1.5px);
  box-shadow: 0 6px 20px rgba(0, 214, 144, 0.5);
}

.ib-btn-subscribe:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(0, 214, 144, 0.3);
}

.ib-btn-subscribe:disabled {
  opacity: 0.75;
  cursor: not-allowed;
  transform: none !important;
}

.ib-btn-subscribe i {
  transition: transform 0.2s ease;
}

.ib-btn-subscribe:hover i {
  transform: translateX(3px);
}

/* Micro Trust Line */
.ib-micro-trust {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
  padding: 0 0.5rem;
  font-size: 0.78rem;
  color: #8c9ba5;
}

.ib-micro-trust i {
  color: var(--ib-mint);
  margin-right: 0.3rem;
}

/* Feedback alerts */
.ib-alert-box {
  border-radius: 10px;
  font-size: 0.88rem;
  font-weight: 500;
  padding: 0.85rem 1rem;
  margin-top: 0.85rem;
  text-align: left;
  line-height: 1.5;
}

.ib-alert-success {
  background: rgba(0, 214, 144, 0.12) !important;
  border: 1px solid rgba(0, 214, 144, 0.35) !important;
  color: #98ffdb !important;
}

.ib-alert-danger {
  background: rgba(239, 68, 68, 0.12) !important;
  border: 1px solid rgba(239, 68, 68, 0.35) !important;
  color: #fca5a5 !important;
}

/* Responsive Breakpoints */
@media (max-width: 991.98px) {
  .ib-newsletter-card {
    padding: 2.25rem 2rem;
  }
  .ib-newsletter-title {
    text-align: center;
  }
  .ib-newsletter-desc {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
  }
  .ib-trust-indicators {
    justify-content: center;
    margin-bottom: 1.5rem;
  }
  .ib-header-wrap {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .ib-newsletter-card {
    padding: 1.75rem 1.25rem;
    border-radius: 16px;
  }
  .ib-input-capsule {
    flex-direction: column;
    background: transparent;
    border: none;
    padding: 0;
    box-shadow: none;
    gap: 0.65rem;
  }
  .ib-input-capsule:focus-within {
    box-shadow: none;
    background: transparent;
    border: none;
  }
  .ib-input-wrap {
    position: relative;
    width: 100%;
  }
  .ib-email-input {
    width: 100%;
    background: rgba(14, 18, 24, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 50px !important;
    height: 50px;
    padding-left: 46px;
    padding-right: 16px;
  }
  .ib-email-input:focus {
    border-color: var(--ib-mint) !important;
    box-shadow: 0 0 0 3px var(--ib-mint-glow) !important;
  }
  .ib-btn-subscribe {
    width: 100%;
    height: 50px;
  }
  .ib-micro-trust {
    justify-content: center;
    text-align: center;
    gap: 0.35rem;
  }
}
</style>

<div class="ib-newsletter-card">
  <div class="ib-newsletter-inner">
    <div class="row align-items-center g-4">
      
      <!-- Left Column: Compelling Headline, Value Proposition & Trust Badges -->
      <div class="col-lg-6">
        <div class="ib-header-wrap">
          <div class="mb-2">
            <span class="ib-newsletter-badge">
              <span class="ib-pulse-dot"></span>
              Free Weekly Prompt Drop
            </span>
          </div>

          <h3 class="ib-newsletter-title">
            Get 50+ Master Prompts &amp; AI Photoshoot Hacks
          </h3>

          <p class="ib-newsletter-desc">
            Join 10,000+ smart creators &amp; brands crafting commercial-grade AI photography with Google Gemini, ChatGPT, and Midjourney. Delivered fresh every Tuesday.
          </p>

          <div class="d-flex align-items-center flex-wrap gap-3 ib-trust-indicators">
            <span class="ib-trust-badge">
              <i class="bi-stars"></i> Curated Prompt Vault
            </span>
            <span class="ib-trust-badge">
              <i class="bi-shield-check"></i> Zero Spam Guarantee
            </span>
            <span class="ib-trust-badge">
              <i class="bi-unlock"></i> Instant Free Access
            </span>
          </div>
        </div>
      </div>

      <!-- Right Column: Interactive Conversion-Focused Subscribe Form -->
      <div class="col-lg-6">
        <form id="newsletterSubscribeForm" class="position-relative" novalidate>
          @csrf
          <!-- Honeypot for bot protection -->
          <input type="text" name="newsletter_check" style="display:none !important;" tabindex="-1" autocomplete="off">
          <input type="hidden" name="source" value="{{ request()->path() }}">

          <!-- Modern Unified Input Capsule -->
          <div class="ib-input-capsule">
            <div class="ib-input-wrap d-flex align-items-center flex-grow-1 position-relative">
              <i class="bi-envelope ib-icon-prefix"></i>
              <input type="email" 
                     name="email" 
                     id="newsletterEmailInput" 
                     class="form-control ib-email-input" 
                     placeholder="Enter your email address..." 
                     required 
                     autocomplete="email" 
                     aria-label="Email address for newsletter">
            </div>
            <button class="btn ib-btn-subscribe" type="submit" id="newsletterSubmitBtn" aria-label="Subscribe to newsletter">
              <span class="btn-text">
                Get Prompts <i class="bi-arrow-right"></i>
              </span>
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </div>

          <!-- Live Dynamic Feedback Alert -->
          <div id="newsletterAlert" class="ib-alert-box d-none" role="alert"></div>

          <!-- Micro Privacy & Deliverability Reassurance -->
          <div class="ib-micro-trust">
            <span>
              <i class="bi-patch-check-fill"></i> Verified deliverability &bull; No ads
            </span>
            <span>
              <i class="bi-box-arrow-right"></i> 1-Click unsubscribe anytime
            </span>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('newsletterSubscribeForm');
  if (!form) return;

  const emailInput = document.getElementById('newsletterEmailInput');
  const submitBtn = document.getElementById('newsletterSubmitBtn');
  const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
  const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;
  const alertBox = document.getElementById('newsletterAlert');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const email = emailInput.value.trim();
    if (!email) {
      showAlert('Please enter your email address.', 'ib-alert-danger', 'bi-exclamation-circle-fill');
      emailInput.focus();
      return;
    }

    // Email format validation (basic client check)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showAlert('Please provide a valid email format (e.g. name@example.com).', 'ib-alert-danger', 'bi-exclamation-circle-fill');
      emailInput.focus();
      return;
    }

    // Set Loading State
    submitBtn.disabled = true;
    if (spinner) spinner.classList.remove('d-none');
    if (btnText) btnText.innerHTML = 'Subscribing...';
    hideAlert();

    const formData = new FormData(form);

    fetch("{{ route('newsletter.subscribe') }}", {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(async (response) => {
      const data = await response.json();
      if (!response.ok) {
        throw new Error(data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Subscription could not be processed.'));
      }
      return data;
    })
    .then((data) => {
      showAlert(
        data.message || '🎉 Almost done! We just sent a confirmation link to your inbox. Click it to unlock your 50+ Prompts!',
        'ib-alert-success',
        'bi-check-circle-fill'
      );
      emailInput.value = '';
    })
    .catch((error) => {
      showAlert(
        error.message || 'Could not complete subscription. Please check your connection and try again.',
        'ib-alert-danger',
        'bi-exclamation-triangle-fill'
      );
    })
    .finally(() => {
      submitBtn.disabled = false;
      if (spinner) spinner.classList.add('d-none');
      if (btnText) btnText.innerHTML = 'Get Prompts <i class="bi-arrow-right"></i>';
    });
  });

  function showAlert(msg, alertClass, iconClass) {
    if (!alertBox) return;
    alertBox.className = 'ib-alert-box ' + alertClass;
    alertBox.innerHTML = '<i class="' + iconClass + ' me-2"></i>' + msg;
    alertBox.classList.remove('d-none');
  }

  function hideAlert() {
    if (!alertBox) return;
    alertBox.classList.add('d-none');
    alertBox.innerHTML = '';
  }
});
</script>
