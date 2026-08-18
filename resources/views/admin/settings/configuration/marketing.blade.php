@extends('admin.include.vertical', ['title' => 'Marketing Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Marketing (SEO & Social)'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-light">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Configuration
        </a>
    </div>

    <form action="{{ route('admin.settings.marketing.update') }}" method="POST" enctype="multipart/form-data"
        class="settings-form">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Default SEO Meta</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="meta_title">
                                Default Meta Title <small class="text-muted fw-normal">(Optional)</small>
                            </label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                maxlength="255" value="{{ old('meta_title', $seoSettings['meta_title'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="meta_description">
                                Default Meta Description <small class="text-muted fw-normal">(Optional)</small>
                            </label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                maxlength="500">{{ old('meta_description', $seoSettings['meta_description'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="meta_keywords">
                                Default Meta Keywords <small class="text-muted fw-normal">(comma-separated,
                                    Optional)</small>
                            </label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                maxlength="255"
                                value="{{ old('meta_keywords', $seoSettings['meta_keywords'] ?? '') }}">
                        </div>

                        <label class="form-label fw-semibold" for="og_image">
                            Default Social Share Image (OG Image) <small class="text-muted fw-normal">(Optional,
                                1200x630 recommended)</small>
                        </label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-light-subtle"
                                style="width:160px;height:84px;overflow:hidden;">
                                @if (!empty($seoSettings['og_image']))
                                    <img src="{{ asset('storage/' . $seoSettings['og_image']) }}" alt="OG Image"
                                        class="img-fluid" style="max-width:100%;max-height:100%;object-fit:cover"
                                        id="og_imagePreview">
                                @else
                                    <i class="text-muted" data-lucide="image" id="og_imagePreviewIcon"></i>
                                    <img src="" alt="OG Image" class="img-fluid d-none"
                                        style="max-width:100%;max-height:100%;object-fit:cover" id="og_imagePreview">
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control form-control-sm @error('og_image') is-invalid @enderror"
                                    id="og_image" name="og_image" accept="image/jpeg,image/jpg,image/png,image/webp">
                                @error('og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                @if (!empty($seoSettings['og_image']))
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            name="remove_og_image" id="remove_og_image">
                                        <label class="form-check-label small text-danger" for="remove_og_image">
                                            Remove current image
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Social Media Links</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                $socialFields = [
                                    'facebook_url'  => ['label' => 'Facebook',  'icon' => 'facebook'],
                                    'instagram_url' => ['label' => 'Instagram', 'icon' => 'instagram'],
                                    'twitter_url'   => ['label' => 'Twitter / X', 'icon' => 'twitter'],
                                    'youtube_url'   => ['label' => 'YouTube',   'icon' => 'youtube'],
                                    'linkedin_url'  => ['label' => 'LinkedIn',  'icon' => 'linkedin'],
                                ];
                            @endphp
                            @foreach ($socialFields as $key => $meta)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $key }}">{{ $meta['label'] }}</label>
                                    <div class="app-search">
                                        <input type="url" class="form-control @error($key) is-invalid @enderror"
                                            id="{{ $key }}" name="{{ $key }}"
                                            value="{{ old($key, $socialSettings[$key] ?? '') }}"
                                            placeholder="https://{{ strtolower(explode(' ', $meta['label'])[0]) }}.com/yourpage">
                                        <i class="app-search-icon text-muted" data-lucide="{{ $meta['icon'] }}"></i>
                                        @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <button type="submit" class="btn btn-primary w-100 btn-save-settings">
                    <i data-lucide="save" class="fs-sm me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </form>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-settings-configuration.js'])
@endsection