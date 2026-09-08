<?php

namespace App\Http\Controllers;

use Mail;
use Image;
use App\Helper;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Stock;
use App\Models\Images;
use App\Models\Photoshoot;
use App\Models\Deposits;
use App\Models\Purchases;
use App\Models\Categories;
use App\Models\Collections;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Notifications;
use App\Models\Subcategories;
use App\Models\Subscriptions;
use App\Models\UsersReported;
use App\Models\ImagesReported;
use App\Models\PaymentGateways;
use Illuminate\Validation\Rule;
use App\Models\CollectionsImages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\DepositVerification;


class AdminController extends Controller
{
	protected $settings;

	public function __construct(AdminSettings $settings)
	{
		$this->settings = $settings::first();
	}
	// START
	public function dashboard()
	{
		if (!auth()->user()->hasPermission('dashboard')) {
			return view('admin.unauthorized');
		}

		$earningNetAdmin = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->sum('earning_net_admin');

		//  Calcule Chart Earnings last 30 days
		for ($i = 0; $i <= 30; ++$i) {

			$date = date('Y-m-d', strtotime('-' . $i . ' day'));

			// Earnings last 30 days
			$sales = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereDate('purchases.date', '=', $date)->sum('earning_net_admin');

			// Sales last 30 days
			$salesLast30 = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereDate('purchases.date', '=', $date)->count();

			// Format Date on Chart
			$formatDate = Helper::formatDateChart($date);
			$monthsData[] =  "'$formatDate'";

			// Earnings last 30 days
			$earningNetAdminSum[] = $sales;

			// Earnings last 30 days
			$lastSales[] = $salesLast30;
		}

		// Today
		$stat_revenue_today = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->where('purchases.date', '>=', Carbon::today())
			->sum('earning_net_admin');

		// Yesterday
		$stat_revenue_yesterday = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->where('purchases.date', '>=', Carbon::yesterday())
			->where('purchases.date', '<', Carbon::today())
			->sum('earning_net_admin');

		// Week
		$stat_revenue_week = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereBetween('purchases.date', [
			Carbon::parse()->startOfWeek(),
			Carbon::parse()->endOfWeek(),
		])->sum('earning_net_admin');

		// Last Week
		$stat_revenue_last_week = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereBetween('purchases.date', [
			Carbon::now()->startOfWeek()->subWeek(),
			Carbon::now()->subWeek()->endOfWeek(),
		])->sum('earning_net_admin');

		// Month
		$stat_revenue_month = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereBetween('purchases.date', [
			Carbon::parse()->startOfMonth(),
			Carbon::parse()->endOfMonth(),
		])->sum('earning_net_admin');

		// Last Month
		$stat_revenue_last_month = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->whereBetween('purchases.date', [
			Carbon::now()->startOfMonth()->subMonth(),
			Carbon::now()->subMonth()->endOfMonth(),
		])->sum('earning_net_admin');

		$label = implode(',', array_reverse($monthsData));
		$data = implode(',', array_reverse($earningNetAdminSum));

		$datalastSales = implode(',', array_reverse($lastSales));

		$totalImages = Images::count();
		$totalUsers  = User::count();
		$totalSales = Purchases::whereApproved('1')->where('mode', '!=', 'subscription')->count();

		return view('admin.dashboard', [
			'earningNetAdmin' => $earningNetAdmin,
			'label' => $label,
			'data' => $data,
			'datalastSales' => $datalastSales,
			'totalImages' => $totalImages,
			'totalUsers' => $totalUsers,
			'totalSales' => $totalSales,
			'stat_revenue_today' => $stat_revenue_today,
			'stat_revenue_yesterday' => $stat_revenue_yesterday,
			'stat_revenue_week' => $stat_revenue_week,
			'stat_revenue_last_week' => $stat_revenue_last_week,
			'stat_revenue_month' => $stat_revenue_month,
			'stat_revenue_last_month' => $stat_revenue_last_month
		]);
	}

	// START
	public function categories()
	{
		$data = Categories::orderBy('name')->get();

		return view('admin.categories')->withData($data);
	}

	public function addCategories()
	{
		return view('admin.add-categories');
	}

	public function storeCategories(Request $request)
	{
		$temp            = 'public/temp/'; // Temp
		$path            = 'public/img-category/'; // Path General

		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
			'name'        => 'required',
			'slug'        => 'required|ascii_only|unique:categories',
			'thumbnail'   => 'mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=457,min_height=359',
		];

		$this->validate($request, $rules);

		if ($request->hasFile('thumbnail')) {

			$extension        = $request->file('thumbnail')->getClientOriginalExtension();
			$type_mime_shot   = $request->file('thumbnail')->getMimeType();
			$sizeFile         = $request->file('thumbnail')->getSize();
			$thumbnail        = $request->slug . '-' . str_random(32) . '.' . $extension;

			if ($request->file('thumbnail')->move($temp, $thumbnail)) {

				$image = Image::make($temp . $thumbnail);

				if ($image->width() == 457 && $image->height() == 359) {

					\File::copy($temp . $thumbnail, $path . $thumbnail);
					\File::delete($temp . $thumbnail);
				} else {
					$image->fit(457, 359)->save($temp . $thumbnail);

					\File::copy($temp . $thumbnail, $path . $thumbnail);
					\File::delete($temp . $thumbnail);
				}
			} // End File
		} // HasFile

		else {
			$thumbnail = '';
		}

		$sql              = new Categories();
		$sql->name        = trim($request->name);
		$sql->slug        = strtolower($request->slug);
		$sql->page_title  = $request->page_title;
		$sql->seo_title   = $request->seo_title;
		$sql->keywords    = $request->keywords;
		$sql->description = $request->description;
		$sql->thumbnail   = $thumbnail;
		$sql->mode        = $request->mode ?? 'off';
		$sql->save();

