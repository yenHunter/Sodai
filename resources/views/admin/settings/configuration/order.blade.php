@extends('admin.include.vertical', ['title' => 'Order Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Order Settings'])

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

    <form action="{{ route('admin.settings.order.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Order Rules</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="order_number_prefix">
                                    Order Number Prefix <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('order_number_prefix') is-invalid @enderror"
                                    id="order_number_prefix" name="order_number_prefix"
                                    value="{{ old('order_number_prefix', $settings['order_number_prefix'] ?? 'ORD-') }}"
                                    required>
                                @error('order_number_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="minimum_order_amount">
                                    Minimum Order Amount <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="minimum_order_amount" name="minimum_order_amount"
                                        value="{{ old('minimum_order_amount', $settings['minimum_order_amount'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="auto_cancel_unpaid_hours">
                                    Auto-Cancel Unpaid Orders After <small class="text-muted fw-normal">(hours,
                                        Optional)</small>
                                </label>
                                <input type="number" min="1" max="720" class="form-control"
                                    id="auto_cancel_unpaid_hours" name="auto_cancel_unpaid_hours"
                                    value="{{ old('auto_cancel_unpaid_hours', $settings['auto_cancel_unpaid_hours'] ?? '') }}"
                                    placeholder="e.g. 24">
                                <div class="form-text">Leave blank to disable auto-cancellation.</div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" value="1"
                                        id="allow_guest_checkout" name="allow_guest_checkout"
                                        {{ old('allow_guest_checkout', $settings['allow_guest_checkout'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="allow_guest_checkout">
                                        Allow Guest Checkout
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