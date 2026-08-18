@extends('admin.include.vertical', ['title' => 'Payment Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Payment Methods'])

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

    <form action="{{ route('admin.settings.payment.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Cash on Delivery</h5></div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="cod_enabled" name="cod_enabled"
                                {{ old('cod_enabled', $settings['cod_enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="cod_enabled">Enable Cash on Delivery</label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="cod_extra_fee">
                                COD Extra Fee <small class="text-muted fw-normal">(Optional)</small>
                            </label>
                            <div class="input-group" style="max-width:220px">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="cod_extra_fee" name="cod_extra_fee"
                                    value="{{ old('cod_extra_fee', $settings['cod_extra_fee'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Bank Transfer</h5></div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="bank_transfer_enabled" name="bank_transfer_enabled"
                                {{ old('bank_transfer_enabled', $settings['bank_transfer_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="bank_transfer_enabled">Enable Manual Bank
                                Transfer</label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="bank_details">
                                Bank Account Details <small class="text-muted fw-normal">(shown to customer at
                                    checkout)</small>
                            </label>
                            <textarea class="form-control" id="bank_details" name="bank_details" rows="4"
                                placeholder="Bank Name: ...&#10;Account Name: ...&#10;Account Number: ...&#10;Routing/Branch: ...">{{ old('bank_details', $settings['bank_details'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Online Payment Gateway</h5></div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="online_payment_enabled" name="online_payment_enabled"
                                {{ old('online_payment_enabled', $settings['online_payment_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="online_payment_enabled">Enable Online
                                Payment</label>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <i class="me-2" data-lucide="triangle-alert"></i>
                            Gateway API keys (Stripe/SSLCommerz/etc.) are configured via <code>.env</code> for
                            security. This toggle only controls whether the option appears at checkout.
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