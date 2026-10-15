<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Custom Photoshoot Inquiry</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f7f9fa; padding: 24px;">
    <div style="max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e1e4e8; overflow: hidden;">
        <div style="background-color: #0f172a; color: #ffffff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 20px;">New Custom Photoshoot Project Inquiry</h2>
            <p style="margin: 4px 0 0; font-size: 14px; opacity: 0.85;">Submitted from {{ $title_site }} Hire Page</p>
        </div>
        <div style="padding: 24px;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 180px; color: #555;">Client Name:</td>
                    <td style="padding: 8px 0;">{{ $inquiry->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Work Email:</td>
                    <td style="padding: 8px 0;"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Company / Brand:</td>
                    <td style="padding: 8px 0;">{{ $inquiry->company_name }}</td>
                </tr>
                @if($inquiry->website_url)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Website URL:</td>
                    <td style="padding: 8px 0;"><a href="{{ $inquiry->website_url }}" target="_blank">{{ $inquiry->website_url }}</a></td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Product Category:</td>
                    <td style="padding: 8px 0;">{{ $inquiry->product_category }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Number of SKUs:</td>
                    <td style="padding: 8px 0;">{{ $inquiry->sku_count }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Photoshoot Types:</td>
                    <td style="padding: 8px 0;">{{ is_array($inquiry->photoshoot_types) ? implode(', ', $inquiry->photoshoot_types) : $inquiry->photoshoot_types }}</td>
                </tr>
                @if(!empty($inquiry->intended_use))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Intended Use:</td>
                    <td style="padding: 8px 0;">{{ is_array($inquiry->intended_use) ? implode(', ', $inquiry->intended_use) : $inquiry->intended_use }}</td>
                </tr>
                @endif
                @if($inquiry->budget_range)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Budget Range:</td>
                    <td style="padding: 8px 0;"><strong>{{ $inquiry->budget_range }}</strong> ({{ $inquiry->currency }})</td>
                </tr>
                @endif
                @if($inquiry->desired_delivery_date)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Desired Delivery:</td>
                    <td style="padding: 8px 0;">{{ $inquiry->desired_delivery_date->format('F d, Y') }}</td>
                </tr>
                @endif
            </table>

            <h3 style="margin: 20px 0 10px; font-size: 16px; border-bottom: 2px solid #eee; padding-bottom: 6px;">Creative Brief & Project Description:</h3>
            <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 12px 16px; margin-bottom: 16px; white-space: pre-line;">{{ $inquiry->project_description }}</div>

            @if($inquiry->reference_links)
            <h4 style="margin: 16px 0 6px; font-size: 14px;">Reference Links:</h4>
            <p style="background: #f8fafc; padding: 10px; border-radius: 4px; word-break: break-all;">{{ $inquiry->reference_links }}</p>
            @endif

            @if($inquiry->additional_notes)
            <h4 style="margin: 16px 0 6px; font-size: 14px;">Additional Notes:</h4>
            <p style="background: #f8fafc; padding: 10px; border-radius: 4px; white-space: pre-line;">{{ $inquiry->additional_notes }}</p>
            @endif

            <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0 16px;">
            <p style="font-size: 12px; color: #888; margin: 0;">
                Submitted from IP: {{ $inquiry->ip_address }} ({{ $inquiry->country_code ?: 'N/A' }}) on {{ $inquiry->created_at->format('M d, Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
