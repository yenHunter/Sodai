@extends('admin.include.vertical', ['title' => 'FAQs'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'CMS', 'title' => 'FAQs'])

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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-end gap-2 mb-3">
        @admincan('faq.create')
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#categoryModal"
                id="addCategoryBtn">
                <i class="fs-sm me-1" data-lucide="folder-plus"></i> Add Category
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqModal"
                id="addFaqBtn">
                <i class="fs-sm me-1" data-lucide="plus"></i> Add FAQ
            </button>
        @endadmincan
    </div>

    @if ($categories->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i data-lucide="help-circle" class="text-muted mb-2" style="width:40px;height:40px;"></i>
                <p class="text-muted mb-0">No FAQ categories yet. Start by adding one.</p>
            </div>
        </div>
    @else
        <div class="accordion" id="faqAccordion">
            @foreach ($categories as $category)
                <div class="accordion-item card mb-2" data-category-id="{{ $category->id }}">
                    <h2 class="accordion-header d-flex align-items-center">
                        <button class="accordion-button collapsed flex-grow-1" type="button"
                            data-bs-toggle="collapse" data-bs-target="#cat-{{ $category->id }}">
                            <span class="fw-semibold">{{ $category->name }}</span>
                            <span class="badge bg-light text-dark ms-2">{{ $category->faqs_count }} FAQ(s)</span>
                            <span class="badge {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} ms-2">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </button>
                        <div class="d-flex gap-1 px-3">
                            @admincan('faq.edit')
                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                    title="Edit Category" data-bs-toggle="modal" data-bs-target="#categoryModal"
                                    data-mode="edit" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    data-sort-order="{{ $category->sort_order }}"
                                    data-status="{{ $category->is_active ? 'active' : 'inactive' }}"
                                    data-update-url="{{ route('admin.cms.faq.categories.update', $category) }}">
                                    <i class="fs-md" data-lucide="square-pen"></i>
                                </button>
                            @endadmincan
                            @admincan('faq.delete')
                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                    title="Delete Category" data-bs-toggle="modal"
                                    data-bs-target="#deleteCategoryModal" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}" data-faq-count="{{ $category->faqs_count }}"
                                    data-delete-url="{{ route('admin.cms.faq.categories.destroy', $category) }}">
                                    <i class="fs-md" data-lucide="trash-2"></i>
                                </button>
                            @endadmincan
                        </div>
                    </h2>
                    <div id="cat-{{ $category->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body pt-0">
                            @if ($category->faqs->isEmpty())
                                <p class="text-muted small mb-0">No FAQs in this category yet.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="30">
                                                    <input type="checkbox" class="form-check-input row-checkbox-all"
                                                        data-category="{{ $category->id }}">
                                                </th>
                                                <th>Question</th>
                                                <th width="80">Sort</th>
                                                <th width="100">Status</th>
                                                <th width="100">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($category->faqs as $faq)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input row-checkbox"
                                                            value="{{ $faq->id }}">
                                                    </td>
                                                    <td>{{ Str::limit($faq->question, 80) }}</td>
                                                    <td>{{ $faq->sort_order }}</td>
                                                    <td>
                                                        @admincan('faq.edit')
                                                            <form action="{{ route('admin.cms.faq.items.toggle-status', $faq) }}"
                                                                method="POST" class="d-inline toggle-status-form">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="badge border-0 {{ $faq->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}"
                                                                    style="cursor:pointer">
                                                                    {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge {{ $faq->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        @endadmincan
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            @admincan('faq.edit')
                                                                <button type="button"
                                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                                    title="Edit" data-bs-toggle="modal"
                                                                    data-bs-target="#faqModal" data-mode="edit"
                                                                    data-id="{{ $faq->id }}"
                                                                    data-category-id="{{ $faq->faq_category_id }}"
                                                                    data-question="{{ $faq->question }}"
                                                                    data-answer="{{ $faq->answer }}"
                                                                    data-sort-order="{{ $faq->sort_order }}"
                                                                    data-status="{{ $faq->is_active ? 'active' : 'inactive' }}"
                                                                    data-update-url="{{ route('admin.cms.faq.items.update', $faq) }}">
                                                                    <i class="fs-md" data-lucide="square-pen"></i>
                                                                </button>
                                                            @endadmincan
                                                            @admincan('faq.delete')
                                                                <button type="button"
                                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                                    title="Delete" data-bs-toggle="modal"
                                                                    data-bs-target="#deleteFaqModal"
                                                                    data-id="{{ $faq->id }}"
                                                                    data-question="{{ Str::limit($faq->question, 60) }}"
                                                                    data-delete-url="{{ route('admin.cms.faq.items.destroy', $faq) }}">
                                                                    <i class="fs-md" data-lucide="trash-2"></i>
                                                                </button>
                                                            @endadmincan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @admincan('faq.delete')
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger d-none bulk-delete-btn"
                                            data-category="{{ $category->id }}">
                                            <i class="fs-sm me-1" data-lucide="trash-2"></i> Delete Selected
                                        </button>
                                    </div>
                                @endadmincan
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- CATEGORY ADD/EDIT MODAL --}}
    @admincan('faq.create')
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="categoryForm" method="POST"
                        data-store-url="{{ route('admin.cms.faq.categories.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="categoryName">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="categoryName" name="name"
                                    placeholder="e.g. Shipping" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold" for="categorySortOrder">Sort Order</label>
                                    <input type="number" class="form-control" id="categorySortOrder"
                                        name="sort_order" value="0" min="0" max="9999">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold" for="categoryStatus">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="categoryStatus" name="is_active" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="categorySubmitBtn">
                                <i data-lucide="plus" class="fs-sm me-1"></i> Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- FAQ ADD/EDIT MODAL --}}
        <div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="faqModalLabel">Add FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="faqForm" method="POST" data-store-url="{{ route('admin.cms.faq.items.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold" for="faqCategoryId">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="faqCategoryId" name="faq_category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="faqSortOrder">Sort Order</label>
                                    <input type="number" class="form-control" id="faqSortOrder" name="sort_order"
                                        value="0" min="0" max="9999">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="faqQuestion">
                                        Question <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="faqQuestion" name="question"
                                        maxlength="500" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="faqAnswer">
                                        Answer <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="faqAnswer" name="answer" rows="5"
                                        maxlength="5000" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="faqStatus">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="faqStatus" name="is_active" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="faqSubmitBtn">
                                <i data-lucide="plus" class="fs-sm me-1"></i> Add FAQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endadmincan

    {{-- DELETE CATEGORY MODAL --}}
    @admincan('faq.delete')
        <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Category
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="deleteCategoryModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteCategoryForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" id="confirmDeleteCategoryBtn">
                                <i data-lucide="trash-2" class="fs-sm me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- DELETE FAQ MODAL --}}
        <div class="modal fade" id="deleteFaqModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete FAQ
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="deleteFaqModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteFaqForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" id="confirmDeleteFaqBtn">
                                <i data-lucide="trash-2" class="fs-sm me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- BULK DELETE FAQ MODAL --}}
        <div class="modal fade" id="bulkDeleteFaqModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Selected FAQs
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bulkDeleteFaqMessage"></p>
                        <div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-2"></i>This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="bulkDeleteFaqForm" action="{{ route('admin.cms.faq.items.bulk-destroy') }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ids" id="bulkDeleteFaqIds">
                            <button type="submit" class="btn btn-danger" id="confirmBulkDeleteFaqBtn">
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
    @vite(['resources/js/pages/admin-cms-faq.js'])
@endsection