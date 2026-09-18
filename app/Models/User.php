<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
  use Notifiable, Billable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  const CREATED_AT = 'date';
  const UPDATED_AT = null;

  protected $fillable = [
    'username',
    'name',
    'bio',
    'countries_id',
    'email',
    'password',
    'avatar',
    'cover',
    'status',
    'type_account',
    'website',
    'twitter',
    'paypal_account',
    'activation_code',
    'oauth_uid',
    'oauth_provider',
    'token',
    'authorized_to_upload',
    'role',
    'ip',
    'stripe_connect_id',
    'completed_stripe_onboarding',
    'downloads',
    'balance',
    'funds'
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'completed_stripe_onboarding' => 'bool',
  ];

  protected $withCount = [
    'newNotifications'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  public function sendPasswordResetNotification($token)
  {
    $this->notify(new ResetPasswordNotification($token));
  }

  public function images()
  {
    return $this->hasMany(Images::class)->where('status', 'active');
  }

  public function allImages()
  {
    return $this->hasMany(Images::class);
  }

  public function images_pending()
  {
    return $this->hasMany(Images::class)->where('status', 'pending');
  }

  public function collections()
  {
    return $this->hasMany(Collections::class);
  }

  public function likes()
  {
    return $this->hasMany(Like::class);
  }

  public function downloads()
  {
    return $this->hasMany(Downloads::class);
  }

  public function comments()
  {
    return $this->hasMany(Comments::class);
  }

  public function following()
  {
    return $this->hasMany(Followers::class, 'follower')->where('status', '1');
  }

  public function followers()
  {
    return $this->hasMany(Followers::class, 'following')->where('status', '1');
  }

  public function notifications()
  {
    return $this->hasMany(Notifications::class, 'destination');
  }

  public function country()
  {
    return $this->belongsTo(Countries::class, 'countries_id')->first();
  }

  public function countryRelation()
  {
    return $this->belongsTo(Countries::class, 'countries_id');
  }

  public static function totalImages($id)
  {
    return Images::where('user_id', '=', $id)->where('status', 'active')->count();
  }

  public function purchases()
  {
    return $this->hasMany(Purchases::class);
  }

  public function newNotifications()
  {
    return $this->notifications()->whereStatus('0');
  }

  public function unseenNotifications()
  {
    return $this->new_notifications_count;
  }

  public function dailyImageDownloadsCount()
  {
    return $this->downloads()
      ->where('date', '>=', today())
      ->where(function ($q) {
        $q->where('action_type', 'download')
          ->orWhere(function ($sq) {
            $sq->whereNull('action_type')->where('size', '!=', 'prompt');
          });
      })
      ->count();
  }

  public function totalDailyImageDownloadLimit()
  {
    $subscription = $this->getSubscription();
    if ($subscription) {
      return ($subscription->plan && $subscription->plan->download_limits)
        ? $subscription->plan->download_limits
        : 100;
    }

    $settings = AdminSettings::first();
    return $settings ? ($settings->daily_limit_downloads ?: 10) : 10;
  }

  public function remainingDailyImageDownloads()
  {
    return max(0, $this->totalDailyImageDownloadLimit() - $this->dailyImageDownloadsCount());
  }

  public function freeDailyDownloads()
  {
    return $this->dailyImageDownloadsCount();
  }

  public function subscriptionDailyDownloads()
  {
    return $this->dailyImageDownloadsCount();
  }

  public function dailyPromptCopiesCount()
  {
    return $this->downloads()
      ->where('date', '>=', today())
      ->where(function ($q) {
        $q->where('action_type', 'copy')
          ->orWhere('size', 'prompt');
      })
      ->count();
  }

  public function totalDailyPromptLimit()
  {
    $subscription = $this->getSubscription();
    if ($subscription) {
      return ($subscription->plan && $subscription->plan->download_limits)
        ? $subscription->plan->download_limits
        : 100;
    }

    return 10;
  }

  public function remainingDailyPromptCopies()
  {
    return max(0, $this->totalDailyPromptLimit() - $this->dailyPromptCopiesCount());
  }

  public function freeDailyPromptCopies()
  {
    return $this->dailyPromptCopiesCount();
  }

  public function subscriptionDailyPromptCopies()
  {
    return $this->dailyPromptCopiesCount();
  }

  public function freeDailyUsage()
  {
    return $this->dailyPromptCopiesCount();
  }

  public function subscriptionDailyUsage()
  {
    return $this->dailyPromptCopiesCount();
  }

  public function canCopyPrompt($image)
  {
    if ($this->isSuperAdmin() || $this->id == $image->user_id) {
      return true;
    }

    $subscription = $this->getSubscription();

    // Premium prompt requires an active subscription
    if ($image->item_for_sale == 'sale' && !$subscription) {
      return false;
    }

    // Both free and premium prompt copies are bounded by totalDailyPromptLimit
    return $this->dailyPromptCopiesCount() < $this->totalDailyPromptLimit();
  }

  public function dailyUploads()
  {
    return $this->images()
      ->where('date', '>=', today())
      ->count();
  }

  public function followActive($user)
  {
    return $this->following()
      ->where('following', $user)
      ->first();
  }

  /**
   * The tax rates that should apply to the customer's subscriptions.
   *
   * @return array
   */
  public function taxRates()
  {
    $taxRates = [];
    $payment = PaymentGateways::whereName('Stripe')
      ->whereEnabled('1')
      ->where('key_secret', '<>', '')
      ->first();

    if ($payment) {
      $stripe = new \Stripe\StripeClient($payment->key_secret);
      $taxes = $stripe->taxRates->all();

      foreach ($taxes->data as $tax) {
        if (
          $tax->active && $tax->state == $this->getRegion()
          && $tax->country == $this->getCountry()
          || $tax->active
          && $tax->country == $this->getCountry()
          && $tax->state == null
        ) {
          $taxRates[] = $tax->id;
        }
      }
    }

    return $taxRates;
  }

  public function isTaxable()
  {
    return TaxRates::whereStatus('1')
      ->whereIsoState($this->getRegion())
      ->whereCountry($this->getCountry())
      ->orWhere('country', $this->getCountry())
      ->whereNull('iso_state')
      ->whereStatus('1')
      ->get();
  }

  public function taxesPayable()
  {
    return $this->isTaxable()
      ->pluck('id')
      ->implode('_');
  }

  public function getCountry()
  {
    $ip = request()->ip();
    return cache('userCountry-' . $ip) ?? ($this->country()->country_code ?? null);
  }

  public function getRegion()
  {
    $ip = request()->ip();
    return cache('userRegion-' . $ip) ?? null;
  }

  /**
   * User plans
   */
  public function plans()
  {
    return $this->hasMany(Plans::class);
  }

  // Get details plan
  public function plan($interval, $field)
  {
    return $this->plans()
      ->whereInterval($interval)
      ->pluck($field)
      ->first();
  }

  // Get Plan Active
  public function planActive()
  {
    return $this->plans()->whereStatus('1')->first();
  }

  public function mySales()
  {
    return $this->hasManyThrough(
      Purchases::class,
      Images::class,
      'user_id',
      'images_id',
      'id',
      'id'
    )->whereApproved('1');
  }

  public function totalDownloads()
  {
    return $this->hasManyThrough(
      Downloads::class,
      Images::class,
      'user_id',
      'images_id',
      'id',
      'id'
    );
  }

  public function totalLikes()
  {
    return $this->hasManyThrough(
      Like::class,
      Images::class,
      'user_id',
      'images_id',
      'id',
      'id'
    );
  }

  public function referrals()
  {
    return $this->hasMany(Referrals::class, 'referred_by');
  }

  public function referralTransactions()
  {
    return $this->hasMany(ReferralTransactions::class, 'referred_by');
  }

  /**
   * Get the user's Role.
   */
  public function role()
  {
    return $this->belongsTo(RolesAndPermissions::class, 'role')->first();
  }

  /**
   * Get the user's is Super Admin.
   */
  public function isSuperAdmin()
  {
    if ($this->role() && $this->role()->permissions == 'full_access') {
      return $this->id;
    }
    return false;
  }

  /**
   * Get the user's permissions.
   */
  public function hasPermission($section)
  {
    if (! $this->role()) {
      return false;
    }

    $permissions = explode(',', $this->role()->permissions);

    return in_array($section, $permissions)
      || $this->role()->permissions == 'full_access'
      || $this->role()->permissions == 'limited_access'
      ? true
      : false;
  }

  public function mySubscription()
  {
    return $this->hasMany(Subscriptions::class);
  }

  public function getSubscription()
  {
    return $this->mySubscription()
      ->where('ends_at', '>=', now())
      ->where(function ($query) {
        $query->where(function ($q) {
          $q->where('cancelled', 'no')
            ->where(function ($sq) {
              $sq->whereNull('stripe_status')
                ->orWhere('stripe_status', 'active');
            });
        })
        ->orWhere(function ($q) {
          $q->where('cancelled', 'yes')
            ->where('ends_at', '>=', now());
        });
      })
      ->orderBy('id', 'desc')
      ->first();
  }

  public function oneSignalDevices()
  {
    return $this->hasMany(UserDevices::class);
  }
}
