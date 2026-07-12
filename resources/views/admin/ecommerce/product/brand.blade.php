@extends('admin.include.vertical', ['title' => 'Brands'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', [
        'subtitle' => 'Ecommerce',
        'title' => 'Brands',
    ])

    <div class="row">
        <div class="col-12">

            {{-- Session Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="circle-check"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="triangle-alert"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="card-title mb-0">Brand List</h5>
                    <div>
                        @admincan('brand.delete')
                            <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                <i class="fs-sm me-1" data-lucide="trash-2"></i>
                                Delete Selected
                            </button>
                        @endadmincan
                        @admincan('brand.create')
                            <button class="btn btn-primary" type="button" id="addBrandBtn" data-bs-toggle="modal"
                                data-bs-target="#brandModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i>
                                Add Brand
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="brandTable"
                        class="table table-striped dt-responsive checkbox-select-datatable align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" />
                                </th>
                                <th width="60">Logo</th>
                                <th>Name</th>
                                <th>Website</th>
                                <th>Products</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr data-id="{{ $brand->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $brand->id }}" />
                                    </td>
                                    <td>
                                        @if ($brand->logo)
                                            <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}"
                                                class="rounded" width="40" height="40" style="object-fit:cover">
                                        @else
                                            <div class="bg-light rounded d-flex
                                                            align-items-center
                                                            justify-content-center"
                                                style="width:40px;height:40px">
                                                <i data-lucide="image" class="text-muted" style="width:18px;height:18px">
                                                </i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $brand->name }}
                                        </span>
                                        @if ($brand->description)
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($brand->description, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($brand->website)
                                            <a href="{{ $brand->website }}" target="_blank" rel="noopener noreferrer"
                                                class="link-reset text-decoration-underline">
                                                {{ Str::limit($brand->website, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $brand->products_count }}
                                        </span>
                                    </td>
                                    <td>{{ $brand->sort_order }}</td>
                                    <td>
                                        @admincan('brand.edit')
                                            <form action="{{ route('admin.ecommerce.brand.toggle-status', $brand) }}"
                                                method="POST" class="d-inline toggle-status-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="badge border-0
                                                            {{ $brand->is_active ? 'bg-success' : 'bg-danger' }}"
                                                    style="cursor:pointer" title="Click to toggle">
                                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="badge
                                                    {{ $brand->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endadmincan
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">

                                            {{-- View --}}
                                            <button type="button"
                                                class="btn btn-default btn-icon
                                                               btn-sm rounded-circle"
                                                title="View Details" data-bs-toggle="modal"
                                                data-bs-target="#brandDetailModal" data-id="{{ $brand->id }}"
                                                data-name="{{ $brand->name }}" data-slug="{{ $brand->slug }}"
                                                data-description="{{ $brand->description }}"
                                                data-website="{{ $brand->website }}"
                                                data-products="{{ $brand->products_count }}"
                                                data-sort="{{ $brand->sort_order }}"
                                                data-status="{{ $brand->is_active ? 'Active' : 'Inactive' }}"
                                                data-image="{{ $brand->logo ? Storage::url($brand->logo) : '' }}"
                                                data-created="{{ $brand->created_at->format('d M Y, h:i A') }}">
                                                <i class="fs-lg" data-lucide="eye"></i>
                                            </button>

                                            {{-- Edit --}}
                                            @admincan('brand.edit')
                                                <button type="button"
                                                    class="btn btn-default btn-icon
                                                                   btn-sm rounded-circle"
                                                    title="Edit" data-bs-toggle="modal" data-bs-target="#brandModal"
                                                    data-mode="edit" data-id="{{ $brand->id }}"
                                                    data-name="{{ $brand->name }}" data-slug="{{ $brand->slug }}"
                                                    data-description="{{ $brand->description }}"
                                                    data-website="{{ $brand->website }}"
                                                    data-sort="{{ $brand->sort_order }}"
                                                    data-status="{{ $brand->is_active ? 'active' : 'inactive' }}"
                                                    data-image="{{ $brand->logo ? Storage::url($brand->logo) : '' }}"
                                                    data-update-url="{{ route('admin.ecommerce.brand.update', $brand) }}">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endadmincan

                                            {{-- Delete --}}
                                            @admincan('brand.delete')
                                                <button type="button"
                                                    class="btn btn-default btn-icon
                                                                   btn-sm rounded-circle"
                                                    title="Delete" data-bs-toggle="modal" data-bs-target="#deleteBrandModal"
                                                    data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                                    data-products="{{ $brand->products_count }}"
                                                    data-delete-url="{{ route('admin.ecommerce.brand.destroy', $brand) }}">
                                                    <i class="fs-lg" data-lucide="trash-2"></i>
                                                </button>
                                            @endadmincan

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD/EDIT MODAL --}}
    <div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="brandForm" method="POST" enctype="multipart/form-data"
                    data-store-url="{{ route('admin.ecommerce.brand.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandName">
                                    Brand Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="brandName" name="name"
                                    placeholder="e.g. Apple" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandSlug">
                                    Slug <small class="text-muted fw-normal">(auto-generated)</small>
                                </label>
                                <input type="text" class="form-control bg-light" id="brandSlug"
                                    placeholder="auto-generated" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandWebsite">
                                    Website <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <input type="url" class="form-control" id="brandWebsite" name="website"
                                    placeholder="https://example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandStatus">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="brandStatus" name="is_active" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandSortOrder">
                                    Sort Order
                                </label>
                                <input type="number" class="form-control" id="brandSortOrder" name="sort_order"
                                    value="0" min="0" max="9999">
                                <div class="form-text">Lower number appears first</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="brandImage">
                                    Brand Logo
                                </label>
                                <input type="file" class="form-control" id="brandImage" name="logo"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                                <div class="form-text">jpeg, jpg, png, webp. Max 2MB</div>
                            </div>
                            <div class="col-12 d-none" id="imagePreviewContainer">
                                <div class="d-flex align-items-center gap-3">
                                    <img id="imagePreview" src="" alt="Preview" class="rounded border"
                                        style="width:80px;height:80px;object-fit:cover">
                                    <div>
                                        <div class="fw-semibold small" id="imagePreviewLabel">
                                            Current Logo
                                        </div>
                                        <small class="text-muted">Upload new to replace</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="brandDescription">
                                    Description <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <textarea class="form-control" id="brandDescription" name="description" rows="3"
                                    placeholder="Brief description..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="brandSubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i>
                            Add Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE SINGLE MODAL --}}
    @admincan('brand.delete')
        <div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Brand
                        </h5>
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

        {{-- BULK DELETE MODAL --}}
        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>
                            Delete Selected Brands
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bulkDeleteMessage"></p>
                        <div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-2"></i>
                            Brands with products assigned cannot be deleted.
                            This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.brand.bulk-destroy') }}" method="POST">
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

    {{-- DETAIL MODAL --}}
    <div class="modal fade" id="brandDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i data-lucide="info" class="me-2"></i>Brand Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="detailImage" src="" alt="" class="rounded border d-none"
                            style="width:100px;height:100px;object-fit:cover">
                        <div id="detailNoImage"
                            class="bg-light rounded d-inline-flex
                                    align-items-center justify-content-center"
                            style="width:100px;height:100px">
                            <i data-lucide="image" class="text-muted" style="width:40px;height:40px"></i>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-semibold text-muted" width="40%">Name</td>
                            <td id="detailName">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Slug</td>
                            <td><code id="detailSlug">—</code></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Website</td>
                            <td id="detailWebsite">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Products</td>
                            <td id="detailProducts">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Sort Order</td>
                            <td id="detailSort">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Status</td>
                            <td id="detailStatus">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Description</td>
                            <td id="detailDescription">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Created</td>
                            <td id="detailCreated">—</td>
                        </tr>
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
    @vite(['resources/js/pages/admin-ecommerce-brand.js'])
@endsection
