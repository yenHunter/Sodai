@extends('visitor.layout.app', ['title' => 'My Addresses', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12"><h2 class="ec-breadcrumb-title">My Addresses</h2></div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Address</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                @include('visitor.partials.account-sidebar')

                <div class="ec-shop-rightside col-lg-9 col-md-12">

                    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                    @if ($errors->any())
                        <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                    @endif

                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>My Addresses</h5>
                            <div class="ec-header-btn">
                                <button type="button" class="btn btn-lg btn-primary" id="addAddressBtn" data-bs-toggle="modal" data-bs-target="#addressModal">Add New Address</button>
                            </div>
                        </div>
                        <div class="ec-vendor-card-body">
                            @forelse ($addresses as $address)
                                <div class="row border-bottom py-3 align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            {{ $address->label }}
                                            @if ($address->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @endif
                                        </h6>
                                        <p class="mb-0 text-muted">
                                            {{ $address->recipient_name }} · {{ $address->recipient_phone }}<br>
                                            {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},
                                            {{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}, {{ $address->country }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        @unless ($address->is_default)
                                            <form action="{{ route('visitor.account.addresses.set-default', $address) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-light">Set Default</button>
                                            </form>
                                        @endunless
                                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addressModal"
                                            data-mode="edit"
                                            data-id="{{ $address->id }}"
                                            data-label="{{ $address->label }}"
                                            data-recipient-name="{{ $address->recipient_name }}"
                                            data-recipient-phone="{{ $address->recipient_phone }}"
                                            data-address-line-1="{{ $address->address_line_1 }}"
                                            data-address-line-2="{{ $address->address_line_2 }}"
                                            data-city="{{ $address->city }}"
                                            data-state="{{ $address->state }}"
                                            data-zip-code="{{ $address->zip_code }}"
                                            data-country="{{ $address->country }}"
                                            data-update-url="{{ route('visitor.account.addresses.update', $address) }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('visitor.account.addresses.destroy', $address) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this address?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">You haven't saved any addresses yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add/Edit Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addressForm" action="{{ route('visitor.account.addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Label (e.g. Home, Office)</label>
                                <input type="text" name="label" id="addr_label" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Recipient Name</label>
                                <input type="text" name="recipient_name" id="addr_recipient_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Recipient Phone</label>
                                <input type="text" name="recipient_phone" id="addr_recipient_phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" id="addr_country" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" name="address_line_1" id="addr_address_line_1" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address Line 2 (Optional)</label>
                                <input type="text" name="address_line_2" id="addr_address_line_2" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" id="addr_city" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" id="addr_state" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="zip_code" id="addr_zip_code" class="form-control" required>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" value="1" id="addr_is_default" class="form-check-input mt-1 me-2">
                                    <label class="form-check-label" for="addr_is_default">Set as default address</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addressSubmitBtn">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-account-address.js'])
@endsection