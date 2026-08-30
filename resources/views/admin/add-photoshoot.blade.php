@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/photoshoots') }}">Photoshoots</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Add New Photoshoot</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@include('errors.errors-forms')

			<div class="card shadow-custom border-0 mb-4">
				<div class="card-header bg-white py-3">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi-plus-circle me-2 text-primary"></i>Add New Photoshoot
          </h5>
        </div>
				<div class="card-body p-lg-4">

          <form method="POST" action="{{ url('panel/admin/photoshoots/add') }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" required class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Photoshoot Title">
                  <label for="title">Photoshoot Title *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="Photoshoot Slug">
                  <label for="slug">Photoshoot Slug (Optional)</label>
                </div>
                <small class="text-muted d-block mt-1">* Custom URL identifier. If left empty, title will be used as slug.</small>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select name="categories_id" class="form-select" id="categories_id">
                    <option value="">None / General</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" @selected(old('categories_id') == $category->id)>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  <label for="categories_id">Category</label>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-floating">
                  <textarea class="form-control" name="description" id="description" placeholder="Description" style="height: 90px;">{{ old('description') }}</textarea>
                  <label for="description">Description (Optional)</label>
                </div>
              </div>
            </div>

            <div class="mt-4 text-end">
              <a href="{{ url('panel/admin/photoshoots') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">Cancel</a>
              <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-check-lg me-1"></i> Create Photoshoot
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>

  </div>
</div>
@endsection
