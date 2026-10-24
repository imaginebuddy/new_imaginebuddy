<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Welcome to {{ $settings->title }} - Here is your Master Vault</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 36px 32px; }
        .btn { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background: linear-gradient(135deg, #10b981 0%, #059669 100%); text-decoration: none; border-radius: 8px; text-align: center; margin: 24px 0; }
        .tip-box { background-color: #1e293b; border-left: 4px solid #6366f1; padding: 16px; border-radius: 6px; margin: 20px 0; font-size: 14px; line-height: 1.6; color: #cbd5e1; }
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
                    You are in! Welcome to the Imagine Buddy Community &bull;
                </h1>
                <p style="font-size: 15px; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px;">
                    Your subscription is confirmed. You now have full access to our curated prompts, commercial photoshoot recipes, and weekly AI inspiration.
                </p>

                <div style="text-align: center;">
                    <a href="{{ $vaultUrl }}" class="btn" target="_blank">
                        Access Free Master Prompts Vault &rarr;
                    </a>
                </div>

                <div class="tip-box">
                    <strong style="color: #f8fafc;">Pro Tip for Best AI Product Photoshoots:</strong><br>
                    Combine <em>85mm lens, f/1.8 aperture, studio softbox rim lighting</em> with your product subject to achieve instant photorealistic luxury aesthetics in Google Gemini, ChatGPT, and Midjourney.
                </div>

                <p style="font-size: 14px; line-height: 1.6; color: #cbd5e1; margin-top: 20px;">
                    Every week, we curate high-converting prompts tested across real e-commerce and creative workflows. No fluff, just production-ready prompts you can copy and use immediately.
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
