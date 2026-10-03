@extends('layouts.app')

@section('content')
<section class="section section-sm py-4" style="padding-top: 95px !important;">
  <div class="container" style="max-width: 1260px;">

    <!-- Top Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <nav aria-label="breadcrumb">
        <div class="breadcrumb-pill-box rounded-pill shadow-sm border border-custom px-3 px-md-4 py-2 d-inline-flex align-items-center bg-card-custom">
          <ol class="breadcrumb mb-0 align-items-center list-unstyled">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted"><i class="bi-house-door me-1"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('blog') }}" class="text-decoration-none text-muted">Blog</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $category->name }}</li>
          </ol>
        </div>
      </nav>
    </div>

    <!-- Category Header -->
    <div class="text-center pt-2 pt-md-3 pb-3 mb-4">
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.75rem;">
        <i class="bi-folder2-open me-1 text-mint"></i> Category Archive
      </span>
      <h1 class="fw-bold text-dark title-custom display-5 mb-3">
        {{ $category->name }}
      </h1>
      @if ($category->description)
        <p class="lead text-muted mx-auto" style="max-width: 720px; font-size: 1.1rem;">
          {{ $category->description }}
        </p>
      @else
        <p class="lead text-muted mx-auto" style="max-width: 720px; font-size: 1.1rem;">
          Explore all tutorials, prompt workflows, and articles published in {{ $category->name }}.
        </p>
      @endif
    </div>

    <!-- Category Filter Bar -->
    @if ($categories->count() > 0)
    <div class="blog-filter-wrapper mb-5 position-relative">
      <div class="blog-category-scroll d-flex align-items-center gap-2 overflow-x-auto py-2 px-1">
        <a href="{{ url('blog') }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 btn-outline-custom">
          All Topics
        </a>
        @foreach ($categories as $cat)
          <a href="{{ url('blog/category', $cat->slug) }}" class="btn btn-sm rounded-pill px-4 py-2 text-nowrap flex-shrink-0 {{ $category->id == $cat->id ? 'btn-dark fw-bold' : 'btn-outline-custom' }}">
            {{ $cat->name }} <span class="badge bg-secondary-soft text-secondary rounded-pill ms-1">{{ $cat->published_posts_count }}</span>
          </a>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Articles Grid & Sidebar Layout -->
    <div class="row g-4 g-lg-5">
      <!-- Main Articles Column -->
      <div class="col-12 col-lg-8">
        @if ($posts->total() > 0)
          <div class="row g-4" id="blogPostsContainer">
            @foreach ($posts as $post)
              @include('includes.blog-card', ['post' => $post])
            @endforeach
          </div>

          <!-- Pagination -->
          @if ($posts->hasPages())
            <div class="d-flex justify-content-center mt-5">
              {{ $posts->onEachSide(1)->links() }}
            </div>
          @endif

        @else
          <div class="text-center py-5 my-4 bg-card-custom rounded-4 border border-custom p-5">
            <div class="mb-3">
              <i class="bi-journal-x text-muted opacity-50" style="font-size: 3.5rem;"></i>
            </div>
            <h3 class="fw-bold text-dark title-custom mb-2">No articles in this category yet</h3>
            <p class="text-muted mb-4">
              We are working on fresh guides for {{ $category->name }}. Check back soon!
            </p>
            <a href="{{ url('blog') }}" class="btn btn-dark rounded-pill px-4">
              <i class="bi-arrow-left me-1"></i> Browse All Articles
            </a>
          </div>
        @endif
      </div><!-- /.col-lg-8 -->

      <!-- Sidebar Column -->
      <div class="col-12 col-lg-4">
        <aside class="blog-sidebar">
          
          <!-- Category List Widget -->
          @if ($categories->count() > 0)
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-card-custom border border-custom">
            <h4 class="title-custom fw-bold h6 mb-3 d-flex align-items-center">
              <i class="bi-folder2-open me-2 text-mint"></i> All Topics
            </h4>
            <div class="list-group list-group-flush">
              @foreach ($categories as $cat)
                <a href="{{ url('blog/category', $cat->slug) }}" class="list-group-item list-group-item-action bg-transparent px-0 py-2 d-flex justify-content-between align-items-center border-custom {{ $cat->id == $category->id ? 'fw-bold text-primary' : '' }}">
                  <span>{{ $cat->name }}</span>
                  <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ $cat->published_posts_count }}</span>
                </a>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Popular Articles Widget -->
          @if ($popularPosts->count() > 0)
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-card-custom border border-custom">
            <h4 class="title-custom fw-bold h6 mb-3 d-flex align-items-center">
              <i class="bi-fire me-2 text-danger"></i> Popular Guides
            </h4>
            <div class="d-flex flex-column gap-3">
              @foreach ($popularPosts as $pop)
                <div class="d-flex gap-3 align-items-center">
                  <a href="{{ url('blog', $pop->slug) }}" class="flex-shrink-0 rounded-3 overflow-hidden d-block" style="width: 72px; height: 56px;">
                    <img src="{{ $pop->featured_image_url }}" alt="{{ $pop->title }}" class="w-100 h-100 object-fit-cover">
                  </a>
                  <div>
                    <h5 class="small fw-bold mb-1" style="line-height: 1.3;">
                      <a href="{{ url('blog', $pop->slug) }}" class="text-dark title-custom text-decoration-none">
                        {{ Str::limit($pop->title, 55) }}
                      </a>
                    </h5>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                      <i class="bi-clock me-1"></i> {{ $pop->reading_time }} min read
                    </small>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- CTA Banner Box -->
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white border-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, #10141a 0%, #171d26 50%, #0d1219 100%); border: 1px solid rgba(0, 214, 144, 0.3) !important;">
            <div class="position-relative" style="z-index: 2;">
              <span class="badge bg-custom-mint text-white rounded-pill px-3 py-1 fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                AI Photoshoot Sets
              </span>
              <h4 class="fw-bold mb-2 h5" style="color: #ffffff !important; line-height: 1.35;">Consistent AI Character &amp; Product Sets</h4>
              <p class="text-white-50 small mb-3">
                Stop struggling with random variations. Browse curated photoshoot prompt sessions.
              </p>
              <a href="{{ url('photoshoots') }}" class="btn btn-sm btn-custom rounded-pill w-100 py-2 fw-semibold">
                Explore Photoshoots <i class="bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>

        </aside>
      </div><!-- /.col-lg-4 -->
    </div><!-- /.row -->

  </div>
</section>

<style>
.blog-category-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(150, 150, 150, 0.3) transparent;
  -webkit-overflow-scrolling: touch;
}
.blog-category-scroll::-webkit-scrollbar {
  height: 4px;
}
.blog-category-scroll::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.3);
  border-radius: 4px;
}
.blog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08) !important;
}
.blog-card:hover .blog-cover-img {
  transform: scale(1.04);
}
.bg-secondary-soft {
  background-color: rgba(108, 117, 125, 0.12);
}
</style>
@endsection
