<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Helper;
use App\Models\AdminSettings;

class UserCountry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (! $request->expectsJson()) {
          try {
            $ip = request()->ip();
            if ($ip != '127.0.0.1' && $ip != '::1' && !str_starts_with($ip, '192.168.') && !str_starts_with($ip, '10.')) {
              if (! Cache::has('userCountry-'.$ip)) {
                $data = Helper::getDatacURL("http://ip-api.com/json/".$ip);
                if (isset($data->countryCode)) {
                  Cache::put('userCountry-'.$ip, $data->countryCode, now()->addDays(7));
                  Cache::put('userRegion-'.$ip, $data->region ?? '', now()->addDays(7));
                  session()->put('user_country', strtoupper($data->countryCode));
                }
              } else {
                session()->put('user_country', strtoupper(Cache::get('userCountry-'.$ip)));
              }
            } else {
              $defaultCountry = env('GEOIP_DEFAULT_COUNTRY', 'IN');
              session()->put('user_country', strtoupper($defaultCountry));
            }

          } catch (\Exception $e) {}
      }// expectsJson

        return $next($request);
    }
}
