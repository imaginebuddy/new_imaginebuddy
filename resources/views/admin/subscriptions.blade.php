@extends('admin.layout')

@section('css')
<style>
  .btn-action {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    text-decoration: none !important;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }
  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
  }
  .btn-action:active {
    transform: translateY(0);
  }
  .btn-action-view {
    background-color: #ebf5ff;
    color: #0d6efd;
  }
  .btn-action-view:hover {
    background-color: #0d6efd;
    color: #ffffff;
  }
  .btn-action-invoice {
    background-color: #e8f7ee;
    color: #198754;
  }
  .btn-action-invoice:hover {
    background-color: #198754;
    color: #ffffff;
  }
  .btn-action-cancel {
    background-color: #fde8e8;
    color: #e02424;
  }
  .btn-action-cancel:hover {
    background-color: #e02424;
    color: #ffffff;
  }
  .btn-action i {
    font-size: 15px;
    line-height: 1;
  }
</style>
@endsection

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.subscriptions') }} ({{ $subscriptions->total() }})</span>
  </h5>

<div class="content">

  @if (session('success_message') || session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi-check-circle me-1"></i> {{ session('success_message') ?: session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Top Metric Cards -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 p-3">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-circle me-3">
            <i class="bi-people fs-4"></i>
          </div>
          <div>
            <span class="text-muted small d-block">Active Subscribers</span>
            <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['active']) }}</h4>
            <small class="text-muted fs-small">of {{ number_format($stats['total']) }} total</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 p-3">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-circle me-3">
            <i class="bi-clock-history fs-4"></i>
          </div>
          <div>
            <span class="text-muted small d-block">Expiring Soon (<= 3d)</span>
            <h4 class="mb-0 fw-bold text-warning">{{ number_format($stats['expiring_soon']) }}</h4>
            <small class="text-muted fs-small">Need renewal</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 p-3">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0 bg-danger-subtle text-danger p-3 rounded-circle me-3">
            <i class="bi-x-circle fs-4"></i>
          </div>
          <div>
            <span class="text-muted small d-block">Expired / Churned</span>
            <h4 class="mb-0 fw-bold text-danger">{{ number_format($stats['expired'] + $stats['cancelled']) }}</h4>
            <small class="text-muted fs-small">{{ number_format($stats['cancelled']) }} cancelled</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-custom border-0 h-100 p-3">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-circle me-3">
            <i class="bi-cash-stack fs-4"></i>
          </div>
          <div>
            <span class="text-muted small d-block">Subscribed Revenue</span>
            <div class="fw-bold text-dark fs-5">₹{{ number_format($stats['revenue_inr'], 0) }}</div>
            <small class="text-muted fs-small">+ ${{ number_format($stats['revenue_usd'], 2) }} USD</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('subscriptions') }}" class="row g-2 align-items-center">
        
        <div class="col-lg-3 col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi-search text-muted"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Search user, email, txn ID...">
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-6">
          <select name="status" class="form-select">
            <option value="">Status (All)</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="expiring_soon" {{ request('status') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-3 col-6">
          <select name="gateway" class="form-select">
            <option value="">Gateway (All)</option>
            <option value="Razorpay" {{ request('gateway') === 'Razorpay' ? 'selected' : '' }}>Razorpay (India)</option>
            <option value="PayPal" {{ request('gateway') === 'PayPal' ? 'selected' : '' }}>PayPal (International)</option>
            <option value="Wallet" {{ request('gateway') === 'Wallet' ? 'selected' : '' }}>Wallet</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-3 col-6">
          <select name="interval" class="form-select">
            <option value="">Billing Interval</option>
            <option value="month" {{ request('interval') === 'month' ? 'selected' : '' }}>Monthly</option>
            <option value="year" {{ request('interval') === 'year' ? 'selected' : '' }}>Yearly</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-3 col-6">
          <select name="country" class="form-select">
            <option value="">Country (All)</option>
            <option value="IN" {{ request('country') === 'IN' ? 'selected' : '' }}>India (IN)</option>
            <option value="US" {{ request('country') === 'US' ? 'selected' : '' }}>United States (US)</option>
          </select>
        </div>

        <div class="col-lg-1 col-md-12 d-flex gap-1">
          <button type="submit" class="btn btn-dark w-100" title="Filter"><i class="bi-filter"></i></button>
          @if (request()->hasAny(['q', 'status', 'gateway', 'interval', 'country']))
            <a href="{{ route('subscriptions') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi-arrow-counterclockwise"></i></a>
          @endif
        </div>

      </form>
    </div>
  </div>

  <!-- Subscriptions Table -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

					<div class="table-responsive p-0">
						<table class="table table-hover align-middle">
						 <thead>
               <tr class="table-light text-muted small text-uppercase">
                 <th>ID</th>
                 <th>Subscriber</th>
                 <th>Plan & Interval</th>
                 <th>Payment & Method</th>
                 <th>Amount</th>
                 <th>Lifecycle Dates</th>
                 <th>Status</th>
                 <th class="text-end">Actions</th>
               </tr>
             </thead>
             <tbody>

               @if ($subscriptions->total() != 0 && $subscriptions->count() != 0)
                 @foreach ($subscriptions as $subscription)
                   <tr>
                     <td>
                       <span class="fw-bold text-muted">#{{ $subscription->id }}</span>
                     </td>

                     <!-- Subscriber Column -->
                     <td>
                       @if ($subscription->user)
                         <div class="d-flex align-items-center">
                           <a href="{{ url($subscription->user->username) }}" target="_blank" class="text-decoration-none">
                             <img src="{{ Storage::url(config('path.avatar') . $subscription->user->avatar) }}" width="36" height="36" class="rounded-circle me-2 border" />
                           </a>
                           <div>
                             <a href="{{ url($subscription->user->username) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-block lh-sm">
                               {{ $subscription->user->name ?: $subscription->user->username }}
                             </a>
                             <small class="text-muted d-block fs-small">&#64;{{ $subscription->user->username }} &bull; {{ $subscription->user->email }}</small>
                             <span class="badge bg-secondary-subtle text-dark border fs-small py-0 mt-1">
                               {{ $subscription->country_code ?: ($subscription->user->country() ? $subscription->user->country()->country_code : ($subscription->payment_gateway === 'Razorpay' ? 'IN' : 'US')) }}
                             </span>
                           </div>
                         </div>
                       @else
                         <span class="text-muted fst-italic">User #{{ $subscription->user_id }} (Deleted)</span>
                       @endif
                     </td>

                     <!-- Plan Column -->
                     <td>
                       <div class="fw-bold text-dark">{{ $subscription->plan ? $subscription->plan->name : 'Pro Plan' }}</div>
                       <span class="badge bg-light text-dark border text-capitalize">
                         {{ $subscription->interval === 'year' ? 'Yearly' : 'Monthly' }}
                       </span>
                     </td>

                     <!-- Payment & Method Column -->
                     <td>
                       <div>
                         <span class="badge {{ $subscription->payment_gateway === 'Razorpay' ? 'bg-dark' : ($subscription->payment_gateway === 'PayPal' ? 'bg-primary' : 'bg-info') }}">
                           {{ $subscription->payment_gateway ?: 'Payment' }}
                         </span>
                         @if ($subscription->payment_method)
                           <span class="badge bg-secondary-subtle text-dark border ms-1 text-uppercase fs-small">
                             {{ $subscription->payment_method }}
                           </span>
                         @endif
                       </div>
                       @php
                         $txnId = $subscription->last_payment ?: ($subscription->stripe_id ?: $subscription->paypal_id);
                       @endphp
                       @if ($txnId)
                         <small class="text-muted d-block font-monospace user-select-all mt-1" title="{{ $txnId }}">
                           {{ \Illuminate\Support\Str::limit($txnId, 16) }}
                         </small>
                       @endif
                     </td>

                     <!-- Amount Column -->
                     <td>
                       <div class="fw-bold text-dark fs-6">{{ $subscription->formatted_amount }}</div>
                       <small class="text-muted text-uppercase">{{ $subscription->currency ?: ($subscription->payment_gateway === 'Razorpay' ? 'INR' : 'USD') }}</small>
                     </td>

                     <!-- Dates Column -->
                     <td>
                       <small class="text-muted d-block">Start: <strong>{{ Helper::formatDate($subscription->created_at) }}</strong></small>
                       <small class="text-dark d-block">Ends: <strong>{{ $subscription->ends_at ? Helper::formatDate($subscription->ends_at) : '—' }}</strong></small>
                       <small class="text-muted fs-small">{{ $subscription->days_remaining }}</small>
                     </td>

                     <!-- Status Badge -->
                     <td>
                       <span class="badge {{ $subscription->status_detail['badge'] }} px-2 py-1">
                         {{ $subscription->status_detail['label'] }}
                       </span>
                     </td>
                     <!-- Actions Column -->
                      <td class="text-end text-nowrap">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                          <button type="button" class="btn-action btn-action-view btnViewDetail" data-id="{{ $subscription->id }}" title="View Subscription Details">
                            <i class="bi-eye-fill"></i>
                          </button>

                          @if ($subscription->invoice)
                            <a href="{{ url('invoice', $subscription->invoice->id) }}" target="_blank" class="btn-action btn-action-invoice" title="View Invoice">
                              <i class="bi-receipt"></i>
                            </a>
                          @endif

                          @if ($subscription->cancelled === 'no' && ($subscription->status_detail['code'] === 'active' || $subscription->status_detail['code'] === 'expiring_soon'))
                            <form action="{{ route('subscriptions.cancel', $subscription->id) }}" method="POST" class="d-inline m-0 p-0">
                              @csrf
                              <button type="button" class="btn-action btn-action-cancel actionDelete" title="Cancel Subscription">
                                <i class="bi-x-circle-fill"></i>
                              </button>
                            </form>
                          @endif
                        </div>
                      </td>

                   </tr>
                 @endforeach
               @else
                 <tr>
                   <td colspan="8" class="text-center py-5 text-muted fw-light">
                     <i class="bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                     {{ trans('misc.no_results_found') }}
                   </td>
                 </tr>
               @endif

             </tbody>
						</table>
					</div>

				 </div>
 			</div>

			<div class="mt-3">
        {{ $subscriptions->links() }}
      </div>
 		</div>
	</div>
</div>

<!-- Modal Container for Details -->
<div class="modal fade" id="subscriptionDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0" id="subscriptionDetailContent">
      <div class="modal-body text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted">Loading subscription details...</div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')
<script>
$(document).on('click', '.btnViewDetail', function(e) {
  e.preventDefault();
  var subId = $(this).data('id');
  var modalEl = new bootstrap.Modal(document.getElementById('subscriptionDetailModal'));
  
  $('#subscriptionDetailContent').html(
    '<div class="modal-body text-center py-5">' +
    '  <div class="spinner-border text-primary" role="status"></div>' +
    '  <div class="mt-2 text-muted">Loading subscription #' + subId + ' details...</div>' +
    '</div>'
  );
  modalEl.show();

  $.ajax({
    url: URL_BASE + '/panel/admin/subscriptions/' + subId,
    type: 'GET',
    dataType: 'html',
    success: function(html) {
      $('#subscriptionDetailContent').html(html);
    },
    error: function() {
      $('#subscriptionDetailContent').html(
        '<div class="modal-body text-center py-4 text-danger">' +
        '  <i class="bi-exclamation-triangle fs-1 d-block mb-2"></i>' +
        '  Failed to load subscription details. Please try again.' +
        '</div>' +
        '<div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>'
      );
    }
  });
});
</script>
@endsection
