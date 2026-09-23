@if (isset($models) && count($models) > 0)
  @foreach ($models as $model)
    @php
      $modelName = $model['name'];
      $modelSlug = $model['slug'];
      $promptCount = $model['count'];
      $firstLetter = mb_substr(trim($modelName), 0, 1);
      $modelLower = strtolower($modelName);
    @endphp

    <div class="col-sm-6 col-md-4 col-lg-3 mb-4 model-item-card">
      <a href="{{ url('ai-model', $modelSlug) }}" 
         class="d-block card border text-decoration-none p-4 rounded-4 shadow-sm transition-all bg-card-custom h-100">
        
        <div class="card-body p-0 d-flex flex-column justify-content-between h-100">
          
          {{-- Top Row: Avatar/Brand Icon Box & Counter Badge --}}
          <div class="d-flex align-items-center justify-content-between mb-3">
            
            {{-- Brand Avatar Badge --}}
            <div class="d-flex align-items-center justify-content-center rounded-3 fw-bold tag-avatar-badge" 
                 style="width: 44px; height: 44px; background-color: rgba(0, 214, 144, 0.12) !important; color: #00d690 !important; font-size: 1.15rem; flex-shrink: 0;">
              @if (str_contains($modelLower, 'gemini'))
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 24C12 17.3726 6.62742 12 0 12C6.62742 12 12 6.62742 12 0C12 6.62742 17.3726 12 24 12C17.3726 12 12 17.3726 12 24Z" fill="url(#gemini-sparkle-card-{{ $modelSlug }})"/>
                  <defs>
                    <linearGradient id="gemini-sparkle-card-{{ $modelSlug }}" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                      <stop offset="0%" stop-color="#4285f4"/>
                      <stop offset="30%" stop-color="#9b72cb"/>
                      <stop offset="70%" stop-color="#d96570"/>
                      <stop offset="100%" stop-color="#f49c46"/>
                    </linearGradient>
                  </defs>
                </svg>
              @elseif (str_contains($modelLower, 'chatgpt') || str_contains($modelLower, 'gpt'))
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color: #00d690;" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.259 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7466-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1686a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4944zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5355-3.0137l.142.0852 4.783 2.7582a.7948.7948 0 0 0 .7854 0l5.833-3.3697v2.3324a.0804.0804 0 0 1-.0332.0615l-4.8351 2.7914a4.4992 4.4992 0 0 1-6.1402-1.6453zm-1.2225-10.456a4.4755 4.4755 0 0 1 2.3458-1.976l.0047.1611v5.5163a.7854.7854 0 0 0 .3927.6813l5.833 3.3697-2.02 1.1686a.0758.0758 0 0 1-.0711 0l-4.8304-2.7915a4.4944 4.4944 0 0 1-1.6547-6.1295zm16.597 3.0231l-5.833-3.3697 2.02-1.1686a.0758.0758 0 0 1 .0711 0l4.8304 2.7915a4.4944 4.4944 0 0 1-.6681 8.1056l-.142-.0852-4.783-2.7582a.7948.7948 0 0 0-.7854 0zm2.0105-3.0231a4.4755 4.4755 0 0 1-2.3458 1.976l-.0047-.1611V9.6644a.7854.7854 0 0 0-.3927-.6813L12.4411 5.6134l2.02-1.1686a.0758.0758 0 0 1 .0711 0l4.8304 2.7915a4.4944 4.4944 0 0 1 1.6263 6.1389zM8.3065 12.863l-2.02-1.1638a.0758.0758 0 0 1-.038-.052V6.0646a4.504 4.504 0 0 1 7.3757-3.4537l-.1419.0804-4.7783 2.7582a.7948.7948 0 0 0-.3927.6813v6.7322zm1.1419-2.0706l2.5516-1.4736 2.5516 1.4736v2.9472l-2.5516 1.4736-2.5516-1.4736z"/>
                </svg>
              @elseif (str_contains($modelLower, 'midjourney'))
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color: #00d690;" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2L2 7L12 12L22 7L12 2Z" opacity="0.6"/>
                  <path d="M2 17L12 22L22 17L12 12L2 17Z"/>
                  <path d="M2 12L12 17L22 12L12 7L2 12Z" opacity="0.8"/>
                </svg>
              @else
                {{ strtoupper($firstLetter) }}
              @endif
            </div>

            {{-- Prompt Count Pill --}}
            <span class="badge border rounded-pill px-3 py-2 fw-normal badge-custom" 
                  style="font-size: 0.78rem;">
              {{ $promptCount }} {{ $promptCount == 1 ? (__('misc.prompt') ?? 'Prompt') : (__('misc.prompts') ?? 'Prompts') }}
            </span>

          </div>

          {{-- Middle Row: Model Title --}}
          <div class="my-2">
            <h5 class="fw-bold m-0 p-0 title-custom text-break" 
                style="font-size: 1.05rem; line-height: 1.35; width: 100% !important; text-align: left !important;">
              {{ $modelName }}
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
