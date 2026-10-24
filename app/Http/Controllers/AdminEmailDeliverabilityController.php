<?php

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Models\Images;
use App\Models\NewsletterSubscribers;
use App\Models\User;
use App\Notifications\WeeklyPromptDropNotification;
use App\Services\AudienceService;
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
        $suppressionsBounces = EmailSuppression::where('reason', 'hard_bounce')->count();
        $suppressionsComplaints = EmailSuppression::where('reason', 'complaint')->count();
        $suppressionsUnsubscribes = EmailSuppression::where('reason', 'manual_unsubscribe')->count();
        $suppressionsAdmin = EmailSuppression::where('reason', 'admin_suppressed')->count();

        // 3. Tab Data
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
            'suppressionsAdmin' => $suppressionsAdmin,
            'suppressions' => $suppressions,
            'subscribers' => $data,
        ]);
    }

    /**
     * Manually add an email to the suppression list.
     */
    public function addSuppression(Request $request)
    {
        $request->validate([
            'email' => 'required|email:filter|max:255',
            'reason' => 'required|in:hard_bounce,complaint,manual_unsubscribe,admin_suppressed',
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

        $prompts = Images::where('status', 'active')
            ->whereNotNull('prompt')
            ->orderBy('date', 'desc')
            ->take(3)
            ->get(['id', 'title', 'prompt', 'description', 'thumbnail']);

        $fakeRecipient = (object) [
            'email' => $testEmail,
            'name' => 'Admin Test',
            'unsubscribe_token' => base64_encode($testEmail),
        ];

        Notification::route('mail', $testEmail)
            ->notify(new WeeklyPromptDropNotification($fakeRecipient, $prompts));

        return redirect()->back()->with('success_message', "Test weekly prompt drop email dispatched to {$testEmail} via Amazon SES!");
    }
}
