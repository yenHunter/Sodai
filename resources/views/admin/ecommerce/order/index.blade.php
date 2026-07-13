@extends('admin.include.vertical', ['title' => 'Orders'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Orders'])

    <div class="row">
        <div class="col-12">

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

            <form method="GET" action="{{ route('admin.ecommerce.order.index') }}" id="filterForm">
                <div class="card">
                    <div class="card-header border-light justify-content-between">
                        <div class="d-flex gap-2">
                            <div class="app-search">
                                <input class="form-control" name="search" placeholder="Search order #, customer..."
                                    type="search" value="{{ request('search') }}" id="searchInput" />
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="me-2 fw-semibold">Filter By:</span>
                            <div class="app-search">
                                <select class="form-select form-control my-1 my-md-0" name="status" id="statusFilter">
                                    <option value="">All Status</option>
                                    @foreach (\App\Models\Order::STATUSES as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="app-search-icon text-muted" data-lucide="activity"></i>
                            </div>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" id="dateFromFilter">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" id="dateToFilter">
                        </div>
                        <div>
                            @admincan('order.create')
                                <a class="btn btn-primary" href="{{ route('admin.ecommerce.order.create') }}">
                                    <i class="fs-sm me-2" data-lucide="plus"></i>
                                    New Order
                                </a>
                            @endadmincan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 1%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <a class="link-reset fw-semibold"
                                                href="{{ route('admin.ecommerce.order.show', $order) }}">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $order->created_at->format('d M, Y') }}
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <h5 class="text-nowrap fs-sm mb-0 lh-base">{{ $order->user?->name ?? '—' }}</h5>
                                            <p class="text-muted fs-xs mb-0">{{ $order->shipping_email }}</p>
                                        </td>
                                        <td>{{ $order->items_count }}</td>
                                        <td>${{ number_format((float) $order->total_amount, 2) }}</td>
                                        <td>
                                            @admincan('order.update-status')
                                                <form action="{{ route('admin.ecommerce.order.status.update', $order) }}"
                                                    method="POST" class="d-inline status-update-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm badge-select {{ $order->status_badge_class }}"
                                                        {{ !$order->isCancellable() && $order->status !== 'cancelled' ? '' : '' }}>
                                                        @foreach (\App\Models\Order::STATUSES as $status)
                                                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                                {{ ucfirst($status) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @else
                                                <span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span>
                                            @endadmincan
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    href="{{ route('admin.ecommerce.order.show', $order) }}" title="View">
                                                    <i class="fs-lg" data-lucide="eye"></i>
                                                </a>
                                                @admincan('order.edit')
                                                    @if ($order->isEditable())
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                            href="{{ route('admin.ecommerce.order.edit', $order) }}" title="Edit">
                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                        </a>
                                                    @endif
                                                @endadmincan
                                                @admincan('order.delete')
                                                    @if ($order->isDeletable())
                                                        <form action="{{ route('admin.ecommerce.order.destroy', $order) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this order permanently?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle" title="Delete">
                                                                <i class="fs-lg" data-lucide="trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endadmincan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="card-footer border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing <span class="fw-semibold">{{ $orders->firstItem() }}</span> to
                                    <span class="fw-semibold">{{ $orders->lastItem() }}</span> of
                                    <span class="fw-semibold">{{ $orders->total() }}</span> orders
                                </div>
                                {{ $orders->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-order-index.js'])
@endsection