@extends('visitor.layout.app', ['title' => 'Track Order', 'bodyClass' => 'track_order_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Track Order</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Track Order</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Track Your Order</h5>
                        </div>
                        <div class="ec-vendor-card-body">
                            <p class="text-muted mb-4">
                                Enter the order number from your confirmation page and the email address used at
                                checkout.
                            </p>

                            <form id="trackOrderForm" action="{{ route('visitor.track-order.lookup') }}" method="post">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="trackOrderNumber" class="form-label">Order Number*</label>
                                        <input type="text" class="form-control" id="trackOrderNumber"
                                            name="order_number" value="{{ old('order_number') }}"
                                            placeholder="e.g. ORD-000123" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="trackOrderEmail" class="form-label">Email*</label>
                                        <input type="email" class="form-control" id="trackOrderEmail" name="email"
                                            value="{{ old('email') }}" placeholder="Email used at checkout" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-4" id="trackOrderBtn">Track Order</button>
                            </form>
                        </div>
                    </div>

                    @isset($order)
                        <div id="trackOrderResult" class="ec-vendor-dashboard-card mt-4">
                            <div class="ec-vendor-card-header d-flex justify-content-between align-items-center gap-2">
                                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                                <span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="ec-vendor-card-body">
                                <p class="mb-1">Placed on {{ $order->created_at->format('d M Y') }}</p>
                                @if ($order->tracking_number)
                                    <p class="mb-1">Tracking number: <strong>{{ $order->tracking_number }}</strong></p>
                                @endif

                                @if (in_array($order->status, ['cancelled', 'refunded'], true))
                                    <div class="alert alert-secondary mt-3 mb-0">
                                        This order was {{ $order->status }} and is no longer in progress.
                                    </div>
                                @endif

                                <div class="ec-vendor-card-table mt-3">
                                    <table class="table ec-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
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
                            </div>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
