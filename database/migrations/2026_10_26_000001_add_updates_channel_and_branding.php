<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add channel column to email_templates table
        if (Schema::hasTable('email_templates')) {
            Schema::table('email_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('email_templates', 'channel')) {
                    $table->string('channel', 32)->default('auth')->after('category')->index();
                }
            });

            // Ensure all existing templates are marked as auth channel
            DB::table('email_templates')->whereNull('channel')->orWhere('channel', '')->update(['channel' => 'auth']);
        }

        // 2. Add updates email branding & sender columns to admin_settings
        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_settings', 'email_sender_name_updates')) {
                    $table->string('email_sender_name_updates', 128)->default('Imagine Buddy Updates')->after('email_sender_address_auth');
                }
                if (!Schema::hasColumn('admin_settings', 'email_sender_address_updates')) {
                    $table->string('email_sender_address_updates', 128)->default('hello@updates.imaginebuddy.com')->after('email_sender_name_updates');
                }
                if (!Schema::hasColumn('admin_settings', 'email_reply_to_updates')) {
                    $table->string('email_reply_to_updates', 128)->default('support@imaginebuddy.com')->after('email_sender_address_updates');
                }
                if (!Schema::hasColumn('admin_settings', 'email_updates_primary_color')) {
                    $table->string('email_updates_primary_color', 32)->default('#4f46e5')->after('email_reply_to_updates');
                }
                if (!Schema::hasColumn('admin_settings', 'email_updates_button_color')) {
                    $table->string('email_updates_button_color', 32)->default('#0f172a')->after('email_updates_primary_color');
                }
                if (!Schema::hasColumn('admin_settings', 'email_updates_footer_text')) {
                    $table->text('email_updates_footer_text')->nullable()->after('email_updates_button_color');
                }
            });
        }

        // 3. Seed Light-Mode Templates for updates.imaginebuddy.com
        $now = now();
        $updatesTemplates = [
            [
                'slug' => 'broadcast-campaign',
                'category' => 'marketing',
                'channel' => 'updates',
                'name' => 'Broadcast Marketing Campaign',
                'description' => 'Default master layout for bulk marketing announcements, prompt drops, and feature dispatches.',
                'subject' => '{campaign_subject}',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>{heading}</h2>
<p>Hi {name},</p>
<p>{body_message}</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">{action_text}</a>
</div>
<p style="color: #64748b; font-size: 13px;">If you have any questions or ideas for new prompt vaults, simply reply directly to this email.</p>',
                'content_plain' => "Hi {name},\n\n{body_message}\n\nExplore here: {action_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{campaign_subject}', '{heading}', '{body_message}', '{action_url}', '{action_text}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'newsletter-double-opt-in',
                'category' => 'newsletters',
                'channel' => 'updates',
                'name' => 'Newsletter Double Opt-In Confirmation',
                'description' => 'Dispatched when a visitor subscribes via site forms to confirm their email address.',
                'subject' => 'Confirm your subscription to {app_name} (and unlock 50+ AI Prompts)',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>Almost there! Confirm your subscription 👋</h2>
<p>Hi there,</p>
<p>Thank you for subscribing to Imagine Buddy updates! Please click the button below to confirm your subscription and immediately unlock your <strong>50+ AI Prompts Master Vault</strong>:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{confirm_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Confirm Subscription &amp; Unlock Vault &rarr;</a>
</div>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 20px 0; font-size: 13px; color: #64748b;">
    <strong>What you will receive:</strong> Weekly commercial photography prompt recipes, trending Midjourney &amp; ChatGPT studio keywords, and early access to new AI photoshoot tools.
</div>
<p style="color: #64748b; font-size: 13px;">If you did not request this newsletter subscription, you can safely ignore this email.</p>',
                'content_plain' => "Confirm your subscription to {app_name}!\n\nPlease visit the link below to confirm and unlock your 50+ AI Prompts Master Vault:\n{confirm_url}\n\nIf you did not request this, you can ignore this email.",
                'variables' => json_encode(['{confirm_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'newsletter-welcome',
                'category' => 'newsletters',
                'channel' => 'updates',
                'name' => 'Newsletter Welcome & 50+ Prompts Vault',
                'description' => 'Sent immediately after subscription confirmation with access to the free prompt vault.',
                'subject' => 'Welcome! Here is your 50+ AI Prompts Master Vault 🎁',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>Welcome to the Creator Community! 🎉</h2>
<p>Hi {name},</p>
<p>Your subscription is officially confirmed! As promised, here is your instant access to our curated <strong>50+ Commercial AI Photoshoot Prompts Vault</strong>:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vault_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Access Free Prompts Vault &rarr;</a>
</div>
<div style="background-color: #f8fafc; border-left: 4px solid {primary_color}; padding: 18px; border-radius: 6px; margin: 24px 0;">
    <h3 style="margin-top: 0; font-size: 15px; color: #0f172a;">💡 Quick Studio Tip for Commercial Realism:</h3>
    <p style="margin-bottom: 0; font-size: 13px; color: #475569; line-height: 1.6;">
        Instead of generic words like "photorealistic", always specify camera optics: e.g. <code>85mm prime lens, f/2.0 aperture, soft studio strobe, high-key lighting</code>. Every prompt in your vault includes exact commercial parameters!
    </p>
</div>
<p style="color: #64748b; font-size: 13px;">Watch your inbox every week for fresh photoshoot recipes and prompt updates.</p>',
                'content_plain' => "Welcome to {app_name}!\n\nHere is your 50+ Commercial AI Photoshoot Prompts Vault:\n{vault_url}\n\nEnjoy creating!\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{vault_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'weekly-prompt-drop',
                'category' => 'newsletters',
                'channel' => 'updates',
                'name' => 'Weekly Prompt Drop Digest',
                'description' => 'Automated weekly newsletter featuring top staff-curated AI photoshoot recipes.',
                'subject' => 'This Week\'s Top Curated AI Prompts & Photoshoot Recipes 📸',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: {primary_color}; letter-spacing: 1px; margin-bottom: 8px;">
    Weekly Creator Dispatch &bull; {current_date}
</div>
<h2>Fresh AI Photoshoot Prompts Ready to Copy 📸</h2>
<p>Hi {name},</p>
<p>Here are this week\'s highest-converting, staff-tested commercial photography prompts. Copy and paste them directly into Midjourney, ChatGPT, or Gemini:</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h3 style="margin-top: 0; font-size: 16px; color: #0f172a;">1. Luxury Perfume Obsidian Reflection</h3>
    <div style="font-family: \'Courier New\', Courier, monospace; font-size: 13px; color: #0f172a; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; margin: 12px 0; line-height: 1.5; word-break: break-word;">
        Luxury fragrance bottle on polished black marble with subtle water droplets, dramatic rim lighting, soft volumetric haze, 85mm lens, f/1.8 --ar 16:9 --style raw
    </div>
    <a href="{vault_url}" style="color: {primary_color}; font-size: 13px; font-weight: 600; text-decoration: underline;">Copy Full Prompt Recipe &rarr;</a>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vault_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Browse All Trending Prompts &rarr;</a>
</div>',
                'content_plain' => "This Week's Top Curated AI Prompts & Photoshoot Recipes\n\nHi {name},\n\nBrowse this week's trending prompt recipes here:\n{vault_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{vault_url}', '{current_date}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'welcome-drip-day3',
                'category' => 'lifecycle',
                'channel' => 'updates',
                'name' => 'Welcome Drip (Day 3 Onboarding)',
                'description' => 'Dispatched 72 hours post-signup guiding users to create their first commercial photoshoot.',
                'subject' => 'How to create your first commercial photoshoot in 60 seconds 🚀',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>Level up your AI visuals in 3 easy steps 🚀</h2>
<p>Hi {name},</p>
<p>It has been a few days since you joined Imagine Buddy! Did you know that thousands of e-commerce brands and creative agencies use our prompt recipes to produce entire product catalogs without a physical studio?</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <ol style="margin: 0; padding-left: 20px; line-height: 1.8; color: #334155;">
        <li><strong>Pick a category:</strong> Choose from Cosmetics, Fashion, Jewelry, Tech, or Food photography.</li>
        <li><strong>Copy the calibrated recipe:</strong> Complete with tested camera lenses, lighting ratios, and aspect ratios.</li>
        <li><strong>Generate &amp; sell:</strong> 100% royalty-free commercial rights included with every generation.</li>
    </ol>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{action_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Start Generating Now &rarr;</a>
</div>',
                'content_plain' => "Hi {name},\n\nLevel up your AI visuals in 3 easy steps.\n\nStart generating: {action_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{action_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'abandoned-checkout',
                'category' => 'lifecycle',
                'channel' => 'updates',
                'name' => 'Abandoned Checkout Recovery',
                'description' => 'Sent 2 hours after an incomplete subscription checkout offering plan benefits.',
                'subject' => 'Did you leave something behind? Complete your {app_name} upgrade ✨',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>Complete your upgrade &amp; unlock unlimited AI power ✨</h2>
<p>Hi {name},</p>
<p>We noticed you started upgrading to the <strong>{plan_name}</strong> plan on {app_name}, but didn\'t finish checking out.</p>
<p>Your creator workspace is ready and waiting with these perks:</p>
<ul style="padding-left: 20px; color: #334155; line-height: 1.8;">
    <li><strong>Unlimited prompt generations:</strong> Never hit limits during photoshoots.</li>
    <li><strong>High-resolution asset downloads:</strong> Full commercial licensing included.</li>
    <li><strong>VIP prompt vaults:</strong> Access restricted enterprise photoshoot recipes.</li>
</ul>
<div style="text-align: center; margin: 30px 0;">
    <a href="{checkout_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Complete Your Upgrade &rarr;</a>
</div>
<p style="color: #64748b; font-size: 13px;">Need assistance with checkout or have billing questions? Simply reply to this email and our team will help you right away.</p>',
                'content_plain' => "Hi {name},\n\nComplete your upgrade to {plan_name} on {app_name}:\n{checkout_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{plan_name}', '{checkout_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'user-reengagement',
                'category' => 'lifecycle',
                'channel' => 'updates',
                'name' => 'Inactive User Re-Engagement',
                'description' => 'Sent to users inactive for 14+ days showcasing newly added photoshoot styles.',
                'subject' => 'We miss you! Explore new AI photoshoot prompts on {app_name} ✨',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>We’ve added 100+ new prompts since your last visit! ✨</h2>
<p>Hi {name},</p>
<p>We noticed it’s been a little while since your last photoshoot session on {app_name}. Our studio team has just published a brand-new collection of trending photography recipes:</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <ul style="margin: 0; padding-left: 20px; line-height: 1.8; color: #334155;">
        <li><strong>Golden Hour Streetwear:</strong> Warm film tones with natural lens flares.</li>
        <li><strong>Minimalist Cosmetic Flatlays:</strong> Clean pastel geometry and sharp macro focus.</li>
        <li><strong>Cyberpunk Tech Product Mockups:</strong> Neon backlights and reflective acrylic stands.</li>
    </ul>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vault_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Discover What’s New &rarr;</a>
</div>',
                'content_plain' => "Hi {name},\n\nWe miss you! Explore 100+ new commercial AI photoshoot prompts on {app_name}:\n{vault_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{vault_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'quota-refilled',
                'category' => 'notifications',
                'channel' => 'updates',
                'name' => 'Monthly Quota Refilled Alert',
                'description' => 'Sent when a paying subscriber\'s monthly generation or download limits refresh.',
                'subject' => 'Your monthly photoshoot generation quota has been refilled! 🚀',
                'sender_name' => 'Imagine Buddy Updates',
                'sender_email' => 'hello@updates.imaginebuddy.com',
                'reply_to' => 'support@imaginebuddy.com',
                'content_html' => '<h2>Your fresh monthly quota is here! 🚀</h2>
<p>Hi {name},</p>
<p>Great news! Your monthly download and prompt generation limits have successfully refilled. You now have <strong>{quota_amount} fresh credits</strong> ready to use in your creator studio.</p>
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: center;">
    <span style="font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 6px;">Available Studio Generations</span>
    <span style="font-size: 32px; font-weight: 800; color: #0f172a;">{quota_amount}</span>
</div>
<div style="text-align: center; margin: 30px 0;">
    <a href="{vault_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Open Studio &amp; Start Creating &rarr;</a>
</div>',
                'content_plain' => "Hi {name},\n\nYour monthly photoshoot generation quota has refilled with {quota_amount} fresh credits!\n\nOpen Studio: {vault_url}\n\nUnsubscribe: {unsubscribe_url}",
                'variables' => json_encode(['{name}', '{quota_amount}', '{vault_url}', '{unsubscribe_url}', '{app_name}']),
                'is_enabled' => true,
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($updatesTemplates as $tmpl) {
            DB::table('email_templates')->updateOrInsert(['slug' => $tmpl['slug']], $tmpl);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('email_templates')) {
            DB::table('email_templates')->where('channel', 'updates')->delete();

            Schema::table('email_templates', function (Blueprint $table) {
                if (Schema::hasColumn('email_templates', 'channel')) {
                    $table->dropColumn('channel');
                }
            });
        }

        if (Schema::hasTable('admin_settings')) {
            Schema::table('admin_settings', function (Blueprint $table) {
                $cols = [
                    'email_sender_name_updates',
                    'email_sender_address_updates',
                    'email_reply_to_updates',
                    'email_updates_primary_color',
                    'email_updates_button_color',
                    'email_updates_footer_text',
                ];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('admin_settings', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
