@extends('admin.include.vertical', ['title' => 'Refunds'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Refunds'])

    <div class="row row-cols-md-4 row-cols-1 g-1 mb-2">
        <div class="col">
            <div class="card mb-1"><div class="card-body">
                <h3 class="mb-1">{{ $stats['total'] }}</h3>
                <p class="mb-0 text-uppercase fs-xs fw-bold">Total Refunds</p>
            </div></div>
        </div>
        <div class="col">
            <div class="card mb-1"><div class="card-body">
                <h3 class="mb-1 text-warning">{{ $stats['pending'] }}</h3>
                <p class="mb-0 text-uppercase fs-xs fw-bold">Pending</p>
            </div></div>
        </div>
        <div class="col">
            <div class="card mb-1"><div class="card-body">
                <h3 class="mb-1 text-success">{{ $stats['approved'] }}</h3>
                <p class="mb-0 text-uppercase fs-xs fw-bold">Approved</p>
            </div></div>
        </div>
        <div class="col">
            <div class="card mb-1"><div class="card-body">
                <h3 class="mb-1 text-danger">{{ $stats['rejected'] }}</h3>
                <p class="mb-0 text-uppercase fs-xs fw-bold">Rejected</p>
            </div></div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.ecommerce.refund.index') }}" id="filterForm">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex gap-2">
                    <div class="app-search">
                        <input class="form-control" name="search" placeholder="Search refund #, order #, customer..."
                            type="search" value="{{ request('search') }}" id="searchInput" />
                        <i class="app-search-icon text-muted" data-lucide="search"></i>
                    </div>
                    @admincan('refund.delete')
                        <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                            <i class="fs-sm me-1" data-lucide="trash-2"></i> Delete Selected
                        </button>
                    @endadmincan
                </div>
                <div class="app-search">
                    <select class="form-select form-control my-1 my-md-0" name="status" id="statusFilter">
                        <option value="">All Status</option>
                        @foreach (\App\Models\Refund::STATUSES as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <i class="app-search-icon text-muted" data-lucide="activity"></i>
                </div>
                <div>
                    @admincan('refund.create')
                        <button class="btn btn-primary" type="button" id="addRefundBtn" data-bs-toggle="modal" data-bs-target="#refundModal">
                            <i class="fs-sm me-2" data-lucide="plus"></i> New Refund
                        </button>
                    @endadmincan
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th class="ps-3" style="width:1%"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                            <th>Refund #</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td class="ps-3"><input type="checkbox" class="form-check-input row-checkbox" value="{{ $refund->id }}"></td>
                                <td class="fw-semibold">{{ $refund->refund_number }}</td>
                                <td>
                                    <a class="link-reset text-decoration-underline" href="{{ route('admin.ecommerce.order.show', $refund->order_id) }}">
                                        #{{ $refund->order?->order_number ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $refund->user?->name ?? $refund->order?->shipping_name ?? '—' }}</td>
                                <td>${{ number_format((float) $refund->amount, 2) }}</td>
                                <td><span class="badge {{ $refund->status_badge_class }} fs-xxs">{{ ucfirst($refund->status) }}</span></td>
                                <td>{{ $refund->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @admincan('refund.approve')
                                            @if ($refund->status === 'pending')
                                                <form action="{{ route('admin.ecommerce.refund.approve', $refund) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Approve this refund? This will mark the order as refunded and restore stock.');">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle text-success" title="Approve">
                                                        <i class="fs-lg" data-lucide="check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.ecommerce.refund.reject', $refund) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Reject this refund?');">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle text-danger" title="Reject">
                                                        <i class="fs-lg" data-lucide="x"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endadmincan
                                        @admincan('refund.edit')
                                            @if ($refund->isEditable())
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle" data-bs-toggle="modal"
                                                    data-bs-target="#refundModal" data-mode="edit" data-id="{{ $refund->id }}"
                                                    data-order-id="{{ $refund->order_id }}" data-amount="{{ $refund->amount }}"
                                                    data-reason="{{ $refund->reason }}"
                                                    data-update-url="{{ route('admin.ecommerce.refund.update', $refund) }}" title="Edit">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endif
                                        @endadmincan
                                        @admincan('refund.delete')
                                            @if ($refund->isDeletable())
                                                <form action="{{ route('admin.ecommerce.refund.destroy', $refund) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this refund?');">
                                                    @csrf @method('DELETE')
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
                            <tr><td colspan="8" class="text-center text-muted py-4">No refunds found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($refunds->hasPages())
                <div class="card-footer border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">Showing {{ $refunds->firstItem() }}–{{ $refunds->lastItem() }} of {{ $refunds->total() }}</div>
                        {{ $refunds->links() }}
                    </div>
                </div>
            @endif
        </div>
    </form>

    {{-- BULK DELETE FORM --}}
    @admincan('refund.delete')
        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.refund.bulk-destroy') }}" method="POST" class="d-none">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulkDeleteIds">
        </form>
    @endadmincan

    {{-- ADD/EDIT MODAL --}}
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">New Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="refundForm" method="POST" data-store-url="{{ route('admin.ecommerce.refund.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Order <span class="text-danger">*</span></label>
                            <select class="form-select" id="refundOrderId" name="order_id" required>
                                <option value="">Select Order</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">#{{ $order->order_number }} — ${{ number_format((float) $order->total_amount, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Refund Amount ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="refundAmount" name="amount" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="refundReason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="refundSubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i> Create Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-refund.js'])
@endsection