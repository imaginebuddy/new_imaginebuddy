<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignBatchJob;
use App\Models\AdminSettings;
use App\Models\CampaignRecipient;
use App\Models\EmailCampaign;
use App\Models\WeeklyDigestHistory;
use App\Notifications\BroadcastCampaignNotification;
use App\Services\AudienceService;
use App\Services\EmailTrackingService;
use App\Services\WeeklyPhotoshootDigestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AdminEmailMarketingController extends Controller
{
    /**
     * List all marketing campaigns & kill-switch status.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'campaigns');
        $killSwitchActive = AdminSettings::isBulkEmailKillSwitchActive();

        // 1. Self-heal and sync any Weekly Photoshoot Drop campaigns into WeeklyDigestHistory
        $weeklyDropCampaigns = EmailCampaign::where('template_preset', 'weekly_photoshoot_drop')
            ->orWhere('name', 'LIKE', 'Weekly Photoshoot Drop%')
            ->get();

        foreach ($weeklyDropCampaigns as $dropCampaign) {
            // If it crashed/failed with 0 recipients processed, mark its status as 'cancelled' (which renders as Stopped)
            if ($dropCampaign->status === 'sending' && $dropCampaign->recipients()->count() === 0) {
                $dropCampaign->update(['status' => 'cancelled']);
            }

            // Ensure a matching history entry exists in WeeklyDigestHistory
            $existingHistory = WeeklyDigestHistory::where('campaign_id', $dropCampaign->id)->first();
            if (!$existingHistory) {
                $range = WeeklyPhotoshootDigestService::getWeeklyDateRange();
                $periodKey = $range['period_key'] ?? now()->format('Y-m-d');
                WeeklyDigestHistory::updateOrCreate(
                    ['period_key' => $periodKey],
                    [
                        'campaign_id' => $dropCampaign->id,
                        'period_start' => $range['start'] ?? now()->subDays(7),
                        'period_end' => $range['end'] ?? now(),
                        'photoshoots_count' => 0,
                        'photoshoot_ids' => [],
                        'recipients_count' => $dropCampaign->recipients_count ?: 0,
                        'status' => $dropCampaign->status === 'sent' ? 'dispatched' : 'stopped',
                    ]
                );
            } elseif ($dropCampaign->status === 'cancelled' && $existingHistory->status !== 'stopped') {
                $existingHistory->update(['status' => 'stopped']);
            }
        }

        // 2. Broadcast Campaigns query: Exclude automated Weekly Photoshoot Drops so they stay in their dedicated tab
        $broadcastQuery = EmailCampaign::with('author')
            ->where(function ($q) {
                $q->whereNull('template_preset')
                  ->orWhere('template_preset', '!=', 'weekly_photoshoot_drop');
            })
            ->where('name', 'NOT LIKE', 'Weekly Photoshoot Drop%');

        $campaigns = $broadcastQuery->latest()->paginate(15)->appends(['tab' => 'campaigns']);

        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();
        $totalCampaignsSent = EmailCampaign::where(function ($q) {
                $q->whereNull('template_preset')
                  ->orWhere('template_preset', '!=', 'weekly_photoshoot_drop');
            })
            ->where('name', 'NOT LIKE', 'Weekly Photoshoot Drop%')
            ->where('status', 'sent')
            ->count();

        $totalEmailsDispatched = EmailCampaign::where(function ($q) {
                $q->whereNull('template_preset')
                  ->orWhere('template_preset', '!=', 'weekly_photoshoot_drop');
            })
            ->where('name', 'NOT LIKE', 'Weekly Photoshoot Drop%')
            ->sum('sent_count');

        // Weekly Photoshoot Drops History & Analytics
        $weeklyDrops = WeeklyDigestHistory::with('campaign')->latest('period_start')->paginate(15, ['*'], 'weekly_page')->appends(['tab' => 'weekly-drops']);
        $totalWeeklyDrops = WeeklyDigestHistory::count();
        $totalPhotoshootsFeatured = WeeklyDigestHistory::sum('photoshoots_count');
        $totalWeeklyRecipients = WeeklyDigestHistory::sum('recipients_count');

        // Calculate average delivery and open rates for weekly drops
        $dispatchedDrops = WeeklyDigestHistory::where('status', 'dispatched')->with('campaign')->get();
        $avgDeliveryRate = 0.0;
        $avgOpenRate = 0.0;
        $dropsWithCampaign = $dispatchedDrops->filter(fn($d) => $d->campaign && $d->campaign->sent_count > 0);
        if ($dropsWithCampaign->isNotEmpty()) {
            $avgDeliveryRate = round($dropsWithCampaign->avg(fn($d) => $d->campaign->delivery_rate), 1);
            $avgOpenRate = round($dropsWithCampaign->avg(fn($d) => $d->campaign->open_rate), 1);
        }

        return view('admin.marketing-campaigns', [
            'tab' => $tab,
            'killSwitchActive' => $killSwitchActive,
            'campaigns' => $campaigns,
            'totalAudience' => $totalAudience,
            'totalCampaignsSent' => $totalCampaignsSent,
            'totalEmailsDispatched' => $totalEmailsDispatched,
            'weeklyDrops' => $weeklyDrops,
            'totalWeeklyDrops' => $totalWeeklyDrops,
            'totalPhotoshootsFeatured' => $totalPhotoshootsFeatured,
            'totalWeeklyRecipients' => $totalWeeklyRecipients,
            'avgDeliveryRate' => $avgDeliveryRate,
            'avgOpenRate' => $avgOpenRate,
        ]);
    }

    /**
     * Show the visual campaign composer.
     */
    public function create()
    {
        $settings = AdminSettings::first();
        $killSwitchActive = AdminSettings::isBulkEmailKillSwitchActive();
        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();

        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $appName = $settings->title ?? 'Imagine Buddy';

        $presets = $this->getPresets($primaryColor, $buttonColor, $appName);

        return view('admin.marketing-campaign-create', [
            'killSwitchActive' => $killSwitchActive,
            'totalAudience' => $totalAudience,
            'presets' => $presets,
            'settings' => $settings,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
        ]);
    }

    /**
     * Show composer to edit or resume a draft campaign.
     */
    public function edit($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        $settings = AdminSettings::first();
        $killSwitchActive = AdminSettings::isBulkEmailKillSwitchActive();
        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();

        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $appName = $settings->title ?? 'Imagine Buddy';

        $presets = $this->getPresets($primaryColor, $buttonColor, $appName);

        return view('admin.marketing-campaign-create', [
            'campaign' => $campaign,
            'killSwitchActive' => $killSwitchActive,
            'totalAudience' => $totalAudience,
            'presets' => $presets,
            'settings' => $settings,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
        ]);
    }

    /**
     * Get presets list helper.
     */
    private function getPresets($primaryColor, $buttonColor, $appName)
    {
        return [
            'prompts_showcase' => [
                'name' => 'AI Prompts Showcase',
                'description' => 'Featured prompt recipes, parameter copy-boxes, and explore buttons.',
                'subject' => '🔥 3 New AI Product Photoshoot Prompts Ready to Copy',
                'preview' => 'Copy studio-tested prompts for Midjourney, ChatGPT & Gemini.',
                'html' => '<h2>This Week’s Top Curated Prompts 📸</h2>
<p>Hi {name},</p>
<p>Here are the highest-converting AI photography prompts our team tested this week. Copy and paste them directly into Midjourney, ChatGPT, or Gemini:</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; font-size: 16px; color: #0f172a;">1. Luxury Perfume Obsidian Reflection</h3>
    <div style="font-family: \'Courier New\', Courier, monospace; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; color: #0f172a; font-size: 13px; line-height: 1.5; margin: 12px 0;">
        Luxury fragrance bottle on polished black marble with subtle water droplets, dramatic rim lighting, soft volumetric haze, 85mm lens, f/1.8 --ar 16:9 --style raw
    </div>
    <a href="{vaultUrl}" style="color: ' . $primaryColor . '; font-size: 13px; font-weight: 600; text-decoration: underline;">Explore Full Recipe In Vault &rarr;</a>
</div>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; font-size: 16px; color: #0f172a;">2. Minimalist Ceramic Skincare Flatlay</h3>
    <div style="font-family: \'Courier New\', Courier, monospace; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; color: #0f172a; font-size: 13px; line-height: 1.5; margin: 12px 0;">
        Organic serum bottle on matte beige sandstone, pastel morning sunlight with delicate palm shadows, clean minimalist geometric aesthetic, Hasselblad 8k --ar 4:5 --style raw
    </div>
    <a href="{vaultUrl}" style="color: ' . $primaryColor . '; font-size: 13px; font-weight: 600; text-decoration: underline;">Explore Full Recipe In Vault &rarr;</a>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vaultUrl}" class="btn-action" style="background-color: ' . $buttonColor . '; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Browse All 1,000+ Free Prompts &rarr;</a>
</div>
<p style="color: #64748b; font-size: 13px;">Want more commercial styles? Access our full library of prompt recipes on Imagine Buddy.</p>',
            ],
            'announcement' => [
                'name' => 'Product / Feature Announcement',
                'description' => 'Clean hero header, feature bullet points, and CTA button.',
                'subject' => '✨ Big Update: New AI Photoshoot Suite is Live',
                'preview' => 'Transform any product into a high-end commercial photoshoot.',
                'html' => '<h2>Introducing Our New AI Photoshoot Suite ✨</h2>
<p>Hi {name},</p>
<p>We are thrilled to announce a major upgrade to Imagine Buddy! We have just released the Product Photoshoot Suite designed to help creators and brands generate commercial catalog photos in seconds.</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <ul style="margin: 0; padding-left: 20px; color: #334155; line-height: 1.8;">
        <li><strong>1-Click Prompt Copying:</strong> Pre-calibrated for Midjourney v6, ChatGPT, and Gemini.</li>
        <li><strong>Studio Lighting Presets:</strong> Softbox, golden hour, and cyberpunk neon recipes.</li>
        <li><strong>Commercial License:</strong> 100% royalty-free for client and e-commerce use.</li>
    </ul>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vaultUrl}" class="btn-action" style="background-color: ' . $buttonColor . '; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Try the New Photoshoot Tools &rarr;</a>
</div>
<p style="color: #64748b; font-size: 13px;">Have feedback or ideas for new prompt categories? Reply directly to this email—our team reads every response!</p>',
            ],
            'promo_discount' => [
                'name' => 'Promotional Discount / Sale',
                'description' => 'Offer banner, coupon code callout box, and countdown urgency.',
                'subject' => '⚡ Limited Flash Sale: 40% Off Imagine Buddy Pro Plans',
                'preview' => 'Unlock unlimited commercial prompts and high-res downloads.',
                'html' => '<div style="text-align: center;">
    <span style="background-color: #0f172a; color: #ffffff; font-weight: 700; padding: 6px 16px; border-radius: 50px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">Limited Time Flash Sale</span>
    <h2 style="font-size: 26px; margin: 18px 0; color: #0f172a;">Upgrade to Pro with 40% Off</h2>
    <p style="color: #475569; max-width: 500px; margin: 0 auto 20px auto; line-height: 1.6;">Unlock unlimited commercial downloads, priority photoshoot generation, and exclusive VIP prompt vaults.</p>
    <div style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 18px; margin: 24px auto; max-width: 320px;">
        <span style="font-size: 12px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600;">Use Coupon Code:</span>
        <strong style="font-size: 24px; color: #0f172a; letter-spacing: 2px;">PROMPT40</strong>
    </div>
    <div style="margin: 28px 0;">
        <a href="' . url('plans') . '" class="btn-action" style="background-color: ' . $buttonColor . '; color: #ffffff; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block;">Claim 40% Discount Now &rarr;</a>
    </div>
    <p style="color: #94a3b8; font-size: 12px;">Offer valid for the next 48 hours for new and renewing creators.</p>
</div>',
            ],
            'weekly_digest' => [
                'name' => 'Weekly Creator Digest',
                'description' => 'Weekly newsletter layout with date dispatch header and curated tips.',
                'subject' => 'This Week\'s Top Curated AI Prompts & Photoshoot Recipes 📸',
                'preview' => 'Fresh photoshoot prompts tested by the studio team.',
                'html' => '<div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: ' . $primaryColor . '; letter-spacing: 1px; margin-bottom: 8px;">
    Weekly Creator Dispatch &bull; ' . now()->format('M d, Y') . '
</div>
<h2>Fresh AI Photoshoot Prompts Ready to Copy 📸</h2>
<p>Hi {name},</p>
<p>Here are this week’s highest-converting, staff-tested commercial photography prompts. Copy and paste them directly into your generator of choice:</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; font-size: 16px; color: #0f172a;">1. High-Fashion Streetwear Studio Portrait</h3>
    <div style="font-family: \'Courier New\', Courier, monospace; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; color: #0f172a; font-size: 13px; line-height: 1.5; margin: 12px 0;">
        High-fashion model in oversized matte black trench coat, clean minimalist studio background, soft key light from 45 degrees, subtle rim light, 85mm prime lens, f/2.0 --ar 4:5 --style raw
    </div>
    <a href="{vaultUrl}" style="color: ' . $primaryColor . '; font-size: 13px; font-weight: 600; text-decoration: underline;">Copy Full Recipe In Vault &rarr;</a>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vaultUrl}" class="btn-action" style="background-color: ' . $buttonColor . '; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Browse All Trending Prompts &rarr;</a>
</div>',
            ],
            'plain_text' => [
                'name' => 'Minimalist Letter (High Deliverability)',
                'description' => 'Clean letter-style with zero heavy markup for maximum inbox placement.',
                'subject' => 'Quick tip for realistic AI product photography',
                'preview' => 'The one prompt keyword that changes everything.',
                'html' => '<p>Hi {name},</p>
<p>A quick tip from the studio team at ' . $appName . ':</p>
<p>When generating product mockups in Midjourney or ChatGPT, always specify your camera focal length and aperture rather than using words like "photorealistic" or "hyper-detailed".</p>
<div style="background-color: #f8fafc; border-left: 4px solid ' . $primaryColor . '; padding: 16px; border-radius: 6px; margin: 20px 0;">
    <p style="margin: 0; font-size: 14px; color: #334155; line-height: 1.6;">
        For example, appending <code>85mm portrait lens, f/2.0 aperture, soft studio strobe</code> produces clean cinematic depth-of-field that immediately looks commercial and professional.
    </p>
</div>
<p>We’ve added 50+ new examples with these exact camera settings on the platform today.</p>
<div style="margin: 24px 0;">
    <a href="{vaultUrl}" class="btn-action" style="background-color: ' . $buttonColor . '; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">View Free Prompts on ' . $appName . ' &rarr;</a>
</div>
<p>Best regards,<br>The ' . $appName . ' Team</p>',
            ],
        ];
    }

    /**
     * AJAX Segment Preview: count and sample recipients.
     */
    public function segmentPreview(Request $request)
    {
        $segment = $request->input('segment', 'all_verified');
        $audience = AudienceService::getSegmentAudience($segment);

        $sample = $audience->take(8)->map(function ($r) {
            return [
                'name' => $r->name ?: 'Creator',
                'email' => $r->email,
                'source' => $r->source ?? 'Google User',
                'joined_at' => $r->joined_at ?? 'N/A',
            ];
        })->values();

        return response()->json([
            'segment' => $segment,
            'count' => $audience->count(),
            'formatted_count' => number_format($audience->count()),
            'sample' => $sample,
        ]);
    }

    /**
     * Send live test preview email synchronously to admin via Amazon SES.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email:filter',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $testEmail = strtolower(trim($request->input('test_email')));
        $subject = trim($request->input('subject'));
        $content = $request->input('content');
        $previewText = $request->input('preview_text');

        $settings = AdminSettings::first();
        $primaryColor = $settings->email_updates_primary_color ?: ($settings->email_primary_color ?: '#4f46e5');
        $buttonColor = $settings->email_updates_button_color ?: ($settings->email_button_color ?: '#0f172a');
        $senderEmail = $settings->email_sender_address_updates ?? 'hello@updates.imaginebuddy.com';
        $senderName = $settings->email_sender_name_updates ?? (($settings->title ?? 'Imagine Buddy') . ' Updates');
        $replyTo = $settings->email_reply_to_updates ?? 'support@imaginebuddy.com';
        $footerText = $settings->email_updates_footer_text;

        $recipientName = auth()->user()->name ?? 'Admin';
        $token = base64_encode($testEmail);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $token]);
        $vaultUrl = url('prompts/free');

        // Personalize tags inside the campaign content body
        $bodyMessage = str_replace(
            ['{name}', '{email}', '{unsubscribe_url}', '{vault_url}', '{vaultUrl}'],
            [htmlspecialchars($recipientName), htmlspecialchars($testEmail), $unsubscribeUrl, $vaultUrl, $vaultUrl],
            $content
        );

        $html = view('emails.layouts.master-updates', [
            'settings' => $settings,
            'subject' => '[TEST PREVIEW] ' . $subject,
            'bodyContent' => $bodyMessage,
            'previewText' => $previewText,
            'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
            'footerText' => $footerText,
            'unsubscribeUrl' => $unsubscribeUrl,
        ])->render();

        try {
            Mail::mailer('ses-marketing')->send('emails.raw', ['content' => $html], function ($message) use ($subject, $senderEmail, $senderName, $replyTo, $testEmail, $unsubscribeUrl) {
                $message->from($senderEmail, $senderName)
                    ->to($testEmail)
                    ->subject('[TEST PREVIEW] ' . $subject);

                if (!empty($replyTo)) {
                    $message->replyTo($replyTo);
                }

                $message->getHeaders()
                    ->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>')
                    ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            });

            return response()->json([
                'status' => 'success',
                'message' => "Live test preview dispatched instantly to {$testEmail} through Amazon SES ({$senderEmail})!",
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to send campaign live test preview to {$testEmail}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => "SES delivery error: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save draft or dispatch broadcast campaign.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'subject' => 'required|max:255',
            'target_segment' => 'required|in:all_verified,google_users,newsletter_subscribers,paying_subscribers,test_list',
            'content' => 'required',
            'action' => 'required|in:save_draft,send_now',
        ]);

        $action = $request->input('action');

        // Check Kill Switch if sending now
        if ($action === 'send_now' && AdminSettings::isBulkEmailKillSwitchActive()) {
            return redirect()->back()->withInput()->with('error_message', 'Cannot send campaign: The Bulk Email Emergency Kill-Switch is currently ENGAGED. Please release the kill-switch first.');
        }

        $campaign = EmailCampaign::create([
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'preview_text' => $request->input('preview_text'),
            'target_segment' => $request->input('target_segment'),
            'template_preset' => $request->input('template_preset', 'prompts_showcase'),
            'content' => $request->input('content'),
            'track_opens' => $request->has('track_opens'),
            'track_clicks' => $request->has('track_clicks'),
            'status' => $action === 'send_now' ? 'sending' : 'draft',
            'created_by' => auth()->id(),
        ]);

        if ($action === 'send_now') {
            return $this->dispatchCampaign($campaign);
        }

        return redirect('panel/admin/email-marketing/campaigns')->with('success_message', "Campaign draft '{$campaign->name}' saved successfully.");
    }

    /**
     * Update draft or dispatch the edited broadcast campaign.
     */
    public function update(Request $request, $id)
    {
        $campaign = EmailCampaign::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'subject' => 'required|max:255',
            'target_segment' => 'required|in:all_verified,google_users,newsletter_subscribers,paying_subscribers,test_list',
            'content' => 'required',
            'action' => 'required|in:save_draft,send_now',
        ]);

        $action = $request->input('action');

        if ($action === 'send_now' && AdminSettings::isBulkEmailKillSwitchActive()) {
            return redirect()->back()->withInput()->with('error_message', 'Cannot send campaign: The Bulk Email Emergency Kill-Switch is currently ENGAGED. Please release the kill-switch first.');
        }

        $campaign->update([
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'preview_text' => $request->input('preview_text'),
            'target_segment' => $request->input('target_segment'),
            'template_preset' => $request->input('template_preset', $campaign->template_preset),
            'content' => $request->input('content'),
            'track_opens' => $request->has('track_opens'),
            'track_clicks' => $request->has('track_clicks'),
        ]);

        if ($action === 'send_now') {
            return $this->dispatchCampaign($campaign);
        }

        return redirect('panel/admin/email-marketing/campaigns')->with('success_message', "Campaign draft '{$campaign->name}' updated successfully.");
    }

    /**
     * Direct 1-click dispatch for an existing draft campaign from the listing page.
     */
    public function sendNow($id)
    {
        $campaign = EmailCampaign::findOrFail($id);

        if ($campaign->status === 'sent') {
            return redirect()->back()->with('error_message', "Campaign '{$campaign->name}' has already been dispatched.");
        }

        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return redirect()->back()->with('error_message', 'Cannot send campaign: The Bulk Email Emergency Kill-Switch is currently ENGAGED. Please release the kill-switch first.');
        }

        return $this->dispatchCampaign($campaign);
    }

    /**
     * Helper to dispatch campaign across audience using high-performance batching.
     */
    private function dispatchCampaign(EmailCampaign $campaign)
    {
        $audience = AudienceService::getSegmentAudience($campaign->target_segment);
        $total = $audience->count();

        if ($total === 0) {
            $campaign->update(['status' => 'draft']);
            return redirect()->back()->with('error_message', 'No eligible recipients found in this segment to dispatch.');
        }

        $campaign->update([
            'recipients_count' => $total,
            'processed_count' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'delivered_count' => 0,
            'bounced_count' => 0,
            'complained_count' => 0,
            'status' => 'sending',
            'sent_at' => now(),
        ]);

        // Clean any old recipient logs if re-dispatching
        $campaign->recipients()->delete();

        // 1. Bulk insert all recipients in fast chunks of 300 (takes ~0.3s for 1,794 recipients)
        $now = now()->toDateTimeString();
        $recipientRows = [];
        foreach ($audience as $recipient) {
            $recipientRows[] = [
                'campaign_id' => $campaign->id,
                'recipient_email' => strtolower(trim($recipient->email)),
                'recipient_name' => $recipient->name ?? null,
                'tracking_token' => EmailTrackingService::generateToken(),
                'status' => 'queued',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($recipientRows, 300) as $chunkRows) {
            CampaignRecipient::insert($chunkRows);
        }

        // 2. Fetch the newly inserted IDs and dispatch jobs in batches of 50
        $recipientIds = CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', 'queued')
            ->pluck('id')
            ->toArray();

        foreach (array_chunk($recipientIds, 50) as $batchIds) {
            SendCampaignBatchJob::dispatch($campaign->id, $batchIds);
        }

        // 3. Trigger a background queue worker automatically so delivery begins immediately
        $this->spawnQueueWorker();

        Log::info("Broadcast campaign '{$campaign->name}' queued for delivery.", [
            'campaign_id' => $campaign->id,
            'recipients' => $total,
            'batches' => ceil(count($recipientIds) / 50),
        ]);

        return redirect('panel/admin/email-marketing/campaigns')->with('success_message', "Campaign '{$campaign->name}' is now broadcasting to {$total} recipients! Real-time delivery progress is active below.");
    }

    /**
     * Trigger detached background queue worker if not already running.
     */
    private function spawnQueueWorker(): void
    {
        try {
            $artisan = base_path('artisan');
            $php = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" queue:work --queue=emails-bulk,default --stop-when-empty --tries=2 > NUL 2>&1", 'r'));
            } else {
                exec("\"{$php}\" \"{$artisan}\" queue:work --queue=emails-bulk,default --stop-when-empty --tries=2 > /dev/null 2>&1 &");
            }
        } catch (\Throwable $e) {
            Log::warning('Unable to spawn background queue worker: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to poll live sending progress.
     */
    public function progress($id)
    {
        $campaign = EmailCampaign::findOrFail($id);

        return response()->json([
            'id' => $campaign->id,
            'status' => $campaign->status,
            'recipients_count' => $campaign->recipients_count,
            'processed_count' => $campaign->processed_count,
            'sent_count' => $campaign->sent_count,
            'delivered_count' => $campaign->delivered_count,
            'bounced_count' => $campaign->bounced_count,
            'failed_count' => $campaign->failed_count,
            'unique_opens_count' => $campaign->unique_opens_count,
            'unique_clicks_count' => $campaign->unique_clicks_count,
            'progress_percentage' => $campaign->progress_percentage,
            'is_completed' => in_array($campaign->status, ['sent', 'cancelled', 'draft']),
        ]);
    }

    /**
     * Show detailed Campaign Analytics & Recipient Delivery Log.
     */
    public function analytics(Request $request, $id, $status = null)
    {
        $campaign = EmailCampaign::with('author')->findOrFail($id);

        $statusFilter = $status ?: $request->input('status', 'all');
        $search = trim($request->input('q', ''));

        $recipientsQuery = $campaign->recipients()->latest();

        if ($statusFilter !== 'all') {
            if ($statusFilter === 'opened') {
                $recipientsQuery->where('open_count', '>', 0);
            } elseif ($statusFilter === 'clicked') {
                $recipientsQuery->where('click_count', '>', 0);
            } elseif ($statusFilter === 'bounced' || $statusFilter === 'hard_bounced') {
                $recipientsQuery->whereIn('status', ['hard_bounced', 'soft_bounced']);
            } else {
                $recipientsQuery->where('status', $statusFilter);
            }
        }

        if (!empty($search)) {
            $recipientsQuery->where(function ($q) use ($search) {
                $q->where('recipient_email', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_name', 'LIKE', "%{$search}%");
            });
        }

        $recipients = $recipientsQuery->paginate(25)->withQueryString();

        // Top clicked links
        $topClicks = $campaign->clicks()
            ->selectRaw('target_url, count(*) as clicks_count, count(distinct recipient_id) as unique_clicks')
            ->groupBy('target_url')
            ->orderByDesc('clicks_count')
            ->take(10)
            ->get();

        return view('admin.marketing-campaign-analytics', [
            'campaign' => $campaign,
            'recipients' => $recipients,
            'topClicks' => $topClicks,
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    /**
     * Re-dispatch failed recipients for a campaign using batch jobs.
     */
    public function retryFailed($id)
    {
        $campaign = EmailCampaign::findOrFail($id);

        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return redirect()->back()->with('error_message', 'Cannot retry sending: The Bulk Email Emergency Kill-Switch is currently active.');
        }

        $failedRecipientIds = $campaign->recipients()->where('status', 'failed')->pluck('id')->toArray();

        if (empty($failedRecipientIds)) {
            return redirect()->back()->with('error_message', 'No failed recipients found to retry for this campaign.');
        }

        CampaignRecipient::whereIn('id', $failedRecipientIds)->update([
            'status' => 'queued',
            'error_message' => null,
        ]);

        foreach (array_chunk($failedRecipientIds, 50) as $batchIds) {
            SendCampaignBatchJob::dispatch($campaign->id, $batchIds);
        }

        $this->spawnQueueWorker();

        $campaign->update(['status' => 'sending']);

        return redirect()->back()->with('success_message', "Successfully re-queued " . count($failedRecipientIds) . " previously failed recipients!");
    }

    /**
     * Emergency Kill-Switch Toggle.
     */
    public function toggleKillSwitch(Request $request)
    {
        $activate = (bool) $request->input('activate', true);
        AdminSettings::setBulkEmailKillSwitch($activate);

        if ($activate) {
            // Instantly clear pending bulk queue jobs
            DB::table('jobs')->where('queue', 'emails-bulk')->delete();

            // Transition all actively sending campaigns to halted/cancelled
            EmailCampaign::where('status', 'sending')->update([
                'status' => 'cancelled',
            ]);

            // Revert in-flight recipient statuses back to queued
            CampaignRecipient::where('status', 'sending')->update([
                'status' => 'queued',
            ]);

            Log::emergency('EMERGENCY KILL-SWITCH ENGAGED by Admin ID: ' . auth()->id() . '. All bulk emails purged and campaigns halted.');

            return redirect()->back()->with('success_message', '🛑 EMERGENCY KILL-SWITCH ENGAGED! Bulk email sending has been immediately halted, campaigns stopped, and pending queue jobs purged.');
        }

        Log::info('Bulk email kill-switch released by Admin ID: ' . auth()->id());

        return redirect()->back()->with('success_message', '✅ Bulk email sending resumed. You can now resume any halted campaigns.');
    }

    /**
     * Pause an actively sending campaign.
     */
    public function pauseCampaign($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        $campaign->update(['status' => 'cancelled']);

        CampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', 'sending')
            ->update(['status' => 'queued']);

        return redirect()->back()->with('success_message', "Campaign '{$campaign->name}' paused.");
    }

    /**
     * Resume a paused/cancelled campaign for remaining queued recipients.
     */
    public function resumeCampaign($id)
    {
        if (AdminSettings::isBulkEmailKillSwitchActive()) {
            return redirect()->back()->with('error_message', 'Cannot resume sending: The Bulk Email Emergency Kill-Switch is currently ENGAGED. Please release the kill-switch first.');
        }

        $campaign = EmailCampaign::findOrFail($id);

        $queuedRecipientIds = $campaign->recipients()
            ->where('status', 'queued')
            ->pluck('id')
            ->toArray();

        if (empty($queuedRecipientIds)) {
            $campaign->update([
                'status' => 'sent',
                'completed_at' => now(),
            ]);
            return redirect()->back()->with('success_message', "All recipients for campaign '{$campaign->name}' have already been sent!");
        }

        $campaign->update([
            'status' => 'sending',
        ]);

        foreach (array_chunk($queuedRecipientIds, 50) as $batchIds) {
            SendCampaignBatchJob::dispatch($campaign->id, $batchIds);
        }

        $this->spawnQueueWorker();

        return redirect()->back()->with('success_message', "Resumed broadcasting '{$campaign->name}' for the remaining " . count($queuedRecipientIds) . " recipients!");
    }

    /**
     * Mark a partially sent or paused campaign as fully completed.
     */
    public function markCompleted($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        $campaign->update([
            'status' => 'sent',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success_message', "Campaign '{$campaign->name}' marked as completed.");
    }

    /**
     * Delete a campaign record.
     */
    public function destroy($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->back()->with('success_message', 'Campaign record deleted successfully.');
    }

    /**
     * Redirect or render dedicated weekly drops history tab.
     */
    public function weeklyDropsHistory(Request $request)
    {
        return redirect()->route('email_campaigns', ['tab' => 'weekly-drops']);
    }

    /**
     * Delete all Weekly Photoshoot Drop history records safely.
     * Only affects WeeklyDigestHistory and any stopped/incomplete weekly drop campaigns.
     */
    public function deleteAllWeeklyDropsHistory(Request $request)
    {
        $count = WeeklyDigestHistory::count();

        if ($count === 0) {
            return redirect()->route('email_campaigns', ['tab' => 'weekly-drops'])
                ->with('info_message', 'Weekly photoshoot drop history is already empty.');
        }

        // Clean up any stopped/cancelled/empty weekly drop campaigns so they do not resurrect
        $weeklyDropCampaignIds = WeeklyDigestHistory::pluck('campaign_id')->filter()->toArray();
        if (!empty($weeklyDropCampaignIds)) {
            $campaignsToDelete = EmailCampaign::whereIn('id', $weeklyDropCampaignIds)
                ->where(function ($q) {
                    $q->whereIn('status', ['stopped', 'cancelled'])
                      ->orWhere('sent_count', 0);
                })
                ->get();

            foreach ($campaignsToDelete as $camp) {
                $camp->recipients()->delete();
                $camp->delete();
            }
        }

        // Truncate or delete all records from weekly_digest_history safely
        WeeklyDigestHistory::truncate();

        \Log::info("Admin cleared all Weekly Photoshoot Drop history ({$count} records removed).");

        return redirect()->route('email_campaigns', ['tab' => 'weekly-drops'])
            ->with('success_message', "Successfully deleted all {$count} weekly photoshoot drop history records.");
    }

    /**
     * Delete a single Weekly Photoshoot Drop history record.
     */
    public function deleteWeeklyDropHistory($id)
    {
        $history = WeeklyDigestHistory::findOrFail($id);
        $period = $history->period_label;
        $campaignId = $history->campaign_id;

        $history->delete();

        // If the associated campaign was stopped, cancelled, or has 0 delivered recipients, delete it too so it does not resurrect
        if ($campaignId) {
            $campaign = EmailCampaign::find($campaignId);
            if ($campaign && (in_array($campaign->status, ['stopped', 'cancelled']) || $campaign->sent_count === 0)) {
                $campaign->recipients()->delete();
                $campaign->delete();
            }
        }

        return redirect()->route('email_campaigns', ['tab' => 'weekly-drops'])
            ->with('success_message', "Weekly drop history for {$period} deleted successfully.");
    }
}