		return redirect('panel/admin/categories')->withSuccessMessage(__('admin.success_add_category'));
	}

	public function editCategories($id)
	{

		$categories = Categories::find($id);

		return view('admin.edit-categories')->with('categories', $categories);
	}

	public function updateCategories(Request $request)
	{


		$categories = Categories::findOrFail($request->id);
		$temp       = 'public/temp/'; // Temp
		$path       = 'public/img-category/'; // Path General

		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
			'name'      => 'required',
			'slug'      => 'required|ascii_only|unique:categories,slug,' . $request->id,
			'thumbnail' => 'mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=457,min_height=359',
		];

		$this->validate($request, $rules);

		if ($request->hasFile('thumbnail')) {

			$extension        = $request->file('thumbnail')->getClientOriginalExtension();
			$type_mime_shot   = $request->file('thumbnail')->getMimeType();
			$sizeFile         = $request->file('thumbnail')->getSize();
			$thumbnail        = $request->slug . '-' . str_random(32) . '.' . $extension;

			if ($request->file('thumbnail')->move($temp, $thumbnail)) {

				$image = Image::make($temp . $thumbnail);

				if ($image->width() == 457 && $image->height() == 359) {

					\File::copy($temp . $thumbnail, $path . $thumbnail);
					\File::delete($temp . $thumbnail);
				} else {
					$image->fit(457, 359)->save($temp . $thumbnail);

					\File::copy($temp . $thumbnail, $path . $thumbnail);
					\File::delete($temp . $thumbnail);
				}

				// Delete Old Image
				\File::delete($path . $categories->thumbnail);
			} // End File
		} // HasFile
		else {
			$thumbnail = $categories->thumbnail;
		}

		// UPDATE CATEGORY
		$categories->name       = $request->name;
		$categories->slug       = strtolower($request->slug);
		$categories->page_title = $request->page_title;
		$categories->seo_title  = $request->seo_title;
		$categories->keywords   = $request->keywords;
		$categories->description = $request->description;
		$categories->thumbnail  = $thumbnail;
		$categories->mode       = $request->mode ?? 'off';
		$categories->save();

		return redirect('panel/admin/categories')->withSuccessMessage(__('misc.success_update'));
	}

	public function deleteCategories($id)
	{
		$categories = Categories::find($id);
		$thumbnail  = 'public/img-category/' . $categories->thumbnail; // Path General

		if (!isset($categories) || $categories->id == 1) {
			return redirect('panel/admin/categories');
		} else {

			$images_category   = Images::where('categories_id', $id)->get();

			// Delete Category
			$categories->delete();

			// Delete Thumbnail
			if (\File::exists($thumbnail)) {
				\File::delete($thumbnail);
			} //<--- IF FILE EXISTS

			//Update Categories Images
			if (isset($images_category)) {
				foreach ($images_category as $key) {
					$key->categories_id = 1;
					$key->save();
				}
			}

			return redirect('panel/admin/categories');
		}
	}

	public function settings()
	{

		return view('admin.settings');
	}

	public function saveSettings(Request $request)
	{
		Validator::extend('sell_option_validate', function ($attribute, $value, $parameters) {
			// Count images for sale
			$imagesForSale = Images::where('item_for_sale', 'sale')->where('status', 'active')->count();

			if ($value == 'off' && $imagesForSale > 0) {
				return false;
			}
			return true;
		});

		if ($request->captcha && !config('captcha.sitekey') && !config('captcha.secret')) {
			return back()->withErrors(['error' => __('misc.error_active_captcha')]);
		}

		$messages = [
			'sell_option.sell_option_validate' => trans('misc.sell_option_validate')
		];

		$rules = array(
			'title'        => 'required',
			'link_terms'   => 'required|url',
			'link_privacy' => 'required|url',
			'link_license' => 'url',
			'link_blog'    => 'url',
			'sell_option'  => 'sell_option_validate'
		);

		$this->validate($request, $rules, $messages);

		$sql                      = AdminSettings::first();
		$sql->title               = $request->title;
		$sql->link_terms          = $request->link_terms;
		$sql->link_privacy        = $request->link_privacy;
		$sql->link_license        = $request->link_license;
		$sql->link_blog           = $request->link_blog;
		$sql->captcha             = $request->captcha ?? 'off';
		$sql->registration_active = $request->registration_active ?? '0';
		$sql->email_verification  = $request->email_verification ?? '0';
		$sql->google_ads_index    = $request->google_ads_index ?? 'off';
		$sql->referral_system    = $request->referral_system ?? 'off';
		$sql->comments            = $request->comments ?? 'off';
		$sql->sell_option         = $request->sell_option;
		$sql->who_can_sell        = $request->who_can_sell;
		$sql->who_can_upload      = $request->who_can_upload;
		$sql->free_photo_upload   = $request->free_photo_upload ?? 'off';
		$sql->show_counter        = $request->show_counter ?? 'off';
		$sql->show_categories_index = $request->show_categories_index ?? 'off';
		$sql->show_images_index    = $request->show_images_index;
		$sql->theme                = $request->theme;
		$sql->show_watermark       = $request->show_watermark ?? '0';
		$sql->lightbox             = $request->lightbox ?? 'off';
		$sql->banner_cookies       = $request->banner_cookies ?? false;
		if ($request->has('ai_models')) {
			$sql->ai_models        = $request->ai_models;
		}
		$sql->save();

		// Default locale
		Helper::envUpdate('DEFAULT_LOCALE', $request->default_language);

		// App Name
		Helper::envUpdate('APP_NAME', ' "' . $request->title . '" ', true);

		if ($this->settings->who_can_upload == 'all' && $request->who_can_upload == 'admin') {
			User::where('role', '<>', 1)->update([
				'authorized_to_upload' => 'no'
			]);
		} elseif ($this->settings->who_can_upload == 'admin' && $request->who_can_upload == 'all') {
			User::where('role', '<>', 1)->update([
				'authorized_to_upload' => 'yes'
			]);
		}

		return redirect('panel/admin/settings')->withSuccessMessage(__('admin.success_update'));
	}

	public function settingsLimits()
	{
		return view('admin.limits');
	}

	public function saveSettingsLimits(Request $request)
	{


		$sql                      = AdminSettings::first();
		$sql->result_request      = $request->result_request;
		$sql->limit_upload_user   = $request->limit_upload_user;
		$sql->daily_limit_downloads = $request->daily_limit_downloads;
		$sql->title_length        = $request->title_length;
		$sql->message_length      = $request->message_length;
		$sql->comment_length      = $request->comment_length;
		$sql->file_size_allowed   = $request->file_size_allowed;
		$sql->auto_approve_images = $request->auto_approve_images;
		$sql->downloads           = $request->downloads;
		$sql->tags_limit          = $request->tags_limit;
		$sql->description_length  = $request->description_length;
		$sql->min_width_height_image = $request->min_width_height_image;
		$sql->file_size_allowed_vector = $request->file_size_allowed_vector;

		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));

		return redirect('panel/admin/settings/limits');
	}

	public function members_reported()
	{

		$data = UsersReported::orderBy('id', 'DESC')->get();

		return view('admin.members_reported')->withData($data);
	}

	public function delete_members_reported(Request $request)
	{

		$report = UsersReported::find($request->id);

		if (isset($report)) {
			$report->delete();
		}

		return redirect('panel/admin/members-reported');
	}

	public function images_reported()
	{

		$data = ImagesReported::orderBy('id', 'DESC')->get();

		//dd($data);

		return view('admin.images_reported')->withData($data);
	}

	public function delete_images_reported(Request $request)
	{

		$report = ImagesReported::find($request->id);

		if (isset($report)) {
			$report->delete();
		}

		return redirect('panel/admin/images-reported');
	}

	public function images()
	{
		$query = request()->get('q');
		$sort = request()->get('sort');
		$pagination = 15;

		$data = Images::orderBy('id', 'desc')->paginate($pagination);

		// Search
		if (isset($query)) {
			$data = Images::where('title', 'LIKE', '%' . $query . '%')
				->orWhere('tags', 'LIKE', '%' . $query . '%')
				->orderBy('id', 'desc')->paginate($pagination);
		}

		// Sort
		if (isset($sort) && $sort == 'title') {
			$data = Images::orderBy('title', 'asc')->paginate($pagination);
		}

		if (isset($sort) && $sort == 'pending') {
			$data = Images::where('status', 'pending')->paginate($pagination);
		}

		if (isset($sort) && $sort == 'downloads') {
			$data = Images::join('downloads', 'images.id', '=', 'downloads.images_id')
				->groupBy('downloads.images_id')
				->orderBy(\DB::raw('COUNT(downloads.images_id)'), 'desc')
				->select('images.*')
				->paginate($pagination);
		}

		if (isset($sort) && $sort == 'likes') {
			$data = Images::join('likes', function ($join) {
				$join->on('likes.images_id', '=', 'images.id')->where('likes.status', '=', '1');
			})
				->groupBy('likes.images_id')
				->orderBy(\DB::raw('COUNT(likes.images_id)'), 'desc')
				->select('images.*')
				->paginate($pagination);
		}

		if (isset($sort) && $sort == 'featured') {
			$data = Images::where('featured', 'yes')->orderBy('id', 'desc')->paginate($pagination);
		}

		return view('admin.images', ['data' => $data, 'query' => $query, 'sort' => $sort]);
	}

	public function delete_image(Request $request)
	{

		//<<<<---------------------------------------------

		$image = Images::find($request->id);

		// Delete Notification
		$notifications = Notifications::where('destination', $request->id)
			->where('type', '2')
			->orWhere('destination', $request->id)
			->where('type', '3')
			->orWhere('destination', $request->id)
			->where('type', '6')
			->get();

		if (isset($notifications)) {
			foreach ($notifications as $notification) {
				$notification->delete();
			}
		}

		// Collections Images
		$collectionsImages = CollectionsImages::where('images_id', '=', $request->id)->get();
		if (isset($collectionsImages)) {
			foreach ($collectionsImages as $collectionsImage) {
				$collectionsImage->delete();
			}
		}

		// Images Reported
		$imagesReporteds = ImagesReported::where('image_id', '=', $request->id)->get();
		if (isset($imagesReporteds)) {
			foreach ($imagesReporteds as $imagesReported) {
				$imagesReported->delete();
			}
		}

		//<---- ALL RESOLUTIONS IMAGES
		$stocks = Stock::where('images_id', '=', $request->id)->get();

		foreach ($stocks as $stock) {

			// Delete Stock
			Storage::delete(config('path.uploads') . $stock->type . '/' . $stock->name);

			// Delete Stock Vector
			Storage::delete(config('path.files') . $stock->name);

			$stock->delete();
		} //<--- End foreach

		// Delete preview
		Storage::delete(config('path.preview') . $image->preview);

		// Delete thumbnail
		Storage::delete(config('path.thumbnail') . $image->thumbnail);

		$image->delete();

		return redirect('panel/admin/images');
	}

	public function edit_image($id)
	{
		$data = Images::findOrFail($id);

		return view('admin.edit-image', ['data' => $data]);
	}

	public function update_image(Request $request)
	{
		$sql = Images::find($request->id);

		$rules = [
			'title'            => 'required|min:3|max:' . $this->settings->title_length,
			'tags'             => 'required',
			'meta_title'       => 'nullable|string|max:255',
			'meta_description' => 'nullable|string|max:500',
			'meta_keywords'    => 'nullable|string|max:255',
		];

		if ($request->featured && $sql->featured == 'no') {
			$featuredDate = \Carbon\Carbon::now();
		} elseif ($request->featured && $sql->featured == 'yes') {
			$featuredDate = $sql->featured_date;
		} else {
			$featuredDate = '';
		}

		$this->validate($request, $rules);

		$sql->title            = $request->title;
		$sql->slug             = Helper::createImageSlug($request->slug ?: $request->title, $sql->id);
		$sql->meta_title       = $request->meta_title ? trim($request->meta_title) : null;
		$sql->meta_description = $request->meta_description ? trim($request->meta_description) : null;
		$sql->meta_keywords    = $request->meta_keywords ? trim($request->meta_keywords) : null;
		$sql->tags             = $request->tags;
		$sql->description   = $request->description;
		$sql->categories_id = $request->categories_id;
		$sql->subcategories_id = $request->subcategories_id;
		$sql->status        = $request->status ?? 'pending';
		$sql->featured      = $request->featured ?? 'no';
		$sql->featured_date = $featuredDate;
		$sql->save();

		return redirect('panel/admin/images')->withSuccessMessage(__('admin.success_update'));
	}

	public function profiles_social()
	{
		return view('admin.profiles-social');
	}

	public function update_profiles_social(Request $request)
	{
		$sql = AdminSettings::find(1);

		$rules = array(
			'twitter'    => 'url',
			'facebook'   => 'url',
			'linkedin'   => 'url',
			'instagram'  => 'url',
			'youtube'  => 'url',
			'pinterest'  => 'url',
		);

		$this->validate($request, $rules);

		$sql->twitter       = $request->twitter;
		$sql->facebook      = $request->facebook;
		$sql->linkedin      = $request->linkedin;
		$sql->instagram     = $request->instagram;
		$sql->youtube     = $request->youtube;
		$sql->pinterest     = $request->pinterest;

		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));

		return redirect('panel/admin/profiles-social');
	}

	public function google()
	{
		return view('admin.google');
	}

	public function update_google(Request $request)
	{
		$sql = AdminSettings::first();

		$sql->google_adsense_index = $request->google_adsense_index;
		$sql->google_adsense   = $request->google_adsense;
		$sql->google_analytics = $request->google_analytics;
		$sql->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);
		}

		return redirect('panel/admin/google')->withSuccessMessage(__('admin.success_update'));
	}

	public function theme()
	{
		return view('admin.theme');
	}

	public function themeStore(Request $request)
	{
		$temp  = 'public/temp/'; // Temp
		$path  = 'public/img/'; // Path
		$pathAvatar = config('path.avatar');
		$pathCover = config('path.cover');
		$pathCategory = 'public/img-category/'; // Path Category

		$rules = [
			'logo'   => 'mimes:png',
			'logo_light' => 'mimes:png',
			'favicon'   => 'mimes:png',
			'image_header'   => 'mimes:jpg,jpeg',
			'img_section'   => 'mimes:jpg,jpeg,png',
		];

		$this->validate($request, $rules);

		//========== LOGO
		if ($request->hasFile('logo')) {

			$extension = $request->file('logo')->getClientOriginalExtension();
			$file      = 'logo-' . time() . '.' . $extension;

			if ($request->file('logo')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->logo);
			} // End File

			$this->settings->logo = $file;
			$this->settings->save();
		} // HasFile

		//========== LOGO
		if ($request->hasFile('logo_light')) {

			$extension = $request->file('logo_light')->getClientOriginalExtension();
			$file      = 'logo_light-' . time() . '.' . $extension;

			if ($request->file('logo_light')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->logo_light);
			} // End File

			$this->settings->logo_light = $file;
			$this->settings->save();
		} // HasFile

		//======== FAVICON
		if ($request->hasFile('favicon')) {

			$extension  = $request->file('favicon')->getClientOriginalExtension();
			$file       = 'favicon-' . time() . '.' . $extension;

			if ($request->file('favicon')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->favicon);
			} // End File

			$this->settings->favicon = $file;
			$this->settings->save();
		} // HasFile

		//======== image_header
		if ($request->hasFile('image_header')) {

			$extension  = $request->file('image_header')->getClientOriginalExtension();
			$file       = 'header_index-' . time() . '.' . $extension;

			if ($request->file('image_header')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->image_header);
			} // End File

			$this->settings->image_header = $file;
			$this->settings->save();
		} // HasFile

		//======== img_section
		if ($request->hasFile('img_section')) {

			$extension  = $request->file('img_section')->getClientOriginalExtension();
			$file       = 'img_section-' . time() . '.' . $extension;

			if ($request->file('img_section')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->img_section);
			} // End File

			$this->settings->img_section = $file;
			$this->settings->save();
		} // HasFile

		//======== Watermark
		if ($request->hasFile('watermark')) {

			$extension  = $request->file('watermark')->getClientOriginalExtension();
			$file       = 'watermark-' . time() . '.' . $extension;

			if ($request->file('watermark')->move($temp, $file)) {
				\File::copy($temp . $file, $path . $file);
				\File::delete($temp . $file);
				\File::delete($path . $this->settings->watermark);
			} // End File

			$this->settings->watermark = $file;
			$this->settings->save();
		} // HasFile

		//======== avatar
		if ($request->hasFile('avatar')) {

			$extension  = $request->file('avatar')->getClientOriginalExtension();
			$file       = 'default-' . time() . '.' . $extension;

			$imgAvatar  = Image::make($request->file('avatar'))->fit(180, 180, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})->encode($extension);

			// Copy folder
			Storage::put($pathAvatar . $file, $imgAvatar, 'public');

			// Update Avatar all users
			User::where('avatar', $this->settings->avatar)->update([
				'avatar' => $file
			]);

			// Delete old Avatar
			Storage::delete(config('path.avatar') . $this->settings->avatar);

			$this->settings->avatar = $file;
			$this->settings->save();
		} // HasFile

		//======== cover
		if ($request->hasFile('cover')) {

			$extension  = $request->file('cover')->getClientOriginalExtension();
			$file       = 'cover-' . time() . '.' . $extension;

			// Copy folder
			$request->file('cover')->storePubliclyAs($pathCover, $file);

			// Update Avatar all users
			User::where('cover', $this->settings->cover)->update([
				'cover' => $file
			]);

			// Delete old Avatar
			Storage::delete(config('path.cover') . $this->settings->cover);

			$this->settings->cover = $file;
			$this->settings->save();
		} // HasFile

		//======== img_category
		if ($request->hasFile('img_category')) {

			$extension  = $request->file('img_category')->getClientOriginalExtension();
			$file       = 'default-' . time() . '.' . $extension;

			if ($request->file('img_category')->move($temp, $file)) {

				$image = Image::make($temp . $file);

				$image->fit(457, 359)->save($temp . $file);

				\File::copy($temp . $file, $pathCategory . $file);
				\File::delete($temp . $file);
				\File::delete($pathCategory . $this->settings->img_category);
			} // End File

			$this->settings->img_category = $file;
			$this->settings->save();
		} // HasFile

		// Update Color Default, and Button style
		$this->settings->whereId(1)
			->update([
				'color_default' => $request->color_default
			]);

		//======= CLEAN CACHE
		\Artisan::call('cache:clear');

		return redirect('panel/admin/theme')
			->withSuccessMessage(__('misc.success_update'));
	}

	public function payments()
	{
		$stripeConnectCountries = explode(',', $this->settings->stripe_connect_countries);
		return view('admin.payments-settings')->withStripeConnectCountries($stripeConnectCountries);
	}

	public function savePayments(Request $request)
	{

		$sql = AdminSettings::first();

		$messages = [
			'stripe_connect_countries.required' => trans('validation.required', ['attribute' => __('misc.stripe_connect_countries')])
		];

		$rules = [
			'currency_code' => 'required|alpha',
			'currency_symbol' => 'required',
			'stripe_connect_countries' => Rule::requiredIf($request->stripe_connect == 1)
		];

		$this->validate($request, $rules, $messages);

		if (isset($request->stripe_connect_countries)) {
			$stripeConnectCountries = implode(',', $request->stripe_connect_countries);
		}

		$sql->currency_symbol  = $request->currency_symbol;
		$sql->currency_code    = strtoupper($request->currency_code);
		$sql->currency_position    = $request->currency_position;
		$sql->default_price_photos   = $request->default_price_photos;
		$sql->extended_license_price   = $request->extended_license_price;
		$sql->min_sale_amount   = $request->min_sale_amount;
		$sql->max_sale_amount   = $request->max_sale_amount;
		$sql->min_deposits_amount   = $request->min_deposits_amount;
		$sql->max_deposits_amount   = $request->max_deposits_amount;
		$sql->fee_commission        = $request->fee_commission;
		$sql->fee_commission_non_exclusive = $request->fee_commission_non_exclusive;
		$sql->percentage_referred  = $request->percentage_referred;
		$sql->referral_transaction_limit  = $request->referral_transaction_limit;
		$sql->amount_min_withdrawal    = $request->amount_min_withdrawal;
		$sql->decimal_format = $request->decimal_format;
		$sql->payout_method_paypal = $request->payout_method_paypal;
		$sql->payout_method_bank = $request->payout_method_bank;
		$sql->stripe_connect = $request->stripe_connect;
		$sql->tax_on_wallet = $request->tax_on_wallet;
		$sql->stripe_connect_countries = $stripeConnectCountries ?? null;

		$sql->save();

		\Session::flash('success_message', trans('admin.success_update'));

		return redirect('panel/admin/payments');
	}

	public function purchases()
	{
		$data = Purchases::with(['images', 'invoice'])->whereApproved('1')->orderBy('id', 'desc')->paginate(30);
		return view('admin.purchases')->withData($data);
	}

	public function deposits()
	{
		$data = Deposits::orderBy('id', 'desc')->paginate(30);
		return view('admin.deposits')->withData($data);
	}

	public function depositsView($id)
	{
		$data = Deposits::findOrFail($id);
		return view('admin.deposits-view')->withData($data);
	}

	public function approveDeposits(Request $request)
	{
		$query = Deposits::with(['invoicePending'])->findOrFail($request->id);

		$data = [
			'type' => 'approve',
			'amount' => Helper::amountFormat($query->amount),
			'name' => $query->user()->name
		];

		// Send Mail to User
		try {
			$query->user()->notify(new DepositVerification($data));
		} catch (\Exception $e) {
			return back()->withErrors([
				'errors' => $e->getMessage(),
			]);
		}

		$query->status = 'active';
		$query->save();

		// Add Funds to User
		$query->user()->increment('funds', $query->amount);

		// Update Invoice
		$query->invoicePending->update([
			'status' => 'paid'
		]);

		return redirect('panel/admin/deposits');
	}

	public function deleteDeposits(Request $request)
	{
		$path = config('path.admin');
		$query = Deposits::with(['invoicePending'])->findOrFail($request->id);

		if (isset($query->user()->name)) {
			$data = [
				'type' => 'not_approve',
				'amount' => Helper::amountFormat($query->amount),
				'name' => $query->user()->name
			];

			// Send Mail to User
			try {
				$query->user()->notify(new DepositVerification($data));
			} catch (\Exception $e) {
				return back()->withErrors([
					'errors' => $e->getMessage(),
				]);
			}
		}

		// Delete Image
		Storage::delete($path . $query->screenshot_transfer);

		// Delete Invoice
		$query->invoicePending->delete();

		$query->delete();

		return redirect('panel/admin/deposits');
	}

	public function withdrawals()
	{
		$data = Withdrawals::orderBy('id', 'DESC')->paginate(50);
		return view('admin.withdrawals', ['data' => $data, 'settings' => $this->settings]);
	}

	public function withdrawalsView($id)
	{
		$data = Withdrawals::findOrFail($id);
		return view('admin.withdrawal-view', ['data' => $data, 'settings' => $this->settings]);
	}

	public function withdrawalsPaid(Request $request)
	{
		$data = Withdrawals::findOrFail($request->id);

		// Set Withdrawal as Paid
		$data->status    = 'paid';
		$data->date_paid = \Carbon\Carbon::now();
		$data->save();

		$user = $data->user();

		// Set Balance a zero
		$user->balance = 0;
		$user->save();

		//<------ Send Email to User ---------->>>
		$amount       = Helper::amountFormatDecimal($data->amount) . ' ' . $this->settings->currency_code;
		$sender       = $this->settings->email_no_reply;
		$titleSite    = $this->settings->title;
		$fullNameUser = $user->name ? $user->name : $user->username;
		$_emailUser   = $user->email;

		Mail::send(
			'emails.withdrawal-processed',
			[
				'amount'     => $amount,
				'fullname'   => $fullNameUser
			],
			function ($message) use ($sender, $fullNameUser, $titleSite, $_emailUser) {
				$message->from($sender, $titleSite)
					->to($_emailUser, $fullNameUser)
					->subject(trans('misc.withdrawal_processed') . ' - ' . $titleSite);
			}
		);
		//<------ Send Email to User ---------->>>

		return redirect('panel/admin/withdrawals');
	}

	public function paymentsGateways($id)
	{
		$data = PaymentGateways::findOrFail($id);
		$name = ucfirst($data->name);

		return view('admin.' . str_slug($name) . '-settings')->withData($data);
	}

	public function savePaymentsGateways($id, Request $request)
	{

		$data = PaymentGateways::findOrFail($id);

		$input = $_POST;

		// Sandbox
		if (!$request->sandbox || $request->sandbox === 'off' || $request->sandbox === 'false') {
			$input['sandbox'] = 'false';
		} else {
			$input['sandbox'] = 'true';
		}

		// Enabled off
		if (!$request->enabled) {
			$input['enabled'] = '0';
		} else {
			$input['enabled'] = '1';
		}

		// Subscription off
		if (!$request->subscription) {
			$input['subscription'] = 0;
		} else {
			$input['subscription'] = 1;
		}

		$this->validate($request, [
			'email'    => 'email',
		]);

		$data->fill($input)->save();

		// Set Stripe Keys
		if ($data->name == 'Stripe') {
			Helper::envUpdate('STRIPE_KEY', $input['key']);
			Helper::envUpdate('STRIPE_SECRET', $input['key_secret']);
			Helper::envUpdate('STRIPE_WEBHOOK_SECRET', $input['webhook_secret']);
		}

		// Set PayPal Keys on .env file
		if ($data->name == 'PayPal') {
			if (!$request->sandbox) {
				Helper::envUpdate('PAYPAL_MODE', 'live');
				Helper::envUpdate('PAYPAL_LIVE_CLIENT_ID', $input['key']);
				Helper::envUpdate('PAYPAL_LIVE_CLIENT_SECRET', $input['key_secret']);
			} else {
				Helper::envUpdate('PAYPAL_MODE', 'sandbox');
				Helper::envUpdate('PAYPAL_SANDBOX_CLIENT_ID', $input['key']);
				Helper::envUpdate('PAYPAL_SANDBOX_CLIENT_SECRET', $input['key_secret']);
			}

			Helper::envUpdate('PAYPAL_WEBHOOK_ID', $input['webhook_secret']);
		} // PayPal

		// Set Paystack Keys
		if ($data->name == 'Paystack') {
			Helper::envUpdate('PAYSTACK_PUBLIC_KEY', $input['key']);
			Helper::envUpdate('PAYSTACK_SECRET_KEY', $input['key_secret']);
			Helper::envUpdate('MERCHANT_EMAIL', $input['email']);
		}

		// Set Flutterwave Keys
		if ($data->name == 'Flutterwave') {
			Helper::envUpdate('FLW_PUBLIC_KEY', $input['key']);
			Helper::envUpdate('FLW_SECRET_KEY', $input['key_secret']);
		}

		// Set Razorpay Keys
		if ($data->name == 'Razorpay') {
			Helper::envUpdate('RAZORPAY_KEY', $input['key']);
			Helper::envUpdate('RAZORPAY_SECRET', $input['key_secret']);
			if (isset($input['webhook_secret'])) {
				Helper::envUpdate('RAZORPAY_WEBHOOK_SECRET', $input['webhook_secret']);
			}
		}

		return back()->withSuccessMessage(__('admin.success_update'));
	}

	public function maintenance(Request $request)
	{
		$strRandom = str_random(50);

		if ($request->maintenance_mode) {
			\Artisan::call('down', [
				'--secret' => $strRandom
			]);
		} elseif (!$request->maintenance_mode) {
			\Artisan::call('up');
		}

		$this->settings->maintenance_mode = $request->maintenance_mode;
		$this->settings->save();

		if ($request->maintenance_mode) {
			return redirect($strRandom)
				->withSuccessMessage(trans('misc.maintenance_mode_on'));
		} else {
			return redirect('panel/admin/maintenance')
				->withSuccessMessage(trans('misc.maintenance_mode_off'));
		}
	}

	public function billingStore(Request $request)
	{
		$this->settings->company = $request->company;
		$this->settings->country = $request->country;
		$this->settings->address = $request->address;
		$this->settings->city = $request->city;
		$this->settings->zip = $request->zip;
		$this->settings->vat = $request->vat;
		$this->settings->save();

		return back()->withSuccessMessage(trans('admin.success_update'));
	}

	public function emailSettings(Request $request)
	{
		$request->validate([
			'MAIL_FROM_ADDRESS' => 'required'
		]);

		$request->MAIL_ENCRYPTION = strtolower($request->MAIL_ENCRYPTION);

		$this->settings->email_admin = $request->email_admin;
		$this->settings->email_no_reply = $request->MAIL_FROM_ADDRESS;
		$this->settings->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);
		}

		return back()->withSuccessMessage(trans('admin.success_update'));
	} // End Method

	public function storage(Request $request)
	{
		$messages = [
			'APP_URL.required' => trans('validation.required', ['attribute' => 'App URL']),
			'APP_URL.url' => trans('validation.url', ['attribute' => 'App URL'])
		];

		$request->validate([
			'APP_URL' => 'required|url',
			'AWS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,s3',
			'AWS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,s3',
			'AWS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,s3',
			'AWS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,s3',

			'DOS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
			'DOS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
			'DOS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,dospace',
			'DOS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,dospace',

			'WAS_ACCESS_KEY_ID' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
			'WAS_SECRET_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
			'WAS_DEFAULT_REGION' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',
			'WAS_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,wasabi',

			'VULTR_ACCESS_KEY' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
			'VULTR_SECRET_KEY' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
			'VULTR_REGION' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
			'VULTR_BUCKET' => 'required_if:FILESYSTEM_DRIVER,==,vultr',
		], $messages);

		foreach ($request->except(['_token']) as $key => $value) {

			if ($value == $request->APP_URL) {
				$value = trim($value, '/');
			}

			Helper::envUpdate($key, $value);
		}

		return back()->withSuccessMessage(trans('admin.success_update'));
	} // End Method

	public function updateSocialLogin(Request $request)
	{
		$this->settings->facebook_login = $request->facebook_login ?? 'off';
		$this->settings->google_login   = $request->google_login ?? 'off';
		$this->settings->twitter_login  = $request->twitter_login ?? 'off';
		$this->settings->save();

		foreach ($request->except(['_token']) as $key => $value) {
			Helper::envUpdate($key, $value);
		}

		\Session::flash('success_message', trans('admin.success_update'));
		return back();
	}

	public function pwa(Request $request)
	{
		$allImgs = $request->file('files');

		if ($allImgs) {
			foreach ($allImgs as $key => $file) {

				$filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
				$file->move(public_path('images/icons'), $filename);

				\File::delete(env($key));

				$envIcon = 'public/images/icons/' . $filename;
				Helper::envUpdate($key, $envIcon);
			}
		}

		// Updaye Short Name
		Helper::envUpdate('PWA_SHORT_NAME', ' "' . $request->PWA_SHORT_NAME . '" ', true);

		$sql = $this->settings;
		$sql->status_pwa = $request->status_pwa;
		$sql->save();

		\Artisan::call('cache:clear');
		\Artisan::call('view:clear');

		return back()->withSuccessMessage(trans('admin.success_update'));
	}

	public function subscriptions(Request $request)
	{
		$query = Subscriptions::with(['user.countryRelation', 'invoice', 'plan'])->orderBy('id', 'DESC');

		// Filter by search query (username, name, email, transaction IDs)
		if ($request->filled('q')) {
			$q = trim($request->q);
			$query->where(function ($sq) use ($q) {
				$sq->where('id', $q)
					->orWhere('last_payment', 'like', "%{$q}%")
					->orWhere('stripe_id', 'like', "%{$q}%")
					->orWhere('paypal_id', 'like', "%{$q}%")
					->orWhere('gateway_order_id', 'like', "%{$q}%")
					->orWhereHas('user', function ($uq) use ($q) {
						$uq->where('username', 'like', "%{$q}%")
							->orWhere('name', 'like', "%{$q}%")
							->orWhere('email', 'like', "%{$q}%");
					});
			});
		}

		// Filter by status
		if ($request->filled('status')) {
			$now = now();
			switch ($request->status) {
				case 'active':
					$query->where('ends_at', '>=', $now)
						->where('cancelled', 'no')
						->where(function ($sq) {
							$sq->whereNull('stripe_status')->orWhere('stripe_status', 'active');
						});
					break;
				case 'expiring_soon':
					$query->where('ends_at', '>=', $now)
						->where('ends_at', '<=', $now->copy()->addDays(3))
						->where('cancelled', 'no');
					break;
				case 'cancelled':
					$query->where('cancelled', 'yes');
					break;
				case 'expired':
					$query->where('ends_at', '<', $now);
					break;
			}
		}

		// Filter by gateway
		if ($request->filled('gateway')) {
			$query->where('payment_gateway', $request->gateway);
		}

		// Filter by country
		if ($request->filled('country')) {
			$country = strtoupper(trim($request->country));
			$query->where(function ($sq) use ($country) {
				$sq->where('country_code', $country)
					->orWhereHas('user.countryRelation', function ($cq) use ($country) {
						$cq->where('country_code', $country);
					});
			});
		}

		// Filter by interval
		if ($request->filled('interval')) {
			$query->where('interval', $request->interval);
		}

		// Summary statistics
		$now = now();
		$stats = [
			'total' => Subscriptions::count(),
			'active' => Subscriptions::where('ends_at', '>=', $now)->where('cancelled', 'no')->count(),
			'expiring_soon' => Subscriptions::where('ends_at', '>=', $now)->where('ends_at', '<=', $now->copy()->addDays(3))->where('cancelled', 'no')->count(),
			'expired' => Subscriptions::where('ends_at', '<', $now)->count(),
			'cancelled' => Subscriptions::where('cancelled', 'yes')->count(),
			'revenue_inr' => Subscriptions::where('currency', 'INR')->sum('amount'),
			'revenue_usd' => Subscriptions::where('currency', 'USD')->sum('amount'),
		];

		$subscriptions = $query->paginate(30)->appends($request->except('page'));

		return view('admin.subscriptions', compact('subscriptions', 'stats'));
	}

	public function subscriptionDetail($id)
	{
		$subscription = Subscriptions::with(['user.countryRelation', 'invoice', 'plan'])->findOrFail($id);

		if (request()->ajax() || request()->wantsJson()) {
			return view('admin.subscription-detail-modal', compact('subscription'))->render();
		}

		return view('admin.subscription-detail-modal', compact('subscription'));
	}

	public function adminCancelSubscription($id)
	{
		$subscription = Subscriptions::findOrFail($id);
		$subscription->cancelled = 'yes';
		$subscription->cancelled_at = now();
		$subscription->rebill_wallet = 'off';
		$subscription->save();

		return redirect()->back()->withSuccess(__('misc.subscription_canceled_success'));
	}

	public function collections()
	{
		$data = Collections::with('collectionImages')
			->with('creator')
			->orderBy('id', 'DESC')->paginate(30);

		return view('admin.collections')->withData($data);
	}

	public function deleteCollection(Request $request)
	{
		$collection = Collections::findOrFail($request->id);

		// Delete images on collection
		CollectionsImages::whereCollectionsId($collection->id)->delete();

		$collection->delete();

		return redirect('panel/admin/collections');
	}

	public function clearCache()
	{
		// Clear Cache, Config and Views
		\Artisan::call('cache:clear');
		\Artisan::call('config:clear');
		\Artisan::call('view:clear');

		$pathLogFile = storage_path("logs" . DIRECTORY_SEPARATOR . "laravel.log");

		try {
			collect(Storage::disk('default')->listContents('.cache', true))
				->each(function ($file) {
					Storage::disk('default')->deleteDirectory($file['path']);
					Storage::disk('default')->delete($file['path']);
				});

			// Delete Log file
			if (auth()->user()->isSuperAdmin()) {
				if (file_exists($pathLogFile)) {
					unlink($pathLogFile);
				}
			}
		} catch (\Exception $e) {
		}

		return redirect('panel/admin/maintenance')
			->withSuccessMessage(trans('admin.successfully_cleaned'));
	} // End method

	public function customCssJs(Request $request)
	{
		$sql = $this->settings;
		$sql->custom_css = $request->custom_css;
		$sql->custom_js = $request->custom_js;
		$sql->save();

		return back()->withSuccessMessage(trans('admin.success_update'));
	} // End method

	public function storeAnnouncements(Request $request)
	{
		$this->settings->announcement = $request->announcement_content;
		$this->settings->type_announcement = $request->type_announcement;
		$this->settings->announcement_show = $request->announcement_show;
		$this->settings->announcement_cookie = str_random(25);
		$this->settings->save();

		return back()->withSuccessMessage(trans('admin.success_update'));
	} // End method

	public function subcategories()
	{
		$subcategories = Subcategories::with(['category'])->orderBy('name')->paginate(20);
		$totalSubcategoriesCategories = $subcategories->count();

		return view('admin.subcategories')->with([
			'subcategories' => $subcategories,
			'totalSubcategoriesCategories' => $totalSubcategoriesCategories,
		]);
	}

	public function addSubcategories()
	{
		return view('admin.add-subcategories');
	}

	public function storeSubcategories(Request $request)
	{
		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
			'name' => 'required',
			'slug' => 'required|ascii_only|unique:subcategories',
			'category' => 'required',
		];

		$this->validate($request, $rules);

		$sql              = new Subcategories();
		$sql->name        = trim($request->name);
		$sql->slug        = strtolower($request->slug);
		$sql->keywords    = $request->keywords;
		$sql->description = $request->description;
		$sql->category_id = $request->category;
		$sql->mode        = $request->mode ?? 'off';
		$sql->save();

		return redirect('panel/admin/subcategories')
			->withSuccessMessage(__('misc.successfully_added'));
	}

	public function editSubcategories($id)
	{
		$subcategory = Subcategories::find($id);

		return view('admin.edit-subcategories')->with('subcategory', $subcategory);
	}

	public function updateSubcategories(Request $request)
	{
		$subcategory = Subcategories::findOrFail($request->id);

		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$rules = [
			'name'      => 'required',
			'slug'      => 'required|ascii_only|unique:subcategories,slug,' . $request->id,
			'category' => 'required',
		];

		$this->validate($request, $rules);

		// UPDATE CATEGORY
		$subcategory->name       = $request->name;
		$subcategory->slug       = strtolower($request->slug);
		$subcategory->keywords    = $request->keywords;
		$subcategory->description = $request->description;
		$subcategory->category_id  = $request->category;
		$subcategory->mode       = $request->mode ?? 'off';
		$subcategory->save();

		return redirect('panel/admin/subcategories')
			->withSuccessMessage(__('misc.success_update'));
	}

	public function deleteSubcategories($id)
	{
		Subcategories::find($id)->delete();

		return redirect('panel/admin/subcategories')
			->withSuccessMessage(__('misc.successfully_removed'));
	}

	public function savePushNotifications(Request $request)
	{
		$this->settings->push_notification_status  = $request->push_notification_status;
		$this->settings->onesignal_appid           = $request->onesignal_appid;
		$this->settings->onesignal_restapi         = $request->onesignal_restapi;
		$this->settings->save();

		return back()->withSuccessMessage(__('admin.success_update'));
	}

	// ==========================================
	// PHOTOSHOOTS MANAGEMENT
	// ==========================================

	public function photoshoots(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$query = trim($request->get('q'));

		$data = Photoshoot::with(['user', 'category'])
			->when($query, function ($q) use ($query) {
				return $q->where('title', 'LIKE', '%' . $query . '%')
					->orWhere('slug', 'LIKE', '%' . $query . '%');
			})
			->orderBy('id', 'desc')
			->paginate(20);

		return view('admin.photoshoots', compact('data', 'query'));
	}

	public function createPhotoshoot()
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$categories = Categories::where('mode', 'on')->orderBy('name')->get();

		return view('admin.add-photoshoot', compact('categories'));
	}

	public function storePhotoshoot(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$request->validate([
			'title' => 'required|string|min:3|max:255',
			'slug' => 'nullable|string|max:255',
			'meta_title' => 'nullable|string|max:255',
			'meta_description' => 'nullable|string|max:500',
			'meta_keywords' => 'nullable|string|max:255',
		]);

		$title = trim($request->title);
		$requestedSlug = \Illuminate\Support\Str::slug($request->slug ?: $title);
		if (empty($requestedSlug)) {
			$requestedSlug = 'photoshoot-' . time();
		}

		$originalSlug = $requestedSlug;
		$count = 1;
		while (Photoshoot::where('slug', $requestedSlug)->exists()) {
			$requestedSlug = $originalSlug . '-' . $count;
			$count++;
		}

		$metaTitle = trim($request->meta_title ?? '');
		$metaDescription = trim($request->meta_description ?? '');
		$metaKeywords = trim($request->meta_keywords ?? '');

		$photoshoot = Photoshoot::create([
			'uuid' => 'batch_' . uniqid(),
			'title' => $title,
			'slug' => $requestedSlug,
			'description' => trim($request->description ?: ''),
			'user_id' => auth()->id(),
			'categories_id' => $request->categories_id ?: null,
			'prompts_count' => 0,
			'meta_title' => $metaTitle !== '' ? $metaTitle : null,
			'meta_description' => $metaDescription !== '' ? $metaDescription : null,
			'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
		]);

		return redirect('panel/admin/photoshoots/edit/' . $photoshoot->id)
			->withSuccessMessage('Photoshoot created successfully! You can now search and add prompts to this photoshoot set.');
	}

	public function editPhotoshoot($id)
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$data = Photoshoot::with(['user', 'category', 'allImages.user', 'allImages.category'])->findOrFail($id);
		$categories = Categories::where('mode', 'on')->orderBy('name')->get();

		return view('admin.edit-photoshoot', compact('data', 'categories'));
	}

	public function updatePhotoshoot(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$request->validate([
			'id' => 'required|exists:photoshoots,id',
			'title' => 'required|string|min:3|max:255',
			'slug' => 'nullable|string|max:255',
			'meta_title' => 'nullable|string|max:255',
			'meta_description' => 'nullable|string|max:500',
			'meta_keywords' => 'nullable|string|max:255',
		]);

		$photoshoot = Photoshoot::findOrFail($request->id);
		$photoshoot->title = trim($request->title);
		$photoshoot->categories_id = $request->categories_id ?: null;
		if ($request->has('description')) {
			$photoshoot->description = trim($request->description ?: '');
		}

		$metaTitle = trim($request->meta_title ?? '');
		$metaDescription = trim($request->meta_description ?? '');
		$metaKeywords = trim($request->meta_keywords ?? '');

		$photoshoot->meta_title = $metaTitle !== '' ? $metaTitle : null;
		$photoshoot->meta_description = $metaDescription !== '' ? $metaDescription : null;
		$photoshoot->meta_keywords = $metaKeywords !== '' ? $metaKeywords : null;

		// Process Slug
		$requestedSlug = \Illuminate\Support\Str::slug($request->slug ?: $request->title);
		if (empty($requestedSlug)) {
			$requestedSlug = 'photoshoot-' . $photoshoot->id;
		}

		if ($requestedSlug != $photoshoot->slug) {
			$originalSlug = $requestedSlug;
			$count = 1;
			while (Photoshoot::where('slug', $requestedSlug)->where('id', '!=', $photoshoot->id)->exists()) {
				$requestedSlug = $originalSlug . '-' . $count;
				$count++;
			}
			$photoshoot->slug = $requestedSlug;
		}

		$photoshoot->save();

		return back()->withSuccessMessage('Photoshoot updated successfully.');
	}

	public function deletePhotoshoot($id)
	{
		if (!auth()->user()->hasPermission('images')) {
			return view('admin.unauthorized');
		}

		$photoshoot = Photoshoot::findOrFail($id);
		
		// Unlink all images belonging to this photoshoot without deleting the images
		Images::where('photoshoot_id', $photoshoot->id)->update(['photoshoot_id' => null]);
		
		$photoshoot->delete();

		return redirect('panel/admin/photoshoots')
			->withSuccessMessage('Photoshoot deleted successfully. Associated prompts remain in database as standalone prompts.');
	}

	public function searchPromptsForPhotoshoot(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
		}

		$q = trim($request->get('q'));
		if (empty($q)) {
			return response()->json(['success' => true, 'prompts' => []]);
		}

		$prompts = Images::select(['id', 'title', 'slug', 'thumbnail', 'photoshoot_id', 'user_id', 'item_for_sale'])
			->with(['user', 'photoshoot'])
			->where('title', 'LIKE', "%{$q}%")
			->orWhere('slug', 'LIKE', "%{$q}%")
			->orWhere('id', $q)
			->take(15)
			->get()
			->map(function ($img) {
				return [
					'id' => $img->id,
					'title' => $img->title,
					'slug' => $img->slug,
					'thumb_url' => Storage::url(config('path.thumbnail') . $img->thumbnail),
					'photoshoot_id' => $img->photoshoot_id,
					'photoshoot_name' => $img->photoshoot ? $img->photoshoot->title : null,
					'user_name' => $img->user ? $img->user->username : 'Unknown',
					'tier' => $img->item_for_sale == 'sale' ? 'Premium' : 'Free',
				];
			});

		return response()->json(['success' => true, 'prompts' => $prompts]);
	}

	public function addPromptToPhotoshoot(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
		}

		$request->validate([
			'photoshoot_id' => 'required|exists:photoshoots,id',
			'prompt_id' => 'required|exists:images,id',
		]);

		$photoshoot = Photoshoot::findOrFail($request->photoshoot_id);
		$image = Images::findOrFail($request->prompt_id);

		if ($image->photoshoot_id == $photoshoot->id) {
			return response()->json([
				'success' => false,
				'message' => 'This prompt is already in this photoshoot.'
			], 422);
		}

		// If prompt was in another photoshoot, decrement old count
		if ($image->photoshoot_id && $image->photoshoot_id != $photoshoot->id) {
			$oldPs = Photoshoot::find($image->photoshoot_id);
			if ($oldPs && $oldPs->prompts_count > 0) {
				$oldPs->decrement('prompts_count');
			}
		}

		$image->photoshoot_id = $photoshoot->id;
		$image->save();

		$photoshoot->increment('prompts_count');

		return response()->json([
			'success' => true,
			'message' => 'Prompt successfully added to photoshoot.',
			'prompt' => [
				'id' => $image->id,
				'title' => $image->title,
				'slug' => $image->slug,
				'thumb_url' => Storage::url(config('path.thumbnail') . $image->thumbnail),
				'user_name' => $image->user ? $image->user->username : 'Unknown',
				'tier' => $image->item_for_sale == 'sale' ? 'Premium' : 'Free',
				'status' => $image->status,
			]
		]);
	}

	public function removePromptFromPhotoshoot(Request $request)
	{
		if (!auth()->user()->hasPermission('images')) {
			return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
		}

		$request->validate([
			'photoshoot_id' => 'required|exists:photoshoots,id',
			'prompt_id' => 'required|exists:images,id',
		]);

		$photoshoot = Photoshoot::findOrFail($request->photoshoot_id);
		$image = Images::findOrFail($request->prompt_id);

		if ($image->photoshoot_id == $photoshoot->id) {
			$image->photoshoot_id = null;
			$image->save();

			if ($photoshoot->prompts_count > 0) {
				$photoshoot->decrement('prompts_count');
			}
		}

		return response()->json([
			'success' => true,
			'message' => 'Prompt removed from photoshoot successfully. Prompt remains in database as standalone.'
		]);
	}
}
