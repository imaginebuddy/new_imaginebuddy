<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add GST fields to admin_settings table
        Schema::table('admin_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_settings', 'gstin')) {
                $table->string('gstin', 15)->nullable()->after('vat');
            }
            if (!Schema::hasColumn('admin_settings', 'gst_percentage')) {
                $table->decimal('gst_percentage', 5, 2)->default(18.00)->nullable()->after('gstin');
            }
            if (!Schema::hasColumn('admin_settings', 'supplier_state')) {
                $table->string('supplier_state', 100)->nullable()->after('gst_percentage');
            }
            if (!Schema::hasColumn('admin_settings', 'lut_number')) {
                $table->string('lut_number', 100)->nullable()->after('supplier_state');
            }
        });

        // 2. Add GST snapshot columns to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'taxable_amount')) {
                $table->decimal('taxable_amount', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('invoices', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0.00)->after('taxable_amount');
            }
            if (!Schema::hasColumn('invoices', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->default(0.00)->after('gst_rate');
            }
            if (!Schema::hasColumn('invoices', 'cgst_amount')) {
                $table->decimal('cgst_amount', 10, 2)->default(0.00)->after('cgst_rate');
            }
            if (!Schema::hasColumn('invoices', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->default(0.00)->after('cgst_amount');
            }
            if (!Schema::hasColumn('invoices', 'sgst_amount')) {
                $table->decimal('sgst_amount', 10, 2)->default(0.00)->after('sgst_rate');
            }
            if (!Schema::hasColumn('invoices', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->default(0.00)->after('sgst_amount');
            }
            if (!Schema::hasColumn('invoices', 'igst_amount')) {
                $table->decimal('igst_amount', 10, 2)->default(0.00)->after('igst_rate');
            }
            if (!Schema::hasColumn('invoices', 'total_tax_amount')) {
                $table->decimal('total_tax_amount', 10, 2)->default(0.00)->after('igst_amount');
            }
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('total_tax_amount');
            }
            if (!Schema::hasColumn('invoices', 'tax_type')) {
                $table->string('tax_type', 50)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('invoices', 'tax_details')) {
                $table->json('tax_details')->nullable()->after('tax_type');
            }
        });

        // 3. Add state to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 100)->nullable()->after('countries_id');
            }
            if (!Schema::hasColumn('users', 'customer_gstin')) {
                $table->string('customer_gstin', 15)->nullable()->after('state');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'gst_percentage', 'supplier_state', 'lut_number']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'taxable_amount',
                'gst_rate',
                'cgst_rate',
                'cgst_amount',
                'sgst_rate',
                'sgst_amount',
                'igst_rate',
                'igst_amount',
                'total_tax_amount',
                'total_amount',
                'tax_type',
                'tax_details',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['state', 'customer_gstin']);
        });
    }
};
