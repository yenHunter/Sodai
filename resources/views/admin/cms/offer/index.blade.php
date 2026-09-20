{{-- resources/views/admin/cms/offer/index.blade.php --}}
@extends('admin.include.vertical', ['title' => 'Offers'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'CMS', 'title' => 'Offers'])

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
                    <h5 class="card-title mb-0">Offer List</h5>
                    <div>
                        @admincan('offer.delete')
                            <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                <i class="fs-sm me-1" data-lucide="trash-2"></i>
                                Delete Selected
                            </button>
                        @endadmincan
                        @admincan('offer.create')
                            <button class="btn btn-primary" type="button" id="addOfferBtn" data-bs-toggle="modal"
                                data-bs-target="#offerModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i>
                                Add Offer
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="offerTable"
                        class="table table-striped dt-responsive checkbox-select-datatable align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" />
                                </th>
                                <th width="90">Image</th>
                                <th>Title</th>
                                <th>Linked Category</th>
                                <th>Sort</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offers as $offer)
                                <tr data-id="{{ $offer->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $offer->id }}" />
                                    </td>
                                    <td>
                                        <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}"
                                            class="rounded" width="70" height="40" style="object-fit:cover">
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $offer->title ?: '—' }}</span>
                                        @if ($offer->subtitle)
                                            <br><small class="text-muted">{{ Str::limit($offer->subtitle, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $offer->category?->name ?? '—' }}</td>
                                    <td>{{ $offer->sort_order }}</td>
                                    <td>
                                        @if ($offer->starts_at || $offer->expires_at)
                                            <small class="text-muted d-block">
                                                {{ $offer->starts_at?->format('d M Y') ?? '—' }}
                                                to
                                                {{ $offer->expires_at?->format('d M Y') ?? '—' }}
                                            </small>
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td>
                                        @admincan('offer.edit')
                                            <form action="{{ route('admin.cms.offer.toggle-status', $offer) }}"
                                                method="POST" class="d-inline toggle-status-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="badge border-0 {{ $offer->status_badge_class }}"
                                                    style="cursor:pointer" title="Click to toggle active/inactive">
                                                    {{ $offer->status_label }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge {{ $offer->status_badge_class }}">
                                                {{ $offer->status_label }}
                                            </span>
                                        @endadmincan
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @admincan('offer.edit')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Edit" data-bs-toggle="modal" data-bs-target="#offerModal"
                                                    data-mode="edit" data-id="{{ $offer->id }}"
                                                    data-title="{{ $offer->title }}"
                                                    data-discount-percentage="{{ $offer->subtitle }}"
                                                    data-description="{{ $offer->description }}"
                                                    data-button-text="{{ $offer->button_text }}"
                                                    data-button-url="{{ $offer->button_url }}"
                                                    data-category-id="{{ $offer->category_id }}"
                                                    data-sort-order="{{ $offer->sort_order }}"
                                                    data-starts-at="{{ $offer->starts_at?->format('Y-m-d\TH:i') }}"
                                                    data-expires-at="{{ $offer->expires_at?->format('Y-m-d\TH:i') }}"
                                                    data-status="{{ $offer->is_active ? 'active' : 'inactive' }}"
                                                    data-image="{{ $offer->image_url }}"
                                                    data-update-url="{{ route('admin.cms.offer.update', $offer) }}">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endadmincan

                                            @admincan('offer.delete')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Delete" data-bs-toggle="modal"
                                                    data-bs-target="#deleteOfferModal" data-id="{{ $offer->id }}"
                                                    data-title="{{ $offer->title ?: 'this offer' }}"
                                                    data-delete-url="{{ route('admin.cms.offer.destroy', $offer) }}">
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
    <div class="modal fade" id="offerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offerModalLabel">Add New Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="offerForm" method="POST" enctype="multipart/form-data"
                    data-store-url="{{ route('admin.cms.offer.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerTitle">
                                    Title <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="offerTitle" name="title"
                                    placeholder="e.g. On Furniture">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerSubtitle">
                                    Subtitle <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="offerSubtitle" name="subtitle"
                                    placeholder="e.g. Upto 40% off">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="offerDescription">
                                    Description <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <textarea class="form-control" id="offerDescription" name="description" rows="2"
                                    placeholder="Short supporting text..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerButtonText">
                                    Button Text <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="text" class="form-control" id="offerButtonText" name="button_text"
                                    placeholder="e.g. Shop Now!">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerButtonUrl">
                                    Button URL <small class="text-muted fw-normal">(Optional override)</small>
                                </label>
                                <input type="text" class="form-control" id="offerButtonUrl" name="button_url"
                                    placeholder="Leave blank to use linked category">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerCategory">
                                    Linked Category <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <select class="form-select" id="offerCategory" name="category_id">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">"Shop Now" links here unless a Button URL is set above.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerStatus">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="offerStatus" name="is_active" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="offerImage">
                                    Background Image <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="offerImage" name="image"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                                <div class="form-text">jpeg, jpg, png, webp. Max 4MB.</div>
                            </div>
                            <div class="col-md-6 d-none" id="imagePreviewContainer">
                                <label class="form-label fw-semibold">Current Image</label>
                                <div>
                                    <img id="imagePreview" src="" alt="Preview" class="rounded border"
                                        style="width:140px;height:70px;object-fit:cover">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="offerSortOrder">
                                    Sort Order
                                </label>
                                <input type="number" class="form-control" id="offerSortOrder" name="sort_order"
                                    value="0" min="0" max="9999">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="offerStartsAt">
                                    Starts At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="offerStartsAt"
                                    name="starts_at">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="offerExpiresAt">
                                    Expires At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="offerExpiresAt"
                                    name="expires_at">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="offerSubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i>
                            Add Offer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE SINGLE MODAL --}}
    @admincan('offer.delete')
        <div class="modal fade" id="deleteOfferModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Offer
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
                            Delete Selected Offers
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
                        <form id="bulkDeleteForm" action="{{ route('admin.cms.offer.bulk-destroy') }}"
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
    @vite(['resources/js/pages/admin-cms-offer.js'])
@endsection