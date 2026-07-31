@extends('visitor.layout.app', ['title' => 'Order History', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12"><h2 class="ec-breadcrumb-title">Order History</h2></div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Order History</li>
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
                    <div class="ec-vendor-dashboard-card">
                        <div class="ec-vendor-card-header">
                            <h5>Order History</h5>
                            <div class="ec-header-btn">
                                <a class="btn btn-lg btn-primary" href="{{ route('visitor.products.index') }}">Shop Now</a>
                            </div>
                        </div>
                        <div class="ec-vendor-card-body">
                            <div class="ec-vendor-card-table">
                                <table class="table ec-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order #</th>
                                            <th scope="col">Items</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $order)
                                            <tr>
                                                <th scope="row"><span>{{ $order->order_number }}</span></th>
                                                <td><span>{{ $order->items_count }} item(s)</span></td>
                                                <td><span>{{ $order->created_at->format('d M Y') }}</span></td>
                                                <td><span>${{ number_format((float) $order->total_amount, 2) }}</span></td>
                                                <td><span>{{ ucfirst($order->status) }}</span></td>
                                                <td>
                                                    <span class="tbl-btn">
                                                        <a class="btn btn-lg btn-primary" href="{{ route('visitor.account.orders.show', $order) }}">View</a>
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">You haven't placed any orders yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($orders->hasPages())
                                <div class="mt-3">{{ $orders->links() }}</div>
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