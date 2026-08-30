<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PaymentGateways;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!PaymentGateways::whereName('Razorpay')->exists()) {
            PaymentGateways::create([
                'name' => 'Razorpay',
                'type' => 'normal',
                'enabled' => '1',
                'sandbox' => 'on',
                'fee' => 0.0,
                'fee_cents' => 0.0,
                'email' => '',
                'token' => '',
                'key' => 'rzp_test_key',
                'key_secret' => 'rzp_test_secret',
                'webhook_secret' => '',
                'subscription' => 1,
                'logo' => 'razorpay.png',
                'bank_info' => ''
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        PaymentGateways::whereName('Razorpay')->delete();
    }
};
