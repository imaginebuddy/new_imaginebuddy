<section class="cms-section cms-section-sm" id="technical-specs">
  <div class="container">
    <div class="cms-section-header">
      @if (!empty($section->badge))
        <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
      @endif
      <h2 class="cms-section-title">
        {{ $section->heading ?: 'Technical Specifications & Compliance Matrix' }}
      </h2>
      @if (!empty($section->subheading))
        <p class="cms-section-sub">{{ $section->subheading }}</p>
      @endif
    </div>

    @php
      $specs = $section->specs;
    @endphp

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="cms-specs-table-card">
          @if (!empty($specs) && count($specs) > 0)
            @foreach ($specs as $spec)
              <div class="cms-specs-row">
                <span class="cms-specs-key">{{ $spec['key'] ?? '' }}</span>
                <span class="cms-specs-val">
                  @if (!empty($spec['compliant']))
                    <i class="bi bi-patch-check-fill text-mint"></i>
                  @endif
                  {{ $spec['value'] ?? '' }}
                </span>
              </div>
            @endforeach
          @else
            <div class="cms-specs-row">
              <span class="cms-specs-key">Background Standard</span>
              <span class="cms-specs-val"><i class="bi bi-patch-check-fill text-mint"></i> Pure White (RGB 255, 255, 255) / Custom Lifestyle</span>
            </div>
            <div class="cms-specs-row">
              <span class="cms-specs-key">Frame Fill Ratio</span>
              <span class="cms-specs-val"><i class="bi bi-patch-check-fill text-mint"></i> 85%+ Product Coverage</span>
            </div>
            <div class="cms-specs-row">
              <span class="cms-specs-key">Resolution & Sizing</span>
              <span class="cms-specs-val"><i class="bi bi-patch-check-fill text-mint"></i> Up to 4000 x 4000 px Ultra HD</span>
            </div>
            <div class="cms-specs-row">
              <span class="cms-specs-key">Export Formats</span>
              <span class="cms-specs-val"><i class="bi bi-patch-check-fill text-mint"></i> JPG, PNG, WEBP with Color Profile sRGB</span>
            </div>
            <div class="cms-specs-row">
              <span class="cms-specs-key">Compliance Status</span>
              <span class="cms-specs-val"><i class="bi bi-patch-check-fill text-mint"></i> 100% Platform Verified (Zero Suppression Guarantee)</span>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
