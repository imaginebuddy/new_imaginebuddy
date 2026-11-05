@php
  $customCards = $section->cards;
  $hasCustomCards = !empty($customCards) && count($customCards) > 0;
  $hasAttachedPrompts = !empty($attachedPrompts) && $attachedPrompts->count() > 0;
@endphp

@if ($hasCustomCards || $hasAttachedPrompts)
  <section class="cms-section" id="prompts-showcase">
    <div class="container">
      <div class="cms-section-header">
        @if (!empty($section->badge))
          <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
        @else
          <span class="cms-eyebrow-badge mb-2">PROMPT RECIPES</span>
        @endif

        <h2 class="cms-section-title">
          {{ $section->heading ?: 'Connected Prompt Library' }}
        </h2>

        @if (!empty($section->subheading))
          <p class="cms-section-sub">{{ $section->subheading }}</p>
        @else
          <p class="cms-section-sub">Tested photoshoot recipes and visual prompts crafted specifically for {{ strtolower($page->title) }}.</p>
        @endif
      </div>

      {{-- Render Custom Structured Cards from Section JSON Settings --}}
      @if ($hasCustomCards)
        <div class="cms-prompts-masonry-wrapper mb-4" id="cmsCustomPromptsMasonryGrid-{{ $section->id }}">
          <div class="cms-prompt-masonry-sizer"></div>
          @foreach ($customCards as $idx => $card)
            @php
              $cardImg = $card['image'] ?? null;
              $cardBadge = $card['badge'] ?? ($card['ai_model'] ?? 'Prompt Recipe');
              $cardTitle = $card['title'] ?? ('Prompt Card #' . ($idx + 1));
              $cardPrompt = $card['prompt'] ?? ($card['description'] ?? '');
              $cardAspect = $card['aspect_ratio'] ?? '1 / 1';
            @endphp
            <div class="cms-prompt-masonry-item">
              <div class="card rounded-4 overflow-hidden d-flex flex-column p-2 guide-prompt-card-wrapper prompt-card-item shadow-sm">
                
                <!-- Media Frame (1:1 default aspect ratio to prevent cropping) -->
                <div class="position-relative overflow-hidden w-100 rounded-4 guide-prompt-media-frame prompt-card-media-wrapper" style="aspect-ratio: {{ $cardAspect }};">
                  <!-- Badges Overlay -->
                  <div class="prompt-card-top-bar d-flex justify-content-between align-items-center position-absolute top-0 start-0 w-100 p-3" style="z-index: 15; pointer-events: none;">
                    <div class="d-inline-flex align-items-center gap-2 flex-wrap" style="pointer-events: auto; max-width: calc(100% - 48px);">
                      @if (!empty($card['is_free']) || !empty($card['free']))
                        <span class="badge prompt-free-badge rounded-pill shadow-sm d-inline-flex align-items-center">
                          Free
                        </span>
                      @endif
                      <span class="badge text-white fw-medium rounded-pill px-3 py-2 shadow-sm text-capitalize d-inline-flex align-items-center gap-1" style="font-size: 12px; background-color: rgba(24, 43, 23, 0.85); backdrop-filter: blur(8px);">
                        {{ $cardBadge }}
                      </span>
                    </div>

                    <div class="prompt-badge-circle fw-bold rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center guide-prompt-heart-badge" style="pointer-events: auto; width: 36px; height: 36px;">
                      <i class="bi bi-stars text-mint" style="font-size: 14px; line-height: 1;"></i>
                    </div>
                  </div>

                  @if (!empty($cardImg))
                    @if (!empty($card['cta_url']))
                      <a href="{{ $card['cta_url'] }}" class="d-block w-100 h-100 text-decoration-none">
                        <img alt="{{ $cardTitle }}" class="previewImage prompt-card-img rounded-4 w-100 h-100" src="{{ $cardImg }}" style="object-fit: cover; display: block;" loading="lazy" />
                      </a>
                    @else
                      <img alt="{{ $cardTitle }}" class="previewImage prompt-card-img rounded-4 w-100 h-100" src="{{ $cardImg }}" style="object-fit: cover; display: block;" loading="lazy" />
                    @endif
                  @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark text-white p-4 text-center rounded-4">
                      <i class="bi bi-camera-fill text-mint display-6 mb-2"></i>
                      <span class="small text-white-50">{{ $cardTitle }}</span>
                    </div>
                  @endif
                </div>

                <!-- Bottom Details -->
                <div class="prompt-card-bottom-info px-2 pb-2 pt-3 position-relative guide-prompt-bottom-info" style="z-index: 6;">
                  <h3 class="fw-bold mb-2 prompt-card-title text-break h5" style="font-size: 17px; line-height: 1.35; font-family: system-ui, -apple-system, sans-serif; letter-spacing: -0.3px;">
                    @if (!empty($card['cta_url']))
                      <a href="{{ $card['cta_url'] }}" class="text-decoration-none guide-prompt-title" title="{{ $cardTitle }}">
                        {{ $cardTitle }}
                      </a>
                    @else
                      <span class="guide-prompt-title">{{ $cardTitle }}</span>
                    @endif
                  </h3>

                  @if (!empty($cardPrompt))
                    <p class="small mb-3 prompt-snippet-text line-clamp-2 guide-prompt-snippet" style="font-size: 13px; line-height: 1.4;">
                      {{ Str::limit($cardPrompt, 180, '...') }}
                    </p>
                  @endif

                  @if (!empty($card['bullets']) && is_array($card['bullets']))
                    <ul class="list-unstyled small text-muted mb-3">
                      @foreach ($card['bullets'] as $bullet)
                        <li class="mb-1"><i class="bi bi-check2 text-mint me-1"></i> {{ $bullet }}</li>
                      @endforeach
                    </ul>
                  @endif

                  <!-- Controls: Copy Prompt & CTA -->
                  <div class="d-flex align-items-center justify-content-between pt-1 gap-2">
                    @if (!empty($card['prompt']))
                      <button type="button" class="btn btn-sm guide-prompt-copy-btn d-inline-flex align-items-center rounded-pill copyPromptBtn" data-prompt="{{ $card['prompt'] }}">
                        <i class="bi bi-clipboard me-1"></i> Copy Prompt
                      </button>
                    @endif

                    @if (!empty($card['cta_url']))
                      <a href="{{ $card['cta_url'] }}" class="btn btn-sm btn-outline-light rounded-pill ms-auto px-3">
                        {{ $card['cta_text'] ?? 'Explore' }} <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                    @endif
                  </div>
                </div>

              </div>
            </div>
          @endforeach
        </div>
      @endif

      {{-- Render Attached Library Prompts if present --}}
      @if ($hasAttachedPrompts && View::exists('guides.prompts-showcase'))
        @include('guides.prompts-showcase', ['prompts' => $attachedPrompts, 'hideShowcaseHeader' => $hasCustomCards])
      @endif

    </div>
  </section>

  {{-- Masonry Initialization Script if custom cards present --}}
  @if ($hasCustomCards)
    <script src="{{ asset('public/js/masonry.pkgd.min.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var grid = document.getElementById('cmsCustomPromptsMasonryGrid-{{ $section->id }}');
        if (grid && typeof Masonry !== 'undefined') {
          var isMobile = window.innerWidth < 768;
          var msnry = new Masonry(grid, {
            itemSelector: '.cms-prompt-masonry-item',
            columnWidth: '.cms-prompt-masonry-sizer',
            percentPosition: true,
            gutter: isMobile ? 0 : 24,
            transitionDuration: '0.2s'
          });

          // Layout re-trigger on image loads
          var imgs = grid.querySelectorAll('img');
          imgs.forEach(function(img) {
            if (!img.complete) {
              img.addEventListener('load', function() { msnry.layout(); });
            }
          });
          window.addEventListener('load', function() { msnry.layout(); });
        }

        // Copy prompt button handler
        document.querySelectorAll('.copyPromptBtn').forEach(function(btn) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            var text = this.getAttribute('data-prompt');
            if (navigator.clipboard && text) {
              navigator.clipboard.writeText(text).then(function() {
                var origText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg me-1 text-success"></i> Copied!';
                setTimeout(function() { btn.innerHTML = origText; }, 2000);
              });
            }
          });
        });
      });
    </script>
  @endif
@endif
