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
            'email' => ['required', 'email', 'max:255', 'unique:users', new \App\Rules\DisposableEmailRule],
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
    	if ($settings->email_verification == '1') {
          session()->put('registered_email_for_verification', $user->email);

          return redirect()->route('register.verify')
                ->with('status', trans('auth.check_account'));

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

    /**
     * Show page informing the user that confirmation email has been sent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerificationNotice(Request $request)
    {
        if (auth()->check() && auth()->user()->status == 'active') {
            return redirect('/');
        }

        $settings = AdminSettings::first();
        $email = session('registered_email_for_verification') ?: $request->query('email');

        return view('auth.verify', [
            'email' => $email,
            'settings' => $settings,
        ]);
    }

    /**
     * Resend the confirmation verification email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->input('email')));

        // Rate limit: max 1 request every 60 seconds per email
        $throttleKey = 'resend_verification_' . md5($email);
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return redirect()->route('register.verify', ['email' => $email])
                ->with('warning', "Please wait {$seconds} seconds before requesting another confirmation email.");
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('register.verify', ['email' => $email])
                ->with('error', 'We could not find an account registered with that email address.');
        }

        if ($user->status === 'active') {
            return redirect('login')
                ->with('status', 'Your account is already verified! You can log in.');
        }

        if ($user->status === 'pending') {
            if (empty($user->activation_code)) {
                $user->activation_code = \Illuminate\Support\Str::random(100);
                $user->save();
            }

            $user->notify(new VerifyAccount($user->activation_code));
            session()->put('registered_email_for_verification', $user->email);

            return redirect()->route('register.verify', ['email' => $user->email])
                ->with('status', 'A fresh confirmation email has been sent to ' . $user->email . '! Please check your inbox and spam folder.');
        }

        return redirect()->route('register.verify', ['email' => $user->email])
            ->with('warning', 'Unable to resend confirmation email for this account status.');
    }
}
