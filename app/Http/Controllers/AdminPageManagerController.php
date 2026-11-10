<?php

namespace App\Http\Controllers;

use App\Models\SeoLandingPage;
use App\Models\SeoLandingPageSection;
use App\Models\SeoLandingPageItem;
use App\Models\Categories;
use App\Models\Images;
use App\Models\Photoshoot;
use App\Models\User;
use App\Models\UrlRedirect;
use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Purify;

class AdminPageManagerController extends Controller
{
    /**
     * Check permission helper.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (!$user->hasPermission('page_manager') && !$user->hasPermission('seo_pages') && !$user->hasPermission('full_access')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a master listing of all managed pages.
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $query = SeoLandingPage::with(['category', 'user'])
            ->withCount(['sections', 'items'])
            ->whereNotNull('route_prefix')
            ->where('route_prefix', '!=', '');

        // Filter by vertical prefix
        if ($request->filled('vertical') && $request->vertical !== 'all') {
            $query->where('route_prefix', $request->vertical);
        }

        // Filter by page type
        if ($request->filled('type')) {
            $query->where('page_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by keyword
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'LIKE', "%{$q}%")
                    ->orWhere('slug', 'LIKE', "%{$q}%")
                    ->orWhere('primary_keyword', 'LIKE', "%{$q}%")
                    ->orWhere('internal_name', 'LIKE', "%{$q}%");
            });
        }

        $pages = $query->orderBy('id', 'desc')->paginate(20);

        // Counts per vertical tab (strictly for the 6 vertical CMS silos)
        $counts = [
            'all' => SeoLandingPage::whereNotNull('route_prefix')->where('route_prefix', '!=', '')->count(),
            'use-case' => SeoLandingPage::where('route_prefix', 'use-case')->count(),
            'industry' => SeoLandingPage::where('route_prefix', 'industry')->count(),
            'product' => SeoLandingPage::where('route_prefix', 'product')->count(),
            'garment' => SeoLandingPage::where('route_prefix', 'garment')->count(),
            'marketplace' => SeoLandingPage::where('route_prefix', 'marketplace')->count(),
            'platform-ai-ads' => SeoLandingPage::where('route_prefix', 'platform-ai-ads')->count(),
        ];

        return view('admin.page-manager.index', compact('pages', 'counts'));
    }

    /**
     * Show form to create a new page.
     */
    public function create()
    {
        $this->checkPermission();

        $categories = Categories::where('mode', 'on')->orderBy('name')->get();
        $authors = $this->getSuperAdminAuthors();

        return view('admin.page-manager.create', compact('categories', 'authors'));
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'title' => 'required|string|max:255',
            'internal_name' => 'nullable|string|max:255',
            'route_prefix' => 'required|string|in:use-case,industry,product,garment,marketplace,platform-ai-ads',
            'page_type' => 'required|string|in:solution,hub',
            'slug' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'header_badge' => 'nullable|string|max:100',
            'header_cta_text' => 'nullable|string|max:100',
            'header_cta_url' => 'nullable|string|max:255',
            'header_cta_subtext' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'primary_keyword' => 'nullable|string|max:150',
            'secondary_keywords' => 'nullable|string|max:1000',
            'search_intent' => 'required|in:informational,commercial,transactional',
            'status' => 'required|in:published,draft,scheduled,archived',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'before_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'after_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'before_label' => 'nullable|string|max:100',
            'after_label' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'schema_type' => 'nullable|string|max:50',
        ]);

        $slug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $prefix = $request->route_prefix ?: null;

        // Ensure slug uniqueness within this route_prefix
        $originalSlug = $slug;
        $counter = 1;
        while (SeoLandingPage::where('route_prefix', $prefix)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $page = new SeoLandingPage();
        $page->user_id = $request->user_id;
        $page->category_id = $request->category_id ?: null;
        $page->page_type = $request->page_type;
        $page->route_prefix = $prefix;
        $page->icon = $request->icon ?: null;
        $page->header_badge = $request->header_badge ?: null;
        $page->header_cta_text = $request->header_cta_text ?: 'Get Started Free';
        $page->header_cta_url = $request->header_cta_url ?: url('register');
        $page->header_cta_subtext = $request->header_cta_subtext ?: 'No credit card required • Instant export';
        $page->internal_name = $request->internal_name ?: $request->title;
        $page->title = $request->title;
        $page->slug = $slug;
        $page->preview_token = Str::random(32);
        $page->excerpt = $request->excerpt;
        $page->target_audience = $request->target_audience;
        $page->primary_keyword = $request->primary_keyword;
        $page->secondary_keywords = $request->secondary_keywords;
        $page->search_intent = $request->search_intent;
        $page->status = $request->status;
        $page->published_at = ($request->status === 'published' && empty($request->published_at)) ? now() : $request->published_at;
        $page->meta_title = $request->meta_title ?: $request->title . ' - ImagineBuddy';
        $page->meta_description = $request->meta_description ?: ($request->excerpt ?: Str::limit($request->title, 150));
        $page->meta_keywords = $request->meta_keywords ?: $request->primary_keyword;
        $page->canonical_url = $request->canonical_url;
        $page->robots = $request->robots ?: 'index, follow';
        $page->schema_type = $request->schema_type ?: ($request->page_type === 'solution' ? 'Service' : 'WebPage');
        $page->before_label = $request->before_label ?: 'Raw Snapshot';
        $page->after_label = $request->after_label ?: 'Campaign Ready';

        $destPath = public_path('uploads/guides');
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        // Handle before_image upload
        if ($request->hasFile('before_image')) {
            $image = $request->file('before_image');
            $filename = 'before_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $image->move($destPath, $filename);
            $page->before_image = $filename;
        }

        // Handle after_image / featured_image upload
        $afterFile = $request->file('after_image') ?: $request->file('featured_image');
        if ($afterFile) {
            $filename = 'page_' . time() . '_' . Str::random(8) . '.' . $afterFile->getClientOriginalExtension();
            $afterFile->move($destPath, $filename);
            $page->after_image = $filename;
            $page->featured_image = $filename;
            $page->og_image = $filename;
        }

        $page->save();

        // Seed default starter sections for solution pages
        if ($page->page_type === 'solution') {
            $this->seedStarterSections($page);
        }

        return redirect('panel/admin/page-manager/edit/' . $page->id)
            ->with('success_message', 'Page created successfully! Configure sections and assets below.');
    }

    /**
     * Seed starter section templates to make authoring rapid and consistent.
     */
    protected function seedStarterSections(SeoLandingPage $page)
    {
        // 1. Hero
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'hero',
            'badge' => $page->header_badge ?: 'AI PRODUCT PHOTOGRAPHY',
            'heading' => $page->title,
            'subheading' => $page->excerpt ?: 'Generate studio-grade product photos and compliance-ready visuals in minutes.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 2. Why Choose / Benefit Grid
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'problem_solution',
            'badge' => 'CORE ADVANTAGES',
            'heading' => 'Why Brands Choose Imagine Buddy',
            'subheading' => 'Ditch expensive studio scheduling, shipping delays, and model coordination.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 3. Built for Every E-commerce Role
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'role_grid',
            'badge' => 'BUILT FOR EVERY E-COMMERCE ROLE',
            'heading' => 'Built for Every E-commerce Role',
            'subheading' => 'From boutique founders to enterprise catalog teams, Imagine Buddy adapts to your commercial workflow.',
            'settings' => [
                'cards' => [
                    [
                        'tag' => 'ORGANIZATION',
                        'num' => '01',
                        'title' => 'D2C Founders & Solo Entrepreneurs',
                        'description' => 'You wear every hat. Stop outsourcing creative work. Generate professional product images in minutes, not days.',
                        'bullets' => [
                            'Launch new products without hiring a photographer',
                            'Create lifestyle variants for Instagram and TikTok',
                            'Test seasonal backgrounds before committing to photoshoots'
                        ]
                    ],
                    [
                        'tag' => 'CATALOG OPS',
                        'num' => '02',
                        'title' => 'E-Commerce & Merchandising Managers',
                        'description' => 'Eliminate catalog bottlenecks. Standardize lighting, margins, and backgrounds across thousands of SKUs automatically.',
                        'bullets' => [
                            'Batch process multi-angle product catalogs at scale',
                            'Maintain consistent RGB 255 pure white backgrounds',
                            'Auto-crop to 1:1, 4:5, and 9:16 aspect ratios'
                        ]
                    ],
                    [
                        'tag' => 'GROWTH & MEDIA',
                        'num' => '03',
                        'title' => 'Performance Marketers & Media Buyers',
                        'description' => 'Combat creative ad fatigue. Generate endless variations of visual hooks and backdrops to boost CTR and lower CAC.',
                        'bullets' => [
                            'A/B test product backdrops, props, and lighting styles',
                            'Generate channel-optimized creative for Meta, Google & TikTok',
                            'Produce seasonal campaign refreshes without reshoots'
                        ]
                    ],
                    [
                        'tag' => 'MARKETPLACES',
                        'num' => '04',
                        'title' => 'Marketplace Sellers (Amazon, Blinkit, Ajio)',
                        'description' => 'Pass platform listing checks on the first try. Zero catalog suppressions with automated compliance verification.',
                        'bullets' => [
                            'Strict Amazon RGB 255 and 85%+ frame fill compliance',
                            'Multi-packshot sets ready for quick commerce PIM upload',
                            'High-resolution zoom details without compression artifacts'
                        ]
                    ],
                    [
                        'tag' => 'AGENCIES & STUDIOS',
                        'num' => '05',
                        'title' => 'Creative Agencies & Content Studios',
                        'description' => 'Deliver commercial client campaigns 10x faster. Scale your output without expanding studio overhead or headcount.',
                        'bullets' => [
                            'Pitch and render concepts to clients in real time',
                            'Full commercial licensing rights included on every asset',
                            'Create on-model fashion campaigns with virtual talent'
                        ]
                    ],
                    [
                        'tag' => 'POST-PRODUCTION',
                        'num' => '06',
                        'title' => 'Photographers & Digital Retouchers',
                        'description' => 'Automate the tedious manual work. Cut clipping paths, background cleanup, and shadow creation from hours to seconds.',
                        'bullets' => [
                            'Sub-pixel edge detection for jewelry, lace & flyaway hair',
                            'Preserve true fabric textures, reflections & logos',
                            'Generate realistic ambient occlusion and drop shadows'
                        ]
                    ],
                ]
            ],
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 4. Alternating Feature Rows (Core Features)
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'alternating_rows',
            'badge' => 'CORE FEATURES',
            'heading' => 'Enterprise Production Capabilities',
            'subheading' => 'A structured AI photography pipeline engineered for visual fidelity, scale, and compliance.',
            'settings' => [
                'cards' => [
                    [
                        'badge' => 'CATALOG AUTOMATION',
                        'title' => 'Batch Process Your Entire Catalog',
                        'description' => 'Transform hundreds of raw product snapshots in a single automated batch. Maintain consistent studio lighting, perfect color calibration, and standardized framing across your entire product line.',
                        'bullets' => [
                            'Process 500+ SKUs in minutes with batch queueing',
                            'Consistent lighting, color preservation & contrast across collections',
                            'Download ready-to-publish high-res assets for your store & PIM'
                        ],
                        'cta_text' => 'Try Batch Processing',
                        'cta_url' => url('register'),
                        'image' => asset('public/img/premium-white-oud-perfume-ad-02.webp')
                    ],
                    [
                        'badge' => 'ON-MODEL LIFESTYLE',
                        'title' => 'Generate On-Model & Lifestyle Variations',
                        'description' => 'Turn flat-lays and ghost mannequin photos into high-end editorial on-model campaigns without booking a single model, makeup artist, or studio space.',
                        'bullets' => [
                            'Diverse virtual models across ethnicities, genders & aesthetics',
                            'Natural fabric drape, real gravity & micro-texture preservation',
                            '50+ curated studio lighting sets & luxury lifestyle backdrops'
                        ],
                        'cta_text' => 'Explore Lifestyle Backdrops',
                        'cta_url' => url('photoshoots'),
                        'image' => asset('public/uploads/preview/bridal-kundan-chandelier-earrings-on-model-lifestyle-prompt-11793629573zfgomjgeuo.jpg')
                    ],
                    [
                        'badge' => 'MICRO PRECISION',
                        'title' => 'Pixel-Perfect Edge & Texture Detail Retention',
                        'description' => 'Our advanced AI vision pipeline preserves intricate jewelry chains, gemstone refraction, transparent cosmetics glass, and delicate embroidery without artificial cutout edges.',
                        'bullets' => [
                            'Sub-pixel precision edge matting & transparent glass handling',
                            'Zero distortion of brand logos, packaging typography & barcodes',
                            'Authentic contact shadows & ambient occlusion photon bounces'
                        ],
                        'cta_text' => 'Inspect Image Quality',
                        'cta_url' => url('photoshoots'),
                        'image' => asset('public/uploads/preview/beauty-model-holding-cleanser-with-gel-swatch-11787919776igzb3wvkvc.jpeg')
                    ],
                    [
                        'badge' => 'MULTI-CHANNEL COMPLIANCE',
                        'title' => 'Ready for Any Marketplace Any Platform',
                        'description' => 'Automate strict marketplace compliance: Amazon pure white RGB 255 backgrounds, 85%+ frame fill, Instagram 9:16 reels, Google Shopping, and Shopify aspect ratios in one click.',
                        'bullets' => [
                            'Guaranteed Amazon, Blinkit, Ajio, Myntra & Etsy compliance',
                            'Automatic export in 1:1, 4:5, 9:16, and 16:9 aspect ratios',
                            'Lossless compression under platform file limits with zero artifacts'
                        ],
                        'cta_text' => 'View Compliance Presets',
                        'cta_url' => url('register'),
                        'image' => asset('public/uploads/preview/conditioner-bottle-on-miniature-beach-lounge-chair-11790755759vhnejf2v04.jpeg')
                    ],
                ]
            ],
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // 5. Results That Matter (Stats)
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'metrics_counter',
            'badge' => 'PROVEN IMPACT',
            'heading' => 'Results That Speak for Themselves',
            'subheading' => 'Measurable cost savings, rapid turnaround, and higher ad performance.',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        // 6. Testimonials Carousel
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'testimonials',
            'badge' => 'COMMUNITY TRUST',
            'heading' => 'What E-commerce Brands Are Saying',
            'subheading' => 'Discover how creators and e-commerce leaders power their visual projects with Imagine Buddy.',
            'sort_order' => 6,
            'is_active' => true,
        ]);

        // 7. FAQ Accordion
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'faq_accordion',
            'badge' => 'FAQ',
            'heading' => 'Frequently Asked Questions',
            'subheading' => 'Everything you need to know about our AI photography workflow.',
            'settings' => [
                'faqs' => [
                    [
                        'question' => 'How does Imagine Buddy compare to traditional physical photoshoots?',
                        'answer' => '<p>Traditional photoshoots require studio rentals, sample shipping, model booking, and weeks of post-production turnaround. Imagine Buddy generates studio-quality on-model and packshot photography from a single product photo in minutes, saving up to 80% of production costs.</p>'
                    ],
                    [
                        'question' => 'Are the generated images compliant with marketplace guidelines?',
                        'answer' => '<p>Yes. All marketplace templates adhere strictly to platform specifications, including Amazon RGB 255 pure white backgrounds, 85%+ product frame fill ratios, and high-resolution zoom standards.</p>'
                    ],
                    [
                        'question' => 'Do I own commercial rights to the generated images?',
                        'answer' => '<p>Yes, all generated imagery created under an active plan includes full commercial licensing rights for online stores, print media, social media ads, and global marketing campaigns.</p>'
                    ],
                ]
            ],
            'sort_order' => 7,
            'is_active' => true,
        ]);

        // 8. Bottom CTA Banner
        SeoLandingPageSection::create([
            'landing_page_id' => $page->id,
            'type' => 'cta_banner',
            'badge' => 'GET STARTED TODAY',
            'heading' => 'Ready to Transform Your Product Photography?',
            'subheading' => 'Start generating professional visuals for your catalog and ad campaigns today.',
            'sort_order' => 8,
            'is_active' => true,
        ]);
    }

    /**
     * Show edit form with 4-tab manager.
     */
    public function edit($id)
    {
        $this->checkPermission();

        $page = SeoLandingPage::with([
            'sections' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            },
            'items.prompt.stock',
            'items.photoshoot'
        ])->findOrFail($id);

        $categories = Categories::where('mode', 'on')->orderBy('name')->get();
        $authors = $this->getSuperAdminAuthors();

        return view('admin.page-manager.edit', compact('page', 'categories', 'authors'));
    }

    /**
     * Update page details with automated 301 redirect protection and granular field preservation.
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission();

        $page = SeoLandingPage::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'internal_name' => 'nullable|string|max:255',
            'route_prefix' => 'sometimes|required|string|in:use-case,industry,product,garment,marketplace,platform-ai-ads',
            'page_type' => 'sometimes|required|string|in:solution,hub',
            'slug' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'header_badge' => 'nullable|string|max:100',
            'header_cta_text' => 'nullable|string|max:100',
            'header_cta_url' => 'nullable|string|max:255',
            'header_cta_subtext' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
            'target_audience' => 'nullable|string|max:255',
            'primary_keyword' => 'nullable|string|max:150',
            'secondary_keywords' => 'nullable|string|max:1000',
            'search_intent' => 'sometimes|required|in:informational,commercial,transactional',
            'status' => 'sometimes|required|in:published,draft,scheduled,archived',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'before_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'after_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'before_label' => 'nullable|string|max:100',
            'after_label' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:50',
            'schema_type' => 'nullable|string|max:50',
        ]);

        // 1. Slug & Route Prefix (with automated 301 redirect protection)
        if ($request->exists('slug') || $request->exists('route_prefix')) {
            $newSlug = $request->exists('slug') ? Str::slug($request->slug) : $page->slug;
            $oldPrefix = $page->route_prefix;
            $newPrefix = $request->exists('route_prefix') ? ($request->route_prefix ?: null) : $oldPrefix;

            // Check if slug changed on a published page -> register automatic 301 redirect!
            if ($page->status === 'published' && ($page->slug !== $newSlug || $oldPrefix !== $newPrefix)) {
                $oldUrl = ($oldPrefix ? $oldPrefix . '/' : 'guides/') . $page->slug;
                $newUrl = ($newPrefix ? $newPrefix . '/' : 'guides/') . $newSlug;

                if (class_exists(UrlRedirect::class) && $oldUrl !== $newUrl) {
                    UrlRedirect::updateOrCreate(
                        ['source_url' => '/' . ltrim($oldUrl, '/')],
                        [
                            'destination_url' => '/' . ltrim($newUrl, '/'),
                            'redirect_type' => 301,
                            'status' => true,
                            'notes' => 'Auto-generated by Page Manager upon slug update.'
                        ]
                    );
                }
            }

            if ($request->exists('slug')) {
                $page->slug = $newSlug;
            }
            if ($request->exists('route_prefix')) {
                $page->route_prefix = $newPrefix;
            }
        }

        // 2. Core Identity & Author
        if ($request->exists('title')) {
            $page->title = $request->title;
            if ($request->exists('internal_name')) {
                $page->internal_name = $request->internal_name ?: $request->title;
            } elseif (empty($page->internal_name)) {
                $page->internal_name = $request->title;
            }
        } elseif ($request->exists('internal_name')) {
            $page->internal_name = $request->internal_name;
        }

        if ($request->exists('user_id')) {
            $page->user_id = $request->user_id;
        }
        if ($request->exists('category_id')) {
            $page->category_id = $request->category_id ?: null;
        }
        if ($request->exists('page_type')) {
            $page->page_type = $request->page_type;
        }

        // 3. Header & Badges
        if ($request->exists('icon')) {
            $page->icon = $request->icon ?: null;
        }
        if ($request->exists('header_badge')) {
            $page->header_badge = $request->header_badge ?: null;
        }
        if ($request->exists('header_cta_text')) {
            $page->header_cta_text = $request->header_cta_text ?: 'Get Started Free';
        }
        if ($request->exists('header_cta_url')) {
            $page->header_cta_url = $request->header_cta_url ?: url('register');
        }
        if ($request->exists('header_cta_subtext')) {
            $page->header_cta_subtext = $request->header_cta_subtext ?: 'No credit card required';
        }

        // 4. Content & Audience
        if ($request->exists('excerpt')) {
            $page->excerpt = $request->excerpt;
        }
        if ($request->exists('target_audience')) {
            $page->target_audience = $request->target_audience;
        }

        // 5. Publishing Status & Search Intent
        if ($request->exists('search_intent')) {
            $page->search_intent = $request->search_intent;
        }
        if ($request->exists('status')) {
            $page->status = $request->status;
            if ($request->exists('published_at')) {
                $page->published_at = ($request->status === 'published' && empty($request->published_at)) ? ($page->published_at ?: now()) : $request->published_at;
            } elseif ($request->status === 'published' && empty($page->published_at)) {
                $page->published_at = now();
            }
        } elseif ($request->exists('published_at')) {
            $page->published_at = $request->published_at;
        }

        // 6. Before / After Labels
        if ($request->exists('before_label')) {
            $page->before_label = $request->before_label ?: 'Raw Snapshot';
        }
        if ($request->exists('after_label')) {
            $page->after_label = $request->after_label ?: 'Campaign Ready';
        }

        // 7. SEO & Social Meta
        if ($request->exists('meta_title')) {
            $page->meta_title = $request->meta_title;
        }
        if ($request->exists('meta_description')) {
            $page->meta_description = $request->meta_description;
        }
        if ($request->exists('meta_keywords')) {
            $page->meta_keywords = $request->meta_keywords;
        }
        if ($request->exists('primary_keyword')) {
            $page->primary_keyword = $request->primary_keyword;
        }
        if ($request->exists('secondary_keywords')) {
            $page->secondary_keywords = $request->secondary_keywords;
        }
        if ($request->exists('canonical_url')) {
            $page->canonical_url = $request->canonical_url;
        }
        if ($request->exists('robots')) {
            $page->robots = $request->robots ?: 'index, follow';
        }
        if ($request->exists('schema_type')) {
            $page->schema_type = $request->schema_type;
        }

        // 8. Media Handling
        $destPath = public_path('uploads/guides');
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        // Handle before_image removal or upload
        if ($request->boolean('remove_before_image')) {
            if (!empty($page->before_image)) {
                $oldPath = public_path('uploads/guides/' . $page->before_image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $page->before_image = null;
        } elseif ($request->hasFile('before_image')) {
            if (!empty($page->before_image)) {
                $oldPath = public_path('uploads/guides/' . $page->before_image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $image = $request->file('before_image');
            $filename = 'before_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $image->move($destPath, $filename);
            $page->before_image = $filename;
        }

        // Handle after_image / featured_image removal or upload
        if ($request->boolean('remove_after_image') || $request->boolean('remove_featured_image')) {
            $existing = $page->after_image ?: $page->featured_image;
            if (!empty($existing)) {
                $oldPath = public_path('uploads/guides/' . $existing);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $page->after_image = null;
            $page->featured_image = null;
            $page->og_image = null;
        } elseif ($request->hasFile('after_image') || $request->hasFile('featured_image')) {
            $afterFile = $request->file('after_image') ?: $request->file('featured_image');
            if ($afterFile) {
                $existing = $page->after_image ?: $page->featured_image;
                if (!empty($existing)) {
                    $oldPath = public_path('uploads/guides/' . $existing);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $filename = 'page_' . time() . '_' . Str::random(8) . '.' . $afterFile->getClientOriginalExtension();
                $afterFile->move($destPath, $filename);
                $page->after_image = $filename;
                $page->featured_image = $filename;
                $page->og_image = $filename;
            }
        }

        $page->save();

        $activeTab = $request->input('active_tab');
        $redirectUrl = url('panel/admin/page-manager/edit/' . $page->id);
        if ($activeTab) {
            $redirectUrl .= '#' . ltrim($activeTab, '#');
        }

        return redirect($redirectUrl)->with('success_message', 'Page settings updated successfully.');
    }

    /**
     * Delete page and its relations.
     */
    public function destroy($id)
    {
        $this->checkPermission();

        $page = SeoLandingPage::findOrFail($id);
        $page->delete();

        return redirect('panel/admin/page-manager')->with('success_message', 'Page deleted successfully.');
    }

    /**
     * Save / update a section.
     */
    public function saveSection(Request $request, $pageId)
    {
        $this->checkPermission();

        $request->validate([
            'section_id' => 'nullable|exists:seo_landing_page_sections,id',
            'type' => 'required|string|max:50',
            'badge' => 'nullable|string|max:100',
            'heading' => 'nullable|string|max:255',
            'subheading' => 'nullable|string',
            'content' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $section = $request->filled('section_id')
            ? SeoLandingPageSection::where('landing_page_id', $pageId)->findOrFail($request->section_id)
            : new SeoLandingPageSection();

        $section->landing_page_id = $pageId;
        $section->type = $request->type;
        $section->badge = $request->badge;
        $section->heading = $request->heading;
        $section->subheading = $request->subheading;
        $section->content = $request->content ? Purify::clean($request->content) : null;
        $section->sort_order = $request->sort_order ?? ($section->sort_order ?? 0);
        $section->is_active = $request->has('is_active') ? (bool)$request->is_active : true;

        // Structured settings handling (cards, faqs, specs, stats)
        $settings = $section->settings ?? [];
        if ($request->filled('settings_json')) {
            $rawJson = trim($request->settings_json);

            // Strip UTF-8 BOM if present
            if (substr($rawJson, 0, 3) === "\xEF\xBB\xBF") {
                $rawJson = substr($rawJson, 3);
            }

            // Normalize curly / smart quotes to standard ASCII quotes
            $normalizedJson = str_replace(
                ["\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}", '“', '”', '‘', '’'],
                ['"', '"', "'", "'", '"', '"', "'", "'"],
                $rawJson
            );

            // Strip trailing commas before closing braces/brackets
            $cleanedJson = preg_replace('/,\s*([\]}])/m', '$1', $normalizedJson);

            $decoded = json_decode($cleanedJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $settings = $decoded;
            } else {
                $decodedRaw = json_decode($rawJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedRaw)) {
                    $settings = $decodedRaw;
                } else {
                    return redirect()->back()
                        ->withErrors(['settings_json' => 'Invalid JSON in Structured JSON Data: ' . json_last_error_msg()])
                        ->withInput()
                        ->with('error_message', 'Could not save section: The Structured JSON Data contains syntax errors (' . json_last_error_msg() . '). Please verify and try again.');
                }
            }

            // Auto-wrap bare lists into proper structured root keys if user pasted a raw array of items
            if (is_array($settings) && array_is_list($settings) && !empty($settings)) {
                if (in_array($section->type, ['prompt_grid', 'problem_solution', 'role_grid', 'alternating_rows'])) {
                    $settings = ['cards' => $settings];
                } elseif ($section->type === 'faq_accordion') {
                    $settings = ['faqs' => $settings];
                } elseif ($section->type === 'metrics_counter') {
                    $settings = ['stats' => $settings];
                } elseif ($section->type === 'specs_table') {
                    $settings = ['specs' => $settings];
                } else {
                    $settings = ['cards' => $settings];
                }
            }
        } elseif ($request->has('settings_json') && trim($request->settings_json) === '') {
            $settings = [];
        }

        // Support direct card form inputs if provided (merges with settings['cards'] to preserve any other properties)
        if ($request->has('cards') && is_array($request->cards)) {
            if (!isset($settings['cards']) || !is_array($settings['cards'])) {
                $settings['cards'] = [];
            }
            foreach ($request->cards as $idx => $cardInput) {
                if (!isset($settings['cards'][$idx])) {
                    $settings['cards'][$idx] = [];
                }
                foreach ($cardInput as $key => $val) {
                    $settings['cards'][$idx][$key] = $val;
                }
            }
        }

        // Handle step / card images for alternating_rows, prompt_grid, and card sections
        if (isset($settings['cards']) && is_array($settings['cards'])) {
            $destPath = public_path('uploads/guides');
            if (!File::exists($destPath)) {
                File::makeDirectory($destPath, 0755, true);
            }

            // Remove card image if requested
            if ($request->has('remove_card_image')) {
                foreach ($request->remove_card_image as $idx => $val) {
                    if (isset($settings['cards'][$idx])) {
                        $settings['cards'][$idx]['image'] = null;
                    }
                }
            }

            // Upload card image file (new file upload takes priority)
            if ($request->hasFile('card_images')) {
                foreach ($request->file('card_images') as $idx => $file) {
                    if ($file && $file->isValid() && isset($settings['cards'][$idx])) {
                        $filename = 'step_' . ($section->id ?: 'new') . '_' . $idx . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move($destPath, $filename);
                        $settings['cards'][$idx]['image'] = asset('public/uploads/guides/' . $filename);
                    }
                }
            }

            // Direct URL input: Only set if user changed the input or if JSON card had no image
            if ($request->has('card_image_urls') && is_array($request->card_image_urls)) {
                $origUrls = $request->input('card_image_urls_orig', []);
                foreach ($request->card_image_urls as $idx => $url) {
                    $url = trim($url);
                    if (isset($settings['cards'][$idx])) {
                        $origUrl = isset($origUrls[$idx]) ? trim($origUrls[$idx]) : null;
                        if ($origUrl !== null && $url !== $origUrl) {
                            // User intentionally changed the URL input in the form
                            $settings['cards'][$idx]['image'] = $url;
                        } elseif (!empty($url) && empty($settings['cards'][$idx]['image'])) {
                            // Default to form URL if JSON card image was empty
                            $settings['cards'][$idx]['image'] = $url;
                        }
                    }
                }
            }
        }

        // Normalize metrics counter if applicable
        if ($section->type === 'metrics_counter' && !empty($settings['stats']) && is_array($settings['stats'])) {
            foreach ($settings['stats'] as &$st) {
                $num = $st['number'] ?? ($st['value'] ?? '10x');
                $st['number'] = $num;
                $st['value'] = $num;
            }
        }

        $section->settings = $settings;
        $section->save();

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('footer_cms_navigation');
        if (class_exists(\App\Http\Controllers\SitemapController::class)) {
            \App\Http\Controllers\SitemapController::clearCache();
        }

        return redirect()->back()->with('success_message', 'Section saved successfully.');
    }

    /**
     * Delete a section.
     */
    public function deleteSection($pageId, $sectionId)
    {
        $this->checkPermission();

        $section = SeoLandingPageSection::where('landing_page_id', $pageId)->findOrFail($sectionId);
        $section->delete();

        return redirect()->back()->with('success_message', 'Section removed successfully.');
    }

    /**
     * Reorder sections via Ajax.
     */
    public function reorderSections(Request $request, $pageId)
    {
        $this->checkPermission();

        $order = $request->input('order', []);
        if (is_array($order)) {
            foreach ($order as $sort => $id) {
                SeoLandingPageSection::where('landing_page_id', $pageId)
                    ->where('id', $id)
                    ->update(['sort_order' => $sort + 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Attach prompt or photoshoot item.
     */
    public function attachItem(Request $request, $pageId)
    {
        $this->checkPermission();

        $request->validate([
            'item_type' => 'required|in:prompt,photoshoot',
            'item_id' => 'required|integer',
            'custom_badge' => 'nullable|string|max:100',
        ]);

        $exists = SeoLandingPageItem::where('landing_page_id', $pageId)
            ->where('item_type', $request->item_type)
            ->where('item_id', $request->item_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['item_id' => 'This item is already attached to this page.']);
        }

        SeoLandingPageItem::create([
            'landing_page_id' => $pageId,
            'item_type' => $request->item_type,
            'item_id' => $request->item_id,
            'custom_badge' => $request->custom_badge,
            'sort_order' => (SeoLandingPageItem::where('landing_page_id', $pageId)->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success_message', 'Item attached successfully.');
    }

    /**
     * Detach item.
     */
    public function detachItem($pageId, $itemId)
    {
        $this->checkPermission();

        SeoLandingPageItem::where('landing_page_id', $pageId)->where('id', $itemId)->delete();

        return redirect()->back()->with('success_message', 'Item detached successfully.');
    }

    /**
     * Ajax search prompts.
     */
    public function searchPrompts(Request $request)
    {
        $this->checkPermission();
        $q = trim($request->q);
        $results = Images::where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%")
                      ->orWhere('id', $q);
            })
            ->limit(15)
            ->get(['id', 'title', 'thumbnail', 'preview']);

        return response()->json($results);
    }

    /**
     * Retrieve Super Admin authors for dropdown.
     */
    protected function getSuperAdminAuthors()
    {
        $superAdminRoleIds = RolesAndPermissions::where('permissions', 'full_access')->pluck('id');
        $authors = User::whereIn('role', $superAdminRoleIds)->orderBy('name')->get();

        if ($authors->isEmpty()) {
            $authors = User::where('id', 1)->get();
        }

        return $authors;
    }
}
