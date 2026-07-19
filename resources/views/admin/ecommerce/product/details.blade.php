@extends('admin.include.vertical', ['title' => 'Product Details'])

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        @include('admin.include.partials.page-title', [
            'subtitle' => 'Ecommerce',
            'title' => 'Product Details',
        ])
    </div>

    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-sm btn-light" href="{{ route('admin.ecommerce.product.index') }}">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Products
        </a>
    </div>

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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        {{-- ═══════════════════════════════════════════
                             LEFT COLUMN: Images & Quick Actions
                        ═══════════════════════════════════════════ --}}
                        <div class="col-xl-4">
                            <div class="card card-top-sticky border-0">
                                <div class="card-body p-0">

                                    @php
                                        $galleryImages = $product->images;
                                        $hasGallery = $galleryImages->isNotEmpty();
                                    @endphp

                                    @if ($hasGallery)
                                        <div class="carousel slide carousel-fade" data-bs-ride="carousel"
                                            id="productImagesCarousel">
                                            <div class="carousel-inner" role="listbox">
                                                @foreach ($galleryImages as $index => $image)
                                                    <div
                                                        class="carousel-item text-center {{ $index === 0 ? 'active' : '' }}">
                                                        <img alt="{{ $product->name }}" class="img-fluid"
                                                            src="{{ Storage::url($image->image_path) }}" />
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if ($galleryImages->count() > 1)
                                                <div
                                                    class="carousel-indicators m-0 mt-3 d-lg-flex d-none position-static h-100 rounded gap-2">
                                                    @foreach ($galleryImages as $index => $image)
                                                        <button aria-label="Slide {{ $index + 1 }}"
                                                            class="h-auto rounded bg-light-subtle border {{ $index === 0 ? 'active' : '' }}"
                                                            data-bs-slide-to="{{ $index }}"
                                                            data-bs-target="#productImagesCarousel"
                                                            style="width: auto !important" type="button">
                                                            <img alt="thumb" class="d-block avatar-xl"
                                                                src="{{ Storage::url($image->image_path) }}" />
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @elseif ($product->thumbnail)
                                        <div class="text-center">
                                            <img alt="{{ $product->name }}" class="img-fluid"
                                                src="{{ Storage::url($product->thumbnail) }}" />
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                            style="height: 320px">
                                            <i class="text-muted" data-lucide="image" style="width: 64px; height: 64px"></i>
                                        </div>
                                    @endif

                                    <div class="text-center my-3">
                                        @admincan('product.edit')
                                            <a class="btn btn-light me-1"
                                                href="{{ route('admin.ecommerce.product.edit', $product) }}">
                                                <i class="fs-lg me-1" data-lucide="pencil"></i>
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.ecommerce.product.toggle-status', $product) }}"
                                                class="d-inline" id="toggleStatusForm" method="POST"
                                                data-activating="{{ $product->is_active ? '0' : '1' }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="btn {{ $product->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                    type="submit">
                                                    <i class="fs-lg me-1"
                                                        data-lucide="{{ $product->is_active ? 'circle-x' : 'circle-check' }}"></i>
                                                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endadmincan

                                        @admincan('product.delete')
                                            <button class="btn btn-danger" data-bs-target="#deleteProductModal"
                                                data-bs-toggle="modal" type="button">
                                                <i class="fs-lg me-1" data-lucide="trash-2"></i>
                                                Delete
                                            </button>
                                        @endadmincan
                                    </div>

                                    @admincan('product.edit')
                                        <div class="border-top pt-3 mt-1 d-flex align-items-center gap-2 flex-wrap"
                                            id="quickStockUpdate"
                                            data-update-url="{{ route('admin.ecommerce.product.stock.update', $product) }}">
                                            @csrf
                                            <label class="form-label mb-0 fw-semibold small" for="quickStockInput">
                                                Quick Stock Update:
                                            </label>
                                            <input class="form-control form-control-sm" id="quickStockInput" min="0"
                                                style="width: 90px" type="number" value="{{ $product->stock_quantity }}">
                                            <button class="btn btn-sm btn-primary" id="quickStockUpdateBtn" type="button">
                                                <i class="fs-xs me-1" data-lucide="refresh-cw"></i>Update
                                            </button>
                                            <span class="small" id="quickStockUpdateStatus"></span>
                                        </div>
                                    @endadmincan

                                </div>
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════
                             RIGHT COLUMN: Product Info
                        ═══════════════════════════════════════════ --}}
                        <div class="col-xl-8">
                            <div class="p-4">

                                @php
                                    $stockBadgeClass = match (true) {
                                        $product->is_out_of_stock => 'bg-danger-subtle text-danger',
                                        $product->is_low_stock => 'bg-warning-subtle text-warning',
                                        default => 'bg-success-subtle text-success',
                                    };
                                @endphp

                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <span class="badge {{ $stockBadgeClass }} px-2 py-1 fs-base rounded-pill"
                                            id="stockStatusBadge">
                                            {{ $product->stock_status }}
                                        </span>
                                        @if (!$product->is_active)
                                            <span
                                                class="badge bg-secondary px-2 py-1 fs-base rounded-pill ms-1">Inactive</span>
                                        @endif
                                        @if ($product->is_featured)
                                            <span
                                                class="badge bg-primary-subtle text-primary px-2 py-1 fs-base rounded-pill ms-1">
                                                <i data-lucide="star" style="width:14px;height:14px"
                                                    class="fill-current"></i>
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 d-inline-flex align-items-center justify-content-end fs-lg">
                                        @php $fullStars = (int) floor((float) $product->average_rating); @endphp
                                        <span class="text-warning">
                                            @for ($i = 0; $i < 5; $i++)
                                                <i @if ($i < $fullStars) class="fill-current" @endif
                                                    data-lucide="star"></i>
                                            @endfor
                                        </span>
                                        <span class="ms-1 fs-base">({{ $product->review_count }} Reviews)</span>
                                    </div>
                                </div>

                                <div class="mt-3 mb-4">
                                    <h4 class="fs-xl">{{ $product->name }}</h4>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">SKU:</h6>
                                        <p class="fw-medium mb-0">{{ $product->sku }}</p>
                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Category:</h6>
                                        <p class="fw-medium mb-0">{{ $product->category?->full_name ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Brand:</h6>
                                        <p class="fw-medium mb-0">{{ $product->brand?->name ?? '—' }}</p>
                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Added On:</h6>
                                        <p class="fw-medium mb-0">
                                            {{ $product->created_at->format('d M Y') }}
                                            <small class="text-muted">{{ $product->created_at->format('h:i A') }}</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Stock Quantity:</h6>
                                        <p class="fw-medium mb-0" id="stockQuantityDisplay">
                                            {{ $product->stock_quantity }}</p>
                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Low Stock Alert:</h6>
                                        <p class="fw-medium mb-0">{{ $product->low_stock_threshold }}</p>
                                    </div>
                                    <div class="col-md-4 col-xl-3">
                                        <h6 class="mb-1 text-muted text-uppercase">Total Sales:</h6>
                                        <p class="fw-medium mb-0">{{ $product->total_sales }}</p>
                                    </div>
                                </div>

                                @if (
                                    ($product->weight && in_array('weight', $activeAttrs ?? [])) ||
                                        ($product->color && in_array('color', $activeAttrs ?? [])) ||
                                        ($product->size && in_array('size', $activeAttrs ?? [])))
                                    <div class="row mb-4">
                                        @if ($product->weight)
                                            <div class="col-md-4 col-xl-3">
                                                <h6 class="mb-1 text-muted text-uppercase">Weight:</h6>
                                                <p class="fw-medium mb-0">{{ $product->weight }}
                                                    {{ $product->weight_unit }}</p>
                                            </div>
                                        @endif
                                        @if ($product->color)
                                            <div class="col-md-4 col-xl-3">
                                                <h6 class="mb-1 text-muted text-uppercase">Color:</h6>
                                                <p class="fw-medium mb-0">{{ $product->color }}</p>
                                            </div>
                                        @endif
                                        @if ($product->size)
                                            <div class="col-md-4 col-xl-3">
                                                <h6 class="mb-1 text-muted text-uppercase">Size:</h6>
                                                <p class="fw-medium mb-0">{{ $product->size }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="mb-4">
                                    @if ($product->has_discount)
                                        <h3 class="text-muted d-flex align-items-center gap-2 mb-1">
                                            <small
                                                class="text-decoration-line-through">${{ number_format((float) $product->price, 2) }}</small>
                                            <span
                                                class="fw-bold text-danger">${{ number_format($product->final_price, 2) }}</span>
                                            <small>
                                                ({{ $product->discount_type === 'percentage'
                                                    ? number_format((float) $product->discount_value, 0) . '%'
                                                    : '$' . number_format((float) $product->discount_value, 2) }})
                                            </small>
                                        </h3>
                                        <p class="text-muted mb-0">
                                            Customer saves ${{ number_format($product->discount_amount, 2) }}
                                            ({{ number_format($product->discount_percentage, 1) }}%)
                                        </p>
                                    @else
                                        <h3 class="fw-bold mb-0">${{ number_format((float) $product->price, 2) }}</h3>
                                    @endif
                                    @if ($product->purchase_price)
                                        <p class="text-muted small mb-0">
                                            Purchase Price: ${{ number_format((float) $product->purchase_price, 2) }}
                                        </p>
                                    @endif
                                </div>

                                @if ($product->short_description)
                                    <div class="text-muted product-description-content">
                                        {!! $product->short_description !!}
                                    </div>
                                @endif

                                @if ($product->description)
                                    <h6 class="mt-3 fs-base">Description:</h6>
                                    {{-- Rich-text HTML authored by trusted admins via Quill --}}
                                    <div class="text-muted product-description-content">
                                        {!! $product->description !!}
                                    </div>
                                @endif

                                @if ($product->tags->isNotEmpty())
                                    <div class="mt-3">
                                        <h6 class="text-uppercase text-muted fs-xs mb-2">Tags:</h6>
                                        @foreach ($product->tags as $tag)
                                            <span
                                                class="badge bg-light text-dark border me-1 mb-1">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                @if (
                                    !empty($product->meta['meta_title']) ||
                                        !empty($product->meta['meta_description']) ||
                                        !empty($product->meta['meta_keywords']))
                                    <h6 class="mt-4 fs-base">SEO Meta:</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        @if (!empty($product->meta['meta_title']))
                                            <tr>
                                                <td class="text-muted fw-semibold" width="160">Meta Title</td>
                                                <td>{{ $product->meta['meta_title'] }}</td>
                                            </tr>
                                        @endif
                                        @if (!empty($product->meta['meta_description']))
                                            <tr>
                                                <td class="text-muted fw-semibold">Meta Description</td>
                                                <td>{{ $product->meta['meta_description'] }}</td>
                                            </tr>
                                        @endif
                                        @if (!empty($product->meta['meta_keywords']))
                                            <tr>
                                                <td class="text-muted fw-semibold">Meta Keywords</td>
                                                <td>{{ $product->meta['meta_keywords'] }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 RELATED PRODUCTS
            ═══════════════════════════════════════════ --}}
            @if ($product->relatedProducts->isNotEmpty())
                <div class="card mt-3 shadow-none border border-dashed">
                    <div class="card-header border-light">
                        <h4 class="card-title">Related Products</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($product->relatedProducts as $related)
                                <div class="col-md-3 col-6">
                                    <a class="text-decoration-none"
                                        href="{{ route('admin.ecommerce.product.show', $related) }}">
                                        <div class="border rounded p-2 text-center h-100">
                                            @if ($related->thumbnail)
                                                <img alt="{{ $related->name }}" class="img-fluid rounded mb-2"
                                                    src="{{ Storage::url($related->thumbnail) }}"
                                                    style="height: 80px; object-fit: cover" />
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-2 mx-auto"
                                                    style="width: 80px; height: 80px">
                                                    <i class="text-muted" data-lucide="image"></i>
                                                </div>
                                            @endif
                                            <p class="mb-0 small fw-semibold text-truncate text-body">{{ $related->name }}
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                ${{ number_format((float) $related->price, 2) }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════
                 REVIEWS
            ═══════════════════════════════════════════ --}}
            <div class="card mt-3 shadow-none border border-dashed">
                <div class="card-header border-light">
                    <h4 class="card-title">Customer Reviews</h4>
                </div>

                @if ($product->reviews->isNotEmpty())
                    <div class="card-header p-0 d-block">
                        <div class="d-flex align-items-center gap-3 p-4">
                            <h3 class="text-primary d-flex align-items-center gap-2 mb-0 fw-bold">
                                {{ number_format((float) $product->average_rating, 2) }}
                                <i data-lucide="star"></i>
                            </h3>
                            <p class="mb-0">
                                Based on {{ $product->review_count }} review{{ $product->review_count === 1 ? '' : 's' }}
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-centered table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Reviewer</th>
                                    <th style="width: 18rem">Review</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center gap-2">
                                                <div class="avatar avatar-sm">
                                                    @if ($review->user?->avatar)
                                                        <img alt="avatar" class="img-fluid rounded-circle"
                                                            src="{{ Storage::url($review->user->avatar) }}" />
                                                    @else
                                                        <span
                                                            class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                            {{ strtoupper(substr($review->user?->name ?? '?', 0, 1)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <h5 class="text-nowrap fs-sm mb-0 lh-base">
                                                    {{ $review->user?->name ?? 'Deleted User' }}
                                                </h5>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-warning fs-lg">
                                                @for ($i = 0; $i < $review->rating; $i++)
                                                    <i data-lucide="star"></i>
                                                @endfor
                                            </span>
                                            @if ($review->comment)
                                                <p class="text-muted fst-italic mb-0">"{{ $review->comment }}"</p>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $review->created_at->format('d M, Y') }}
                                            <small class="text-muted">{{ $review->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($review->status) {
                                                    'approved' => 'badge-soft-success',
                                                    'rejected' => 'badge-soft-danger',
                                                    default => 'badge-soft-warning',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $statusClass }} fs-xxs">{{ ucfirst($review->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center text-muted py-5">
                        <i class="mb-2" data-lucide="star-off" style="width: 32px; height: 32px"></i>
                        <p class="mb-0">No reviews yet for this product.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         DELETE MODAL
    ═══════════════════════════════════════════ --}}
    @admincan('product.delete')
        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="me-2" data-lucide="triangle-alert"></i>Delete Product
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($product->canDelete())
                            <p>Are you sure you want to delete <strong>{{ $product->name }}</strong>?</p>
                            <p class="text-danger small mb-0">
                                <i class="me-1" data-lucide="info"></i>
                                This action cannot be undone.
                            </p>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="me-2" data-lucide="triangle-alert"></i>
                                {{ $product->deletion_block_reason }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                        @if ($product->canDelete())
                            <form action="{{ route('admin.ecommerce.product.destroy', $product) }}" id="deleteProductForm"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" id="confirmDeleteBtn" type="submit">
                                    <i class="fs-sm me-1" data-lucide="trash-2"></i>Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-product-details.js'])
@endsection
