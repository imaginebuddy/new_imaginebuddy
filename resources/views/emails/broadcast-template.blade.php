<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $campaign->subject }}</title>
    @if(!empty($campaign->preview_text))
    <div style="display:none; font-size:1px; color:#333333; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">
        {{ $campaign->preview_text }}
    </div>
    @endif
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 620px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; line-height: 1.6; font-size: 15px; color: #cbd5e1; }
        .content h1, .content h2, .content h3 { color: #f8fafc; }
        .content a { color: #00d690; text-decoration: underline; }
        .content p { margin-bottom: 16px; }
        .btn-custom { display: inline-block; padding: 12px 28px; font-size: 15px; font-weight: 700; color: #041b11 !important; background: #00d690; text-decoration: none !important; border-radius: 50px; text-align: center; margin: 16px 0; }
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
                {!! $renderedContent !!}
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. All rights reserved.</p>
            <p>
                You are receiving this broadcast because you have an account or active newsletter subscription on Imagine Buddy.<br>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe instantly from marketing broadcasts</a> &bull;
                <a href="{{ url('page/terms-and-conditions') }}">Terms</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
