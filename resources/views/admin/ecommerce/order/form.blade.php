@php
    $isEdit = isset($order);
@endphp

<div class="row">
    {{-- LEFT: Customer + Product Search + Cart --}}
    <div class="col-xxl-8">

        {{-- Customer Card --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Customer</h4>
                    <p class="text-muted mb-0">Select an existing customer or add a new one.</p>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                    data-bs-target="#quickCustomerModal">
                    <i data-lucide="user-plus" class="fs-sm me-1"></i>Quick Add
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="customerSelect"
                        name="user_id" required data-search-url="{{ route('admin.ecommerce.order.customers.search') }}"
                        data-address-url-template="{{ route('admin.ecommerce.order.customers.address', ['customer' => '__ID__']) }}">
                        @if ($isEdit && $order->user)
                            <option value="{{ $order->user->id }}" selected>{{ $order->user->name }}
                                ({{ $order->user->email }})</option>
                        @endif
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Recipient Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_name') is-invalid @enderror"
                            name="shipping_name" id="shippingName"
                            value="{{ old('shipping_name', $order->shipping_name ?? '') }}" required>
                        @error('shipping_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('shipping_email') is-invalid @enderror"
                            name="shipping_email" id="shippingEmail"
                            value="{{ old('shipping_email', $order->shipping_email ?? '') }}" required>
                        @error('shipping_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_phone') is-invalid @enderror"
                            name="shipping_phone" id="shippingPhone"
                            value="{{ old('shipping_phone', $order->shipping_phone ?? '') }}" required>
                        @error('shipping_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_country') is-invalid @enderror"
                            name="shipping_country" id="shippingCountry"
                            value="{{ old('shipping_country', $order->shipping_country ?? 'Bangladesh') }}" required>
                        @error('shipping_country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('shipping_address') is-invalid @enderror" name="shipping_address"
                            id="shippingAddress" rows="2" required>{{ old('shipping_address', $order->shipping_address ?? '') }}</textarea>
                        @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_city') is-invalid @enderror"
                            name="shipping_city" id="shippingCity"
                            value="{{ old('shipping_city', $order->shipping_city ?? '') }}" required>
                        @error('shipping_city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_state') is-invalid @enderror"
                            name="shipping_state" id="shippingState"
                            value="{{ old('shipping_state', $order->shipping_state ?? '') }}" required>
                        @error('shipping_state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ZIP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('shipping_zip') is-invalid @enderror"
                            name="shipping_zip" id="shippingZip"
                            value="{{ old('shipping_zip', $order->shipping_zip ?? '') }}" required>
                        @error('shipping_zip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Search + Cart Card --}}
        <div class="card">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Products</h4>
                <p class="text-muted mb-0">Search and add products to the cart.</p>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <select class="form-select" id="productSearchSelect"
                        data-search-url="{{ route('admin.ecommerce.order.products.search') }}">
                        <option value="">Search product by name or SKU...</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-centered align-middle mb-0" id="cartTable">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th>Product</th>
                                <th style="width:110px">Price</th>
                                <th style="width:120px">Qty</th>
                                <th style="width:120px" class="text-end">Line Total</th>
                                <th style="width:1%"></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr id="cartEmptyRow">
                                <td colspan="5" class="text-center text-muted py-4">Cart is empty. Search a product
                                    above to add it.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Order Summary --}}
    <div class="col-xxl-4">
        <div class="card card-top-sticky">
            <div class="card-header d-block p-3">
                <h4 class="card-title mb-1">Order Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold" id="summarySubtotal">$0.00</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Discount ($)</label>
                    <input type="number" class="form-control @error('discount_amount') is-invalid @enderror"
                        name="discount_amount" id="discountAmount" min="0" step="0.01"
                        value="{{ old('discount_amount', $order->discount_amount ?? 0) }}">
                    @error('discount_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Auto-filled when a coupon is applied. Editable to override.</div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0">Shipping Charge ($)</label>
                        <span class="badge bg-secondary-subtle text-secondary" id="shippingAreaBadge"></span>
                    </div>
                    <input type="number" class="form-control @error('shipping_charge') is-invalid @enderror"
                        name="shipping_charge" id="shippingCharge" min="0" step="0.01"
                        value="{{ old('shipping_charge', $order->shipping_charge ?? '') }}"
                        placeholder="Auto from shipping settings">
                    @error('shipping_charge')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Auto-calculated from Shipping settings based on the city above. Editable to
                        override.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tax ($) <span class="text-muted" id="taxLabelHint"></span></label>
                    <input type="number" class="form-control @error('tax_amount') is-invalid @enderror"
                        name="tax_amount" id="taxAmount" min="0" step="0.01"
                        value="{{ old('tax_amount', $order->tax_amount ?? '') }}"
                        placeholder="Auto from tax settings">
                    @error('tax_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Auto-calculated from Tax settings. Editable to override.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Coupon Code <span class="text-muted">(Optional)</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('coupon_code') is-invalid @enderror"
                            name="coupon_code" id="couponCodeInput"
                            value="{{ old('coupon_code', $order->coupon_code ?? '') }}"
                            style="text-transform:uppercase" placeholder="Enter coupon code">
                        <button type="button" class="btn btn-outline-secondary" id="applyCouponBtn"
                            data-apply-url="{{ route('admin.ecommerce.order.apply-coupon') }}">Apply</button>
                    </div>
                    @error('coupon_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div id="couponFeedback" class="small mt-1"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes <span class="text-muted">(Optional)</span></label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes', $order->notes ?? '') }}</textarea>
                </div>

                <hr>
                <div class="d-flex justify-content-between fs-lg fw-bold">
                    <span>Grand Total</span>
                    <span id="summaryTotal">$0.00</span>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success w-100" id="submitOrderBtn">
                    <i data-lucide="save" class="me-1" style="width:16px;height:16px;"></i>
                    {{ $isEdit ? 'Update Order' : 'Place Order' }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden container for cart items — populated by JS before submit --}}
<div id="cartItemsInputs"></div>
<input type="hidden" id="orderIdField" value="{{ $order->id ?? '' }}">
<input type="hidden" id="previewShippingUrl" value="{{ route('admin.ecommerce.order.preview-shipping') }}">
<input type="hidden" id="previewTaxUrl" value="{{ route('admin.ecommerce.order.preview-tax') }}">

{{-- Quick Add Customer Modal --}}
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="quickCustomerError" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="quickCustomerName">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="quickCustomerEmail">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone <span class="text-muted">(Optional)</span></label>
                    <input type="text" class="form-control" id="quickCustomerPhone">
                </div>
                <p class="text-muted small mb-0">A temporary password will be generated. The customer can reset it
                    later via "Forgot Password" once login is available.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="quickCustomerSubmitBtn">Create & Select</button>
            </div>
        </div>
    </div>
</div>
