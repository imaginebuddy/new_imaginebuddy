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
        Schema::table('categories', function (Blueprint $table) {
            $table->text('hero_lead')->nullable()->after('description');
            $table->longText('content_guide')->nullable()->after('hero_lead');
            $table->boolean('cta_banner_enabled')->default(false)->after('content_guide');
            $table->string('cta_banner_title')->nullable()->after('cta_banner_enabled');
            $table->text('cta_banner_desc')->nullable()->after('cta_banner_title');
            $table->string('cta_banner_btn_text')->nullable()->after('cta_banner_desc');
            $table->string('cta_banner_btn_url')->nullable()->after('cta_banner_btn_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'hero_lead',
                'content_guide',
                'cta_banner_enabled',
                'cta_banner_title',
                'cta_banner_desc',
                'cta_banner_btn_text',
                'cta_banner_btn_url',
            ]);
        });
    }
};
