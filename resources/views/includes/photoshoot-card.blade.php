@php
  $photoshootImages = $photoshoot->images ? $photoshoot->images->take(3) : collect();
  $img0 = $photoshootImages->get(0);
  $img1 = $photoshootImages->get(1);
  $img2 = $photoshootImages->get(2);

  $getPhotoshootImgUrl = function($img) {
    if (!$img) return '';
    if (!empty($img->preview)) {
      return Storage::url(config('path.preview') . $img->preview);
    }
    if (!empty($img->thumbnail)) {
      return Storage::url(config('path.thumbnail') . $img->thumbnail);
    }
    $stock = $img->stock ? $img->stock->first() : null;
    if ($stock) {
      return Storage::url(config('path.small') . $stock->name);
    }
    return '';
  };

  $src0 = $getPhotoshootImgUrl($img0) ?: asset('public/img/thumbnail-default.jpg');
  $src1 = $getPhotoshootImgUrl($img1);
  $src2 = $getPhotoshootImgUrl($img2);

  $categoryObj = $photoshoot->category ?: ($img0 ? $img0->category : null);
  $promptCount = $photoshoot->prompts_count ?: ($photoshoot->images ? $photoshoot->images->count() : 0);
@endphp

<div class="col-sm-6 col-md-6 col-lg-4 mb-4 photoshoot-item">
  <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden photoshoot-card bg-card-custom border border-custom" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
    <a href="{{ url('photoshoots', $photoshoot->slug) }}" class="d-block overflow-hidden position-relative photoshoot-cover-wrapper text-decoration-none" style="border-radius: 16px 16px 0 0;">
      <div class="wrap-collection mb-0" style="margin-bottom: 0 !important; padding-bottom: 102%;">
        <div class="grid-collection" style="border-radius: 16px 16px 0 0; top: 0; left: 0;">
          <div class="collection-1">
            @if ($src0)
              <img class="img-collection" src="{{ $src0 }}" alt="{{ $photoshoot->title }} - Primary Shot" loading="lazy" decoding="async">
            @endif
          </div><!-- collection-1 -->

          <div class="collection-right">
            <div class="collection-2">
              @if ($src1)
                <img class="img-collection" src="{{ $src1 }}" alt="{{ $photoshoot->title }} - Angle 2" loading="lazy" decoding="async">
              @endif
            </div>

            <div class="collection-2">
              @if ($src2)
                <img class="img-collection" src="{{ $src2 }}" alt="{{ $photoshoot->title }} - Angle 3" loading="lazy" decoding="async">
              @endif
            </div>
          </div>
        </div><!-- grid-collection -->
      </div><!-- wrap-collection -->

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

        <h2 class="fw-bold mb-2 photoshoot-card-title text-break h5" style="line-height: 1.35;">
          <a href="{{ url('photoshoots', $photoshoot->slug) }}" class="text-dark title-custom text-decoration-none photoshoot-title">
            {{ $photoshoot->title }}
          </a>
        </h2>

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
