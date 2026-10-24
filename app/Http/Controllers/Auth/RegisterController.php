<?php

namespace App\Http\Controllers\Auth;

use Cookie;
use Validator;
use App\Models\Referrals;
use App\Models\User;
use App\Models\Countries;
use App\Models\AdminSettings;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Helper;
use App\Notifications\VerifyAccount;
use Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminSettings $settings)
    {
        $this->middleware('guest');
        $this->settings = $settings::first();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
      $settings = AdminSettings::first();
      $data['_captcha'] = $settings->captcha;

		$messages = array (
			"letters"    => trans('validation.letters'),
      'g-recaptcha-response.required_if' => trans('misc.captcha_error_required'),
      'g-recaptcha-response.captcha' => trans('misc.captcha_error'),
        );

		 Validator::extend('ascii_only', function($attribute, $value, $parameters){
    		return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		// Validate if have one letter
	Validator::extend('letters', function($attribute, $value, $parameters){
    	return preg_match('/[a-zA-Z0-9]/', $value);
	});

        return Validator::make($data, [
            'username'  => 'required|min:3|max:15|ascii_only|alpha_dash|letters|unique:users|unique:pages,slug|unique:reserved,name',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'agree_gdpr' => 'required',
            'g-recaptcha-response' => 'required_if:_captcha,==,on|captcha'
        ],$messages);
    }

	public function showRegistrationForm()
  {
     	$settings = AdminSettings::first();

		if ($settings->registration_active == '1')	{
			return view('auth.register');
		} else {
			return redirect('/');
		}
  }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
    	$settings    = AdminSettings::first();

		// Verify Settings Admin
		if ($settings->email_verification == '1') {
			$confirmation_code = str_random(100);
			$status = 'pending';
		} else {
			$confirmation_code = '';
			$status            = 'active';
		}

		$token   = str_random(75);

    if ($settings->who_can_upload == 'all') {
      $authorized_to_upload = 'yes';
    } else {
      $authorized_to_upload = 'no';
    }

    // Get user country
    $country = Countries::whereCountryCode(Helper::userCountry())->first();

    $clientIp = request()->header('CF-Connecting-IP') ?: request()->ip();
    $marketingOptIn = !empty($data['marketing_opt_in']) ? 1 : 0;

		$user = User::create([
			'username'        => $data['username'],
			'name'            => '',
      'bio'             => '',
      'countries_id'    => $country->id ?? '',
			'password'        => bcrypt($data['password']),
			'email'           => strtolower($data['email']),
			'avatar'          => $settings->avatar,
			'cover'           => $settings->cover,
			'status'          => $status,
			'type_account'    => '1',
      'website'         => '',
      'twitter'         => '',
      'paypal_account'  => '',
			'activation_code' => $confirmation_code,
      'oauth_uid'       => '',
      'oauth_provider'  => '',
			'token'           => $token,
      'authorized_to_upload' => $authorized_to_upload,
      'ip'               => $clientIp,
      'marketing_opt_in' => $marketingOptIn,
      'marketing_opt_in_at' => $marketingOptIn ? now() : null,
      'marketing_opt_in_ip' => $marketingOptIn ? $clientIp : null
		]);

		if ($settings->email_verification == '1' && !empty($confirmation_code)) {
			$user->notify(new VerifyAccount($confirmation_code));
		}

		return $user;
  }// create

  /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $settings = AdminSettings::first();
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        \App\Services\AnalyticsService::stitchUser($user->id);
        \App\Services\AnalyticsService::logEvent('user_register', url('register'), null, ['user_id' => $user->id]);

        // Check Referral
        if ($this->settings->referral_system == 'on') {

          $referredBy = User::find(Cookie::get('referred'));

          if ($referredBy) {
            Referrals::create([
              'user_id' => $user->id,
              'referred_by' => $referredBy->id,
            ]);
          }
        }

        // Verify Settings Admin
    		if ($settings->email_verification) {

          return redirect('register')
                ->withStatus(trans('auth.check_account'));

        } else {
            $this->guard()->login($user);
            $urlReturn = $request->input('_url');
            if (isset($urlReturn) && url()->isValidUrl($urlReturn) && Helper::checkSourceURL($urlReturn)) {
              return redirect($urlReturn);
            }
            return $this->registered($request, $user)
                  ?: redirect($this->redirectPath());
        }

    }// register
}
