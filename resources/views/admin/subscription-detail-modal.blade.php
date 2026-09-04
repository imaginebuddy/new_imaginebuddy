<div class="modal-header border-bottom-0 pb-0">
  <div>
    <h5 class="modal-title fw-bold mb-1">
      <i class="bi-credit-card-2-front me-1 text-primary"></i>
      Subscription #{{ $subscription->id }}
    </h5>
    <span class="badge {{ $subscription->status_detail['badge'] }} px-2 py-1">
      {{ $subscription->status_detail['label'] }}
    </span>
    @if ($subscription->days_remaining)
      <span class="text-muted small ms-2">
        <i class="bi-clock-history me-1"></i> {{ $subscription->days_remaining }}
      </span>
    @endif
  </div>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
  <div class="row g-4">
    
    <!-- User Information Card -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi-person-circle me-1 text-secondary"></i> Subscriber Information
        </h6>
        @if ($subscription->user)
          <div class="d-flex align-items-center mb-3">
            <img src="{{ Storage::url(config('path.avatar') . $subscription->user->avatar) }}" width="48" height="48" class="rounded-circle me-2 border" />
            <div>
              <div class="fw-bold text-dark">{{ $subscription->user->name ?: $subscription->user->username }}</div>
              <div class="text-muted small">&#64;{{ $subscription->user->username }} &bull; ID #{{ $subscription->user->id }}</div>
            </div>
          </div>
          <ul class="list-unstyled small mb-0">
            <li class="mb-2 d-flex justify-content-between">
              <span class="text-muted">Email:</span>
              <strong class="text-dark">{{ $subscription->user->email }}</strong>
            </li>
            <li class="mb-2 d-flex justify-content-between">
              <span class="text-muted">Profile Country:</span>
              <span class="badge bg-secondary-subtle text-dark border">
                {{ $subscription->user->country() ? $subscription->user->country()->country_name . ' (' . $subscription->user->country()->country_code . ')' : 'Not Set' }}
              </span>
            </li>
            <li class="mb-2 d-flex justify-content-between">
              <span class="text-muted">Billing Country:</span>
              <span class="badge bg-dark text-white">
                {{ $subscription->country_code ?: ($subscription->payment_gateway === 'Razorpay' ? 'IN' : 'US') }}
              </span>
            </li>
            <li class="d-flex justify-content-between">
              <span class="text-muted">Registered IP:</span>
              <code class="text-muted">{{ $subscription->user->ip ?: '—' }}</code>
            </li>
          </ul>
        @else
          <p class="text-muted small m-0">User record deleted (ID: {{ $subscription->user_id }})</p>
        @endif
      </div>
    </div>

    <!-- Plan Specifications Card -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi-box me-1 text-secondary"></i> Plan Specifications
        </h6>
        <div class="mb-3">
          <div class="fs-5 fw-bold text-dark">{{ $subscription->plan ? $subscription->plan->name : 'Pro Plan' }}</div>
          <span class="badge bg-primary-subtle text-primary border text-capitalize">
            {{ $subscription->interval === 'year' ? 'Yearly Billing' : 'Monthly Billing' }}
          </span>
        </div>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Plan ID:</span>
            <code>{{ $subscription->stripe_price }}</code>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Daily Limit:</span>
            <strong>{{ $subscription->plan ? $subscription->plan->download_limits : 100 }} prompts/day</strong>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Monthly Limit:</span>
            <strong>{{ $subscription->plan ? number_format($subscription->plan->downloads_per_month) : 300 }} downloads/mo</strong>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-muted">Auto-Renewal:</span>
            <span class="badge {{ $subscription->payment_gateway === 'Razorpay' ? 'bg-secondary' : ($subscription->cancelled === 'yes' ? 'bg-danger' : 'bg-success') }}">
              {{ $subscription->payment_gateway === 'Razorpay' ? 'Manual (Prepaid)' : ($subscription->cancelled === 'yes' ? 'Cancelled' : 'Enabled') }}
            </span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Payment & Gateway Card -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi-wallet2 me-1 text-secondary"></i> Payment & Gateway
        </h6>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <span class="badge {{ $subscription->payment_gateway === 'Razorpay' ? 'bg-dark' : ($subscription->payment_gateway === 'PayPal' ? 'bg-primary' : 'bg-info') }} px-2 py-1 fs-6">
              {{ $subscription->payment_gateway ?: 'Payment Gateway' }}
            </span>
            @if ($subscription->payment_method)
              <span class="badge bg-secondary-subtle text-dark border ms-1 text-uppercase">
                {{ $subscription->payment_method }}
              </span>
            @endif
          </div>
          <div class="fs-4 fw-bold text-dark">
            {{ $subscription->formatted_amount }}
          </div>
        </div>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Currency:</span>
            <strong>{{ $subscription->currency ?: ($subscription->payment_gateway === 'Razorpay' ? 'INR (₹)' : 'USD ($)') }}</strong>
          </li>
          <li class="mb-2 d-flex justify-content-between align-items-center">
            <span class="text-muted">Payment ID:</span>
            <code class="user-select-all">{{ $subscription->last_payment ?: ($subscription->stripe_id ?: ($subscription->paypal_id ?: '—')) }}</code>
          </li>
          @if ($subscription->gateway_order_id)
            <li class="mb-2 d-flex justify-content-between align-items-center">
              <span class="text-muted">Gateway Order ID:</span>
              <code class="user-select-all">{{ $subscription->gateway_order_id }}</code>
            </li>
          @endif
          @if ($subscription->paypal_id)
            <li class="mb-2 d-flex justify-content-between align-items-center">
              <span class="text-muted">PayPal Sub ID:</span>
              <code class="user-select-all">{{ $subscription->paypal_id }}</code>
            </li>
          @endif
          <li class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <span class="text-muted">Invoice Record:</span>
            @if ($subscription->invoice)
              <a href="{{ url('invoice', $subscription->invoice->id) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0">
                <i class="bi-receipt me-1"></i> View Invoice #{{ str_pad($subscription->invoice->id, 4, '0', STR_PAD_LEFT) }}
              </a>
            @else
              <span class="text-muted small">None Generated</span>
            @endif
          </li>
        </ul>
      </div>
    </div>

    <!-- Lifecycle Timeline Card -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi-calendar-range me-1 text-secondary"></i> Lifecycle & Dates
        </h6>
        <div class="timeline small">
          <div class="d-flex mb-2">
            <div class="me-3 text-muted" style="min-width: 90px;">Started:</div>
            <div>
              <strong class="text-dark">{{ Helper::formatDate($subscription->created_at) }}</strong>
              <div class="text-muted fs-small">{{ $subscription->created_at->format('H:i:s T') }}</div>
            </div>
          </div>
          <div class="d-flex mb-2">
            <div class="me-3 text-muted" style="min-width: 90px;">Ends At:</div>
            <div>
              <strong class="text-dark">{{ $subscription->ends_at ? Helper::formatDate($subscription->ends_at) : '—' }}</strong>
              <div class="text-muted fs-small">
                {{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('H:i:s T') : '' }}
                ({{ $subscription->days_remaining }})
              </div>
            </div>
          </div>
          @if ($subscription->cancelled === 'yes')
            <div class="d-flex mb-2">
              <div class="me-3 text-danger" style="min-width: 90px;">Cancelled:</div>
              <div>
                <strong class="text-danger">{{ $subscription->cancelled_at ? Helper::formatDate($subscription->cancelled_at) : 'Yes' }}</strong>
                @if ($subscription->status_detail['code'] === 'cancelled_grace')
                  <div class="text-warning fs-small">Active in grace period until end date</div>
                @endif
              </div>
            </div>
          @endif
          <div class="d-flex mt-3 pt-2 border-top">
            <div class="me-3 text-muted" style="min-width: 90px;">Current State:</div>
            <div>
              <span class="badge {{ $subscription->status_detail['badge'] }}">
                {{ $subscription->status_detail['label'] }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal-footer border-top-0">
  @if ($subscription->user)
    <a href="{{ url($subscription->user->username) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
      <i class="bi-box-arrow-up-right me-1"></i> User Profile
    </a>
  @endif

  @if ($subscription->cancelled === 'no' && ($subscription->status_detail['code'] === 'active' || $subscription->status_detail['code'] === 'expiring_soon'))
    <form action="{{ route('subscriptions.cancel', $subscription->id) }}" method="POST" class="d-inline">
      @csrf
      <button type="button" class="btn btn-danger btn-sm actionDelete">
        <i class="bi-x-circle me-1"></i> Cancel Auto-Renewal
      </button>
    </form>
  @endif

  <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
</div>
