@extends('layouts.app')

@section('css')
<link href="{{ asset('public/js/tagin/tagin.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<section class="section section-sm">

<div class="container pt-5">
	<div class="row">

		@if (session('success_message'))
			<div class="col-12">
			<div class="alert alert-success alert-dismissible fade show" role="alert">
							<i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

							<a class="text-white text-decoration-underline ms-2" href="{{ url('prompt', $data->slug) }}">{{ __('misc.view_photo') }} <i class="bi-arrow-right ms-1"></i></a>

								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
									<i class="bi bi-x-lg"></i>
								</button>
								</div>
							</div>
						@endif

				@include('errors.errors-forms')

    <div class="col-md-7 mb-3">
      <!-- wrapper upload -->
  		<div class="filer-input-dragDrop position-relative border d-none d-lg-block" id="draggable">

  			<!-- previewPhoto -->
  			<div class="previewPhoto d-block" style="background-image: url('{{ Storage::url(config('path.preview').$data->preview) }}')"></div>
				<!-- previewPhoto -->

  			</div><!-- ./ wrapper upload -->

        <ul class="list-inline">
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ trans('conditions.terms') }}</li>
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ trans('conditions.sex_content') }}</li>
  			</ul>

    </div>

	<!-- col-md-12 -->
	<div class="col-md-5">

			<div class="card border-0">

				<div class="card-body p-0">

			<!-- form start -->
      <form method="POST" action="{{ url('update/photo') }}" enctype="multipart/form-data" id="formUpload" files="true">

      	<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="id" value="{{ $data->id }}">

          <div class="mb-3">
           <input type="text" required class="form-control" id="title" value="{{ $data->title }}" name="title" placeholder="{{ trans('admin.title') }}">
         </div>

         <div class="mb-3">
           <input type="text" class="form-control" id="slug" value="{{ $data->slug }}" name="slug" placeholder="Slug">
           <small class="text-muted d-block mt-1">* Custom URL slug. If left empty, title will be used as slug.</small>
         </div>

         <div class="mb-3">
           <textarea required class="form-control" id="prompt" name="prompt" rows="4" placeholder="Enter complete photography prompt...">{{ $data->prompt }}</textarea>
           <small class="text-muted d-block mt-1">* Enter the full AI prompt used for this product photo.</small>
         </div>

         <div class="form-floating mb-3">
           <select name="ai_model" required class="form-select" id="ai_model">
             <option value="">Select AI Model</option>
             @foreach (App\Models\Images::getAiModels() as $model)
               <option @if ($data->ai_model == $model) selected="selected" @endif value="{{ $model }}">{{ $model }}</option>
             @endforeach
           </select>
           <label for="ai_model">AI Model</label>
         </div>

         <div class="mb-3">
          <input type="text" required class="form-control tagin" id="tagInput" value="{{ $data->tags }}" name="tags" placeholder="{{ trans('misc.tags') }}">
          <small class="d-block">* {{ trans('misc.add_tags_guide') }} ({{trans('misc.maximum_tags', ['limit' => $settings->tags_limit ]) }})</small>
        </div>

        <!-- Photoshoot / Batch Selection -->
        <div class="form-floating mb-3">
          <select name="photoshoot_id" class="form-select" id="photoshootSelect">
            <option value="">No Photoshoot (Standalone Prompt)</option>
            <option value="new">+ Create New Photoshoot...</option>
            @foreach ($photoshoots as $ps)
              <option value="{{ $ps->id }}" @selected($data->photoshoot_id == $ps->id)>
                {{ $ps->title }} ({{ $ps->prompts_count }} prompts)
              </option>
            @endforeach
          </select>
          <label for="photoshootSelect"><i class="bi bi-collection-play me-1"></i> Photoshoot / Batch</label>
        </div>

        <div class="form-floating mb-3 display-none" id="newPhotoshootBox">
          <input type="text" class="form-control" name="photoshoot_title" id="photoshoot_title" placeholder="e.g. Luxury Skincare Product Set">
          <label for="photoshoot_title">New Photoshoot Name</label>
        </div>

        <div class="form-floating mb-3">
        <select name="categories_id" class="form-select" id="category">
          @foreach ($categories as $category)
            <option @if ($data->categories_id == $category->id) selected="selected" @endif value="{{$category->id}}">
							{{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
						</option>
            @endforeach
        </select>
        <label for="category">{{ trans('misc.category') }}</label>
      </div>

      <div class="form-floating mb-3">
        <select name="subcategories_id" class="form-select">
          <option id="subcategory" value="">{{ __('misc.select') }}</option>
          @foreach ($subcategories as $subcategory)
            <option class="valuesSub" @if ($data->subcategories_id == $subcategory->id) selected="selected" @endif value="{{$subcategory->id}}">
							{{ Lang::has('subcategories.' . $subcategory->slug) ? __('subcategories.' . $subcategory->slug) : $subcategory->name }}
						</option>
            @endforeach
        </select>
        <label for="category">{{ trans('misc.subcategory') }}</label>
      </div>

             <div class="form-floating mb-3">
               <select name="item_for_sale" class="form-select" id="itemForSale">
                 <option @if ($data->item_for_sale == 'free') selected="selected" @endif value="free">Free Prompt (Available to Free Users & Subscribers)</option>
                 <option @if ($data->item_for_sale == 'sale') selected="selected" @endif value="sale">Premium Prompt (Requires Pro Subscription Plan)</option>
               </select>
               <label for="itemForSale">Prompt Tier / Access Type</label>
             </div>
             <input type="hidden" name="price" value="{{ $data->price }}">

									<div class="form-floating mb-3">
			             <input type="text" class="form-control" id="input-camera" value="{{ $data->camera }}" name="camera" placeholder="{{ trans('misc.camera') }}">
			             <label for="input-camera">{{ trans('misc.camera') }}</label>
			           </div>

								 <div class="form-floating mb-3">
									<input type="text" class="form-control" id="input-exif_data" value="{{ $data->exif }}" name="exif" placeholder="{{ trans('misc.exif_data') }}">
									<label for="input-exif_data">{{ trans('misc.exif_data') }}</label>
								</div>

                  <!-- Start Form Group -->
                <div class="form-floating mb-3 options_free">
                  <select name="how_use_image" class="form-select" id="how_use_image">
                    <option @selected($data->how_use_image == 'free') value="free">{{ trans('misc.use_free') }}</option>
                    <option @selected($data->how_use_image == 'free_personal') value="free_personal">{{ trans('misc.use_free_personal') }}</option>
                     <option @selected($data->how_use_image == 'editorial_only') value="editorial_only">{{ trans('misc.use_editorial_only') }}</option>
                      <option @selected($data->how_use_image == 'web_only') value="web_only">{{ trans('misc.use_web_only') }}</option>
                  </select>
                  <label for="how_use_image">{{ trans('misc.how_use_image') }}</label>
                </div>

              <div class="form-check form-switch form-switch-md mb-3 options_free @if ($settings->free_photo_upload == 'off') display-none @endif">
                <input class="form-check-input" @if ($data->attribution_required == 'yes') checked @endif name="attribution_required" type="checkbox" value="yes" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault">{{ trans('misc.attribution_required') }}</label>
              </div>

              <div class="form-floating mb-3">
               <textarea class="form-control" placeholder="{{ trans('misc.description') }}" name="description" id="input-description" style="height: 100px">{{ $data->description }}</textarea>
               <label for="input-description">{{ trans('admin.description') }} ({{ trans('misc.optional') }})</label>
             </div>

              <!-- SEO Fields Card Section -->
              <div class="card border rounded-4 p-3 mb-3 bg-light shadow-sm">
                <h6 class="fw-bold text-dark mb-2">
                  <i class="bi bi-search me-1 text-primary"></i> Search Engine Optimization (SEO) <span class="badge bg-secondary font-weight-normal ms-1" style="font-size:0.75rem;">Optional</span>
                </h6>
                <p class="small text-muted mb-3">Custom SEO metadata for search engine snippets. Leave blank to use automatic fallbacks.</p>
                
                <div class="mb-3">
                  <label for="meta_title" class="form-label text-dark small fw-medium">Meta Title</label>
                  <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $data->meta_title }}" placeholder="SEO Title">
                </div>
                
                <div class="mb-3">
                  <label for="meta_description" class="form-label text-dark small fw-medium">Meta Description</label>
                  <textarea class="form-control" id="meta_description" name="meta_description" rows="2" placeholder="Brief summary for search result snippets...">{{ $data->meta_description }}</textarea>
                </div>
                
                <div class="mb-0">
                  <label for="meta_keywords" class="form-label text-dark small fw-medium">Meta Keywords</label>
                  <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ $data->meta_keywords }}" placeholder="keyword1, keyword2, keyword3">
                </div>
              </div>

              <!-- Example Output Images Section -->
              <div class="card border rounded-4 p-3 mb-3 bg-light shadow-sm">
                <h6 class="fw-bold text-dark mb-2">
                  <i class="bi bi-images me-1 text-primary"></i> Example Output Images (Max 5)
                </h6>

                @if ($data->examples && $data->examples->count() > 0)
                  <p class="small text-muted mb-2">Check to remove existing example images:</p>
                  <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach ($data->examples as $ex)
                      <div class="position-relative border rounded p-1 bg-white text-center" style="width: 80px;">
                        <img src="{{ Storage::url(config('path.examples').$ex->file) }}" class="img-fluid rounded mb-1" style="height: 60px; object-fit: cover; width: 100%;">
                        <div class="form-check d-flex align-items-center justify-content-center p-0 m-0">
                          <input class="form-check-input ms-0 me-1" type="checkbox" name="delete_examples[]" value="{{ $ex->id }}" id="delEx{{ $ex->id }}">
                          <label class="form-check-label text-danger fw-semibold" style="font-size:0.75rem;" for="delEx{{ $ex->id }}">Delete</label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif

                @if (!$data->examples || $data->examples->count() < 5)
                  <label class="form-label small text-muted mb-1" for="editExamplePhotosInput">Add new example output images (Remaining: {{ 5 - ($data->examples ? $data->examples->count() : 0) }}):</label>
                  <input type="file" accept="image/*" name="example_photos[]" id="editExamplePhotosInput" multiple class="custom-file form-control" style="position: static !important; opacity: 1 !important; visibility: visible !important;">
                  <div id="editExamplePreviewsContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                @else
                  <small class="text-muted d-block">Maximum limit of 5 example images reached. Check delete above to make space for new ones.</small>
                @endif
              </div>
                    <!-- Alert -->
            <div class="alert alert-danger display-none" id="dangerAlert">
							<ul class="list-unstyled mb-0" id="showErrors"></ul>
						</div><!-- Alert -->

                  <div class="box-footer text-center">
                    <button type="submit" class="btn btn-lg btn-custom w-100">
                      {{ trans('misc.save_changes') }}
                    </button>
                  </div><!-- /.box-footer -->
                </form>
         	</div>
         </div>

		</div>
		<!-- col-md-12-->

	</div><!-- row -->
