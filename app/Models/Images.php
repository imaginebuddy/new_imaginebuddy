<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Traits\SearchTrait;

class Images extends Model
{
	use SearchTrait;

	protected $guarded = [];
	const CREATED_AT = 'date';
	const UPDATED_AT = null;

	protected $fillable = [
    	'title',
		'slug',
		'meta_title',
		'meta_description',
		'meta_keywords',
		'description',
		'prompt',
		'ai_model',
		'copies_count',
		'categories_id',
		'subcategories_id',
		'photoshoot_id',
		'photoshoot_order',
		'tags',
		'camera',
		'exif',
		'how_use_image',
		'attribution_required',
		'price',
		'item_for_sale'
    ];

	protected $searchable = [
	    'title',
	    'tags',
	    'prompt',
	    'description'
	];

	/**
	 * Invalidate sitemap cache whenever images/prompts are saved or deleted
	 */
	protected static function booted()
	{
		static::saved(function () {
			\App\Http\Controllers\SitemapController::clearCache();
		});

		static::deleted(function () {
			\App\Http\Controllers\SitemapController::clearCache();
		});
	}

	public static $aiModels = [
		'Nano Banana',
		'Midjourney',
		'GPT Image',
		'Flux',
		'Gemini',
		'Other'
	];

	public static function getAiModels()
	{
		$setting = config('settings.ai_models') ?: (AdminSettings::first()->ai_models ?? null);
		if (!$setting) {
			$setting = 'Nano Banana, Midjourney, GPT Image, Flux, Gemini, Other';
		}
		$modelsArray = array_map('trim', explode(',', $setting));
		return array_values(array_filter($modelsArray));
	}

	public function user()
	{
        return $this->belongsTo(User::class, 'user_id');
    }

	public function author()
	{
        return $this->belongsTo(User::class, 'user_id');
    }

	public function likes()
	{
		return $this->hasMany(Like::class)->where('status', '1');
	}

	public function downloads()
	{
		return $this->hasMany(Downloads::class);
	}

	public function totalImageDownloads()
	{
		return $this->downloads()
			->where(function ($q) {
				$q->where('action_type', 'download')
					->orWhere(function ($sq) {
						$sq->whereNull('action_type')->where('size', '!=', 'prompt');
					});
			})
			->count();
	}

	public function totalPromptCopies()
	{
		$copies = $this->downloads()
			->where(function ($q) {
				$q->where('action_type', 'copy')
					->orWhere('size', 'prompt');
			})
			->count();

		return max((int)$this->copies_count, $copies);
	}

	public function stock()
	{
		return $this->hasMany(Stock::class)->orderBy('type','asc');
	}

	public function examples()
	{
		return $this->hasMany(ImageExample::class, 'images_id');
	}

	 public function comments()
	 {
		return $this->hasMany(Comments::class);
	}

	 public function visits()
	 {
		return $this->hasMany(Visits::class);
	}

	 public function category()
	 {
	 	 return $this->belongsTo(Categories::class, 'categories_id');
	 }

	 public function subcategory()
	 {
	 	 return $this->belongsTo(Subcategories::class, 'subcategories_id');
	 }

	 public function photoshoot()
	 {
	 	 return $this->belongsTo(Photoshoot::class, 'photoshoot_id');
	 }

	 public function collections()
	 {
	 	 return $this->belongsTo(Collections::class);
	 }

	 public function collectionsImages()
	 {
	 	 return $this->belongsTo(CollectionsImages::class);
	 }

	 public function landingPageItems()
	 {
	 	 return $this->hasMany(SeoLandingPageItem::class, 'item_id')->where('item_type', 'prompt');
	 }

	  public function tags()
		{
	 	 return $this->hasMany(Images::class, 'tags');
	 }

	 public function purchases()
	 {
 		return $this->hasMany(Purchases::class);
 	}

	 public function scopeSelectFieldsRelation($query)
	 {
	   return $query->select(['images.id',
		'images.user_id',
		'images.title',
		'images.slug',
		'images.preview',
		'images.thumbnail',
		'images.colors',
		'images.extension',
		'images.featured',
		'images.item_for_sale',
		'images.prompt',
		'images.ai_model',
		'images.copies_count',
		'images.categories_id'
	   ])
	   ->with(['author:id,avatar,name,username', 'category:id,name,slug', 'stock:id,images_id,name,type,resolution', 'examples:id,images_id,file']);
	 }

	 public function scopeCountLikesDownloads($query)
	 {
		return $query->withCount(['likes', 'downloads']);
	 }

	/**
	 * Dynamic getter for image preview URL (compatible with S3, CloudFront, local, thumbnails, and stock fallbacks).
	 */
	public function getPreviewUrlAttribute(): ?string
	{
		if (!empty($this->preview)) {
			if (str_starts_with($this->preview, 'http://') || str_starts_with($this->preview, 'https://')) {
				return $this->preview;
			}
			return \Illuminate\Support\Facades\Storage::url(config('path.preview') . $this->preview);
		}

		if (!empty($this->thumbnail)) {
			if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
				return $this->thumbnail;
			}
			return \Illuminate\Support\Facades\Storage::url(config('path.thumbnail') . $this->thumbnail);
		}

		$stockImage = $this->relationLoaded('stock') ? $this->stock : $this->stock()->get();
		$chosenStock = $stockImage ? ($stockImage->where('type', 'small')->first() ?: ($stockImage->where('type', 'medium')->first() ?: $stockImage->first())) : null;
		if ($chosenStock && !empty($chosenStock->name)) {
			return url('files/preview/' . $chosenStock->resolution, $chosenStock->name) . '?size=medium&type=' . $this->item_for_sale;
		}

		return null;
	}
}
