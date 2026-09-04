@extends('layouts.app')

@section('title') {{ __('misc.pricing') }} - @endsection

@section('content')
@php
  $isIndia = Helper::isIndia();
  $curr = Helper::currentCurrency();
@endphp
<section class="section section-sm">

<div class="container">

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
      <i class="bi-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="bi-check2 me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row justify-content-center">
	<!-- Col MD -->
	<div class="col-md-6">

    <div class="col-lg-12 py-5 text-center">
  		<h1 class="mb-0">
  			{{ __('misc.plans_for_photos') }}
  		</h1>
  		<p class="lead text-muted mt-0">{{ __('misc.subtitle_pricing') }}</p>

      <div class="d-flex justify-content-center">
        <div class="form-check form-switch form-switch-md flex-row d-flex align-items-center p-0">
          <label class="c-pointer" for="plan">{{ __('misc.monthly') }}</label>
          <input class="form-check-input mx-2" value="mo" type="checkbox" id="plan">
          <label class="c-pointer" for="plan">{{ __('misc.yearly') }}</label>
        </div>
      </div>

  	  </div>
		  </div><!-- /COL MD -->
    </div><!-- row -->

    <div class="row justify-content-center g-4 mb-4">

      <!-- FREE STARTER CARD -->
      <div class="col-lg-5 col-md-6">
        <div class="card h-100 rounded-4 shadow-sm p-4 border bg-white">
          <div class="card-header py-3 bg-transparent border-bottom-0 text-center">
            <span class="w-100 mb-2 d-block">
              <span class="badge rounded-pill bg-light text-dark px-3 py-1.5 border">Free Forever</span>
            </span>
            <h2 class="my-0 fw-bold">Starter Plan</h2>
            <p class="text-muted small mt-2 mb-0">Free access to explore and try AI photography prompts</p>
          </div>
          <div class="card-body d-flex flex-column">
            <h1 class="card-title text-center mb-4">
              <sup class="h4 fw-bold lh-1">{{ $curr['symbol'] }}</sup>0
              <small class="fw-light f-size-18 text-muted">/forever</small>
            </h1>

            <ul class="list-unstyled mb-4 flex-grow-1">
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2"></i>
                <span>Access to all <strong>Free AI Prompts</strong></span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2"></i>
                <span>Copy up to <strong>20 free prompts</strong> / day</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2"></i>
                <span>Up to <strong>{{ $settings->daily_limit_downloads ?: 20 }} free photo downloads</strong> / day</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="bi bi-check2 text-success fs-5 me-2"></i>
                <span>Supports Gemini, ChatGPT & Midjourney</span>
              </li>
              <li class="mb-3 d-flex align-items-center text-muted">
                <i class="bi bi-x fs-4 text-muted me-2"></i>
                <span class="text-decoration-line-through">Unlock Premium AI Prompts</span>
              </li>
              <li class="mb-3 d-flex align-items-center text-muted">
                <i class="bi bi-x fs-4 text-muted me-2"></i>
                <span class="text-decoration-line-through">High-Resolution Pro Downloads</span>
              </li>
              <li class="mb-3 d-flex align-items-center text-muted">
                <i class="bi bi-x fs-4 text-muted me-2"></i>
                <span class="text-decoration-line-through">Commercial Client License</span>
              </li>
            </ul>

            <div class="mt-auto">
              @if (!auth()->check())
                <a href="{{ url('register') }}" class="w-100 btn btn-lg btn-outline-dark rounded-pill py-3 fw-semibold">
                  Get Started Free
                </a>
              @elseif (!$getSubscription)
                <button type="button" class="w-100 btn btn-lg btn-outline-secondary rounded-pill py-3 fw-semibold disabled" disabled>
                  <i class="bi bi-check2 me-1"></i> Current Plan
                </button>
              @else
                <button type="button" class="w-100 btn btn-lg btn-outline-secondary rounded-pill py-3 fw-semibold disabled" disabled>
                  Free Tier
                </button>
              @endif
            </div>

          </div>
        </div>
      </div>

      <!-- PRO PLAN CARD(S) -->
      @foreach ($plans->whereDownloadableContent('images')->get() as $plan)
        @php
          $isCurrentExactPlan = auth()->check() && $getSubscription && ($getSubscription->stripe_price == $plan->plan_id || preg_replace('/_(month|year).*$/', '', $getSubscription->stripe_price) == $plan->plan_id);
          $monthlyPrice = $isIndia ? ($plan->price_inr ?: 284.00) : ($plan->price ?: 3.00);
          $yearlyPrice  = $isIndia ? ($plan->price_year_inr ?: 2550.00) : ($plan->price_year ?: 27.00);
          $planDiscount = Helper::calculateSubscriptionDiscount($monthlyPrice, $yearlyPrice);
        @endphp
        <div class="col-lg-5 col-md-6">
          <div class="card h-100 rounded-4 p-4 border border-2 border-dark position-relative bg-white text-dark" style="box-shadow: 0 16px 40px rgba(0,0,0,0.08) !important; color: #1e293b !important;">
            <div class="card-header py-3 bg-transparent border-bottom-0 text-center">
              <span class="w-100 mb-2 d-block">
                <span class="badge rounded-pill bg-dark text-white px-3 py-1.5">
                  <i class="bi bi-stars text-warning me-1"></i> Most Popular
                </span>
              </span>
              <h2 class="my-0 fw-bold text-dark">
                {{ $plan->name }}
                @if ($planDiscount > 0)
                  <small class="badge bg-success rounded-pill display-none planYearly fs-small align-middle ms-1">
                    {{ $planDiscount }}% {{ __('misc.discount') }}
                  </small>
                @endif
              </h2>
              <p class="text-secondary small mt-2 mb-0">Unlimited creativity with full prompt access & commercial rights</p>
            </div>
            <div class="card-body d-flex flex-column text-dark">
              <h1 class="card-title text-center text-dark mb-4">
                <span class="planMonthly text-dark">
                  <sup class="h4 fw-bold lh-1 text-dark">{{ $curr['symbol'] }}</sup><span class="text-dark">{{ $isIndia ? number_format($monthlyPrice, 0) : number_format($monthlyPrice, 2) }}</span>
                  <small class="fw-light f-size-18 text-muted">/{{ __('misc.mo') }}</small>
                </span>

                <span class="planYearly text-dark display-none">
                  <sup class="h4 fw-bold lh-1 text-dark">{{ $curr['symbol'] }}</sup><span class="text-dark">{{ $isIndia ? number_format($yearlyPrice, 0) : number_format($yearlyPrice, 2) }}</span>
                  <small class="fw-light f-size-18 text-muted">/{{ __('misc.yr') }}</small>
                </span>
              </h1>

              <ul class="list-unstyled mb-4 flex-grow-1 text-dark" style="color: #1e293b !important;">
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span><strong>Unlock & copy ALL Premium AI Prompts</strong></span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span>Copy up to <strong>100 prompts per day</strong></span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span><strong>{{ number_format($plan->downloads_per_month) }} High-Res Downloads</strong> / month (up to {{ $plan->download_limits }}/day)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span><strong>Full Prompt Details & Parameters</strong> (Midjourney, Gemini, ChatGPT)</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span>Multi-angle <strong>Generated Example Outputs</strong> gallery</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success me-2"></i>
                  <span><strong>Commercial Regular License</strong> included</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span>{{ $plan->unused_downloads_rollover ? 'Unused downloads roll over each month' : 'Downloads renew fresh every month' }}</span>
                </li>
                <li class="mb-3 d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                  <span><strong>Cancel anytime</strong> with 1-click</span>
                </li>
              </ul>

              <div class="mt-auto">
                <a
                  data-plan-id="{{ $plan->plan_id }}"
                  data-plan-name="{{ __('misc.plan_name', ['plan' => $plan->name]) }}"
                  data-price="{{ Helper::formatPrice($monthlyPrice, $curr['code']) }}"
                  data-price-total="{{ Helper::formatPrice($monthlyPrice, $curr['code'], true) }}"
                  data-price-gross="{{ $monthlyPrice }}"
                  data-price-year="{{ Helper::formatPrice($yearlyPrice, $curr['code']) }}"
                  data-price-year-gross="{{ $yearlyPrice }}"
                  data-price-year-total="{{ Helper::formatPrice($yearlyPrice, $curr['code'], true) }}"
                  href="@auth javascript:void(0); @else{{ url('/login') }}@endauth"
                  @if (auth()->check()) data-bs-toggle="modal" data-bs-target="#checkout" @endif
                  class="w-100 btn btn-lg rounded-pill py-3 fw-bold shadow-sm"
                  style="background-color: #0f172a !important; color: #ffffff !important; border: 1px solid #0f172a !important;">
                  @if ($isCurrentExactPlan)
                    <i class="bi bi-check2 me-1"></i> {{ __('misc.active') }} (Switch)
                  @else
                    <i class="bi bi-stars text-warning me-1"></i> Upgrade to Pro
                  @endif
                </a>
              </div>

            </div>
          </div>
        </div>
      @endforeach

    </div>

      <div class="d-block text-center w-100 fst-italic">
        <small>
          {{ __('misc.prices_and_excludes_tax', ['currency' => $curr['code']]) }}
        </small>
      </div>

 </div><!-- container -->

 <div class="container py-5">
            <div class="text-center">
                <h2 class="d-inline-block">{{ __('misc.frequently_asked_questions') }}</h2>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mt-5 h-100">
                    <h5 class="text-muted">{{ __('misc.faq_pricing_1') }}</h5>
                    <div class="text-muted">{{ __('misc.faq_pricing_1_reply') }}</div>
                </div>

                <div class="col-12 col-md-6 mt-5 h-100">
                    <h5 class="text-muted">{{ __('misc.faq_pricing_2') }}</h5>
                    <div class="text-muted">{{ __('misc.faq_pricing_2_reply') }}</div>
                </div>

                <div class="col-12 col-md-6 mt-5 h-100">
                    <h5 class="text-muted">{{ __('misc.faq_pricing_3') }}</h5>
                    <div class="text-muted">{{ __('misc.faq_pricing_3_reply') }}</div>
                </div>

                <div class="col-12 col-md-6 mt-5 h-100">
                    <h5 class="text-muted">{{ __('misc.faq_pricing_4') }}</h5>
                    <div class="text-muted">{{ __('misc.faq_pricing_4_reply') }}</div>
                </div>
            </div>
        </div>
