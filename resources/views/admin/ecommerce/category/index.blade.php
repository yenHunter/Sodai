{{-- resources/views/admin/ecommerce/category/index.blade.php --}}

@extends('admin.include.vertical', ['title' => 'Categories'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', [
        'subtitle' => 'Ecommerce',
        'title'    => 'Categories'
    ])

    <div class="row">
        <div class="col-12">

            {{-- Session Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="me-2" data-lucide="circle-check"></i>
                    {{ session('success') }}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="me-2" data-lucide="triangle-alert"></i>
                    {{ session('error') }}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            {{-- Toolbar --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    {{-- ✅ Bulk Delete Button --}}
                    @admincan('category.delete')
                        <button class="btn btn-danger d-none"
                                id="bulkDeleteBtn"
                                type="button">
                            <i class="fs-sm me-1" data-lucide="trash-2"></i>
                            Delete Selected
                        </button>
                    @endadmincan
                </div>
                <div>
                    {{-- ✅ Add Category Button --}}
                    @admincan('category.create')
                        <button class="btn btn-primary"
                                type="button"
                                id="addCategoryBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#categoryModal">
                            <i class="fs-sm me-1" data-lucide="plus"></i>
                            Add Category
                        </button>
                    @endadmincan
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="categoryTable"
                               class="table table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="selectAllCheckbox"/>
                                    </th>
                                    <th width="60">Image</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Sub-categories</th>
                                    <th>Products</th>
                                    <th>Sort</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)

                                    {{-- Parent Row --}}
                                    <tr data-id="{{ $category->id }}">
                                        <td>
                                            <input type="checkbox"
                                                   class="form-check-input row-checkbox"
                                                   value="{{ $category->id }}"/>
                                        </td>
                                        <td>
                                            @if($category->image)
                                                <img src="{{ Storage::url($category->image) }}"
                                                     alt="{{ $category->name }}"
                                                     class="rounded"
                                                     width="40"
                                                     height="40"
                                                     style="object-fit:cover">
                                            @else
                                                <div class="bg-light rounded d-flex
                                                            align-items-center
                                                            justify-content-center"
                                                     style="width:40px;height:40px">
                                                    <i data-lucide="image"
                                                       class="text-muted"
                                                       style="width:18px;height:18px">
                                                    </i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ $category->name }}
                                            </span>
                                            @if($category->description)
                                                <br>
                                                <small class="text-muted">
                                                    {{ Str::limit($category->description, 50) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                Category
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $category->children->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $category->products_count }}
                                            </span>
                                        </td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            {{-- ✅ Toggle Status --}}
                                            @admincan('category.edit')
                                                <form action="{{ route('admin.ecommerce.category.toggle-status', $category) }}"
                                                      method="POST"
                                                      class="d-inline toggle-status-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="badge border-0
                                                            {{ $category->is_active ? 'bg-success' : 'bg-danger' }}"
                                                            style="cursor:pointer"
                                                            title="Click to toggle">
                                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge
                                                    {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endadmincan
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">

                                                {{-- View --}}
                                                <button type="button"
                                                        class="btn btn-default btn-icon
                                                               btn-sm rounded-circle"
                                                        title="View Details"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#categoryDetailModal"
                                                        data-id="{{ $category->id }}"
                                                        data-name="{{ $category->name }}"
                                                        data-slug="{{ $category->slug }}"
                                                        data-description="{{ $category->description }}"
                                                        data-type="Category"
                                                        data-parent=""
                                                        data-children="{{ $category->children->count() }}"
                                                        data-products="{{ $category->products_count }}"
                                                        data-sort="{{ $category->sort_order }}"
                                                        data-status="{{ $category->is_active ? 'Active' : 'Inactive' }}"
                                                        data-image="{{ $category->image ? Storage::url($category->image) : '' }}"
                                                        data-created="{{ $category->created_at->format('d M Y, h:i A') }}">
                                                    <i class="fs-lg" data-lucide="eye"></i>
                                                </button>

                                                {{-- Edit --}}
                                                @admincan('category.edit')
                                                    <button type="button"
                                                            class="btn btn-default btn-icon
                                                                   btn-sm rounded-circle"
                                                            title="Edit"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#categoryModal"
                                                            data-mode="edit"
                                                            data-id="{{ $category->id }}"
                                                            data-name="{{ $category->name }}"
                                                            data-slug="{{ $category->slug }}"
                                                            data-description="{{ $category->description }}"
                                                            data-parent-id="{{ $category->parent_id }}"
                                                            data-sort="{{ $category->sort_order }}"
                                                            data-status="{{ $category->is_active ? 'active' : 'inactive' }}"
                                                            data-image="{{ $category->image ? Storage::url($category->image) : '' }}"
                                                            data-has-children="{{ $category->children->count() }}"
                                                            data-update-url="{{ route('admin.ecommerce.category.update', $category) }}">
                                                        <i class="fs-lg" data-lucide="square-pen"></i>
                                                    </button>
                                                @endadmincan

                                                {{-- Delete --}}
                                                @admincan('category.delete')
                                                    <button type="button"
                                                            class="btn btn-default btn-icon
                                                                   btn-sm rounded-circle"
                                                            title="Delete"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteCategoryModal"
                                                            data-id="{{ $category->id }}"
                                                            data-name="{{ $category->name }}"
                                                            data-children="{{ $category->children->count() }}"
                                                            data-delete-url="{{ route('admin.ecommerce.category.destroy', $category) }}">
                                                        <i class="fs-lg" data-lucide="trash-2"></i>
                                                    </button>
                                                @endadmincan

                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Sub-category Rows --}}
                                    @foreach($category->children as $child)
                                        <tr data-id="{{ $child->id }}">
                                            <td>
                                                <input type="checkbox"
                                                       class="form-check-input row-checkbox"
                                                       value="{{ $child->id }}"/>
                                            </td>
                                            <td>
                                                @if($child->image)
                                                    <img src="{{ Storage::url($child->image) }}"
                                                         alt="{{ $child->name }}"
                                                         class="rounded"
                                                         width="36"
                                                         height="36"
                                                         style="object-fit:cover">
                                                @else
                                                    <div class="bg-light rounded d-flex
                                                                align-items-center
                                                                justify-content-center"
                                                         style="width:36px;height:36px">
                                                        <i data-lucide="image"
                                                           class="text-muted"
                                                           style="width:16px;height:16px">
                                                        </i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="ms-3">
                                                    <i data-lucide="corner-down-right"
                                                       class="text-muted me-1"
                                                       style="width:14px;height:14px">
                                                    </i>
                                                    {{ $child->name }}
                                                </span>
                                                @if($child->description)
                                                    <br>
                                                    <small class="text-muted ms-4">
                                                        {{ Str::limit($child->description, 50) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    Sub-category
                                                </span>
                                            </td>
                                            <td>—</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $child->products_count }}
                                                </span>
                                            </td>
                                            <td>{{ $child->sort_order }}</td>
                                            <td>
                                                @admincan('category.edit')
                                                    <form action="{{ route('admin.ecommerce.category.toggle-status', $child) }}"
                                                          method="POST"
                                                          class="d-inline toggle-status-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="badge border-0
                                                                {{ $child->is_active ? 'bg-success' : 'bg-danger' }}"
                                                                style="cursor:pointer"
                                                                title="Click to toggle">
                                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge
                                                        {{ $child->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                @endadmincan
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">

                                                    {{-- View --}}
                                                    <button type="button"
                                                            class="btn btn-default btn-icon
                                                                   btn-sm rounded-circle"
                                                            title="View Details"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#categoryDetailModal"
                                                            data-id="{{ $child->id }}"
                                                            data-name="{{ $child->name }}"
                                                            data-slug="{{ $child->slug }}"
                                                            data-description="{{ $child->description }}"
                                                            data-type="Sub-category"
                                                            data-parent="{{ $category->name }}"
                                                            data-children="—"
                                                            data-products="{{ $child->products_count }}"
                                                            data-sort="{{ $child->sort_order }}"
                                                            data-status="{{ $child->is_active ? 'Active' : 'Inactive' }}"
                                                            data-image="{{ $child->image ? Storage::url($child->image) : '' }}"
                                                            data-created="{{ $child->created_at->format('d M Y, h:i A') }}">
                                                        <i class="fs-lg" data-lucide="eye"></i>
                                                    </button>

                                                    {{-- Edit --}}
                                                    @admincan('category.edit')
                                                        <button type="button"
                                                                class="btn btn-default btn-icon
                                                                       btn-sm rounded-circle"
                                                                title="Edit"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#categoryModal"
                                                                data-mode="edit"
                                                                data-id="{{ $child->id }}"
                                                                data-name="{{ $child->name }}"
                                                                data-slug="{{ $child->slug }}"
                                                                data-description="{{ $child->description }}"
                                                                data-parent-id="{{ $child->parent_id }}"
                                                                data-sort="{{ $child->sort_order }}"
                                                                data-status="{{ $child->is_active ? 'active' : 'inactive' }}"
                                                                data-image="{{ $child->image ? Storage::url($child->image) : '' }}"
                                                                data-has-children="0"
                                                                data-update-url="{{ route('admin.ecommerce.category.update', $child) }}">
                                                            <i class="fs-lg" data-lucide="square-pen"></i>
                                                        </button>
                                                    @endadmincan

                                                    {{-- Delete --}}
                                                    @admincan('category.delete')
                                                        <button type="button"
                                                                class="btn btn-default btn-icon
                                                                       btn-sm rounded-circle"
                                                                title="Delete"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteCategoryModal"
                                                                data-id="{{ $child->id }}"
                                                                data-name="{{ $child->name }}"
                                                                data-children="0"
                                                                data-delete-url="{{ route('admin.ecommerce.category.destroy', $child) }}">
                                                            <i class="fs-lg" data-lucide="trash-2"></i>
                                                        </button>
                                                    @endadmincan

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i data-lucide="inbox"
                                               style="width:48px;height:48px"
                                               class="text-muted d-block mx-auto mb-2">
                                            </i>
                                            <p class="text-muted">No categories found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- All modals same as before --}}
    {{-- ADD/EDIT MODAL --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm"
                      method="POST"
                      enctype="multipart/form-data"
                      data-store-url="{{ route('admin.ecommerce.category.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categoryName">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="categoryName"
                                       name="name"
                                       placeholder="e.g. Electronics"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categorySlug">
                                    Slug <small class="text-muted fw-normal">(auto-generated)</small>
                                </label>
                                <input type="text"
                                       class="form-control bg-light"
                                       id="categorySlug"
                                       placeholder="auto-generated"
                                       readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categoryParent">
                                    Parent Category
                                </label>
                                <select class="form-select" id="categoryParent" name="parent_id">
                                    <option value="">— Top Level Category —</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Leave empty for top-level category</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categoryStatus">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="categoryStatus" name="is_active" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categorySortOrder">
                                    Sort Order
                                </label>
                                <input type="number"
                                       class="form-control"
                                       id="categorySortOrder"
                                       name="sort_order"
                                       value="0"
                                       min="0"
                                       max="9999">
                                <div class="form-text">Lower number appears first</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="categoryImage">
                                    Category Image
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="categoryImage"
                                       name="image"
                                       accept="image/jpeg,image/jpg,image/png,image/webp">
                                <div class="form-text">jpeg, jpg, png, webp. Max 2MB</div>
                            </div>
                            <div class="col-12 d-none" id="imagePreviewContainer">
                                <div class="d-flex align-items-center gap-3">
                                    <img id="imagePreview"
                                         src=""
                                         alt="Preview"
                                         class="rounded border"
                                         style="width:80px;height:80px;object-fit:cover">
                                    <div>
                                        <div class="fw-semibold small" id="imagePreviewLabel">
                                            Current Image
                                        </div>
                                        <small class="text-muted">Upload new to replace</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="categoryDescription">
                                    Description <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <textarea class="form-control"
                                          id="categoryDescription"
                                          name="description"
                                          rows="3"
                                          placeholder="Brief description..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="categorySubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i>
                            Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE SINGLE MODAL --}}
    @admincan('category.delete')
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i data-lucide="triangle-alert" class="me-2"></i>Delete Category
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
                        Delete Selected Categories
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="bulkDeleteMessage"></p>
                    <div class="alert alert-warning mb-0">
                        <i data-lucide="triangle-alert" class="me-2"></i>
                        Categories with products cannot be deleted.
                        Sub-categories will also be deleted with their parent.
                        This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="bulkDeleteForm"
                          action="{{ route('admin.ecommerce.category.bulk-destroy') }}"
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

    {{-- DETAIL MODAL --}}
    <div class="modal fade" id="categoryDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i data-lucide="info" class="me-2"></i>Category Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="detailImage"
                             src="" alt=""
                             class="rounded border d-none"
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
                            <td class="fw-semibold text-muted">Type</td>
                            <td id="detailType">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Parent</td>
                            <td id="detailParent">—</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Sub-categories</td>
                            <td id="detailChildren">—</td>
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
    @vite(['resources/js/pages/admin-ecommerce-category.js'])
@endsection