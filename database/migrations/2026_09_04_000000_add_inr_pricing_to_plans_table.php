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
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'price_inr')) {
                $table->decimal('price_inr', 10, 2)->default(284.00)->after('price');
            }
            if (!Schema::hasColumn('plans', 'price_year_inr')) {
                $table->decimal('price_year_inr', 10, 2)->default(2550.00)->after('price_year');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('amount');
            }
        });

        // Initialize existing plan with confirmed dual-currency pricing
        DB::table('plans')->where('plan_id', 'pro_149')->update([
            'price' => 3.00,
            'price_year' => 27.00,
            'price_inr' => 284.00,
            'price_year_inr' => 2550.00,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'price_inr')) {
                $table->dropColumn('price_inr');
            }
            if (Schema::hasColumn('plans', 'price_year_inr')) {
                $table->dropColumn('price_year_inr');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};