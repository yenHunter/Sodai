@extends('admin.include.vertical', ['title' => 'Shipping Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Shipping'])

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

    <form action="{{ route('admin.settings.shipping.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Shipping Rate</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="flat_rate">
                                    Flat Shipping Rate <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('flat_rate') is-invalid @enderror"
                                        id="flat_rate" name="flat_rate"
                                        value="{{ old('flat_rate', $settings['flat_rate'] ?? 0) }}" required>
                                    @error('flat_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Applied to every order unless free shipping applies.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="default_processing_days">
                                    Default Processing Time <small class="text-muted fw-normal">(days)</small>
                                </label>
                                <input type="number" min="0" max="60" class="form-control"
                                    id="default_processing_days" name="default_processing_days"
                                    value="{{ old('default_processing_days', $settings['default_processing_days'] ?? 1) }}">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" value="1"
                                        id="enable_free_shipping" name="enable_free_shipping"
                                        {{ old('enable_free_shipping', $settings['enable_free_shipping'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="enable_free_shipping">
                                        Enable Free Shipping Threshold
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="free_shipping_threshold">
                                    Free Shipping Minimum Order
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="free_shipping_threshold" name="free_shipping_threshold"
                                        value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '') }}">
                                </div>
                                <div class="form-text">Orders at or above this subtotal ship free.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="shipping_note">
                                    Shipping Policy Note <small class="text-muted fw-normal">(shown at
                                        checkout)</small>
                                </label>
                                <textarea class="form-control" id="shipping_note" name="shipping_note"
                                    rows="3">{{ old('shipping_note', $settings['shipping_note'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="alert alert-info">
                    <i class="me-2" data-lucide="info"></i>
                    This is a single flat-rate model. If you need per-zone/per-weight rates later,
                    that can be added as a dedicated Shipping Zones module.
                </div>
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