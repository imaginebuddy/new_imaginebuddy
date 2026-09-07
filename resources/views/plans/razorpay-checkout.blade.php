@extends('layouts.app')

@section('title') Razorpay Checkout - @endsection

@section('content')
<section class="section section-sm">
  <div class="container pt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center py-5">
        <div class="card border-0 shadow-sm p-4 rounded-3">
          <h3 class="fw-bold mb-3">Complete Your Subscription</h3>
          @php
            $currSymbol = ($order['currency'] ?? 'INR') == 'USD' ? '$' : '₹';
          @endphp
          <p class="text-muted mb-4">Plan: <strong>{{ $plan->name }}</strong> ({{ strtoupper($interval) }}) &bull; <strong class="text-dark">{{ $currSymbol }}{{ number_format($order['amount'] / 100, 2) }} {{ $order['currency'] ?? 'INR' }}</strong></p>

          <form action="{{ url('razorpay/subscription/process') }}" method="POST" id="razorpayForm">
            @csrf
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $order['id'] }}">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
            <input type="hidden" name="plan_id" value="{{ $plan->plan_id }}">
            <input type="hidden" name="interval" value="{{ $interval }}">

            <button type="button" id="payRazorpayBtn" class="btn btn-custom btn-lg w-100 py-3 fw-bold">
              <i class="bi bi-credit-card me-2"></i> Pay Now with Razorpay
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('javascript')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  var options = {
    "key": "{{ $payment->key }}",
    "amount": "{{ $order['amount'] }}",
    "currency": "{{ $order['currency'] }}",
    "name": "{{ $settings->title }}",
    "description": "Subscription for {{ $plan->name }}",
    "order_id": "{{ $order['id'] }}",
    "handler": function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.getElementById('razorpayForm').submit();
    },
    "prefill": {
        "name": "{{ auth()->user()->name }}",
        "email": "{{ auth()->user()->email }}"
    },
    "theme": {
        "color": "#000000"
    }
  };
  var rzp1 = new Razorpay(options);
  document.getElementById('payRazorpayBtn').onclick = function(e){
    rzp1.open();
    e.preventDefault();
  }
  // Auto-launch checkout
  rzp1.open();
</script>
@endsection
