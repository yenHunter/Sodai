@extends('visitor.layout.app', ['title' => 'Order #' . $order->order_number, 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Order #{{ $order->order_number }}</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.account.orders.index') }}">Order
                                        History</a></li>
                                <li class="ec-breadcrumb-item active">#{{ $order->order_number }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ec Track Order section -->
    <section class="ec-page-content section-space-p pb-0">
        <div class="container">
            <div class="ec-trackorder-content col-md-12">
                <div class="ec-trackorder-inner">
                    <div class="ec-trackorder-top">
                        <h2 class="ec-order-id">Order #{{ $order->order_number }}</h2>
                        <div class="ec-order-detail">
                            <div>Ordered on {{ $order->created_at->format('d M Y') }}</div>
                            @if ($order->tracking_number)
                                <div>Tracking <span>{{ $order->tracking_number }}</span></div>
                            @endif
                        </div>
                    </div>

                    @if (in_array($order->status, ['cancelled', 'refunded']))
                        <div class="ec-trackorder-bottom">
                            <div
                                class="alert {{ $order->status === 'cancelled' ? 'alert-danger' : 'alert-secondary' }} mb-0">
                                This order has been <strong>{{ $order->status }}</strong>
                                @if ($order->status === 'cancelled' && $order->cancel_reason)
                                    — {{ $order->cancel_reason }}
                                @endif
                                and is no longer in progress.
                            </div>
                        </div>
                    @else
                        <div class="ec-trackorder-bottom">
                            <div class="ec-progress-track">
                                <ul id="ec-progressbar">
                                    @foreach ($trackingSteps as $step)
                                        <li class="step0 {{ $step['active'] ? 'active' : '' }}">
                                            <span class="ec-track-icon">
                                                <img src="{{ asset('visitor/images/icons/track_' . $step['icon'] . '.png') }}"
                                                    alt="{{ $step['label'] }}">
                                            </span>
                                            <span class="ec-progressbar-track"></span>
                                            <span class="ec-track-title">{{ $step['label'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- End Track Order section -->
    
    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                @include('visitor.partials.account-sidebar')

                <div class="ec-shop-rightside col-lg-9 col-md-12">
                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Order #{{ $order->order_number }} — {{ ucfirst($order->status) }}</h5>
                        </div>
                        <div class="ec-vendor-card-body">
                            <div class="ec-vendor-card-table">
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
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product_image)
                                                            <img class="prod-img me-2"
                                                                src="{{ Storage::url($item->product_image) }}"
                                                                alt="">
                                                        @endif
                                                        {{ $item->product_name }}
                                                    </div>
                                                </td>
                                                <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format((float) $item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6>Shipping Address</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $order->shipping_name }}<br>
                                        {{ $order->shipping_address }}, {{ $order->shipping_city }},
                                        {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                        {{ $order->shipping_country }}<br>
                                        {{ $order->shipping_phone }}
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-1">Subtotal: ${{ number_format((float) $order->subtotal, 2) }}</p>
                                    <p class="mb-1">Discount: -${{ number_format((float) $order->discount_amount, 2) }}
                                    </p>
                                    <p class="mb-1">Shipping: ${{ number_format((float) $order->shipping_charge, 2) }}
                                    </p>
                                    <p class="mb-1">Tax: ${{ number_format((float) $order->tax_amount, 2) }}</p>
                                    <h5>Total: ${{ number_format((float) $order->total_amount, 2) }}</h5>
                                </div>
                            </div>

                            @if ($order->statusHistories->isNotEmpty())
                                <hr>
                                <h6>Order Timeline</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($order->statusHistories as $history)
                                        <li class="mb-1">
                                            <strong>{{ ucfirst($history->to_status) }}</strong>
                                            — {{ $history->created_at->format('d M Y, h:i A') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
