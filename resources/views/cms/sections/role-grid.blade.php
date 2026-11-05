<section class="cms-section" id="built-for-roles">
  <div class="container">
    <div class="cms-section-header">
      @if (!empty($section->badge))
        <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
      @endif
      <h2 class="cms-section-title">
        {{ $section->heading ?: 'Built for Every E-commerce Role' }}
      </h2>
      @if (!empty($section->subheading))
        <p class="cms-section-sub">{{ $section->subheading }}</p>
      @endif
    </div>

    @php
      $cards = $section->cards;
    @endphp

    <div class="row g-4">
      @if (!empty($cards) && count($cards) > 0)
        @foreach ($cards as $idx => $card)
          <div class="col-lg-4 col-md-6">
            <div class="cms-role-card h-100">
              <div class="cms-role-card-header d-flex justify-content-between align-items-center mb-3">
                <span class="cms-role-tag">{{ $card['tag'] ?? 'ORGANIZATION' }}</span>
                <span class="cms-role-num">{{ $card['num'] ?? sprintf('%02d', $idx + 1) }}</span>
              </div>
              
              <h3 class="cms-role-title">{{ $card['title'] ?? 'Role' }}</h3>
              
              <p class="cms-role-desc">{{ $card['description'] ?? '' }}</p>

              @if (!empty($card['bullets']) && is_array($card['bullets']))
                <div class="cms-role-divider"></div>
                <ul class="cms-role-bullets">
                  @foreach ($card['bullets'] as $bullet)
                    <li>
                      <span class="cms-role-bullet-dot"></span>
                      <span>{{ $bullet }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif

              @if (!empty($card['cta_text']))
                <div class="mt-auto pt-3">
                  <a href="{{ $card['cta_url'] ?? url('register') }}" class="cms-role-link">
                    {{ $card['cta_text'] }} <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      @else
        {{-- Default 6 Roles Matching Reference --}}
        @php
          $defaultRoles = [
            [
              'tag' => 'ORGANIZATION',
              'num' => '01',
              'title' => 'D2C Founders & Solo Entrepreneurs',
              'description' => 'You wear every hat. Stop outsourcing creative work. Generate professional product images in minutes, not days.',
              'bullets' => [
                'Launch new products without hiring a photographer',
                'Create lifestyle variants for Instagram and TikTok',
                'Test seasonal backgrounds before committing to photoshoots',
              ]
            ],
            [
              'tag' => 'CATALOG OPS',
              'num' => '02',
              'title' => 'E-Commerce & Merchandising Managers',
              'description' => 'Eliminate catalog bottlenecks. Standardize lighting, margins, and backgrounds across thousands of SKUs automatically.',
              'bullets' => [
                'Batch process multi-angle product catalogs at scale',
                'Maintain consistent RGB 255 pure white backgrounds',
                'Auto-crop to 1:1, 4:5, and 9:16 aspect ratios',
              ]
            ],
            [
              'tag' => 'GROWTH & MEDIA',
              'num' => '03',
              'title' => 'Performance Marketers & Media Buyers',
              'description' => 'Combat creative ad fatigue. Generate endless variations of visual hooks and backdrops to boost CTR and lower CAC.',
              'bullets' => [
                'A/B test product backdrops, props, and lighting styles',
                'Generate channel-optimized creative for Meta, Google & TikTok',
                'Produce seasonal campaign refreshes without reshoots',
              ]
            ],
            [
              'tag' => 'MARKETPLACES',
              'num' => '04',
              'title' => 'Marketplace Sellers (Amazon, Blinkit, Ajio)',
              'description' => 'Pass platform listing checks on the first try. Zero catalog suppressions with automated compliance verification.',
              'bullets' => [
                'Strict Amazon RGB 255 and 85%+ frame fill compliance',
                'Multi-packshot sets ready for quick commerce PIM upload',
                'High-resolution zoom details without compression artifacts',
              ]
            ],
            [
              'tag' => 'AGENCIES & STUDIOS',
              'num' => '05',
              'title' => 'Creative Agencies & Content Studios',
              'description' => 'Deliver commercial client campaigns 10x faster. Scale your output without expanding studio overhead or headcount.',
              'bullets' => [
                'Pitch and render concepts to clients in real time',
                'Full commercial licensing rights included on every asset',
                'Create on-model fashion campaigns with virtual talent',
              ]
            ],
            [
              'tag' => 'POST-PRODUCTION',
              'num' => '06',
              'title' => 'Photographers & Digital Retouchers',
              'description' => 'Automate the tedious manual work. Cut clipping paths, background cleanup, and shadow creation from hours to seconds.',
              'bullets' => [
                'Sub-pixel edge detection for jewelry, lace & flyaway hair',
                'Preserve true fabric textures, reflections & logos',
                'Generate realistic ambient occlusion and drop shadows',
              ]
            ],
          ];
        @endphp

        @foreach ($defaultRoles as $card)
          <div class="col-lg-4 col-md-6">
            <div class="cms-role-card h-100">
              <div class="cms-role-card-header d-flex justify-content-between align-items-center mb-3">
                <span class="cms-role-tag">{{ $card['tag'] }}</span>
                <span class="cms-role-num">{{ $card['num'] }}</span>
              </div>
              
              <h3 class="cms-role-title">{{ $card['title'] }}</h3>
              
              <p class="cms-role-desc">{{ $card['description'] }}</p>

              <div class="cms-role-divider"></div>
              <ul class="cms-role-bullets">
                @foreach ($card['bullets'] as $bullet)
                  <li>
                    <span class="cms-role-bullet-dot"></span>
                    <span>{{ $bullet }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</section>
