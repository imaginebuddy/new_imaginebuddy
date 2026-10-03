@php
  $coverUrl = $post->featured_image_url;
  $category = $post->category;
  $cardColumnClass = $cardCol ?? 'col-12 col-md-6 mb-4 blog-item';
@endphp

<div class="{{ $cardColumnClass }}">
  <article class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden blog-card bg-card-custom border border-custom" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
    <!-- Featured Image Link -->
    <a href="{{ url('blog', $post->slug) }}" class="d-block overflow-hidden position-relative text-center blog-cover-wrapper" style="border-radius: 16px 16px 0 0; min-height: 220px; max-height: 260px; background-color: #111;">
      <!-- Blurred Background Fill -->
      <img src="{{ $coverUrl }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" style="filter: blur(20px); transform: scale(1.15); opacity: 0.55; pointer-events: none;" />

      <!-- Crisp Main Image Centered -->
      <img src="{{ $coverUrl }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy" class="img-fluid w-100 h-100 position-relative blog-cover-img" style="object-fit: cover; max-height: 260px; z-index: 2; transition: transform 0.4s ease;" />

      <div class="position-absolute top-0 end-0 m-3" style="z-index: 5;">
        <span class="badge bg-dark text-white rounded-pill px-3 py-1 fw-semibold shadow-sm" style="background-color: rgba(0,0,0,0.7) !important; font-size: 0.75rem;">
          <i class="bi bi-clock me-1 text-mint"></i> {{ $post->reading_time }} min read
        </span>
      </div>
    </a>

    <div class="card-body p-4 d-flex flex-column justify-content-between">
      <div>
        @if ($category)
          <div class="mb-2">
            <a href="{{ url('blog/category', $category->slug) }}" class="badge bg-subtle-custom text-secondary border border-custom text-decoration-none rounded-pill px-3 py-1 fw-medium" style="font-size: 0.78rem;">
              {{ $category->name }}
            </a>
          </div>
        @endif

        <h3 class="fw-bold mb-2 blog-card-title text-break h5" style="line-height: 1.35;">
          <a href="{{ url('blog', $post->slug) }}" class="text-dark title-custom text-decoration-none blog-title">
            {{ $post->title }}
          </a>
        </h3>

        @if ($post->excerpt)
          <p class="text-muted small mb-3" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $post->excerpt }}
          </p>
        @endif
      </div>

      <div class="pt-3 border-top border-custom d-flex justify-content-between align-items-center mt-3">
        <div class="d-flex align-items-center gap-2 text-muted small" style="font-size: 0.82rem;">
          @if ($post->user && $post->user->avatar)
            <img src="{{ Storage::url(config('path.avatar') . $post->user->avatar) }}" alt="{{ $post->user->username }}" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
          @else
            <i class="bi bi-person-circle"></i>
          @endif
          <span>{{ $post->user ? ($post->user->name ?: $post->user->username) : 'Editor' }}</span>
        </div>

        <a href="{{ url('blog', $post->slug) }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem;">
          Read <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </article>
</div>
