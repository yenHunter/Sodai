@extends('admin.include.vertical', ['title' => 'Invoice Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Invoice Settings'])

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

    <form action="{{ route('admin.settings.invoice.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Invoice Numbering</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="invoice_prefix">
                                    Invoice Prefix <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('invoice_prefix') is-invalid @enderror"
                                    id="invoice_prefix" name="invoice_prefix"
                                    value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}"
                                    required>
                                @error('invoice_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="invoice_starting_number">
                                    Starting Number <span class="text-danger">*</span>
                                </label>
                                <input type="number" min="1"
                                    class="form-control @error('invoice_starting_number') is-invalid @enderror"
                                    id="invoice_starting_number" name="invoice_starting_number"
                                    value="{{ old('invoice_starting_number', $settings['invoice_starting_number'] ?? 1000) }}"
                                    required>
                                @error('invoice_starting_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" value="1"
                                        id="show_tax_breakdown" name="show_tax_breakdown"
                                        {{ old('show_tax_breakdown', $settings['show_tax_breakdown'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_tax_breakdown">
                                        Show Tax Breakdown on Invoice
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="invoice_footer_note">
                                    Invoice Footer Note <small class="text-muted fw-normal">(Optional — e.g. thank
                                        you message, return policy)</small>
                                </label>
                                <textarea class="form-control" id="invoice_footer_note" name="invoice_footer_note"
                                    rows="3">{{ old('invoice_footer_note', $settings['invoice_footer_note'] ?? '') }}</textarea>
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