</section>

@if (auth()->check())
<div class="modal fade" tabindex="-1"  id="checkout">
  <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-body p-lg-4">
        <h5 class="mb-3">
              <i class="bi bi-cart2 me-1"></i> {{ __('misc.checkout') }}

              <span class="float-end c-pointer" data-bs-dismiss="modal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
              </span>
            </h5>
            <div class="container">
              <div class="row">

                <div class="col-md-6 ps-0">
                  <div class="mb-3">
                    <strong>{{ __('misc.payments_options') }}</strong>
                  </div>

                  <form method="post" action="{{url('buy/subscription')}}" class="d-inline" id="formBuySubscription">
                    @csrf

                    <input type="hidden" id="interval" name="interval" value="month">
                    <input type="hidden" id="planId" name="plan" value="">

                  @php
                    $availableGateways = PaymentGateways::whereEnabled('1')->whereSubscription('1')->orderBy('type', 'DESC')->get();
                    if ($isIndia) {
                      $availableGateways = $availableGateways->filter(function($p) {
                        return $p->name === 'Razorpay';
                      });
                    } else {
                      $availableGateways = $availableGateways->filter(function($p) {
                        return $p->name === 'PayPal';
                      });
                    }
                  @endphp

                  @forelse ($availableGateways as $payment)
                    <div class="form-check custom-radio mb-2">
                      <input name="payment_gateway" value="{{$payment->id}}" id="payment_radio{{$payment->id}}" class="form-check-input radio-bws" type="radio" checked>
                      <label class="form-check-label" for="payment_radio{{$payment->id}}">
                        <span><img class="me-1 rounded" src="{{ url('public/img/payments', $payment->logo) }}" width="20" /> <strong>{{ $payment->name }}</strong></span>
                        <small class="w-100 d-block">
                          @if ($payment->name == 'Razorpay')
                            UPI, Google Pay, PhonePe, Paytm, RuPay & Cards
                          @elseif ($payment->name == 'PayPal')
                            PayPal, Credit/Debit Cards, Apple Pay
                          @elseif ($payment->type == 'card')
                            {{ __('misc.debit_credit_card') }}
                          @endif
                        </small>
                      </label>
                    </div>
                  @empty
                    @if ($isIndia)
                      <div class="alert alert-warning py-2 mb-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i> Razorpay gateway is currently unavailable.
                      </div>
                    @else
                      <div class="alert alert-warning py-2 mb-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i> PayPal gateway is currently unavailable.
                      </div>
                    @endif
                  @endforelse

                  <div class="form-check custom-radio mb-3">
                    <input name="payment_gateway" @if (auth()->user()->funds == 0.00) disabled @endif value="wallet" id="wallet" class="form-check-input radio-bws" type="radio" @if($availableGateways->isEmpty() && auth()->user()->funds > 0) checked @endif>
                    <label class="form-check-label" for="wallet">
                      <span><img class="me-1 rounded" src="{{ url('public/img/payments/wallet.png') }}" width="20" /> <strong>{{ __('misc.wallet') }}</strong></span>
                      <small class="w-100 d-block">
                        {{ __('misc.available_balance') }}: <strong>{{Helper::formatPrice(auth()->user()->funds, $curr['code'])}}</strong>
                      </small>
                    </label>
                  </div>
                </div>
                <div class="col-md-6 ps-0">

                  <div class="mb-1">
                    <strong>{{ __('misc.order_summary') }}</strong>
                  </div>


            <ul class="list-group list-group-flush">

              <li class="list-group-item py-1 px-0">
                <div class="row">
                  <div class="col">
                    <span class="d-block w-100" id="summaryPlan"></span>
                    <small class="planMonthly">{{ __('misc.billed_monthly') }}</small>
                    <small class="planYearly display-none">{{ __('misc.billed_yearly') }}</small>
                  </div>
                </div>
              </li>

              	<li class="list-group-item py-1 px-0">
                  <div class="row">
                    <div class="col">
                      <small>{{ __('misc.subtotal') }}:</small>
                    </div>
                    <div class="col-auto">
                      <small class="font-weight-bold" id="subtotal"></small>
                    </div>
                  </div>
                </li>

            @if (auth()->user()->isTaxable()->count())

            	@foreach (auth()->user()->isTaxable() as $tax)
      					<li class="list-group-item py-1 px-0 isTaxable">
          	    <div class="row">
          	      <div class="col">
          	        <small>{{ $tax->name }} {{ $tax->percentage }}%:</small>
          	      </div>
          	      <div class="col-auto percentageAppliedTax{{$loop->iteration}}" data="{{ $tax->percentage }}">
          	        <small class="font-weight-bold">
          	        {{ $curr['symbol'] }}<span class="amount{{$loop->iteration}}"></span>
          	        </small>
          	      </div>
          	    </div>
          	  </li>
              @endforeach
            @endif

          	<li class="list-group-item py-1 px-0">
              <div class="row">
                <div class="col">
                  <small class="fw-bold">{{ __('misc.total') }}:</small>
                </div>
                <div class="col-auto fw-bold">
                  <small><span id="total"></span> {{ $curr['code'] }}</small>
                </div>
              </div>
            </li>
          </ul>

          <small class="d-block mb-3 text-muted">
            {!! __('misc.agree_subscription', ['terms' => '<a href="'.$settings->link_terms.'" target="_blank" class="text-decoration-underline text-dark fw-bold">'. __('misc.terms_services') .'</a>']) !!}
          </small>

          <div class="alert alert-danger py-2 display-none" id="errorPurchase">
              <ul class="list-unstyled m-0" id="showErrorsPurchase"></ul>
            </div>

            <button type="submit" class="btn btn-success w-100" id="subscribe"><i></i> {{ __('misc.pay') }}</button>
            <div class="w-100 d-block text-center">
              <button type="button" class="btn btn-link e-none text-decoration-none text-reset" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
            </div>
          </div>

          </form>
        </div><!-- row -->
      </div><!-- container -->


      </div><!-- modal-body -->
    </div><!-- modal-content -->
  </div><!-- modal-dialog -->
</div><!-- modal -->
@endif
@endsection

@section('javascript')
  @auth
    <script src="{{ asset('public/js/subscription.js') }}?v={{$settings->version}}"></script>
  @endauth

  <script type="text/javascript">
  $('#plan').change(function () {
	  if ($(this).is(":checked")) {
	    $('.planMonthly').hide();
	    $('.planYearly').show();
	    $('#interval').val('year');
	  } else {
	    $('.planMonthly').show();
	    $('.planYearly').hide();
	    $('#interval').val('month');
	  }
	});
  </script>
@endsection
