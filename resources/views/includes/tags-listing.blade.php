@if (isset($tags) && count($tags) > 0)
  @foreach ($tags as $tag)
    @php
      $tagName = $tag['name'];
      $firstLetter = mb_substr(trim($tagName), 0, 1);
      $promptCount = $tag['count'];
      $tagSlug = $tag['slug'];
    @endphp

    <div class="col-sm-6 col-md-4 col-lg-3 mb-4 tag-item-card">
      <a href="{{ url('tags', $tagSlug) }}" 
         class="d-block card border text-decoration-none p-4 rounded-4 shadow-sm transition-all bg-card-custom h-100">
        
        <div class="card-body p-0 d-flex flex-column justify-content-between h-100">
          
          {{-- Top Row: Avatar Box & Counter Badge --}}
          <div class="d-flex align-items-center justify-content-between mb-3">
            
            {{-- Avatar Badge --}}
            <div class="d-flex align-items-center justify-content-center rounded-3 fw-bold tag-avatar-badge" 
                 style="width: 44px; height: 44px; background-color: rgba(0, 214, 144, 0.12) !important; color: #00d690 !important; font-size: 1.15rem; flex-shrink: 0;">
              {{ strtoupper($firstLetter) }}
            </div>

            {{-- Prompt Count Pill --}}
            <span class="badge border rounded-pill px-3 py-2 fw-normal badge-custom" 
                  style="font-size: 0.78rem;">
              {{ $promptCount }} {{ $promptCount == 1 ? (__('misc.prompt') ?? 'Prompt') : (__('misc.prompts') ?? 'Prompts') }}
            </span>

          </div>

          {{-- Middle Row: Tag Title --}}
          <div class="my-2">
            <h5 class="fw-bold m-0 p-0 title-custom text-break" 
                style="font-size: 1.05rem; line-height: 1.35; width: 100% !important; text-align: left !important;">
              {{ $tagName }}
            </h5>
          </div>

          {{-- Bottom Row: Action Link --}}
          <div class="pt-2">
            <span class="fw-semibold d-inline-flex align-items-center" 
                  style="color: #00d690 !important; font-size: 0.875rem;">
              {{ __('misc.view_prompts') ?? 'View Prompts' }}
              <svg class="ms-1" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
              </svg>
            </span>
          </div>

        </div>
      </a>
    </div><!-- col-3 -->
  @endforeach
@endif
