<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Complete Your Upgrade - {{ $settings->title }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; line-height: 1.6; font-size: 15px; color: #cbd5e1; }
        .plan-card { background: #1e293b; border-radius: 8px; border: 1px solid #334155; padding: 18px; margin: 20px 0; }
        .btn-custom { display: inline-block; padding: 14px 34px; font-size: 15px; font-weight: 700; color: #041b11 !important; background: #00d690; text-decoration: none !important; border-radius: 50px; text-align: center; margin: 16px 0; }
        .footer { padding: 24px 28px; text-align: center; font-size: 12px; color: #64748b; line-height: 1.6; }
        .footer a { color: #94a3b8; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding: 24px 0;">
            <tr>
                <td align="center">
                    <a href="{{ url('/') }}" style="text-decoration: none; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: -0.5px;">
                        {{ $settings->title }}
                    </a>
                </td>
            </tr>
        </table>

        <div class="main">
            <div class="content">
                <h1 style="margin-top: 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                    Did you leave something behind? 🛒
                </h1>
                <p>
                    Hi {{ $user->name ?: $user->username }}, we noticed you were about to upgrade your Imagine Buddy account{{ $plan ? ' to the ' . $plan->name : '' }} but didn’t complete your checkout.
                </p>

                <div class="plan-card">
                    <strong style="color: #f8fafc; font-size: 16px;">What you unlock with Imagine Buddy Pro:</strong>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #cbd5e1; line-height: 1.8;">
                        <li><strong>Unlimited Commercial License:</strong> Safe for client campaigns, Amazon/Shopify stores, and social ads.</li>
                        <li><strong>Full Studio Photoshoot Vaults:</strong> Access thousands of tested product prompts.</li>
                        <li><strong>Full Resolution Downloads:</strong> Uncompressed assets without watermarks.</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ $checkoutUrl }}" class="btn-custom" target="_blank">
                        Resume &amp; Complete Your Upgrade &rarr;
                    </a>
                </div>

                <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 14px;">
                    Have questions or need assistance? Reply directly to this email and our team will help you.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. All rights reserved.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe immediately with 1 click</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
