<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
	public $timestamps = false;

	public static function users()
	{
		$sort      =  request()->get('sort');
		$location  =  request()->get('location');

		if ($sort == 'latest') {
			$sortQuery = 'users.id';
		} else if ($sort == 'photos') {
			$sortQuery = 'COUNT(images.id)';
		} else {
			$sortQuery = 'COUNT(followers.id)';
		}

		$data = User::where('users.status', 'active');

		// lOCATION
		if (isset($location) && $location != '') {
			$data->where('users.countries_id', $location);
		}

		// PHOTOS
		if ($sort == 'photos') {
			$data->leftjoin('images', 'users.id', '=', \DB::raw('images.user_id AND images.status = "active"'));
		}

		// POPULAR
		if ($sort == 'popular' || !$sort) {
			$data->leftjoin('followers', 'users.id', '=', \DB::raw('followers.following AND followers.status = "1"'));
		}

		$query = 	$data->where('users.status', '=', 'active')
			->groupBy('users.id')
			->orderBy(\DB::raw($sortQuery), 'DESC')
			->orderBy('users.id', 'ASC')
			->select(
				'users.id',
				'users.username',
				'users.name',
				'users.avatar',
				'users.cover',
				'users.status'
			)
			->with(['images' => function ($query) {
				$query->select('id', 'user_id', 'thumbnail')->orderByDesc('id');
			}])
			->withCount(['images', 'followers'])
			->paginate(config('settings.result_request'))->onEachSide(1);

		return $query;
	}

	//Search
	public static function searchImages()
	{
		$q     = request()->get('q');
		$page  = request()->get('page');
		$sort  = request()->get('sort');

		try {
			$query = Images::search($q)
				->with(['author:id,avatar,name,username', 'category:id,name,slug', 'stock:id,images_id,name,type,resolution'])
				->where('images.status', 'active');

			if ($sort == 'oldest') {
				$query->orderBy('images.id', 'asc');
			} else if ($sort == 'latest') {
				$query->orderBy('images.id', 'desc');
			} // Default retains orderByDesc('relevance') from scopeSearch

			$images = $query->paginate(config('settings.result_request', 12))->onEachSide(1);
		} catch (\Exception $e) {
			// Fallback to LIKE search if MySQL/MariaDB FULLTEXT index is missing or throws 1064
			$query = Images::searchLike($q)
				->selectFieldsRelation()
				->where('images.status', 'active');

			if ($sort == 'oldest') {
				$query->orderBy('images.id', 'asc');
			} else {
				$query->orderBy('images.id', 'desc');
			}

			$images = $query->paginate(config('settings.result_request', 12))->onEachSide(1);
		}

		$title = __('misc.result_of') . ' ' . $q . ' - ';
		$total = $images->total();

		return ['images' => $images, 'page' => $page, 'title' => $title, 'total' => $total, 'q' => $q];
	}

	public static function latestImagesHome()
	{
		$poolLimit = max(30, (int) config('settings.result_request', 12) * 2);

		$data = Images::selectFieldsRelation()
			->where('images.status', 'active')
			->orderBy('images.id', 'DESC')
			->take($poolLimit)
			->get();

		return $data->shuffle()->take((int) config('settings.result_request', 12));
	}

	public static function latestImages()
	{
		$poolLimit = (int) config('settings.result_request', 12) * 4;

		$recentIds = Images::where('status', 'active')
			->orderBy('id', 'DESC')
			->take($poolLimit)
			->pluck('id');

		$data = Images::selectFieldsRelation()
			->whereIn('images.id', $recentIds)
			->inRandomOrder()
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return $data;
	}

	public static function featuredImages()
	{
		$query = Images::selectFieldsRelation()
			->where('featured', 'yes')
			->where('status', 'active');

		//=== Timeframe
		$query->when(request('timeframe') == 'today', function ($q) {
			$q->where('featured_date', '>=', Carbon::today());
		});

		$query->when(request('timeframe') == 'week', function ($q) {
			$q->whereBetween('featured_date', [
				Carbon::parse()->startOfWeek(),
				Carbon::parse()->endOfWeek(),
			]);
		});

		$query->when(request('timeframe') == 'month', function ($q) {
			$q->whereBetween('featured_date', [
				Carbon::parse()->startOfMonth(),
				Carbon::parse()->endOfMonth(),
			]);
		});

		$query->when(request('timeframe') == 'year', function ($q) {
			$q->whereYear('featured_date', date('Y'));
		});

		$data = $query->orderBy('featured_date', 'DESC')->paginate(config('settings.result_request'))->onEachSide(1);


		return $data;
	}

	public static function popularImages()
	{
		$query = Images::join('likes', function ($join) {
			$join->on('likes.images_id', '=', 'images.id')
				->where('images.status', 'active');
		});

		//=== Timeframe
		$query->when(request('timeframe') == 'today', function ($q) {
			$q->where('likes.date', '>=', Carbon::today()->toDateString());
		});

		$query->when(request('timeframe') == 'week', function ($q) {
			$q->whereBetween('likes.date', [
				Carbon::parse()->startOfWeek(),
				Carbon::parse()->endOfWeek(),
			]);
		});

		$query->when(request('timeframe') == 'month', function ($q) {
			$q->whereBetween('likes.date', [
				Carbon::parse()->startOfMonth(),
				Carbon::parse()->endOfMonth(),
			]);
		});

		$query->when(request('timeframe') == 'year', function ($q) {
			$q->whereYear('likes.date', date('Y'));
		});

		$data = $query->groupBy('likes.images_id')
			->orderByRaw('COUNT(likes.images_id) desc')
			->selectFieldsRelation()
			->paginate(config('settings.result_request'))->onEachSide(1);

		return $data;
	}

	public static function commentedImages()
	{
		$query = Images::join('comments', 'images.id', '=', 'comments.images_id')
			->where('images.status', 'active');

		//=== Timeframe
		$query->when(request('timeframe') == 'today', function ($q) {
			$q->where('comments.date', '>=', Carbon::today()->toDateString());
		});

		$query->when(request('timeframe') == 'week', function ($q) {
			$q->whereBetween('comments.date', [
				Carbon::parse()->startOfWeek(),
				Carbon::parse()->endOfWeek(),
			]);
		});

		$query->when(request('timeframe') == 'month', function ($q) {
			$q->whereBetween('comments.date', [
				Carbon::parse()->startOfMonth(),
				Carbon::parse()->endOfMonth(),
			]);
		});

		$query->when(request('timeframe') == 'year', function ($q) {
			$q->whereYear('comments.date', date('Y'));
		});

		$data = $query->groupBy('comments.images_id')
			->orderByRaw('COUNT(comments.images_id) desc')
			->selectFieldsRelation()
			->paginate(config('settings.result_request'))->onEachSide(1);

		return $data;
	}

	public static function viewedImages()
	{
		$query = Images::join('visits', 'images.id', '=', 'visits.images_id')
			->where('images.status', 'active');

		//=== Timeframe
		$query->when(request('timeframe') == 'today', function ($q) {
			$q->where('visits.date', '>=', Carbon::today()->toDateString());
		});

		$query->when(request('timeframe') == 'week', function ($q) {
			$q->whereBetween('visits.date', [
				Carbon::parse()->startOfWeek(),
				Carbon::parse()->endOfWeek(),
			]);
		});

		$query->when(request('timeframe') == 'month', function ($q) {
			$q->whereBetween('visits.date', [
				Carbon::parse()->startOfMonth(),
				Carbon::parse()->endOfMonth(),
			]);
		});

		$query->when(request('timeframe') == 'year', function ($q) {
			$q->whereYear('visits.date', date('Y'));
		});

		$data = $query->groupBy('visits.images_id')
			->orderByRaw('COUNT(visits.images_id) desc')
			->selectFieldsRelation()
			->paginate(config('settings.result_request'))->onEachSide(1);

		return $data;
	}

	public static function downloadsImages()
	{
		$query = Images::join('downloads', 'images.id', '=', 'downloads.images_id')
			->where('images.status', 'active');

		//=== Timeframe
		$query->when(request('timeframe') == 'today', function ($q) {
			$q->where('downloads.date', '>=', Carbon::today()->toDateString());
		});

		$query->when(request('timeframe') == 'week', function ($q) {
			$q->whereBetween('downloads.date', [
				Carbon::parse()->startOfWeek(),
				Carbon::parse()->endOfWeek(),
			]);
		});

		$query->when(request('timeframe') == 'month', function ($q) {
			$q->whereBetween('downloads.date', [
				Carbon::parse()->startOfMonth(),
				Carbon::parse()->endOfMonth(),
			]);
		});

		$query->when(request('timeframe') == 'year', function ($q) {
			$q->whereYear('downloads.date', date('Y'));
		});

		$data = $query->groupBy('downloads.images_id')
			->orderByRaw('COUNT(downloads.images_id) desc')
			->selectFieldsRelation()
			->paginate(config('settings.result_request'))->onEachSide(1);

		return $data;
	}

	public static function copiedImages()
	{
		$query = Images::selectFieldsRelation()
			->where('images.status', 'active');

		if (request('ai_model')) {
			$query->where('images.ai_model', request('ai_model'));
		}

		$data = $query->orderBy('images.copies_count', 'DESC')
			->orderBy('images.id', 'DESC')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return $data;
	}

	public static function categoryImages($slug)
	{
		$category = Categories::with(['subcategories:id,category_id,name,slug'])->where('slug', '=', $slug)->firstOrFail();

		$images   = Images::selectFieldsRelation()
			->where('status', 'active')
			->where('categories_id', $category->id)
			->orderBy('id', 'DESC')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return ['images' => $images, 'category' => $category];
	}

	public static function subCategoryImages($slug, $subcategory)
	{
		$subcategory = Subcategories::with(['category:id,name,slug'])->where('slug', '=', $subcategory)->firstOrFail();

		$images   = Images::selectFieldsRelation()
			->where('status', 'active')
			->where('subcategories_id', $subcategory->id)
			->orderBy('id', 'DESC')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return ['images' => $images, 'subcategory' => $subcategory];
	}

	public static function tagsImages($tags)
	{
		$images = Images::where('tags', 'LIKE', '%' . $tags . '%')
			->where('status', 'active')
			->groupBy('id')
			->orderBy('id', 'desc')
			->paginate(config('settings.result_request'))->onEachSide(1);

		$title = __('misc.tags') . ' - ' . $tags;

		$total = $images->total();

		return ['images' => $images, 'title' => $title, 'total' => $total, 'tags' => $tags];
	}

	public static function camerasImages($camera)
	{
		$images = Images::selectFieldsRelation()
			->where('camera', 'LIKE', '%' . $camera . '%')
			->where('status', 'active')
			->groupBy('id')
			->orderBy('id', 'desc')
			->paginate(config('settings.result_request'))->onEachSide(1);

		$title = __('misc.photos_taken_with') . ' ' . ucfirst($camera);

		$total = $images->total();

		return ['images' => $images, 'title' => $title, 'total' => $total, 'camera' => $camera];
	}

	public static function colorsImages($colors)
	{
		$images = Images::selectFieldsRelation()
			->where('colors', 'LIKE', '%' . $colors . '%')
			->where('status', 'active')
			->groupBy('id')
			->orderBy('id', 'desc')
			->paginate(config('settings.result_request'))->onEachSide(1);

		$title = __('misc.colors') . ' #' . $colors;

		$total = $images->total();

		return ['images' => $images, 'title' => $title, 'total' => $total, 'colors' => $colors];
	}

	public static function userImages($id)
	{
		$images = Images::selectFieldsRelation()
			->where('user_id', $id)
			->where('status', 'active')
			->groupBy('id')
			->orderBy('id', 'desc')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return $images;
	}

	public static function premiumImages()
	{
		$data = Images::selectFieldsRelation()
			->where('item_for_sale', 'sale')
			->where('status', 'active')
			->orderBy('id', 'DESC')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return $data;
	}

	public static function vectors()
	{
		$data = Images::selectFieldsRelation()
			->where('vector', 'yes')
			->orderBy('images.id', 'DESC')
			->paginate(config('settings.result_request'))
			->onEachSide(1);

		return $data;
	}
}
