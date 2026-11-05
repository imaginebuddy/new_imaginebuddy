@php
  $hasAttachedPrompts = !empty($attachedPrompts) && $attachedPrompts->count() > 0;
@endphp

@if ($hasAttachedPrompts)
  <section class="cms-section" id="prompts-showcase">
    <div class="container">
      <div class="cms-section-header">
        @if (!empty($section->badge))
          <span class="cms-eyebrow-badge mb-2">{{ $section->badge }}</span>
        @else
          <span class="cms-eyebrow-badge mb-2">PROMPT RECIPES</span>
        @endif

        <h2 class="cms-section-title">
          {{ $section->heading ?: 'Connected Prompt Library' }}
        </h2>

        @if (!empty($section->subheading))
          <p class="cms-section-sub">{{ $section->subheading }}</p>
        @else
          <p class="cms-section-sub">Tested photoshoot recipes and visual prompts crafted specifically for {{ strtolower($page->title) }}.</p>
        @endif
      </div>

      {{-- Render Connected Library Prompts Attached in Tab 3 --}}
      @if (View::exists('guides.prompts-showcase'))
        @include('guides.prompts-showcase', ['prompts' => $attachedPrompts, 'hideShowcaseHeader' => true])
      @endif

    </div>
  </section>
@endif
