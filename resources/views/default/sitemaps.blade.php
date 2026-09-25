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
            <url>
            <loc>{{ url('category', [$subcategory->category->slug, $subcategory->slug]) }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
            </url>
        @endforeach

        <url>
            <loc>{{ url('photoshoots') }}</loc>
            <lastmod>{{$date}}</lastmod>
            <priority>0.8</priority>
        </url>

        @foreach (App\Models\Photoshoot::whereNotNull('slug')->where('slug', '!=', '')->orderBy('id', 'desc')->get() as $photoshoot)
        <url>
            <loc>{{ url('photoshoots', $photoshoot->slug) }}</loc>
            <lastmod>{{ $photoshoot->created_at ? Carbon\Carbon::parse($photoshoot->created_at)->format('Y-m-d') : $date }}</lastmod>
            <priority>0.8</priority>
        </url>
        @endforeach

        
   
	@foreach (Pages::all() as $page)
	<url>
         <loc>{{ url('page', $page->slug) }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>
 @endforeach
   
</urlset>