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
    use Traits\FunctionsTrait;

    public function showSubscription(Request $request)
    {
        $payment = PaymentGateways::whereName('Razorpay')->firstOrFail();
        $plan = Plans::wherePlanId($request->plan)->whereStatus('1')->firstOrFail();

        $amount = $request->interval == 'month' ? ($plan->price_inr ?: 284.00) : ($plan->price_year_inr ?: 2550.00);
        $totalAmount = round($amount * 100); // in paise

        try {
            $api = new Api($payment->key, $payment->key_secret);

            $orderData = [
                'receipt'         => 'sub_' . auth()->id() . '_' . time(),
                'amount'          => (int) $totalAmount,
                'currency'        => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $api->order->create($orderData);
        } catch (\Exception $e) {
            \Log::error('Razorpay Error: ' . $e->getMessage());
            $errorMsg = 'Razorpay Error: ' . $e->getMessage();
            if (str_contains($e->getMessage(), 'Authentication failed')) {
                $errorMsg = 'Razorpay Error: Authentication failed. Please verify your Razorpay Key ID and Secret in Admin Settings (Payment Settings > Razorpay).';
            }
            return redirect('pricing')->withError($errorMsg);
        }

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
        $planPrice = $request->interval == 'month' ? ($plan->price_inr ?: 284.00) : ($plan->price_year_inr ?: 2550.00);

        // Fetch payment details to capture exact payment method (upi, card, netbanking, wallet)
        $paymentMethod = 'card';
        try {
            $razorpayPayment = $api->payment->fetch($request->razorpay_payment_id);
            if ($razorpayPayment && isset($razorpayPayment->method)) {
                $paymentMethod = strtolower($razorpayPayment->method);
            }
        } catch (\Exception $e) {
            \Log::info('Could not fetch Razorpay payment method: ' . $e->getMessage());
        }

        $subscription = new Subscriptions();
        $subscription->user_id = auth()->id();
        $subscription->stripe_price = $plan->plan_id;
        $subscription->stripe_id = $request->razorpay_payment_id;
        $subscription->stripe_status = 'active';
        $subscription->last_payment = $request->razorpay_payment_id;
        $subscription->payment_gateway = 'Razorpay';
        $subscription->payment_method = $paymentMethod;
        $subscription->gateway_order_id = $request->razorpay_order_id;
        $subscription->amount = $planPrice;
        $subscription->currency = 'INR';
        $subscription->country_code = 'IN';
        $subscription->ends_at = Helper::planInterval($request->interval);
        $subscription->interval = $request->interval;
        $subscription->save();

        // Create Invoice
        $this->invoiceSubscription($subscription->user_id, $subscription->id, $planPrice, auth()->user()->taxesPayable(), true, 'INR');

        auth()->user()->update([
            'downloads' => $plan->downloads_per_month
        ]);

        return redirect('account/subscription')->withSuccess(__('misc.subscription_success'));
    }
}
