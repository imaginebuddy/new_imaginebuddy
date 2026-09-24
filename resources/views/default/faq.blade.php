@extends('layouts.app')

@section('content')
<section class="section py-5 py-large faq-section position-relative min-vh-100" id="faq">
  <div class="container">

    <!-- Breadcrumb -->
    <div class="row mb-4">
      <div class="col-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 list-unstyled">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="bi-house-door me-1"></i> {{ __('misc.home') }}</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ __('admin.faq') }}</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="row g-4 g-lg-5">

      <!-- Left Column: Sticky Header & Search & Contact CTA -->
      <div class="col-12 col-lg-4">
        <div class="faq-sticky-header">
          <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            {{ __('admin.faq') }}
          </span>
          <h1 class="display-6 fw-bold title-custom mb-3" style="line-height: 1.2;">
            Frequently Asked Questions
          </h1>
          <p class="text-muted lead mb-4" style="font-size: 1.05rem;">
            Find quick answers to common questions about our AI prompts, commercial licenses, photoshoot sets, and subscription plans.
          </p>

          <!-- Search Box -->
          <div class="faq-search-box mb-4">
            <div class="input-group bg-card-custom rounded-pill border p-1 shadow-sm">
              <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                <i class="bi-search"></i>
              </span>
              <input type="text" id="faqSearchInput" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Search questions..." autocomplete="off">
              <button class="btn btn-sm btn-link text-muted pe-3 d-none" id="faqSearchClear" type="button" aria-label="Clear search">
                <i class="bi-x-circle-fill"></i>
              </button>
            </div>
          </div>

          <!-- Category Filter Pills -->
          @if ($categories->count() > 0)
          <div class="faq-categories-widget mb-4 d-none d-lg-block">
            <h6 class="text-uppercase fw-bold small text-muted mb-2" style="letter-spacing: 0.5px;">Browse by Topic</h6>
            <div class="d-flex flex-column gap-1" id="categoryFilterList">
              <button type="button" class="btn btn-sm text-start py-2 px-3 rounded-pill fw-semibold category-pill-btn active" data-category="all">
                <i class="bi-grid me-2"></i> All Topics
                <span class="badge bg-secondary-soft text-secondary rounded-pill float-end">{{ $faqs->count() }}</span>
              </button>
              @foreach ($categories as $cat)
              @php $countInCat = $faqs->where('category', $cat)->count(); @endphp
              <button type="button" class="btn btn-sm text-start py-2 px-3 rounded-pill fw-semibold category-pill-btn text-muted" data-category="{{ Str::slug($cat) }}">
                <i class="bi-bookmark me-2"></i> {{ $cat }}
                <span class="badge bg-secondary-soft text-secondary rounded-pill float-end">{{ $countInCat }}</span>
              </button>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Support Card Box -->
          <div class="faq-card bg-card-custom rounded-4 p-4 border shadow-xs d-none d-lg-block">
            <div class="d-flex align-items-center mb-3">
              <div class="rounded-circle bg-color-default text-white p-2 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                <i class="bi-headset fs-5"></i>
              </div>
              <div>
                <h6 class="title-custom fw-bold mb-0">Still need help?</h6>
                <small class="text-muted">Our team is here for you</small>
              </div>
            </div>
            <p class="text-muted small mb-3">
              Can't find the answer you're looking for? Reach out and we'll be happy to assist you.
            </p>
            <a href="{{ url('contact') }}" class="btn btn-sm btn-dark w-100 rounded-pill py-2">
              <i class="bi-envelope me-1"></i> {{ __('misc.contact') }}
            </a>
          </div>

        </div><!-- /.faq-sticky-header -->
      </div><!-- /.col-lg-4 -->

      <!-- Right Column: Accordion Items -->
      <div class="col-12 col-lg-8">

        <!-- Mobile Category Filter Pills Slider -->
        @if ($categories->count() > 0)
        <div class="d-lg-none mb-3 overflow-auto text-nowrap pb-2" style="scrollbar-width: none;">
          <div class="d-inline-flex gap-2" id="mobileCategoryFilterList">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold category-pill-btn active" data-category="all">
              All ({{ $faqs->count() }})
            </button>
            @foreach ($categories as $cat)
            @php $countInCat = $faqs->where('category', $cat)->count(); @endphp
            <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold category-pill-btn btn-outline-secondary" data-category="{{ Str::slug($cat) }}">
              {{ $cat }} ({{ $countInCat }})
            </button>
            @endforeach
          </div>
        </div>
        @endif

        <div class="faq-accordion-wrapper" id="faqAccordion">

          @if ($faqs->count() > 0)
            @foreach ($faqs as $index => $item)
            @php
              $itemCatSlug = $item->category ? Str::slug($item->category) : 'general';
              $isOpen = ($index === 0);
            @endphp
            <div class="faq-card bg-card-custom rounded-4 p-4 border border-custom mb-3 shadow-xs faq-item-wrapper"
                 data-category="{{ $itemCatSlug }}"
                 data-keywords="{{ strtolower($item->question . ' ' . strip_tags($item->answer) . ' ' . $item->category) }}">

              <button class="faq-toggle w-100 d-flex justify-content-between align-items-start border-0 bg-transparent text-start p-0 {{ $isOpen ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#faq-collapse-{{ $item->id }}"
                      aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                      aria-controls="faq-collapse-{{ $item->id }}">
                <div class="me-3">
                  @if ($item->category)
                  <div class="mb-2 pb-1">
                    <span class="badge badge-custom rounded-pill border text-muted fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.3px; padding: 0.3rem 0.75rem;">
                      {{ $item->category }}
                    </span>
                  </div>
                  @endif
                  <h2 class="h5 fw-bold title-custom mb-0 fs-6 fs-md-5" style="line-height: 1.45;">
                    {{ $item->question }}
                  </h2>
                </div>
                <span class="faq-icon-indicator text-muted fs-4 lh-1 flex-shrink-0 ms-2 mt-1"></span>
              </button>

              <div id="faq-collapse-{{ $item->id }}"
                   class="collapse {{ $isOpen ? 'show' : '' }} faq-collapse"
                   data-bs-parent="#faqAccordion">
                <div class="pt-3 text-muted faq-answer-content" style="line-height: 1.75; font-size: 0.98rem;">
                  {!! $item->answer !!}
                </div>
              </div>

            </div><!-- /.faq-card -->
            @endforeach

            <!-- Empty Search Results Message -->
            <div id="faqNoSearchResults" class="text-center p-5 text-muted d-none">
              <i class="bi-search display-5 d-block mb-3 opacity-50"></i>
              <h5 class="fw-semibold title-custom">No matching questions found</h5>
              <p class="small text-muted mb-3">Try adjusting your keywords or browse all topics.</p>
              <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-4" id="faqResetSearchBtn">
                Show All Questions
              </button>
            </div>

          @else
            <!-- Completely Empty State -->
            <div class="text-center p-5 text-muted bg-card-custom rounded-4 border">
              <i class="bi-patch-question display-4 d-block mb-3 opacity-50"></i>
              <h5 class="fw-semibold title-custom">Frequently Asked Questions coming soon</h5>
              <p class="small text-muted mb-3">Our team is currently preparing helpful answers for you.</p>
              <a href="{{ url('contact') }}" class="btn btn-sm btn-dark rounded-pill px-4">
                {{ __('misc.contact') }}
              </a>
            </div>
          @endif

        </div><!-- /.faq-accordion-wrapper -->

        <!-- Mobile Contact CTA Box -->
        <div class="d-lg-none mt-4">
          <div class="faq-card bg-card-custom rounded-4 p-4 border text-center shadow-xs">
            <h6 class="title-custom fw-bold mb-1">Still have questions?</h6>
            <p class="text-muted small mb-3">Can't find the answer you need? Get in touch with our support team.</p>
            <a href="{{ url('contact') }}" class="btn btn-sm btn-dark rounded-pill px-4">
              <i class="bi-envelope me-1"></i> {{ __('misc.contact') }}
            </a>
          </div>
        </div>

      </div><!-- /.col-lg-8 -->

    </div><!-- /.row -->

  </div><!-- /.container -->
