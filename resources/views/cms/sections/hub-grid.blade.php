<section class="cms-section">
  <div class="container">
    @if (!empty($sectionHeading))
      <div class="cms-section-header">
        @if(!empty($sectionBadge))
          <span class="cms-eyebrow-badge mb-2">{{ $sectionBadge }}</span>
        @endif
        <h2 class="cms-section-title">{{ $sectionHeading }}</h2>
        @if(!empty($sectionSubheading))
          <p class="cms-section-sub">{{ $sectionSubheading }}</p>
        @endif
      </div>
    @endif

    <div class="row g-4">
      @forelse ($items as $index => $item)
        @php
          $itemUrl = $item->public_url ?? url(($prefix ?? 'solution') . '/' . $item->slug);
          $itemIndex = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        @endphp
        <div class="col-lg-4 col-md-6">
          <a href="{{ $itemUrl }}" class="cms-hub-card">
            <div class="cms-hub-card-header">
              <span class="cms-hub-card-tag">
                @if (!empty($item->icon))
                  <span class="me-1">{{ $item->icon }}</span>
                @else
                  <i class="bi bi-folder-fill me-1"></i>
                @endif
                {{ $item->header_badge ?: ($verticalName ?? 'SOLUTION') }}
              </span>
              <span class="cms-hub-card-index">{{ $itemIndex }}</span>
            </div>

            <h3 class="cms-hub-card-title">
              {{ $item->title }}
            </h3>

            <p class="cms-hub-card-desc">
              {{ $item->excerpt ?: Str::limit(strip_tags($item->meta_description ?? ''), 120) }}
            </p>

            <span class="cms-hub-card-link">
              Learn more <i class="bi bi-arrow-right"></i>
            </span>
          </a>
        </div>
      @empty
        <div class="col-12 text-center py-5">
          <div class="text-muted">No solutions published in this directory yet.</div>
        </div>
      @endforelse
    </div>

    @if (isset($items) && method_exists($items, 'links') && $items->hasPages())
      <div class="d-flex justify-content-center mt-5">
        {{ $items->links() }}
      </div>
    @endif
  </div>
</section>
