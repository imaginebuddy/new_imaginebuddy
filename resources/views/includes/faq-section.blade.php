@php
  $homeFaqs = \App\Models\Faq::active()->ordered()->take(6)->get();
@endphp
<!-- FAQ Section -->
<section class="section py-5 py-large faq-section position-relative" id="faq">
  <div class="container">
    <div class="row g-4 g-lg-5">
      
      <!-- Left Sticky Column: Section Title & Contact Prompt -->
      <div class="col-12 col-lg-5">
        <div class="faq-sticky-header">
          <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            FAQ
          </span>
          <h2 class="display-5 fw-bold title-custom mb-4" style="line-height: 1.15; font-size: 2.75rem;">
            Answers <br>
            before the <br>
            first prompt.
          </h2>
          <p class="text-muted lead mb-2" style="font-size: 1.05rem;">
            Everything you need to know about our photoshoot prompt library before getting started.
          </p>
          <p class="text-muted mb-4" style="font-size: 0.95rem;">
            Can’t find your answer? <a href="{{ url('contact') }}" class="title-custom fw-bold text-decoration-underline">Just ask us.</a>
          </p>

          <a href="{{ url('frequently-asked-questions') }}" class="btn btn-sm btn-dark rounded-pill px-4 py-2 fw-semibold">
            Browse All FAQs <i class="bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>

      <!-- Right Column: Accordion Items Cards -->
      <div class="col-12 col-lg-7">
        <div class="faq-accordion-wrapper" id="faqAccordion">

          @if ($homeFaqs->count() > 0)
            @foreach ($homeFaqs as $index => $faqItem)
            <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs">
              <button class="faq-toggle w-100 d-flex justify-content-between align-items-center border-0 bg-transparent text-start p-0 {{ $index === 0 ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#home-faq-collapse-{{ $faqItem->id }}"
                      aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                      aria-controls="home-faq-collapse-{{ $faqItem->id }}">
                <h5 class="fw-bold title-custom mb-0 me-3 fs-6 fs-md-5">{{ $faqItem->question }}</h5>
                <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0"></span>
              </button>
              <div id="home-faq-collapse-{{ $faqItem->id }}" class="collapse {{ $index === 0 ? 'show' : '' }} faq-collapse" data-bs-parent="#faqAccordion">
                <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.7; font-size: 0.98rem;">
                  {!! $faqItem->answer !!}
                </div>
              </div>
            </div>
            @endforeach
          @endif

        </div><!-- /.faq-accordion-wrapper -->
      </div><!-- /.col-lg-7 -->

    </div><!-- /.row -->
  </div><!-- /.container -->
</section>