</div><!-- container -->
</section>
@endsection

@section('javascript')
	<script src="{{ asset('public/js/tagin/tagin.min.js') }}" type="text/javascript"></script>

	<script type="text/javascript">

	//======== Start Tagin
  const tagin = new Tagin(document.querySelector('.tagin'), {
		enter: true,
    placeholder: '{{ trans("misc.add_tag") }}',
	});

	var inputDefault = $(".tagin").siblings('.tagin-wrapper');
	var maxLenDefault = {{$settings->tags_limit}};

	if (inputDefault.children('span.tagin-tag').length >= maxLenDefault) {
		inputDefault.children('input.tagin-input').addClass('d-none');
	}

  $(".tagin").on('change', function() {
    var input = $(this).siblings('.tagin-wrapper');
    var maxLen = {{$settings->tags_limit}};

if (input.children('span.tagin-tag').length >= maxLen) {
        input.children('input.tagin-input').addClass('d-none');
    }
    else {
        input.children('input.tagin-input').removeClass('d-none');
    }
  });
	//======== End Tagin

  function replaceString(string) {
  	return string.replace(/[\-\_\.\+]/ig,' ')
  }

  $('#photoshootSelect').on('change', function() {
    if ($(this).val() == 'new') {
      $('#newPhotoshootBox').slideDown();
    } else {
      $('#newPhotoshootBox').slideUp();
      $('#photoshoot_title').val('');
    }
  });

  $('#itemForSale').on('change', function() {
    if($(this).val() == 'sale') {
			$('#priceBox').slideDown();
      $('.options_free').slideUp();

		} else {
				$('#priceBox').slideUp();
        $('.options_free').slideDown();
		}
});

