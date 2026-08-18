@extends('admin.include.vertical', ['title' => 'Design Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Design'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="triangle-alert"></i>{{ session('error') }}
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

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-light">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Configuration
        </a>
    </div>

    <form action="{{ route('admin.settings.design.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            @php
                $imageFields = [
                    'logo'      => ['label' => 'Light Logo', 'hint' => 'Used on light backgrounds. PNG/SVG with transparent background recommended.', 'w' => 180, 'h' => 60],
                    'logo_dark' => ['label' => 'Dark Logo', 'hint' => 'Used on dark backgrounds / dark mode.', 'w' => 180, 'h' => 60],
                    'favicon'   => ['label' => 'Favicon', 'hint' => 'Browser tab icon. Square, at least 64x64px.', 'w' => 64, 'h' => 64],
                    'login_bg'  => ['label' => 'Login Background', 'hint' => 'Right-side image on the admin login screen.', 'w' => 240, 'h' => 140],
                ];
            @endphp

            @foreach ($imageFields as $key => $meta)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $meta['label'] }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">{{ $meta['hint'] }}</p>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="border rounded d-flex align-items-center justify-content-center bg-light-subtle"
                                    style="width:{{ $meta['w'] }}px;height:{{ $meta['h'] }}px;overflow:hidden;"
                                    id="{{ $key }}PreviewWrapper">
                                    @if (!empty($settings[$key]))
                                        <img src="{{ asset('storage/' . $settings[$key]) }}" alt="{{ $meta['label'] }}"
                                            class="img-fluid" style="max-width:100%;max-height:100%;object-fit:contain"
                                            id="{{ $key }}Preview">
                                    @else
                                        <i class="text-muted" data-lucide="image" id="{{ $key }}PreviewIcon"></i>
                                        <img src="" alt="{{ $meta['label'] }}" class="img-fluid d-none"
                                            style="max-width:100%;max-height:100%;object-fit:contain" id="{{ $key }}Preview">
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control form-control-sm @error($key) is-invalid @enderror"
                                        id="{{ $key }}" name="{{ $key }}"
                                        accept="{{ $key === 'favicon' ? 'image/png,image/x-icon,image/jpeg,image/webp' : 'image/png,image/jpeg,image/webp,image/svg+xml' }}">
                                    @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror

                                    @if (!empty($settings[$key]))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="remove_{{ $key }}" id="remove_{{ $key }}">
                                            <label class="form-check-label small text-danger" for="remove_{{ $key }}">
                                                Remove current {{ strtolower($meta['label']) }}
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Brand Color</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold" for="primary_color">
                            Primary Color <small class="text-muted fw-normal">(Optional — used for storefront
                                accents)</small>
                        </label>
                        <input type="color" class="form-control form-control-color"
                            id="primary_color" name="primary_color"
                            value="{{ old('primary_color', $settings['primary_color'] ?? '#1a1a2e') }}"
                            title="Choose brand color">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="designSubmitBtn">
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