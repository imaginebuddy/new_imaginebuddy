<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    protected $table = 'blog_posts';

    protected $fillable = [
        'user_id',
        'blog_category_id',
        'title',
        'slug',
        'preview_token',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'status',
        'is_featured',
        'reading_time',
        'views_count',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'schema_type',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer',
        'views_count' => 'integer',
    ];

    /**
     * Author relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Tags relationship.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id');
    }

    /**
     * Scope query to published posts with published_at in the past.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope query to featured spotlight posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Helper to get featured image full URL or fallback.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        if (!empty($this->featured_image)) {
            $path = config('path.blog', 'uploads/blog/') . $this->featured_image;
            if (file_exists(public_path($path))) {
                return url('public/' . $path);
            }
            if (Storage::exists($path)) {
                return Storage::url($path);
            }
            return url('public/' . $path);
        }

        return asset('public/img/thumbnail-default.jpg');
    }

    /**
     * Calculate reading time in minutes based on 200 words per minute.
     */
    public static function estimateReadingTime(string $htmlContent): int
    {
        $text = strip_tags($htmlContent);
        $wordCount = str_word_count($text);
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Determine if post is currently published and live.
     */
    public function isLive(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->isPast();
    }
}
