<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; max-width: 600px; margin: 0 auto;">

    <!-- Digest Header -->
    <div style="text-align: left; margin-bottom: 24px;">
        <span style="display: inline-block; background-color: #ede9fe; color: #6366f1; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; letter-spacing: 0.8px; margin-bottom: 12px;">
            Weekly Photoshoot Drop &bull; {{ $periodLabel }}
        </span>
        <h1 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.3;">
            {{ $headline ?? 'New Commercial Studio Photoshoots' }}
        </h1>
        <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #475569;">
            Hi {name}, here are the newly published commercial photoshoot sets added to Imagine Buddy this past week. Every photoshoot includes pre-calibrated multi-angle prompt formulas tailored for commercial product photography.
        </p>
    </div>

    <!-- Photoshoots List -->
    @if(!empty($photoshoots) && $photoshoots->isNotEmpty())
        <div style="margin-bottom: 28px;">
            @foreach($photoshoots as $photoshoot)
                <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px; overflow: hidden; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                    <!-- Thumbnail / Cover Image -->
                    <tr>
                        <td style="padding: 0; background-color: #0f172a; text-align: center;">
                            <a href="{{ $photoshoot->url }}" target="_blank" style="display: block; text-decoration: none;">
                                <img src="{{ $photoshoot->thumbnail_url }}" alt="{{ $photoshoot->title }}" style="width: 100%; max-height: 320px; object-fit: cover; display: block; border-top-left-radius: 12px; border-top-right-radius: 12px;" />
                            </a>
                        </td>
                    </tr>

                    <!-- Photoshoot Details -->
                    <tr>
                        <td style="padding: 22px;">
                            <!-- Category and Prompts count -->
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; background-color: #f1f5f9; color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 10px; border-radius: 6px; letter-spacing: 0.5px;">
                                    {{ $photoshoot->category->name ?? 'Commercial' }}
                                </span>
                                <span style="display: inline-block; background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; margin-left: 6px;">
                                    {{ $photoshoot->prompts_count }} {{ Str::plural('Studio Prompt', $photoshoot->prompts_count) }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 style="margin: 0 0 10px 0; font-size: 19px; font-weight: 700; line-height: 1.4; color: #0f172a;">
                                <a href="{{ $photoshoot->url }}" target="_blank" style="color: #0f172a; text-decoration: none;">
                                    {{ $photoshoot->title }}
                                </a>
                            </h2>

                            <!-- Description (if present) -->
                            @if(!empty($photoshoot->description))
                                <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.5; color: #64748b;">
                                    {{ Str::limit(strip_tags($photoshoot->description), 150) }}
                                </p>
                            @endif

                            <!-- CTA Button -->
                            <div style="margin-top: 14px;">
                                <a href="{{ $photoshoot->url }}" target="_blank" style="display: inline-block; background-color: #0f172a; color: #ffffff !important; padding: 11px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none;">
                                    View Photoshoot & Prompts &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>
            @endforeach
        </div>
    @endif

    <!-- Catalog Footer Banner -->
    <div style="text-align: center; margin: 32px 0 12px 0; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
        <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;">
            Explore Complete Studio Catalog
        </h3>
        <p style="margin: 0 0 18px 0; font-size: 13px; line-height: 1.5; color: #64748b; max-width: 440px; margin-left: auto; margin-right: auto;">
            Need inspiration for a different product or packaging style? Browse all our multi-angle photoshoot sets and commercial prompt recipes.
        </p>
        <a href="{{ url('photoshoots') }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff !important; padding: 12px 30px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);">
            Browse All Photoshoots &rarr;
        </a>
    </div>

</div>
