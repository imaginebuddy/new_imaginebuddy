<?php

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use App\Notifications\NewsletterDoubleOptIn;
use App\Notifications\NewsletterWelcomeLeadMagnet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NewsletterController extends Controller
{
    /**
     * Handle newsletter subscription request (Double Opt-In).
     */
    public function subscribe(Request $request)
    {
        // 1. Bot Honeypot Protection
        if (!empty($request->input('newsletter_check'))) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Subscription received!',
            ], 200);
        }

        // 2. Validation
        $request->validate([
            'email' => ['required', 'email:filter', 'max:255', new \App\Rules\DisposableEmailRule],
        ]);

        $email = strtolower(trim($request->input('email')));

        // 3. Suppression Check
        if (EmailSuppression::isSuppressed($email)) {
            return response()->json([
                'message' => 'This email address is currently unable to receive newsletters.',
            ], 422);
        }

        // 4. Check if already active subscriber
        $existing = NewsletterSubscribers::where('email', $email)->first();
        if ($existing && $existing->status === 'subscribed') {
            return response()->json([
                'status' => 'already_subscribed',
                'message' => 'You are already an active subscriber! Check your inbox for our latest weekly prompt drops.',
            ], 200);
        }

        // 5. Generate secure token & save subscriber record
        $token = NewsletterSubscribers::generateToken();
        $ip = $request->header('CF-Connecting-IP') ?: $request->ip();
        $source = substr($request->input('source', 'footer'), 0, 100);

        $subscriber = NewsletterSubscribers::updateOrCreate(
            ['email' => $email],
            [
                'status' => 'pending_verification',
                'verification_token' => $token,
                'source_page' => $source,
                'ip_address' => $ip,
                'unsubscribed_at' => null,
            ]
        );

        // 6. Send Double Opt-In Email
        Notification::route('mail', $email)->notify(new NewsletterDoubleOptIn($subscriber));

        return response()->json([
            'status' => 'pending_confirmation',
            'message' => '🎉 Almost done! We sent a confirmation link to ' . $email . '. Please click it to unlock your 50+ Prompts!',
        ], 200);
    }

    /**
     * Confirm subscription via verification token and dispatch lead magnet.
     */
    public function confirm(Request $request, string $token)
    {
        $subscriber = NewsletterSubscribers::where('verification_token', $token)->first();

        if (!$subscriber) {
            return redirect('/')->with('error', 'The confirmation link is invalid or has expired.');
        }

        // Mark as active
        $subscriber->status = 'subscribed';
        $subscriber->confirmed_at = now();
        $subscriber->save();

        // Also sync user if already an existing registered account
        User::where('email', $subscriber->email)->update([
            'marketing_opt_in' => 1,
            'marketing_opt_in_at' => now(),
            'marketing_unsubscribed_at' => null,
        ]);

        // Dispatch Welcome Email + Lead Magnet
        Notification::route('mail', $subscriber->email)->notify(new NewsletterWelcomeLeadMagnet($subscriber));

        return view('newsletter.confirmed', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * One-click unsubscribe (supports both RFC 8058 POST and direct browser GET).
     */
    public function unsubscribe(Request $request, string $token)
    {
        $decoded = base64_decode($token, true);

        $subscriber = NewsletterSubscribers::where('verification_token', $token)
            ->orWhere('email', $token)
            ->when($decoded, function ($q) use ($decoded) {
                return $q->orWhere('email', $decoded);
            })
            ->first();

        $email = $subscriber ? $subscriber->email : (filter_var($token, FILTER_VALIDATE_EMAIL) ? $token : ($decoded && filter_var($decoded, FILTER_VALIDATE_EMAIL) ? $decoded : null));

        if ($subscriber) {
            $subscriber->status = 'unsubscribed';
            $subscriber->unsubscribed_at = now();
            $subscriber->save();
        }

        if ($email) {
            User::where('email', $email)->update([
                'marketing_opt_in' => 0,
                'marketing_unsubscribed_at' => now(),
            ]);

            EmailSuppression::suppress($email, 'manual_unsubscribe', 'marketing');
        }

        // RFC 8058 One-Click POST handler
        if ($request->isMethod('post')) {
            return response()->json([
                'status' => 'unsubscribed',
                'email' => $email,
            ], 200);
        }

        return view('newsletter.unsubscribed', [
            'email' => $email,
        ]);
    }
}
