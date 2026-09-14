<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
	protected $guarded = [];
	public $timestamps = false;

	public function images()
	{
		return $this->hasMany(Images::class, 'categories_id')->where('status', 'active');
	}

	public function subcategories()
	{
		return $this->hasMany(Subcategories::class, 'category_id')->where('mode', 'on');
	}

}
