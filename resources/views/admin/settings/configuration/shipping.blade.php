@extends('admin.include.vertical', ['title' => 'Shipping Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Shipping'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
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

    @php
        $selectedAreas = old('operation_areas', $settings['operation_areas'] ?? []);
    @endphp

    <form action="{{ route('admin.settings.shipping.update') }}" method="POST" class="settings-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Your Operation Area</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-light" id="selectAllAreas">Select All</button>
                            <button type="button" class="btn btn-sm btn-light" id="clearAllAreas">Clear All</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="me-2" data-lucide="info"></i>
                            Select every district your business physically operates/delivers from as a local
                            hub. Orders shipping to a customer in any selected district get the lower
                            <strong>In-Area</strong> charge; every other district gets the
                            <strong>Out-of-Area</strong> charge.
                        </div>

                        <div class="app-search mb-3">
                            <input type="text" class="form-control" id="areaSearchInput"
                                placeholder="Search districts...">
                            <i class="app-search-icon text-muted" data-lucide="search"></i>
                        </div>

                        @if ($errors->has('operation_areas'))
                            <div class="text-danger small mb-2">{{ $errors->first('operation_areas') }}</div>
                        @endif

                        <div class="row" id="districtGrid" style="max-height:320px; overflow-y:auto;">
                            @foreach ($districts as $district)
                                <div class="col-md-4 col-6 mb-2 district-option">
                                    <div class="form-check">
                                        <input class="form-check-input area-checkbox" type="checkbox"
                                            name="operation_areas[]" value="{{ $district }}"
                                            id="area_{{ Str::slug($district) }}"
                                            {{ in_array($district, $selectedAreas) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="area_{{ Str::slug($district) }}">
                                            {{ $district }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Shipping Rates</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="inside_area_charge">
                                    In-Area Charge <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal">(within your operation area)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('inside_area_charge') is-invalid @enderror"
                                        id="inside_area_charge" name="inside_area_charge"
                                        value="{{ old('inside_area_charge', $settings['inside_area_charge'] ?? '') }}"
                                        required>
                                    @error('inside_area_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="outside_area_charge">
                                    Out-of-Area Charge <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal">(everywhere else)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('outside_area_charge') is-invalid @enderror"
                                        id="outside_area_charge" name="outside_area_charge"
                                        value="{{ old('outside_area_charge', $settings['outside_area_charge'] ?? '') }}"
                                        required>
                                    @error('outside_area_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Free Shipping</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                id="enable_free_shipping" name="enable_free_shipping"
                                {{ old('enable_free_shipping', $settings['enable_free_shipping'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="enable_free_shipping">
                                Enable Free Shipping Threshold
                            </label>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="free_shipping_threshold">
                                Free Shipping Minimum Order
                            </label>
                            <div class="input-group" style="max-width:250px">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="free_shipping_threshold" name="free_shipping_threshold"
                                    value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '') }}">
                            </div>
                            <div class="form-text">Orders at or above this subtotal ship free, overriding both
                                rates above.</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Other</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="default_processing_days">
                                    Default Processing Time <small class="text-muted fw-normal">(days)</small>
                                </label>
                                <input type="number" min="0" max="60" class="form-control"
                                    id="default_processing_days" name="default_processing_days"
                                    value="{{ old('default_processing_days', $settings['default_processing_days'] ?? 1) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="shipping_note">
                                    Shipping Policy Note <small class="text-muted fw-normal">(shown at
                                        checkout)</small>
                                </label>
                                <textarea class="form-control" id="shipping_note" name="shipping_note" rows="3">{{ old('shipping_note', $settings['shipping_note'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Live preview — purely client-side, mirrors SettingService::resolveShippingCharge() --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Try It</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold" for="previewCity">Customer's City/District</label>
                        <input type="text" class="form-control mb-2" id="previewCity"
                            placeholder="e.g. Dhaka or Chattogram">
                        <label class="form-label fw-semibold" for="previewSubtotal">Order Subtotal
                            <small class="text-muted fw-normal">(Optional)</small></label>
                        <input type="number" step="0.01" min="0" class="form-control mb-3"
                            id="previewSubtotal" placeholder="0.00">
                        <div class="alert alert-secondary mb-0" id="previewResult">
                            Select your operation area(s) and enter a city to preview the charge.
                        </div>
                    </div>
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
    <script>
        window.__shippingSettingsInitial = {
            insideCharge: {{ (float) old('inside_area_charge', $settings['inside_area_charge'] ?? 0) }},
            outsideCharge: {{ (float) old('outside_area_charge', $settings['outside_area_charge'] ?? 0) }},
            freeShippingEnabled: {{ old('enable_free_shipping', $settings['enable_free_shipping'] ?? false) ? 'true' : 'false' }},
            freeShippingThreshold: {{ (float) old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? 0) }},
        };
    </script>
    @vite(['resources/js/pages/admin-settings-configuration.js'])
@endsection
