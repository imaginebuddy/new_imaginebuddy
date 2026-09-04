@extends('layouts.app')

@if ($response->meta_title)
@section('title'){{ $response->meta_title }}@endsection
@else
@section('title'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }}@endsection
@endif

@if ($response->meta_description)
@section('description_override'){{ Helper::removeLineBreak(e($response->meta_description)) }}@endsection
@else
@section('description_custom'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }} @if ($response->description != ''){{ Helper::removeLineBreak(e($response->description)).' - ' }}@endif @endsection
@endif

@if ($response->meta_keywords)
@section('keywords_override'){{ $response->meta_keywords }}@endsection
@else
@section('keywords_custom'){{ $response->tags . ',' }}@endsection
@endif

@section('css')
<meta property="og:type" content="website" />
<meta property="og:image:width" content="{{$previewWidth}}"/>
<meta property="og:image:height" content="{{$previewHeight}}"/>

<meta property="og:site_name" content="{{$settings->title}}"/>
<meta property="og:url" content="{{ url('prompt', $response->slug) }}"/>
<meta property="og:image" content="{{ asset('public/uploads/preview/' . $response->preview) }}"/>
<meta property="og:title" content="{{ $response->meta_title ?: ($response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id) }}"/>
<meta property="og:description" content="{{ $response->meta_description ? Helper::removeLineBreak(e($response->meta_description)) : Helper::removeLineBreak(e($response->description)) }}"/>

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{ asset('public/uploads/preview/' . $response->preview) }}" />
<meta name="twitter:title" content="{{ $response->meta_title ?: ($response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id) }}" />
<meta name="twitter:description" content="{{ $response->meta_description ? Helper::removeLineBreak(e($response->meta_description)) : Helper::removeLineBreak(e($response->description)) }}"/>

<style>
@media (min-width: 992px) {
  .sticky-preview-container {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 95px !important;
    z-index: 10 !important;
  }
}
</style>
@endsection

@section('content')

@if ($response->item_for_sale == 'free' && $response->author->id != auth()->id())
  <!-- start thanks to author sharing -->
  <div class="fixed-bottom display-none" id="alertThanks">
    <div class="d-flex justify-content-center align-items-center">
      <div class="alert-thanks bg-white border shadow-sm mb-3 mx-2 position-relative alert-dismissible">
        <button type="button" class="btn-close text-dark" id="closeThanks">
          <i class="bi bi-x-lg"></i>
        </button>

        <div class="d-flex">
          <div class="flex-shrink-0">
            <img class="img-fluid rounded img-thanks-share" width="100" src="{{ Storage::url(config('path.thumbnail').$response->thumbnail) }}" />
          </div>
          <div class="flex-grow-1 ms-3">
            <h5>{{ __('misc.give_thanks') }} <i class="bi-stars text-warning"></i></h5>
            {!! __('misc.thanks_to_author_sharing', ['username' => '<strong>'.$response->author->username.'</strong>']) !!}

            <ul class="list-inline mt-2 fs-5">
              <li class="list-inline-item me-3"><a class="btn-facebook-share" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="fab fa-facebook"></i></a></li>
              <li class="list-inline-item me-3"><a class="text-dark" title="Twitter" href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ e( $response->title ) }}" data-url="{{ url()->current() }}" target="_blank"><i class="bi-twitter-x"></i></a></li>
              <li class="list-inline-item me-3"><a class="btn-pinterest-share" title="Pinterest" href="//www.pinterest.com/pin/create/button/?url={{ url()->current() }}&media={{ asset('public/uploads/preview/' . $response->preview) }}&description={{ e( $response->title ) }}" target="_blank"><i class="fab fa-pinterest"></i></a></li>
              <li class="list-inline-item"><a class="btn-whatsapp-share" title="Whatsapp" href="whatsapp://send?text={{ url()->current() }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
             </ul>
          </div>
        </div>
      </div>
    </div>
  </div><!-- thanks to author sharing -->
@endif

