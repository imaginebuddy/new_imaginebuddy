<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? ($settings->title ?? 'Imagine Buddy') }}</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0; }
        .main-card { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); overflow: hidden; }
        .header { padding: 32px 32px 24px 32px; text-align: center; border-bottom: 1px solid #f1f5f9; background-color: #ffffff; }
        .content { padding: 32px; font-size: 15px; line-height: 1.65; color: #334155; }
        .content h1, .content h2, .content h3 { color: #0f172a; margin-top: 0; font-weight: 700; line-height: 1.3; }
        .content p { margin: 0 0 16px 0; }
        .content a { color: {{ $primaryColor ?? '#4f46e5' }}; text-decoration: underline; }
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
<body>
    <div class="wrapper">
        <center>
            <table role="presentation" class="main-card" cellpadding="0" cellspacing="0">
                <!-- Header / Logo -->
                <tr>
                    <td class="header">
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
                    <td class="content">
                        {!! $bodyContent !!}
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="footer">
                        @if(!empty($footerText))
                            <p style="margin: 0 0 8px 0;">{!! nl2br(e($footerText)) !!}</p>
                        @else
                            <p style="margin: 0 0 8px 0;">
                                &copy; {{ date('Y') }} {{ $settings->title ?? 'Imagine Buddy' }}. All rights reserved.
                            </p>
                            <p style="margin: 0 0 8px 0;">
                                This security email was sent automatically from a verified, notification-only address. Please do not reply directly to this email.
                            </p>
                        @endif
                        <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                            Protected by Amazon SES Enterprise Cloud Infrastructure &bull; 
                            <a href="{{ url('page/terms-and-conditions') }}" target="_blank" style="color: #94a3b8;">Terms &amp; Privacy</a>
                        </p>
                    </td>
                </tr>
            </table>
        </center>
    </div>
</body>
</html>
