<div id="{{ isset($imgFeatured) ? 'imagesFlexFeatured' : 'imagesFlex' }}" class="flex-images d-block">
	@foreach ($images as $image)

		@php
			$colors = explode(",", $image->colors);
			$color = $colors[0] ?? 'e4e4e4';

			if ($image->extension == 'png') {
				$background = 'background: url('.url('public/img/pixel.gif').') repeat center center #e4e4e4;';
			} else {
				$background = 'background-color: #'.$color.';';
			}

			$stockImage = $image->stock ? ($image->stock->where('type', 'medium')->first() ?: $image->stock->first()) : null;
			$newWidth   = 480;
			$newHeight  = 740;

			$previewName = $image->preview ?: ($stockImage ? $stockImage->name : '');
			$previewUrl  = $image->preview ? Storage::url(config('path.preview').$image->preview) : ($stockImage ? url('files/preview/'.$stockImage->resolution, $stockImage->name).'?size=medium&type='.$image->item_for_sale : asset('public/uploads/preview/' . $previewName));
		@endphp

		<div class="item hovercard prompt-card-item rounded-4 overflow-hidden position-relative d-block text-decoration-none shadow-sm prompt-card-container" data-w="{{$newWidth}}" data-h="{{$newHeight}}">
			<div class="card border-0 rounded-4 overflow-hidden h-100 d-flex flex-column justify-content-between p-2 prompt-card-bg">
				
				<!-- 1. Top Image Section with Badges Overlay -->
				<div class="position-relative overflow-hidden w-100 flex-grow-1 rounded-4" style="{{$background}}">
					<!-- Top Badges Overlay -->
					<div class="prompt-card-top-bar d-flex justify-content-between align-items-center position-absolute top-0 start-0 w-100 p-3" style="z-index: 5; pointer-events: none;">
						<!-- Top Left Dark Translucent Badge: Category/Model -->
						<span class="badge text-white fw-medium rounded-pill px-3 py-2 shadow-sm text-capitalize d-inline-flex align-items-center gap-1" style="pointer-events: auto; font-size: 12px; background-color: rgba(24, 43, 23, 0.85); backdrop-filter: blur(8px);">
							@if ($image->item_for_sale == 'sale')
								<i class="fa fa-crown text-warning me-1" title="Premium Prompt"></i>
							@endif
							@if ($image->category)
								{{ ucfirst(strtolower($image->category->name)) }}
							@elseif ($image->ai_model)
								{{ ucfirst(strtolower($image->ai_model)) }}
							@else
								Prompt
							@endif
						</span>

						<!-- Top Right White Circle: Heart Icon + Count -->
						<div class="prompt-badge-circle fw-bold rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center" style="pointer-events: auto; width: 36px; height: 36px;">
							<i class="bi bi-heart text-danger" style="font-size: 13px; line-height: 1;"></i>
							<span class="small lh-1 prompt-badge-circle-text" style="font-size: 10px; font-weight: 700; margin-top: 1px;">{{ Helper::formatNumber($image->likes()->count() ?: $image->copies_count) }}</span>
						</div>
					</div>

					<!-- Full Cover Image Link -->
					<a href="{{ url('prompt', $image->slug) }}" class="d-block w-100 h-100 text-decoration-none">
						<img alt="{{ $image->title }}" class="previewImage prompt-card-img rounded-4" src="{{ $previewUrl }}" data-src="{{ $previewUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
					</a>
				</div>

				<!-- 2. Bottom Details Section (Title, Subtext, Author, Share & Copy) PLACED AFTER THE IMAGE -->
				<div class="prompt-card-bottom-info px-2 pb-2 pt-3 position-relative prompt-card-info-bg" style="z-index: 6;">
					<!-- Title -->
					<h3 class="fw-bold mb-2 prompt-card-title text-break h5" style="font-size: 17px; line-height: 1.35; font-family: system-ui, -apple-system, sans-serif; letter-spacing: -0.3px;">
						<a href="{{ url('prompt', $image->slug) }}" class="text-decoration-none prompt-title-link" title="{{ $image->title }}">
							{{ $image->title }}
						</a>
					</h3>

					<!-- Prompt Snippet (Subtext) -->
					<p class="small mb-3 prompt-snippet-text line-clamp-2" style="font-size: 13px; line-height: 1.4;">
						{{ Str::limit($image->prompt ?: $image->description ?: $image->title, 160, '...') }}
					</p>

					<!-- Bottom Action Controls Bar -->
					<div class="d-flex align-items-center justify-content-between gap-2">
						<!-- Author Pill Badge (Left Side) -->
						<span class="badge fw-bold rounded-pill px-3 py-2 text-truncate prompt-author-pill" style="font-size: 13px; max-width: 185px;">
							By {{ $image->author->name ?: $image->author->username }}
						</span>

						<!-- Right Action Buttons (Share Circle + Copy Pill with Icon) -->
						<div class="d-flex align-items-center gap-2 flex-shrink-0">
							<!-- Share Circle Button -->
							<button type="button" class="btn rounded-circle btn-sm shadow-none btn-share-prompt prompt-share-btn d-inline-flex align-items-center justify-content-center" data-url="{{ url('prompt', $image->slug) }}" data-title="{{ $image->title }}" style="width: 36px; height: 36px;" title="Share">
								<i class="bi bi-share-fill" style="font-size: 13px;"></i>
							</button>

							<!-- Copy Pill Button -->
							<button type="button" class="btn text-white rounded-pill btn-sm fw-bold btn-copy-prompt-grid prompt-copy-btn shadow-none d-inline-flex align-items-center justify-content-center" data-id="{{ $image->id }}" title="Copy Prompt">
								<span class="prompt-copy-text">Copy</span>
								<span class="prompt-copy-icon-bg">
									<i class="bi bi-copy"></i>
								</span>
							</button>
						</div>
					</div>
				</div>

			</div>
		</div>
	@endforeach
</div><!-- flex-images -->

@if ($images->count() && request()->ajax())
	@include('includes.pagination-links')
@endif
