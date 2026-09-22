<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\SocialAccountService;
use App\Helper;
use Socialite; // socialite namespace

class SocialAuthController extends Controller
{
    // redirect function
    public function redirect(Request $request, $provider){
      if ($request->has('return')) {
        session(['url.intended' => $request->input('return')]);
      }
      return Socialite::driver($provider)->redirect();
    }
    // callback function
    public function callback(SocialAccountService $service, Request $request, $provider){

      if ($request->has('error')) {
        $errorMsg = $request->get('error_description') ?: $request->get('error');
        return redirect('login')->with(['login_required' => trans('misc.error') . ' - ' . $errorMsg]);
      }

      try {
          // Try standard stateful OAuth first, fallback to stateless if session state is missing/mismatched
          try {
            $providerUser = Socialite::driver($provider)->user();
          } catch (\Exception $e) {
            $providerUser = Socialite::driver($provider)->stateless()->user();
          }

          $user = $service->createOrGetUser($providerUser, $provider);

          // If service returned a RedirectResponse (e.g., missing email error)
          if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
          }

          if (! isset($user->id)) {
            return redirect('login')->with(['login_required' => trans('misc.error')]);
          }

          if ($user->status == 'suspended') {
            return redirect('login')->with(['login_required' => trans('validation.user_suspended')]);
          }

          auth()->login($user);

          if (class_exists('\App\Services\AnalyticsService')) {
            \App\Services\AnalyticsService::stitchUser($user->id);
          }

      } catch (\Exception $e) {
           \Log::error("OAuth login error ($provider): " . $e->getMessage());
           return redirect('login')->with(['login_required' => trans('misc.error').' - '.$e->getMessage() ]);
      }

      $returnUrl = session()->pull('url.intended', '/');
      if ($returnUrl && (url()->isValidUrl($returnUrl) || str_starts_with($returnUrl, '/'))) {
        return redirect()->to($returnUrl);
      }

      return redirect()->to('/');
    }// End callback

}//<-- End Class
