@extends('admin.include.vertical', ['title' => 'Abandoned Carts'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Abandoned Carts'])

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

    <div class="row">
        <div class="col-12">
            <form method="GET" action="{{ route('admin.ecommerce.cart.index') }}" id="filterForm">
                <div class="card">
                    <div class="card-header border-light justify-content-between">
                        <div class="d-flex gap-2">
                            <div class="app-search">
                                <input class="form-control" name="search" placeholder="Search customer name/email..."
                                    type="search" value="{{ request('search') }}" id="searchInput" />
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                            </div>
                        </div>
                        <div>
                            @admincan('cart.delete')
                                <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                    <i class="fs-sm me-1" data-lucide="trash-2"></i> Delete Selected
                                </button>
                            @endadmincan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th class="ps-3" style="width:1%">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                    </th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Cart Value</th>
                                    <th>Last Updated</th>
                                    <th class="text-center" style="width:1%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carts as $cart)
                                    <tr>
                                        <td class="ps-3">
                                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $cart->id }}">
                                        </td>
                                        <td>
                                            @if ($cart->user)
                                                <h5 class="fs-sm mb-0">{{ $cart->user->name }}</h5>
                                                <p class="text-muted fs-xxs mb-0">{{ $cart->user->email }}</p>
                                            @else
                                                <span class="badge bg-secondary">Guest</span>
                                            @endif
                                        </td>
                                        <td>{{ $cart->items_count }}</td>
                                        <td>${{ number_format($cart->total_value, 2) }}</td>
                                        <td>
                                            {{ $cart->updated_at->format('d M, Y') }}
                                            <small class="text-muted">{{ $cart->updated_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle view-cart-btn"
                                                    data-id="{{ $cart->id }}" data-url="{{ route('admin.ecommerce.cart.show', $cart) }}" title="View">
                                                    <i class="fs-lg" data-lucide="eye"></i>
                                                </button>
                                                @if ($cart->user)
                                                    <form action="{{ route('admin.ecommerce.cart.send-reminder', $cart) }}" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Send a reminder email to this customer?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle" title="Send Reminder">
                                                            <i class="fs-lg" data-lucide="mail"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @admincan('cart.delete')
                                                    <form action="{{ route('admin.ecommerce.cart.destroy', $cart) }}" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Delete this cart?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle" title="Delete">
                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endadmincan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No abandoned carts found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($carts->hasPages())
                        <div class="card-footer border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">Showing {{ $carts->firstItem() }}–{{ $carts->lastItem() }} of {{ $carts->total() }}</div>
                                {{ $carts->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- BULK DELETE FORM (hidden, submitted via JS) --}}
    @admincan('cart.delete')
        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.cart.bulk-destroy') }}" method="POST" class="d-none">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulkDeleteIds">
        </form>
    @endadmincan

    {{-- DETAIL MODAL --}}
    <div class="modal fade" id="cartDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i data-lucide="shopping-cart" class="me-2"></i>Cart Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong id="modalCustomerName">—</strong>
                        <p class="text-muted mb-0 small" id="modalCustomerEmail"></p>
                        <p class="text-muted mb-0 small">Last updated: <span id="modalUpdatedAt">—</span></p>
                    </div>
                    <table class="table table-sm table-custom mb-0">
                        <thead class="bg-light">
                            <tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody id="modalItemsBody"></tbody>
                        <tfoot>
                            <tr class="border-top">
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold" id="modalTotal">$0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-cart.js'])
@endsection