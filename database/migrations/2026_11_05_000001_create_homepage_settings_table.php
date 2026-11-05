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
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();

            // 1. Hero Section
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('search_placeholder')->nullable();

            // 2. What Makes ImagineBuddy Different Section
            $table->boolean('diff_enabled')->default(true);
            $table->string('diff_eyebrow')->nullable();
            $table->string('diff_heading')->nullable();
            $table->text('diff_description_1')->nullable();
            $table->text('diff_description_2')->nullable();
            $table->string('diff_card_generic_title')->nullable();
            $table->json('diff_card_generic_items')->nullable();
            $table->string('diff_card_adaptive_title')->nullable();
            $table->json('diff_card_adaptive_items')->nullable();
            $table->string('diff_cta_text')->nullable();
            $table->string('diff_cta_url')->nullable();
            $table->text('diff_disclaimer')->nullable();

            // 3. Latest Photoshoots Section
            $table->boolean('photoshoots_enabled')->default(true);
            $table->string('photoshoots_heading')->nullable();
            $table->text('photoshoots_subheading')->nullable();
            $table->string('photoshoots_cta_text')->nullable();
            $table->string('photoshoots_cta_url')->nullable();

            // 4. How It Works Section
            $table->boolean('how_it_works_enabled')->default(true);
            $table->string('how_it_works_eyebrow')->nullable();
            $table->string('how_it_works_heading')->nullable();
            $table->json('how_it_works_steps')->nullable();
            $table->string('how_it_works_cta_text')->nullable();
            $table->string('how_it_works_cta_url')->nullable();
            $table->text('how_it_works_subtext')->nullable();

            // 5. Who Is ImagineBuddy For Section
            $table->boolean('who_is_it_for_enabled')->default(true);
            $table->string('who_is_it_for_heading')->nullable();
            $table->text('who_is_it_for_subheading')->nullable();
            $table->json('who_is_it_for_items')->nullable();
            $table->string('who_is_it_for_cta_text')->nullable();
            $table->string('who_is_it_for_cta_url')->nullable();

            // 6. Free vs Pro Plans Section
            $table->boolean('free_vs_pro_enabled')->default(true);
            $table->string('free_pro_eyebrow')->nullable();
            $table->string('free_pro_heading')->nullable();
            $table->text('free_pro_subheading')->nullable();
            $table->string('free_card_title')->nullable();
            $table->text('free_card_desc')->nullable();
            $table->string('free_card_cta_text')->nullable();
            $table->string('free_card_cta_url')->nullable();
            $table->string('pro_card_title')->nullable();
            $table->text('pro_card_desc')->nullable();
            $table->string('pro_card_cta_text')->nullable();
            $table->string('pro_card_cta_url')->nullable();
            $table->text('free_pro_footnote')->nullable();

            // 7. More Ways to Showcase Your Product (Promo Section)
            $table->boolean('promo_section_enabled')->default(true);
            $table->string('promo_eyebrow')->nullable();
            $table->string('promo_heading')->nullable();
            $table->text('promo_subheading')->nullable();
            $table->json('promo_items')->nullable();
            $table->text('promo_note')->nullable();
            $table->string('promo_cta_text')->nullable();
            $table->string('promo_cta_url')->nullable();

            // 8. Transformation Showcase Section
            $table->boolean('transformation_enabled')->default(true);
            $table->string('transformation_eyebrow')->nullable();
            $table->string('transformation_heading')->nullable();
            $table->text('transformation_subheading')->nullable();
            $table->string('transformation_before_image')->nullable();
            $table->string('transformation_after_image')->nullable();
            $table->string('transformation_before_label')->nullable();
            $table->string('transformation_after_label')->nullable();
            $table->string('transformation_right_badge')->nullable();
            $table->string('transformation_right_badge_sub')->nullable();
            $table->string('transformation_right_headline')->nullable();
            $table->text('transformation_right_description')->nullable();
            $table->json('transformation_traditional_items')->nullable();
            $table->string('transformation_traditional_price_inr')->nullable();
            $table->string('transformation_traditional_price_usd')->nullable();
            $table->json('transformation_library_items')->nullable();
            $table->string('transformation_library_price_inr')->nullable();
            $table->string('transformation_library_price_usd')->nullable();
            $table->string('transformation_cta_title')->nullable();
            $table->text('transformation_cta_subtitle')->nullable();
            $table->string('transformation_cta_btn_text')->nullable();
            $table->string('transformation_cta_btn_url')->nullable();

            // 9. FAQ Section
            $table->boolean('faq_enabled')->default(true);
            $table->string('faq_badge')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_subtitle')->nullable();
            $table->string('faq_contact_text')->nullable();
            $table->string('faq_contact_url')->nullable();
            $table->string('faq_view_all_text')->nullable();
            $table->string('faq_view_all_url')->nullable();
            $table->json('faq_items')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homepage_settings');
    }
};
