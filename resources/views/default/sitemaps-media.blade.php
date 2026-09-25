{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
@php
    $date = Carbon\Carbon::yesterday()->format('Y-m-d');
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

@foreach (Images::select(['id', 'title', 'slug', 'thumbnail', 'preview'])->where('status','active')->get() as $response)
  <url>
    <loc>{{ url('prompt', $response->slug) }}</loc>
    <image:image>
      <image:loc>{{ Storage::url(config('path.preview') . $response->preview) }}</image:loc>
    </image:image>
    <lastmod>{{$date}}</lastmod>
    <priority>0.8</priority>
  </url>
  @endforeach
</urlset>