<header class="cms-hub-hero">
  <div class="container">
    <div class="cms-hub-hero-content">
      @if (!empty($badge))
        <span class="cms-eyebrow-badge mb-2">
          <i class="bi bi-stars"></i> {{ $badge }}
        </span>
      @endif

      <h1 class="cms-hub-hero-title">
        {!! $title !!}
      </h1>

      @if (!empty($subtitle))
        <p class="cms-hub-hero-sub">
          {{ $subtitle }}
        </p>
      @endif

      @if (!empty($filterPills) && count($filterPills) > 0)
        <nav class="cms-filter-pill-bar" aria-label="Vertical filters">
          @foreach ($filterPills as $pill)
            <a href="{{ $pill['url'] }}" class="cms-filter-pill {{ !empty($pill['active']) ? 'active' : '' }}">
              @if(!empty($pill['icon']))
                <span class="me-1">{{ $pill['icon'] }}</span>
              @endif
              {{ $pill['label'] }}
            </a>
          @endforeach
        </nav>
      @endif

      @if (!empty($ctaText))
        <div class="mt-4">
          <a href="{{ $ctaUrl ?? url('register') }}" class="btn-cms-primary">
            {{ $ctaText }} <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      @endif
    </div>
  </div>
</header>
