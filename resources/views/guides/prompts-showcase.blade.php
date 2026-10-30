@if (isset($prompts) && $prompts->count() > 0)
  <section class="guide-prompts-showcase mb-5 mt-2" id="prompts-showcase" data-heading="Handpicked Prompts for Premium Product Imagery">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <span class="badge bg-custom-mint text-white rounded-pill px-3 py-1 mb-2 fw-semibold">Prompt Library</span>
        <h2 class="h3 fw-bold mb-0" style="color: var(--guide-text-main);">Handpicked Prompts for Premium Product Imagery</h2>
      </div>
      <small class="text-muted">Click any prompt card to view high-res photoshoot variations</small>
    </div>

    <div class="row g-4">
      @foreach ($prompts as $prompt)
        @php
          $stockImage = $prompt->stock ? ($prompt->stock->where('type', 'medium')->first() ?: $prompt->stock->first()) : null;
          $previewUrl = $prompt->preview 
            ? Storage::url(config('path.preview') . $prompt->preview) 
            : ($stockImage ? url('files/preview/' . $stockImage->resolution, $stockImage->name) . '?size=medium&type=' . $prompt->item_for_sale : asset('public/uploads/preview/' . $prompt->thumbnail));
          $authorName = $prompt->author ? ($prompt->author->name ?: $prompt->author->username) : 'Imagine Buddy';

          $promptExamples = $prompt->examples ? $prompt->examples->filter(function($ex) {
            return !empty($ex->file);
          }) : collect();

          $hasPromptExamples = $promptExamples->isNotEmpty();
          $promptSlides = collect();
          if ($hasPromptExamples) {
            if ($previewUrl) {
              $promptSlides->push($previewUrl);
            }
            foreach ($promptExamples as $ex) {
              $promptSlides->push(Storage::url(config('path.examples') . $ex->file));
            }
          }
        @endphp
        <div class="col-md-6 col-lg-4">
          <div class="card border-0 rounded-4 overflow-hidden h-100 d-flex flex-column justify-content-between p-2 guide-prompt-card-wrapper prompt-card-item shadow-sm">
            
            <!-- 1. Top Image Section with Badges Overlay -->
            <div class="position-relative overflow-hidden w-100 flex-grow-1 rounded-4 guide-prompt-media-frame prompt-card-media-wrapper" style="min-height: 380px;">
              <!-- Top Badges Overlay -->
              <div class="prompt-card-top-bar d-flex justify-content-between align-items-center position-absolute top-0 start-0 w-100 p-3" style="z-index: 15; pointer-events: none;">
                <!-- Top Left Badges: Free Tag + Category/Badge -->
                <div class="d-inline-flex align-items-center gap-2 flex-wrap" style="pointer-events: auto; max-width: calc(100% - 48px);">
                  @if ($prompt->item_for_sale == 'free')
                    <span class="badge prompt-free-badge rounded-pill shadow-sm d-inline-flex align-items-center">
                      Free
                    </span>
                  @endif

                  <!-- Dark Translucent Badge: Category/Badge -->
                  <span class="badge text-white fw-medium rounded-pill px-3 py-2 shadow-sm text-capitalize d-inline-flex align-items-center gap-1" style="font-size: 12px; background-color: rgba(24, 43, 23, 0.85); backdrop-filter: blur(8px);">
                    @if ($prompt->item_for_sale == 'sale')
                      <i class="fa fa-crown text-warning me-1" title="Premium Prompt"></i>
                    @endif
                    @if (!empty($prompt->custom_badge))
                      {{ $prompt->custom_badge }}
                    @elseif ($prompt->category)
                      {{ ucfirst(strtolower($prompt->category->name)) }}
                    @elseif ($prompt->ai_model)
                      {{ ucfirst(strtolower($prompt->ai_model)) }}
                    @else
                      Prompt
                    @endif
                  </span>
                </div>

                <!-- Top Right Circle: Heart Icon + Count -->
                <div class="prompt-badge-circle fw-bold rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center guide-prompt-heart-badge" style="pointer-events: auto; width: 36px; height: 36px;">
                  <i class="bi bi-heart text-danger" style="font-size: 13px; line-height: 1;"></i>
                  <span class="small lh-1 prompt-badge-circle-text" style="font-size: 10px; font-weight: 700; margin-top: 1px;">{{ Helper::formatNumber($prompt->likes ? $prompt->likes()->count() : 0) }}</span>
                </div>
              </div>

              @if (!$hasPromptExamples)
                <!-- Full Cover Image Link (Original untouched layout) -->
                <a href="{{ url('prompt', $prompt->slug) }}" class="d-block w-100 h-100 text-decoration-none">
                  <img alt="{{ $prompt->title }}" class="previewImage prompt-card-img rounded-4" src="{{ $previewUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy" />
                </a>
              @else
                <!-- Image Carousel Showcase for Example Images -->
                <div class="prompt-card-carousel position-relative w-100 h-100 overflow-hidden rounded-4" data-current-slide="0" data-total-slides="{{ $promptSlides->count() }}">
                  <div class="prompt-carousel-track w-100 h-100 position-relative">
                    @foreach ($promptSlides as $idx => $slideUrl)
                      <a href="{{ url('prompt', $prompt->slug) }}" class="prompt-carousel-slide d-block w-100 h-100 text-decoration-none {{ $idx === 0 ? 'active' : '' }}" data-slide-index="{{ $idx }}" data-img-src="{{ $slideUrl }}" aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}">
                        <img alt="{{ $prompt->title }} {{ $idx > 0 ? '- Example ' . $idx : '' }}" class="{{ $idx === 0 ? 'previewImage ' : '' }}prompt-card-img rounded-4" src="{{ $slideUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                      </a>
                    @endforeach
                  </div>

                  @if ($promptSlides->count() > 1)
                    <!-- Left Navigation Arrow -->
                    <button type="button" class="btn prompt-carousel-arrow prompt-carousel-prev" data-action="prev" aria-label="Previous example image" title="Previous image">
                      <i class="bi bi-chevron-left"></i>
                    </button>

                    <!-- Right Navigation Arrow -->
                    <button type="button" class="btn prompt-carousel-arrow prompt-carousel-next" data-action="next" aria-label="Next example image" title="Next image">
                      <i class="bi bi-chevron-right"></i>
                    </button>

                    <!-- Subtle Dots Indicators -->
                    <div class="prompt-carousel-dots d-flex align-items-center gap-1 position-absolute bottom-0 start-50 translate-middle-x mb-2" style="z-index: 10;">
                      @foreach ($promptSlides as $idx => $slideUrl)
                        <span class="prompt-carousel-dot {{ $idx === 0 ? 'active' : '' }}" data-dot-index="{{ $idx }}" role="button" aria-label="Go to slide {{ $idx + 1 }}"></span>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endif
            </div>

            <!-- 2. Bottom Details Section (Title, Subtext, Author, Share & Copy) -->
            <div class="prompt-card-bottom-info px-2 pb-2 pt-3 position-relative guide-prompt-bottom-info" style="z-index: 6;">
              <!-- Title -->
              <h3 class="fw-bold mb-2 prompt-card-title text-break h5" style="font-size: 17px; line-height: 1.35; font-family: system-ui, -apple-system, sans-serif; letter-spacing: -0.3px;">
                <a href="{{ url('prompt', $prompt->slug) }}" class="text-decoration-none guide-prompt-title" title="{{ $prompt->title }}">
                  {{ $prompt->custom_title ?: $prompt->title }}
                </a>
              </h3>

              <!-- Prompt Snippet (Subtext) -->
              <p class="small mb-3 prompt-snippet-text line-clamp-2 guide-prompt-snippet" style="font-size: 13px; line-height: 1.4;">
                {{ Str::limit($prompt->prompt ?: $prompt->description ?: $prompt->title, 160, '...') }}
              </p>

              <!-- Bottom Action Controls Bar -->
              <div class="d-flex align-items-center justify-content-between gap-2">
                <!-- Author Pill Badge (Left Side) -->
                <span class="badge fw-bold rounded-pill px-3 py-2 text-truncate guide-prompt-author-pill prompt-author-pill" style="font-size: 13px; max-width: 140px;">
                  By {{ $authorName }}
                </span>

                <!-- Right Action Buttons (Share Circle + Copy Pill with Icon) -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                  <!-- Share Circle Button -->
                  <button type="button" class="btn rounded-circle btn-sm shadow-none btn-share-prompt prompt-share-btn guide-prompt-share-btn d-inline-flex align-items-center justify-content-center" data-url="{{ url('prompt', $prompt->slug) }}" data-title="{{ $prompt->title }}" style="width: 36px; height: 36px;" title="Share">
                    <i class="bi bi-share-fill" style="font-size: 13px;"></i>
                  </button>

                  <!-- Copy Pill Button -->
                  <button type="button" class="btn text-white rounded-pill btn-sm fw-bold btn-copy-prompt-grid prompt-copy-btn guide-prompt-copy-btn shadow-none d-inline-flex align-items-center justify-content-center" data-id="{{ $prompt->id }}" title="Copy Prompt">
                    <span class="prompt-copy-text">Copy</span>
                    <span class="prompt-copy-icon-bg">
                      <i class="bi bi-copy"></i>
                    </span>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </section>
@endif
