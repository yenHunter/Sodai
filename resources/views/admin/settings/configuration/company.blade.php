@extends('admin.include.vertical', ['title' => 'Company Settings'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Company Information'])

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

    <form action="{{ route('admin.settings.company.update') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Business Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold" for="name">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $settings['name'] ?? '') }}"
                                    required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="tagline">
                                    Tagline <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="tagline" name="tagline"
                                    value="{{ old('tagline', $settings['tagline'] ?? '') }}"
                                    placeholder="e.g. Shop Smart, Shop Sodai">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="email">
                                    Support Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}"
                                    required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="phone">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}"
                                    required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="whatsapp">
                                    WhatsApp <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                    value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="address">
                                    Address <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="2" required>{{ old('address', $settings['address'] ?? '') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="city">
                                    City <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city', $settings['city'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="country">
                                    Country <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="country" name="country"
                                    value="{{ old('country', $settings['country'] ?? 'Bangladesh') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Regional Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="currency">
                                Currency Code <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency"
                                name="currency" required>
                                @foreach (['USD' => '$ US Dollar', 'BDT' => '৳ Bangladeshi Taka', 'EUR' => '€ Euro', 'GBP' => '£ British Pound', 'INR' => '₹ Indian Rupee'] as $code => $label)
                                    <option value="{{ $code }}"
                                        {{ old('currency', $settings['currency'] ?? 'BDT') == $code ? 'selected' : '' }}>
                                        {{ $code }} — {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Symbol Position <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="currency_symbol_position"
                                        id="posBefore" value="before"
                                        {{ old('currency_symbol_position', $settings['currency_symbol_position'] ?? 'before') == 'before' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="posBefore">Before ($100)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="currency_symbol_position"
                                        id="posAfter" value="after"
                                        {{ old('currency_symbol_position', $settings['currency_symbol_position'] ?? '') == 'after' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="posAfter">After (100$)</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="timezone">
                                Timezone <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('timezone') is-invalid @enderror" id="timezone"
                                name="timezone" required>
                                @foreach (['Asia/Dhaka', 'Asia/Kolkata', 'Asia/Karachi', 'UTC', 'America/New_York', 'Europe/London'] as $tz)
                                    <option value="{{ $tz }}"
                                        {{ old('timezone', $settings['timezone'] ?? 'Asia/Dhaka') == $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="companySubmitBtn">
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