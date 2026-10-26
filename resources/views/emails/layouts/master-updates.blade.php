<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $subject ?? ($settings->title ?? 'Imagine Buddy') }}</title>
    @if(!empty($previewText))
    <div style="display:none; font-size:1px; color:#f1f5f9; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">
        {{ $previewText }}
    </div>
    @endif
    <style type="text/css">
        :root { color-scheme: light; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f1f5f9 !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a !important; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0; }
        .main-card { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); overflow: hidden; }
        .header { padding: 32px 32px 24px 32px; text-align: center; border-bottom: 1px solid #f1f5f9; background-color: #ffffff; }
        .content { padding: 32px; font-size: 15px; line-height: 1.65; color: #334155; }
        .content h1, .content h2, .content h3 { color: #0f172a; margin-top: 0; font-weight: 700; line-height: 1.3; }
        .content p { margin: 0 0 16px 0; }
        .content a { color: {{ $primaryColor ?? '#4f46e5' }}; text-decoration: underline; }
        .content a.btn-action, .content a.btn-primary-action, .content a[style*="display: inline-block"] { text-decoration: none !important; }
        .btn-action, .btn-primary-action { background-color: {{ $buttonColor ?? '#0f172a' }} !important; color: #ffffff !important; text-decoration: none !important; }
        .footer { padding: 24px 32px 32px 32px; text-align: center; font-size: 12px; line-height: 1.5; color: #64748b; background-color: #f8fafc; border-top: 1px solid #e2e8f0; }
        .footer a { color: #64748b; text-decoration: underline; }
        @media only screen and (max-width: 620px) {
            .wrapper { padding: 16px 8px !important; }
            .header { padding: 24px 20px !important; }
            .content { padding: 24px 20px !important; font-size: 14px !important; }
            .footer { padding: 20px !important; }
        }
    </style>
</head>
<body style="background-color: #f1f5f9; margin: 0; padding: 0;">
    <div class="wrapper" style="background-color: #f1f5f9;">
        <center>
            <table role="presentation" class="main-card" cellpadding="0" cellspacing="0" style="background-color: #ffffff; max-width: 600px; width: 100%; border: 1px solid #e2e8f0; border-radius: 12px;">
                <!-- Header / Logo -->
                <tr>
                    <td class="header" style="text-align: center; background-color: #ffffff;">
                        <a href="{{ url('/') }}" target="_blank" style="text-decoration: none; display: inline-block;">
                            @if(!empty($logoUrl))
                                <img src="{{ $logoUrl }}" alt="{{ $settings->title ?? 'Imagine Buddy' }}" style="max-height: 40px; width: auto; max-width: 220px; display: block; margin: 0 auto;">
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

                <!-- Email Body Content -->
                <tr>
                    <td class="content" style="background-color: #ffffff; color: #334155;">
                        {!! $bodyContent !!}
                    </td>
                </tr>

                <!-- Footer with Compliant Unsubscribe -->
                <tr>
                    <td class="footer" style="background-color: #f8fafc; color: #64748b;">
                        @if(!empty($footerText))
                            <p style="margin: 0 0 10px 0;">{!! nl2br(e($footerText)) !!}</p>
                        @else
                            <p style="margin: 0 0 8px 0;">
                                &copy; {{ date('Y') }} {{ $settings->title ?? 'Imagine Buddy' }}. All rights reserved.
                            </p>
                            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px;">
                                You are receiving this email because you opted in to updates and marketing on Imagine Buddy.
                            </p>
                        @endif

                        <p style="margin: 0 0 8px 0; font-size: 11px; color: #94a3b8;">
                            @if(!empty($unsubscribeUrl))
                                <a href="{{ $unsubscribeUrl }}" target="_blank" style="color: #64748b; text-decoration: underline;">Unsubscribe instantly</a> &bull;
                            @endif
                            <a href="{{ url('page/terms-and-conditions') }}" target="_blank" style="color: #94a3b8;">Terms</a> &bull;
                            <a href="{{ url('page/privacy') }}" target="_blank" style="color: #94a3b8;">Privacy Policy</a>
                        </p>
                        <p style="margin: 0; font-size: 10px; color: #cbd5e1;">
                            Protected by Amazon SES Enterprise Cloud Infrastructure &bull; updates.imaginebuddy.com
                        </p>
                    </td>
                </tr>
            </table>
        </center>
    </div>
</body>
</html>
