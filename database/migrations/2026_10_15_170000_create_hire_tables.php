<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('hire_settings')) {
            Schema::create('hire_settings', function (Blueprint $table) {
                $table->id();
                $table->enum('status', ['on', 'off'])->default('on');
                $table->string('hero_eyebrow')->default('CUSTOM AI PRODUCT PHOTOSHOOTS');
                $table->string('hero_headline')->default('Your Product. Your Vision. A Campaign Built Around Your Brand.');
                $table->text('hero_subheadline')->nullable();
                $table->string('hero_badge_text')->default('Custom creative direction · AI-assisted production · Human refinement');
                $table->string('hero_image')->nullable();
                
                // Pricing in USD
                $table->decimal('price_1_sku_usd', 10, 2)->default(699.00);
                $table->decimal('price_3_5_sku_usd', 10, 2)->default(499.00);
                $table->decimal('price_6_plus_sku_usd', 10, 2)->default(349.00);
                
                // Pricing in INR
                $table->decimal('price_1_sku_inr', 10, 2)->default(54999.00);
                $table->decimal('price_3_5_sku_inr', 10, 2)->default(39999.00);
                $table->decimal('price_6_plus_sku_inr', 10, 2)->default(27999.00);
                
                $table->text('custom_quote_text')->nullable();
                $table->string('service_intro_title')->default('More Than Product Images. A Creative Direction Built for Your Brand.');
                $table->text('service_intro_subtitle')->nullable();
                
                // Manageable options stored as JSON
                $table->text('form_categories')->nullable();
                $table->text('form_shoot_types')->nullable();
                $table->text('form_intended_uses')->nullable();
                $table->text('form_budgets_usd')->nullable();
                $table->text('form_budgets_inr')->nullable();
                
                $table->string('notification_email')->nullable();
                $table->string('meta_title')->default('Hire Us | Custom AI Product Photoshoots');
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->timestamps();
            });

            // Seed default configuration
            DB::table('hire_settings')->insert([
                'status' => 'on',
                'hero_eyebrow' => 'CUSTOM AI PRODUCT PHOTOSHOOTS',
                'hero_headline' => 'Your Product. Your Vision. A Campaign Built Around Your Brand.',
                'hero_subheadline' => 'Need product visuals that go beyond standard catalog photography? Hire us to create custom AI-assisted product photoshoots tailored to your brand, creative direction, and campaign goals. From premium studio-style imagery to imaginative campaign concepts, we help transform your product references into distinctive visuals for your brand.',
                'hero_badge_text' => 'Custom creative direction · AI-assisted production · Human refinement',
                'hero_image' => null,
                'price_1_sku_usd' => 699.00,
                'price_3_5_sku_usd' => 499.00,
                'price_6_plus_sku_usd' => 349.00,
                'price_1_sku_inr' => 54999.00,
                'price_3_5_sku_inr' => 39999.00,
                'price_6_plus_sku_inr' => 27999.00,
                'custom_quote_text' => 'Custom quote based on your brief, creative direction, deliverables, and project scope.',
                'service_intro_title' => 'More Than Product Images. A Creative Direction Built for Your Brand.',
                'service_intro_subtitle' => 'Every product and brand has a different story to tell. Our custom AI product photoshoots are designed for brands that need a specific visual direction, a distinctive campaign concept, or a more tailored approach to product photography.',
                'form_categories' => json_encode([
                    'Skincare & Cosmetics',
                    'Beauty & Grooming',
                    'Wellness & Supplements',
                    'Food & Beverage',
                    'Fragrance & Perfumery',
                    'Lifestyle Products',
                    'Consumer Packaged Goods (CPG)',
                    'Other'
                ]),
                'form_shoot_types' => json_encode([
                    'Product hero imagery',
                    'Lifestyle product imagery',
                    'Campaign concepts',
                    'E-commerce imagery',
                    'Paid social creative',
                    'Seasonal campaign',
                    'Other / Not sure yet'
                ]),
                'form_intended_uses' => json_encode([
                    'E-commerce product pages',
                    'Paid advertising (Meta, TikTok, Google)',
                    'Organic social media',
                    'Website & landing pages',
                    'Product launch campaign',
                    'Print or marketing collateral',
                    'Other'
                ]),
                'form_budgets_usd' => json_encode([
                    'Under $500',
                    '$500–$1,000',
                    '$1,000–$2,500',
                    '$2,500–$5,000',
                    '$5,000+',
                    'Not sure yet'
                ]),
                'form_budgets_inr' => json_encode([
                    'Under ₹40,000',
                    '₹40,000–₹80,000',
                    '₹80,000–₹2,00,000',
                    '₹2,00,000–₹4,00,000',
                    '₹4,00,000+',
                    'Not sure yet'
                ]),
                'notification_email' => null,
                'meta_title' => 'Hire Us | Custom AI Product Photoshoots',
                'meta_description' => 'Request a custom AI-assisted product photoshoot tailored to your brand, creative direction, and campaign goals. Share your project brief to discuss scope and pricing.',
                'meta_keywords' => 'custom AI photoshoot, AI product photography, brand campaigns, D2C creative studio, ecommerce photography',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        if (!Schema::hasTable('hire_inquiries')) {
            Schema::create('hire_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email');
                $table->string('company_name');
                $table->string('website_url')->nullable();
                $table->string('product_category');
                $table->string('sku_count');
                $table->text('photoshoot_types'); // json
                $table->text('intended_use')->nullable(); // json
                $table->string('preferred_direction')->nullable();
                $table->string('budget_range')->nullable();
                $table->string('currency', 10)->default('USD');
                $table->date('desired_delivery_date')->nullable();
                $table->text('project_description');
                $table->text('reference_links')->nullable();
                $table->text('additional_notes')->nullable();
                $table->string('country_code', 10)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('status', 20)->default('new');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hire_inquiries');
        Schema::dropIfExists('hire_settings');
    }
};
