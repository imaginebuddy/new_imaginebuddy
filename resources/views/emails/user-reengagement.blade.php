<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>We Miss You on {{ $settings->title }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; }
        .feature-box { background-color: #1e293b; border-radius: 8px; border: 1px solid #334155; padding: 16px; margin: 16px 0; font-size: 14px; line-height: 1.6; color: #cbd5e1; }
        .btn-main { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); text-decoration: none; border-radius: 8px; text-align: center; margin: 20px 0; }
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
                    A lot has changed since your last visit ✨
                </h1>
                <p style="font-size: 14px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
                    Hi {{ $recipient->name ?: 'there' }}, we noticed it's been a while since you browsed Imagine Buddy. Our team has added hundreds of new hyper-realistic commercial product photoshoot prompts designed specifically for the latest generation of AI image models (Google Gemini, ChatGPT, and Midjourney).
                </p>

                <div class="feature-box">
                    <strong style="color: #f8fafc;">What's New in Your Account:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #94a3b8;">
                        <li>1-Click prompt copying directly to clipboard</li>
                        <li>Fresh lighting and studio camera angle presets</li>
                        <li>Over 500+ free prompts ready to test instantly</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ url('prompts/free') }}" class="btn-main" target="_blank">
                        See What's New on Imagine Buddy &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. You are receiving this because you have an account on Imagine Buddy.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe immediately with 1 click</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
