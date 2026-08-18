@extends('admin.include.vertical', ['title' => 'Tax Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Taxes'])

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

    <form action="{{ route('admin.settings.tax.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Tax Configuration</h5></div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="tax_enabled" name="tax_enabled"
                                {{ old('tax_enabled', $settings['tax_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="tax_enabled">Enable Tax Calculation</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="tax_label">
                                    Tax Label
                                </label>
                                <input type="text" class="form-control" id="tax_label" name="tax_label"
                                    value="{{ old('tax_label', $settings['tax_label'] ?? 'VAT') }}"
                                    placeholder="e.g. VAT, GST, Sales Tax">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="tax_rate">
                                    Tax Rate <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100"
                                        class="form-control @error('tax_rate') is-invalid @enderror"
                                        id="tax_rate" name="tax_rate"
                                        value="{{ old('tax_rate', $settings['tax_rate'] ?? 0) }}" required>
                                    <span class="input-group-text">%</span>
                                    @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" value="1"
                                        id="prices_include_tax" name="prices_include_tax"
                                        {{ old('prices_include_tax', $settings['prices_include_tax'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="prices_include_tax">
                                        Product Prices Already Include Tax
                                    </label>
                                </div>
                            </div>
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