@extends('layouts.app')

@section('title'){{ __('users.upload').' - ' }}@endsection

@section('css')
<link href="{{ asset('public/js/tagin/tagin.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<section class="section section-sm">

<div class="container pt-5">
	<div class="row">

@if (auth()->user()->status == 'active')

@if ($settings->limit_upload_user == 0
    || auth()->user()->dailyUploads() < $settings->limit_upload_user
    || auth()->user()->isSuperAdmin()
    )

    <div class="col-md-7 mb-3">
      <!-- form start -->
      <form method="POST" action="{{ url('upload') }}" enctype="multipart/form-data" id="formUpload" files="true">

      	<input type="hidden" name="_token" value="{{ csrf_token() }}">

      <!-- wrapper upload -->
  		<div class="filer-input-dragDrop position-relative rounded" id="draggable">

  			<input type="file" accept="image/*" name="photo" id="filePhoto" class="visible">

  			<!-- previewPhoto -->
  			<div class="previewPhoto"></div><!-- previewPhoto -->

        <span class="text-dark btn-remove-photo display-none c-pointer" id="removePhoto">
          <i class="bi bi-x-lg text-white"></i>
        </span>

  			<div class="filer-input-inner">
  				<div class="filer-input-icon">
  					<i class="bi bi-image"></i>
  					</div>
  					<div class="filer-input-text">
  						<h3 class="mb-2 fw-light">{{ __('misc.click_select_image') }}</h3>
  						<h3 class="fw-light">{{ __('misc.max_size') }}: {{  $settings->min_width_height_image.' - '.Helper::formatBytes($settings->file_size_allowed * 1024)}} </h3>
  					</div>
  				</div>
  			</div><!-- ./ wrapper upload -->

        <ul class="list-inline">
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ __('conditions.terms') }}</li>
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ __('conditions.upload_max', ['limit' => $settings->limit_upload_user == 0 ? strtolower(__('admin.unlimited')) : $settings->limit_upload_user ]) }}</li>
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ __('conditions.sex_content') }}</li>
  				<li class="list-inline-item"><i class="bi bi-dot me-1"></i> {{ __('conditions.own_images') }}</li>
  			</ul>

        <!-- Additional Example Output Images (Optional, Max 5) -->
        <div class="card border border-dashed rounded-4 p-3 mt-3 bg-white shadow-sm">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-images me-1 text-primary"></i> Example Output Images <span class="badge bg-secondary font-weight-normal ms-1" style="font-size:0.75rem;">Optional</span>
            </h6>
            <small class="text-muted">Max 5 images</small>
          </div>
          <p class="small text-muted mb-2">Upload additional output images generated using this prompt to showcase its capabilities to users.</p>
          <input type="file" accept="image/*" name="example_photos[]" id="examplePhotosInput" multiple class="custom-file form-control" style="position: static !important; opacity: 1 !important; visibility: visible !important;">
          <div id="examplePreviewsContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
        </div>

    </div>

	<!-- col-md-12 -->
	<div class="col-md-5">

			<div class="card border-0">

				<div class="card-body p-0">

          <div class="mb-3">
           <input type="text" required class="form-control" id="title" name="title" placeholder="{{ __('admin.title') }}">
					 <div class="alert alert-info py-2 mt-2">
						 <small><i class="bi-info-circle me-2"></i> {{ __('misc.info_data_iptc') }}</small>
					 </div>
         </div>

         <div class="mb-3">
           <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug (Optional)">
           <small class="text-muted d-block mt-1">* Custom URL slug. If left empty, title will be used as slug.</small>
         </div>

         <div class="mb-3">
           <textarea required class="form-control" id="prompt" name="prompt" rows="4" placeholder="Enter complete photography prompt..."></textarea>
           <small class="text-muted d-block mt-1">* Enter the full AI prompt used for this product photo.</small>
         </div>

         <div class="form-floating mb-3">
           <select name="ai_model" required class="form-select" id="ai_model">
             <option value="">Select AI Model</option>
             @foreach (App\Models\Images::getAiModels() as $model)
               <option value="{{ $model }}">{{ $model }}</option>
             @endforeach
           </select>
           <label for="ai_model">AI Model</label>
         </div>

         <div class="mb-3">
          <input type="text" required class="form-control tagin" id="tagInput" name="tags" placeholder="{{ __('misc.tags') }}">
          <small class="d-block">* {{ __('misc.add_tags_guide') }} ({{__('misc.maximum_tags', ['limit' => $settings->tags_limit ]) }})</small>
        </div>

        <!-- SEO Fields Card Section -->
        <div class="card border rounded-4 p-3 mb-3 bg-light shadow-sm">
          <h6 class="fw-bold text-dark mb-2">
            <i class="bi bi-search me-1 text-primary"></i> Search Engine Optimization (SEO) <span class="badge bg-secondary font-weight-normal ms-1" style="font-size:0.75rem;">Optional</span>
          </h6>
          <p class="small text-muted mb-3">Custom SEO metadata for search engine snippets. Leave blank to use automatic fallbacks.</p>
          
          <div class="mb-3">
            <label for="meta_title" class="form-label text-dark small fw-medium">Meta Title</label>
            <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="SEO Title (e.g. Premium Product Photography Prompt)">
          </div>
          
          <div class="mb-3">
            <label for="meta_description" class="form-label text-dark small fw-medium">Meta Description</label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="2" placeholder="Brief summary for search result snippets..."></textarea>
          </div>
          
          <div class="mb-0">
            <label for="meta_keywords" class="form-label text-dark small fw-medium">Meta Keywords</label>
            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
          </div>
        </div>

        <div class="form-floating mb-3">
        <select name="categories_id" class="form-select" id="category">
									<option value="">{{__('misc.please_select_category')}}</option>
          @foreach (Categories::where('mode','on')->orderBy('name')->get() as $category)
            <option value="{{$category->id}}">
							{{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
						</option>
            @endforeach
        </select>
        <label for="category">{{ __('misc.category') }}</label>
      </div>

      <div class="form-floating mb-3">
        <select name="subcategory" class="form-select" id="input-subcategory">
          <option selected value="" id="subcategory">{{ __('misc.select') }}</option>
        </select>
        <label for="input-subcategory">{{ __('misc.subcategory') }}</label>
      </div>

            @if ($settings->sell_option == 'on'
                && $settings->who_can_sell == 'all'
                || $settings->sell_option == 'on'
                && $settings->who_can_sell == 'admin'
                && auth()->user()->isSuperAdmin()
                )

             <div class="form-floating mb-3">
               <select name="item_for_sale" class="form-select" id="itemForSale">
                 <option value="free">Free Prompt (Available to Free Users & Subscribers)</option>
                 <option value="sale">Premium Prompt (Requires ₹149 Subscription Plan)</option>
               </select>
               <label for="itemForSale">Prompt Tier / Access Type</label>
             </div>
             <input type="hidden" name="price" value="0">
             @endif

                  <!-- Start Form Group -->
                <div class="form-floating mb-3 options_free @if ($settings->free_photo_upload == 'off') display-none @endif">
                  <select name="how_use_image" class="form-select" id="how_use_image">
                    <option value="free">{{ __('misc.use_free') }}</option>
                    <option value="free_personal">{{ __('misc.use_free_personal') }}</option>
                     <option value="editorial_only">{{ __('misc.use_editorial_only') }}</option>
                      <option value="web_only">{{ __('misc.use_web_only') }}</option>
                  </select>
                  <label for="how_use_image">{{ __('misc.how_use_image') }}</label>
                </div>

                  <!-- Start Form Group -->
                  <div class="form-floating mb-3">
                  <select name="type_image" class="form-select" id="typeImage">
                    <option value="image">{{ __('misc.image') }}</option>
                    <option value="vector">{{ __('misc.image_and_vector_graphic') }} (AI, EPS, PSD, SVG, CDR, ZIP)</option>
                  </select>
                  <label for="typeImage">{{ __('misc.type_image') }}</label>

                  <div class="w-100 display-none" id="vector">
                    <button type="button" class="btn btn-light w-100" id="upload_file" style="margin-top: 10px;border: 1px dashed #bdbdbd;padding: 12px;">
                    <i class="bi bi-cloud-arrow-up me-1"></i> {{__('misc.select_file')}} (AI, EPS, PSD, SVG, CDR, ZIP)
                    </button>

                      <input type="file" name="file" id="uploadFile" style="visibility: hidden;">
                  </div>

                  <small class="d-block mt-2" id="fileDocument"></small>
                </div>

                <div class="form-check form-switch form-switch-md mb-3 options_free @if ($settings->free_photo_upload == 'off') display-none @endif">
                <input class="form-check-input" name="attribution_required" type="checkbox" checked value="yes" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault">{{ __('misc.attribution_required') }}</label>
              </div>

              <div class="form-floating mb-3">
               <textarea class="form-control" placeholder="{{ __('misc.description') }}" name="description" id="input-description" style="height: 100px"></textarea>
               <label for="input-description">{{ __('admin.description') }} ({{ __('misc.optional') }})</label>
             </div>
                    <!-- Alert -->
            <div class="alert alert-danger display-none" id="dangerAlert">
							<ul class="list-unstyled mb-0" id="showErrors"></ul>
						</div><!-- Alert -->

                  <div class="box-footer text-center">
                    <button type="submit" id="upload" class="btn btn-lg btn-custom w-100" data-msg-processing="{{__('misc.processing')}}" data-error="{{__('misc.error')}}" data-msg-error="{{__('misc.err_internet_disconnected')}}">
                      <i class="bi bi-cloud-arrow-up-fill me-1"></i> {{ __('users.upload') }}
                    </button>
                  </div><!-- /.box-footer -->
                </form>
         	</div>
         </div>

		</div>
		<!-- col-md-12-->

		@else
		<h3 class="mt-0 text-center fw-light">
			<span class="w-100 d-block mb-4 display-1 text-warning">
				<i class="bi bi-exclamation-triangle-fill"></i>
			</span>

	    		{{__('misc.limit_uploads_user')}}
	    	</h3>
		@endif

@else
	   <h3 class="mt-0 text-center fw-light">
			 <span class="w-100 d-block mb-4 display-1 text-warning">
 				<i class="bi bi-exclamation-triangle-fill"></i>
 			</span>

	    	{{__('misc.confirm_email')}} <span class="fw-bold">{{auth()->user()->email}}</span>
	    	</h3>
        @endif
          {{-- Verify User Active --}}

	</div><!-- row -->
</div><!-- container -->
</section>
@endsection

@section('javascript')
	<script src="{{ asset('public/js/tagin/tagin.min.js') }}" type="text/javascript"></script>

	<script type="text/javascript">

  const tagin = new Tagin(document.querySelector('.tagin'), {
		enter: true,
    placeholder: '{{ __("misc.add_tag") }}',
	});

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

  function replaceString(string) {
  	return string.replace(/[\-\_\.\+]/ig,' ')
  }

$('#removePhoto').click(function(){
	 	$('#filePhoto').val('');
	 	$('#title').val('');
	 	$('.previewPhoto').css({backgroundImage: 'none'}).hide();
	 	$('.filer-input-dragDrop').removeClass('hoverClass');
    $(this).hide();
	 });

//================== START FILE IMAGE FILE READER
$("#filePhoto").on('change', function(){

	var loaded = false;
	if(window.File && window.FileReader && window.FileList && window.Blob){
		if($(this).val()){ //check empty input filed
			oFReader = new FileReader(), rFilter = /^(?:image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/png|image)$/i;
			if($(this)[0].files.length === 0){return}


			var oFile = $(this)[0].files[0];
			var fsize = $(this)[0].files[0].size; //get file size
			var ftype = $(this)[0].files[0].type; // get file type


			if(!rFilter.test(oFile.type)) {
				$('#filePhoto').val('');
				$('.popout').addClass('popout-error').html("{{ __('misc.formats_available') }}").fadeIn(500).delay(5000).fadeOut();
				return false;
			}

			var allowed_file_size = {{$settings->file_size_allowed * 1024}};

			if(fsize>allowed_file_size){
				$('#filePhoto').val('');
				$('.popout').addClass('popout-error').html("{{__('misc.max_size').': '.Helper::formatBytes($settings->file_size_allowed * 1024)}}").fadeIn(500).delay(5000).fadeOut();
				return false;
			}
		<?php 
			$dimensions = explode('x', $settings->min_width_height_image); 
			$dimW = (int) ($dimensions[0] ?? 1024);
			$dimH = (int) ($dimensions[1] ?? 768);
			$minDim = min($dimW, $dimH);
			$maxDim = max($dimW, $dimH);
		?>

			oFReader.onload = function (e) {

				var image = new Image();
			    image.src = oFReader.result;

				image.onload = function() {

					var imgMin = Math.min(image.width, image.height);
					var imgMax = Math.max(image.width, image.height);

			    	if (imgMin < {{ $minDim }} || imgMax < {{ $maxDim }}) {
			    		$('#filePhoto').val('');
			    		$('.popout').addClass('popout-error').html("{{__('misc.width_min',['data' => $dimensions[0]])}}").fadeIn(500).delay(5000).fadeOut();
			    		return false;
			    	}

            $('.previewPhoto').css({backgroundImage: 'url('+e.target.result+')'}).show();
            $('#removePhoto').show();
			    	$('.filer-input-dragDrop').addClass('hoverClass');
			    	var _filname =  oFile.name;
					  var fileName = _filname.substr(0, _filname.lastIndexOf('.'));
			    	$('#title').val(replaceString(fileName));
			    };// <<--- image.onload


           }

           oFReader.readAsDataURL($(this)[0].files[0]);

		}
	} else{
		$('.popout').html('Can\'t upload! Your browser does not support File API! Try again with modern browsers like Chrome or Firefox.').fadeIn(500).delay(5000).fadeOut();
		return false;
	}
});

	$('input[type="file"]').attr('title', window.URL ? ' ' : '');

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
				$('.popout').addClass('popout-error').html("{{__('misc.max_size_vector').': '.Helper::formatBytes($settings->file_size_allowed_vector * 1024)}}").fadeIn(500).delay(4000).fadeOut();
        $(this).val('');
				return false;
			}

			$('#fileDocument').html('<i class="fa fa-paperclip"></i> <strong class="text-muted"><em>' + oFile.name + '</em></strong> - <a href="javascript:void(0);" id="deleteFile" class="text-danger">{{__('misc.delete')}}</a>');

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

document.getElementById('examplePhotosInput')?.addEventListener('change', function(e) {
  const container = document.getElementById('examplePreviewsContainer');
  container.innerHTML = '';
  const files = Array.from(e.target.files).slice(0, 5);
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(evt) {
        const img = document.createElement('img');
        img.src = evt.target.result;
        img.className = 'img-thumbnail rounded shadow-sm';
        img.style.width = '75px';
        img.style.height = '75px';
        img.style.objectFit = 'cover';
        container.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
});
</script>


@endsection