$('#typeImage').on('change', function(){
  if($(this).val() == 'vector') {
    $('#vector').slideDown();
  } else {
      $('#vector').slideUp('fast');
      $('#uploadFile').val('');
      $('#fileDocument').html('');
  }
});

$(".onlyNumber").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

$(document).on('click','#deleteFile',function () {
    $('#uploadFile').val('');
    $('#fileDocument').html('');
});

//================== START FILE - FILE READER
$("#uploadFile").change(function() {

	$('#fileDocument').html('');

	var loaded = false;
	if(window.File && window.FileReader && window.FileList && window.Blob){
		if($(this).val()){ //check empty input filed
			if($(this)[0].files.length === 0){return}

			var oFile = $(this)[0].files[0];
			var fsize = $(this)[0].files[0].size; //get file size
			var ftype = $(this)[0].files[0].type; // get file type

			var allowed_file_size = {{$settings->file_size_allowed_vector * 1024}};

			if(fsize>allowed_file_size){
				$('.popout').addClass('popout-error').html("{{trans('misc.max_size_vector').': '.App\Helper::formatBytes($settings->file_size_allowed_vector * 1024)}}").fadeIn(500).delay(4000).fadeOut();
        $(this).val('');
				return false;
			}

			$('#fileDocument').html('<i class="fa fa-paperclip"></i> <strong class="text-muted"><em>' + oFile.name + '</em></strong> - <a href="javascript:void(0);" id="deleteFile" class="text-danger">{{trans('misc.delete')}}</a>');

		}
	} else{
		alert('Can\'t upload! Your browser does not support File API! Try again with modern browsers like Chrome or Firefox.');
		return false;
	}
});
//================== END FILE - FILE READER ==============>

