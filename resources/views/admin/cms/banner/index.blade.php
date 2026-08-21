@extends('admin.include.vertical', ['title' => 'Banners'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Banners'])

    <div class="row">
        <div class="col-12">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="circle-check"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="triangle-alert"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="card-title mb-0">Banner List</h5>
                    <div>
                        @admincan('banner.delete')
                            <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                <i class="fs-sm me-1" data-lucide="trash-2"></i>
                                Delete Selected
                            </button>
                        @endadmincan
                        @admincan('banner.create')
                            <button class="btn btn-primary" type="button" id="addBannerBtn" data-bs-toggle="modal"
                                data-bs-target="#bannerModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i>
                                Add Banner
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="bannerTable"
                        class="table table-striped dt-responsive checkbox-select-datatable align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" />
                                </th>
                                <th width="90">Image</th>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Sort</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banners as $banner)
                                <tr data-id="{{ $banner->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $banner->id }}" />
                                    </td>
                                    <td>
                                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                            class="rounded" width="70" height="40" style="object-fit:cover">
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $banner->title ?: '—' }}</span>
                                        @if ($banner->subtitle)
                                            <br><small class="text-muted">{{ $banner->subtitle }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $banner->position_label }}
                                        </span>
                                    </td>
                                    <td>{{ $banner->sort_order }}</td>
                                    <td>
                                        @if ($banner->starts_at || $banner->expires_at)
                                            <small class="text-muted d-block">
                                                {{ $banner->starts_at?->format('d M Y') ?? '—' }}
                                                to
                                                {{ $banner->expires_at?->format('d M Y') ?? '—' }}
                                            </small>
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td>
                                        @admincan('banner.edit')
                                            <form action="{{ route('admin.cms.banner.toggle-status', $banner) }}"
                                                method="POST" class="d-inline toggle-status-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="badge border-0 {{ $banner->status_badge_class }}"
                                                    style="cursor:pointer" title="Click to toggle active/inactive">
                                                    {{ $banner->status_label }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge {{ $banner->status_badge_class }}">
                                                {{ $banner->status_label }}
                                            </span>
                                        @endadmincan
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @admincan('banner.edit')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Edit" data-bs-toggle="modal" data-bs-target="#bannerModal"
                                                    data-mode="edit" data-id="{{ $banner->id }}"
                                                    data-title="{{ $banner->title }}"
                                                    data-subtitle="{{ $banner->subtitle }}"
                                                    data-description="{{ $banner->description }}"
                                                    data-button-text="{{ $banner->button_text }}"
                                                    data-button-url="{{ $banner->button_url }}"
                                                    data-button-target="{{ $banner->button_target }}"
                                                    data-position="{{ $banner->position }}"
                                                    data-text-position="{{ $banner->text_position }}"
                                                    data-sort-order="{{ $banner->sort_order }}"
                                                    data-starts-at="{{ $banner->starts_at?->format('Y-m-d\TH:i') }}"
                                                    data-expires-at="{{ $banner->expires_at?->format('Y-m-d\TH:i') }}"
                                                    data-status="{{ $banner->is_active ? 'active' : 'inactive' }}"
                                                    data-image="{{ $banner->image_url }}"
                                                    data-mobile-image="{{ $banner->mobile_image_url }}"
                                                    data-update-url="{{ route('admin.cms.banner.update', $banner) }}">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endadmincan

                                            @admincan('banner.delete')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Delete" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBannerModal" data-id="{{ $banner->id }}"
                                                    data-title="{{ $banner->title ?: 'this banner' }}"
                                                    data-delete-url="{{ route('admin.cms.banner.destroy', $banner) }}">
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
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bannerForm" method="POST" enctype="multipart/form-data"
                    data-store-url="{{ route('admin.cms.banner.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerTitle">
                                    Title <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="bannerTitle" name="title"
                                    placeholder="e.g. New Fashion Collection">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerSubtitle">
                                    Subtitle <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="bannerSubtitle" name="subtitle"
                                    placeholder="e.g. Sale Offer">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="bannerDescription">
                                    Description <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <textarea class="form-control" id="bannerDescription" name="description" rows="2"
                                    placeholder="Short supporting text..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="bannerButtonText">
                                    Button Text <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="bannerButtonText" name="button_text"
                                    placeholder="e.g. Order Now">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold" for="bannerButtonUrl">
                                    Button URL <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="bannerButtonUrl" name="button_url"
                                    placeholder="/products or https://...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold" for="bannerButtonTarget">
                                    Open Link In
                                </label>
                                <select class="form-select" id="bannerButtonTarget" name="button_target">
                                    <option value="_self">Same Tab</option>
                                    <option value="_blank">New Tab</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerPosition">
                                    Position <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="bannerPosition" name="position" required>
                                    <option value="">Select Position</option>
                                    <option value="home_slider">Home Slider</option>
                                    <option value="home_promo">Home Promo</option>
                                    <option value="category_banner">Category Banner</option>
                                    <option value="popup">Popup</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerTextPosition">
                                    Text Alignment
                                </label>
                                <select class="form-select" id="bannerTextPosition" name="text_position">
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerImage">
                                    Banner Image <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="bannerImage" name="image"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                                <div class="form-text">jpeg, jpg, png, webp. Max 4MB. Recommended 1920x700.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerMobileImage">
                                    Mobile Image <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="file" class="form-control" id="bannerMobileImage" name="mobile_image"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                                <div class="form-text">Used on small screens if provided.</div>
                            </div>

                            <div class="col-12 d-none" id="imagePreviewContainer">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        <img id="imagePreview" src="" alt="Preview" class="rounded border"
                                            style="width:140px;height:70px;object-fit:cover">
                                        <small class="text-muted d-block mt-1">Current image</small>
                                    </div>
                                    <div id="mobileImagePreviewWrapper" class="d-none">
                                        <img id="mobileImagePreview" src="" alt="Mobile Preview"
                                            class="rounded border" style="width:70px;height:70px;object-fit:cover">
                                        <small class="text-muted d-block mt-1">Current mobile image</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="bannerSortOrder">
                                    Sort Order
                                </label>
                                <input type="number" class="form-control" id="bannerSortOrder" name="sort_order"
                                    value="0" min="0" max="9999">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="bannerStartsAt">
                                    Starts At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="bannerStartsAt"
                                    name="starts_at">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="bannerExpiresAt">
                                    Expires At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="bannerExpiresAt"
                                    name="expires_at">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="bannerStatus">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="bannerStatus" name="is_active" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="bannerSubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i>
                            Add Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE SINGLE MODAL --}}
    @admincan('banner.delete')
        <div class="modal fade" id="deleteBannerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Banner
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
                            Delete Selected Banners
                        </h5>
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
                        <form id="bulkDeleteForm" action="{{ route('admin.cms.banner.bulk-destroy') }}"
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
    @vite(['resources/js/pages/admin-ecommerce-banner.js'])
@endsection