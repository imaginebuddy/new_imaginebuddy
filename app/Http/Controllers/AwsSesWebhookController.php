<?php

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AwsSesWebhookController extends Controller
{
    /**
     * Handle incoming AWS SNS notifications for SES events (bounces, complaints).
     */
    public function handle(Request $request)
    {
        $raw = $request->getContent();
        $payload = json_decode($raw, true);

        if (!$payload && $request->isJson()) {
            $payload = $request->json()->all();
        }

        if (empty($payload) || !isset($payload['Type'])) {
            return response()->json(['error' => 'Invalid SNS payload'], 400);
        }

        // 1. Handle SNS Subscription Confirmation
        if ($payload['Type'] === 'SubscriptionConfirmation') {
            $subscribeUrl = $payload['SubscribeURL'] ?? null;

            if ($subscribeUrl && filter_var($subscribeUrl, FILTER_VALIDATE_URL)) {
                $host = parse_url($subscribeUrl, PHP_URL_HOST);
                if ($host && preg_match('/^sns\.[a-z0-9\-]+\.amazonaws\.com$/i', $host)) {
                    $ch = curl_init($subscribeUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    Log::info('AWS SNS subscription confirmed automatically.', [
                        'topic' => $payload['TopicArn'] ?? null,
                        'http_code' => $httpCode,
                    ]);

                    return response()->json(['status' => 'confirmed'], 200);
                }
            }

            return response()->json(['error' => 'Invalid SubscribeURL'], 400);
        }

        // 2. Handle SES Event Notifications
        if ($payload['Type'] === 'Notification') {
            $messageRaw = $payload['Message'] ?? null;
            $message = is_string($messageRaw) ? json_decode($messageRaw, true) : $messageRaw;

            if (!is_array($message)) {
                return response()->json(['status' => 'ignored_no_message'], 200);
            }

            $notificationType = $message['notificationType'] ?? ($message['eventType'] ?? null);
            $mailSource = $message['mail']['source'] ?? '';
            $stream = str_contains($mailSource, 'updates.imaginebuddy.com') ? 'marketing' : 'transactional';

            // Handle Hard Bounces
            if ($notificationType === 'Bounce') {
                $bounceType = $message['bounce']['bounceType'] ?? 'Permanent';
                $recipients = $message['bounce']['bouncedRecipients'] ?? [];

                if ($bounceType === 'Permanent') {
                    foreach ($recipients as $recipient) {
                        $email = strtolower(trim($recipient['emailAddress'] ?? ''));
                        if (!empty($email)) {
                            EmailSuppression::suppress($email, 'hard_bounce', $stream, $message);
                            User::where('email', $email)->update([
                                'marketing_opt_in' => 0,
                                'marketing_unsubscribed_at' => now(),
                            ]);
                            NewsletterSubscribers::where('email', $email)->update([
                                'status' => 'unsubscribed',
                                'unsubscribed_at' => now(),
                            ]);
                            Log::warning("Email hard bounce recorded and suppressed: {$email}");
                        }
                    }
                }
            }

            // Handle Spam Complaints
            if ($notificationType === 'Complaint') {
                $recipients = $message['complaint']['complainedRecipients'] ?? [];

                foreach ($recipients as $recipient) {
                    $email = strtolower(trim($recipient['emailAddress'] ?? ''));
                    if (!empty($email)) {
                        EmailSuppression::suppress($email, 'complaint', $stream, $message);
                        User::where('email', $email)->update([
                            'marketing_opt_in' => 0,
                            'marketing_unsubscribed_at' => now(),
                        ]);
                        NewsletterSubscribers::where('email', $email)->update([
                            'status' => 'unsubscribed',
                            'unsubscribed_at' => now(),
                        ]);
                        Log::warning("Email complaint recorded and suppressed: {$email}");
                    }
                }
            }

            return response()->json(['status' => 'processed'], 200);
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
