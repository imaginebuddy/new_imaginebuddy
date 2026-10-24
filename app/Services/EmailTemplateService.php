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
                $primaryColor = $settings->email_primary_color ?? '#4f46e5';
                $buttonColor = $settings->email_button_color ?? '#0f172a';

                // 1. Prepare dynamic variable map
                $sampleData = $template->getSampleVariables();
                $finalData = array_merge($sampleData, $data);

                // Global system tokens
                $finalData['{app_name}'] = $settings->title ?? 'Imagine Buddy';
                $finalData['{primary_color}'] = $primaryColor;
                $finalData['{button_color}'] = $buttonColor;
                if (!isset($finalData['{name}']) && isset($data['name'])) {
                    $finalData['{name}'] = $data['name'];
                }

                // 2. Parse subject & body
                $parsedSubject = strtr($template->subject, $finalData);
                $parsedBody = strtr($template->content_html, $finalData);
                $parsedBody = self::applyBrandingToHtml($parsedBody, $buttonColor, $primaryColor);
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
                    'primaryColor' => $primaryColor,
                    'buttonColor' => $buttonColor,
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
        $primaryColor = $settings->email_primary_color ?? '#4f46e5';
        $buttonColor = $settings->email_button_color ?? '#0f172a';

        $sampleData = array_merge($template->getSampleVariables(), $overrides);
        $sampleData['{primary_color}'] = $primaryColor;
        $sampleData['{button_color}'] = $buttonColor;

        $parsedSubject = strtr($template->subject, $sampleData);
        $parsedBody = strtr($template->content_html, $sampleData);
        $parsedBody = self::applyBrandingToHtml($parsedBody, $buttonColor, $primaryColor);

        return View::make('emails.layouts.master', [
            'settings' => $settings,
            'subject' => $parsedSubject,
            'bodyContent' => $parsedBody,
            'logoUrl' => $settings->email_logo ? url($settings->email_logo) : null,
            'primaryColor' => $primaryColor,
            'buttonColor' => $buttonColor,
            'footerText' => $settings->email_footer_text,
        ])->render();
    }

    /**
     * Dynamically apply master branding colors (button background & link accents)
     * to the email HTML body, replacing both custom tokens and inline legacy defaults.
     */
    public static function applyBrandingToHtml(string $html, string $buttonColor, string $primaryColor): string
    {
        // 1. Replace explicit color tokens if present
        $html = str_replace(
            ['{button_color}', '{primary_color}'],
            [$buttonColor, $primaryColor],
            $html
        );

        // 2. Dynamically replace background-color on button-like <a> tags (e.g. style="... background-color: #...; ...")
        $html = preg_replace_callback(
            '/(<a\b[^>]*?style\s*=\s*["\'][^"\']*?background-color\s*:\s*)#[0-9a-fA-F]{3,6}([^"\']*?["\'][^>]*?>)/i',
            function ($matches) use ($buttonColor) {
                return $matches[1] . $buttonColor . $matches[2];
            },
            $html
        );

        // 3. Dynamically replace default #4f46e5 accent/link colors with master primary color
        $html = preg_replace(
            '/(color\s*:\s*)#4f46e5/i',
            '${1}' . $primaryColor,
            $html
        );

        return $html;
    }
}
