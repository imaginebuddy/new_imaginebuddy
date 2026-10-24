<?php

namespace App\Services;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use Illuminate\Support\Collection;

class AudienceService
{
    /**
     * Get deliverable marketing recipients based on strict zero-cost deliverability rules:
     * - Only Google OAuth users with marketing_opt_in = 1
     * - Only confirmed NewsletterSubscribers with status = 'subscribed'
     * - Strictly excludes legacy unverified manual emails (oauth_provider IS NULL)
     * - Strictly excludes suppressed addresses (bounces, complaints, unsubscribes)
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getDeliverableMarketingAudience(): Collection
    {
        $suppressedEmails = EmailSuppression::pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->flip();

        $recipients = collect();

        // 1. Verified Google OAuth users
        $googleUsers = User::where('status', 'active')
            ->where('marketing_opt_in', 1)
            ->where('oauth_provider', 'google')
            ->whereNull('marketing_unsubscribed_at')
            ->select('id', 'name', 'username', 'email', 'token')
            ->get();

        foreach ($googleUsers as $user) {
            $email = strtolower(trim($user->email));
            if (!empty($email) && !isset($suppressedEmails[$email])) {
                $recipients->put($email, (object) [
                    'email' => $email,
                    'name' => $user->name ?: $user->username,
                    'user_id' => $user->id,
                    'unsubscribe_token' => $user->token ?: base64_encode($email),
                    'source' => 'google_user',
                ]);
            }
        }

        // 2. Confirmed newsletter subscribers (Double Opt-In)
        $subscribers = NewsletterSubscribers::where('status', 'subscribed')
            ->whereNotNull('confirmed_at')
            ->whereNull('unsubscribed_at')
            ->select('id', 'email', 'verification_token')
            ->get();

        foreach ($subscribers as $sub) {
            $email = strtolower(trim($sub->email));
            if (!empty($email) && !isset($suppressedEmails[$email])) {
                if (!$recipients->has($email)) {
                    $recipients->put($email, (object) [
                        'email' => $email,
                        'name' => 'Creator',
                        'user_id' => null,
                        'unsubscribe_token' => $sub->verification_token,
                        'source' => 'newsletter_subscriber',
                    ]);
                }
            }
        }

        return $recipients->values();
    }

    /**
     * Get audience filtered by specific segment key.
     */
    public static function getSegmentAudience(string $segment = 'all_verified'): Collection
    {
        $suppressedEmails = EmailSuppression::pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->flip();

        $recipients = collect();

        // 1. All Google Users segment
        if ($segment === 'all_verified' || $segment === 'google_users') {
            $googleUsers = User::where('status', 'active')
                ->where('marketing_opt_in', 1)
                ->where('oauth_provider', 'google')
                ->whereNull('marketing_unsubscribed_at')
                ->select('id', 'name', 'username', 'email', 'token', 'date')
                ->get();

            foreach ($googleUsers as $user) {
                $email = strtolower(trim($user->email));
                if (!empty($email) && !isset($suppressedEmails[$email])) {
                    $recipients->put($email, (object) [
                        'email' => $email,
                        'name' => $user->name ?: $user->username,
                        'user_id' => $user->id,
                        'unsubscribe_token' => $user->token ?: base64_encode($email),
                        'source' => 'Google User',
                        'joined_at' => $user->date,
                    ]);
                }
            }
        }

        // 2. Newsletter subscribers segment
        if ($segment === 'all_verified' || $segment === 'newsletter_subscribers') {
            $subscribers = NewsletterSubscribers::where('status', 'subscribed')
                ->whereNotNull('confirmed_at')
                ->whereNull('unsubscribed_at')
                ->select('id', 'email', 'verification_token', 'confirmed_at')
                ->get();

            foreach ($subscribers as $sub) {
                $email = strtolower(trim($sub->email));
                if (!empty($email) && !isset($suppressedEmails[$email])) {
                    if (!$recipients->has($email)) {
                        $recipients->put($email, (object) [
                            'email' => $email,
                            'name' => 'Creator',
                            'user_id' => null,
                            'unsubscribe_token' => $sub->verification_token,
                            'source' => 'Newsletter Subscriber',
                            'joined_at' => $sub->confirmed_at ? $sub->confirmed_at->format('Y-m-d') : null,
                        ]);
                    }
                }
            }
        }

        // 3. Paying subscribers segment
        if ($segment === 'paying_subscribers') {
            $payingUsers = User::where('status', 'active')
                ->where('marketing_opt_in', 1)
                ->where('oauth_provider', 'google')
                ->whereNull('marketing_unsubscribed_at')
                ->whereHas('mySubscription', function ($q) {
                    $q->where('status', 'active');
                })
                ->select('id', 'name', 'username', 'email', 'token', 'date')
                ->get();

            foreach ($payingUsers as $user) {
                $email = strtolower(trim($user->email));
                if (!empty($email) && !isset($suppressedEmails[$email])) {
                    $recipients->put($email, (object) [
                        'email' => $email,
                        'name' => $user->name ?: $user->username,
                        'user_id' => $user->id,
                        'unsubscribe_token' => $user->token ?: base64_encode($email),
                        'source' => 'Active Subscriber',
                        'joined_at' => $user->date,
                    ]);
                }
            }
        }

        // 4. Test list segment
        if ($segment === 'test_list') {
            $adminEmail = auth()->user()->email ?? config('mail.from.address');
            $recipients->put(strtolower($adminEmail), (object) [
                'email' => strtolower($adminEmail),
                'name' => 'Admin Preview',
                'user_id' => auth()->id(),
                'unsubscribe_token' => base64_encode($adminEmail),
                'source' => 'Admin Test',
                'joined_at' => now()->format('Y-m-d'),
            ]);
        }

        return $recipients->values();
    }

    /**
     * Get inactive Google OAuth users for re-engagement:
     * - Active account with Google OAuth
     * - marketing_opt_in = 1
     * - No login/activity for 30+ days
     * - Not suppressed
     */
    public static function getReengagementAudience(int $inactiveDays = 30): Collection
    {
        $suppressedEmails = EmailSuppression::pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->flip();

        $cutoff = now()->subDays($inactiveDays);

        $users = User::where('status', 'active')
            ->where('marketing_opt_in', 1)
            ->where('oauth_provider', 'google')
            ->whereNull('marketing_unsubscribed_at')
            ->where('date', '<', $cutoff)
            ->select('id', 'name', 'username', 'email', 'token', 'date')
            ->get();

        $recipients = collect();

        foreach ($users as $user) {
            $email = strtolower(trim($user->email));
            if (!empty($email) && !isset($suppressedEmails[$email])) {
                $recipients->put($email, (object) [
                    'email' => $email,
                    'name' => $user->name ?: $user->username,
                    'user_id' => $user->id,
                    'unsubscribe_token' => $user->token ?: base64_encode($email),
                    'created_date' => $user->date,
                ]);
            }
        }

        return $recipients->values();
    }
}
