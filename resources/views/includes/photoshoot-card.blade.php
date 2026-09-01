@php
  $coverImage = $photoshoot->images->first();
  $coverUrl = $coverImage ? Storage::url(config('path.preview') . $coverImage->preview) : asset('public/img/thumbnail-default.jpg');
  $categoryObj = $photoshoot->category ?: ($coverImage ? $coverImage->category : null);
  $promptCount = $photoshoot->prompts_count ?: ($photoshoot->images ? $photoshoot->images->count() : 0);
@endphp

<div class="col-sm-6 col-md-6 col-lg-4 mb-4 photoshoot-item">
  <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden photoshoot-card bg-card-custom border border-custom" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
    <a href="{{ url('photoshoots', $photoshoot->slug) }}" class="d-block overflow-hidden position-relative text-center photoshoot-cover-wrapper" style="border-radius: 16px 16px 0 0; min-height: 280px; max-height: 380px; background-color: #111;">
      <!-- Blurred Background Fill -->
      <img src="{{ $coverUrl }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" style="filter: blur(24px); transform: scale(1.2); opacity: 0.55; pointer-events: none;" />

      <!-- Crisp Full Main Image Centered -->
      <img src="{{ $coverUrl }}" alt="{{ $photoshoot->title }}" class="img-fluid w-100 h-100 position-relative photoshoot-cover-img" style="object-fit: contain; max-height: 380px; z-index: 2; transition: transform 0.4s ease;" />

      <div class="position-absolute top-0 end-0 m-3" style="z-index: 5;">
        <span class="badge bg-dark text-white rounded-pill px-3 py-2 fw-semibold shadow-sm" style="background-color: rgba(0,0,0,0.75) !important; font-size: 0.78rem;">
          <i class="bi bi-images me-1 text-mint"></i> {{ $promptCount }} {{ str_plural('Prompt', $promptCount) }}
        </span>
      </div>
    </a>

    <div class="card-body p-4 d-flex flex-column justify-content-between">
      <div>
        @if ($categoryObj)
          <div class="mb-2">
            <a href="{{ url('photoshoots') }}?category={{ $categoryObj->slug }}" class="badge bg-subtle-custom text-secondary border border-custom text-decoration-none rounded-pill px-3 py-1 fw-normal" style="font-size: 0.78rem;">
              {{ $categoryObj->name }}
            </a>
          </div>
        @endif

        <h5 class="fw-bold mb-2 photoshoot-card-title text-break" style="line-height: 1.35;">
          <a href="{{ url('photoshoots', $photoshoot->slug) }}" class="text-dark title-custom text-decoration-none photoshoot-title">
            {{ $photoshoot->title }}
          </a>
        </h5>

        @if ($photoshoot->description)
          <p class="text-muted small mb-3 line-clamp-2" style="font-size: 0.88rem; line-height: 1.5;">
            {{ str_limit($photoshoot->description, 180) }}
          </p>
        @endif
      </div>

      <div class="pt-3 border-top border-custom d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small" style="font-size: 0.82rem;">
          <i class="bi bi-clock me-1"></i> {{ Helper::formatDate($photoshoot->created_at) }}
        </span>

        <a href="{{ url('photoshoots', $photoshoot->slug) }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 fw-semibold">
          Explore Set <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>