</section>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('faqSearchInput');
  const searchClear = document.getElementById('faqSearchClear');
  const resetBtn = document.getElementById('faqResetSearchBtn');
  const faqItems = document.querySelectorAll('.faq-item-wrapper');
  const noResults = document.getElementById('faqNoSearchResults');
  const categoryPills = document.querySelectorAll('.category-pill-btn');

  let activeCategory = 'all';

  function filterFaqs() {
    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
    let visibleCount = 0;

    faqItems.forEach(function (item) {
      const itemCat = item.getAttribute('data-category');
      const itemKeywords = item.getAttribute('data-keywords') || '';

      const matchesCat = (activeCategory === 'all' || itemCat === activeCategory);
      const matchesSearch = (!query || itemKeywords.includes(query));

      if (matchesCat && matchesSearch) {
        item.classList.remove('d-none');
        visibleCount++;
      } else {
        item.classList.add('d-none');
      }
    });

    if (noResults) {
      if (visibleCount === 0 && (query || activeCategory !== 'all')) {
        noResults.classList.remove('d-none');
      } else {
        noResults.classList.add('d-none');
      }
    }

    if (searchClear) {
      if (query.length > 0) {
        searchClear.classList.remove('d-none');
      } else {
        searchClear.classList.add('d-none');
      }
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterFaqs);
  }

  if (searchClear) {
    searchClear.addEventListener('click', function () {
      searchInput.value = '';
      filterFaqs();
      searchInput.focus();
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (searchInput) searchInput.value = '';
      activeCategory = 'all';
      updateActiveCategoryPills('all');
      filterFaqs();
    });
  }

  function updateActiveCategoryPills(selectedCat) {
    categoryPills.forEach(function (btn) {
      const cat = btn.getAttribute('data-category');
      if (cat === selectedCat) {
        btn.classList.add('active');
        btn.classList.remove('text-muted', 'btn-outline-secondary');
        if (btn.closest('#mobileCategoryFilterList')) {
          btn.classList.add('btn-dark');
        }
      } else {
        btn.classList.remove('active', 'btn-dark');
        if (btn.closest('#mobileCategoryFilterList')) {
          btn.classList.add('btn-outline-secondary');
        } else {
          btn.classList.add('text-muted');
        }
      }
    });
  }

  categoryPills.forEach(function (btn) {
    btn.addEventListener('click', function () {
      activeCategory = this.getAttribute('data-category');
      updateActiveCategoryPills(activeCategory);
      filterFaqs();
    });
  });
});
</script>
@endsection
