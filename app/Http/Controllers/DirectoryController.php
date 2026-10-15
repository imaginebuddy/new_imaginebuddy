<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Categories;
use App\Models\Subcategories;
use App\Models\SeoLandingPage;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Photoshoot;
use App\Helper;

class DirectoryController extends Controller
{
    /**
     * Cache expiration: 24 hours
     */
    const CACHE_MINUTES = 1440;

    /**
     * Clear the directory cache
     */
    public static function clearCache(): void
    {
        Cache::forget('imaginebuddy_directory_dataset');
    }

    /**
     * Display the ImagineBuddy Directory page
     */
    public function index(Request $request)
    {
        if ($request->has('refresh') || $request->has('clear_cache')) {
            self::clearCache();
        }

        // Apply SEO Override
        Helper::seo()->override([
            'title' => 'ImagineBuddy Directory — AI Prompts, Product Photography & Tools',
            'meta_title' => 'ImagineBuddy Directory — AI Prompts, Product Photography & Tools',
            'meta_description' => 'Explore the ImagineBuddy directory to discover AI image prompts, product photography collections, creative styles, free tools, and practical resources.',
            'canonical_url' => url('directory'),
        ]);

        $data = Cache::remember('imaginebuddy_directory_dataset', now()->addMinutes(self::CACHE_MINUTES), function () {
            // 1. Dynamic E-commerce & Product Categories from Database
            $categories = Categories::where('mode', 'on')
                ->where('slug', '!=', 'uncategorized')
                ->orderBy('name')
                ->with(['subcategories' => function ($q) {
                    $q->where('mode', 'on')->orderBy('name');
                }])
                ->get();

            // Category Icon Mapping for Premium UI Cards
            $categoryIconMap = [
                'beauty-and-skincare' => 'bi-droplet-half',
                'jewellery-and-accessories' => 'bi-gem',
                'foods-and-drinks' => 'bi-cup-hot',
                'haircare' => 'bi-stars',
                'electronics-and-gadgets' => 'bi-phone',
                'bags-and-luggage' => 'bi-bag',
                'pet-products' => 'bi-balloon-heart',
                'furniture-and-decor' => 'bi-lamp',
                'fashion-and-apparel' => 'bi-person-standing',
                'kitchen-and-dining' => 'bi-egg-fried',
                'fitness-and-activewear' => 'bi-activity',
                'home-and-living' => 'bi-house',
                'personal-care' => 'bi-heart-pulse',
                'makeup-and-cosmetics' => 'bi-brush',
                'footwear' => 'bi-tag',
                'watches' => 'bi-stopwatch',
                'health-and-supplements' => 'bi-capsule',
                'fragrance-and-perfume' => 'bi-flower2',
                'baby-and-kids' => 'bi-emoji-smile',
                'home-appliances' => 'bi-plug',
                'sports-and-outdoor' => 'bi-bicycle',
                'toys-and-games' => 'bi-controller',
                'stationery-and-office' => 'bi-pencil',
                'gifts-and-personalized-products' => 'bi-gift',
                'grocery-and-staples' => 'bi-basket',
            ];

            // Commercial & D2C Product Photography Curated Queries (all 60 user-provided queries contextually mapped)
            $categoryQueriesMap = [
                'beauty-and-skincare' => [
                    'Splash & Motion Photography',
                    'Water, Dew & Freshness Effects',
                    'Macro Texture & Close-Ups',
                    'Model & Hand Interaction Shots',
                    'Transparent & Translucent Product Shots',
                    'Studio Hero Photoshoots',
                ],
                'jewellery-and-accessories' => [
                    'Macro Texture & Close-Ups',
                    'Glass, Gloss & Surface Detail Shots',
                    'Reflection & Shadow Play',
                    'Material & Craftsmanship Details',
                    'Dramatic Low-Key Lighting',
                    '45-Degree Angled Product Shots',
                ],
                'foods-and-drinks' => [
                    'Splash & Motion Photography',
                    'Water, Dew & Freshness Effects',
                    'Top-Down Overhead Shots',
                    'Sunlight Patterns & Window Shadows',
                    'Freeze-Motion Action Shots',
                    'Storytelling & Themed Scenes',
                ],
                'haircare' => [
                    'Product-in-Use Demonstrations',
                    'Model & Hand Interaction Shots',
                    'Before & After Comparisons',
                    'High-Key Bright Photography',
                    'Packaging & Unboxing Photography',
                    'Water, Dew & Freshness Effects',
                ],
                'electronics-and-gadgets' => [
                    'Exploded-View Product Photography',
                    'Floating & Levitation Shots',
                    '360° & Multi-Angle Views',
                    'Feature-by-Feature Detail Series',
                    'Neon & Colored-Gel Lighting',
                    'Side-Profile Product Photography',
                ],
                'bags-and-luggage' => [
                    'Material & Craftsmanship Details',
                    'Scale & Size Comparison Shots',
                    'Lifestyle & In-Context',
                    'In-the-Box Product Layouts',
                    'Top-Down Overhead Shots',
                    'Side-Profile Product Photography',
                ],
                'pet-products' => [
                    'Product-in-Use Demonstrations',
                    'UGC-Style Authentic Photography',
                    'Sibling & Family Lifestyle Scenes',
                    'Freeze-Motion Action Shots',
                    'Playful Storybook Setups',
                    'Natural Light Photography',
                ],
                'furniture-and-decor' => [
                    'Nature-Inspired & Botanical Styling',
                    'Wood, Linen & Organic Textures',
                    'Sunlight Patterns & Window Shadows',
                    'Negative Space Compositions',
                    'Golden Hour Product Photography',
                    'Lifestyle & In-Context',
                ],
                'fashion-and-apparel' => [
                    'Premium Editorial Photography',
                    'Model & Hand Interaction Shots',
                    'Silhouette & Shape Emphasis',
                    'Color Coordination & Palette Stories',
                    'Natural Light Photography',
                    '45-Degree Angled Product Shots',
                ],
                'kitchen-and-dining' => [
                    'Flat-Lay Compositions',
                    'Product-in-Use Demonstrations',
                    'Storytelling & Themed Scenes',
                    'Wood, Linen & Organic Textures',
                    'Top-Down Overhead Shots',
                    'Reflection & Shadow Play',
                ],
                'fitness-and-activewear' => [
                    'Freeze-Motion Action Shots',
                    'Sequential Action Storytelling',
                    'Model & Hand Interaction Shots',
                    'Dramatic Low-Key Lighting',
                    'UGC-Style Authentic Photography',
                    'White Background E-commerce',
                ],
                'home-and-living' => [
                    'Sunlight Patterns & Window Shadows',
                    'Geometric Composition Photography',
                    'Symmetry & Repetition Shots',
                    'Monochromatic Product Photography',
                    'Natural Light Photography',
                    'Creative Props & Set Design',
                ],
                'personal-care' => [
                    'White Background E-commerce',
                    'Model & Hand Interaction Shots',
                    'Infographic & Feature Highlight Shots',
                    'High-Key Bright Photography',
                    'In-the-Box Product Layouts',
                    'Studio Hero Photoshoots',
                ],
                'makeup-and-cosmetics' => [
                    'Color-Blocked Studio Photography',
                    'Pastel Dreamscape Photography',
                    'Macro Texture & Close-Ups',
                    'Color Coordination & Palette Stories',
                    'Glass, Gloss & Surface Detail Shots',
                    'Social Media Campaign Creatives',
                ],
                'footwear' => [
                    'Side-Profile Product Photography',
                    '45-Degree Angled Product Shots',
                    '360° & Multi-Angle Views',
                    'Floating & Levitation Shots',
                    'Material & Craftsmanship Details',
                    'Studio Hero Photoshoots',
                ],
                'watches' => [
                    'Glass, Gloss & Surface Detail Shots',
                    'Dramatic Low-Key Lighting',
                    'Macro Texture & Close-Ups',
                    'Material & Craftsmanship Details',
                    '45-Degree Angled Product Shots',
                    'Reflection & Shadow Play',
                ],
                'health-and-supplements' => [
                    'White Background E-commerce',
                    'Infographic & Feature Highlight Shots',
                    'Scale & Size Comparison Shots',
                    'Nature-Inspired & Botanical Styling',
                    'Packaging & Unboxing Photography',
                    'High-Key Bright Photography',
                ],
                'fragrance-and-perfume' => [
                    'Transparent & Translucent Product Shots',
                    'Reflection & Shadow Play',
                    'Backlit & Silhouette Shots',
                    'Cloud, Sky & Dream-Inspired Scenes',
                    'Splash & Motion Photography',
                    'Dramatic Low-Key Lighting',
                ],
                'baby-and-kids' => [
                    'Nursery & Kids’ Room Styling',
                    'Sibling & Family Lifestyle Scenes',
                    'Playful Storybook Setups',
                    'Pastel Dreamscape Photography',
                    'UGC-Style Authentic Photography',
                    'Natural Light Photography',
                ],
                'home-appliances' => [
                    'Product Assembly & How-It-Works Shots',
                    'Exploded-View Product Photography',
                    'Feature-by-Feature Detail Series',
                    '360° & Multi-Angle Views',
                    'In-the-Box Product Layouts',
                    'White Background E-commerce',
                ],
                'sports-and-outdoor' => [
                    'Freeze-Motion Action Shots',
                    'Long-Exposure Light Trails',
                    'Nature-Inspired & Botanical Styling',
                    'Golden Hour Product Photography',
                    'Sequential Action Storytelling',
                    'Lifestyle & In-Context',
                ],
                'toys-and-games' => [
                    'Miniature World & Diorama Scenes',
                    'Whimsical Fantasy Setups',
                    'Playful Storybook Setups',
                    'Color-Blocked Studio Photography',
                    'Cloud, Sky & Dream-Inspired Scenes',
                    'Creative Props & Set Design',
                ],
                'stationery-and-office' => [
                    'Flat-Lay Compositions',
                    'Top-Down Overhead Shots',
                    'Geometric Composition Photography',
                    'Symmetry & Repetition Shots',
                    'Negative Space Compositions',
                    'Premium Minimalist Compositions',
                ],
                'gifts-and-personalized-products' => [
                    'Gift Sets & Bundled Products',
                    'Packaging & Unboxing Photography',
                    'Seasonal & Holiday Campaigns',
                    'Social Media Campaign Creatives',
                    'Storytelling & Themed Scenes',
                    'Creative Props & Set Design',
                ],
                'grocery-and-staples' => [
                    'In-the-Box Product Layouts',
                    'Scale & Size Comparison Shots',
                    'Nature-Inspired & Botanical Styling',
                    'White Background E-commerce',
                    'Flat-Lay Compositions',
                    'Top-Down Overhead Shots',
                ],
            ];

            // 2. Published Prompt Guides & Landing Pages from Database (Safe for 1000s of guides)
            $guides = SeoLandingPage::where('status', 'published')
                ->with('category')
                ->orderBy('id', 'desc')
                ->take(18)
                ->get(['id', 'title', 'slug', 'excerpt', 'category_id', 'published_at']);

            // 3. Blog Categories & Recent Published Articles
            $blogCategories = BlogCategory::orderBy('name')->get(['id', 'name', 'slug']);
            $blogPosts = BlogPost::where('status', 'published')
                ->orderBy('id', 'desc')
                ->take(18)
                ->get(['id', 'title', 'slug', 'excerpt']);

            // 4. Photoshoots
            $photoshoots = Photoshoot::orderBy('id', 'desc')
                ->take(12)
                ->get(['id', 'title', 'slug']);

            // Helper to dynamically resolve Blog URLs from database (Zero hardcoded links)
            $resolveBlogUrl = function ($keywords, $fallback = 'blog') use ($blogPosts) {
                $keywords = (array) $keywords;
                foreach ($keywords as $kw) {
                    $matched = $blogPosts->first(function ($b) use ($kw) {
                        return $b->slug === $kw || str_contains($b->slug, $kw) || str_contains(strtolower($b->title), $kw);
                    });
                    if ($matched) {
                        return url('blog/' . $matched->slug);
                    }
                }
                return url($fallback);
            };

            // 5. AI Image Prompts by Engine (Structured with direct URLs)
            $engines = [
                [
                    'name' => 'Google Gemini / Imagen Prompts',
                    'badge' => 'Imagen 3',
                    'icon' => 'bi-google',
                    'url' => url('ai-model/gemini'),
                    'links' => [
                        ['title' => 'Studio Product Renders with Gemini', 'url' => url('search?q=gemini+product'), 'keywords' => 'google gemini imagen 3 clean background'],
                        ['title' => 'Macro Commercial Photography', 'url' => url('search?q=gemini+macro'), 'keywords' => 'macro 100mm extreme detail gemini'],
                        ['title' => 'Lifestyle & Advertising Campaigns', 'url' => url('search?q=lifestyle+advertising'), 'keywords' => 'lifestyle campaign model d2c'],
                        ['title' => 'Clean Studio Backgrounds & Scenes', 'url' => url('search?q=studio+background'), 'keywords' => 'podium pastel minimal studio'],
                        ['title' => 'Gemini Prompt Writing Tutorial', 'url' => $resolveBlogUrl(['google-flow', 'gemini']), 'keywords' => 'gemini guide tutorial prompt recipe'],
                        ['title' => 'All Google Gemini Prompts', 'url' => url('ai-model/gemini'), 'keywords' => 'gemini prompts archive collection'],
                    ],
                ],
                [
                    'name' => 'ChatGPT & Stable Diffusion Prompts',
                    'badge' => 'SDXL & DALL·E',
                    'icon' => 'bi-cpu',
                    'url' => url('ai-model/chatgpt'),
                    'links' => [
                        ['title' => 'ChatGPT Image Prompts & Formulas', 'url' => url('ai-model/chatgpt'), 'keywords' => 'chatgpt dall-e 3 prompt builder ideas'],
                        ['title' => 'SDXL Product & E-commerce Mockups', 'url' => url('search?q=sdxl+product'), 'keywords' => 'stable diffusion sdxl mockup ecommerce'],
                        ['title' => 'Editorial & Modern Lifestyle Prompts', 'url' => url('search?q=editorial+lifestyle'), 'keywords' => 'editorial lifestyle fashion mood'],
                        ['title' => 'Artistic & Illustrated Concept Art', 'url' => url('search?q=artistic+style'), 'keywords' => 'artistic 3d render vector illustration'],
                        ['title' => 'Negative Prompt Formulas Cheat Sheet', 'url' => $resolveBlogUrl(['negative-prompts', 'artifacts']), 'keywords' => 'negative prompts avoid distortions clean image'],
                        ['title' => 'Browse All Supported AI Engines', 'url' => url('ai-models'), 'keywords' => 'all models engines midjourney gemini chatgpt'],
                    ],
                ],
                [
                    'name' => 'Midjourney Prompts',
                    'badge' => 'v6 & v6.1',
                    'icon' => 'bi-stars',
                    'url' => url('ai-model/midjourney'),
                    'links' => [
                        ['title' => 'Midjourney v6.1 Commercial Prompts', 'url' => url('search?q=midjourney+commercial'), 'keywords' => 'midjourney v6 v6.1 photorealistic product'],
                        ['title' => 'Photorealistic Product Photography', 'url' => url('search?q=photorealistic+product'), 'keywords' => 'photorealism product studio photography'],
                        ['title' => 'Studio Lighting & Softbox Formulas', 'url' => url('search?q=studio+lighting'), 'keywords' => 'softbox rim chiaroscuro strobe'],
                        ['title' => 'Texture & Material Close-Ups', 'url' => url('search?q=texture+detail'), 'keywords' => 'matte glossy glass leather metallic'],
                        ['title' => 'Cinematic Commercial Scenes', 'url' => url('search?q=cinematic+commercial'), 'keywords' => 'film 35mm anamorphic wide shot'],
                        ['title' => 'All Midjourney Prompts Archive', 'url' => url('ai-model/midjourney'), 'keywords' => 'midjourney gallery library explore'],
                    ],
                ],
                [
                    'name' => 'FLUX.1 Prompts',
                    'badge' => 'Pro & Dev',
                    'icon' => 'bi-lightning-charge',
                    'url' => url('search?q=flux'),
                    'links' => [
                        ['title' => 'Commercial D2C Product Photography', 'url' => url('search?q=flux+product'), 'keywords' => 'flux flux.1 pro dev commercial d2c'],
                        ['title' => 'Ultra-Detailed Texture & Detail Prompts', 'url' => url('search?q=flux+detail'), 'keywords' => 'flux ultra detailed 8k photography'],
                        ['title' => 'Cinematic Editorial Compositions', 'url' => url('search?q=flux+cinematic'), 'keywords' => 'flux dramatic lighting atmospheric'],
                        ['title' => 'Clean Typography & Packaging Visuals', 'url' => url('search?q=flux+packaging'), 'keywords' => 'flux packaging label typography mockup'],
                        ['title' => 'Photorealistic Gadgets & Tech Shots', 'url' => url('search?q=flux+gadget'), 'keywords' => 'flux electronics technology matte aluminum'],
                        ['title' => 'Explore All FLUX Creations', 'url' => url('search?q=flux'), 'keywords' => 'flux flux1 prompts explore archive'],
                    ],
                ],
            ];

            // 6. Themes, Settings & Lighting Styles (Comprehensive 6-card layout: 2 rows of 3 in dir-grid-3)
            $stylesAndThemes = [
                [
                    'title' => 'Lighting Styles & Setups',
                    'icon' => 'bi-brightness-high',
                    'links' => [
                        ['title' => 'Natural Light Photography', 'url' => url('search?q=natural+light+product+photography'), 'keywords' => 'natural daylight soft sun outdoor window'],
                        ['title' => 'Golden Hour Product Photography', 'url' => url('search?q=golden+hour+product+photography'), 'keywords' => 'golden hour warm sunset sunrise glow rays'],
                        ['title' => 'Dramatic Low-Key Lighting', 'url' => url('search?q=dramatic+low+key+lighting'), 'keywords' => 'low key dark moody shadows luxury black background'],
                        ['title' => 'High-Key Bright Photography', 'url' => url('search?q=high+key+bright+photography'), 'keywords' => 'high key clean white backdrop bright commercial'],
                        ['title' => 'Neon & Colored-Gel Lighting', 'url' => url('search?q=neon+colored+gel+lighting'), 'keywords' => 'neon color gel cyberpunk duotone magenta cyan'],
                        ['title' => 'Long-Exposure Light Trails', 'url' => url('search?q=long+exposure+light+trails'), 'keywords' => 'long exposure light streak light painting motion'],
                        ['title' => 'Backlit & Silhouette Shots', 'url' => url('search?q=backlit+silhouette+product'), 'keywords' => 'backlight rim light halo silhouette contour edge'],
                        ['title' => 'Sunlight Patterns & Window Shadows', 'url' => url('search?q=sunlight+patterns+window+shadows'), 'keywords' => 'gobo blind shadow dappled sunlight window frame'],
                        ['title' => 'Softbox Diffused Studio Lighting', 'url' => url('search?q=softbox+diffused+lighting'), 'keywords' => 'softbox soft light diffused shadows clean studio'],
                        ['title' => 'Chiaroscuro & High-Contrast Moody', 'url' => url('search?q=chiaroscuro+product+lighting'), 'keywords' => 'chiaroscuro dramatic dark contrast master'],
                        ['title' => 'Studio Multi-Strobe Setup', 'url' => url('search?q=studio+multi+strobe+setup'), 'keywords' => 'strobe key light fill light hair light 3-point'],
                        ['title' => 'Volumetric God Rays & Tyndall Effect', 'url' => url('search?q=volumetric+god+rays+product'), 'keywords' => 'volumetric rays crepuscular atmospheric mist dust'],
                    ],
                ],
                [
                    'title' => 'Backgrounds, Environments & Textures',
                    'icon' => 'bi-image',
                    'links' => [
                        ['title' => 'White Background E-commerce', 'url' => url('search?q=white+background+ecommerce'), 'keywords' => 'white backdrop pure white amazon shopify isolated'],
                        ['title' => 'Minimalist Studio Podiums', 'url' => url('search?q=minimalist+studio+podiums'), 'keywords' => 'podium pedestal cylinder marble display stand'],
                        ['title' => 'Nature-Inspired & Botanical Styling', 'url' => url('search?q=nature+inspired+botanical+styling'), 'keywords' => 'botanical leaves plants floral moss organic greenery'],
                        ['title' => 'Wood, Linen & Organic Textures', 'url' => url('search?q=wood+linen+organic+textures'), 'keywords' => 'wood grain raw linen jute burlap canvas earthy'],
                        ['title' => 'Water, Dew & Freshness Effects', 'url' => url('search?q=water+dew+freshness+effects'), 'keywords' => 'water droplets condensation dew drops shallow pool splash'],
                        ['title' => 'Pastel Dreamscape Photography', 'url' => url('search?q=pastel+dreamscape+photography'), 'keywords' => 'pastel pink lilac mint soft gradient dreamscape'],
                        ['title' => 'Cloud, Sky & Dream-Inspired Scenes', 'url' => url('search?q=cloud+sky+dream+inspired'), 'keywords' => 'fluffy clouds blue sky celestial ethereal dreamy'],
                        ['title' => 'Travertine & Raw Concrete Textures', 'url' => url('search?q=travertine+raw+concrete'), 'keywords' => 'concrete travertine stone plaster raw slate texture'],
                        ['title' => 'Luxury Penthouse & Architectural Settings', 'url' => url('search?q=luxury+penthouse+architecture'), 'keywords' => 'penthouse luxury interior modern modernism marble glass'],
                        ['title' => 'Nursery & Kids’ Room Styling', 'url' => url('search?q=nursery+kids+room+styling'), 'keywords' => 'nursery playroom baby crib pastel toys cozy interior'],
                        ['title' => 'Seamless Studio Cyclorama', 'url' => url('search?q=seamless+studio+cyclorama'), 'keywords' => 'cyclorama infinite cove infinity curve solid color'],
                        ['title' => 'Mirror & Liquid Ripple Reflections', 'url' => url('search?q=mirror+liquid+ripple+reflections'), 'keywords' => 'mirror reflection ripple puddle gloss reflective surface'],
                    ],
                ],
                [
                    'title' => 'Camera Angles, Framing & Perspectives',
                    'icon' => 'bi-camera',
                    'links' => [
                        ['title' => 'Top-Down Overhead Shots', 'url' => url('search?q=top+down+overhead+shots'), 'keywords' => 'top down bird eye overhead 90 degree vertical view'],
                        ['title' => 'Flat-Lay Compositions', 'url' => url('search?q=flat+lay+compositions'), 'keywords' => 'flat lay knolling neatly arranged grid composition'],
                        ['title' => '45-Degree Angled Product Shots', 'url' => url('search?q=45+degree+angled+product+shots'), 'keywords' => '45 degree angle commercial hero perspective depth'],
                        ['title' => 'Side-Profile Product Photography', 'url' => url('search?q=side+profile+product+photography'), 'keywords' => 'side profile silhouette side view contour packaging'],
                        ['title' => '360° & Multi-Angle Views', 'url' => url('search?q=360+multi+angle+views'), 'keywords' => '360 degree multiple angles turnaround rotation orthographic'],
                        ['title' => 'Macro Texture & Close-Ups', 'url' => url('search?q=macro+texture+close+ups'), 'keywords' => 'macro 100mm extreme detail stitch texture droplet weave'],
                        ['title' => 'Scale & Size Comparison Shots', 'url' => url('search?q=scale+size+comparison+shots'), 'keywords' => 'scale comparison size reference in hand coin card'],
                        ['title' => 'Material & Craftsmanship Details', 'url' => url('search?q=material+craftsmanship+details'), 'keywords' => 'craftsmanship leather stitching brushed gold precision finish'],
                        ['title' => 'Eye-Level Commercial Perspective', 'url' => url('search?q=eye+level+commercial+perspective'), 'keywords' => 'eye level straight on product shelf packshot display'],
                        ['title' => 'Isometric 3D Orthographic Perspective', 'url' => url('search?q=isometric+3d+product+photography'), 'keywords' => 'isometric orthographic 3d angle axonometric render'],
                        ['title' => 'Dutch Angle & Dynamic Action Tilt', 'url' => url('search?q=dutch+angle+dynamic+action'), 'keywords' => 'dutch angle tilted camera dynamic motion diagonal power'],
                        ['title' => 'Centered Hero Symmetry', 'url' => url('search?q=centered+hero+symmetry'), 'keywords' => 'centered symmetry focal point clean balanced billboard'],
                    ],
                ],
                [
                    'title' => 'Composition, Color & Art Direction',
                    'icon' => 'bi-grid-3x3',
                    'links' => [
                        ['title' => 'Negative Space Compositions', 'url' => url('search?q=negative+space+compositions'), 'keywords' => 'negative space minimalist breathing room copy space banner'],
                        ['title' => 'Geometric Composition Photography', 'url' => url('search?q=geometric+composition+photography'), 'keywords' => 'geometric shapes cubes prisms cylinders lines arches'],
                        ['title' => 'Symmetry & Repetition Shots', 'url' => url('search?q=symmetry+repetition+shots'), 'keywords' => 'symmetry repetitive pattern rhythm mirrored alignment'],
                        ['title' => 'Color Coordination & Palette Stories', 'url' => url('search?q=color+coordination+palette+stories'), 'keywords' => 'color palette harmony complementary tones chromatic swatches'],
                        ['title' => 'Monochromatic Product Photography', 'url' => url('search?q=monochromatic+product+photography'), 'keywords' => 'monochrome single tone uniform color tint shade'],
                        ['title' => 'Color-Blocked Studio Photography', 'url' => url('search?q=color+blocked+studio+photography'), 'keywords' => 'color blocked bold contrasting two tone split backdrop'],
                        ['title' => 'Silhouette & Shape Emphasis', 'url' => url('search?q=silhouette+shape+emphasis'), 'keywords' => 'silhouette outline shape graphic bold form shadow'],
                        ['title' => 'Reflection & Shadow Play', 'url' => url('search?q=reflection+shadow+play'), 'keywords' => 'reflection harsh shadow soft shadow optical art double shadow'],
                        ['title' => 'Premium Minimalist Compositions', 'url' => url('search?q=premium+minimalist+compositions'), 'keywords' => 'minimalist clean simplicity modern wabi sabi zen'],
                        ['title' => 'Premium Editorial Photography', 'url' => url('search?q=premium+editorial+photography'), 'keywords' => 'editorial vogue magazine spread high fashion lookbook'],
                        ['title' => 'Social Media Campaign Creatives', 'url' => url('search?q=social+media+campaign+creatives'), 'keywords' => 'instagram tiktok 9:16 viral creative ads hook social'],
                        ['title' => 'Knolling Precision Grid Arrangement', 'url' => url('search?q=knolling+grid+arrangement'), 'keywords' => 'knolling organized disassembled square angles technical flat lay'],
                    ],
                ],
                [
                    'title' => 'Motion, Action & Creative Dynamics',
                    'icon' => 'bi-lightning-charge',
                    'links' => [
                        ['title' => 'Floating & Levitation Shots', 'url' => url('search?q=floating+levitation+shots'), 'keywords' => 'floating suspended anti gravity levitating mid air dynamic'],
                        ['title' => 'Splash & Motion Photography', 'url' => url('search?q=splash+motion+photography'), 'keywords' => 'liquid splash milk splash water crown droplet high speed'],
                        ['title' => 'Freeze-Motion Action Shots', 'url' => url('search?q=freeze+motion+action+shots'), 'keywords' => 'freeze frame high shutter speed 1/8000s motion stop action'],
                        ['title' => 'Sequential Action Storytelling', 'url' => url('search?q=sequential+action+storytelling'), 'keywords' => 'sequence multi frame process step by step motion trail'],
                        ['title' => 'Product-in-Use Demonstrations', 'url' => url('search?q=product+in+use+demonstrations'), 'keywords' => 'product in use demo hands applied worn poured working'],
                        ['title' => 'Model & Hand Interaction Shots', 'url' => url('search?q=model+hand+interaction+shots'), 'keywords' => 'holding product hand holding model interaction human touch'],
                        ['title' => 'Creative Props & Set Design', 'url' => url('search?q=creative+props+set+design'), 'keywords' => 'props set dressing styling decorative elements creative theme'],
                        ['title' => 'Whimsical Fantasy Setups', 'url' => url('search?q=whimsical+fantasy+setups'), 'keywords' => 'fantasy magical surreal fairytale whimsical enchanted glow'],
                        ['title' => 'Miniature World & Diorama Scenes', 'url' => url('search?q=miniature+world+diorama+scenes'), 'keywords' => 'miniature diorama tilt shift tiny scale world micro setting'],
                        ['title' => 'Playful Storybook Setups', 'url' => url('search?q=playful+storybook+setups'), 'keywords' => 'storybook playful whimsical illustrated vibrant joyful narrative'],
                        ['title' => 'Seasonal & Holiday Campaigns', 'url' => url('search?q=seasonal+holiday+campaigns'), 'keywords' => 'holiday christmas festive new year halloween summer seasonal'],
                        ['title' => 'Liquid Crown & Droplet Splashes', 'url' => url('search?q=liquid+crown+droplet+splash'), 'keywords' => 'liquid crown ripple droplet impact collision high speed capture'],
                    ],
                ],
                [
                    'title' => 'Commercial Staging & Product Displays',
                    'icon' => 'bi-box-seam',
                    'links' => [
                        ['title' => 'Studio Hero Photoshoots', 'url' => url('search?q=studio+hero+photoshoots'), 'keywords' => 'studio hero flagship commercial key visual master render'],
                        ['title' => 'Lifestyle & In-Context', 'url' => url('search?q=lifestyle+in+context'), 'keywords' => 'lifestyle in context real world environment home setting cafe'],
                        ['title' => 'In-the-Box Product Layouts', 'url' => url('search?q=in+the+box+product+layouts'), 'keywords' => 'in the box inside box unboxed packaging accessories arranged'],
                        ['title' => 'Exploded-View Product Photography', 'url' => url('search?q=exploded+view+product+photography'), 'keywords' => 'exploded view components disassembled technical floating parts layers'],
                        ['title' => 'Product Assembly & How-It-Works Shots', 'url' => url('search?q=product+assembly+how+it+works'), 'keywords' => 'assembly how it works mechanism internal breakdown diagrammatic'],
                        ['title' => 'Feature-by-Feature Detail Series', 'url' => url('search?q=feature+by+feature+detail+series'), 'keywords' => 'features highlights close up buttons ports texture specs'],
                        ['title' => 'Packaging & Unboxing Photography', 'url' => url('search?q=packaging+unboxing+photography'), 'keywords' => 'unboxing box packaging unwrap cardboard mailer premium retail'],
                        ['title' => 'Transparent & Translucent Product Shots', 'url' => url('search?q=transparent+translucent+product'), 'keywords' => 'transparent glass acrylic clear bottle translucent refraction light pass'],
                        ['title' => 'Glass, Gloss & Surface Detail Shots', 'url' => url('search?q=glass+gloss+surface+detail'), 'keywords' => 'glossy glass reflective mirror surface specular highlight'],
                        ['title' => 'Gift Sets & Bundled Products', 'url' => url('search?q=gift+sets+bundled+products'), 'keywords' => 'gift box bundle kit collection combo package ribbon festive'],
                        ['title' => 'Infographic & Feature Highlight Shots', 'url' => url('search?q=infographic+feature+highlight'), 'keywords' => 'infographic callouts benefit highlight clean commercial pointers'],
                        ['title' => 'Before & After Comparisons', 'url' => url('search?q=before+after+comparisons'), 'keywords' => 'before after split screen transformation comparative contrast'],
                        ['title' => 'UGC-Style Authentic Photography', 'url' => url('search?q=ugc+style+authentic+photography'), 'keywords' => 'ugc user generated content real smartphone candid authentic natural'],
                        ['title' => 'Sibling & Family Lifestyle Scenes', 'url' => url('search?q=sibling+family+lifestyle+scenes'), 'keywords' => 'family siblings kids parents shared moments heartfelt warm'],
                        ['title' => 'Storytelling & Themed Scenes', 'url' => url('search?q=storytelling+themed+scenes'), 'keywords' => 'narrative story themed cinematic journey brand story mood'],
                        ['title' => 'Amazon Clean White Hero Standards', 'url' => url('search?q=amazon+white+hero+standard'), 'keywords' => 'amazon compliant pure white 255 background 85% frame natural shadow'],
                    ],
                ],
            ];

            // 7. Free Tools, Generators & Reference Resources
            $toolsAndResources = [
                [
                    'title' => 'Interactive AI Tools & Generators',
                    'icon' => 'bi-tools',
                    'links' => [
                        ['title' => 'AI Prompt Search & Generator Engine', 'url' => url('search'), 'keywords' => 'prompt generator builder search discover'],
                        ['title' => 'Free AI Image Prompts Library', 'url' => url('prompts/free'), 'keywords' => 'free prompts library commercial royalty free'],
                        ['title' => 'Premium Commercial Prompts Library', 'url' => url('prompts/premium'), 'keywords' => 'premium prompts d2c brands high conversion'],
                        ['title' => 'Curated Photoshoot Concept Packs', 'url' => url('photoshoots'), 'keywords' => 'photoshoots series multiple angles brand kit'],
                        ['title' => 'Supported AI Image Models Index', 'url' => url('ai-models'), 'keywords' => 'models midjourney gemini chatgpt flux sd'],
                    ],
                ],
                [
                    'title' => 'Curated Prompt Reference Cheat Sheets',
                    'icon' => 'bi-file-earmark-text',
                    'links' => [
                        ['title' => 'Lighting Terminology Reference Sheet', 'url' => $resolveBlogUrl(['lighting-terminology', 'lighting']), 'keywords' => 'lighting terms guide softbox rim key fill'],
                        ['title' => 'Negative Prompts & Anti-Artifact Formula', 'url' => $resolveBlogUrl(['negative-prompts', 'artifacts']), 'keywords' => 'negative prompt tokens avoid blur distortion'],
                        ['title' => 'Consistent Character & Model Guide', 'url' => $resolveBlogUrl(['consistent-character', 'model-photoshoots']), 'keywords' => 'consistent character face seed model photography'],
                        ['title' => 'High-Converting E-Commerce Visuals Guide', 'url' => $resolveBlogUrl(['converting', 'ecommerce']), 'keywords' => 'ecommerce conversion rate product hero clickthrough'],
                        ['title' => '50 Visual Assets in One Afternoon Playbook', 'url' => $resolveBlogUrl(['50-visual-assets', 'launch']), 'keywords' => 'batch workflow speed production catalog'],
                    ],
                ],
            ];

            // Helper resolvers for dynamic database linking (Zero hardcoded links)
            $resolveCategoryGoal = function (array $keywords, string $defaultTitle, string $desc, string $icon, string $customSlug = '') use ($categories) {
                $matched = null;
                foreach ($keywords as $kw) {
                    $cleanKw = preg_replace('/-prompts$/', '', $kw);
                    $matched = $categories->first(function ($c) use ($kw, $cleanKw) {
                        return $c->slug === $kw || $c->slug === $cleanKw || str_contains($c->slug, $cleanKw);
                    });
                    if ($matched) break;
                }
                $cat = $matched ?? $categories->first();
                $slug = $customSlug ?: ($cat ? $cat->slug : 'categories');
                return [
                    'type' => 'category',
                    'type_label' => 'Product Category',
                    'badge_icon' => 'bi-bag-check',
                    'icon' => $icon,
                    'title' => $defaultTitle,
                    'url' => url('category/' . $slug),
                    'desc' => $desc,
                    'action_text' => 'Explore Category',
                ];
            };

            $resolveGuideGoal = function (array $keywords, string $defaultTitle, string $desc, string $icon) use ($guides) {
                $matched = null;
                foreach ($keywords as $kw) {
                    $matched = $guides->first(function ($g) use ($kw) {
                        return $g->slug === $kw || str_contains($g->slug, $kw) || str_contains(strtolower($g->title), $kw);
                    });
                    if ($matched) break;
                }
                $guide = $matched ?? $guides->first();
                return [
                    'type' => 'guide',
                    'type_label' => 'Prompt Masterclass',
                    'badge_icon' => 'bi-book',
                    'icon' => $icon,
                    'title' => $defaultTitle,
                    'url' => $guide ? url('guides/' . $guide->slug) : url('categories'),
                    'desc' => $desc,
                    'action_text' => 'Read Masterclass',
                ];
            };

            $resolveBlogGoal = function (array $keywords, string $defaultTitle, string $desc, string $icon) use ($blogPosts) {
                $matched = null;
                foreach ($keywords as $kw) {
                    $matched = $blogPosts->first(function ($b) use ($kw) {
                        return $b->slug === $kw || str_contains($b->slug, $kw) || str_contains(strtolower($b->title), $kw);
                    });
                    if ($matched) break;
                }
                $blog = $matched ?? $blogPosts->first();
                return [
                    'type' => 'blog',
                    'type_label' => 'Blog Playbook',
                    'badge_icon' => 'bi-journal-text',
                    'icon' => $icon,
                    'title' => $defaultTitle,
                    'url' => $blog ? url('blog/' . $blog->slug) : url('blog'),
                    'desc' => $desc,
                    'action_text' => 'Read Playbook',
                ];
            };

            // 8. Browse by Creative Goal (Dynamic linking from Categories, Guides & Blog tables)
            $creativeGoals = [
                // Category Destinations (Dynamic from Categories Table with -prompts targeted slugs)
                $resolveCategoryGoal(
                    ['beauty-and-skincare', 'skincare', 'cosmetics'],
                    'Luxury D2C Beauty & Skincare Campaign',
                    'Water splashes, acrylic pedestals, soft diffused rim light, and frosted glass dropper bottles.',
                    'bi-droplet-half',
                    'beauty-and-skincare-prompts'
                ),
                $resolveCategoryGoal(
                    ['jewellery-and-accessories', 'jewellery', 'jewelry'],
                    'High-End Jewellery Macro Sparkle & Reflections',
                    'Faceted diamond fire, brushed 18k gold reflections on deep slate or rich velvet surfaces.',
                    'bi-gem',
                    'jewellery-and-accessories-prompts'
                ),
                $resolveCategoryGoal(
                    ['fashion-and-apparel', 'fashion', 'apparel'],
                    'Apparel & Fashion Ghost Mannequin / On-Model',
                    'Wrinkle-free garment shape, stitch-level macro texture, and editorial streetwear lookbooks.',
                    'bi-person-standing',
                    'fashion-and-apparel-prompts'
                ),
                $resolveCategoryGoal(
                    ['electronics-and-gadgets', 'electronics', 'gadgets'],
                    'Modern Minimalist Tech & Consumer Gadgets',
                    'Exploded perspective, matte tactile finishes, brushed metal, and futuristic clean studio scenes.',
                    'bi-phone',
                    'electronics-and-gadgets-prompts'
                ),
                $resolveCategoryGoal(
                    ['foods-and-drinks', 'food', 'drinks'],
                    'Gourmet Food & Pouring Beverage Motion',
                    'Condensation droplets, ice cube splash, steam wisps, and fresh farm ingredient styling.',
                    'bi-cup-hot',
                    'foods-and-drinks-prompts'
                ),
                $resolveCategoryGoal(
                    ['health-and-supplements', 'supplements', 'personal-care'],
                    'Health, Supplements & Organic Wellness',
                    'Clean botanical backdrop, amber glass bottles, certified organic labels, and morning sunlight.',
                    'bi-capsule'
                ),

                // Seasonal & Holiday Campaigns (Direct Search Query)
                [
                    'type' => 'search',
                    'type_label' => '',
                    'badge_icon' => 'bi-search',
                    'icon' => 'bi-stars',
                    'title' => 'Seasonal Holiday & Festive Ad Campaigns',
                    'url' => url('search?q=seasonal+holiday+festive+campaigns'),
                    'desc' => 'Festive lighting, bokeh ornaments, warm holiday atmosphere, and celebration themes.',
                    'action_text' => 'Explore Prompts',
                ],
                $resolveGuideGoal(
                    ['footwear', 'shoes', 'sneakers'],
                    'Commercial Action & Footwear Hero Launch',
                    'Dynamic floating angles, motion particles, sole tread details, and high-impact lifestyle lighting.',
                    'bi-tag'
                ),
                $resolveGuideGoal(
                    ['skincare', 'beauty', 'top-10'],
                    'Top 10 Skincare Commercial Formulations',
                    'Curated studio recipes, lighting prompt tags, and commercial camera angles for skincare bottles.',
                    'bi-droplet'
                ),

                // Blog Destinations (Dynamic from BlogPost Table)
                $resolveBlogGoal(
                    ['converting', 'product-images', 'ecommerce'],
                    'Amazon & Shopify High-Converting Hero Visuals',
                    'Pure white background (#FFFFFF), natural contact shadow, and conversion rate optimization formulas.',
                    'bi-graph-up-arrow'
                ),
                $resolveBlogGoal(
                    ['50-visual-assets', 'product-launch', 'playbook'],
                    'E-Commerce Product Launch: 50 Visual Assets',
                    'Batch production workflow to create complete catalog visual assets in a single afternoon.',
                    'bi-collection'
                ),
                $resolveBlogGoal(
                    ['consistent-character', 'model-photoshoots', 'consistent'],
                    'Consistent Model & Brand Photoshoots with AI',
                    'Face consistency seed methods, lighting replication, and multi-pose model styling across shots.',
                    'bi-people'
                ),
            ];

            // 9. Company, Membership & Legal Information
            $companyLinks = [
                'company' => [
                    ['title' => 'About ImagineBuddy', 'url' => url('page/about'), 'keywords' => 'about mission company team creators'],
                    ['title' => 'Contact Support', 'url' => url('contact'), 'keywords' => 'contact email support helpdesk reach out'],
                    ['title' => 'Frequently Asked Questions (FAQ)', 'url' => url('frequently-asked-questions'), 'keywords' => 'faq questions help answers subscriptions'],
                    ['title' => 'Help & Documentation', 'url' => url('page/help'), 'keywords' => 'help knowledge base docs guide'],
                ],
                'plans' => [
                    ['title' => 'Pricing & Subscription Plans', 'url' => url('pricing'), 'keywords' => 'pricing plans pro subscription monthly yearly cost'],
                    ['title' => 'Free AI Prompts Collection', 'url' => url('prompts/free'), 'keywords' => 'free commercial use no watermark'],
                    ['title' => 'Premium High-Resolution Prompts', 'url' => url('prompts/premium'), 'keywords' => 'premium download commercial licensing pro'],
                ],
                'legal' => [
                    ['title' => 'Terms of Service', 'url' => url('page/terms-of-service'), 'keywords' => 'terms service conditions agreement legal'],
                    ['title' => 'Privacy Policy', 'url' => url('page/privacy'), 'keywords' => 'privacy policy gdpr cookies data security'],
                    ['title' => 'Commercial Licensing & Usage Rights', 'url' => url('page/terms-of-service'), 'keywords' => 'license commercial rights royalty free copyright'],
                ],
            ];

            return compact(
                'categories',
                'guides',
                'blogCategories',
                'blogPosts',
                'photoshoots',
                'engines',
                'stylesAndThemes',
                'toolsAndResources',
                'creativeGoals',
                'companyLinks',
                'categoryIconMap',
                'categoryQueriesMap'
            );
        });

        return view('default.directory', $data);
    }
}
