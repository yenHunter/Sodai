@extends('admin.include.vertical', ['title' => 'Products'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Products'])

    <div class="row">
        <div class="col-12">

            {{-- Session Messages --}}
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

            <form method="GET" action="{{ route('admin.ecommerce.product.index') }}" id="filterForm">
                <div class="card">
                    <div class="card-header border-light justify-content-between">
                        <div class="d-flex gap-2">
                            <div class="app-search">
                                <input class="form-control" name="search" placeholder="Search product..." type="search"
                                    value="{{ request('search') }}" id="searchInput" />
                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                            </div>
                            @admincan('product.delete')
                                <button class="btn btn-danger d-none" type="button" id="bulkDeleteBtn">
                                    Delete Selected
                                </button>
                            @endadmincan
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="me-2 fw-semibold">Filter By:</span>
                            <div class="app-search">
                                <select class="form-select form-control my-1 my-md-0" name="category_id"
                                    id="categoryFilter">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="app-search-icon text-muted" data-lucide="tag"></i>
                            </div>
                            <div class="app-search">
                                <select class="form-select form-control my-1 my-md-0" name="status" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                    <option value="out_of_stock"
                                        {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                                <i class="app-search-icon text-muted" data-lucide="activity"></i>
                            </div>
                        </div>
                        <div>
                            @admincan('product.create')
                                <a class="btn btn-primary" href="{{ route('admin.ecommerce.product.create') }}">
                                    <i class="fs-sm me-2" data-lucide="plus"></i>
                                    Add Product
                                </a>
                            @endadmincan
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-select table-hover w-100 mb-0"
                            id="productsTable">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th class="ps-3" style="width: 1%">
                                        <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"
                                            id="selectAllCheckbox" />
                                    </th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Orders</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 1%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr data-product-id="{{ $product->id }}">
                                        <td class="ps-3">
                                            <input class="form-check-input form-check-input-light fs-14 row-checkbox mt-0"
                                                type="checkbox" value="{{ $product->id }}" />
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="avatar-xl me-3">
                                                    @if ($product->thumbnail_url)
                                                        <img src="{{ $product->thumbnail_url }}"
                                                            alt="{{ $product->name }}" class="img-fluid rounded" />
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                            style="width:48px;height:48px">
                                                            <i data-lucide="image" class="text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a class="link-reset"
                                                            href="{{ route('admin.ecommerce.product.show', $product) }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h5>
                                                    @if ($product->brand)
                                                        <p class="text-muted mb-0 fs-xxs">
                                                            <small>by: {{ $product->brand->name }}</small>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->category?->name ?? '—' }}</td>
                                        <td>
                                            <h5 class="fs-base mb-0 fw-medium">{{ $product->total_stock }}</h5>
                                            @if ($product->has_variants)
                                                <small class="text-muted">{{ $product->variants_count }}
                                                    variant{{ $product->variants_count === 1 ? '' : 's' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $product->price_range_label }}</td>
                                        <td>{{ $product->total_sales }}</td>
                                        <td>
                                            @if ($product->review_count > 0)
                                                <span class="text-warning">
                                                    @php
                                                        $fullStars = floor($product->average_rating);
                                                        $hasHalfStar = $product->average_rating - $fullStars >= 0.5;
                                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                    @endphp
                                                    @for ($i = 0; $i < $fullStars; $i++)
                                                        <i data-lucide="star" class="fill-current"></i>
                                                    @endfor
                                                    @if ($hasHalfStar)
                                                        <i data-lucide="star-half" class="fill-current"></i>
                                                    @endif
                                                    @for ($i = 0; $i < $emptyStars; $i++)
                                                        <i data-lucide="star"></i>
                                                    @endfor
                                                </span>
                                                <span class="ms-1">
                                                    <a class="link-reset fw-semibold" href="#" title="Reviews">
                                                        ({{ $product->review_count }})
                                                    </a>
                                                </span>
                                            @else
                                                <span class="text-muted small">No reviews</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$product->is_active)
                                                <span class="badge badge-soft-danger fs-xxs">Inactive</span>
                                            @elseif ($product->total_stock <= 0)
                                                <span class="badge badge-soft-warning fs-xxs">Out of Stock</span>
                                            @else
                                                <span class="badge badge-soft-success fs-xxs">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    href="{{ route('admin.ecommerce.product.show', $product) }}"
                                                    title="View Details">
                                                    <i class="fs-lg" data-lucide="eye"></i>
                                                </a>
                                                @admincan('product.edit')
                                                    <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                        href="{{ route('admin.ecommerce.product.edit', $product) }}"
                                                        title="Edit">
                                                        <i class="fs-lg" data-lucide="square-pen"></i>
                                                    </a>
                                                @endadmincan
                                                @admincan('product.delete')
                                                    <button type="button"
                                                        class="btn btn-default btn-icon btn-sm rounded-circle delete-product-btn"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-delete-url="{{ route('admin.ecommerce.product.destroy', $product) }}"
                                                        title="Delete">
                                                        <i class="fs-lg" data-lucide="trash-2"></i>
                                                    </button>
                                                @endadmincan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            No products found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="card-footer border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing <span class="fw-semibold">{{ $products->firstItem() }}</span> to
                                    <span class="fw-semibold">{{ $products->lastItem() }}</span> of
                                    <span class="fw-semibold">{{ $products->total() }}</span> products
                                </div>
                                <div>
                                    <ul class="pagination pagination-sm pagination-boxed mb-0 justify-content-center">
                                        {{-- Previous --}}
                                        <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{ $products->previousPageUrl() }}" class="page-link">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 6l-6 6l6 6" />
                                                </svg>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                            <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                            </li>
                                        @endforeach

                                        {{-- Next --}}
                                        <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                                            <a href="{{ $products->nextPageUrl() }}" class="page-link">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @admincan('product.delete')
        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Confirm Deletion
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="deleteModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteSingleForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                                <i data-lucide="trash-2" class="fs-sm me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk Delete Modal --}}
        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Selected Products
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bulkDeleteMessage"></p>
                        <div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-2"></i>
                            This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.product.bulk-destroy') }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ids" id="bulkDeleteIds">
                            <button type="submit" class="btn btn-danger" id="confirmBulkDeleteBtn">
                                <i data-lucide="trash-2" class="fs-sm me-1"></i>Delete Selected
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-product-index.js'])
@endsection
