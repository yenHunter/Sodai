@extends('admin.include.vertical', ['title' => 'Edit ' . $page->title])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'CMS', 'title' => 'Edit ' . $page->title])

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-sm btn-light">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Pages
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.cms.pages.update', $page->slug) }}" method="POST" id="cmsPageForm">
        @csrf
        <div class="row">
            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-header d-block p-3">
                        <h4 class="card-title mb-1">Page Content</h4>
                        <p class="text-muted mb-0">Visible to customers at <code>/{{ $page->slug }}</code>.</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="pageTitle">
                                Page Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="pageTitle"
                                name="title" value="{{ old('title', $page->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Content</label>
                            <div id="pageContentEditor" class="quill-editor-container" style="min-height: 320px;">
                                {!! old('content', $page->content) !!}
                            </div>
                            <textarea name="content" id="pageContentInput" class="d-none">{{ old('content', $page->content) }}</textarea>
                            @error('content')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-header d-block p-3">
                        <h4 class="card-title mb-1">SEO Meta</h4>
                        <p class="text-muted mb-0">Optional, shown in search results.</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="metaTitle">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                id="metaTitle" name="meta_title" maxlength="255"
                                value="{{ old('meta_title', $page->meta_title) }}">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="metaDescription">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="metaDescription"
                                name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="cmsPageSubmitBtn">
                    <i data-lucide="save" class="fs-sm me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </form>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-cms-page-edit.js'])
@endsection
