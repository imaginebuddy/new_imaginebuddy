<section class="cms-section cms-section-sm" id="results-that-matter">
  <div class="container">
    @if (!empty($section->heading))
      <div class="cms-section-header">
        @if (!empty($section->badge))
          <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
        @endif
        <h2 class="cms-section-title">{{ $section->heading }}</h2>
        @if (!empty($section->subheading))
          <p class="cms-section-sub">{{ $section->subheading }}</p>
        @endif
      </div>
    @endif

    @php
      $stats = $section->stats;
    @endphp

    <div class="row g-4 text-center">
      @if (!empty($stats) && count($stats) > 0)
        @foreach ($stats as $stat)
          <div class="col-lg-3 col-6">
            <div class="cms-stat-card">
              <div class="cms-stat-number">{{ $stat['number'] ?? ($stat['value'] ?? '10x') }}</div>
              <div class="cms-stat-label">{{ $stat['label'] ?? ($stat['title'] ?? '') }}</div>
              @if (!empty($stat['description']) || !empty($stat['desc']))
                <p class="cms-stat-desc">{{ $stat['description'] ?? $stat['desc'] }}</p>
              @endif
            </div>
          </div>
        @endforeach
      @else
        <div class="col-lg-3 col-6">
          <div class="cms-stat-card">
            <div class="cms-stat-number">10x</div>
            <div class="cms-stat-label">Faster Time to Market</div>
            <p class="cms-stat-desc">From idea to live catalog in minutes</p>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="cms-stat-card">
            <div class="cms-stat-number">80%</div>
            <div class="cms-stat-label">Production Cost Savings</div>
            <p class="cms-stat-desc">Compared to traditional physical shoots</p>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="cms-stat-card">
            <div class="cms-stat-number">3.4x</div>
            <div class="cms-stat-label">Higher Ad Click-Through</div>
            <p class="cms-stat-desc">Through tested lifestyle background variations</p>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="cms-stat-card">
            <div class="cms-stat-number">99.8%</div>
            <div class="cms-stat-label">Compliance Approval Rate</div>
            <p class="cms-stat-desc">Zero marketplace catalog suppressions</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>
