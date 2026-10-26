<?php

namespace App\Http\Controllers;

use App\Models\CampaignLinkClick;
use App\Models\CampaignRecipient;
use Illuminate\Http\Request;

class EmailTrackingController extends Controller
{
    /**
     * Transparent 1x1 GIF tracking pixel.
     */
    private const PIXEL_GIF = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /**
     * Handle Email Open Tracking Pixel.
     */
    public function trackOpen(string $token)
    {
        $recipient = CampaignRecipient::where('tracking_token', $token)->first();

        if ($recipient) {
            $campaign = $recipient->campaign;

            // Only mark unique open if this is the first open
            if ($recipient->open_count === 0) {
                $recipient->opened_at = now();
                if ($campaign) {
                    $campaign->increment('unique_opens_count');
                }
            }

            $recipient->increment('open_count');

            if ($campaign) {
                $campaign->increment('total_opens_count');
            }
        }

        // Return 1x1 transparent GIF with anti-caching headers
        return response(base64_decode(self::PIXEL_GIF))
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    /**
     * Handle Email Link Click Redirection.
     */
    public function trackClick(Request $request, string $token)
    {
        $rawUrl = $request->query('url');

        if (empty($rawUrl)) {
            return redirect('/');
        }

        $decodedUrl = urldecode($rawUrl);

        // Security check: only allow valid HTTP/HTTPS URLs
        if (!filter_var($decodedUrl, FILTER_VALIDATE_URL) || (!str_starts_with($decodedUrl, 'http://') && !str_starts_with($decodedUrl, 'https://'))) {
            return redirect('/');
        }

        $recipient = CampaignRecipient::where('tracking_token', $token)->first();

        if ($recipient) {
            $campaign = $recipient->campaign;

            if ($recipient->click_count === 0) {
                $recipient->clicked_at = now();
                if ($campaign) {
                    $campaign->increment('unique_clicks_count');
                }
            }

            $recipient->increment('click_count');

            if ($campaign) {
                $campaign->increment('total_clicks_count');
            }

            // Log individual click event
            CampaignLinkClick::create([
                'campaign_id' => $recipient->campaign_id,
                'recipient_id' => $recipient->id,
                'target_url' => $decodedUrl,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'clicked_at' => now(),
            ]);

            // Append standard Google Analytics UTM parameters to internal site links if not already present
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $targetHost = parse_url($decodedUrl, PHP_URL_HOST);

            $isInternal = false;
            if ($targetHost) {
                if ($appHost && (strcasecmp($appHost, $targetHost) === 0 || str_ends_with($targetHost, '.' . $appHost))) {
                    $isInternal = true;
                } elseif (str_contains(strtolower($targetHost), 'imaginebuddy.com')) {
                    $isInternal = true;
                }
            }

            if ($isInternal) {
                if (!str_contains($decodedUrl, 'utm_source=')) {
                    $campaignSlug = $campaign ? \Illuminate\Support\Str::slug($campaign->name) : 'broadcast';
                    $separator = str_contains($decodedUrl, '?') ? '&' : '?';
                    $decodedUrl .= $separator . http_build_query([
                        'utm_source' => 'email',
                        'utm_medium' => 'campaign',
                        'utm_campaign' => $campaignSlug,
                    ]);
                }
            }
        }

        return redirect()->away($decodedUrl);
    }
}
