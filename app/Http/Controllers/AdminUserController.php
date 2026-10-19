<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Models\User;
use App\Models\Collections;
use App\Models\UsersReported;
use App\Models\Stock;
use App\Models\Images;
use App\Models\ImagesReported;
use App\Models\Notifications;
use App\Models\Followers;
use App\Models\Downloads;
use App\Models\Like;
use App\Models\Replies;
use App\Models\Comments;
use App\Models\CollectionsImages;
use App\Models\Pages;
use App\Models\AdminSettings;
use App\Models\RolesAndPermissions;
use App\Models\Subscriptions;
use App\Models\Plans;
use App\Models\Countries;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller {

	use Traits\UserTrait;

	 protected function validator(array $data, $id = null) {

    	Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

			return Validator::make($data, [
	        	'email'     => 'required|email|max:255|unique:users,id,'.$id,
	        ]);

    }

	 /**
   * Display a listing of the resource.
   *
   * @return Response
   */
	 public function index()
	 {		 
		$query = request()->get('q');

		if ($query != '' && strlen($query) > 2) {
		 	$data = User::with('countryRelation')
				->where('name', 'LIKE', '%'.$query.'%')
				->orWhere('username', 'LIKE', '%'.$query.'%')
				->orWhere('email', 'LIKE', '%'.$query.'%')
		 		->orderBy('id','desc')->paginate(20);
		 } else {
		 	$data = User::with('countryRelation')->orderBy('id','desc')->paginate(20);
		 }

    	return view('admin.members', ['data' => $data,'query' => $query]);
	 }

	/**
	 * Show the form for creating a new user.
	 *
	 * @return Response
	 */
	public function create() {
		$roles = RolesAndPermissions::all();
		$proPlan = Plans::where('downloadable_content', 'images')->where('status', '1')->first();
		if (! $proPlan) {
			$proPlan = Plans::where('status', '1')->first();
		}
		return view('admin.add-member', compact('roles', 'proPlan'));
	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request) {
		Validator::extend('ascii_only', function($attribute, $value, $parameters){
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$messages = [
			'username.ascii_only' => 'The username must contain only ASCII characters without spaces or special accents.',
			'username.alpha_dash' => 'The username can only contain letters, numbers, dashes, and underscores.',
		];

		$validator = Validator::make($request->all(), [
			'name' => 'nullable|string|max:100',
			'username' => 'required|min:3|max:25|ascii_only|alpha_dash|unique:users,username|unique:pages,slug',
			'email' => 'required|email|max:255|unique:users,email',
			'password' => 'required|min:8',
			'status' => 'required|in:active,pending,suspended',
			'role' => 'required',
			'test_pro_duration' => 'nullable|integer|min:1|max:365',
		], $messages);

		// Additional case-insensitive duplicate check for username & email
		$validator->after(function ($validator) use ($request) {
			$cleanUsername = strtolower(trim($request->username ?? ''));
			$cleanEmail = strtolower(trim($request->email ?? ''));

			if ($cleanUsername !== '' && User::whereRaw('LOWER(username) = ?', [$cleanUsername])->exists()) {
				$validator->errors()->add('username', trans('validation.unique', ['attribute' => 'username']));
			}
			if ($cleanEmail !== '' && User::whereRaw('LOWER(email) = ?', [$cleanEmail])->exists()) {
				$validator->errors()->add('email', trans('validation.unique', ['attribute' => 'email']));
			}

			// Prevent non-super-admin from creating super admins (role id 1)
			if (Auth::user()->id != 1 && $request->role == '1') {
				$validator->errors()->add('role', trans('admin.unauthorized_action'));
			}
		});

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		$settings = AdminSettings::first();
		$country = Countries::whereCountryCode(Helper::userCountry())->first();

		$user = new User();
		$user->name = trim($request->name ?? '');
		$user->username = trim($request->username);
		$user->email = strtolower(trim($request->email));
		$user->password = bcrypt($request->password);
		$user->status = $request->status ?: 'active';
		$user->role = $request->role !== null ? $request->role : '0';
		$user->avatar = $settings->avatar ?? 'default.jpg';
		$user->cover = $settings->cover ?? 'cover.jpg';
		$user->type_account = '1';
		$user->bio = '';
		$user->website = '';
		$user->twitter = '';
		$user->paypal_account = '';
		$user->activation_code = '';
		$user->oauth_uid = '';
		$user->oauth_provider = '';
		$user->token = str_random(75);
		$user->authorized_to_upload = ($settings->who_can_upload ?? 'all') == 'all' ? 'yes' : 'no';
		$user->countries_id = $country->id ?? '';
		$user->save();

		// Optional Pro Test Access
		if ($request->filled('grant_test_pro') && in_array($request->grant_test_pro, ['yes', '1', 1, true], true)) {
			$duration = (int) ($request->test_pro_duration ?: 30);
			$notes = $request->filled('test_notes') ? trim($request->test_notes) : ('Granted upon creation by admin #' . Auth::id() . ' (' . Auth::user()->username . ')');
			$this->applyTestProAccess($user, $duration, $notes);
		}

		\Session::flash('success_message', 'User ' . $user->username . ' has been successfully created.');

		return redirect('panel/admin/members');
	}

	/**
	 * Display the specified user detail modal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function detail($id) {
		$user = User::with(['countryRelation', 'images', 'collections'])->findOrFail($id);
		$activeTestSub = $user->activeTestSubscription();
		$activePaidSub = $user->activePaidSubscription();
		$roles = RolesAndPermissions::all();

		if (request()->ajax() || request()->wantsJson()) {
			return view('admin.member-detail-modal', compact('user', 'activeTestSub', 'activePaidSub', 'roles'))->render();
		}

		return view('admin.member-detail-modal', compact('user', 'activeTestSub', 'activePaidSub', 'roles'));
	}

	/**
	 * Grant Pro Photography Plan test access to an existing user.
	 *
	 * @param  int  $id
	 * @param  Request  $request
	 * @return Response
	 */
	public function grantTestPro($id, Request $request) {
		$user = User::findOrFail($id);

		// Safeguard: Check if user already has an active PAID subscription
		$activePaidSub = Subscriptions::where('user_id', $user->id)
			->where('is_test', false)
			->where('ends_at', '>=', now())
			->where('cancelled', 'no')
			->where(function($q) {
				$q->whereNull('stripe_status')->orWhere('stripe_status', 'active');
			})
			->first();

		if ($activePaidSub) {
			\Session::flash('info_message', 'This member already has an active paid subscription (' . ($activePaidSub->payment_gateway ?: 'Paid') . '). Test access cannot overwrite paid subscriber accounts.');
			return redirect()->back();
		}

		$duration = (int) ($request->duration ?: 30);
		$duration = max(1, min(365, $duration));
		$notes = $request->filled('notes') ? trim($request->notes) : ('Granted by admin #' . Auth::id() . ' (' . Auth::user()->username . ')');

		$this->applyTestProAccess($user, $duration, $notes);

		\Session::flash('success_message', 'Pro Photography Plan test access (' . $duration . ' days) has been granted to ' . ($user->name ?: $user->username) . '.');

		return redirect()->back();
	}

	/**
	 * Revoke Pro Plan test access from a user.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function revokeTestPro($id) {
		$user = User::findOrFail($id);

		Subscriptions::where('user_id', $user->id)
			->where('is_test', true)
			->delete();

		// Check if user has any other active paid subscription
		$hasOtherActive = Subscriptions::where('user_id', $user->id)
			->where('is_test', false)
			->where('ends_at', '>=', now())
			->where('cancelled', 'no')
			->exists();

		if (! $hasOtherActive) {
			$user->update(['downloads' => 0]);
		}

		\Session::flash('success_message', 'Pro Plan test access has been revoked for ' . ($user->name ?: $user->username) . '.');

		return redirect()->back();
	}

	/**
	 * Helper to apply Pro Photography test subscription to a user.
	 *
	 * @param  User  $user
	 * @param  int  $durationDays
	 * @param  string|null  $notes
	 * @return void
	 */
	protected function applyTestProAccess($user, $durationDays = 30, $notes = null) {
		$proPlan = Plans::where('downloadable_content', 'images')->where('status', '1')->first();
		if (! $proPlan) {
			$proPlan = Plans::where('status', '1')->first();
		}

		$days = max(1, min(365, (int) $durationDays));
		$endsAt = now()->addDays($days);

		// Check if user already has an active test grant
		$existingTestSub = Subscriptions::where('user_id', $user->id)
			->where('is_test', true)
			->where('ends_at', '>=', now())
			->where('cancelled', 'no')
			->first();

		if ($existingTestSub) {
			$existingTestSub->update([
				'ends_at' => $endsAt,
				'stripe_status' => 'active',
				'test_notes' => $notes ?: $existingTestSub->test_notes,
				'last_refilled_at' => now(),
			]);
		} else {
			$subscription = new Subscriptions();
			$subscription->user_id = $user->id;
			$subscription->stripe_price = $proPlan ? $proPlan->plan_id : 'images';
			$subscription->stripe_status = 'active';
			$subscription->ends_at = $endsAt;
			$subscription->cancelled = 'no';
			$subscription->rebill_wallet = 'off';
			$subscription->interval = 'month';
			$subscription->taxes = '';
			$subscription->payment_gateway = 'Admin';
			$subscription->payment_method = 'test_grant';
			$subscription->amount = 0.00;
			$subscription->currency = Helper::isIndia() ? 'INR' : 'USD';
			$subscription->country_code = Helper::resolveUserCountry();
			$subscription->last_refilled_at = now();
			$subscription->is_test = true;
			$subscription->test_notes = $notes ?: ('Granted by admin #' . Auth::id() . ' (' . Auth::user()->username . ')');
			$subscription->save();
		}

		// Allocate downloads allowance from plan
		$monthlyDownloads = ($proPlan && $proPlan->downloads_per_month) ? $proPlan->downloads_per_month : 3000;
		$user->update(['downloads' => $monthlyDownloads]);
	}

	/**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
	public function edit($id) {

		$data = User::findOrFail($id);

		if( $data->id == 1 || $data->id == Auth::user()->id ) {
			\Session::flash('info_message', trans('admin.user_no_edit'));
			return redirect('panel/admin/members');
		}
    	return view('admin.edit-member')->withData($data);

	}//<--- End Method

	/**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
	public function update($id, Request $request) {

    $user = User::findOrFail($id);

	  $input = $request->all();

		if (! $request->authorized_to_upload) {
			$input['authorized_to_upload'] = 'no';
		}

	  $validator = $this->validator($input, $id);

		 if ($validator->fails()) {
	      return redirect()->back()
						 ->withErrors($validator)
						 ->withInput();
					 }

				 if ($request->status == 'suspended') {
					 $this->userSuspended($id);
				 }

    $user->fill($input)->save();

    \Session::flash('success_message', trans('admin.success_update'));

    return redirect('panel/admin/members');

	}//<--- End Method


	/**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */

	public function destroy($id) {

	 $user = User::findOrFail($id);

	 if( $user->id == 1 || $user->id == Auth::user()->id ) {
	 	return redirect('panel/admin/members');
		exit;
	 }

	 $this->deleteUser($id);

      return redirect('panel/admin/members');

	}//<--- End Method

	public function userSuspended($id) {

		// Collections
	$collections = Collections::where('user_id', '=', $id)->get();

	if( isset( $collections ) ){
		foreach($collections as $collection){

			// Collections
		$collectionsImages = CollectionsImages::where('images_id', '=', $collection->id)->get();
		 if( isset( $collectionsImages ) ){
				foreach($collectionsImages as $collectionsImage) {
					$collectionsImage->delete();
				}
			}
   $collection->delete();
		}
	}

	// Comments Delete
	$comments = Comments::where('user_id', '=', $id)->get();

	if( isset( $comments ) ){
		foreach($comments as $comment){
			$comment->delete();
		}
	}

	// Replies
	$replies = Replies::where('user_id', '=', $id)->get();

	if( isset( $replies ) ){
		foreach($replies as $replie){
			$replies->delete();
		}
	}

	// Likes
	$likes = Like::where('user_id', '=', $id)->get();
	if( isset( $likes ) ){
		foreach($likes as $like){
			$like->delete();
		}
	}

	// Downloads
	$downloads = Downloads::where('user_id', '=', $id)->get();
	if( isset( $downloads ) ){
		foreach($downloads as $download){
			$download->delete();
		}
	}

	// Followers
	$followers = Followers::where( 'follower', $id )->orwhere('following',$id)->get();
	if( isset( $followers ) ){
		foreach($followers as $follower){
			$follower->delete();
		}
	}

	// Delete Notification
	$notifications = Notifications::where('author',$id)
	->orWhere('destination', $id)
	->get();

	if(isset( $notifications ) ){
		foreach($notifications as $notification){
			$notification->delete();
		}
	}

	// Images Reported
	$images_reporteds = ImagesReported::where('user_id', '=', $id)->get();

	if(isset( $images_reporteds ) ){
		foreach ($images_reporteds as $images_reported ) {
				$images_reported->delete();
			}// End
	}

	// Images
    $images = Images::where('user_id', '=', $id)->get();

	if(isset( $images )) {
		foreach($images as $image) {

			// Collections Images
		$collectionsImagesUsers = CollectionsImages::where('images_id', '=', $image->id)->get();
		 if(isset( $collectionsImagesUsers ) ) {
				foreach($collectionsImagesUsers as $collectionsImagesUser){
					$collectionsImagesUser->delete();
				}
			}

			//<---- ALL RESOLUTIONS IMAGES
			$stocks = Stock::where('images_id', '=', $image->id)->get();

			foreach($stocks as $stock) {

				// Delete Stock
  			Storage::delete(config('path.uploads').$stock->type.'/'.$stock->name);

  			// Delete Stock Vector
  			Storage::delete(config('path.files').$stock->name);

				$stock->delete();
			}

			// Delete preview
  		Storage::delete(config('path.preview').$image->preview);

  		// Delete thumbnail
  		Storage::delete(config('path.thumbnail').$image->thumbnail);

			$image->delete();
		}
	}// End

	// User Reported
	$users_reporteds = UsersReported::where('user_id', '=', $id)->orWhere('id_reported', '=', $id)->get();

	if (isset($users_reporteds)) {
		foreach ($users_reporteds as $users_reported ) {
				$users_reported->delete();
			}// End
	}

	}


}
