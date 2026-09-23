<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Like;
use App\Models\Stock;
use App\Models\Images;
use App\Models\Visits;
use App\Models\Downloads;
use App\Models\Followers;
use App\Models\Purchases;
use App\Models\Categories;
use App\Models\Collections;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Notifications;
use App\Models\Subcategories;
use App\Models\ImagesReported;
use App\Models\PaymentGateways;
use League\Glide\ServerFactory;
use App\Models\CollectionsImages;
use App\Notifications\NewSale;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Glide\Responses\LaravelResponseFactory;

class ImagesController extends Controller
{
	use Traits\UploadTrait, Traits\FunctionsTrait;

	protected $settings;
	protected $request;

	public function __construct(AdminSettings $settings, Request $request)
	{
		$this->settings = $settings::first();
		$this->request = $request;
	}

	protected function validatorUpdate(array $data)
	{
		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$sizeAllowed = $this->settings->file_size_allowed * 1024;

		$dimensions = explode('x', $this->settings->min_width_height_image);

		if ($this->settings->currency_position == 'right') {
			$currencyPosition =  2;
		} else {
			$currencyPosition =  null;
		}

		$messages = array(
			'photo.required' => __('misc.please_select_image'),
			"photo.max"   => __('misc.max_size') . ' ' . Helper::formatBytes($sizeAllowed, 1),
			"price.required_if" => __('misc.price_required'),
			'price.min' => __('misc.price_minimum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
			'price.max' => __('misc.price_maximum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),

		);

		// Create Rules
		return Validator::make($data, [
			'title'       => 'required|min:3|max:' . $this->settings->title_length . '',
			'prompt'      => 'required|string|min:5',
			'ai_model'    => 'required|in:' . implode(',', Images::getAiModels()),
			'description' => 'nullable|min:2|max:' . $this->settings->description_length . '',
			'meta_title'       => 'nullable|string|max:255',
			'meta_description' => 'nullable|string|max:500',
			'meta_keywords'    => 'nullable|string|max:255',
			'tags'        => 'required'
		], $messages);
	}

	/**
	 * Upload Section
	 *
	 * @return View
	 */
	public function showUpload()
	{
		if (auth()->user()->authorized_to_upload == 'yes' || auth()->user()->isSuperAdmin()) {
			return view('images.upload');
		} else {
			return redirect('/');
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = Images::all();

		return view('admin.images')->withData($data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($slug)
	{
		$response = Images::with(['author', 'comments', 'category', 'subcategory', 'examples', 'photoshoot'])
			->where('slug', $slug)
			->first();

		if (!$response) {
			if (is_numeric($slug)) {
				$imageById = Images::find($slug);
				if ($imageById) {
					return redirect('prompt/' . $imageById->slug, 301);
				}
			}
			abort(404);
		}

		if (auth()->check() && $response->user_id != auth()->id() && $response->status == 'pending' && !auth()->user()->isSuperAdmin()) {
			abort(404);
		} else if (auth()->guest() && $response->status == 'pending') {
			abort(404);
		}

		$url_image = 'prompt/' . $response->slug;

		//<<<-- * Redirect the user real page * -->>>
		$uriImage     =  $this->request->path();
		$uriCanonical = $url_image;

		if ($uriImage != $uriCanonical) {
			return redirect($uriCanonical);
		}

		//<--------- * Visits * ---------->
		$user_IP = request()->ip();
		$date = time();

		if (auth()->check()) {
			// SELECT IF YOU REGISTERED AND VISITED THE PUBLICATION
			$visitCheckUser = $response->visits()->where('user_id', auth()->id())->first();

			if (!$visitCheckUser && auth()->id() != $response->author->id) {
				$visit = new Visits;
				$visit->images_id = $response->id;
				$visit->user_id  = auth()->id();
				$visit->ip       = $user_IP;
				$visit->save();
			}
		} else {

			// IF YOU SELECT "UNREGISTERED" ALREADY VISITED THE PUBLICATION
			$visitCheckGuest = $response->visits()->where('user_id', 0)
				->where('ip', $user_IP)
				->orderBy('date', 'desc')
				->first();

			if ($visitCheckGuest) {
				$dateGuest = strtotime($visitCheckGuest->date) + (7200); // 2 Hours
			}

			if (empty($visitCheckGuest->ip)) {
				$visit = new Visits();
				$visit->images_id = $response->id;
				$visit->user_id  = 0;
				$visit->ip = $user_IP;
				$visit->save();
			} else if ($dateGuest < $date) {
				$visit = new Visits();
				$visit->images_id = $response->id;
				$visit->user_id  = 0;
				$visit->ip  = $user_IP;
				$visit->save();
			}
		} //<--------- * Visits * ---------->

		\App\Services\AnalyticsService::logEvent('prompt_view', request()->fullUrl(), $response->id, [
			'prompt_title' => $response->title,
			'ai_model' => $response->ai_model,
			'tier' => $response->item_for_sale,
		]);

		if (auth()->check()) {

			// FOLLOW ACTIVE
			$followActive = Followers::where('follower', auth()->id())
				->where('following', $response->author->id)
				->where('status', '1')
				->first();

			if ($followActive) {
				$textFollow   = __('users.following');
				$icoFollow    = '-person-check';
				$activeFollow = 'btnFollowActive';
			} else {
				$textFollow   = __('users.follow');
				$icoFollow    = '-person-plus';
				$activeFollow = '';
			}

			// LIKE ACTIVE
			$likeActive = Like::where('user_id', auth()->id())
				->where('images_id', $response->id)
				->where('status', '1')
				->first();

			if ($likeActive) {
				$textLike   = __('misc.unlike');
				$icoLike    = 'bi bi-heart-fill';
				$statusLike = 'active';
			} else {
				$textLike   = __('misc.like');
				$icoLike    = 'bi bi-heart';
				$statusLike = '';
			}

			// ADD TO COLLECTION
			$collections = Collections::where('user_id', auth()->id())->orderBy('id', 'asc')->get();
		} //<<<<---- *** END AUTH ***

		// Stock image resolutions
		$stockImages = $response->stock;
		$stockSmall  = $response->stock->where('type', 'small')->first() ?: $response->stock->first();

		$resolution = $stockSmall ? explode('x', Helper::resolutionPreview($stockSmall->resolution)) : [800, 600];
		$previewWidth = $resolution[0] ?? 800;
		$previewHeight = $resolution[1] ?? 600;

		// Recommendations (Priority: Same Photoshoot -> Fallback: Similar Tags & Category)
		$photoshootPrompts = collect();
		if ($response->photoshoot_id) {
			$photoshootPrompts = Images::selectFieldsRelation()
				->where('photoshoot_id', $response->photoshoot_id)
				->whereStatus('active')
				->where('id', '<>', $response->id)
				->take(9)
				->get();
		}

		$neededFallback = 9 - $photoshootPrompts->count();
		$fallbackImages = collect();
		if ($neededFallback > 0) {
			$arrayTags  = explode(",", $response->tags);
			$countTags = count($arrayTags);
			$excludeIds = $photoshootPrompts->pluck('id')->push($response->id)->toArray();

			$fallbackImages = Images::selectFieldsRelation()
				->where('categories_id', $response->categories_id)
				->whereStatus('active')
				->whereNotIn('id', $excludeIds)
				->where(function ($query) use ($arrayTags, $countTags) {
					for ($k = 0; $k < $countTags; ++$k) {
						$query->orWhere('tags', 'LIKE', '%' . $arrayTags[$k] . '%');
					}
				})
				->orderByRaw('RAND()')
				->take($neededFallback)
				->get();
		}

		$images = $photoshootPrompts->concat($fallbackImages);

		// Comments
		$comments_sql = $response->comments()->where('status', '1')->orderBy('date', 'desc')->paginate(10);

		// Payments gateways enabled
		$paymentsGatewaysEnabled = PaymentGateways::where('enabled', '1')->count();

		// Item price
		$itemPrice = $this->settings->default_price_photos ?: $response->price;

		Helper::seo()->setEntity($response);

		return view('images.show')->with([
			'response' => $response,
			'textFollow' => $textFollow ?? null,
			'icoFollow' => $icoFollow ?? null,
			'activeFollow' => $activeFollow ?? null,
			'textLike'   => $textLike ?? null,
			'icoLike' => $icoLike ?? null,
			'statusLike' => $statusLike ?? null,
			'collections' => $collections ?? null,
			'stockImages' => $stockImages,
			'previewWidth' => $previewWidth,
			'previewHeight' => $previewHeight,
			'images' => $images,
			'comments_sql' => $comments_sql,
			'paymentsGatewaysEnabled' => $paymentsGatewaysEnabled,
			'itemPrice' => $itemPrice,
			'getSubscription' => auth()->check() ? auth()->user()->getSubscription() : null
		]);
	} //<--- End Method

	public function showLegacyRedirect($id, $slug = null)
	{
		$image = Images::findOrFail($id);
		return redirect('prompt/' . $image->slug, 301);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data = Images::findOrFail($id);
		$categories = Categories::where('mode','on')
			->orderBy('name')
			->get();

		$subcategories = Subcategories::where('mode','on')
		->where('category_id', $data->categories_id)
		->orderBy('name')
		->get();

		$photoshoots = \App\Models\Photoshoot::where('user_id', auth()->id())
			->orderBy('title')
			->get();

		if ($data->user_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
			abort('404');
		}

		return view('images.edit')->with([
			'data' => $data, 
			'categories' => $categories,
			'subcategories' => $subcategories,
			'photoshoots' => $photoshoots,
		]);
	} //<--- End Method

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		$image = Images::findOrFail($request->id);

		if ($image->user_id != auth()->id()) {
			return redirect('/');
		}

		$input = $request->all();

		$input['tags'] = Helper::cleanStr($input['tags']);

		if (strlen($input['tags']) == 1) {
			return redirect()->back()
				->withErrors(__('validation.required', ['attribute' => __('misc.tags')]));
		}

		$tagsLength = explode(',', $input['tags']);

		// Validate length tags
		foreach ($tagsLength as $tag) {
			if (strlen($tag) < 2) {
				return redirect()->back()
					->withErrors(__('misc.error_length_tags'));
			}
		}

		// Validate number of tags
		if (count($tagsLength) > $this->settings->tags_limit && !$image->data_iptc) {
			return redirect()->back()
				->withErrors(__('misc.maximum_tags', ['limit' => $this->settings->tags_limit]));
		}

		$input['item_for_sale'] = $request->item_for_sale ?: 'free';
		$input['price'] = 0;
		$input['attribution_required'] = $request->attribution_required ?? 'no';

		$validator = $this->validatorUpdate($input);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		if ($this->settings->default_price_photos) {
			$input['price'] = $image->price;
		}

		if (!$request->subcategories_id) {
			$input['subcategories_id'] = 0;
		}

		// Handle Photoshoot assignment
		$oldPhotoshootId = $image->photoshoot_id;
		$newPhotoshootId = null;

		if ($request->photoshoot_id == 'new' && !empty(trim($request->photoshoot_title))) {
			$title = trim($request->photoshoot_title);
			$slug = \Illuminate\Support\Str::slug($title);
			$originalSlug = $slug;
			$count = 1;
			while (\App\Models\Photoshoot::where('slug', $slug)->exists()) {
				$slug = $originalSlug . '-' . $count;
				$count++;
			}
			$newPs = \App\Models\Photoshoot::create([
				'uuid' => 'batch_' . uniqid(),
				'title' => $title,
				'slug' => $slug,
				'user_id' => auth()->id(),
				'categories_id' => $request->categories_id ?: null,
			]);
			$newPhotoshootId = $newPs->id;
		} elseif (is_numeric($request->photoshoot_id) && $request->photoshoot_id > 0) {
			$newPhotoshootId = (int) $request->photoshoot_id;
		}

		if ($oldPhotoshootId != $newPhotoshootId) {
			if ($oldPhotoshootId) {
				$oldPs = \App\Models\Photoshoot::find($oldPhotoshootId);
				if ($oldPs && $oldPs->prompts_count > 0) {
					$oldPs->decrement('prompts_count');
				}
			}
			if ($newPhotoshootId) {
				$newPs = \App\Models\Photoshoot::find($newPhotoshootId);
				if ($newPs) {
					$newPs->increment('prompts_count');
				}
			}
		}

		$input['photoshoot_id']    = $newPhotoshootId;
		$input['slug']             = Helper::createImageSlug($request->slug ?: $input['title'], $image->id);
		$input['meta_title']       = $request->meta_title ? trim($request->meta_title) : null;
		$input['meta_description'] = $request->meta_description ? trim($request->meta_description) : null;
		$input['meta_keywords']    = $request->meta_keywords ? trim($request->meta_keywords) : null;

		$image->fill($input)->save();

		// Delete selected example images if requested
		if ($request->has('delete_examples') && is_array($request->delete_examples)) {
			$pathExamples = config('path.examples');
			foreach ($request->delete_examples as $exId) {
				$exItem = \App\Models\ImageExample::where('id', $exId)->where('images_id', $image->id)->first();
				if ($exItem) {
					Storage::delete($pathExamples . $exItem->file);
					$exItem->delete();
				}
			}
		}

		// Handle new example photos upload (max total 5 example images)
		if ($request->hasFile('example_photos')) {
			$currentCount = $image->examples()->count();
			$allowedNew = 5 - $currentCount;
			if ($allowedNew > 0) {
				$newExFiles = array_slice($request->file('example_photos'), 0, $allowedNew);
				$pathExamples = config('path.examples');
				foreach ($newExFiles as $exPhoto) {
					if ($exPhoto && $exPhoto->isValid()) {
						$exExt = strtolower($exPhoto->getClientOriginalExtension());
						$exFileName = strtolower('ex-' . $image->id . '-' . time() . '-' . str_random(15) . '.' . $exExt);

						$exDimensions = getimagesize($exPhoto);
						$exW = $exDimensions[0] ?? 800;

						$exScale = ($exW > 1280) ? 850 / $exW : (850 / $exW);
						$exPreviewWidth = ceil($exW * min(1, $exScale));

						$imgExPreview = \Image::make($exPhoto)->orientate()->resize($exPreviewWidth, null, function ($constraint) {
							$constraint->aspectRatio();
							$constraint->upsize();
						})->encode($exExt);

						Storage::put($pathExamples . $exFileName, $imgExPreview, 'public');

						\App\Models\ImageExample::create([
							'images_id' => $image->id,
							'file' => $exFileName,
						]);
					}
				}
			}
		}

		\Session::flash('success_message', __('admin.success_update'));

		return redirect('edit/photo/' . $image->id);
	} //<--- End Method


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		$image = Images::findOrFail($request->id);

		if ($image->user_id != auth()->id()) {
			return redirect('/');
		}

		// Delete Notification
		$notifications = Notifications::where('destination', $request->id)
			->where('type', '2')
			->orWhere('destination', $request->id)
			->where('type', '3')
			->orWhere('destination', $request->id)
			->where('type', '4')
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

		// Delete example images
		$pathExamples = config('path.examples');
		foreach ($image->examples as $exampleItem) {
			Storage::delete($pathExamples . $exampleItem->file);
			$exampleItem->delete();
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

		return redirect(auth()->user()->username);
	} //<--- End Method

	public function download($token_id)
	{
		$type = $this->request->type ?: 'small';

		$image = Images::where('token_id', $token_id)->firstOrFail();

		if ($image->item_for_sale != 'free') {
			if (auth()->guest() || (auth()->id() != $image->user_id && !auth()->user()->isSuperAdmin())) {
				abort(404);
			}
		}

		// Get stock image
		$getImage = Stock::where('images_id', $image->id)->where('type', '=', $type)->first()
			?: Stock::where('images_id', $image->id)->firstOrFail();

		// Download Check User
		$user_IP = request()->ip();
		$date = time();

		if (auth()->check()) {

			$downloadCheckUser = $image->downloads()->whereUserId(auth()->id())->whereSize($type)->first();
			$dailyDownloads    = auth()->user()->dailyImageDownloadsCount();
			$downloadLimit     = auth()->user()->totalDailyImageDownloadLimit();

			if (
				!$downloadCheckUser
				&& $downloadLimit != 0
				&& $dailyDownloads >= $downloadLimit
				&& auth()->id() != $image->user_id
				&& !auth()->user()->isSuperAdmin()
			) {
				return back()->withError(__('misc.reached_daily_download'));
			}

			if (!$downloadCheckUser && auth()->id() != $image->user_id && !auth()->user()->isSuperAdmin()) {
				$download            = new Downloads();
				$download->images_id = $image->id;
				$download->user_id   = auth()->id();
				$download->ip        = $user_IP;
				$download->type      = auth()->user()->getSubscription() ? 'subscription' : 'free';
				$download->size      = $type;
				$download->action_type = 'download';
				$download->save();
			}
		} // Auth check

		else {

			// IF YOU SELECT "UNREGISTERED" ALREADY DOWNLOAD THE IMAGE
			$downloadCheckUser = $image->downloads()->where('user_id', 0)
				->where('ip', $user_IP)
				->orderBy('date', 'desc')
				->first();

			if ($downloadCheckUser) {
				$dateGuest = strtotime($downloadCheckUser->date) + (7200); // 2 Hours
			}

			if (empty($downloadCheckUser->ip)) {
				$download            = new Downloads;
				$download->images_id = $image->id;
				$download->user_id   = 0;
				$download->ip        = $user_IP;
				$download->save();
			} else if ($dateGuest < $date) {
				$download            = new Downloads;
				$download->images_id = $image->id;
				$download->user_id   = 0;
				$download->ip        = $user_IP;
				$download->save();
			}
		} //<--------- * Visits * ---------->
		//<<<<---/ Download Check User

		if ($type != 'vector') {
			$pathFile = config('path.uploads') . $type . '/' . $getImage->name;
			$resolution = $getImage->resolution;
		} else {
			$pathFile = config('path.files') . $getImage->name;
			$resolution = __('misc.vector_graphic');
		}

		$headers = [
			'Content-Type' => 'image/' . $image->extension,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		return Storage::download($pathFile, $image->title . ' - ' . $resolution . '.' . $getImage->extension, $headers);
	} //<--- End Method

	public function report(Request $request)
	{

		$data = ImagesReported::firstOrNew(['user_id' => auth()->id(), 'image_id' => $request->id]);

		if ($data->exists) {
			\Session::flash('noty_error', 'error');
			return redirect()->back();
		} else {

			$data->reason = $request->reason;
			$data->save();
			\Session::flash('noty_success', 'success');
			return redirect()->back();
		}
	} //<--- End Method

	public function purchase($token_id)
	{
		$type = strtolower($this->request->type);
		$license = strtolower($this->request->license);
		$urlDashboardUser = url('user/dashboard/purchases');

		if (url()->previous() == $urlDashboardUser && !$this->request->downloadAgain) {
			abort(404);
		}

		$image = Images::where('token_id', $token_id)->firstOrFail();

		// Validate Licenses and Type
		$licensesArray = ['regular', 'extended'];
		$typeArray     = ['small', 'medium', 'large', 'vector'];

		// License
		if (!in_array($license, $licensesArray) && auth()->id() != $image->user_id) {
			abort(404);
		}

		// Type
		if (!in_array($type, $typeArray) && auth()->id() != $image->user_id) {
			abort(404);
		}

		$getImage = Stock::where('images_id', $image->id)->where('type', '=', $type)->firstOrFail();

		// Download image from the user's Dashboard
		if ($this->request->downloadAgain) {
			return $this->downloadAgain($image, $getImage);
		}

		if ($type != 'vector') {
			$pathFile = config('path.uploads') . $type . '/' . $getImage->name;
			$resolution = $getImage->resolution;
		} else {
			$pathFile = config('path.files') . $getImage->name;
			$resolution = __('misc.vector_graphic');
		}

		$headers = [
			'Content-Type:' => ' image/' . $image->extension,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		return Storage::download($pathFile, $image->title . ' - ' . $resolution . '.' . $getImage->extension, $headers);
	} //<--- End Method

	public function subscriptionDownload($token_id)
	{
		$type = strtolower($this->request->type ?: 'small');
		$license = strtolower($this->request->license);
		$urlDashboardUser = url('user/dashboard/downloads');

		if (url()->previous() == $urlDashboardUser && !$this->request->downloadAgain) {
			abort(404);
		}

		$image = Images::where('token_id', $token_id)->firstOrFail();

		$getImage = Stock::where('images_id', $image->id)->where('type', '=', $type)->first()
			?: Stock::where('images_id', $image->id)->firstOrFail();

		$downloadCheckUser = $image->downloads()->whereUserId(auth()->id())->whereType('subscription')->whereSize($type)->first();
		$dailyDownloads    = auth()->user()->dailyImageDownloadsCount();
		$downloadLimit     = auth()->user()->totalDailyImageDownloadLimit();

		if (!auth()->user()->getSubscription() && !$downloadCheckUser) {
			return back()->withError(__('misc.not_subscribed'));
		}

		if (!$downloadCheckUser) {
			$planUser = auth()->user()->getSubscription();
			$planPrice = ($planUser->currency === 'INR' && ($planUser->plan->price_inr ?? 0) > 0)
				? ($planUser->interval == 'month' ? $planUser->plan->price_inr : $planUser->plan->price_year_inr)
				: ($planUser->interval == 'month' ? $planUser->plan->price : $planUser->plan->price_year);

			$itemPrice = $planUser->interval == 'month'
				? Helper::calculatePriceGrossByDownloads($planPrice, $planUser->plan->downloads_per_month, true)
				: Helper::calculatePriceGrossByDownloads($planPrice, $planUser->plan->downloads_per_month);

			if ($downloadLimit != 0 && $dailyDownloads >= $downloadLimit) {
				return back()->withError(__('misc.reached_daily_download'));
			}

			if (auth()->user()->downloads == 0) {
				return back()->withError(__('misc.reached_download_limit_plan'));
			}

			// Admin and user earnings calculation
			$authorExclusive = $image->user ? $image->user->author_exclusive : 'no';
			$earnings = $this->earningsAdminUser($authorExclusive, $itemPrice, null, null);
			$directPayment = false;

			// Stripe Connect
			if ($image->user && $image->user->stripe_connect_id && $image->user->completed_stripe_onboarding && $planUser->payment_gateway == 'Stripe') {
				try {
					$payment = PaymentGateways::whereName('Stripe')->whereEnabled(1)->first();
					// Stripe Client
					$stripe = new \Stripe\StripeClient($payment->key_secret);

					$earningsUser = in_array(config('settings.currency_code'), config('currencies.zero_decimal')) ? $earnings['user'] : ($earnings['user'] * 100);

					$stripe->transfers->create([
						'amount' => $earningsUser,
						'currency' => $this->settings->currency_code,
						'destination' => $image->user->stripe_connect_id,
						'description' => __('misc.stock_photo_purchase')
					]);

					$directPayment = true;
				} catch (\Exception $e) {
					\Log::info($e->getMessage());
				}
			}

			// Insert Download
			$download            = new Downloads();
			$download->images_id = $image->id;
			$download->user_id   = auth()->id();
			$download->ip        = request()->ip();
			$download->type      = 'subscription';
			$download->size      = $type;
			$download->action_type = 'download';
			$download->save();

			// Subtract download to user
			auth()->user()->decrement('downloads', 1);

			// If photo belongs to a 3rd-party contributor (not admin), credit their balance
			$isOwnerAdmin = ($image->user_id == 1 || ($image->user && $image->user->isSuperAdmin()));
			if (!$isOwnerAdmin && $image->user && !$directPayment && ($earnings['user'] ?? 0) > 0) {
				$image->user->increment('balance', $earnings['user']);
			}
		}


		if ($type != 'vector') {
			$pathFile = config('path.uploads') . $type . '/' . $getImage->name;
			$resolution = $getImage->resolution;
		} else {
			$pathFile = config('path.files') . $getImage->name;
			$resolution = __('misc.vector_graphic');
		}

		$headers = [
			'Content-Type' => 'image/' . $image->extension,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		return Storage::download($pathFile, $image->title . ' - ' . $resolution . '.' . $getImage->extension, $headers);
	} //<--- End Method

	protected function downloadAgain($image, $getImage)
	{

		$verifyPurchaseUserAgain = $image->purchases()
			->where('user_id', auth()->id())
			->where('images_id', $image->id)
			->where('type', '=', $this->request->type)
			->where('license', '=', $this->request->license)
			->first();

		if (!$verifyPurchaseUserAgain) {
			abort(404);
		}

		if ($this->request->type != 'vector') {
			$pathFile = config('path.uploads') . $this->request->type . '/' . $getImage->name;
			$resolution = $getImage->resolution;
		} else {
			$pathFile = config('path.files') . $getImage->name;
			$resolution = __('misc.vector_graphic');
		}

		$headers = [
			'Content-Type' => 'image/' . $image->extension,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		return Storage::download($pathFile, $image->title . ' - ' . $resolution . '.' . $getImage->extension, $headers);
	} //<--- End Method

	public function create()
	{
		if (auth()->guest()) {
			return response()->json([
				'session_null' => true,
				'success' => false,
			]);
		}

		return $this->upload('normal');
	}

	public function image($size, $path)
	{
		try {
			$prefix = '/uploads/preview/';
			if (!file_exists(public_path('uploads/preview/' . $path)) && file_exists(public_path('uploads/small/' . $path))) {
				$prefix = '/uploads/small/';
			}

			$server = ServerFactory::create([
				'response' => new LaravelResponseFactory(app('request')),
				'source' => Storage::disk()->getDriver(),
				'watermarks' => public_path('img'),
				'cache' => Storage::disk()->getDriver(),
				'source_path_prefix' => $prefix,
				'cache_path_prefix' => '.cache',
				'base_url' => $prefix,
			]);

			if (request()->get('size') && request()->get('size') == 'small') {
				$thumbnail = true;
			} else {
				$thumbnail = false;
			}

			if (request()->get('size') && request()->get('size') == 'medium') {
				$medium = true;
			} else {
				$medium = false;
			}

			$resolution = explode('x', Helper::resolutionPreview($size, $thumbnail, $medium));

			$width = $resolution[0];
			$height = $resolution[1];

			$server->outputImage(
				$path,
				[
					'w' => $width,
					'h' => $height,
					'mark' => $this->settings->show_watermark ? $this->settings->watermark : null,
					'markpos' => 'center',
					'markw' => '90w',
					''
				]
			);

			$server->deleteCache($path);
		} catch (\Exception $e) {

			\Log::info('Error Image Show - ' . $e->getMessage());

			abort(404);

			$server->deleteCache($path);
		}
	}

	public function preview($path)
	{
		$image = Stock::whereToken($path)->whereType('small')->select('name', 'resolution', 'extension')->firstOrFail();
		$resolution = $image->resolution;
		$resolution = explode('x', $image->resolution);
		$width = $resolution[0];
		$height = $resolution[1];

		$imageUrl = Storage::url(config('path.small') . $image->name);

		header('Content-type: image/' . $image->extension);
		header('Cache-Control: public, max-age=10800');
		header("Expires: " . date('D, d F Y H:i:s', strtotime('+1 year')) . ""); // Fecha en el pasado

		// Crop Image
		if (request()->get('fit') == 'crop') {

			$size_x = 400;

			if ($width > $height) {
				$new_height = $size_x;
				$new_width = ($width / $height) * $new_height;

				$x = ($width - $height) / 2;
				$y = 0;
			} else {
				$new_width = $size_x;
				$new_height = ($height / $width) * $new_width;

				$y = ($height - $width) / 2;
				$x = 0;
			}

			$newImage = imagecreatetruecolor($size_x, $size_x);
		} else {

			switch (request()->get('w')) {
				case "tiny":
					$size_x = 100;
					break;
				case "small":
					$size_x = 280;
					break;
				case "medium":
					$size_x = 480;
					break;
				default:
					$size_x = 580;
			}

			$size_y = 800;

			$resize_x = $size_x / $width;
			$resize_y = $size_y / $height;

			if ($resize_x < $resize_y) {
				$resize = $resize_x;
			} else {
				$resize = $resize_y;
			}

			$newImage = imagecreatetruecolor(ceil($width * $resize), ceil($height * $resize));
		}

		switch ($image->extension) {
			case "gif":
				$source = imagecreatefromgif($imageUrl);
				imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
				imagealphablending($newImage, TRUE);
				break;
			case "pjpeg":
			case "jpeg":
			case "jpg":
				$source = imagecreatefromjpeg($imageUrl);
				break;
			case "png":
			case "x-png":
				$source = imagecreatefrompng($imageUrl);
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
				break;
		}

		if (request()->get('fit') == 'crop') {
			imagecopyresampled($newImage, $source, 0, 0, $x, $y, $new_width, $new_height, $width, $height);
		} else {
			imagecopyresampled($newImage, $source, 0, 0, 0, 0, ceil($width * $resize), ceil($height * $resize), $width, $height);
		}

		switch ($image->extension) {
			case "gif":
				imagegif($newImage);
				break;
			case "pjpeg":
			case "jpeg":
			case "jpg":
				imagejpeg($newImage, NULL, 90);
				break;
			case "png":
			case "x-png":
				imagepng($newImage);
				break;
		}

		imagedestroy($newImage);
	}

	/**
     * Get Subcategories via Ajax	
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategories(Request $request)
	{
		if (! $request->expectsJson()) {
			abort(404);
		}

		$data = null;

		$subcategories = Subcategories::select('id', 'name', 'slug')
            ->whereCategoryId($request->id)
			->whereMode('on')
            ->orderBy('name', 'desc')
            ->get();

		foreach ($subcategories as $subcategory) {
			$data[] = [
				'id' => $subcategory->id,
				'name' => \Lang::has('subcategories.' . $subcategory->slug) 
				? __('subcategories.' . $subcategory->slug) 
				: $subcategory->name,
			];
		}

		return response()->json($data);
    }

	public function copyPrompt(Request $request, $id)
	{
		if (!auth()->check()) {
			return response()->json([
				'success' => false,
				'message' => __('misc.login_to_copy_prompt') ?: 'Please log in to copy this prompt.',
				'require_login' => true
			], 401);
		}

		$image = Images::findOrFail($id);
		$user = auth()->user();

		if (!$user->canCopyPrompt($image)) {
			$subscription = $user->getSubscription();
			if ($image->item_for_sale == 'sale' && !$subscription) {
				return response()->json([
					'success' => false,
					'message' => __('misc.premium_prompt_requires_subscription') ?: 'Upgrade your plan to copy premium prompts.',
					'require_subscription' => true
				], 403);
			}

			$used = $user->dailyPromptCopiesCount();
			$limit = $user->totalDailyPromptLimit();

			// Fetch active plan for upsell details
			$plan = \App\Models\Plans::where('status', '1')->orderBy('popular', 'desc')->first();
			$isIndia = Helper::isIndia();

			if ($plan) {
				$planName = $plan->name;
				$currencySymbol = $isIndia ? '₹' : '$';
				$monthlyPrice = $isIndia ? ($plan->price_inr ?: 250) : ($plan->price ?: 3);
				$dailyPrice = $monthlyPrice / 30;

				$monthlyFormatted = $currencySymbol . ($isIndia ? number_format($monthlyPrice, 0) : (float)$monthlyPrice) . '/month';
				$dailyFormatted = '(' . $currencySymbol . number_format($dailyPrice, 2) . '/day)';
				$priceFormatted = $monthlyFormatted . ' ' . $dailyFormatted;
				$planLimit = $plan->download_limits ?: 100;
			} else {
				$planName = 'Pro Photography Plan';
				$priceFormatted = '$3/month ($0.10/day)';
				$planLimit = 100;
			}

			if (!$subscription) {
				$title = "Daily Free Limit Reached ($used/$limit Used)";
				$message = "Upgrade to \"$planName\" for just $priceFormatted to get $planLimit prompt copies/day, studio camera parameters, and full commercial client rights.";
			} else {
				$title = "Daily Limit Reached ($used/$limit Used)";
				$message = __('misc.reached_daily_copy_limit') ?: "You have reached your daily prompt copy limit of $limit.";
			}

			return response()->json([
				'success' => false,
				'title' => $title,
				'message' => $message,
				'limit_reached' => true,
				'copies_used' => $used,
				'total_limit' => $limit,
				'plan_name' => $planName,
				'plan_price' => $priceFormatted,
				'upgrade_url' => url('pricing'),
				'upgrade_text' => 'Upgrade Now'
			], 403);
		}

		// Check for debounced copy (within 10 seconds by same user for same image)
		$isDuplicateCopy = Downloads::where('images_id', $image->id)
			->where('user_id', $user->id)
			->where('action_type', 'copy')
			->where('date', '>=', \Carbon\Carbon::now()->subSeconds(10))
			->exists();

		if (!$isDuplicateCopy) {
			// Record copy action in downloads table
			$type = ($image->item_for_sale == 'sale' || $user->getSubscription()) ? 'subscription' : 'free';
			$download            = new Downloads();
			$download->images_id = $image->id;
			$download->user_id   = $user->id;
			$download->ip        = request()->ip();
			$download->type      = $type;
			$download->size      = 'prompt';
			$download->action_type = 'copy';
			$download->save();

			// Increment copies count
			$image->increment('copies_count');

			// Log analytics event
			\App\Services\AnalyticsService::logEvent('prompt_copy', request()->fullUrl(), $image->id, [
				'prompt_title' => $image->title,
				'ai_model' => $image->ai_model,
				'tier' => $image->item_for_sale,
			]);
		}

		$remaining = $user->remainingDailyPromptCopies();
		$limit = $user->totalDailyPromptLimit();
		$used = $user->dailyPromptCopiesCount();

		return response()->json([
			'success' => true,
			'prompt' => $image->prompt ?: $image->title,
			'remaining_copies' => $remaining,
			'total_limit' => $limit,
			'copies_used' => $used,
			'total_copies' => $image->totalPromptCopies(),
			'message' => __('misc.prompt_copied_success') ?: ($limit == 0 ? "Prompt copied!" : "Prompt copied! ($remaining remaining today)")
		]);
	}
}
