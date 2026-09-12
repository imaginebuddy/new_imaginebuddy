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
	   ->with(['author:id,avatar,name,username', 'category:id,name,slug', 'stock:id,images_id,name,type,resolution']);
	 }

	 public function scopeCountLikesDownloads($query)
	 {
		return $query->withCount(['likes', 'downloads']);
	 }
}
