@foreach ($data as $collection)

@php
$image = $collection->collectionImages->take(3);
$totalImages = $collection->collectionImages->count();
@endphp

<div class="col-md-4 mb-3 text-truncate">
  <a href="{{ url(($collection->creator->username ?? 'user').'/collection', $collection->id) }}"
    class="position-relative text-decoration-none">
    <div class="wrap-collection">
      <div class="grid-collection">
        <div class="collection-1">
          @if (isset($image[0]) && $image[0]->images)
            @php
              $imgObj0 = $image[0]->images;
              $stock0 = $image[0]->stockCollection->first();
              $src0 = $imgObj0->preview ? Storage::url(config('path.preview').$imgObj0->preview) : ($stock0 ? Storage::url(config('path.small').$stock0->name) : '');
            @endphp
            @if ($src0)
              <img role="presentation" class="img-collection" src="{{ $src0 }}">
            @endif
          @endif
        </div><!-- collection-1 -->

        <div class="collection-right">
          <div class="collection-2">
            @if (isset($image[1]) && $image[1]->images)
              @php
                $imgObj1 = $image[1]->images;
                $stock1 = $image[1]->stockCollection->first();
                $src1 = $imgObj1->preview ? Storage::url(config('path.preview').$imgObj1->preview) : ($stock1 ? Storage::url(config('path.small').$stock1->name) : '');
              @endphp
              @if ($src1)
                <img role="presentation" class="img-collection" src="{{ $src1 }}">
              @endif
            @endif
          </div>

          <div class="collection-2">
            @if (isset($image[2]) && $image[2]->images)
              @php
                $imgObj2 = $image[2]->images;
                $stock2 = $image[2]->stockCollection->first();
                $src2 = $imgObj2->preview ? Storage::url(config('path.preview').$imgObj2->preview) : ($stock2 ? Storage::url(config('path.small').$stock2->name) : '');
              @endphp
              @if ($src2)
                <img role="presentation" class="img-collection" src="{{ $src2 }}">
              @endif
            @endif
          </div>
        </div>

      </div><!-- grid-collection -->
    </div><!-- wrap-collection -->
    <span class="collection-title text-dark mb-1">
      @if ($collection->type == 'private') <i class="fa fa-lock me-1 padlock" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{__('misc.private')}}"></i> @endif {{$collection->title}}
    </span>

    <small class="d-block w-100 text-muted">
      {{ $totalImages }} {{ trans_choice('misc.images_plural', $totalImages) }} - {{ __('misc.by') }}
      <strong>{{ $collection->creator->username ?? '' }}</strong>
    </small>
  </a>
</div><!-- col-3-->
@endforeach

@if ($data->count() != 0)
<div class="container-paginator" id="linkPagination">
  {{ $data->links() }}
</div>
@endif