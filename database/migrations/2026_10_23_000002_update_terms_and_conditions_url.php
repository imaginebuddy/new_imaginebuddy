<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely update link_terms in admin_settings to the live page URL
        DB::table('admin_settings')->update([
            'link_terms' => 'https://www.imaginebuddy.com/page/terms-and-conditions',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
