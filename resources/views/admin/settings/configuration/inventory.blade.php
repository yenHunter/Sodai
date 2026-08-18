@extends('admin.include.vertical', ['title' => 'Inventory Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Inventory'])

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

    <form action="{{ route('admin.settings.inventory.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Stock Behavior</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="default_low_stock_threshold">
                                Default Low-Stock Threshold <span class="text-danger">*</span>
                            </label>
                            <input type="number" min="0" max="1000"
                                class="form-control @error('default_low_stock_threshold') is-invalid @enderror"
                                id="default_low_stock_threshold" name="default_low_stock_threshold"
                                value="{{ old('default_low_stock_threshold', $settings['default_low_stock_threshold'] ?? 5) }}"
                                style="max-width:200px" required>
                            @error('default_low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Used as the default when a new product variant doesn't set its own
                                threshold.</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="hide_out_of_stock_products" name="hide_out_of_stock_products"
                                {{ old('hide_out_of_stock_products', $settings['hide_out_of_stock_products'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="hide_out_of_stock_products">
                                Hide Out-of-Stock Products on Storefront
                            </label>
                        </div>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="allow_backorders" name="allow_backorders"
                                {{ old('allow_backorders', $settings['allow_backorders'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="allow_backorders">
                                Allow Backorders <small class="text-muted fw-normal">(customers can order
                                    out-of-stock items)</small>
                            </label>
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