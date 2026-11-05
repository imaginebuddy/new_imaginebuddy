@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <a class="text-reset" href="{{ url('panel/admin/website-pages-content?tab=ai_model_detail') }}">Website Pages Content</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Edit AI Model: {{ $content->model_name }}</span>

    <a href="{{ url('ai-model', $slug) }}" target="_blank" class="btn btn-sm btn-outline-primary float-lg-end mt-1 mt-lg-0">
        <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Page
    </a>
</h5>

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @include('errors.errors-forms')

            <div class="card shadow-custom border-0 mb-4">
                <div class="card-body p-lg-5">
                    <form method="POST" action="{{ url('panel/admin/website-pages-content/ai-model', $slug) }}">
                        @csrf

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">{{ __('admin.name') }}</label>
                            <div class="col-sm-10">
                                <input value="{{ old('model_name', $content->model_name) }}" name="model_name" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Live Slug</label>
                            <div class="col-sm-10">
                                <input value="{{ $slug }}" readonly type="text" class="form-control bg-light">
                                <small class="text-muted">Target live URL: <code>{{ url('ai-model', $slug) }}</code> ({{ number_format($totalPrompts) }} active prompts)</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Page Title (H1)</label>
                            <div class="col-sm-10">
                                <input value="{{ old('page_title', $content->page_title) }}" name="page_title" type="text" class="form-control" placeholder="{{ $content->model_name }} - AI Prompts">
                                <small class="text-muted d-block mt-1">Tip: e.g. <code>{{ $content->model_name }} &lt;span style="color: #00d690;"&gt;AI Prompts&lt;/span&gt;</code></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Hero Subtitle</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="hero_lead" rows="3" placeholder="Leave empty to use the standard prompt count formula (Explore {count} curated, tested AI prompts designed for {{ $content->model_name }}...).">{{ old('hero_lead', $content->hero_lead) }}</textarea>
                                <small class="text-muted">Descriptive subtitle shown beneath the H1. Leave empty to automatically use the standard prompt count formula.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Title (SEO)</label>
                            <div class="col-sm-10">
                                <input value="{{ old('seo_title', $content->seo_title) }}" name="seo_title" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Keywords (SEO)</label>
                            <div class="col-sm-10">
                                <input value="{{ old('meta_keywords', $content->meta_keywords) }}" name="meta_keywords" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label text-lg-end">Description (SEO)</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="meta_description" rows="4">{{ old('meta_description', $content->meta_description) }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-dark px-5 py-2">
                                    <i class="bi bi-check2-circle me-1"></i> {{ __('admin.save') }} Changes
                                </button>
                                <a href="{{ url('panel/admin/website-pages-content?tab=ai_model_detail') }}" class="btn btn-outline-secondary ms-2 px-4 py-2">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
