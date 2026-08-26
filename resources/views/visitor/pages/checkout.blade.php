@extends('visitor.layout.app', ['title' => 'Checkout', 'bodyClass' => 'checkout_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Checkout</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Checkout</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="container mt-3"><div class="alert alert-danger">{{ session('error') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        </div>
    @endif

    <section class="ec-page-content section-space-p">
        <div class="container">
            <form id="checkoutForm" action="{{ route('visitor.checkout.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="ec-checkout-leftside col-lg-8 col-md-12">
                        <div class="ec-checkout-content">
                            <div class="ec-checkout-inner">

                                @auth('customer')
                                    @if ($addresses->isNotEmpty())
                                        <div class="ec-checkout-wrap margin-bottom-30">
                                            <div class="ec-checkout-block">
                                                <h3 class="ec-checkout-title">Shipping Address</h3>
                                                <div class="ec-check-block-content">
                                                    @foreach ($addresses as $address)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="address_id"
                                                                id="addr_{{ $address->id }}" value="{{ $address->id }}"
                                                                {{ $address->is_default ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="addr_{{ $address->id }}">
                                                                <strong>{{ $address->label }}</strong> —
                                                                {{ $address->recipient_name }}, {{ $address->full_address }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="radio" name="address_id"
                                                            id="addr_new" value="">
                                                        <label class="form-check-label" for="addr_new">Use a new address</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    @if (! $allowGuestCheckout)
                                        <div class="alert alert-info">
                                            Please <a href="{{ route('visitor.login') }}">login</a> or
                                            <a href="{{ route('visitor.register') }}">register</a> to continue checkout.
                                        </div>
                                    @endif
                                @endauth

                                <div id="newAddressFields" class="ec-checkout-wrap margin-bottom-30 padding-bottom-3">
                                    <div class="ec-checkout-block">
                                        <h3 class="ec-checkout-title">Billing Details</h3>
                                        <div class="ec-check-bill-form">
                                            <span class="ec-bill-wrap">
                                                <label>Full Name*</label>
                                                <input type="text" name="shipping_name" value="{{ old('shipping_name') }}"
                                                    placeholder="Enter your full name" />
                                            </span>
                                            <span class="ec-bill-wrap">
                                                <label>Email*</label>
                                                <input type="email" name="shipping_email" value="{{ old('shipping_email', auth('customer')->user()?->email) }}"
                                                    placeholder="Enter your email" />
                                            </span>
                                            <span class="ec-bill-wrap">
                                                <label>Phone*</label>
                                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}"
                                                    placeholder="Enter your phone number" />
                                            </span>
                                            <span class="ec-bill-wrap">
                                                <label>Address*</label>
                                                <input type="text" name="shipping_address" value="{{ old('shipping_address') }}"
                                                    placeholder="Address Line" />
                                            </span>
                                            <span class="ec-bill-wrap ec-bill-half">
                                                <label>City*</label>
                                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" placeholder="City" />
                                            </span>
                                            <span class="ec-bill-wrap ec-bill-half">
                                                <label>State*</label>
                                                <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" placeholder="State" />
                                            </span>
                                            <span class="ec-bill-wrap ec-bill-half">
                                                <label>ZIP Code*</label>
                                                <input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" placeholder="ZIP Code" />
                                            </span>
                                            <span class="ec-bill-wrap ec-bill-half">
                                                <label>Country*</label>
                                                <input type="text" name="shipping_country" value="{{ old('shipping_country', 'Bangladesh') }}" placeholder="Country" />
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <span class="ec-check-order-btn">
                                    <button type="submit" class="btn btn-primary" id="placeOrderBtn"
                                        {{ (! auth('customer')->check() && ! $allowGuestCheckout) ? 'disabled' : '' }}>
                                        Place Order
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="ec-checkout-rightside col-lg-4 col-md-12">
                        <div class="ec-sidebar-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title"><h3 class="ec-sidebar-title">Summary</h3></div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-summary">
                                        <div><span class="text-left">Sub-Total</span><span class="text-right" id="summarySubtotal">${{ number_format($summary['subtotal'], 2) }}</span></div>
                                        <div><span class="text-left">Est. Shipping</span><span class="text-right">${{ number_format($summary['shipping_charge_estimate'], 2) }}</span></div>
                                        <div><span class="text-left">Est. Tax</span><span class="text-right">${{ number_format($summary['tax_amount_estimate'], 2) }}</span></div>
                                        <div>
                                            <span class="text-left">Coupon</span>
                                            <span class="text-right"><a class="ec-checkout-coupan" id="toggleCoupon">Apply Coupon</a></span>
                                        </div>
                                    </div>
                                    <div class="ec-checkout-coupan-content" style="display:none;" id="couponBox">
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control" id="couponCodeInput" placeholder="Enter coupon code">
                                            <button type="button" class="btn btn-sm btn-primary" id="applyCouponBtn">Apply</button>
                                        </div>
                                        <div id="couponMessage" class="small mt-2"></div>
                                        <input type="hidden" name="coupon_code" id="couponCodeHidden">
                                    </div>
                                    <div class="ec-checkout-summary-total">
                                        <span class="text-left">Est. Total</span>
                                        <span class="text-right" id="summaryTotal">
                                            ${{ number_format($summary['subtotal'] + $summary['shipping_charge_estimate'] + $summary['tax_amount_estimate'], 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ec-sidebar-wrap ec-checkout-pay-wrap">
                            <div class="ec-sidebar-block">
                                <div class="ec-sb-title"><h3 class="ec-sidebar-title">Payment Method</h3></div>
                                <div class="ec-sb-block-content">
                                    <div class="ec-checkout-pay">
                                        <span class="ec-pay-option">
                                            <span>
                                                <input type="radio" id="pay_cod" name="payment_method" value="cod" checked>
                                                <label for="pay_cod">Cash On Delivery</label>
                                            </span>
                                        </span>
                                        <textarea name="notes" class="form-control mt-3" placeholder="Order notes (optional)">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-checkout.js'])
@endsection