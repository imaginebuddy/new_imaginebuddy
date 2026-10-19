@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.billing_information') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

              @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="POST" action="{{ url('panel/admin/billing') }}" enctype="multipart/form-data">
						 @csrf

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.company') }}</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->company }}" name="company" class="form-control">
		          </div>
		        </div>

		        <div class="row mb-3">
		          <label for="select" class="col-sm-2 col-form-labe text-lg-end">{{ __('misc.select_your_country') }}</label>
		          <div class="col-sm-10">
		            <select name="country" class="form-select" id="select">
									<option value="">{{trans('misc.select_your_country')}}</option>
 										 @foreach (Countries::orderBy('country_name')->get() as $country)
 											 <option @if ($settings->country == $country->country_name) selected="selected" @endif value="{{$country->country_name}}">{{ $country->country_name }}</option>
 											 @endforeach
		           </select>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.address') }}</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->address }}" name="address" class="form-control">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.city') }}</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->city }}" name="city" class="form-control">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.zip') }}</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->zip }}" name="zip" class="form-control">
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">{{ __('misc.vat') }}</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->vat }}" name="vat" class="form-control">
		          </div>
		        </div>

						<hr class="my-4">
						<h6 class="text-muted text-uppercase fw-bold mb-3"><i class="bi bi-receipt me-1"></i> Indian GST Configuration</h6>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end fw-semibold">Business GSTIN</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->gstin }}" name="gstin" class="form-control text-uppercase font-monospace" placeholder="e.g. 27AAPFU0939F1ZV" maxlength="15">
		            <div class="form-text">
		              15-character Goods & Services Tax Identification Number (e.g. 27AAPFU0939F1ZV). Leave blank if business is not GST-registered.
		            </div>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end fw-semibold">GST Percentage (%)</label>
		          <div class="col-sm-10">
		            <div class="input-group">
		              <input type="number" step="0.01" min="0" max="100" value="{{ $settings->gst_percentage ?? '18.00' }}" name="gst_percentage" class="form-control" placeholder="18.00">
		              <span class="input-group-text">%</span>
		            </div>
		            <div class="form-text">
		              Configurable GST rate applied to domestic taxable transactions (e.g. 18.00%). B2C retail prices are calculated tax-inclusive.
		            </div>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end fw-semibold">Supplier State</label>
		          <div class="col-sm-10">
		            <select name="supplier_state" class="form-select">
		              <option value="">-- Auto-detect from GSTIN or select state --</option>
		              @foreach (\App\Services\TaxService::getIndianStates() as $code => $stateName)
		                <option value="{{ $stateName }}" @if($settings->supplier_state == $stateName || (empty($settings->supplier_state) && \App\Services\TaxService::getStateCodeFromGstin($settings->gstin) == $code)) selected @endif>
		                  {{ $code }} - {{ $stateName }}
		                </option>
		              @endforeach
		            </select>
		            <div class="form-text">
		              Registered location of your business. Used to determine intra-state (CGST + SGST) vs inter-state (IGST) supply.
		            </div>
		          </div>
		        </div>

						<div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Export LUT / ARN</label>
		          <div class="col-sm-10">
		            <input type="text" value="{{ $settings->lut_number }}" name="lut_number" class="form-control text-uppercase" placeholder="e.g. AD270324000000X">
		            <div class="form-text">
		              Letter of Undertaking (LUT) ARN for zero-rated export of services to international customers without payment of IGST.
		            </div>
		          </div>
		        </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
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

<script type="text/javascript"></script>
  @endsection
