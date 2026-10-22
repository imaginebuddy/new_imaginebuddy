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

    /**
     * Handle Google One Tap asynchronous sign-in
     */
    public function oneTap(SocialAccountService $service, Request $request)
    {
        $credential = $request->input('credential');
        if (empty($credential)) {
            return response()->json(['success' => false, 'message' => 'Missing Google credential token.'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(8)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $credential
            ]);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Invalid Google ID token.'], 401);
            }

            $payload = $response->json();
            $clientId = config('services.google.client_id');

            if (!empty($clientId) && isset($payload['aud']) && $payload['aud'] !== $clientId) {
                return response()->json(['success' => false, 'message' => 'Client ID mismatch.'], 401);
            }

            if (empty($payload['email']) || empty($payload['sub'])) {
                return response()->json(['success' => false, 'message' => 'Incomplete Google profile data.'], 400);
            }

            $providerUser = new \Laravel\Socialite\Two\User();
            $providerUser->map([
                'id' => $payload['sub'],
                'nickname' => null,
                'name' => $payload['name'] ?? explode('@', $payload['email'])[0],
                'email' => $payload['email'],
                'avatar' => $payload['picture'] ?? null,
            ]);

            $user = $service->createOrGetUser($providerUser, 'google');

            if ($user instanceof \Illuminate\Http\RedirectResponse) {
                return response()->json(['success' => false, 'message' => 'Could not complete registration.'], 400);
            }

            if (!$user || !isset($user->id)) {
                return response()->json(['success' => false, 'message' => 'Account creation failed.'], 400);
            }

            if ($user->status == 'suspended') {
                return response()->json(['success' => false, 'message' => trans('validation.user_suspended')], 403);
            }

            auth()->login($user, true);

            if (class_exists('\App\Services\AnalyticsService')) {
                \App\Services\AnalyticsService::stitchUser($user->id);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                ],
            ]);

        } catch (\Throwable $e) {
            \Log::error("Google One Tap error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error during Google authentication.'], 500);
        }
    }

}//<-- End Class
