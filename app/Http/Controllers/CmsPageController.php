<?php

namespace App\Http\Controllers;

use App\Models\SeoLandingPage;
use App\Models\Categories;
use App\Models\Testimonial;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    /**
     * Supported vertical prefixes and their metadata configuration.
     */
    protected array $verticalConfigs = [
        'use-case' => [
            'name' => 'Use Case',
            'plural' => 'Use Cases',
            'badge' => 'ALL OUR SERVICES AT YOUR FINGERTIPS',
            'title' => 'Transform Your <span class="cms-heading-gradient-mint">Product Imagery</span>',
            'subtitle' => 'Discover how Imagine Buddy\'s AI-powered tools format, optimize, and customize your product photos for e-commerce platforms, social media, and beyond.',
            'meta_title' => 'AI Product Photography Use Cases - ImagineBuddy',
            'meta_description' => 'Explore AI product photography use cases for D2C brands, bulk e-commerce cataloging, boutique shoots, background removal, and campaign visuals.',
        ],
        'industry' => [
            'name' => 'Industry',
            'plural' => 'Industries',
            'badge' => 'SOLUTIONS FOR EVERY SECTOR',
            'title' => 'Industry-Specific <span class="cms-heading-gradient-mint">Photography Solutions</span>',
            'subtitle' => 'Tailored AI product photography workflows built to conquer the unique challenges of fashion, jewelry, cosmetics, electronics, and home decor.',
            'meta_title' => 'Industry-Specific AI Photography Solutions - ImagineBuddy',
            'meta_description' => 'Professional AI photography tailored for e-commerce fashion, luxury jewelry, beauty products, electronics, and home decor brands.',
        ],
        'product' => [
            'name' => 'Product Content',
            'plural' => 'Product Deliverables',
            'badge' => 'FORMAT-OPTIMIZED ASSETS',
            'title' => 'Product Photos for <span class="cms-heading-gradient-mint">Every Purpose</span>',
            'subtitle' => 'Whether you need carousel ads, social media posts, website hero imagery, or high-converting static creatives, we\'ve got you covered.',
            'meta_title' => 'Product Photos & Ad Deliverables - ImagineBuddy',
            'meta_description' => 'Generate carousel ad images, social media packs, static Facebook creatives, and website product photos instantly with AI.',
        ],
        'garment' => [
            'name' => 'Garment',
            'plural' => 'Garments & Apparel',
            'badge' => 'FABRIC & DRAPE FIDELITY',
            'title' => 'On-Model Apparel <span class="cms-heading-gradient-mint">Photography</span>',
            'subtitle' => 'Turn flat lays and ghost mannequins into studio-quality on-model fashion shoots. Perfect fabric texture, realistic drapes, and ethnic diversity.',
            'meta_title' => 'AI Apparel & Garment Photography - ImagineBuddy',
            'meta_description' => 'AI fashion photography for sarees, kurtas, dresses, western wear, and streetwear. True-to-fabric textures and diverse virtual models.',
        ],
        'marketplace' => [
            'name' => 'Marketplace',
            'plural' => 'Marketplace Listings',
            'badge' => 'COMPLIANCE GUARANTEED',
            'title' => 'Marketplace-Ready <span class="cms-heading-gradient-mint">Product Photos</span>',
            'subtitle' => 'Automate strict marketplace compliance: pure white RGB 255 backgrounds, 85%+ frame fill, high-res zoom, and zero catalog suppressions.',
            'meta_title' => 'Marketplace Compliance Product Photography - ImagineBuddy',
            'meta_description' => 'Guaranteed compliance imagery for Amazon, Ajio, Blinkit, Etsy, Myntra, and Meta Shop. Avoid suppressions with instant AI generation.',
        ],
        'platform-ai-ads' => [
            'name' => 'Platform Ad Creative',
            'plural' => 'Ad Creatives',
            'badge' => 'ROAS & CTR OPTIMIZATION',
            'title' => 'High-Converting <span class="cms-heading-gradient-mint">Platform Ad Creatives</span>',
            'subtitle' => 'Generate scroll-stopping ad creatives formatted for Facebook, Instagram Reels, Google Display, TikTok, Pinterest, and YouTube.',
            'meta_title' => 'Platform AI Ad Creatives & Video Formats - ImagineBuddy',
            'meta_description' => 'High-converting AI ad visuals and story/feed creatives for Meta Ads, Google Display, TikTok, and Pinterest marketing campaigns.',
        ],
    ];

    /**
     * Display a Vertical Listing Hub Page (e.g., /use-case, /garment, /marketplace).
     */
    public function showHub(string $prefix)
    {
        if (!array_key_exists($prefix, $this->verticalConfigs)) {
            abort(404);
        }

        $config = $this->verticalConfigs[$prefix];

        // Check if there is an explicit admin-managed Hub record
        $hubRecord = SeoLandingPage::where('page_type', 'hub')
            ->where('route_prefix', $prefix)
            ->first();

        // Query child solution pages under this prefix
        $items = SeoLandingPage::published()
            ->where(function ($q) {
                $q->where('page_type', 'solution')
                  ->orWhereNull('page_type');
            })
            ->where('route_prefix', $prefix)
            ->orderBy('id', 'desc')
            ->paginate(18);

        // Build horizontal navigation filter pills across all 6 verticals
        $filterPills = [];
        foreach ($this->verticalConfigs as $key => $vConfig) {
            $filterPills[] = [
                'key' => $key,
                'label' => $vConfig['plural'],
                'url' => url($key),
                'active' => ($key === $prefix),
            ];
        }

        $title = $hubRecord?->title ?: $config['title'];
        $subtitle = $hubRecord?->excerpt ?: $config['subtitle'];
        $badge = $hubRecord?->header_badge ?: $config['badge'];
        $metaTitle = $hubRecord?->meta_title ?: $config['meta_title'];
        $metaDescription = $hubRecord?->meta_description ?: $config['meta_description'];

        // Inject SEO into the application service
        if (class_exists(Helper::class) && method_exists(Helper::class, 'seo')) {
            $seo = Helper::seo();
            $seo->setPage('cms_hub_' . $prefix);
        }

        return view('cms.hub', [
            'prefix' => $prefix,
            'verticalName' => $config['name'],
            'title' => $title,
            'subtitle' => $subtitle,
            'badge' => $badge,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'filterPills' => $filterPills,
            'items' => $items,
            'gridHeading' => 'All ' . $config['plural'],
            'gridSubheading' => 'Browse specialized ' . strtolower($config['name']) . ' workflows and production-ready photography guides.',
        ]);
    }

    /**
     * Display a Programmatic Solution Detail Page (e.g., /garment/saree-product-photography).
     */
    public function showDetail(string $prefix, string $slug)
    {
        if (!array_key_exists($prefix, $this->verticalConfigs)) {
            abort(404);
        }

        $page = SeoLandingPage::published()
            ->where('route_prefix', $prefix)
            ->where('slug', $slug)
            ->with([
                'activeSections' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
                'activeItems.prompt.stock',
                'activeItems.prompt.author',
                'activeItems.prompt.examples',
                'activeItems.prompt.category',
                'activeItems.photoshoot'
            ])
            ->firstOrFail();

        // Increment view count quietly
        $page->increment('views_count');

        // Extract attached prompt library items with custom badges/titles
        $attachedPrompts = $page->activeItems
            ->where('item_type', 'prompt')
            ->map(function ($item) {
                if ($item->prompt) {
                    if (!empty($item->custom_badge)) {
                        $item->prompt->custom_badge = $item->custom_badge;
                    }
                    if (!empty($item->custom_title)) {
                        $item->prompt->custom_title = $item->custom_title;
                    }
                    return $item->prompt;
                }
                return null;
            })
            ->filter()
            ->values();

        // Fetch up to 3 sibling solution pages within the same vertical for cross-linking silo
        $relatedPages = SeoLandingPage::published()
            ->where('route_prefix', $prefix)
            ->where('id', '!=', $page->id)
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        // Fetch active client testimonials for social proof
        $testimonials = Testimonial::active()->ordered()->get();

        return view('cms.detail', compact('page', 'relatedPages', 'prefix', 'attachedPrompts', 'testimonials'));
    }

    /**
     * Preview a draft landing page via secure preview token.
     */
    public function preview(string $prefix, string $token)
    {
        $page = SeoLandingPage::where('route_prefix', $prefix)
            ->where('preview_token', $token)
            ->with([
                'activeSections' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
                'activeItems.prompt.stock',
                'activeItems.prompt.author',
                'activeItems.prompt.examples',
                'activeItems.prompt.category',
                'activeItems.photoshoot'
            ])
            ->firstOrFail();

        // Extract attached prompt library items with custom badges/titles
        $attachedPrompts = $page->activeItems
            ->where('item_type', 'prompt')
            ->map(function ($item) {
                if ($item->prompt) {
                    if (!empty($item->custom_badge)) {
                        $item->prompt->custom_badge = $item->custom_badge;
                    }
                    if (!empty($item->custom_title)) {
                        $item->prompt->custom_title = $item->custom_title;
                    }
                    return $item->prompt;
                }
                return null;
            })
            ->filter()
            ->values();

        $relatedPages = SeoLandingPage::published()
            ->where('route_prefix', $prefix)
            ->where('id', '!=', $page->id)
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        // Fetch active client testimonials for social proof
        $testimonials = Testimonial::active()->ordered()->get();

        return view('cms.detail', compact('page', 'relatedPages', 'prefix', 'attachedPrompts', 'testimonials'));
    }
}
