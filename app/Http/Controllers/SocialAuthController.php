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
    public function callback(SocialAccountService $service ,Request $request, $provider){

      try {
          $user = $service->createOrGetUser(Socialite::driver($provider)->user(), $provider);

          // Return Error missing Email User
          if( !isset($user->id) ) {
            return $user;
          } else {
            auth()->login($user);
          }

      } catch (\Exception $e) {
           return redirect('login')->with(['login_required' => trans('misc.error').' - '.$e->getMessage() ]);
      }

      $returnUrl = session()->pull('url.intended', '/');
      if ($returnUrl && (url()->isValidUrl($returnUrl) || str_starts_with($returnUrl, '/'))) {
        return redirect()->to($returnUrl);
      }

      return redirect()->to('/');
    }// End callback

}//<-- End Class
