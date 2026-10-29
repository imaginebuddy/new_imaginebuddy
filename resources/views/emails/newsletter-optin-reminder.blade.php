<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Reminder: Your Free Prompts Are Waiting - {{ $settings->title ?? 'Imagine Buddy' }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 36px 32px; }
        .btn { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); text-decoration: none; border-radius: 8px; text-align: center; margin: 24px 0; }
        .badge { display: inline-block; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #a5b4fc; background-color: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 9999px; margin-bottom: 16px; }
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
                        {{ $settings->title ?? 'Imagine Buddy' }}
                    </a>
                </td>
            </tr>
        </table>

        <div class="main">
            <div class="content">
                <span class="badge">Friendly 24-Hour Reminder</span>
                <h1 style="margin-top: 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                    Your 50+ Curated AI Photoshoot Prompts are waiting! 🎁
                </h1>
                <p style="font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
                    We noticed you requested access to our <strong>50+ Curated Master AI Prompts Cheat Sheet</strong>, but haven't confirmed your email address yet.
                </p>
                <p style="font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
                    With just one quick click below, you'll verify your inbox and immediately unlock the prompts vault plus our weekly photoshoot drops:
                </p>

                <div style="text-align: center;">
                    <a href="{{ $confirmUrl }}" class="btn" target="_blank">
                        Confirm &amp; Unlock Free Prompts Vault &rarr;
                    </a>
                </div>

                <p style="font-size: 13px; line-height: 1.5; color: #94a3b8; margin-top: 24px;">
                    If the button above does not work, copy and paste this link into your browser:<br>
                    <a href="{{ $confirmUrl }}" style="color: #818cf8; word-break: break-all;">{{ $confirmUrl }}</a>
                </p>

                <p style="font-size: 12px; line-height: 1.5; color: #64748b; margin-top: 28px; border-top: 1px solid #1e293b; padding-top: 16px;">
                    This is the only reminder you will receive. If you did not sign up or changed your mind, you can simply ignore this message or click below to cancel.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title ?? 'Imagine Buddy' }}. All rights reserved.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Instant Unsubscribe</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a> &bull;
                <a href="{{ url('page/terms-and-conditions') }}">Terms and Conditions</a>
            </p>
        </div>
    </div>
</body>
</html>
