@php
  $collectionPrompts = $collection->prompts ? $collection->prompts->take(3) : collect();
  $img0 = $collectionPrompts->get(0);
  $img1 = $collectionPrompts->get(1);
  $img2 = $collectionPrompts->get(2);

  $getPromptImgUrl = function($img) {
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

  $src0 = $getPromptImgUrl($img0) ?: $collection->cover_image_url;
  $src1 = $getPromptImgUrl($img1);
  $src2 = $getPromptImgUrl($img2);

  $categoryObj = $collection->category ?: ($img0 ? $img0->category : null);
  $promptCount = $collection->prompts_count ?: ($collection->prompts ? $collection->prompts->count() : 0);
  $collectionSummary = $collection->summary ?: $collection->description;
@endphp

<div class="col-sm-6 col-md-6 col-lg-4 mb-4 photoshoot-item prompt-collection-item">
  <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden photoshoot-card bg-card-custom border border-custom" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
    <a href="{{ url('prompts', $collection->slug) }}" class="d-block overflow-hidden position-relative photoshoot-cover-wrapper text-decoration-none" style="border-radius: 16px 16px 0 0;">
      <div class="wrap-collection mb-0" style="margin-bottom: 0 !important; padding-bottom: 102%;">
        <div class="grid-collection" style="border-radius: 16px 16px 0 0; top: 0; left: 0;">
          <div class="collection-1">
            @if ($src0)
              <img class="img-collection" src="{{ $src0 }}" alt="{{ $collection->title }} - Primary Shot" loading="lazy" decoding="async">
            @endif
          </div><!-- collection-1 -->

          <div class="collection-right">
            <div class="collection-2">
              @if ($src1)
                <img class="img-collection" src="{{ $src1 }}" alt="{{ $collection->title }} - Angle 2" loading="lazy" decoding="async">
              @endif
            </div>

            <div class="collection-2">
              @if ($src2)
                <img class="img-collection" src="{{ $src2 }}" alt="{{ $collection->title }} - Angle 3" loading="lazy" decoding="async">
              @endif
            </div>
          </div>
        </div><!-- grid-collection -->
      </div><!-- wrap-collection -->
    </a>

    <div class="card-body p-4 d-flex flex-column justify-content-between">
      <div>
        <div class="mb-2 d-flex align-items-center flex-wrap gap-2">
          @if ($categoryObj)
            <a href="{{ url('prompts') }}?category={{ $categoryObj->slug }}" class="badge bg-subtle-custom text-secondary border border-custom text-decoration-none rounded-pill px-3 py-1 fw-normal" style="font-size: 0.78rem;">
              {{ $categoryObj->name }}
            </a>
          @endif

          <span class="badge bg-subtle-custom text-secondary border border-custom rounded-pill px-2.5 py-1 fw-normal d-inline-flex align-items-center" style="font-size: 0.78rem; gap: 5px;">
            <i class="bi bi-images text-mint" style="font-size: 11px;"></i>
            <span>{{ $promptCount }} {{ str_plural('Prompt', $promptCount) }}</span>
          </span>
        </div>

        <h2 class="fw-bold mb-2 photoshoot-card-title text-break h5" style="line-height: 1.35;">
          <a href="{{ url('prompts', $collection->slug) }}" class="text-dark title-custom text-decoration-none photoshoot-title">
            {{ $collection->title }}
          </a>
        </h2>

        @if ($collectionSummary)
          <p class="text-muted small mb-3 line-clamp-2" style="font-size: 0.88rem; line-height: 1.5;">
            {{ str_limit($collectionSummary, 180) }}
          </p>
        @endif
      </div>

      <div class="pt-3 border-top border-custom d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small" style="font-size: 0.82rem;">
          <i class="bi bi-clock me-1"></i> {{ Helper::formatDate($collection->created_at) }}
        </span>

        <a href="{{ url('prompts', $collection->slug) }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 fw-semibold">
          Explore Set <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>
