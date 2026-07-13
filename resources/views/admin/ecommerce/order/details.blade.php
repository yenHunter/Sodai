@extends('admin.include.vertical', ['title' => 'Order Details'])

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Order #' . $order->order_number])
    </div>

    <div class="d-flex justify-content-end gap-2 mb-2">
        @admincan('order.edit')
            @if ($order->isEditable())
                <a class="btn btn-sm btn-light" href="{{ route('admin.ecommerce.order.edit', $order) }}">
                    <i class="fs-sm me-1" data-lucide="pencil"></i> Edit
                </a>
            @endif
        @endadmincan
        <a class="btn btn-sm btn-light" href="{{ route('admin.ecommerce.order.index') }}">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Orders
        </a>
    </div>

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

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Items</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-centered align-middle mb-0">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($item->product_image)
                                                <img src="{{ Storage::url($item->product_image) }}" class="avatar-md rounded me-2" alt="">
                                            @endif
                                            <div>
                                                <h5 class="mb-0 fs-sm">{{ $item->product_name }}</h5>
                                                <small class="text-muted">{{ $item->product_sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format((float) $item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <table class="table table-borderless table-sm mb-0" style="max-width: 320px">
                            <tr><td class="text-muted">Subtotal</td><td class="text-end">${{ number_format((float) $order->subtotal, 2) }}</td></tr>
                            <tr><td class="text-muted">Discount</td><td class="text-end text-danger">-${{ number_format((float) $order->discount_amount, 2) }}</td></tr>
                            <tr><td class="text-muted">Shipping</td><td class="text-end">${{ number_format((float) $order->shipping_charge, 2) }}</td></tr>
                            <tr><td class="text-muted">Tax</td><td class="text-end">${{ number_format((float) $order->tax_amount, 2) }}</td></tr>
                            <tr class="border-top"><td class="fw-bold">Total</td><td class="text-end fw-bold">${{ number_format((float) $order->total_amount, 2) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Status</h4>
                </div>
                <div class="card-body">
                    <span class="badge {{ $order->status_badge_class }} fs-base px-3 py-2 mb-3">{{ ucfirst($order->status) }}</span>

                    @admincan('order.update-status')
                        <form action="{{ route('admin.ecommerce.order.status.update', $order) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-2">
                                @foreach (\App\Models\Order::STATUSES as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary w-100" type="submit">Update Status</button>
                        </form>
                    @endadmincan

                    @admincan('order.cancel')
                        @if ($order->isCancellable())
                            <form action="{{ route('admin.ecommerce.order.cancel', $order) }}" method="POST"
                                onsubmit="return confirm('Cancel this order and restore stock?');">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-danger w-100" type="submit">Cancel Order</button>
                            </form>
                        @endif
                    @endadmincan
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Customer & Shipping</h4>
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-semibold">{{ $order->shipping_name }}</p>
                    <p class="text-muted mb-1">{{ $order->shipping_email }} · {{ $order->shipping_phone }}</p>
                    <p class="text-muted mb-0">
                        {{ $order->shipping_address }}, {{ $order->shipping_city }},
                        {{ $order->shipping_state }} {{ $order->shipping_zip }}, {{ $order->shipping_country }}
                    </p>
                    @if ($order->notes)
                        <hr>
                        <p class="text-muted fst-italic mb-0">{{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection