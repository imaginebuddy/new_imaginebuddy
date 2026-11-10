{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
@php
    $date = Carbon\Carbon::yesterday()->format('Y-m-d');
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
         <loc>{{ url('/') }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
      </url>


         @if (Plans::whereStatus('1')->count() != 0 && $settings->sell_option == 'on')
         <url>
            <loc>{{ url('pricing') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>
         @endif

         <url>
            <loc>{{ url('prompts') }}</loc>
            <lastmod>{{ (class_exists(App\Models\PromptCollection::class) && App\Models\PromptCollection::where('status', 'active')->max('updated_at')) ? Carbon\Carbon::parse(App\Models\PromptCollection::where('status', 'active')->max('updated_at'))->format('Y-m-d') : $date }}</lastmod>
            <priority>0.9</priority>
            <changefreq>daily</changefreq>
         </url>

         <url>
            <loc>{{ url('prompts/free') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         @if ($settings->sell_option == 'on')
         <url>
            <loc>{{ url('prompts/premium') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>
         @endif

         <url>
            <loc>{{ url('featured') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('popular') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('latest') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('contact') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('frequently-asked-questions') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('categories') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('ai-models') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
         </url>

         <url>
            <loc>{{ url('directory') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.9</priority>
         </url>

         @foreach (App\Models\Images::getAiModels() as $aiModel)
            <url>
            <loc>{{ url('ai-model', Str::slug($aiModel)) }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
            </url>
         @endforeach

         @foreach (Categories::where('mode', 'on')->get() as $category)
            <url>
            <loc>{{ url('category', $category->slug) }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
            </url>
        @endforeach

        @foreach (App\Models\Subcategories::with(['category:id,slug'])->where('mode', 'on')->get() as $subcategory)
            @if ($subcategory->category && $subcategory->category->slug)
            <url>
                <loc>{{ url('category', [$subcategory->category->slug, $subcategory->slug]) }}</loc>
                <lastmod>{{$date}}</lastmod>
                <priority>0.8</priority>
            </url>
            @endif
        @endforeach

        <url>
            <loc>{{ url('photoshoots') }}</loc>
            <lastmod>{{ App\Models\Photoshoot::max('created_at') ? Carbon\Carbon::parse(App\Models\Photoshoot::max('created_at'))->format('Y-m-d') : $date }}</lastmod>
            <priority>0.8</priority>
        </url>

        @foreach (App\Models\Photoshoot::whereNotNull('slug')->where('slug', '!=', '')->orderBy('id', 'desc')->get() as $photoshoot)
        <url>
            <loc>{{ url('photoshoots/' . $photoshoot->slug) }}</loc>
            <lastmod>{{ $photoshoot->created_at ? Carbon\Carbon::parse($photoshoot->created_at)->format('Y-m-d') : $date }}</lastmod>
            <priority>0.8</priority>
        </url>
        @endforeach

        {{-- Prompt Collections Directory & Collections (/prompts/{slug}) --}}
        @if (class_exists(App\Models\PromptCollection::class))
            @foreach (App\Models\PromptCollection::where('status', 'active')->whereNotNull('slug')->where('slug', '!=', '')->orderBy('updated_at', 'desc')->get() as $promptCollection)
            <url>
                <loc>{{ url('prompts/' . $promptCollection->slug) }}</loc>
                <lastmod>{{ $promptCollection->updated_at ? Carbon\Carbon::parse($promptCollection->updated_at)->format('Y-m-d') : ($promptCollection->created_at ? Carbon\Carbon::parse($promptCollection->created_at)->format('Y-m-d') : $date) }}</lastmod>
                <priority>0.8</priority>
                <changefreq>weekly</changefreq>
            </url>
            @endforeach
        @endif

        @if (class_exists(App\Models\HireSetting::class) && (App\Models\HireSetting::getSettings()->status ?? 'on') == 'on')
        <url>
            <loc>{{ url('hire') }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <priority>0.8</priority>
            <changefreq>monthly</changefreq>
        </url>
        @endif

        
   
	@foreach (Pages::all() as $page)
	<url>
         <loc>{{ url('page', $page->slug) }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>
 @endforeach

        <url>
            <loc>{{ url('blog') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
        </url>

        @foreach (App\Models\BlogCategory::active()->get() as $bCategory)
        <url>
            <loc>{{ url('blog/category', $bCategory->slug) }}</loc>
            <lastmod>{{ $bCategory->updated_at ? Carbon\Carbon::parse($bCategory->updated_at)->format('Y-m-d') : $date }}</lastmod>
            <priority>0.7</priority>
        </url>
        @endforeach

        @foreach (App\Models\BlogPost::published()->get() as $bPost)
        <url>
            <loc>{{ url('blog', $bPost->slug) }}</loc>
            <lastmod>{{ $bPost->updated_at ? Carbon\Carbon::parse($bPost->updated_at)->format('Y-m-d') : $date }}</lastmod>
            <priority>0.8</priority>
        </url>
        @endforeach

        {{-- 6 Vertical Silo CMS Hubs --}}
        @php
            $verticalHubs = ['use-case', 'industry', 'product', 'garment', 'marketplace', 'platform-ai-ads'];
            $managedHubs = App\Models\SeoLandingPage::published()
                ->where('page_type', 'hub')
                ->pluck('route_prefix')
                ->toArray();
        @endphp
        @foreach ($verticalHubs as $hub)
            @if (!in_array($hub, $managedHubs))
            <url>
                <loc>{{ url($hub) }}</loc>
                <lastmod>{{ $date }}</lastmod>
                <priority>0.9</priority>
                <changefreq>daily</changefreq>
            </url>
            @endif
        @endforeach

        {{-- Centralized Page Manager & SEO Landing Pages --}}
        @foreach (App\Models\SeoLandingPage::published()
            ->where(function ($q) {
                $q->whereNull('robots')
                  ->orWhere('robots', 'not like', '%noindex%');
            })
            ->orderBy('updated_at', 'desc')
            ->get() as $landingPage)
        <url>
            <loc>{{ $landingPage->public_url }}</loc>
            <lastmod>{{ $landingPage->updated_at ? Carbon\Carbon::parse($landingPage->updated_at)->format('Y-m-d') : $date }}</lastmod>
            <priority>{{ $landingPage->page_type === 'hub' ? '0.9' : '0.8' }}</priority>
            <changefreq>{{ $landingPage->page_type === 'hub' ? 'daily' : 'weekly' }}</changefreq>
        </url>
        @endforeach
   
</urlset>