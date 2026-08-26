@extends('visitor.layout.app', ['title' => 'Thank You', 'bodyClass' => 'cart_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Thank You</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Thank You</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-thank-you-page section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ec-thank-you section-space-p">
                        <div class="ec-thank-content">
                            <i class="ecicon eci-check-circle" aria-hidden="true"></i>
                            <div class="section-title">
                                <h2 class="ec-title">Thank You</h2>
                                @if ($order)
                                    <p class="sub-title">Your order <strong>#{{ $order->order_number }}</strong> has been placed successfully.</p>
                                @else
                                    <p class="sub-title">For Shopping with us.</p>
                                @endif
                            </div>
                        </div>

                        @if ($order)
                            <div class="ec-vendor-dashboard-card mt-4">
                                <div class="ec-vendor-card-header">
                                    <h5>Order Summary</h5>
                                </div>
                                <div class="ec-vendor-card-body">
                                    <div class="ec-vendor-card-table">
                                        <table class="table ec-table">
                                            <thead>
                                                <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->items as $item)
                                                    <tr>
                                                        <td>{{ $item->product_name }}</td>
                                                        <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>${{ number_format((float) $item->total_price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-md-end mt-3">
                                        <p class="mb-1">Subtotal: ${{ number_format((float) $order->subtotal, 2) }}</p>
                                        <p class="mb-1">Discount: -${{ number_format((float) $order->discount_amount, 2) }}</p>
                                        <p class="mb-1">Shipping: ${{ number_format((float) $order->shipping_charge, 2) }}</p>
                                        <p class="mb-1">Tax: ${{ number_format((float) $order->tax_amount, 2) }}</p>
                                        <h5>Total: ${{ number_format((float) $order->total_amount, 2) }}</h5>
                                    </div>
                                    @auth('customer')
                                        <a href="{{ route('visitor.account.orders.show', $order) }}" class="btn btn-primary mt-2">Track Order</a>
                                    @endauth
                                </div>
                            </div>
                        @endif

                        <div class="ec-hunger">
                            <div class="ec-hunger-detial">
                                <h3>Still Hungry For Shopping?</h3>
                                <a href="{{ route('visitor.products.index') }}" class="btn btn-primary">Continue Shopping</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection