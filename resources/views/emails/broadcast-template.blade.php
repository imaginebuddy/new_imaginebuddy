<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $campaign->subject }}</title>
    @if(!empty($campaign->preview_text))
    <div style="display:none; font-size:1px; color:#333333; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">
        {{ $campaign->preview_text }}
    </div>
    @endif
    <style type="text/css">
        :root { color-scheme: light; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #f1f5f9 !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a !important; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); overflow: hidden; }
        .header { padding: 32px 32px 24px 32px; text-align: center; border-bottom: 1px solid #f1f5f9; background-color: #ffffff; }
        .content { padding: 32px; font-size: 15px; line-height: 1.65; color: #334155; }
        .content h1, .content h2, .content h3 { color: #0f172a; margin-top: 0; font-weight: 700; }
        .content a { color: {{ $settings->email_updates_primary_color ?? '#4f46e5' }}; text-decoration: underline; }
        .content p { margin-bottom: 16px; }
        .btn-custom, .btn-action { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background-color: {{ $settings->email_updates_button_color ?? '#0f172a' }}; text-decoration: none !important; border-radius: 8px; text-align: center; margin: 16px 0; }
        .footer { padding: 24px 32px 32px 32px; text-align: center; font-size: 12px; line-height: 1.5; color: #64748b; background-color: #f8fafc; border-top: 1px solid #e2e8f0; }
        .footer a { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body style="background-color: #f1f5f9; margin: 0; padding: 0;">
    <div class="wrapper">
        <center>
            <table role="presentation" class="main" cellpadding="0" cellspacing="0" style="background-color: #ffffff; width: 100%; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 12px;">
                <tr>
                    <td class="header" style="text-align: center; padding: 32px 32px 24px 32px; border-bottom: 1px solid #f1f5f9;">
                        <a href="{{ url('/') }}" target="_blank" style="text-decoration: none; display: inline-block;">
                            @if(!empty($settings->email_logo))
                                <img src="{{ url($settings->email_logo) }}" alt="{{ $settings->title ?? 'Imagine Buddy' }}" style="max-height: 40px; width: auto; max-width: 220px; display: block; margin: 0 auto;">
                            @elseif(!empty($settings->logo))
                                <img src="{{ url('public/img', $settings->logo) }}" alt="{{ $settings->title ?? 'Imagine Buddy' }}" style="max-height: 40px; width: auto; max-width: 220px; display: block; margin: 0 auto;">
                            @else
                                <span style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                    {{ $settings->title ?? 'Imagine Buddy' }}
                                </span>
                            @endif
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="content" style="padding: 32px; font-size: 15px; line-height: 1.65; color: #334155;">
                        {!! $renderedContent !!}
                    </td>
                </tr>

                <tr>
                    <td class="footer" style="padding: 24px 32px 32px 32px; text-align: center; font-size: 12px; color: #64748b; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                        <p style="margin: 0 0 8px 0;">&copy; {{ date('Y') }} {{ $settings->title ?? 'Imagine Buddy' }}. All rights reserved.</p>
                        <p style="margin: 0 0 8px 0;">
                            You are receiving this broadcast because you have an account or active newsletter subscription on Imagine Buddy.
                        </p>
                        <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                            <a href="{{ $unsubscribeUrl }}" style="color: #64748b; text-decoration: underline;">Unsubscribe instantly from marketing broadcasts</a> &bull;
                            <a href="{{ url('page/terms-and-conditions') }}" style="color: #94a3b8;">Terms</a> &bull;
                            <a href="{{ url('page/privacy') }}" style="color: #94a3b8;">Privacy Policy</a>
                        </p>
                    </td>
                </tr>
            </table>
        </center>
    </div>
</body>
</html>
