{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($images as $response)
  <url>
    <loc>{{ url('prompt', $response->slug) }}</loc>
@if ($response->preview)
    <image:image>
      <image:loc>{{ Storage::url(config('path.preview') . $response->preview) }}</image:loc>
    </image:image>
@endif
    <lastmod>{{ $response->date ? Carbon\Carbon::parse($response->date)->format('Y-m-d') : Carbon\Carbon::now()->format('Y-m-d') }}</lastmod>
    <priority>0.8</priority>
  </url>
@endforeach
</urlset>
