@extends('admin.include.vertical', ['title' => 'Reviews'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Product Reviews'])

    <div class="row row-cols-md-4 row-cols-1 g-1 mb-2">
        <div class="col"><div class="card mb-1"><div class="card-body">
            <h3 class="mb-1">{{ $stats['total'] }}</h3><p class="mb-0 text-uppercase fs-xs fw-bold">Total Reviews</p>
        </div></div></div>
        <div class="col"><div class="card mb-1"><div class="card-body">
            <h3 class="mb-1 text-warning">{{ $stats['pending'] }}</h3><p class="mb-0 text-uppercase fs-xs fw-bold">Pending</p>
        </div></div></div>
        <div class="col"><div class="card mb-1"><div class="card-body">
            <h3 class="mb-1 text-success">{{ $stats['approved'] }}</h3><p class="mb-0 text-uppercase fs-xs fw-bold">Approved</p>
        </div></div></div>
        <div class="col"><div class="card mb-1"><div class="card-body">
            <h3 class="mb-1 text-danger">{{ $stats['rejected'] }}</h3><p class="mb-0 text-uppercase fs-xs fw-bold">Rejected</p>
        </div></div></div>
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

    <form method="GET" action="{{ route('admin.ecommerce.review.index') }}" id="filterForm">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex gap-2">
                    <div class="app-search">
                        <input class="form-control" name="search" placeholder="Search product or customer..."
                            type="search" value="{{ request('search') }}" id="searchInput" />
                        <i class="app-search-icon text-muted" data-lucide="search"></i>
                    </div>
                    @admincan('review.delete')
                        <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                            <i class="fs-sm me-1" data-lucide="trash-2"></i> Delete Selected
                        </button>
                    @endadmincan
                </div>
                <div class="d-flex gap-2">
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" name="status" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="activity"></i>
                    </div>
                    <div class="app-search">
                        <select class="form-select form-control my-1 my-md-0" name="rating" id="ratingFilter">
                            <option value="">All Ratings</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        <i class="app-search-icon text-muted" data-lucide="star"></i>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th class="ps-3" style="width:1%"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th style="width:22rem">Review</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-3"><input type="checkbox" class="form-check-input row-checkbox" value="{{ $review->id }}"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($review->product?->thumbnail)
                                            <img src="{{ Storage::url($review->product->thumbnail) }}" class="avatar-sm rounded me-2" alt="">
                                        @endif
                                        <a class="link-reset fw-semibold" href="{{ $review->product ? route('admin.ecommerce.product.show', $review->product) : '#' }}">
                                            {{ $review->product?->name ?? 'Deleted Product' }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="fs-sm mb-0">{{ $review->user?->name ?? 'Deleted User' }}</h5>
                                    <p class="text-muted fs-xxs mb-0">{{ $review->user?->email }}</p>
                                </td>
                                <td>
                                    <span class="text-warning">
                                        @for ($i = 0; $i < $review->rating; $i++)<i data-lucide="star" style="width:14px;height:14px" class="fill-current"></i>@endfor
                                    </span>
                                </td>
                                <td class="text-truncate" style="max-width:22rem">{{ $review->comment }}</td>
                                <td>{{ $review->created_at->format('d M, Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match ($review->status) {
                                            'approved' => 'badge-soft-success',
                                            'rejected' => 'badge-soft-danger',
                                            default    => 'badge-soft-warning',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} fs-xxs">{{ ucfirst($review->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @admincan('review.approve')
                                            @if ($review->status !== 'approved')
                                                <form action="{{ route('admin.ecommerce.review.approve', $review) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle text-success" title="Approve">
                                                        <i class="fs-lg" data-lucide="check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($review->status !== 'rejected')
                                                <form action="{{ route('admin.ecommerce.review.reject', $review) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-default btn-icon btn-sm rounded-circle text-danger" title="Reject">
                                                        <i class="fs-lg" data-lucide="x"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endadmincan
                                        @admincan('review.delete')
                                            <form action="{{ route('admin.ecommerce.review.destroy', $review) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this review?');">
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
                            <tr><td colspan="8" class="text-center text-muted py-4">No reviews found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reviews->hasPages())
                <div class="card-footer border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }}</div>
                        {{ $reviews->links() }}
                    </div>
                </div>
            @endif
        </div>
    </form>

    @admincan('review.delete')
        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.review.bulk-destroy') }}" method="POST" class="d-none">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulkDeleteIds">
        </form>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-review.js'])
@endsection