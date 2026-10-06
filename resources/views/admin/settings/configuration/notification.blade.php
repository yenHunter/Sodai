@extends('admin.include.vertical', ['title' => 'Notification Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Notifications'])

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

    <form action="{{ route('admin.settings.notification.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Admin Alerts</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="admin_alert_email">
                                Alert Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('admin_alert_email') is-invalid @enderror"
                                id="admin_alert_email" name="admin_alert_email"
                                value="{{ old('admin_alert_email', $settings['admin_alert_email'] ?? '') }}"
                                required>
                            @error('admin_alert_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Where store-wide notifications are sent (separate from
                                per-admin login).</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="notify_new_order" name="notify_new_order"
                                {{ old('notify_new_order', $settings['notify_new_order'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="notify_new_order">
                                Notify on New Order
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="notify_low_stock" name="notify_low_stock"
                                {{ old('notify_low_stock', $settings['notify_low_stock'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="notify_low_stock">
                                Notify on Low Stock
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="abandoned_cart_enabled" name="abandoned_cart_enabled"
                                {{ old('abandoned_cart_enabled', ($settings['abandoned_cart_enabled'] ?? '0') === '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="abandoned_cart_enabled">
                                Send abandoned-cart reminders
                            </label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="abandoned_cart_hours">
                                Send after (hours of inactivity)
                            </label>
                            <input type="number" class="form-control" min="1" max="720"
                                id="abandoned_cart_hours" name="abandoned_cart_hours"
                                value="{{ old('abandoned_cart_hours', $settings['abandoned_cart_hours'] ?? 24) }}">
                            <div class="form-text">Emails are queued automatically twice an hour when enabled.</div>
                        </div>

                        <div class="form-check form-switch mb-3 mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="weekly_sales_summary_enabled" name="weekly_sales_summary_enabled"
                                {{ old('weekly_sales_summary_enabled', ($settings['weekly_sales_summary_enabled'] ?? '0') === '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="weekly_sales_summary_enabled">
                                Send weekly sales summary email
                            </label>
                            <div class="form-text">
                                Emails the store notification email every Monday at 8:00 AM with last week's revenue,
                                orders and best sellers.
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="monthly_sales_summary_enabled" name="monthly_sales_summary_enabled"
                                {{ old('monthly_sales_summary_enabled', ($settings['monthly_sales_summary_enabled'] ?? '0') === '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="monthly_sales_summary_enabled">
                                Send monthly sales summary email
                            </label>
                            <div class="form-text">
                                Emails the store notification email on the 1st of every month at 8:00 AM with last
                                month's revenue, orders and best sellers.
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