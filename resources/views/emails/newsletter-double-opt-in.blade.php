<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Confirm Your Subscription - {{ $settings->title }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 36px 32px; }
        .btn { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); text-decoration: none; border-radius: 8px; text-align: center; margin: 24px 0; }
        .footer { padding: 24px 32px; text-align: center; font-size: 12px; color: #64748b; line-height: 1.6; }
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
                    Confirm your subscription & unlock your 50+ AI Prompts
                </h1>
                <p style="font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
                    Thank you for signing up for the Imagine Buddy weekly newsletter! To verify that this email address belongs to you and unlock instant access to our <strong>50+ Curated Master AI Prompts Cheat Sheet</strong>, please confirm below:
                </p>

                <div style="text-align: center;">
                    <a href="{{ $confirmUrl }}" class="btn" target="_blank">
                        Confirm Subscription & Unlock Prompts &rarr;
                    </a>
                </div>

                <p style="font-size: 13px; line-height: 1.5; color: #94a3b8; margin-top: 24px;">
                    If the button above does not work, copy and paste this link into your browser:<br>
                    <a href="{{ $confirmUrl }}" style="color: #818cf8; word-break: break-all;">{{ $confirmUrl }}</a>
                </p>

                <p style="font-size: 12px; line-height: 1.5; color: #64748b; margin-top: 28px; border-top: 1px solid #1e293b; padding-top: 16px;">
                    If you did not request this newsletter subscription, you can safely ignore this message. You will not be emailed again unless you click the confirmation link above.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. All rights reserved.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Instant Unsubscribe</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a> &bull;
                <a href="{{ url('page/terms-and-conditions') }}">Terms and Conditions</a>
            </p>
        </div>
    </div>
</body>
</html>
