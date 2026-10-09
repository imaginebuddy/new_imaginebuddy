<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photoshoot extends Model
{
    protected $table = 'photoshoots';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'user_id',
        'categories_id',
        'prompts_count',
        'created_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'ai_model',
        'creative_direction',
        'show_product_adaptability',
        'product_adaptability',
        'show_faqs',
        'faqs'
    ];

    protected $casts = [
        'creative_direction' => 'array',
        'show_product_adaptability' => 'boolean',
        'product_adaptability' => 'array',
        'show_faqs' => 'boolean',
        'faqs' => 'array',
    ];

    /**
     * Default system product adaptability & category compatibility data
     */
    public function getDefaultProductAdaptability(): array
    {
        $categoryName = $this->category ? $this->category->name : '';
        $titleLower = strtolower(($this->title ?: '') . ' ' . $categoryName);

        // Detect appropriate sub-products & category context
        if (str_contains($titleLower, 'watch') || str_contains($titleLower, 'timepiece') || str_contains($titleLower, 'horolog')) {
            $catLabel = 'Watch';
            $catScope = 'Watches & Horology';
            $applicable = 'Chronographs, Smartwatches, Minimalist Dress Watches, Sports & Dive Watches, Luxury Timepieces, Hybrid Watches, Stainless Steel & Leather Straps';
        } elseif (str_contains($titleLower, 'beverage') || str_contains($titleLower, 'drink') || str_contains($titleLower, 'juice') || str_contains($titleLower, 'soda') || str_contains($titleLower, 'coffee') || str_contains($titleLower, 'tea') || str_contains($titleLower, 'pouch')) {
            $catLabel = 'Beverage & Drink';
            $catScope = 'Beverages & Packaged Drinks';
            $applicable = 'Beverage Cans, Stand-Up Pouches, Glass Bottles, RTD Bottles, Tetra Paks, Cold Brew Bottles, Cocktail Shakers, Drink Cartons';
        } elseif (str_contains($titleLower, 'beauty') || str_contains($titleLower, 'skincare') || str_contains($titleLower, 'cosmetic') || str_contains($titleLower, 'serum') || str_contains($titleLower, 'lotion') || str_contains($titleLower, 'haircare')) {
            $catLabel = 'Beauty & Skincare';
            $catScope = 'Cosmetics & Skincare';
            $applicable = 'Serum Droppers, Cream Jars, Pump Dispensers, Cosmetic Tubes, Spray Mists, Lipsticks & Tints, Perfume Bottles, Conditioner Bottles';
        } elseif (str_contains($titleLower, 'food') || str_contains($titleLower, 'snack') || str_contains($titleLower, 'supplement') || str_contains($titleLower, 'protein') || str_contains($titleLower, 'bar')) {
            $catLabel = 'Food & Nutrition';
            $catScope = 'Packaged Food & Nutrition';
            $applicable = 'Protein Bar Wrappers, Snack Pouches, Packaging Boxes, Supplement Tins, Gourmet Jars, Granola Bags, Nutritional Canisters';
        } elseif (str_contains($titleLower, 'electronic') || str_contains($titleLower, 'tech') || str_contains($titleLower, 'gadget') || str_contains($titleLower, 'audio') || str_contains($titleLower, 'phone')) {
            $catLabel = 'Electronics & Tech';
            $catScope = 'Consumer Tech & Gadgets';
            $applicable = 'Wireless Earbuds, Headphones, Smart Devices, Portable Speakers, Wearables, Mobile Cases, Power Accessories';
        } elseif (str_contains($titleLower, 'shoe') || str_contains($titleLower, 'sneaker') || str_contains($titleLower, 'fashion') || str_contains($titleLower, 'apparel') || str_contains($titleLower, 'bag')) {
            $catLabel = 'Fashion & Apparel';
            $catScope = 'Fashion & Accessories';
            $applicable = 'Sneakers & Footwear, Handbags & Totes, Wallets, Eyewear & Sunglasses, Leather Goods, Jewelry, Apparel Mockups';
        } else {
            $catLabel = $categoryName ?: 'Product';
            $catScope = $categoryName ?: 'Commercial Products';
            $applicable = 'Different Packaging Formats, Multiple Sizes & Shapes, Custom Brand Colorways, Diverse Finishes & Textures, Full Product Line SKUs';
        }

        return [
            'badge' => 'Universal Category Compatibility',
            'heading' => 'Engineered for Any ' . $catLabel . ' — Not Just the Demo Product',
            'description' => 'Our library provides production-grade, battle-tested prompt recipes specifically engineered for commercial product photography. No manual prompt editing is required. The demo product shown in these examples demonstrates lighting balance, reflections, and compositions in action. You can seamlessly apply this entire prompt collection to any ' . strtolower($catLabel) . ' regardless of specific design, brand, or SKU.',
            'subtext' => 'Simply use the prompt with your product image, and the intelligent prompt framework analyzes your product and automatically adapts the visual direction including colors, background, theme, props, composition, lighting, textures, and overall styling to create a cohesive, premium product photoshoot tailored to your product. Run it in Midjourney, Gemini, ChatGPT, or your preferred AI image generator and generate studio-grade commercial visuals in minutes.',
            'blueprint_title' => 'Category Compatibility Spec',
            'category_scope' => $catScope,
            'workflow_mode' => 'Zero-Edit Image-to-Prompt',
            'prompt_adaptation' => 'Auto-Tuned Colors & Props',
            'product_fit' => 'Any Brand, Style or SKU',
            'show_applicable_products' => '1',
            'applicable_products_title' => 'Works Seamlessly Across These Products & Styles',
            'applicable_products' => $applicable,
            'step_1_title' => 'Just Copy Your Selected Prompt',
            'step_1_desc' => 'Browse the multi-angle studio catalog below and copy your desired commercial prompt recipe with a single click. Every formula is pre-calibrated for production use.',
            'step_2_title' => 'No Need to Change Any Settings',
            'step_2_desc' => 'Zero prompt surgery or technical tweaking required. The camera optics, lighting balance, and surface physics automatically adapt to your product.',
            'step_3_title' => 'Paste in Generator & Generate',
            'step_3_desc' => 'Attach your product image and run the prompt in Midjourney, Google Gemini, ChatGPT, or your preferred AI image generator to produce studio-grade visuals in minutes.',
            'adaptation_highlight' => 'Our library provides production-grade, battle-tested prompt recipes specifically engineered for commercial product photography. No manual prompt editing is required. Simply use the prompt with your product image, and the intelligent prompt framework analyzes your product and automatically adapts the visual direction including colors, background, theme, props, composition, lighting, textures, and overall styling to create a cohesive, premium product photoshoot tailored to your product. Run it in Midjourney, Gemini, ChatGPT, or your preferred AI image generator and generate studio-grade commercial visuals in minutes.',
        ];
    }

    /**
     * Accessor for resolved product adaptability data (custom with fallback to defaults)
     */
    public function getProductAdaptabilityDataAttribute(): array
    {
        $defaults = $this->getDefaultProductAdaptability();
        $custom = is_array($this->product_adaptability) ? $this->product_adaptability : [];

        $result = [];
        foreach ($defaults as $key => $defaultVal) {
            if ($key === 'show_applicable_products') {
                $result[$key] = isset($custom[$key]) ? (string)$custom[$key] : (string)$defaultVal;
            } else {
                $val = isset($custom[$key]) ? trim((string)$custom[$key]) : '';
                $result[$key] = $val !== '' ? $val : $defaultVal;
            }
        }

        return $result;
    }

    /**
     * Default system creative direction blueprint data
     */
    public function getDefaultCreativeDirection(): array
    {
        $title = $this->title ?: 'Commercial Products';
        $model = $this->ai_model ?: 'ChatGPT, Google Gemini, and Midjourney';

        return [
            'badge' => 'Creative Direction & Camera Settings',
            'heading' => 'Commercial Lookbook Direction for ' . $title,
            'description' => 'Every prompt in this curated lookbook is crafted to maintain visual identity and studio lighting coherence across multiple camera focal lengths and compositions. Calibrated specifically for ' . $model . ', this session solves multi-angle AI inconsistency by standardizing core packaging proportions, surface reflectance, and color temperature.',
            'subtext' => 'Ideal for e-commerce brands, packaging designers, and 3D visualizers seeking consistent product imagery without unpredictable prompt drift.',
            'blueprint_title' => 'Camera & Studio Blueprint',
            'lighting' => 'Diffused Softbox 5500K',
            'lenses' => '50mm, 85mm & 100mm Macro',
            'target_ai' => $this->ai_model ?: 'ChatGPT, Gemini, Midjourney',
            'commercial' => 'E-Commerce Ready',
            'pillar_1_title' => 'Studio Lighting & Atmosphere',
            'pillar_1_desc' => 'Diffused three-point softbox illumination, balanced rim backlight separation, and natural specular caustics. Recreates organic photon scatter and realistic depth-of-field without artificial AI plastic sheen.',
            'pillar_2_title' => 'Multi-Angle Coverage',
            'pillar_2_desc' => 'Structured for comprehensive catalog coverage: eye-level hero packshots, dynamic 45-degree isometric angles, 100mm macro label close-ups, and in-context lifestyle frames for e-commerce listings.',
            'pillar_3_title' => 'Cross-Platform Model Fidelity',
            'pillar_3_desc' => 'Optimized for ChatGPT (DALL-E 3), Google Gemini, and Midjourney (v6). Consistent subject descriptor locking and seed retention preserve product geometry across every prompt angle.',
        ];
    }

    /**
     * Accessor for resolved creative direction data (custom with fallback to defaults)
     */
    public function getCreativeDirectionDataAttribute(): array
    {
        $defaults = $this->getDefaultCreativeDirection();
        $custom = is_array($this->creative_direction) ? $this->creative_direction : [];

        $result = [];
        foreach ($defaults as $key => $defaultVal) {
            $val = isset($custom[$key]) ? trim((string)$custom[$key]) : '';
            $result[$key] = $val !== '' ? $val : $defaultVal;
        }

        return $result;
    }

    /**
     * Default system 5 FAQs
     */
    public function getDefaultFaqs(): array
    {
        $model = $this->ai_model ? ' (optimized specifically for ' . $this->ai_model . ')' : '';

        return [
            [
                'question' => 'How do I customize these photoshoot prompts for my own product?',
                'answer' => 'Simply substitute the bracketed subject, packaging style, and color descriptors with your own brand details while retaining the camera lens, lighting modifiers, and focal length tokens. This preserves consistent studio quality while adapting the scene to your unique product.'
            ],
            [
                'question' => 'Which AI models can I run these photoshoot prompts on?',
                'answer' => 'These prompt formulas are engineered and tested for ChatGPT (DALL-E 3 & GPT-4o), Google Gemini, and Midjourney' . $model . '. You can also run them across Stable Diffusion and Flux with the provided style and lighting keywords.'
            ],
            [
                'question' => 'How do I maintain prompt consistency across multiple camera angles?',
                'answer' => 'To preserve visual consistency, lock your product\'s core identity tokens (shape, material textures, label color codes), keep uniform studio lighting tags (softbox diffusion, rim highlights, neutral studio backdrop), and vary only the camera angle modifier (front packshot, 45-degree angle, macro close-up). In Midjourney, keep the same --seed or image reference (--sref); in ChatGPT and Google Gemini, generate angles within the same conversation thread to retain visual memory.'
            ],
            [
                'question' => 'What camera angles and lighting setups are included in this batch?',
                'answer' => 'This photoshoot batch includes front-facing hero product shots, 45-degree perspective angles, macro texture close-ups, and natural lifestyle interactions. Each prompt balances softbox studio lighting, clean reflections, and background depth-of-field separation.'
            ],
            [
                'question' => 'Are the images generated from these prompts free for commercial use?',
                'answer' => 'Yes. Visual assets generated with these prompt recipes can be deployed across commercial e-commerce storefronts (Shopify, Amazon, WooCommerce), digital ad campaigns, social media channels, and print collateral according to each AI generator\'s terms of service.'
            ],
        ];
    }

    /**
     * Accessor for resolved 5 FAQ items (custom with fallback to defaults)
     */
    public function getFaqItemsAttribute(): array
    {
        $defaults = $this->getDefaultFaqs();
        $custom = is_array($this->faqs) ? $this->faqs : [];

        $items = [];
        for ($i = 0; $i < 5; $i++) {
            $defaultItem = $defaults[$i] ?? ['question' => '', 'answer' => ''];
            $customItem = $custom[$i] ?? null;

            $q = (is_array($customItem) && isset($customItem['question'])) ? trim((string)$customItem['question']) : '';
            $a = (is_array($customItem) && isset($customItem['answer'])) ? trim((string)$customItem['answer']) : '';

            $items[] = [
                'question' => $q !== '' ? $q : $defaultItem['question'],
                'answer' => $a !== '' ? $a : $defaultItem['answer'],
                'is_custom' => ($q !== '' || $a !== '')
            ];
        }

        return $items;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categories_id');
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'photoshoot_id')->where('status', 'active');
    }

    public function allImages()
    {
        return $this->hasMany(Images::class, 'photoshoot_id');
    }
}
