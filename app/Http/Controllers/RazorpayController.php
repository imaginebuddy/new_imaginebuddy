<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use App\Models\User;
use App\Models\Plans;
use App\Models\Subscriptions;
use App\Models\PaymentGateways;
use App\Models\AdminSettings;
use App\Helper;
use Illuminate\Http\Request;

class RazorpayController extends Controller
{
    public function showSubscription(Request $request)
    {
        $payment = PaymentGateways::whereName('Razorpay')->firstOrFail();
        $plan = Plans::wherePlanId($request->plan)->whereStatus('1')->firstOrFail();

        $amount = $request->interval == 'month' ? $plan->price : $plan->price_year;
        $totalAmount = Helper::amountGross($amount) * 100; // in paise

        $api = new Api($payment->key, $payment->key_secret);

        $orderData = [
            'receipt'         => 'sub_' . auth()->id() . '_' . time(),
            'amount'          => (int) $totalAmount,
            'currency'        => config('settings.currency_code', 'INR'),
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);

        return view('plans.razorpay-checkout', [
            'order' => $razorpayOrder,
            'plan' => $plan,
            'payment' => $payment,
            'interval' => $request->interval
        ]);
    }

    public function processSubscription(Request $request)
    {
        $payment = PaymentGateways::whereName('Razorpay')->firstOrFail();
        $api = new Api($payment->key, $payment->key_secret);

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            return redirect('account/subscription')->withError('Razorpay payment signature verification failed.');
        }

        $plan = Plans::wherePlanId($request->plan_id)->firstOrFail();

        $subscription = new Subscriptions();
        $subscription->user_id = auth()->id();
        $subscription->stripe_price = $plan->plan_id;
        $subscription->stripe_id = $request->razorpay_payment_id;
        $subscription->stripe_status = 'active';
        $subscription->ends_at = Helper::planInterval($request->interval);
        $subscription->interval = $request->interval;
        $subscription->payment_gateway = 'Razorpay';
        $subscription->save();

        auth()->user()->update([
            'downloads' => $plan->downloads_per_month
        ]);

        return redirect('account/subscription')->withSuccess(__('misc.subscription_success'));
    }
}