@auth
<div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title text-center" id="myModalLabel">
          {{ __('misc.add_collection') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- Modal header -->

      <div class="modal-body">
        <div class="collectionsData">
          @if (isset($collections) && $collections->count() != 0)
            @foreach ($collections as $collection)
              @php
                $collectionImages = $collection->collectionImages->where('images_id', $response->id)->where('collections_id', $collection->id)->first();
                $checked = $collectionImages ? 'checked="checked"' : null;
              @endphp

              <div class="form-check mb-1">
                <input class="no-show form-check-input addImageCollection" data-image-id="{{$response->id}}" data-collection-id="{{$collection->id}}" name="checked" {{$checked}} type="checkbox" value="true" id="collectionCheck{{$collection->id}}">
                <label class="form-check-label text-overflow" for="collectionCheck{{$collection->id}}">
                  {{$collection->title}}
                </label>
              </div>
            @endforeach
          @else
            <div class="d-block text-center no-collections mb-1 p-3 bg-warning rounded"><i class="bi bi-exclamation-circle me-1"></i> {{ __('misc.no_have_collections') }}</div>
          @endif
        </div><!-- collection data -->

        <div class="alert alert-danger display-none mt-2" id="dangerAlertCollection">
          <ul class="list-unstyled m-0" id="showErrorsCollection"></ul>
        </div>

        <form method="POST" action="{{ url('collection/store') }}" id="addCollectionForm">
          @csrf
          <input type="hidden" name="image_id" value="{{$response->id}}">
          <div class="form-group mt-3">
            <input type="text" class="form-control" name="title" id="titleCollection" placeholder="{{ __('misc.collection_name') }}" required>
          </div>
          @if (auth()->user()->isSuperAdmin())
            <div class="form-check form-switch my-3">
              <input class="form-check-input" name="type" type="checkbox" value="private" id="collectionPrivateSwitch">
              <label class="form-check-label text-dark fw-medium small" for="collectionPrivateSwitch">Make Collection Private (Only visible to you)</label>
            </div>
          @endif
          <div class="form-group mt-2">
            <button type="submit" class="btn btn-custom w-100" id="addCollection">{{ __('misc.create_collection') }}</button>
          </div>
        </form>
      </div><!-- Modal body -->
    </div><!-- Modal content -->
  </div><!-- Modal dialog -->
</div><!-- Modal -->
@endauth

<section class="section section-sm py-4" style="padding-top: 95px !important;">
  <div class="container">

    <!-- Top Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <nav aria-label="breadcrumb">
        <div class="breadcrumb-pill-box rounded-pill shadow-sm border px-4 py-2 d-inline-flex align-items-center">
          <ol class="breadcrumb mb-0 align-items-center">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('categories') }}" class="text-decoration-none">Categories</a></li>
            @if ($response->category)
              <li class="breadcrumb-item"><a href="{{ url('category', $response->category->slug) }}" class="text-decoration-none">{{ $response->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
              <span class="badge bg-custom-mint text-white rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem; letter-spacing: -0.2px;">{{ $response->title }}</span>
            </li>
          </ol>
        </div>
      </nav>
    </div>

    @if ($response->status == 'pending')
      <div class="alert alert-warning mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('misc.pending_approval') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    @endif

    @php
      $showPreviewUrl = asset('public/uploads/preview/' . $response->preview);
      $stockToken = $response->token_id;

      $canViewPrompt = true;
      if ($response->item_for_sale == 'sale') {
        if (auth()->guest()) {
          $canViewPrompt = false;
        } else {
          $canViewPrompt = ($getSubscription || auth()->id() == $response->user_id || auth()->user()->isSuperAdmin());
        }
      }
    @endphp

    <!-- Main Content Grid Row with Floating Left Column -->
    <div class="row position-relative">

      <!-- LEFT COLUMN: Floating Fixed Image Preview -->
      <div class="col-lg-6 mb-4">
        <div class="sticky-preview-container">
          <div class="p-3 bg-white shadow-sm border border-0 left-image-card-box" style="border-radius: 28px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;">
            <div class="position-relative overflow-hidden" style="border-radius: 20px !important;">
              
              @if ($settings->lightbox == 'on')
                <a href="{{ $showPreviewUrl }}" class="glightbox d-block main-preview-link text-center" style="cursor: zoom-in;" data-gallery="prompt-examples">
                  <img id="mainPreviewImg" alt="{{ $response->title }}" class="img-fluid w-100 d-block mx-auto" style="object-fit: contain; width: 100%; max-height: 480px; border-radius: 20px !important;" src="{{ $showPreviewUrl }}" />
                </a>
              @else
                <img id="mainPreviewImg" alt="{{ $response->title }}" class="img-fluid w-100 d-block mx-auto" style="object-fit: contain; width: 100%; max-height: 480px; border-radius: 20px !important;" src="{{ $showPreviewUrl }}" />
              @endif

              <!-- Floating Download Icon Button (Solid Black Rounded Box) -->
              <div class="position-absolute bottom-0 start-0 p-3" style="z-index: 15;">
                @if (auth()->check())
                  @if ($response->item_for_sale == 'free' || auth()->id() == $response->user_id)
                    <form action="{{ url('download/stock', $stockToken) }}" method="post" class="d-inline">
                      @csrf
                      <input type="hidden" name="type" value="small">
                      <button type="submit" class="btn btn-dark p-0 shadow-sm d-inline-flex align-items-center justify-content-center btnDownload" style="width: 38px; height: 38px; background-color: #000000 !important; border-radius: 10px !important; border: none !important;" title="{{ __('misc.download') }}">
                        <i class="bi bi-download text-white fs-6"></i>
                      </button>
                    </form>
                  @elseif ($getSubscription && auth()->user()->downloads != 0)
                    <form action="{{ url('subscription/stock', $stockToken) }}" method="post" class="d-inline">
                      @csrf
                      <input type="hidden" name="type" value="small">
                      <button type="submit" class="btn btn-dark p-0 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #000000 !important; border-radius: 10px !important; border: none !important;" title="{{ __('misc.download') }}">
                        <i class="bi bi-download text-white fs-6"></i>
                      </button>
                    </form>
                  @else
                    <a href="{{ url('pricing') }}" class="btn btn-dark p-0 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #000000 !important; border-radius: 10px !important; border: none !important;" title="{{ __('misc.download') }}">
                      <i class="bi bi-download text-white fs-6"></i>
                    </a>
                  @endif
                @else
                  <a href="{{ url('login') }}" class="btn btn-dark p-0 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #000000 !important; border-radius: 10px !important; border: none !important;" title="{{ __('misc.download') }}">
                    <i class="bi bi-download text-white fs-6"></i>
                  </a>
                @endif
              </div>

            </div>

            @if ($response->examples && $response->examples->count() > 0)
              <!-- Example Outputs Gallery Thumbnails Strip -->
              <div class="mt-3 pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">
                    <i class="bi bi-images me-1 text-primary"></i> Generated Example Outputs ({{ $response->examples->count() + 1 }})
                  </span>
                  <small class="text-muted" style="font-size: 0.75rem;">Click to view full size</small>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <!-- Main Image Thumbnail -->
                  <div class="example-thumb-wrapper active-thumb p-1 rounded-3 border border-primary shadow-sm" style="cursor: pointer; width: 68px; height: 68px; transition: all 0.2s;" data-full-url="{{ $showPreviewUrl }}">
                    <img src="{{ $showPreviewUrl }}" class="w-100 h-100 rounded-2" style="object-fit: cover;" alt="Main Preview">
                  </div>
                  
                  <!-- Additional Example Output Thumbnails -->
                  @foreach ($response->examples as $index => $example)
                    @php $exUrl = Storage::url(config('path.examples') . $example->file); @endphp
                    <div class="example-thumb-wrapper p-1 rounded-3 border" style="cursor: pointer; width: 68px; height: 68px; transition: all 0.2s;" data-full-url="{{ $exUrl }}">
                      @if ($settings->lightbox == 'on')
                        <a href="{{ $exUrl }}" class="glightbox" data-gallery="prompt-examples">
                          <img src="{{ $exUrl }}" class="w-100 h-100 rounded-2" style="object-fit: cover;" alt="Example Output {{ $index + 1 }}">
                        </a>
                      @else
                        <img src="{{ $exUrl }}" class="w-100 h-100 rounded-2" style="object-fit: cover;" alt="Example Output {{ $index + 1 }}">
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: Details & Actions -->
      <div class="col-lg-6 mb-4">
        
        <!-- 1. Edit & Delete Action Buttons (if Author or Admin) -->
        @if (auth()->check() && (auth()->id() == $response->author->id || auth()->user()->isSuperAdmin()))
          <div class="d-flex align-items-center gap-2 mb-3">
            <a class="btn btn-sm btn-outline-blue-pill rounded-pill px-4 py-1 text-decoration-none fw-semibold" href="{{ url('edit/photo', $response->id) }}">{{ __('admin.edit') }}</a>
            <form method="POST" action="{{ url('delete/photo', $response->id) }}" class="d-inline">
              @csrf
              <button type="button" class="btn btn-sm btn-outline-red-pill rounded-pill px-4 py-1 fw-semibold" id="deletePhoto">
                {{ __('admin.delete') }}
              </button>
            </form>
          </div>
        @endif

        <!-- 2. Author Profile Header Bar -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="d-flex align-items-center gap-3">
            <a href="{{ url($response->author->username) }}">
              <img class="rounded-circle" src="{{ Storage::url(config('path.avatar').$response->author->avatar) }}" width="55" height="55" alt="{{ $response->author->username }}" style="object-fit: cover;">
            </a>
            <div>
              <a href="{{ url($response->author->username) }}" class="text-decoration-none link-dark fw-bold fs-4 d-block title-custom" style="line-height: 1.2;">
                {{ '@' . ($response->author->username ?: $response->author->name) }}
              </a>
              <small class="text-muted" style="font-size: 0.85rem;">{{ number_format(User::totalImages($response->author->id)) }} Prompts</small>
            </div>
          </div>

          @if (auth()->check() && $response->author->id != auth()->id())
            <button type="button" class="btn btn-dark rounded-pill px-4 py-2 me-1 fw-semibold btnFollow btn-follow {{ $activeFollow }}" data-id="{{ $response->author->id }}" data-follow="{{ __('users.follow') }}" data-following="{{ __('users.following') }}">
              <i class="bi bi{{ $icoFollow }} me-1"></i> {{ $textFollow }}
            </button>
          @endif
        </div>

        <!-- 3. Stats & Action Buttons Row -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
          <!-- Left Stats (Views, Likes, Downloads) -->
          <div class="d-flex align-items-center gap-4">
            <span class="text-muted small d-inline-flex align-items-center gap-2" style="font-size: 0.95rem;">
              <i class="bi bi-eye fs-5"></i> {{ Helper::formatNumber($response->visits()->count()) }}
            </span>
            <span class="text-muted small d-inline-flex align-items-center gap-2" style="font-size: 0.95rem;">
              <i class="bi bi-heart fs-5"></i> <span id="countLikes">{{ Helper::formatNumber($response->likes()->count()) }}</span>
            </span>
            <span class="text-muted small d-inline-flex align-items-center gap-2" style="font-size: 0.95rem;">
              <i class="bi bi-download fs-5"></i> {{ Helper::formatNumber($response->downloads()->count()) }}
            </span>
          </div>

          <!-- Right Action Buttons (Like, Collection) -->
          <div class="d-flex align-items-center gap-2 me-1">
            @if (auth()->check())
              <button class="btn btn-outline-pill-action rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 btnLike likeButton {{ $statusLike }}" data-id="{{ $response->id }}" data-like="Like" data-unlike="Liked">
                <span class="fw-semibold textLike {{ $statusLike ? 'text-danger' : '' }}">{{ $statusLike ? 'Liked' : 'Like' }}</span>
                <span class="action-icon-circle rounded-circle d-inline-flex align-items-center justify-content-center">
                  <i class="{{ $icoLike }}"></i>
                </span>
              </button>
              <button class="btn btn-outline-pill-action rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#collections">
                <span class="fw-semibold">Collection</span>
                <span class="action-icon-circle rounded-circle d-inline-flex align-items-center justify-content-center">
                  <i class="bi bi-plus-square"></i>
                </span>
              </button>
            @else
              <a href="{{ url('login') }}" class="btn btn-outline-pill-action rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 text-decoration-none">
                <span class="fw-semibold">Like</span>
                <span class="action-icon-circle rounded-circle d-inline-flex align-items-center justify-content-center">
                  <i class="bi bi-heart"></i>
                </span>
              </a>
              <a href="{{ url('login') }}" class="btn btn-outline-pill-action rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 text-decoration-none">
                <span class="fw-semibold">Collection</span>
                <span class="action-icon-circle rounded-circle d-inline-flex align-items-center justify-content-center">
                  <i class="bi bi-plus-square"></i>
                </span>
              </a>
            @endif
          </div>
        </div>

        <!-- Title & Category Detail Badge -->
        <div class="mb-4">
          <span class="badge rounded-pill px-3 py-1.5 mb-2 fw-medium text-dark bg-light border-0" style="font-size: 0.8rem; background-color: #f1f5f9 !important;">Prompt Detail</span>
          <h1 class="h2 fw-bold text-dark mb-2 title-custom" style="letter-spacing: -0.5px;">{{ $response->title }}</h1>
          @if ($response->description != '')
            <p class="text-secondary mb-0 leading-relaxed" style="font-size: 0.95rem; color: #64748b !important;">{{ $response->description }}</p>
          @endif
        </div>

        <!-- Main Prompt Card Box -->
        <div class="card border-0 prompt-card-box-custom p-4 shadow-sm mb-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm border" style="width: 44px; height: 44px; flex-shrink: 0;">
                <i class="bi bi-file-earmark-text-fill text-dark fs-5"></i>
              </div>
              <div>
                <strong class="text-dark d-block fw-bold fs-5 title-custom" style="letter-spacing: 0.2px; line-height: 1.2;">PROMPT</strong>
                <small class="text-muted" style="font-size: 0.8rem;">Published on: {{ Helper::formatDate($response->date ?: $response->created_at) }}</small>
              </div>
            </div>
            <button type="button" class="btn btn-white border rounded-pill px-4 py-2 bg-white text-dark shadow-sm d-inline-flex align-items-center gap-2 btn-share-prompt" data-url="{{ url()->current() }}" data-title="{{ e($response->title) }}">
              <span class="fw-semibold">Share</span>
              <i class="bi bi-share text-dark fs-6"></i>
            </button>
          </div>

          <!-- Prompt Text Content -->
          @if ($canViewPrompt)
            <div class="p-4 prompt-inner-content-box mb-4">
              <p class="mb-0 text-secondary font-monospace title-custom" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;" id="promptText">{{ $response->prompt ?: $response->title }}</p>
            </div>
            
            <!-- Bottom Action Buttons Row -->
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
              <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-dark rounded-pill ps-4 pe-2 py-2 fw-bold d-inline-flex align-items-center gap-2 btn-copy-prompt shadow-sm" data-id="{{ $response->id }}" style="height: 44px;">
                  <span class="fs-6 btn-copy-text">Copy</span>
                  <span class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center ms-1" style="width: 30px; height: 30px;">
                    <i class="bi bi-copy" style="font-size: 13px;"></i>
                  </span>
                </button>

                @auth
                  <div class="small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-lightning-charge text-warning"></i>
                    <span>Daily copies remaining: <strong class="text-dark remaining-copies-count">{{ auth()->user()->remainingDailyPromptCopies() }}</strong> / {{ auth()->user()->totalDailyPromptLimit() }}</span>
                  </div>
                @endauth
              </div>
            </div>
          @else
            <div class="p-4 prompt-inner-content-box position-relative mb-4 text-center overflow-hidden" style="min-height: 140px;">
              <p class="mb-0 text-secondary font-monospace title-custom" style="filter: blur(6px); user-select: none;">
                Ultra realistic commercial product photography of {{ $response->title }} standing on a clean reflective surface...
              </p>
              <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white p-3 rounded-4">
                <i class="bi bi-lock-fill fs-3 mb-1 text-warning"></i>
                <h6 class="fw-bold mb-1">Premium Prompt Locked</h6>
                <p class="small text-white-50 mb-2">Subscribe to unlock and copy premium prompts.</p>
                <!-- <a href="{{ url('pricing') }}" class="btn btn-warning btn-sm px-4 py-2 text-dark fw-bold rounded-pill">
                  <i class="bi bi-star-fill me-1"></i> Upgrade to Unlock
                </a> -->
              </div>
            </div>
            <a href="{{ url('pricing') }}" class="btn btn-warning w-100 py-3 fw-bold rounded-pill text-dark shadow-sm">
              <i class="bi bi-lock-fill me-2"></i> Unlock Premium Prompt
            </a>
          @endif
        </div>

        <!-- Model OR Tool Section -->
        <div class="card border-0 prompt-card-box-custom p-4 shadow-sm mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-gear text-muted fs-5"></i>
            <strong class="text-dark small text-uppercase title-custom" style="letter-spacing: 0.5px;">Model OR Tool</strong>
          </div>
          <div>
            <div class="btn btn-outline-pill-action rounded-pill ps-4 pe-2 py-2 d-inline-flex align-items-center gap-3 bg-white" style="border: 1.5px solid #d1d5db; height: 46px;">
              <span class="fw-bold text-dark fs-6 title-custom">{{ $response->ai_model ?: 'Gemini' }}</span>
              <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #f1f5f9;">
                @php
                  $modelLower = strtolower($response->ai_model ?: 'gemini');
                @endphp

                @if (str_contains($modelLower, 'gemini'))
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 24C12 17.3726 6.62742 12 0 12C6.62742 12 12 6.62742 12 0C12 6.62742 17.3726 12 24 12C17.3726 12 12 17.3726 12 24Z" fill="url(#gemini-sparkle-grad-1)"/>
                    <defs>
                      <linearGradient id="gemini-sparkle-grad-1" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#4285f4"/>
                        <stop offset="30%" stop-color="#9b72cb"/>
                        <stop offset="70%" stop-color="#d96570"/>
                        <stop offset="100%" stop-color="#f49c46"/>
                      </linearGradient>
                    </defs>
                  </svg>
                @elseif (str_contains($modelLower, 'chatgpt') || str_contains($modelLower, 'gpt'))
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" class="text-dark" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.259 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7466-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1686a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4944zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5355-3.0137l.142.0852 4.783 2.7582a.7948.7948 0 0 0 .7854 0l5.833-3.3697v2.3324a.0804.0804 0 0 1-.0332.0615l-4.8351 2.7914a4.4992 4.4992 0 0 1-6.1402-1.6453zm-1.2225-10.456a4.4755 4.4755 0 0 1 2.3458-1.976l.0047.1611v5.5163a.7854.7854 0 0 0 .3927.6813l5.833 3.3697-2.02 1.1686a.0758.0758 0 0 1-.0711 0l-4.8304-2.7915a4.4944 4.4944 0 0 1-1.6547-6.1295zm16.597 3.0231l-5.833-3.3697 2.02-1.1686a.0758.0758 0 0 1 .0711 0l4.8304 2.7915a4.4944 4.4944 0 0 1-.6681 8.1056l-.142-.0852-4.783-2.7582a.7948.7948 0 0 0-.7854 0zm2.0105-3.0231a4.4755 4.4755 0 0 1-2.3458 1.976l-.0047-.1611V9.6644a.7854.7854 0 0 0-.3927-.6813L12.4411 5.6134l2.02-1.1686a.0758.0758 0 0 1 .0711 0l4.8304 2.7915a4.4944 4.4944 0 0 1 1.6263 6.1389zM8.3065 12.863l-2.02-1.1638a.0758.0758 0 0 1-.038-.052V6.0646a4.504 4.504 0 0 1 7.3757-3.4537l-.1419.0804-4.7783 2.7582a.7948.7948 0 0 0-.3927.6813v6.7322zm1.1419-2.0706l2.5516-1.4736 2.5516 1.4736v2.9472l-2.5516 1.4736-2.5516-1.4736z"/>
                  </svg>
                @elseif (str_contains($modelLower, 'midjourney'))
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" class="text-dark" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" opacity="0.6"/>
                    <path d="M2 17L12 22L22 17L12 12L2 17Z"/>
                    <path d="M2 12L12 17L22 12L12 7L2 12Z" opacity="0.8"/>
                  </svg>
                @else
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 24C12 17.3726 6.62742 12 0 12C6.62742 12 12 6.62742 12 0C12 6.62742 17.3726 12 24 12C17.3726 12 12 17.3726 12 24Z" fill="url(#gemini-sparkle-grad-2)"/>
                    <defs>
                      <linearGradient id="gemini-sparkle-grad-2" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#4285f4"/>
                        <stop offset="30%" stop-color="#9b72cb"/>
                        <stop offset="70%" stop-color="#d96570"/>
                        <stop offset="100%" stop-color="#f49c46"/>
                      </linearGradient>
                    </defs>
                  </svg>
                @endif
              </span>
            </div>
          </div>
        </div>

        <!-- Tags Section -->
        <div class="card border-0 bg-card-custom rounded-4 p-4 shadow-sm mb-4 border">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-tag text-muted fs-5"></i>
            <strong class="text-dark small text-uppercase title-custom" style="letter-spacing: 0.5px;">Tags</strong>
          </div>
          <div class="d-flex flex-wrap gap-2">
            @php
              $tags = explode(',', $response->tags);
            @endphp
            @foreach ($tags as $index => $tag)
              @php $trimmedTag = trim($tag); @endphp
              @if ($trimmedTag != '')
                <a href="{{ url('tags', str_replace(' ', '_', $trimmedTag)) }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 text-decoration-none">
                  {{ $trimmedTag }}
                </a>
              @endif
            @endforeach
          </div>
        </div>

        <!-- Comments Section -->
        @if ($response->comments->count() != 0 || (auth()->check() && $settings->comments))
          <div class="card border-0 bg-card-custom rounded-4 p-4 shadow-sm mb-4 border">
            <h5 class="fw-bold text-dark title-custom mb-3">{{ __('misc.comments') }} (<span id="totalComments">{{ number_format($response->comments->count()) }}</span>)</h5>
            
            @if (auth()->check() && $response->status == 'active' && $settings->comments)
              <div class="d-flex gap-2 mb-4">
                <img alt="Avatar" src="{{ Storage::url(config('path.avatar').auth()->user()->avatar) }}" class="rounded-circle" width="40" height="40">
                <div class="flex-grow-1">
                  <form action="{{ url('comment/store') }}" method="post" id="commentsForm">
                    @csrf
                    <input type="hidden" name="image_id" value="{{ $response->id }}">
                    <textarea name="comment" rows="2" required minlength="2" id="comments" class="form-control rounded-3 mb-2" placeholder="Add a comment..."></textarea>
                    <button type="submit" class="btn btn-custom btn-sm rounded-pill px-4" id="commentSend">{{ __('auth.send') }}</button>
                  </form>
                </div>
              </div>
            @endif

            <div class="gridComments" id="gridComments">
              @include('includes.comments')
            </div>
          </div>
        @endif

      </div><!-- /RIGHT COLUMN -->

    </div><!-- /ROW -->

    <!-- MORE PROMPTS SECTION -->
    <div class="pt-5 border-top mt-4" id="morePromptsSection">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
          <h3 class="fw-bold text-dark title-custom m-0">Explore Similar Prompts</h3>
        </div>
        @if ($response->category)
          <a href="{{ url('category', $response->category->slug) }}" class="text-decoration-none link-dark title-custom fw-bold small">
            View All <i class="bi bi-arrow-right ms-1"></i>
          </a>
        @endif
      </div>

      @if (isset($images) && $images->count() > 0)
        @include('includes.images', ['images' => $images])
      @endif
    </div>

  </div>
</section>

@endsection
