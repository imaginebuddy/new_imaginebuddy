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
            'two-factor-code' => ['{code}'],
            'verify-account' => ['{action_url}'],
            'password-reset' => ['{action_url}'],
            'subscription-expiring' => ['{action_url}'],
            'payment-receipt' => ['{transaction_id}'],
            'admin-deposit-pending' => ['{admin_url}'],
        ];
    }

    /**
     * Realistic sample values for previewing and test dispatches.
     */
    public function getSampleVariables(): array
    {
        $settings = AdminSettings::first();
        $appName = $settings->title ?? 'Imagine Buddy';

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
