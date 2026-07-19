@extends('admin.include.vertical', ['title' => 'Attributes'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Product Attributes'])

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

    <div class="alert alert-info">
        <i class="me-2" data-lucide="info"></i>
        These correspond to fixed fields on the product form (Color, Size, Weight). Disabling an attribute hides
        that field group on the product create/edit form and product details page — it does not delete existing
        product data.
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Attribute List</h5></div>
        <div class="table-responsive">
            <table class="table table-custom table-centered align-middle mb-0">
                <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                    <tr class="text-uppercase fs-xxs">
                        <th>Key</th>
                        <th>Label</th>
                        <th>Status</th>
                        <th class="text-center" style="width:1%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attributes as $attribute)
                        <tr>
                            <td><code>{{ $attribute->key }}</code></td>
                            <td class="fw-semibold">{{ $attribute->label }}</td>
                            <td>
                                @admincan('attribute.edit')
                                    <form action="{{ route('admin.ecommerce.attribute.toggle-status', $attribute) }}" method="POST" class="d-inline toggle-status-form">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="badge border-0 {{ $attribute->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="cursor:pointer">
                                            {{ ucfirst($attribute->status) }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge {{ $attribute->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($attribute->status) }}</span>
                                @endadmincan
                            </td>
                            <td class="text-center">
                                @admincan('attribute.edit')
                                    <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle" data-bs-toggle="modal"
                                        data-bs-target="#attributeModal" data-id="{{ $attribute->id }}" data-label="{{ $attribute->label }}"
                                        data-status="{{ $attribute->status }}" data-update-url="{{ route('admin.ecommerce.attribute.update', $attribute) }}"
                                        title="Edit">
                                        <i class="fs-lg" data-lucide="square-pen"></i>
                                    </button>
                                @endadmincan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    @admincan('attribute.edit')
        <div class="modal fade" id="attributeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="attributeForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Display Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="attributeLabel" name="label" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="attributeStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-attribute.js'])
@endsection