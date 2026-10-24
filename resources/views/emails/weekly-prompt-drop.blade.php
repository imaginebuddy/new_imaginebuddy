<?php $settings = App\Models\AdminSettings::first(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $settings->title }} - Weekly Prompt Drop</title>
    <style type="text/css">
        body { margin: 0; padding: 0; width: 100% !important; background-color: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #e2e8f0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #0b0f19; padding-bottom: 40px; }
        .main { background-color: #151c2c; margin: 0 auto; width: 100%; max-width: 620px; border-radius: 12px; border: 1px solid #1e293b; overflow: hidden; }
        .content { padding: 32px 28px; }
        .prompt-card { background-color: #1e293b; border-radius: 8px; border: 1px solid #334155; padding: 18px; margin-bottom: 20px; }
        .prompt-text { font-family: monospace; font-size: 13px; color: #cbd5e1; background: #0f172a; padding: 12px; border-radius: 6px; margin: 12px 0; line-height: 1.5; word-break: break-word; }
        .btn-view { display: inline-block; padding: 8px 18px; font-size: 13px; font-weight: 600; color: #ffffff !important; background: #6366f1; text-decoration: none; border-radius: 6px; }
        .btn-main { display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); text-decoration: none; border-radius: 8px; text-align: center; margin: 16px 0; }
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
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #818cf8; letter-spacing: 1px; margin-bottom: 8px;">
                    Weekly Creator Dispatch &bull; {{ date('M d, Y') }}
                </div>
                <h1 style="margin-top: 0; font-size: 22px; font-weight: 700; color: #f8fafc; line-height: 1.3;">
                    Fresh AI Photoshoot Prompts Ready to Copy
                </h1>
                <p style="font-size: 14px; line-height: 1.6; color: #cbd5e1; margin-bottom: 24px;">
                    Hi {{ $recipient->name ?: 'Creator' }}, here are this week's highest-converting, staff-tested commercial photography prompts. Copy them directly into your AI generator of choice!
                </p>

                @if(!empty($prompts) && count($prompts) > 0)
                    @foreach($prompts as $prompt)
                    <div class="prompt-card">
                        <div style="font-size: 16px; font-weight: 600; color: #f8fafc; margin-bottom: 4px;">
                            {{ $prompt->title }}
                        </div>
                        <div class="prompt-text">
                            {{ Str::limit($prompt->prompt ?: $prompt->description, 240) }}
                        </div>
                        <div style="text-align: right;">
                            <a href="{{ url('photo', $prompt->id) }}" class="btn-view" target="_blank">
                                View Full Prompt & Parameters &rarr;
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="prompt-card">
                        <div style="font-size: 16px; font-weight: 600; color: #f8fafc; margin-bottom: 4px;">
                            Featured Luxury Commercial Product Setup
                        </div>
                        <div class="prompt-text">
                            Studio photoshoot of high-end skincare serum on wet dark slate rock, gentle water splash, dramatic rim light, shallow depth of field, 85mm lens, Hasselblad H6D --ar 16:9 --style raw
                        </div>
                    </div>
                @endif

                <div style="text-align: center; margin-top: 28px;">
                    <a href="{{ url('prompts/free') }}" class="btn-main" target="_blank">
                        Explore All 1,000+ Free Prompts &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->title }}. You are receiving this because you opted in to Imagine Buddy updates.</p>
            <p>
                <a href="{{ $unsubscribeUrl }}">Unsubscribe immediately with 1 click</a> &bull;
                <a href="{{ url('page/privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
