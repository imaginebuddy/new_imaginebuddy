<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('subscriptions', 'gateway_order_id')) {
                $table->string('gateway_order_id', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('subscriptions', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('gateway_order_id');
            }
            if (!Schema::hasColumn('subscriptions', 'currency')) {
                $table->string('currency', 10)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('subscriptions', 'country_code')) {
                $table->string('country_code', 5)->nullable()->after('currency');
            }
            if (!Schema::hasColumn('subscriptions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled');
            }
        });

        // Backfill existing subscriptions from invoices and payment gateway defaults
        try {
            $subscriptions = DB::table('subscriptions')->get();
            foreach ($subscriptions as $sub) {
                $invoice = DB::table('invoices')->where('subscriptions_id', $sub->id)->first();
                $update = [];

                if ($invoice) {
                    $update['amount'] = $invoice->amount;
                    $update['currency'] = $invoice->currency ?: ($sub->payment_gateway === 'Razorpay' ? 'INR' : 'USD');
                } else {
                    $update['amount'] = $sub->interval === 'year' ? 27.00 : 3.00;
                    $update['currency'] = $sub->payment_gateway === 'Razorpay' ? 'INR' : 'USD';
                }

                if ($sub->payment_gateway === 'Razorpay') {
                    $update['country_code'] = 'IN';
                    $update['payment_method'] = 'card';
                } elseif ($sub->payment_gateway === 'PayPal') {
                    $update['country_code'] = 'US';
                    $update['payment_method'] = 'paypal';
                } elseif ($sub->payment_gateway === 'Wallet') {
                    $update['payment_method'] = 'wallet';
                }

                if ($sub->cancelled === 'yes') {
                    $update['cancelled_at'] = $sub->updated_at;
                }

                if (!empty($update)) {
                    DB::table('subscriptions')->where('id', $sub->id)->update($update);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Subscriptions backfill error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'gateway_order_id',
                'amount',
                'currency',
                'country_code',
                'cancelled_at'
            ]);
        });
    }
};
