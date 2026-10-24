<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>3 Studio Lighting Secrets - {{ $settings->title }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; line-height: 1.6; font-size: 15px; color: #cbd5e1; }
        .tip-card { background: #1e293b; border-left: 4px solid #00d690; padding: 14px 18px; border-radius: 6px; margin: 16px 0; font-size: 14px; }
        .code-box { font-family: monospace; background: #0f172a; padding: 10px 14px; border-radius: 6px; color: #38bdf8; font-size: 13px; margin-top: 8px; display: block; word-break: break-all; }
        .btn-custom { display: inline-block; padding: 12px 28px; font-size: 15px; font-weight: 700; color: #041b11 !important; background: #00d690; text-decoration: none !important; border-radius: 50px; text-align: center; margin: 20px 0; }
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
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #00d690; letter-spacing: 1px; margin-bottom: 6px;">
                    Creator Masterclass &bull; Day 3 Drip
                </div>
                <h1 style="margin-top: 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                    3 studio lighting secrets for photorealistic AI mockups
                </h1>
                <p>
                    Hi {{ $recipient->name ?: 'Creator' }}, hope you’ve had a chance to check out our 50+ Master Prompts vault!
                </p>
                <p>
                    Today, we wanted to share the 3 lighting keywords that professional photographers and prompt creators use to make AI images look like real studio catalog shoots:
                </p>

                <div class="tip-card">
                    <strong style="color: #f8fafc;">1. The Edge Separator (Rim Lighting)</strong><br>
                    Without rim lighting, dark products blend into dark backgrounds. Add this:
                    <span class="code-box">soft rim lighting on product edges, dark slate background --ar 16:9</span>
                </div>

                <div class="tip-card">
                    <strong style="color: #f8fafc;">2. The Softbox Glow (Volumetric Diffusion)</strong><br>
                    Harsh white highlights look fake. Softbox diffusion creates silky reflections:
                    <span class="code-box">studio softbox diffusion, subtle gradient highlight, Hasselblad 85mm</span>
                </div>

                <div class="tip-card">
                    <strong style="color: #f8fafc;">3. Physical Texture Anchors</strong><br>
                    Anchor floating products to real textures like water droplets, natural stone, or linen cloth:
                    <span class="code-box">resting on wet obsidian stone, crisp condensation droplets, shallow depth of field</span>
                </div>

                <div style="text-align: center;">
                    <a href="{{ $vaultUrl }}" class="btn-custom" target="_blank">
                        Test These Styles in Free Vault &rarr;
                    </a>
                </div>
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
