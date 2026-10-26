<?php

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use App\Services\EmailTrackingService;
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

            // 1. Handle Successful Deliveries
            if ($notificationType === 'Delivery') {
                EmailTrackingService::handleDeliveryNotification($message);
                Log::info('AWS SES delivery event recorded.', [
                    'message_id' => $message['mail']['messageId'] ?? null,
                    'recipients' => $message['delivery']['recipients'] ?? [],
                ]);
            }

            // 2. Handle Bounces (Hard & Soft)
            if ($notificationType === 'Bounce') {
                EmailTrackingService::handleBounceNotification($message, $stream);
            }

            // 3. Handle Spam Complaints
            if ($notificationType === 'Complaint') {
                EmailTrackingService::handleComplaintNotification($message, $stream);
            }

            return response()->json(['status' => 'processed'], 200);
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
