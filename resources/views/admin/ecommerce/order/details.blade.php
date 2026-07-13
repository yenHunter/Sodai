@extends('admin.include.vertical', ['title' => 'Order Details'])

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Order #' . $order->order_number])
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
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header align-items-start p-4">
                    <div>
                        <h3 class="mb-1 d-flex fs-xl align-items-center">Order #{{ $order->order_number }}</h3>
                        <p class="text-muted mb-3">
                            <i data-lucide="calendar"></i> {{ $order->created_at->format('d M, Y') }}
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </p>
                        <span class="badge {{ $order->status_badge_class }} fs-xxs badge-label">
                            <i class="fs-sm align-middle" data-lucide="circle"></i> {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="ms-auto">
                        @admincan('order.edit')
                            @if ($order->isEditable())
                                <a class="btn btn-light" href="{{ route('admin.ecommerce.order.edit', $order) }}">
                                    <i class="me-1" data-lucide="pencil"></i> Modify
                                </a>
                            @endif
                        @endadmincan
                        @admincan('order.delete')
                            @if ($order->isDeletable())
                                <form action="{{ route('admin.ecommerce.order.destroy', $order) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this order permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="me-1" data-lucide="trash-2"></i> Delete
                                    </button>
                                </form>
                            @endif
                        @endadmincan
                        <a class="btn btn-light" href="{{ route('admin.ecommerce.order.index') }}">
                            <i class="me-1" data-lucide="arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body px-4">
                    <h4 class="fs-sm mb-3">Order Summary</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-custom table-nowrap align-middle mb-1">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>QTY</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="avatar-md me-3">
                                                    @if ($item->product_image)
                                                        <img alt="{{ $item->product_name }}" class="img-fluid rounded"
                                                            src="{{ Storage::url($item->product_image) }}" />
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center h-100">
                                                            <i class="text-muted" data-lucide="image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">
                                                        @if ($item->product)
                                                            <a class="link-reset" href="{{ route('admin.ecommerce.product.show', $item->product) }}">
                                                                {{ $item->product_name }}
                                                            </a>
                                                        @else
                                                            {{ $item->product_name }}
                                                        @endif
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xxs">SKU: {{ $item->product_sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format((float) $item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-top">
                                    <td class="text-end fw-semibold" colspan="3">Subtotal</td>
                                    <td class="text-end">${{ number_format((float) $order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold" colspan="3">Discount</td>
                                    <td class="text-end text-danger fw-semibold">-${{ number_format((float) $order->discount_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold" colspan="3">Shipping fee</td>
                                    <td class="text-end">${{ number_format((float) $order->shipping_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold" colspan="3">Tax</td>
                                    <td class="text-end">${{ number_format((float) $order->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-end fw-bold text-uppercase" colspan="3">Grand Total</td>
                                    <td class="fw-bold text-end table-active">${{ number_format((float) $order->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if ($order->coupon_code)
                        <p class="text-muted small mb-0">Coupon applied: <span class="fw-semibold">{{ $order->coupon_code }}</span></p>
                    @endif
                    @if ($order->notes)
                        <div class="alert alert-light border mt-3 mb-0">
                            <strong>Notes:</strong> {{ $order->notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 ORDER TIMELINE (derived from real timestamps)
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Timeline</h4>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        @if ($order->status === 'cancelled')
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-time pe-3 text-muted">{{ $order->cancelled_at?->format('d M, h:i A') }}</div>
                                <div class="timeline-dot bg-danger"></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">Order Cancelled</h5>
                                    @if ($order->cancel_reason)
                                        <p class="mb-0 text-muted">{{ $order->cancel_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($order->delivered_at)
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-time pe-3 text-muted">{{ $order->delivered_at->format('d M, h:i A') }}</div>
                                <div class="timeline-dot bg-success"></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">Delivered</h5>
                                    <p class="mb-0 text-muted">Order was delivered to the customer.</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->shipped_at)
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-time pe-3 text-muted">{{ $order->shipped_at->format('d M, h:i A') }}</div>
                                <div class="timeline-dot bg-success"></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">Shipped</h5>
                                    <p class="mb-0 text-muted">Order left the warehouse for delivery.</p>
                                </div>
                            </div>
                        @endif

                        <div class="timeline-item d-flex align-items-stretch">
                            <div class="timeline-time pe-3 text-muted">{{ $order->created_at->format('d M, h:i A') }}</div>
                            <div class="timeline-dot bg-success"></div>
                            <div class="timeline-content ps-3">
                                <h5 class="mb-1">Order Placed</h5>
                                <p class="mb-0 text-muted">Order #{{ $order->order_number }} was created.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            {{-- ═══════════════════════════════════════════
                 CUSTOMER DETAILS
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title">Customer Details</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            @if ($order->user?->avatar)
                                <img alt="avatar" class="rounded-circle avatar-lg" src="{{ Storage::url($order->user->avatar) }}" />
                            @else
                                <span class="avatar-lg avatar-title bg-primary-subtle text-primary rounded-circle fs-xl">
                                    {{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $order->user?->name ?? 'Guest' }}</h5>
                            @if ($order->user)
                                <p class="text-muted mb-0">Since {{ $order->user->created_at->format('Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <ul class="list-unstyled text-muted mb-0">
                        <li class="mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs avatar-img-size fs-24">
                                    <span class="avatar-title text-bg-light fs-sm rounded-circle">
                                        <i data-lucide="mail"></i>
                                    </span>
                                </div>
                                <h5 class="fs-base mb-0 fw-medium">{{ $order->shipping_email }}</h5>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs avatar-img-size fs-24">
                                    <span class="avatar-title text-bg-light fs-sm rounded-circle">
                                        <i data-lucide="phone"></i>
                                    </span>
                                </div>
                                <h5 class="fs-base mb-0 fw-medium">{{ $order->shipping_phone }}</h5>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 SHIPPING ADDRESS
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title">Shipping Address</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">{{ $order->shipping_name }}</h5>
                    <p class="text-muted mb-1">
                        {{ $order->shipping_address }}<br />
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br />
                        {{ $order->shipping_country }}
                    </p>
                    <p class="mb-0 text-muted">
                        <strong>Phone:</strong> {{ $order->shipping_phone }}<br />
                        <strong>Email:</strong> {{ $order->shipping_email }}
                    </p>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 STATUS / CANCEL ACTIONS
            ═══════════════════════════════════════════ --}}
            <div class="card">
                <div class="card-header border-dashed">
                    <h4 class="card-title">Update Status</h4>
                </div>
                <div class="card-body">
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
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection