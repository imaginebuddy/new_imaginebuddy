<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use App\Models\EmailSuppression;
use App\Models\EmailSyncLog;
use App\Models\Images;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use App\Services\AudienceService;
use App\Services\EmailSuppressionSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminEmailDeliverabilityController extends Controller
{
    /**
     * Display email deliverability metrics and suppression records.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'suppressions');
        $q = trim($request->input('q', ''));
        $reason = $request->input('reason', '');

        // 1. Audience Metrics
        $googleVerifiedCount = User::where('oauth_provider', 'google')
            ->where('status', 'active')
            ->where('marketing_opt_in', 1)
            ->count();

        $newsletterActiveCount = NewsletterSubscribers::where('status', 'subscribed')->count();
        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();

        // 2. Suppression Metrics
        $suppressionsTotal = EmailSuppression::count();
        $suppressionsBounces = EmailSuppression::whereIn('reason', ['hard_bounce', 'soft_bounce'])->count();
        $suppressionsComplaints = EmailSuppression::where('reason', 'complaint')->count();
        $suppressionsUnsubscribes = EmailSuppression::where('reason', 'manual_unsubscribe')->count();
        $suppressionsDisposable = EmailSuppression::where('reason', 'disposable')->count();
        $suppressionsAdmin = EmailSuppression::where('reason', 'admin_suppressed')->count();

        // 3. Sync & Reconciliation Status
        $lastSync = EmailSyncLog::with('admin')->latest()->first();
        $syncLogs = EmailSyncLog::with('admin')->latest()->take(10)->get();

        // 4. Tab Data
        if ($tab === 'subscribers') {
            $subscribersQuery = NewsletterSubscribers::latest();
            if (!empty($q)) {
                $subscribersQuery->where('email', 'LIKE', "%{$q}%");
            }
            $data = $subscribersQuery->paginate(20)->withQueryString();
            $suppressions = null;
        } elseif ($tab === 'guide') {
            $data = null;
            $suppressions = null;
        } elseif ($tab === 'sync-logs') {
            $data = null;
            $suppressions = null;
        } else {
            $suppressionQuery = EmailSuppression::latest();
            if (!empty($q)) {
                $suppressionQuery->where('email', 'LIKE', "%{$q}%");
            }
            if (!empty($reason)) {
                $suppressionQuery->where('reason', $reason);
            }
            $suppressions = $suppressionQuery->paginate(20)->withQueryString();
            $data = null;
        }

        return view('admin.email-deliverability', [
            'tab' => $tab,
            'q' => $q,
            'reason' => $reason,
            'googleVerifiedCount' => $googleVerifiedCount,
            'newsletterActiveCount' => $newsletterActiveCount,
            'totalAudience' => $totalAudience,
            'suppressionsTotal' => $suppressionsTotal,
            'suppressionsBounces' => $suppressionsBounces,
            'suppressionsComplaints' => $suppressionsComplaints,
            'suppressionsUnsubscribes' => $suppressionsUnsubscribes,
            'suppressionsDisposable' => $suppressionsDisposable,
            'suppressionsAdmin' => $suppressionsAdmin,
            'suppressions' => $suppressions,
            'subscribers' => $data,
            'lastSync' => $lastSync,
            'syncLogs' => $syncLogs,
        ]);
    }

    /**
     * Trigger manual unified deliverability & suppression synchronization.
     */
    public function sync(Request $request)
    {
        $result = EmailSuppressionSyncService::run('admin', auth()->id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success_message', "Deliverability synchronization completed in {$result['duration_seconds']}s! Reconciled {$result['bounces_synced']} bounces, detected {$result['disposables_detected']} disposable emails, and updated {$result['newly_suppressed']} suppression records.");
        }

        return redirect()->back()->with('error_message', 'Synchronization error: ' . ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Manually add an email to the suppression list.
     */
    public function addSuppression(Request $request)
    {
        $request->validate([
            'email' => 'required|email:filter|max:255',
            'reason' => 'required|in:hard_bounce,complaint,manual_unsubscribe,admin_suppressed,disposable',
        ]);

        $email = strtolower(trim($request->input('email')));
        $reason = $request->input('reason');

        EmailSuppression::suppress($email, $reason, 'admin_manual', [
            'added_by_admin_id' => auth()->id(),
            'notes' => $request->input('notes', 'Manually suppressed by admin'),
        ]);

        // Sync flags
        User::where('email', $email)->update([
            'marketing_opt_in' => 0,
            'marketing_unsubscribed_at' => now(),
        ]);

        NewsletterSubscribers::where('email', $email)->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return redirect()->back()->with('success_message', "Email address {$email} has been suppressed.");
    }

    /**
     * Remove an email from the suppression list (unblock).
     */
    public function removeSuppression($id)
    {
        $suppression = EmailSuppression::findOrFail($id);
        $email = $suppression->email;
        $suppression->delete();

        return redirect()->back()->with('success_message', "Email address {$email} was removed from suppression. It can now receive emails again.");
    }

    /**
     * Send a single live test weekly prompt drop to an admin email address.
     */
    public function sendTestWeeklyDrop(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email:filter|max:255',
        ]);

        $testEmail = strtolower(trim($request->input('test_email')));

        $range = \App\Services\WeeklyPhotoshootDigestService::getWeeklyDateRange();
        $photoshoots = \App\Services\WeeklyPhotoshootDigestService::getEligiblePhotoshoots($range['start'], $range['end'], false);

        // If no new photoshoots this specific week, fallback to latest 3 active photoshoots for the test preview
        if ($photoshoots->isEmpty()) {
            $photoshoots = \App\Models\Photoshoot::where('prompts_count', '>', 0)
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->with([
                    'category',
                    'images' => function ($q) {
                        $q->where('status', 'active')->orderBy('id', 'asc');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->unique('id');
        }

        $bodyContent = \App\Services\WeeklyPhotoshootDigestService::renderDigestBody($photoshoots, $range);

        $settings = AdminSettings::first();
        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $senderEmail = $settings->email_sender_address_updates ?? 'hello@updates.imaginebuddy.com';
        $senderName = $settings->email_sender_name_updates ?? (($settings->title ?? 'Imagine Buddy') . ' Updates');
        $replyTo = $settings->email_reply_to_updates ?? 'support@imaginebuddy.com';
        $footerText = $settings->email_updates_footer_text;

        $recipientName = 'Admin Test';
        $token = base64_encode($testEmail);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);

        $firstTitle = $photoshoots->isNotEmpty() ? $photoshoots->first()->title : 'New Photoshoots';
        $count = $photoshoots->count();
        $subject = $count <= 1
            ? "📸 New Studio Photoshoot: {$firstTitle}"
            : "📸 New Photoshoots: {$firstTitle} + " . ($count - 1) . " More Sets";

        $personalizedBody = str_replace(
            ['{name}', '{email}', '{unsubscribe_url}', '{vault_url}', '{vaultUrl}'],
            [htmlspecialchars($recipientName), htmlspecialchars($testEmail), $unsubscribeUrl, url('photoshoots'), url('photoshoots')],
            $bodyContent
        );

        $html = view('emails.layouts.master-updates', [
            'settings' => $settings,
            'subject' => $subject,
            'bodyContent' => $personalizedBody,
            'previewText' => "Explore this week's newly published commercial studio photoshoots and multi-angle prompts.",
            'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
            'footerText' => $footerText,
            'unsubscribeUrl' => $unsubscribeUrl,
        ])->render();

        \Illuminate\Support\Facades\Mail::mailer('ses-marketing')->send([], [], function ($message) use ($testEmail, $senderEmail, $senderName, $replyTo, $subject, $html, $unsubscribeUrl) {
            $message->to($testEmail)
                ->from($senderEmail, $senderName)
                ->subject('[TEST PREVIEW] ' . $subject)
                ->html($html);

            if (!empty($replyTo)) {
                $message->replyTo($replyTo);
            }

            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click')
                ->addTextHeader('X-SES-MESSAGE-TAGS', 'CampaignType=WeeklyDigestTest');
        });

        return redirect()->back()->with('success_message', "Live test photoshoot digest ({$count} photoshoot sets) dispatched to {$testEmail} via Amazon SES!");
    }
}
