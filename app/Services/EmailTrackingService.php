<?php

namespace App\Services;

use App\Models\CampaignLinkClick;
use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailTrackingService
{
    /**
     * Generate a cryptographically secure, unique tracking token.
     */
    public static function generateToken(): string
    {
        return Str::random(40) . bin2hex(random_bytes(8));
    }

    /**
     * Rewrite email HTML to inject open tracking pixel and wrap click links.
     */
    public static function instrumentHtml(string $html, CampaignRecipient $recipient, EmailCampaign $campaign): string
    {
        $token = $recipient->tracking_token;

        // 1. Wrap hyperlinks if click tracking is enabled
        if ($campaign->track_clicks) {
            $html = preg_replace_callback('/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) use ($token) {
                $before = $matches[1];
                $url = trim($matches[2]);
                $after = $matches[3];

                // Skip non-HTTP links, anchors, or unsubscribe links
                if (
                    str_starts_with($url, '#') ||
                    str_starts_with($url, 'mailto:') ||
                    str_starts_with($url, 'tel:') ||
                    str_contains($url, '/newsletter/unsubscribe') ||
                    str_contains($url, '/email/track/')
                ) {
                    return $matches[0];
                }

                $trackingUrl = route('email.track.click', [
                    'token' => $token,
                    'url' => $url,
                ]);

                return '<a ' . $before . 'href="' . htmlspecialchars($trackingUrl) . '"' . $after . '>';
            }, $html);
        }

        // 2. Inject open tracking pixel if open tracking is enabled
        if ($campaign->track_opens) {
            $pixelUrl = route('email.track.open', ['token' => $token]);
            $pixelTag = '<img src="' . $pixelUrl . '" width="1" height="1" alt="" border="0" style="display:none !important; min-height:0; max-height:0; min-width:0; max-width:0; overflow:hidden;" />';

            if (stripos($html, '</body>') !== false) {
                $html = str_ireplace('</body>', $pixelTag . "\n</body>", $html);
            } else {
                $html .= "\n" . $pixelTag;
            }
        }

        return $html;
    }

    /**
     * Handle MessageSent event from Laravel mailer to record provider dispatch.
     */
    public static function handleMessageSent(MessageSent $event): void
    {
        try {
            $symfonyMessage = $event->message;
            $headers = $symfonyMessage->getHeaders();

            $campaignIdHeader = $headers->get('X-Campaign-ID');
            $recipientIdHeader = $headers->get('X-Recipient-ID');

            if (!$campaignIdHeader || !$recipientIdHeader) {
                return;
            }

            $campaignId = (int) $campaignIdHeader->getBodyAsString();
            $recipientId = (int) $recipientIdHeader->getBodyAsString();
            $sesMessageId = $event->sent ? $event->sent->getMessageId() : null;

            $recipient = CampaignRecipient::where('id', $recipientId)
                ->where('campaign_id', $campaignId)
                ->first();

            if ($recipient) {
                $recipient->update([
                    'status' => 'sent',
                    'ses_message_id' => $sesMessageId,
                    'sent_at' => now(),
                ]);

                $campaign = EmailCampaign::find($campaignId);
                if ($campaign) {
                    $campaign->increment('processed_count');
                    $campaign->increment('sent_count');

                    if ($campaign->processed_count >= $campaign->recipients_count) {
                        $campaign->update([
                            'status' => 'sent',
                            'completed_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error in EmailTrackingService::handleMessageSent: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Process AWS SES Delivery Notification from SNS.
     */
    public static function handleDeliveryNotification(array $message): void
    {
        $sesMessageId = $message['mail']['messageId'] ?? null;
        $deliveryRecipients = $message['delivery']['recipients'] ?? [];
        $deliveredAt = isset($message['delivery']['timestamp'])
            ? date('Y-m-d H:i:s', strtotime($message['delivery']['timestamp']))
            : now();

        $campaignId = self::extractCampaignId($message);

        foreach ($deliveryRecipients as $email) {
            $normalizedEmail = strtolower(trim($email));

            $query = CampaignRecipient::where('recipient_email', $normalizedEmail);
            if ($campaignId) {
                $query->where('campaign_id', $campaignId);
            } elseif ($sesMessageId) {
                $query->where('ses_message_id', $sesMessageId);
            }

            $recipient = $query->latest()->first();

            if ($recipient && in_array($recipient->status, ['queued', 'sending', 'sent'])) {
                $recipient->update([
                    'status' => 'delivered',
                    'delivered_at' => $deliveredAt,
                ]);

                $recipient->campaign?->increment('delivered_count');
            }
        }
    }

    /**
     * Process AWS SES Bounce Notification from SNS.
     */
    public static function handleBounceNotification(array $message, string $stream): void
    {
        $sesMessageId = $message['mail']['messageId'] ?? null;
        $bounceType = $message['bounce']['bounceType'] ?? 'Permanent';
        $bouncedRecipients = $message['bounce']['bouncedRecipients'] ?? [];
        $bouncedAt = isset($message['bounce']['timestamp'])
            ? date('Y-m-d H:i:s', strtotime($message['bounce']['timestamp']))
            : now();

        $campaignId = self::extractCampaignId($message);

        foreach ($bouncedRecipients as $b) {
            $email = strtolower(trim($b['emailAddress'] ?? ''));
            if (empty($email)) {
                continue;
            }

            $status = ($bounceType === 'Permanent') ? 'hard_bounced' : 'soft_bounced';

            $query = CampaignRecipient::where('recipient_email', $email);
            if ($campaignId) {
                $query->where('campaign_id', $campaignId);
            } elseif ($sesMessageId) {
                $query->where('ses_message_id', $sesMessageId);
            }

            $recipient = $query->latest()->first();
            if ($recipient) {
                $recipient->update([
                    'status' => $status,
                    'bounced_at' => $bouncedAt,
                    'bounce_type' => $bounceType . ($b['diagnosticCode'] ? ' - ' . $b['diagnosticCode'] : ''),
                ]);

                $recipient->campaign?->increment('bounced_count');
            }

            // Suppress permanently if hard bounce, or record soft bounce threshold
            if ($bounceType === 'Permanent') {
                EmailSuppression::suppress($email, 'hard_bounce', $stream, $message, 'all');
                User::where('email', $email)->update([
                    'marketing_opt_in' => 0,
                    'marketing_unsubscribed_at' => now(),
                ]);
                NewsletterSubscribers::where('email', $email)->update([
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => now(),
                ]);
            } else {
                // Soft bounce: track threshold (promote to suppression if repeated 3 times)
                EmailSuppression::recordSoftBounce($email, $stream, $message, 3);
            }
        }
    }

    /**
     * Process AWS SES Complaint Notification from SNS.
     */
    public static function handleComplaintNotification(array $message, string $stream): void
    {
        $sesMessageId = $message['mail']['messageId'] ?? null;
        $complainedRecipients = $message['complaint']['complainedRecipients'] ?? [];
        $complainedAt = isset($message['complaint']['timestamp'])
            ? date('Y-m-d H:i:s', strtotime($message['complaint']['timestamp']))
            : now();

        $campaignId = self::extractCampaignId($message);

        foreach ($complainedRecipients as $c) {
            $email = strtolower(trim($c['emailAddress'] ?? ''));
            if (empty($email)) {
                continue;
            }

            $query = CampaignRecipient::where('recipient_email', $email);
            if ($campaignId) {
                $query->where('campaign_id', $campaignId);
            } elseif ($sesMessageId) {
                $query->where('ses_message_id', $sesMessageId);
            }

            $recipient = $query->latest()->first();
            if ($recipient) {
                $recipient->update([
                    'status' => 'complained',
                    'complained_at' => $complainedAt,
                ]);

                $recipient->campaign?->increment('complained_count');
            }

            EmailSuppression::suppress($email, 'complaint', $stream, $message);
            User::where('email', $email)->update([
                'marketing_opt_in' => 0,
                'marketing_unsubscribed_at' => now(),
            ]);
            NewsletterSubscribers::where('email', $email)->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);
        }
    }

    /**
     * Extract CampaignId from headers or tags in the SES SNS payload.
     */
    private static function extractCampaignId(array $message): ?int
    {
        // 1. From tags
        $tags = $message['mail']['tags'] ?? [];
        if (isset($tags['CampaignId'][0])) {
            return (int) $tags['CampaignId'][0];
        }

        // 2. From headers
        $headers = $message['mail']['headers'] ?? [];
        foreach ($headers as $h) {
            if (isset($h['name']) && strcasecmp($h['name'], 'X-Campaign-ID') === 0) {
                return (int) $h['value'];
            }
        }

        return null;
    }
}
