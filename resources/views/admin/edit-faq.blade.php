@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/faqs') }}">{{ __('admin.faqs') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.edit') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/faqs/update', $faq->id) }}">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.question') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
		            <input value="{{ old('question', $faq->question) }}" name="question" required type="text" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Category</label>
		          <div class="col-sm-10">
		            <input value="{{ old('category', $faq->category) }}" name="category" type="text" class="form-control" list="categoryOptions">
                <datalist id="categoryOptions">
                  @foreach ($existingCategories as $cat)
                    <option value="{{ $cat }}">
                  @endforeach
                </datalist>
                <small class="text-muted d-block mt-1">Optional. Groups related FAQs on the public page.</small>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.answer') }} <span class="text-danger">*</span></label>
		          <div class="col-sm-10">
                <textarea class="form-control" name="answer" rows="6" id="content" required>{{ old('answer', $faq->answer) }}</textarea>
                <small class="text-muted d-block mt-1">Rich text supported. Plain paragraphs, bolding, lists, and links are formatted automatically.</small>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.sort_order') }}</label>
		          <div class="col-sm-10">
		            <input value="{{ old('sort_order', $faq->sort_order) }}" name="sort_order" type="number" min="0" class="form-control" style="max-width: 150px;">
                <small class="text-muted d-block mt-1">Lower numbers appear first on the public FAQ page.</small>
		          </div>
		        </div>

            <fieldset class="row mb-3">
              <legend class="col-form-label col-sm-2 pt-0 text-lg-end">{{ __('admin.status') }}</legend>
              <div class="col-sm-10">
                <div class="form-check form-switch form-switch-md">
                  <input class="form-check-input" type="checkbox" name="status" @if(old('status', $faq->status) == 'active') checked="checked" @endif value="active" role="switch" id="statusSwitch">
                  <label class="form-check-label ms-2" for="statusSwitch">Active / Published (Visible on public FAQ page)</label>
                </div>
              </div>
            </fieldset><!-- end row -->

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
                <a href="{{ url('panel/admin/faqs') }}" class="btn btn-outline-secondary mt-3 ms-2">{{ __('admin.cancel') }}</a>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection

@section('javascript')
<script src="{{ asset('public/js/ckeditor/ckeditor-init.js') }}?v={{$settings->version}}" type="text/javascript"></script>
@endsection
