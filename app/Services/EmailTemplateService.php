<?php

namespace App\Services;

use App\Models\AdminSettings;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class EmailTemplateService
{
    /**
     * Render a dynamic email template by slug.
     * If the template is missing, disabled, or an exception occurs,
     * it safely falls back to the provided fallback callable.
     */
    public static function render(string $slug, array $data = [], ?callable $fallback = null): array
    {
        $settings = AdminSettings::first();

        try {
            $template = EmailTemplate::where('slug', $slug)->first();

            if ($template && $template->is_enabled) {
                // 1. Prepare dynamic variable map
                $sampleData = $template->getSampleVariables();
                $finalData = array_merge($sampleData, $data);

                // Global system tokens
                $finalData['{app_name}'] = $settings->title ?? 'Imagine Buddy';
                if (!isset($finalData['{name}']) && isset($data['name'])) {
                    $finalData['{name}'] = $data['name'];
                }

                // 2. Parse subject & body
                $parsedSubject = strtr($template->subject, $finalData);
                $parsedBody = strtr($template->content_html, $finalData);
                $parsedPlain = $template->content_plain ? strtr($template->content_plain, $finalData) : strip_tags($parsedBody);

                // 3. Sender configuration (Per-template override > Global Auth Setting > Default)
                $defaultAuthSender = $settings->email_sender_address_auth ?? 'noreply@auth.imaginebuddy.com';
                $defaultAuthName = $settings->email_sender_name_auth ?? ($settings->title . ' Security');

                $senderEmail = !empty($template->sender_email) ? $template->sender_email : $defaultAuthSender;
                $senderName = !empty($template->sender_name) ? $template->sender_name : $defaultAuthName;
                $replyTo = $template->reply_to ?: null;

                // 4. Wrap within master light layout
                $html = View::make('emails.layouts.master', [
                    'settings' => $settings,
                    'subject' => $parsedSubject,
                    'bodyContent' => $parsedBody,
                    'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
                    'primaryColor' => $settings->email_primary_color ?? '#4f46e5',
                    'footerText' => $settings->email_footer_text,
                ])->render();

                return [
                    'used_custom' => true,
                    'subject' => $parsedSubject,
                    'html' => $html,
                    'plain' => $parsedPlain,
                    'sender_name' => $senderName,
                    'sender_email' => $senderEmail,
                    'reply_to' => $replyTo,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Failed to render custom email template [{$slug}], falling back to default.", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Safe Fallback Execution
        if (is_callable($fallback)) {
            return $fallback();
        }

        return [
            'used_custom' => false,
            'subject' => ($settings->title ?? 'Imagine Buddy') . ' Notification',
            'html' => '',
            'plain' => '',
            'sender_name' => $settings->email_sender_name_auth ?? 'Imagine Buddy Security',
            'sender_email' => $settings->email_sender_address_auth ?? 'noreply@auth.imaginebuddy.com',
            'reply_to' => null,
        ];
    }

    /**
     * Generate HTML for Admin Live Device Previews using sample variables.
     */
    public static function preview(EmailTemplate $template, array $overrides = []): string
    {
        $settings = AdminSettings::first();
        $sampleData = array_merge($template->getSampleVariables(), $overrides);

        $parsedSubject = strtr($template->subject, $sampleData);
        $parsedBody = strtr($template->content_html, $sampleData);

        return View::make('emails.layouts.master', [
            'settings' => $settings,
            'subject' => $parsedSubject,
            'bodyContent' => $parsedBody,
            'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
            'primaryColor' => $settings->email_primary_color ?? '#4f46e5',
            'footerText' => $settings->email_footer_text,
        ])->render();
    }
}
