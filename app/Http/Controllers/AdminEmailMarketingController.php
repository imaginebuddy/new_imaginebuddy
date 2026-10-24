<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use App\Models\EmailCampaign;
use App\Notifications\BroadcastCampaignNotification;
use App\Services\AudienceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminEmailMarketingController extends Controller
{
    /**
     * List all marketing campaigns & kill-switch status.
     */
    public function index()
    {
        $killSwitchActive = AdminSettings::isBulkEmailKillSwitchActive();
        $campaigns = EmailCampaign::with('author')->latest()->paginate(15);

        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();
        $totalCampaignsSent = EmailCampaign::where('status', 'sent')->count();
        $totalEmailsDispatched = EmailCampaign::sum('sent_count');

        return view('admin.marketing-campaigns', [
            'killSwitchActive' => $killSwitchActive,
            'campaigns' => $campaigns,
            'totalAudience' => $totalAudience,
            'totalCampaignsSent' => $totalCampaignsSent,
            'totalEmailsDispatched' => $totalEmailsDispatched,
        ]);
    }

    /**
     * Show the visual campaign composer.
     */
    public function create()
    {
        $killSwitchActive = AdminSettings::isBulkEmailKillSwitchActive();
        $totalAudience = AudienceService::getDeliverableMarketingAudience()->count();

        // Template Presets
        $presets = [
            'prompts_showcase' => [
                'name' => 'AI Prompts Showcase',
                'description' => 'Featured prompt recipes, parameter copy-boxes, and explore buttons.',
                'subject' => '🔥 3 New AI Product Photoshoot Prompts Ready to Copy',
                'preview' => 'Copy studio-tested prompts for Midjourney, ChatGPT & Gemini.',
                'html' => '<h2>This Week’s Top Curated Prompts 📸</h2>
<p>Hi {name}, here are the highest-converting AI photography prompts our team tested this week. Copy and paste them directly into your generator:</p>
<div style="background:#1e293b; border:1px solid #334155; border-radius:8px; padding:16px; margin:20px 0;">
    <h3 style="margin-top:0; color:#f8fafc;">1. Luxury Perfume Obsidian Reflection</h3>
    <p style="font-family:monospace; background:#0f172a; padding:12px; border-radius:6px; color:#cbd5e1; font-size:13px;">Luxury fragrance bottle on polished black marble with subtle water droplets, dramatic rim lighting, soft volumetric haze, 85mm lens, f/1.8 --ar 16:9 --style raw</p>
    <p><a href="{vaultUrl}" class="btn-custom" style="padding:10px 24px;">Explore In Prompt Vault &rarr;</a></p>
</div>
<p>Want more commercial styles? Access our full library of 1,000+ free prompts on Imagine Buddy.</p>',
            ],
            'announcement' => [
                'name' => 'Product / Feature Announcement',
                'description' => 'Clean hero header, feature bullet points, and CTA button.',
                'subject' => '✨ Big Update: New AI Photoshoot Adaptability Engine',
                'preview' => 'Transform any product into a high-end commercial photoshoot.',
                'html' => '<h2>Introducing Our New AI Photoshoot Engine ✨</h2>
<p>Hi {name},</p>
<p>We are thrilled to announce a major update to Imagine Buddy! We have just released the Product Photoshoot Suite designed to help creators and brands generate commercial catalog photos in seconds.</p>
<ul style="padding-left:20px; color:#cbd5e1; line-height:1.8;">
    <li><strong>1-Click Prompt Copying:</strong> Formatted for Midjourney v6, ChatGPT, and Gemini.</li>
    <li><strong>Lighting Presets:</strong> Studio softbox, sunset golden hour, and cyberpunk neon.</li>
    <li><strong>Commercial License:</strong> 100% royalty-free for client and e-commerce use.</li>
</ul>
<p style="text-align:center; margin:30px 0;">
    <a href="{vaultUrl}" class="btn-custom">Try the New Photoshoot Tools &rarr;</a>
</p>',
            ],
            'promo_discount' => [
                'name' => 'Promotional Discount / Sale',
                'description' => 'Offer banner, coupon code callout box, and countdown urgency.',
                'subject' => '⚡ Limited Flash Sale: 40% Off Imagine Buddy Pro Plans',
                'preview' => 'Unlock unlimited commercial prompts and high-res downloads.',
                'html' => '<div style="text-align:center;">
    <span style="background:#00d690; color:#041b11; font-weight:700; padding:6px 14px; border-radius:50px; font-size:12px; text-transform:uppercase;">Limited Time Flash Sale</span>
    <h2 style="font-size:26px; margin:16px 0;">Upgrade to Pro with 40% Off</h2>
    <p>Unlock unlimited commercial downloads, priority photoshoot generation, and exclusive VIP prompt vaults.</p>
    <div style="background:#1e293b; border:2px dashed #00d690; border-radius:8px; padding:18px; margin:24px auto; max-width:320px;">
        <span style="font-size:12px; color:#94a3b8; display:block; text-transform:uppercase;">Use Coupon Code:</span>
        <strong style="font-size:22px; color:#00d690; letter-spacing:2px;">PROMPT40</strong>
    </div>
    <a href="' . url('plans') . '" class="btn-custom" style="font-size:16px; padding:14px 36px;">Claim 40% Discount Now &rarr;</a>
</div>',
            ],
            'plain_text' => [
                'name' => 'Minimalist Letter (High Deliverability)',
                'description' => 'Clean letter-style with zero heavy markup for maximum inbox placement.',
                'subject' => 'Quick tip for realistic AI product photography',
                'preview' => 'The one prompt keyword that changes everything.',
                'html' => '<p>Hi {name},</p>
<p>A quick tip from the studio team at Imagine Buddy:</p>
<p>When generating product mockups in Midjourney or ChatGPT, always specify your camera focal length and aperture rather than using words like "photorealistic" or "hyper-detailed".</p>
<p>For example, appending <code>85mm portrait lens, f/2.0 aperture, soft studio strobe</code> produces clean cinematic depth-of-field that immediately looks commercial and professional.</p>
<p>We’ve added 50+ new examples with these exact camera settings on the platform today.</p>
<p><a href="{vaultUrl}">You can view them free on Imagine Buddy right here &rarr;</a></p>
<p>Best regards,<br>The Imagine Buddy Team</p>',
            ],
        ];

        return view('admin.marketing-campaign-create', [
            'killSwitchActive' => $killSwitchActive,
            'totalAudience' => $totalAudience,
            'presets' => $presets,
        ]);
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
     * Send live test preview email to admin.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email:filter',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $testEmail = strtolower(trim($request->input('test_email')));

        $fakeCampaign = new EmailCampaign([
            'name' => 'Admin Live Test Preview',
            'subject' => '[TEST PREVIEW] ' . $request->input('subject'),
            'preview_text' => $request->input('preview_text'),
            'content' => $request->input('content'),
        ]);

        $fakeRecipient = (object) [
            'email' => $testEmail,
            'name' => auth()->user()->name ?? 'Admin',
            'unsubscribe_token' => base64_encode($testEmail),
        ];

        Notification::route('mail', $testEmail)
            ->notify(new BroadcastCampaignNotification($fakeRecipient, $fakeCampaign));

        return response()->json([
            'status' => 'success',
            'message' => "Live test preview dispatched to {$testEmail} through Amazon SES!",
        ]);
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
            'status' => $action === 'send_now' ? 'sending' : 'draft',
            'created_by' => auth()->id(),
        ]);

        if ($action === 'send_now') {
            $audience = AudienceService::getSegmentAudience($campaign->target_segment);
            $total = $audience->count();

            if ($total === 0) {
                $campaign->update(['status' => 'draft']);
                return redirect()->back()->with('error_message', 'No eligible recipients found in this segment to dispatch.');
            }

            $campaign->update([
                'recipients_count' => $total,
                'status' => 'sending',
            ]);

            // Dispatch in throttled chunks to emails-bulk queue
            $dispatchedCount = 0;
            foreach ($audience->chunk(50) as $chunk) {
                foreach ($chunk as $recipient) {
                    Notification::route('mail', $recipient->email)
                        ->notify(new BroadcastCampaignNotification($recipient, $campaign));
                    $dispatchedCount++;
                }
                usleep(100000); // 100ms throttle between batches
            }

            $campaign->update([
                'status' => 'sent',
                'sent_count' => $dispatchedCount,
                'sent_at' => now(),
            ]);

            Log::info("Broadcast campaign '{$campaign->name}' queued for delivery.", [
                'campaign_id' => $campaign->id,
                'recipients' => $dispatchedCount,
            ]);

            return redirect('panel/admin/email-marketing/campaigns')->with('success_message', "Campaign '{$campaign->name}' successfully queued for {$dispatchedCount} recipients!");
        }

        return redirect('panel/admin/email-marketing/campaigns')->with('success_message', "Campaign draft '{$campaign->name}' saved successfully.");
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

            Log::emergency('EMERGENCY KILL-SWITCH ENGAGED by Admin ID: ' . auth()->id() . '. All pending bulk emails purged.');

            return redirect()->back()->with('success_message', '🛑 EMERGENCY KILL-SWITCH ENGAGED! Bulk email sending has been immediately halted, and pending bulk queue jobs have been purged.');
        }

        Log::info('Bulk email kill-switch released by Admin ID: ' . auth()->id());

        return redirect()->back()->with('success_message', '✅ Bulk email sending has been resumed.');
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
}
