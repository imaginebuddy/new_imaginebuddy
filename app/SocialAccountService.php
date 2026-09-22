<?php

namespace App;

use Cookie;
use App\Models\AdminSettings;
use App\Models\Countries;
use App\Models\Referrals;
use App\Models\User;
use App\Helper;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccountService
{
    public function createOrGetUser(ProviderUser $providerUser, $provider)
    {
      $settings = AdminSettings::first();
      $email = strtolower(trim($providerUser->getEmail() ?? ''));

      // 1. Check if user already exists with this OAuth provider and UID
      $user = User::whereOauthProvider($provider)
          ->whereOauthUid($providerUser->getId())
          ->first();

      // 2. If not found by OAuth UID, check if an existing account has this email
      if (! $user && ! empty($email)) {
        $user = User::where('email', $email)->first();

        if ($user) {
          // Link this OAuth provider to the existing account
          $user->oauth_provider = $provider;
          $user->oauth_uid = $providerUser->getId();

          // If user account was pending confirmation, activate it since email is verified by OAuth
          if ($user->status == 'pending') {
            $user->status = 'active';
          }

          $user->save();
          return $user;
        }
      }

      // If user is already found (by OAuth UID)
      if ($user) {
        return $user;
      }

      // 3. New user registration via OAuth: verify email is provided
      if (empty($email)) {
        return redirect("login")->with(['login_required' => trans('error.error_required_mail')]);
      }

      $token = str_random(75);
      $avatar = 'default.jpg';
      $path = config('path.avatar');

      // Safely download and store avatar
      if (! empty($providerUser->getAvatar())) {
        $avatarUser = $providerUser->getAvatar();

        if ($provider == 'facebook') {
          $avatarUser = str_replace('?type=normal', '?type=large', $avatarUser);
        } elseif ($provider == 'twitter') {
          $avatarUser = str_replace('_normal', '_200x200', $avatarUser);
        }

        try {
          $context = stream_context_create([
            'http' => [
              'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
              'timeout' => 5,
            ],
            'ssl' => [
              'verify_peer'      => false,
              'verify_peer_name' => false,
            ]
          ]);

          $fileContents = @file_get_contents($avatarUser, false, $context);
          if ($fileContents) {
            $nameAvatar = time() . '_' . Helper::strRandom(5);
            \Storage::put($path . $nameAvatar . '.jpg', $fileContents, 'public');
            $avatar = $nameAvatar . '.jpg';
          }
        } catch (\Throwable $e) {
          \Log::warning("Could not download social avatar for {$providerUser->getId()}: " . $e->getMessage());
          $avatar = 'default.jpg';
        }
      }

      // Get user country
      $country = Countries::whereCountryCode(Helper::userCountry())->first();
      $authorized_to_upload = ($settings && $settings->who_can_upload == 'all') ? 'yes' : 'no';

      // Generate unique username
      $username = Helper::strRandom();
      while (User::whereUsername($username)->exists()) {
        $username = Helper::strRandom();
      }

      $user = User::create([
        'username'             => $username,
        'name'                 => $providerUser->getName() ?? $username,
        'countries_id'         => $country->id ?? '',
        'password'             => '',
        'email'                => $email,
        'avatar'               => $avatar,
        'cover'                => 'cover.jpg',
        'status'               => 'active',
        'type_account'         => '1',
        'website'              => '',
        'activation_code'      => '',
        'oauth_uid'            => $providerUser->getId(),
        'oauth_provider'       => $provider,
        'token'                => $token,
        'authorized_to_upload' => $authorized_to_upload,
        'ip'                   => request()->ip(),
      ]);

      // Check Referral
      if ($settings && $settings->referral_system == 'on') {
        $referredBy = User::find(Cookie::get('referred'));
        if ($referredBy) {
          Referrals::create([
            'user_id'     => $user->id,
            'referred_by' => $referredBy->id,
          ]);
        }
      }

      return $user;
    }
}
