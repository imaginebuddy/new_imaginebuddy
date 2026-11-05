<section class="cms-section" id="features">
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
      $rows = $section->cards;
    @endphp

    @if (!empty($rows) && count($rows) > 0)
      @foreach ($rows as $index => $row)
        @php
          $isReversed = ($index % 2 !== 0);
        @endphp
        <div class="cms-split-row {{ $isReversed ? 'is-reversed' : '' }}">
          <div class="cms-split-media">
            <div class="cms-preview-card">
              @if (!empty($row['image']))
                <img src="{{ $row['image'] }}" alt="{{ $row['title'] ?? 'Feature' }}" class="img-fluid" loading="lazy">
              @else
                <div class="p-5 text-center bg-dark text-white rounded">
                  <i class="bi bi-camera-fill text-mint display-5 d-block mb-3"></i>
                  <span class="small text-white-50">{{ $row['title'] ?? 'Feature Preview' }}</span>
                </div>
              @endif
            </div>
          </div>

          <div class="cms-split-copy">
            @if (!empty($row['badge']))
              <span class="cms-feature-eyebrow">{{ $row['badge'] }}</span>
            @endif
            <h3 class="cms-split-title">
              {{ $row['title'] ?? 'Feature' }}
            </h3>
            <p class="cms-split-desc">
              {{ $row['description'] ?? '' }}
            </p>

            @if (!empty($row['bullets']) && is_array($row['bullets']))
              <ul class="cms-checklist">
                @foreach ($row['bullets'] as $bullet)
                  <li class="cms-checklist-item">
                    <span class="cms-checklist-dot"></span>
                    <span>{{ $bullet }}</span>
                  </li>
                @endforeach
              </ul>
            @endif

            @if (!empty($row['cta_text']))
              <a href="{{ $row['cta_url'] ?? url('register') }}" class="btn-cms-primary mt-2">
                {{ $row['cta_text'] }} <i class="bi bi-arrow-right"></i>
              </a>
            @endif
          </div>
        </div>
      @endforeach
    @elseif(!empty($section->content))
      <div class="cms-feature-card">
        {!! $section->content !!}
      </div>
    @endif
  </div>
</section>