$('#price').on('keyup', function() {

  var valueOriginal = $('.onlyNumber').val();
  var value = parseFloat($('.onlyNumber').val());
  var element = $(this).val();

  if (element != '') {

    if (valueOriginal >= {{$settings->min_sale_amount}} && valueOriginal <= {{$settings->max_sale_amount}}) {
      var amountSmall = value;
    } else {
      amountSmall = 0;
    }
      var amountMedium = (amountSmall * 2);
      var amountLarge = (amountSmall * 3);
      var amountVector = (amountSmall * 4);


      $('#s-price').html(amountSmall);
      $('#m-price').html(amountMedium);
      $('#l-price').html(amountLarge);
      $('#v-price').html(amountVector);

  }

  if (valueOriginal == '') {
    $('#s-price').html('0');
    $('#m-price').html('0');
    $('#l-price').html('0');
    $('#v-price').html('0');
  }
});

document.getElementById('editExamplePhotosInput')?.addEventListener('change', function(e) {
  const container = document.getElementById('editExamplePreviewsContainer');
  if (container) {
    container.innerHTML = '';
    const files = Array.from(e.target.files).slice(0, 5);
    files.forEach(file => {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(evt) {
          const img = document.createElement('img');
          img.src = evt.target.result;
          img.className = 'img-thumbnail rounded shadow-sm';
          img.style.width = '65px';
          img.style.height = '65px';
          img.style.objectFit = 'cover';
          container.appendChild(img);
        };
        reader.readAsDataURL(file);
      }
    });
  }
});
</script>


@endsection
