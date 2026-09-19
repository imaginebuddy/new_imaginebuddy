@extends('admin.layout')

@section('content')
<div class="content">
  @include('admin.analytics.nav')

  <!-- 5-Stage Conversion Funnel Visualizer -->
  <div class="card shadow-custom border-0 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-0">
      <h5 class="card-title m-0 fw-light"><i class="bi bi-funnel text-primary me-2"></i> Subscription Purchase Conversion Funnel</h5>
      <small class="text-muted">Visitor &rarr; Pricing Page &rarr; Checkout Initiated &rarr; Payment Attempted &rarr; Successful Subscription</small>
    </div>
    <div class="card-body pt-4">
      @php
        $p1 = $totalVisitors > 0 ? 100 : 0;
        $p2 = $totalVisitors > 0 ? round(($pricingViews / $totalVisitors) * 100, 1) : 0;
        $p3 = $pricingViews > 0 ? round(($checkoutStarts / $pricingViews) * 100, 1) : 0;
        $p4 = $checkoutStarts > 0 ? round(($paymentAttempts / $checkoutStarts) * 100, 1) : 0;
        $p5 = $paymentAttempts > 0 ? round(($paymentSuccesses / $paymentAttempts) * 100, 1) : 0;
        $overallConversion = $totalVisitors > 0 ? round(($paymentSuccesses / $totalVisitors) * 100, 2) : 0;
      @endphp

      <div class="row g-3 text-center align-items-center mb-4">
        <!-- Stage 1 -->
        <div class="col-md">
          <div class="p-3 border rounded bg-light">
            <span class="small text-muted text-uppercase fw-bold d-block">1. Site Visitors</span>
            <h3 class="fw-bold my-1 text-primary">{{ number_format($totalVisitors) }}</h3>
            <span class="badge bg-secondary">100%</span>
          </div>
        </div>

        <div class="col-auto d-none d-md-block text-muted fs-4">&rarr;</div>

        <!-- Stage 2 -->
        <div class="col-md">
          <div class="p-3 border rounded bg-light">
            <span class="small text-muted text-uppercase fw-bold d-block">2. Pricing Viewed</span>
            <h3 class="fw-bold my-1 text-info">{{ number_format($pricingViews) }}</h3>
            <span class="badge bg-info-subtle text-info">{{ $p2 }}% of visitors</span>
          </div>
        </div>

        <div class="col-auto d-none d-md-block text-muted fs-4">&rarr;</div>

        <!-- Stage 3 -->
        <div class="col-md">
          <div class="p-3 border rounded bg-light">
            <span class="small text-muted text-uppercase fw-bold d-block">3. Checkout Start</span>
            <h3 class="fw-bold my-1 text-warning">{{ number_format($checkoutStarts) }}</h3>
            <span class="badge bg-warning-subtle text-warning">{{ $p3 }}% of pricing</span>
          </div>
        </div>

        <div class="col-auto d-none d-md-block text-muted fs-4">&rarr;</div>

        <!-- Stage 4 -->
        <div class="col-md">
          <div class="p-3 border rounded bg-light">
            <span class="small text-muted text-uppercase fw-bold d-block">4. Payment Attempt</span>
            <h3 class="fw-bold my-1 text-secondary">{{ number_format($paymentAttempts) }}</h3>
            <span class="badge bg-secondary-subtle text-secondary">{{ $p4 }}% of checkout</span>
          </div>
        </div>

        <div class="col-auto d-none d-md-block text-muted fs-4">&rarr;</div>

        <!-- Stage 5 -->
        <div class="col-md">
          <div class="p-3 border rounded bg-success-subtle border-success">
            <span class="small text-success text-uppercase fw-bold d-block">5. Paid Success</span>
            <h3 class="fw-bold my-1 text-success">{{ number_format($paymentSuccesses) }}</h3>
            <span class="badge bg-success">{{ $p5 }}% success rate</span>
          </div>
        </div>
      </div>

      <!-- Overall Funnel Conversion Callout -->
      <div class="alert alert-light border d-flex justify-content-between align-items-center mb-0">
        <div>
          <strong>Overall Visitor-to-Paid Conversion Rate:</strong>
          <span class="text-muted ms-1">Percentage of all site visitors who purchase a subscription</span>
        </div>
        <span class="fs-4 fw-bold text-success">{{ $overallConversion }}%</span>
      </div>
    </div>
  </div>

  <!-- Gateway Breakdown & Funnel Insights -->
  <div class="row g-4">
    <!-- Gateway Success Table -->
    <div class="col-lg-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-credit-card text-success me-2"></i> Payment Gateway Breakdown</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Gateway</th>
                  <th class="text-center">Successful Orders</th>
                  <th class="text-end">Revenue Processed</th>
                </tr>
              </thead>
              <tbody>
                @forelse($gateways as $gatewayName => $gw)
                  <tr>
                    <td class="fw-bold">{{ $gatewayName }}</td>
                    <td class="text-center">{{ number_format($gw['count']) }}</td>
                    <td class="text-end fw-bold text-success">{{ Helper::amountFormatDecimal($gw['total_amount']) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">No successful payments recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Drop-Off Analysis -->
    <div class="col-lg-6">
      <div class="card shadow-custom border-0 h-100">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
          <h5 class="card-title m-0 fw-light"><i class="bi bi-pie-chart text-info me-2"></i> Funnel Drop-off Analysis</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span>Visitors who never visited Pricing:</span>
              <strong class="text-danger">{{ $totalVisitors > 0 ? round((($totalVisitors - $pricingViews) / $totalVisitors) * 100, 1) : 0 }}%</strong>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-danger" style="width: {{ $totalVisitors > 0 ? round((($totalVisitors - $pricingViews) / $totalVisitors) * 100) : 0 }}%;"></div>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
              <span>Pricing visitors who abandoned without checkout:</span>
              <strong class="text-warning">{{ $pricingViews > 0 ? round((($pricingViews - $checkoutStarts) / $pricingViews) * 100, 1) : 0 }}%</strong>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-warning" style="width: {{ $pricingViews > 0 ? round((($pricingViews - $checkoutStarts) / $pricingViews) * 100) : 0 }}%;"></div>
            </div>
          </div>

          <div class="mb-0">
            <div class="d-flex justify-content-between small mb-1">
              <span>Checkout attempts that failed / abandoned at gateway:</span>
              <strong class="text-secondary">{{ $paymentAttempts > 0 ? round((($paymentAttempts - $paymentSuccesses) / $paymentAttempts) * 100, 1) : 0 }}%</strong>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-secondary" style="width: {{ $paymentAttempts > 0 ? round((($paymentAttempts - $paymentSuccesses) / $paymentAttempts) * 100) : 0 }}%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
