<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('email_templates')) {
            $exists = EmailTemplate::where('slug', 'newsletter-opt-in-reminder')->exists();
            if (!$exists) {
                $now = now();
                EmailTemplate::create([
                    'slug' => 'newsletter-opt-in-reminder',
                    'category' => 'newsletters',
                    'channel' => 'updates',
                    'name' => 'Newsletter 24h Opt-In Reminder',
                    'description' => 'Automated reminder sent 24 hours after sign-up to visitors who have not confirmed their email.',
                    'subject' => 'Reminder: Your 50+ free studio photoshoot prompts are waiting 🎁',
                    'sender_name' => 'Imagine Buddy Updates',
                    'sender_email' => 'hello@updates.imaginebuddy.com',
                    'reply_to' => 'support@imaginebuddy.com',
                    'content_html' => '<div style="display: inline-block; background-color: #ede9fe; color: #6366f1; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 9999px; margin-bottom: 12px;">Friendly 24-Hour Reminder</div>
<h2>Your 50+ Curated AI Prompts are waiting! 🎁</h2>
<p>Hi there,</p>
<p>We noticed you requested our <strong>50+ Curated Master AI Prompts Cheat Sheet</strong>, but haven\'t confirmed your email address yet.</p>
<p>With just one quick click below, you\'ll verify your inbox and immediately unlock the prompts vault plus our weekly photoshoot drops:</p>
<div style="text-align: center; margin: 30px 0;">
    <a href="{confirm_url}" class="btn-action" style="background-color: {button_color}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">Confirm &amp; Unlock Prompts Vault &rarr;</a>
</div>
<div style="background-color: #f8fafc; border-left: 4px solid {primary_color}; padding: 16px; border-radius: 6px; margin: 20px 0; font-size: 13px; color: #475569;">
    <strong>Note:</strong> This is the only reminder you will receive. If you changed your mind, you can safely ignore this email.
</div>',
                    'content_plain' => "Hi there,\n\nYour 50+ Curated Master AI Prompts Cheat Sheet is waiting!\n\nPlease visit the link below to confirm your subscription and immediately unlock the vault:\n{confirm_url}\n\nThis is the only reminder you will receive. If you did not request this, you can ignore this email.",
                    'variables' => ['{confirm_url}', '{unsubscribe_url}', '{app_name}'],
                    'is_enabled' => true,
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('email_templates')) {
            EmailTemplate::where('slug', 'newsletter-opt-in-reminder')->delete();
        }
    }
};
