<div class="modal-header border-bottom-0 pb-0">
  <div class="d-flex align-items-center">
    <img src="{{ Storage::url(config('path.avatar') . $user->avatar) }}" width="56" height="56" class="rounded-circle me-3 border shadow-sm" />
    <div>
      <h5 class="modal-title fw-bold mb-0 text-dark">
        {{ $user->name ?: $user->username }}
      </h5>
      <div class="text-muted small">
        <span>&#64;{{ $user->username }}</span>
        <span class="badge bg-light text-dark border ms-2">ID #{{ $user->id }}</span>
        
        @php
          $roleName = trans('admin.normal');
          foreach ($roles as $r) {
            if ($user->role == $r->id) {
              $roleName = $r->name;
              break;
            }
          }
        @endphp
        <span class="badge bg-{{ $user->role != '0' ? 'success' : 'secondary' }} ms-1">{{ $roleName }}</span>

        @php
          $statusMode = $user->status == 'active' ? 'success' : ($user->status == 'pending' ? 'info' : 'warning');
        @endphp
        <span class="badge bg-{{ $statusMode }} ms-1 text-capitalize">{{ $user->status }}</span>
      </div>
    </div>
  </div>
  <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
  <div class="row g-3">

    <!-- Card 1: Account Information -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi bi-person-lines-fill me-1 text-secondary"></i> Account Information
        </h6>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Email:</span>
            <strong class="text-dark">{{ $user->email }}</strong>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Country:</span>
            <span class="badge bg-secondary-subtle text-dark border">
              {{ $user->countryRelation ? $user->countryRelation->country_name : ($user->country() ? $user->country()->country_name : 'Not set') }}
            </span>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Registered Date:</span>
            <strong>{{ Helper::formatDate($user->date) }}</strong>
          </li>
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Registered IP:</span>
            <code>{{ $user->ip ?: 'Not available' }}</code>
          </li>
          <li class="d-flex justify-content-between">
            <span class="text-muted">Authorized to Upload:</span>
            <span class="badge bg-{{ $user->authorized_to_upload == 'yes' ? 'success' : 'secondary' }}-subtle text-{{ $user->authorized_to_upload == 'yes' ? 'success' : 'dark' }} border">
              {{ ucfirst($user->authorized_to_upload) }}
            </span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Card 2: Subscription & Access -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi bi-star-fill text-warning me-1"></i> Subscription & Access
        </h6>
        @if ($activeTestSub)
          <div class="alert alert-info border-0 p-2 mb-3 rounded-2">
            <div class="fw-bold">
              <i class="bi bi-shield-check me-1"></i> Pro Photography Plan (Test Access)
            </div>
            <div class="small">Expires: <strong>{{ Helper::formatDate($activeTestSub->ends_at) }}</strong> ({{ $activeTestSub->days_remaining }})</div>
            @if ($activeTestSub->test_notes)
              <div class="small text-muted mt-1">Note: {{ $activeTestSub->test_notes }}</div>
            @endif
          </div>
        @elseif ($activePaidSub)
          <div class="alert alert-success border-0 p-2 mb-3 rounded-2">
            <div class="fw-bold">
              <i class="bi bi-check-circle-fill me-1"></i> {{ $activePaidSub->plan ? $activePaidSub->plan->name : 'Pro Plan' }} (Paid)
            </div>
            <div class="small">Billed via <strong>{{ $activePaidSub->payment_gateway }}</strong> &bull; Ends: <strong>{{ Helper::formatDate($activePaidSub->ends_at) }}</strong></div>
          </div>
        @else
          <div class="alert alert-light border p-2 mb-3 rounded-2 text-muted small">
            <i class="bi bi-info-circle me-1"></i> Standard Free Member (No active subscription)
          </div>
        @endif

        <ul class="list-unstyled small mb-0">
          <li class="mb-2 d-flex justify-content-between">
            <span class="text-muted">Available Downloads:</span>
            <strong>{{ $user->downloads }}</strong>
          </li>
          @if ($user->getSubscription() && $user->getSubscription()->plan)
            <li class="mb-2 d-flex justify-content-between">
              <span class="text-muted">Daily Limit:</span>
              <strong>{{ $user->getSubscription()->plan->download_limits ?: 100 }} prompts/day</strong>
            </li>
            <li class="d-flex justify-content-between">
              <span class="text-muted">Daily Copies Left Today:</span>
              <strong class="text-success">{{ $user->remainingDailyPromptCopies() }}</strong>
            </li>
          @else
            <li class="d-flex justify-content-between">
              <span class="text-muted">Subscription Tier:</span>
              <span class="badge bg-secondary">Free</span>
            </li>
          @endif
        </ul>
      </div>
    </div>

    <!-- Card 3: Activity & Content -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi bi-images me-1 text-secondary"></i> Activity & Content
        </h6>
        <div class="row text-center g-2 mb-2">
          <div class="col-6">
            <div class="p-2 border rounded bg-white">
              <div class="fs-4 fw-bold text-dark">{{ $user->images()->count() }}</div>
              <small class="text-muted">Prompts Uploaded</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-white">
              <div class="fs-4 fw-bold text-dark">{{ $user->collections()->count() }}</div>
              <small class="text-muted">Collections</small>
            </div>
          </div>
        </div>
        <ul class="list-unstyled small mb-0 mt-3">
          @if ($user->website)
            <li class="mb-1 text-truncate">
              <span class="text-muted">Website:</span>
              <a href="{{ $user->website }}" target="_blank" class="text-decoration-none">{{ $user->website }}</a>
            </li>
          @endif
          @if ($user->twitter)
            <li class="mb-1 text-truncate">
              <span class="text-muted">Twitter:</span>
              <a href="https://twitter.com/{{ $user->twitter }}" target="_blank" class="text-decoration-none">&#64;{{ $user->twitter }}</a>
            </li>
          @endif
          @if ($user->bio)
            <li class="text-muted fst-italic mt-2">
              &ldquo;{{ \Illuminate\Support\Str::limit($user->bio, 120) }}&rdquo;
            </li>
          @endif
        </ul>
      </div>
    </div>

    <!-- Card 4: Financials & Funds -->
    <div class="col-md-6">
      <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
          <i class="bi bi-cash-stack me-1 text-secondary"></i> Balances & Funds
        </h6>
        <div class="row text-center g-2 mb-3">
          <div class="col-6">
            <div class="p-2 border rounded bg-white">
              <div class="fs-5 fw-bold text-success">{{ Helper::amountFormatDecimal($user->balance) }}</div>
              <small class="text-muted">Earnings Balance</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-white">
              <div class="fs-5 fw-bold text-primary">{{ Helper::amountFormatDecimal($user->funds) }}</div>
              <small class="text-muted">Account Funds</small>
            </div>
          </div>
        </div>
        <ul class="list-unstyled small mb-0">
          <li class="d-flex justify-content-between">
            <span class="text-muted">Payout Account (PayPal):</span>
            <strong>{{ $user->paypal_account ?: '—' }}</strong>
          </li>
        </ul>
      </div>
    </div>

  </div>
</div>

<div class="modal-footer border-top-0 pt-0">
  <a href="{{ url($user->username) }}" target="_blank" class="btn btn-outline-secondary btn-sm me-auto">
    <i class="bi bi-box-arrow-up-right me-1"></i> View Public Profile
  </a>
  <a href="{{ url('panel/admin/members/edit', $user->id) }}" class="btn btn-dark btn-sm">
    <i class="far fa-edit me-1"></i> Edit Member
  </a>
  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
