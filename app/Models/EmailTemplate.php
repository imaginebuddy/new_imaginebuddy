<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'email_templates';

    protected $fillable = [
        'slug',
        'category',
        'channel',
        'name',
        'description',
        'subject',
        'sender_name',
        'sender_email',
        'reply_to',
        'content_html',
        'content_plain',
        'variables',
        'is_enabled',
        'is_system',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_enabled' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Map of required variables per template slug to prevent administrators
     * from accidentally omitting critical tokens.
     */
    public static function requiredVariables(): array
    {
        return [
            // Auth channel templates (auth.imaginebuddy.com)
            'two-factor-code' => ['{code}'],
            'verify-account' => ['{action_url}'],
            'password-reset' => ['{action_url}'],
            'subscription-expiring' => ['{action_url}'],
            'payment-receipt' => ['{transaction_id}'],
            'admin-deposit-pending' => ['{admin_url}'],

            // Updates channel templates (updates.imaginebuddy.com)
            // Admins can freely customize links (e.g. use custom URLs or vault/photoshoots)
            'broadcast-campaign' => [],
            'newsletter-double-opt-in' => ['{confirm_url}'],
            'newsletter-opt-in-reminder' => ['{confirm_url}'],
            'newsletter-welcome' => [],
            'welcome-drip-day3' => [],
            'abandoned-checkout' => [],
            'user-reengagement' => [],
            'quota-refilled' => [],
        ];
    }

    /**
     * Realistic sample values for previewing and test dispatches.
     */
    public function getSampleVariables(): array
    {
        $settings = AdminSettings::first();
        $appName = $settings->title ?? 'Imagine Buddy';

        $isUpdates = ($this->channel ?? 'auth') === 'updates';
        $primaryColor = $isUpdates
            ? ($settings->email_updates_primary_color ?? ($settings->email_primary_color ?? '#4f46e5'))
            : ($settings->email_primary_color ?? '#4f46e5');

        $buttonColor = $isUpdates
            ? ($settings->email_updates_button_color ?? ($settings->email_button_color ?? '#0f172a'))
            : ($settings->email_button_color ?? '#0f172a');

        return [
            '{name}' => 'Alex Mercer',
            '{code}' => '849201',
            '{minutes}' => '10',
            '{expire_minutes}' => '60',
            '{app_name}' => $appName,
            '{action_url}' => url('verify/account/sample-token-preview-12345'),
            '{support_email}' => 'support@imaginebuddy.com',
            '{ip_address}' => '192.168.1.1',
            '{plan_name}' => 'Pro Creator Annual',
            '{expires_at}' => now()->addDays(3)->format('M d, Y'),
            '{transaction_id}' => 'TXN-9842104',
            '{amount}' => ($settings->currency_symbol ?? '$') . '49.00',
            '{payment_gateway}' => 'Stripe Credit Card',
            '{date}' => now()->format('M d, Y H:i'),
            '{user_name}' => 'Sarah Jenkins',
            '{user_email}' => 'sarah.creator@example.com',
            '{admin_url}' => url('panel/admin/deposits'),
            '{primary_color}' => $primaryColor,
            '{button_color}' => $buttonColor,

            // Updates & Marketing Tokens
            '{unsubscribe_url}' => route('newsletter.unsubscribe', ['token' => 'sample-test-token-123']),
            '{vault_url}' => url('prompts/free'),
            '{confirm_url}' => route('newsletter.confirm', ['token' => 'sample-test-token-123']),
            '{checkout_url}' => url('plans'),
            '{quota_amount}' => '150',
            '{current_date}' => now()->format('M d, Y'),
            '{campaign_subject}' => '🔥 3 New AI Product Photoshoot Prompts Ready to Copy',
            '{heading}' => 'This Week’s Top Curated Prompts 📸',
            '{body_message}' => 'Here are the highest-converting AI photography prompts our team tested this week. Copy and paste them directly into your generator of choice:',
            '{action_text}' => 'Explore In Prompt Vault &rarr;',
        ];
    }

    /**
     * Replace placeholders in text with provided data.
     */
    public function replaceVariables(string $text, array $data): string
    {
        $samples = $this->getSampleVariables();
        $merged = array_merge($samples, $data);

        return strtr($text, $merged);
    }
}
