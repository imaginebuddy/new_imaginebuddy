@php
  $homeFaqList = [
    [
      'question' => 'What is ImagineBuddy?',
      'answer' => '<p>ImagineBuddy is an AI product photography prompt platform that helps users explore product-focused visual concepts using AI image generators such as ChatGPT and Gemini.</p>'
    ],
    [
      'question' => 'What are adaptive product photography prompts?',
      'answer' => '<p>Adaptive prompts are designed to guide AI image generation according to the uploaded product. Depending on the prompt and image generator, the product’s appearance can influence the styling, background, props, lighting, and overall creative direction.</p>'
    ],
    [
      'question' => 'How do I use an ImagineBuddy prompt?',
      'answer' => '<p>Choose a prompt on ImagineBuddy, copy it, upload your product photo to a compatible AI image generator, paste the prompt, and generate your visual.</p>'
    ],
    [
      'question' => 'Can I use ImagineBuddy for any product?',
      'answer' => '<p>ImagineBuddy’s product photography concepts can be explored across many categories, including skincare, jewellery, fashion, food, electronics, furniture, and more. Results will vary depending on the product, prompt, and AI tool.</p>'
    ],
    [
      'question' => 'Do I need professional photography equipment?',
      'answer' => '<p>You can start with a clear product photo and a compatible AI image generator. You do not need to arrange a traditional studio setup to experiment with AI-generated concepts.</p>'
    ],
    [
      'question' => 'Does ImagineBuddy generate the images?',
      'answer' => '<p>ImagineBuddy provides product photography prompts. You use those prompts with a compatible AI image generator to create your visuals.</p>'
    ],
    [
      'question' => 'Can I use the visuals for commercial purposes?',
      'answer' => '<p>Commercial usage rights depend on the AI image generator you use, its terms, the assets involved, and any applicable brand or intellectual property rights. Check the relevant terms before using generated visuals commercially.</p>'
    ],
    [
      'question' => 'Are there free prompts?',
      'answer' => '<p>Yes, explore the free prompts available on ImagineBuddy to get started. Visit the pricing page to compare the current free and Pro plans.</p>'
    ],
  ];
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
            Frequently <br>
            Asked <br>
            Questions
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

          @foreach ($homeFaqList as $index => $faqItem)
          <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs">
            <button class="faq-toggle w-100 d-flex justify-content-between align-items-center border-0 bg-transparent text-start p-0 {{ $index === 0 ? '' : 'collapsed' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#home-faq-collapse-{{ $index }}"
                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-controls="home-faq-collapse-{{ $index }}">
              <h3 class="fw-bold title-custom mb-0 me-3 fs-6 fs-md-5">{{ $faqItem['question'] }}</h3>
              <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0"></span>
            </button>
            <div id="home-faq-collapse-{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }} faq-collapse" data-bs-parent="#faqAccordion">
              <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.7; font-size: 0.98rem;">
                {!! $faqItem['answer'] !!}
              </div>
            </div>
          </div>
          @endforeach

        </div><!-- /.faq-accordion-wrapper -->
      </div><!-- /.col-lg-7 -->

    </div><!-- /.row -->
  </div><!-- /.container -->
</section>
