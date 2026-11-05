<section class="cms-section" id="faq">
  <div class="container">
    <div class="cms-section-header">
      @if (!empty($section->badge))
        <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
      @endif
      <h2 class="cms-section-title">
        {{ $section->heading ?: 'Frequently Asked Questions' }}
      </h2>
      @if (!empty($section->subheading))
        <p class="cms-section-sub">{{ $section->subheading }}</p>
      @endif
    </div>

    @php
      $faqs = $section->faqs;
      $schemaFaqs = [];
    @endphp

    <div class="cms-faq-container">
      @if (!empty($faqs) && count($faqs) > 0)
        <div class="accordion" id="cmsFaqAccordion">
          @foreach ($faqs as $idx => $faq)
            @php
              $collapseId = 'faq-item-' . $idx;
              $isFirst = ($idx === 0);
              $schemaFaqs[] = [
                '@type' => 'Question',
                'name' => $faq['question'] ?? '',
                'acceptedAnswer' => [
                  '@type' => 'Answer',
                  'text' => strip_tags($faq['answer'] ?? '')
                ]
              ];
            @endphp
            <div class="accordion-item cms-accordion-item">
              <h3 class="accordion-header cms-accordion-header" id="heading-{{ $idx }}">
                <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $isFirst ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                  {{ $faq['question'] ?? '' }}
                </button>
              </h3>
              <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}" aria-labelledby="heading-{{ $idx }}" data-bs-parent="#cmsFaqAccordion">
                <div class="accordion-body cms-accordion-body">
                  {!! $faq['answer'] ?? '' !!}
                </div>
              </div>
            </div>
          @endforeach
        </div>

        {{-- FAQPage Schema JSON-LD --}}
        @if (count($schemaFaqs) > 0)
          <script type="application/ld+json">
          {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": {!! json_encode($schemaFaqs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
          }
          </script>
        @endif
      @elseif(!empty($section->content))
        <div class="cms-feature-card">
          {!! $section->content !!}
        </div>
      @endif

      <div class="text-center mt-4">
        <p class="text-muted small mb-3">Still have questions about your specific product category or workflow?</p>
        <a href="{{ url('contact') }}" class="btn-cms-secondary">
          Contact Our Photography Team <i class="bi bi-chat-dots"></i>
        </a>
      </div>
    </div>
  </div>
</section>
