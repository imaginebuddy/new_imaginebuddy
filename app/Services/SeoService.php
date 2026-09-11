<?php

namespace App\Services;

use App\Models\SeoMetadata;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Explicit page key set by controller or middleware.
     */
    protected ?string $pageKey = null;

    /**
     * Active model entity (e.g. Image, Category, Photoshoot).
     */
    protected $entity = null;

    /**
     * Resolved SEO data cache for current request cycle.
     */
    protected ?array $resolved = null;

    /**
     * Set explicit page key.
     */
    public function setPage(string $key): self
    {
        $this->pageKey = $key;
        $this->resolved = null;
        return $this;
    }

    /**
     * Set explicit entity.
     */
    public function setEntity($entity): self
    {
        $this->entity = $entity;
        $this->resolved = null;
        return $this;
    }

    /**
     * Get cached registry of all active SEO records.
     */
    public function getRegistry()
    {
        return Cache::rememberForever(SeoMetadata::CACHE_KEY, function () {
            return SeoMetadata::active()->get();
        });
    }

    /**
     * Resolve the active SEO configuration.
     */
    public function resolve(): array
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        $siteTitle = config('settings.title') ?: config('app.name', 'ImagineBuddy');
        $registry = $this->getRegistry();

        // 1. Check if an entity is provided (Prompt/Image, Category, Photoshoot)
        if ($this->entity) {
            $this->resolved = $this->resolveEntityMetadata($this->entity, $registry, $siteTitle);
            return $this->resolved;
        }

        // 2. Lookup by explicit pageKey if specified
        if ($this->pageKey) {
            $record = $registry->firstWhere('page_key', $this->pageKey);
            if ($record) {
                $this->resolved = $this->recordToPayload($record, $siteTitle);
                return $this->resolved;
            }
        }

        // 3. Lookup by Route Name
        $currentRoute = Request::route() ? Request::route()->getName() : null;
        if ($currentRoute) {
            $record = $registry->firstWhere('route_name', $currentRoute);
            if ($record) {
                $this->resolved = $this->recordToPayload($record, $siteTitle);
                return $this->resolved;
            }
        }

        // 4. Lookup by normalized Path
        $currentPath = '/' . trim(Request::path(), '/');
        if ($currentPath === '') {
            $currentPath = '/';
        }
        $record = $registry->firstWhere('path', $currentPath);
        if ($record) {
            $this->resolved = $this->recordToPayload($record, $siteTitle);
            return $this->resolved;
        }

        // 5. Fallback for known standard routes if pageKey was not directly assigned
        $fallbackKey = $this->detectFallbackKey($currentPath);
        if ($fallbackKey) {
            $record = $registry->firstWhere('page_key', $fallbackKey);
            if ($record) {
                $this->resolved = $this->recordToPayload($record, $siteTitle);
                return $this->resolved;
            }
        }

        // 6. Global default fallback
        $defaultDesc = trans('seo.description') !== 'seo.description' ? trans('seo.description') : 'AI Prompts, Stock Photos and Creative Assets';
        $defaultKeywords = trans('seo.keywords') !== 'seo.keywords' ? trans('seo.keywords') : 'AI prompts, stock photos, Midjourney';

        $this->resolved = [
            'title' => $siteTitle . ' - ' . (trans('seo.welcome_subtitle') !== 'seo.welcome_subtitle' ? trans('seo.welcome_subtitle') : 'AI Image Prompts'),
            'description' => $defaultDesc,
            'keywords' => $defaultKeywords,
            'canonical' => url()->current(),
            'robots' => 'index, follow',
            'og_title' => $siteTitle,
            'og_description' => $defaultDesc,
            'og_image' => url('public/img', config('settings.logo_light', 'logo.png')),
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'schema_type' => 'WebSite',
            'schema_custom' => null,
            'has_custom_title' => false,
            'has_custom_desc' => false,
            'has_custom_keywords' => false,
        ];

        return $this->resolved;
    }

    /**
     * Map a path to a known fallback page_key.
     */
    protected function detectFallbackKey(string $path): ?string
    {
        $map = [
            '/' => 'home',
            '/pricing' => 'pricing',
            '/photoshoots' => 'photoshoots_index',
            '/photos/premium' => 'photos_premium',
            '/featured' => 'explore_featured',
            '/popular' => 'explore_popular',
            '/latest' => 'explore_latest',
            '/most/viewed' => 'explore_viewed',
            '/most/downloads' => 'explore_downloads',
            '/most/copied' => 'explore_copied',
            '/page/terms-and-conditions' => 'page_terms',
            '/page/privacy-policy' => 'page_privacy',
            '/contact' => 'contact',
        ];

        return $map[$path] ?? null;
    }

    /**
     * Convert an SeoMetadata Eloquent record to standard payload.
     */
    protected function recordToPayload(SeoMetadata $record, string $siteTitle): array
    {
        $canonical = $record->canonical_url ? $record->canonical_url : url($record->path ?: Request::path());
        $title = $record->meta_title ?: $siteTitle;
        $description = $record->meta_description ?: (trans('seo.description') ?: 'AI Prompts & Creative Stock');
        $ogImage = $record->og_image ? url($record->og_image) : url('public/img', config('settings.logo_light', 'logo.png'));

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $record->meta_keywords ?: '',
            'canonical' => $canonical,
            'robots' => $record->robots ?: 'index, follow',
            'og_title' => $record->og_title ?: $title,
            'og_description' => $record->og_description ?: $description,
            'og_image' => $ogImage,
            'og_type' => 'website',
            'twitter_card' => $record->twitter_card ?: 'summary_large_image',
            'schema_type' => $record->schema_type ?: 'WebPage',
            'schema_custom' => $record->schema_custom,
            'has_custom_title' => !empty($record->meta_title),
            'has_custom_desc' => !empty($record->meta_description),
            'has_custom_keywords' => !empty($record->meta_keywords),
        ];
    }

    /**
     * Resolve metadata for dynamic entities (Images, Categories, Photoshoots).
     */
    protected function resolveEntityMetadata($entity, $registry, string $siteTitle): array
    {
        $className = class_basename($entity);

        // Case A: Image / Prompt
        if ($className === 'Images') {
            $template = $registry->firstWhere('page_key', 'prompt_template');
            $titlePattern = $template ? $template->meta_title : '{title} - AI Prompt & Image | {site_name}';
            $descPattern = $template ? $template->meta_description : 'Get the prompt for "{title}" generated in {category}. Copy parameters and tags on {site_name}.';

            $categoryName = $entity->category ? $entity->category->name : 'General';
            $authorName = $entity->author ? $entity->author->username : 'ImagineBuddy';
            $tags = $entity->tags ?: 'AI Art, Prompt';

            $tokens = [
                '{title}' => $entity->title,
                '{category}' => $categoryName,
                '{tags}' => $tags,
                '{author}' => $authorName,
                '{site_name}' => $siteTitle,
                '{year}' => date('Y'),
            ];

            $keywordsPattern = $template && !empty($template->meta_keywords) ? $template->meta_keywords : '{tags}, {category}, AI prompt';

            $finalTitle = $entity->meta_title ?: str_replace(array_keys($tokens), array_values($tokens), $titlePattern);
            $finalDesc = $entity->meta_description ?: str_replace(array_keys($tokens), array_values($tokens), $descPattern);
            $finalKeywords = !empty($entity->meta_keywords) ? $entity->meta_keywords : str_replace(array_keys($tokens), array_values($tokens), $keywordsPattern);

            $previewUrl = $entity->preview ? url('public/uploads/preview', $entity->preview) : url('public/img', config('settings.logo_light'));

            return [
                'title' => $finalTitle,
                'description' => $finalDesc,
                'keywords' => $finalKeywords,
                'canonical' => url('prompt/' . $entity->slug),
                'robots' => 'index, follow',
                'og_title' => $finalTitle,
                'og_description' => $finalDesc,
                'og_image' => $previewUrl,
                'og_type' => 'article',
                'twitter_card' => 'summary_large_image',
                'schema_type' => 'ImageObject',
                'schema_custom' => null,
                'entity' => $entity,
                'has_custom_title' => true,
                'has_custom_desc' => true,
                'has_custom_keywords' => true,
            ];
        }

        // Case B: Category
        if ($className === 'Categories') {
            $template = $registry->firstWhere('page_key', 'category_template');
            $titlePattern = $template ? $template->meta_title : 'Best {category} AI Prompts & Images | {site_name}';
            $descPattern = $template ? $template->meta_description : 'Browse and copy top {category} prompts. Download high-resolution AI art on {site_name}.';
            $keywordsPattern = $template && !empty($template->meta_keywords) ? $template->meta_keywords : '{category} prompts, {category} AI art, prompt engineering';

            $tokens = [
                '{category}' => $entity->name,
                '{title}' => $entity->name,
                '{site_name}' => $siteTitle,
                '{year}' => date('Y'),
            ];

            $finalTitle = !empty($entity->seo_title) ? $entity->seo_title : str_replace(array_keys($tokens), array_values($tokens), $titlePattern);
            $finalDesc = !empty($entity->description) ? $entity->description : str_replace(array_keys($tokens), array_values($tokens), $descPattern);
            $finalKeywords = !empty($entity->keywords) ? $entity->keywords : str_replace(array_keys($tokens), array_values($tokens), $keywordsPattern);

            $catImage = !empty($entity->thumbnail) ? url('public/img-category', $entity->thumbnail) : url('public/img', config('settings.logo_light'));

            return [
                'title' => $finalTitle,
                'description' => $finalDesc,
                'keywords' => $finalKeywords,
                'canonical' => url('category/' . $entity->slug),
                'robots' => 'index, follow',
                'og_title' => $finalTitle,
                'og_description' => $finalDesc,
                'og_image' => $catImage,
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'schema_type' => 'CollectionPage',
                'schema_custom' => null,
                'entity' => $entity,
                'has_custom_title' => true,
                'has_custom_desc' => true,
                'has_custom_keywords' => true,
            ];
        }

        // Case C: Subcategory
        if ($className === 'Subcategories') {
            $template = $registry->firstWhere('page_key', 'category_template');
            $titlePattern = $template ? $template->meta_title : 'Best {category} AI Prompts & Images | {site_name}';
            $descPattern = $template ? $template->meta_description : 'Browse and copy top {category} prompts. Download high-resolution AI art on {site_name}.';
            $keywordsPattern = $template && !empty($template->meta_keywords) ? $template->meta_keywords : '{category} prompts, {category} AI art, prompt engineering';

            $catName = $entity->category ? $entity->category->name : '';
            $subName = $entity->name . ($catName ? ' - ' . $catName : '');

            $tokens = [
                '{category}' => $subName,
                '{title}' => $entity->name,
                '{site_name}' => $siteTitle,
                '{year}' => date('Y'),
            ];

            $finalTitle = str_replace(array_keys($tokens), array_values($tokens), $titlePattern);
            $finalDesc = str_replace(array_keys($tokens), array_values($tokens), $descPattern);
            $finalKeywords = str_replace(array_keys($tokens), array_values($tokens), $keywordsPattern);

            return [
                'title' => $finalTitle,
                'description' => $finalDesc,
                'keywords' => $finalKeywords,
                'canonical' => url('category/' . ($entity->category ? $entity->category->slug : 'all') . '/' . $entity->slug),
                'robots' => 'index, follow',
                'og_title' => $finalTitle,
                'og_description' => $finalDesc,
                'og_image' => url('public/img', config('settings.logo_light')),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'schema_type' => 'CollectionPage',
                'schema_custom' => null,
                'entity' => $entity,
                'has_custom_title' => true,
                'has_custom_desc' => true,
                'has_custom_keywords' => true,
            ];
        }

        // Default fallback
        return $this->resolve();
    }

    /**
     * Generate Schema.org JSON-LD structured data.
     */
    public function generateJsonLd(): string
    {
        $data = $this->resolve();
        $siteUrl = url('/');
        $siteName = config('settings.title') ?: config('app.name', 'ImagineBuddy');
        $logoUrl = url('public/img', config('settings.logo_light', 'logo.png'));

        $schemas = [];

        // Base Organization
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => $logoUrl,
        ];

        // Specific Schemas
        switch ($data['schema_type']) {
            case 'WebSite':
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $siteUrl . '/search?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ];
                break;

            case 'PricingPage':
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'url' => $data['canonical'],
                ];
                break;

            case 'ContactPage':
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'ContactPage',
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'url' => $data['canonical'],
                ];
                break;

            case 'ImageObject':
                if (isset($data['entity']) && class_basename($data['entity']) === 'Images') {
                    $img = $data['entity'];
                    $schemas[] = [
                        '@context' => 'https://schema.org',
                        '@type' => 'ImageObject',
                        'name' => $img->title,
                        'caption' => $img->title,
                        'description' => $data['description'],
                        'contentUrl' => $data['og_image'],
                        'url' => $data['canonical'],
                        'author' => [
                            '@type' => 'Person',
                            'name' => $img->author ? $img->author->username : 'ImagineBuddy',
                        ],
                        'datePublished' => $img->date ? date('c', strtotime($img->date)) : date('c'),
                    ];
                }
                break;

            case 'CollectionPage':
            default:
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => $data['schema_type'] ?: 'WebPage',
                    'name' => $data['title'],
                    'description' => $data['description'],
                    'url' => $data['canonical'],
                ];
                break;
        }

        // Custom schema override if defined
        if (!empty($data['schema_custom'])) {
            $custom = json_decode($data['schema_custom'], true);
            if (is_array($custom)) {
                $schemas[] = $custom;
            }
        }

        return '<script type="application/ld+json">' . json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }
}
