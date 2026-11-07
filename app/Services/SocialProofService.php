<?php

namespace App\Services;

use App\Models\Subscriptions;
use App\Models\Purchases;
use App\Models\User;
use App\Models\Plans;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SocialProofService
{
    /**
     * Cache duration in seconds (10 minutes)
     */
    const CACHE_TTL = 600;

    /**
     * Get the social proof payload for frontend rotation.
     *
     * @return array
     */
    public static function getPayload(): array
    {
        try {
            return Cache::remember('social_proof_payload_v1', self::CACHE_TTL, function () {
                return self::buildPayload();
            });
        } catch (\Throwable $e) {
            // Fail gracefully to demo data if cache or DB throws
            return self::buildPayload(false);
        }
    }

    /**
     * Build the structured payload containing config and four categories.
     *
     * @param bool $includeLive
     * @return array
     */
    protected static function buildPayload(bool $includeLive = true): array
    {
        $livePurchases = $includeLive ? self::fetchLivePurchases() : [];
        $liveSignups = $includeLive ? self::fetchLiveSignups() : [];

        $demoPurchases = self::getDemoPurchases();
        $demoSignups = self::getDemoSignups();
        $trendingItems = self::getTrendingItems();
        $milestoneItems = self::getMilestoneItems();

        // Merge live items with demo pool (live items placed first for authenticity)
        $purchases = array_merge($livePurchases, $demoPurchases);
        $signups = array_merge($liveSignups, $demoSignups);

        return [
            'enabled' => true,
            'config' => [
                'displayDuration' => 8000,     // 8 seconds
                'minInterval' => 12000,         // 12 seconds
                'maxInterval' => 20000,         // 20 seconds
                'initialDelay' => 6000,         // 6 seconds initial delay
                'dismissCooldownMinutes' => 15, // 15 mins cooldown if dismissed
            ],
            'categories' => [
                'purchase' => [
                    'id' => 'purchase',
                    'weight' => 50,
                    'title' => 'Purchase / Upgrade',
                    'badge' => 'Pro Upgrade',
                    'icon' => 'bi-lightning-charge-fill',
                    'accent' => '#10b981', // Emerald
                    'items' => $purchases,
                ],
                'signup' => [
                    'id' => 'signup',
                    'weight' => 25,
                    'title' => 'Creator Signup',
                    'badge' => 'New Creator',
                    'icon' => 'bi-person-check-fill',
                    'accent' => '#3b82f6', // Indigo / Blue
                    'items' => $signups,
                ],
                'trending' => [
                    'id' => 'trending',
                    'weight' => 15,
                    'title' => 'Trending Activity',
                    'badge' => 'Trending',
                    'icon' => 'bi-fire',
                    'accent' => '#f59e0b', // Amber / Orange
                    'items' => $trendingItems,
                ],
                'milestone' => [
                    'id' => 'milestone',
                    'weight' => 10,
                    'title' => 'Community Milestone',
                    'badge' => 'Milestone',
                    'icon' => 'bi-trophy-fill',
                    'accent' => '#8b5cf6', // Violet
                    'items' => $milestoneItems,
                ],
            ],
        ];
    }

    /**
     * Fetch recent verified subscriptions / upgrades (privacy-safe).
     *
     * @return array
     */
    protected static function fetchLivePurchases(): array
    {
        $items = [];

        try {
            $recentSubs = Subscriptions::with(['user', 'plan'])
                ->where('ends_at', null)
                ->orWhere('ends_at', '>', Carbon::now())
                ->latest()
                ->take(5)
                ->get();

            foreach ($recentSubs as $sub) {
                if (!$sub->user) {
                    continue;
                }

                $anonymized = self::anonymizeUser($sub->user);
                $planName = $sub->plan ? $sub->plan->name : 'Pro Photography Plan';
                $timeAgo = $sub->created_at ? Carbon::parse($sub->created_at)->diffForHumans() : 'Recently';

                $items[] = [
                    'id' => 'live_sub_' . $sub->id,
                    'headline' => $anonymized['display'],
                    'message' => 'Just upgraded to ' . $planName,
                    'time' => $timeAgo,
                    'avatar' => $anonymized['avatar_text'],
                    'avatar_color' => self::getAvatarColor($anonymized['name_seed']),
                    'verified' => true,
                ];
            }
        } catch (\Throwable $e) {
            // Silently fallback if table or connection is not ready
        }

        return $items;
    }

    /**
     * Fetch recent verified creator signups (privacy-safe).
     *
     * @return array
     */
    protected static function fetchLiveSignups(): array
    {
        $items = [];

        try {
            $recentUsers = User::where('status', 'active')
                ->latest('id')
                ->take(5)
                ->get();

            foreach ($recentUsers as $user) {
                $anonymized = self::anonymizeUser($user);
                $timeAgo = !empty($user->date) ? Carbon::parse($user->date)->diffForHumans() : 'Recently';

                $items[] = [
                    'id' => 'live_user_' . $user->id,
                    'headline' => $anonymized['display'],
                    'message' => 'Just joined the creator community',
                    'time' => $timeAgo,
                    'avatar' => $anonymized['avatar_text'],
                    'avatar_color' => self::getAvatarColor($anonymized['name_seed']),
                    'verified' => true,
                ];
            }
        } catch (\Throwable $e) {
            // Silently fallback
        }

        return $items;
    }

    /**
     * Demo Purchases / Upgrades (50% frequency pool).
     *
     * @return array
     */
    protected static function getDemoPurchases(): array
    {
        return [
            [
                'id' => 'demo_p1',
                'headline' => 'Rahul M. from Mumbai',
                'message' => 'Just upgraded to Pro Photography Plan',
                'time' => '2 minutes ago',
                'avatar' => 'RM',
                'avatar_color' => '#059669',
                'verified' => true,
            ],
            [
                'id' => 'demo_p2',
                'headline' => 'Ananya S. from Bangalore',
                'message' => 'Unlocked Commercial Product Studio Prompts',
                'time' => '5 minutes ago',
                'avatar' => 'AS',
                'avatar_color' => '#2563eb',
                'verified' => true,
            ],
            [
                'id' => 'demo_p3',
                'headline' => 'Vikram K. from Delhi NCR',
                'message' => 'Subscribed to Annual Unlimited Creator Plan',
                'time' => '8 minutes ago',
                'avatar' => 'VK',
                'avatar_color' => '#7c3aed',
                'verified' => true,
            ],
            [
                'id' => 'demo_p4',
                'headline' => 'Pooja P. from Pune',
                'message' => 'Upgraded to Pro — Jewelry & Cosmetics Studio Pack',
                'time' => '12 minutes ago',
                'avatar' => 'PP',
                'avatar_color' => '#db2777',
                'verified' => true,
            ],
            [
                'id' => 'demo_p5',
                'headline' => 'Arjun R. from Hyderabad',
                'message' => 'Just upgraded to Pro Photography Plan',
                'time' => '14 minutes ago',
                'avatar' => 'AR',
                'avatar_color' => '#d97706',
                'verified' => true,
            ],
            [
                'id' => 'demo_p6',
                'headline' => 'Sneha D. from Chennai',
                'message' => 'Unlocked High-Res Midjourney & Gemini Prompts',
                'time' => '18 minutes ago',
                'avatar' => 'SD',
                'avatar_color' => '#0d9488',
                'verified' => true,
            ],
        ];
    }

    /**
     * Demo Signups (25% frequency pool).
     *
     * @return array
     */
    protected static function getDemoSignups(): array
    {
        return [
            [
                'id' => 'demo_s1',
                'headline' => 'Priya S. from Bangalore',
                'message' => 'Just joined the creator community',
                'time' => '1 minute ago',
                'avatar' => 'PS',
                'avatar_color' => '#3b82f6',
                'verified' => true,
            ],
            [
                'id' => 'demo_s2',
                'headline' => 'Kavita M. from Chandigarh',
                'message' => 'Created an account to explore product photoshoot prompts',
                'time' => '4 minutes ago',
                'avatar' => 'KM',
                'avatar_color' => '#ec4899',
                'verified' => true,
            ],
            [
                'id' => 'demo_s3',
                'headline' => 'Aditya V. from Gurgaon',
                'message' => 'Just joined the creator community',
                'time' => '7 minutes ago',
                'avatar' => 'AV',
                'avatar_color' => '#6366f1',
                'verified' => true,
            ],
            [
                'id' => 'demo_s4',
                'headline' => 'Tanvi B. from Kochi',
                'message' => 'Joined Imaginebuddy creator platform',
                'time' => '11 minutes ago',
                'avatar' => 'TB',
                'avatar_color' => '#14b8a6',
                'verified' => true,
            ],
            [
                'id' => 'demo_s5',
                'headline' => 'Nikhil J. from Ahmedabad',
                'message' => 'Started generating commercial photography concepts',
                'time' => '15 minutes ago',
                'avatar' => 'NJ',
                'avatar_color' => '#f97316',
                'verified' => true,
            ],
        ];
    }

    /**
     * Trending Notifications (15% frequency pool).
     *
     * @return array
     */
    protected static function getTrendingItems(): array
    {
        return [
            [
                'id' => 'trend_1',
                'headline' => 'Trending this week',
                'message' => '12 photographers upgraded to Pro',
                'time' => 'Updated recently',
                'avatar' => 'TR',
                'avatar_icon' => 'bi-fire',
                'avatar_color' => '#f59e0b',
                'verified' => false,
            ],
            [
                'id' => 'trend_2',
                'headline' => 'Trending category',
                'message' => 'Perfume & Luxury Bottle prompts are #1 this week',
                'time' => 'High activity',
                'avatar' => 'TR',
                'avatar_icon' => 'bi-graph-up-arrow',
                'avatar_color' => '#ea580c',
                'verified' => false,
            ],
            [
                'id' => 'trend_3',
                'headline' => 'Prompt downloads spiking',
                'message' => 'Over 1,400 product photoshoot prompts copied today',
                'time' => 'Past 24 hours',
                'avatar' => 'TR',
                'avatar_icon' => 'bi-lightning-fill',
                'avatar_color' => '#e11d48',
                'verified' => false,
            ],
        ];
    }

    /**
     * Community Milestone Notifications (10% frequency pool).
     *
     * @return array
     */
    protected static function getMilestoneItems(): array
    {
        return [
            [
                'id' => 'ms_1',
                'headline' => 'Community milestone',
                'message' => "We've welcomed 50 new Pro creators this week!",
                'time' => 'Community update',
                'avatar' => 'MS',
                'avatar_icon' => 'bi-trophy-fill',
                'avatar_color' => '#8b5cf6',
                'verified' => false,
            ],
            [
                'id' => 'ms_2',
                'headline' => 'Milestone reached',
                'message' => '10,000+ AI product photoshoots crafted worldwide',
                'time' => 'Milestone celebration',
                'avatar' => 'MS',
                'avatar_icon' => 'bi-stars',
                'avatar_color' => '#9333ea',
                'verified' => false,
            ],
            [
                'id' => 'ms_3',
                'headline' => 'Creator network growing',
                'message' => 'Serving eCommerce brands & studios across 24 countries',
                'time' => 'Global community',
                'avatar' => 'MS',
                'avatar_icon' => 'bi-globe-central-south-asia',
                'avatar_color' => '#6d28d9',
                'verified' => false,
            ],
        ];
    }

    /**
     * Strictly anonymize user identity for privacy: First name + initial only.
     *
     * @param User $user
     * @return array
     */
    protected static function anonymizeUser(User $user): array
    {
        $rawName = trim($user->name ?: $user->username ?: 'Creator');
        $parts = preg_split('/\s+/', $rawName);
        $firstName = ucfirst(strtolower($parts[0] ?? 'Creator'));

        if (count($parts) > 1 && !empty($parts[1])) {
            $lastInitial = strtoupper(substr($parts[1], 0, 1)) . '.';
            $safeName = "{$firstName} {$lastInitial}";
            $initials = strtoupper(substr($firstName, 0, 1) . substr($parts[1], 0, 1));
        } else {
            $safeName = $firstName;
            $initials = strtoupper(substr($firstName, 0, 2));
        }

        // Determine location (City or State or Country)
        $location = 'India';
        if (!empty($user->state)) {
            $location = trim($user->state);
        } elseif (!empty($user->countries_id)) {
            $country = DB::table('countries')->where('id', $user->countries_id)->value('country_name');
            if ($country) {
                $location = $country;
            }
        } else {
            $defaultHubs = ['Mumbai', 'Bangalore', 'Delhi', 'Hyderabad', 'Pune', 'Kolkata'];
            $location = $defaultHubs[crc32($rawName) % count($defaultHubs)];
        }

        return [
            'display' => "{$safeName} from {$location}",
            'avatar_text' => $initials ?: 'CR',
            'name_seed' => $safeName,
        ];
    }

    /**
     * Generate consistent pleasant accent colors for initials.
     *
     * @param string $seed
     * @return string
     */
    protected static function getAvatarColor(string $seed): string
    {
        $colors = [
            '#059669', // Emerald
            '#2563eb', // Blue
            '#7c3aed', // Purple
            '#db2777', // Pink
            '#d97706', // Amber
            '#0d9488', // Teal
            '#4f46e5', // Indigo
            '#e11d48', // Rose
        ];

        $idx = abs(crc32($seed)) % count($colors);
        return $colors[$idx];
    }
}
