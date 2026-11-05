@if (!empty($relatedPages) && $relatedPages->count() > 0)
  @php
    $siloName = ucwords(str_replace('-', ' ', $page->route_prefix ?? 'Use Cases'));
    $sectionTitle = !empty($section->heading)
      ? $section->heading
      : ('Explore More ' . ($siloName === 'Use Case' ? 'Use Cases' : $siloName));
    $sectionSub = !empty($section->subheading)
      ? $section->subheading
      : 'Discover complementary photography workflows and specialized visual blueprints.';

    // Dynamic column widths based strictly on the count of published pages in the database
    $count = $relatedPages->count();
    $colClass = 'col-lg-4 col-md-6';
    if ($count === 1) {
      $colClass = 'col-lg-5 col-md-8';
    } elseif ($count === 2) {
      $colClass = 'col-lg-5 col-md-6';
    }
  @endphp

  <section class="cms-section cms-section-sm" id="related-solutions">
    <div class="container">
      <div class="cms-section-header">
        <span class="cms-eyebrow-badge mb-2">EXPLORE MORE</span>
        <h2 class="cms-section-title">{{ $sectionTitle }}</h2>
        <p class="cms-section-sub">{{ $sectionSub }}</p>
      </div>

      <div class="row g-4 justify-content-center">
        @foreach ($relatedPages as $idx => $relPage)
          @php
            $relUrl = $relPage->public_url ?? url(($relPage->route_prefix ?? 'use-case') . '/' . $relPage->slug);
            $indexNum = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            $badge = $relPage->header_badge ?: ucwords(str_replace('-', ' ', $relPage->route_prefix ?? 'Use Case'));
            $desc = $relPage->excerpt ?: Str::limit(strip_tags($relPage->meta_description ?? ''), 120);
            $icon = !empty($relPage->icon) ? $relPage->icon : 'bi bi-stars';
            $linkText = 'Explore ' . ucwords(str_replace('-', ' ', $relPage->route_prefix ?? 'Use Case'));
          @endphp
          <div class="{{ $colClass }}">
            <a href="{{ $relUrl }}" class="cms-hub-card">
              <div class="cms-hub-card-header">
                <span class="cms-hub-card-tag">
                  @if (Str::startsWith($icon, 'bi '))
                    <i class="{{ $icon }} me-1"></i>
                  @else
                    <span class="me-1">{{ $icon }}</span>
                  @endif
                  {{ $badge }}
                </span>
                <span class="cms-hub-card-index">{{ $indexNum }}</span>
              </div>

              <h3 class="cms-hub-card-title">
                {{ $relPage->title }}
              </h3>

              <p class="cms-hub-card-desc">
                {{ $desc }}
              </p>

              <span class="cms-hub-card-link">
                {{ $linkText }} <i class="bi bi-arrow-right"></i>
              </span>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endif
