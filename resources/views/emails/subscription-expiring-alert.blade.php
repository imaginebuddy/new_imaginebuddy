<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Subscription Renewal Notice - {{ $settings->title }}</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; line-height: 1.6; font-size: 15px; color: #cbd5e1; }
        .alert-card { background: #1e293b; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 6px; margin: 20px 0; font-size: 14px; }
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
                <h1 style="margin-top: 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                    Your subscription renews / expires in 3 days
                </h1>
                <p>
                    Hi {{ $user->name ?: $user->username }}, this is a friendly notification that your Imagine Buddy plan is scheduled to expire or renew on <strong>{{ \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') }}</strong>.
                </p>

                <div class="alert-card">
                    <strong style="color: #f8fafc;">Why Keep Your Plan Active:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #cbd5e1;">
                        <li>Keep your commercial licensing rights active on all downloaded prompts</li>
                        <li>Maintain priority generation speeds for product photoshoots</li>
                        <li>Retain access to premium member vaults</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ $renewUrl }}" class="btn-custom" target="_blank">
                        Manage Plan &amp; Download Quota &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. This is an essential billing and account security notice.</p>
            <p>
                <a href="{{ url('page/terms-and-conditions') }}">Terms</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
