@extends('admin.include.vertical', ['title' => 'Categories'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Categories'])

    <div class="row">
        <div class="col-12">
            {{-- Session Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="me-2" data-lucide="triangle-alert"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card" data-table="" data-table-rows-per-page="8">
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex gap-2">
                        <div class="app-search">
                            <input class="form-control" data-table-search="" placeholder="Search category..."
                                type="search" />
                            <i class="app-search-icon text-muted" data-lucide="search"></i>
                        </div>
                        <button class="btn btn-danger d-none" data-table-delete-selected="">Delete</button>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div>
                            <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                        <div class="app-search">
                            <select class="form-select form-control my-1 my-md-0" data-table-filter="status">
                                <option value="">All</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <i class="app-search-icon text-muted" data-lucide="circle-small"></i>
                        </div>
                        @can('category.create', 'admin')
                            <a class="btn btn-primary ms-1" data-bs-target="#addEditCategoryModal" data-bs-toggle="modal"
                                href="#!"> <i class="fs-sm me-2" data-lucide="plus"></i> Add Category </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th class="ps-3" style="width: 1%">
                                    <input class="form-check-input form-check-input-light fs-14 mt-0"
                                        data-table-select-all="" type="checkbox" value="option" />
                                </th>
                                <th width="60">Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Sub-categories</th>
                                <th>Products</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="ps-3">
                                        <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0"
                                            type="checkbox" value="option" />
                                    </td>
                                    <td>
                                        @if ($category->image)
                                            <div class="avatar-md me-3">
                                                <img alt="{{ $category->name }}" class="img-fluid rounded"
                                                    src="{{ Storage::url($category->image) }}" />
                                            </div>
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center
                                                    justify-content-center"
                                                style="width:40px;height:40px">
                                                <i class="text-muted" data-lucide="image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $category->name }}
                                        </span>
                                        @if ($category->description)
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($category->description, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Category</span>
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
                                        <div class="d-flex justify-content-center gap-1">
                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i
                                                    class="fs-lg" data-lucide="eye"></i></a>
                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i
                                                    class="fs-lg" data-lucide="square-pen"></i></a>
                                            <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                data-table-delete-row="" href="#"><i class="fs-lg"
                                                    data-lucide="trash-2"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @foreach ($category->children as $child)
                                    <tr>
                                        <td class="ps-3">
                                            <input
                                                class="form-check-input form-check-input-light fs-14 product-item-check mt-0"
                                                type="checkbox" value="option" />
                                        </td>
                                        <td>
                                            @if ($child->image)
                                                <div class="avatar-md me-3">
                                                    <img alt="{{ $child->name }}" class="img-fluid rounded"
                                                        src="{{ Storage::url($child->image) }}" />
                                                </div>
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center
                                                        justify-content-center"
                                                    style="width:35px;height:35px">
                                                    <i class="text-muted small" data-lucide="image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="ms-3">
                                                <i class="text-muted me-1" data-lucide="arrow-right"></i>
                                                {{ $child->name }}
                                            </span>
                                            @if ($child->description)
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
                                            <div class="d-flex justify-content-center gap-1">
                                                <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                <a class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    data-table-delete-row="" href="#"><i class="fs-lg"
                                                        data-lucide="trash-2"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <i class="fs-1 text-muted" data-lucide="info"></i>
                                        <p class="mt-2 text-muted">No categories found.</p>
                                        @can('category.create', 'admin')
                                            <a class="btn btn-primary ms-1" data-bs-target="#addEditCategoryModal"
                                                data-bs-toggle="modal" href="#!"> <i class="fs-sm me-2"
                                                    data-lucide="plus"></i> Add Category </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div data-table-pagination-info="categories"></div>
                        <div data-table-pagination=""></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="addEditCategoryModalLabel" class="modal fade" id="addEditCategoryModal"
        tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditCategoryModalLabel">Add New Category</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <form id="categoryForm" action="{{ route('admin.ecommerce.category.store') }}" method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="categoryName">Category Name</label>
                                <input class="form-control" id="categoryName" placeholder="e.g. Electronics"
                                    required="" type="text" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="categorySlug">Slug</label>
                                <input class="form-control" id="categorySlug" placeholder="e.g. electronics"
                                    required="" type="text" />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="categoryImage">Category Image</label>
                                <input accept="image/*" class="form-control" id="categoryImage" type="file" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="categoryStatus">Status</label>
                                <select class="form-select" id="categoryStatus" required="">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="categoryDescription">Description (Optional)</label>
                                <textarea class="form-control" id="categoryDescription" placeholder="Brief description of the category..."
                                    rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" type="submit">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/custom-table.js'])
@endsection
