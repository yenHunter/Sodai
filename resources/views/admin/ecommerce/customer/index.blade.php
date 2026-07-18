@extends('admin.include.vertical', ['title' => 'Customers'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Customers'])

    <div class="row">
        <div class="col-12">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-2">
                    <i class="me-2" data-lucide="triangle-alert"></i>{{ session('error') }}
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
                    <h5 class="card-title mb-0">Customer List</h5>
                    <div>
                        @admincan('customer.delete')
                            <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                <i class="fs-sm me-1" data-lucide="trash-2"></i> Delete Selected
                            </button>
                        @endadmincan
                        @admincan('customer.create')
                            <button class="btn btn-primary" type="button" id="addCustomerBtn" data-bs-toggle="modal"
                                data-bs-target="#customerModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i> Add Customer
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="customerTable" class="table table-striped dt-responsive align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th width="60">Avatar</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>City</th>
                                <th>Joined</th>
                                <th>Orders</th>
                                <th>Total Spend</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $customer->id }}"></td>
                                    <td>
                                        @if ($customer->avatar_url)
                                            <img src="{{ $customer->avatar_url }}" class="rounded-circle" width="40"
                                                height="40" style="object-fit:cover" alt="">
                                        @else
                                            <span
                                                class="avatar-title bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width:40px;height:40px">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $customer->name }}</td>
                                    <td>
                                        {{ $customer->email }}
                                        <br>
                                        {{ $customer->phone ?? '—' }}
                                    </td>
                                    <td>{{ $customer->defaultAddress?->city ?? '—' }}</td>
                                    <td>
                                        {{ $customer->created_at->format('d M, Y') }}
                                        <small
                                            class="text-muted d-block">{{ $customer->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $customer->orders_count }}</span></td>
                                    <td>${{ number_format((float) ($customer->total_spent ?? 0), 2) }}</td>
                                    <td>
                                        @if ($customer->status === 'banned')
                                            <span class="badge bg-danger">Banned</span>
                                        @elseif ($customer->status === 'active')
                                            @admincan('customer.edit')
                                                <form action="{{ route('admin.ecommerce.customer.toggle-status', $customer) }}"
                                                    method="POST" class="d-inline toggle-status-form">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="badge border-0 bg-success"
                                                        style="cursor:pointer">Active</button>
                                                </form>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endadmincan
                                        @else
                                            @admincan('customer.edit')
                                                <form action="{{ route('admin.ecommerce.customer.toggle-status', $customer) }}"
                                                    method="POST" class="d-inline toggle-status-form">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="badge border-0 bg-warning text-dark"
                                                        style="cursor:pointer">Inactive</button>
                                                </form>
                                            @else
                                                <span class="badge bg-warning text-dark">Inactive</span>
                                            @endadmincan
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @admincan('customer.edit')
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#customerModal" data-mode="edit"
                                                    data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                                    data-email="{{ $customer->email }}" data-phone="{{ $customer->phone }}"
                                                    data-status="{{ $customer->status }}"
                                                    data-image="{{ $customer->avatar_url }}"
                                                    data-update-url="{{ route('admin.ecommerce.customer.update', $customer) }}"
                                                    title="Edit">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                                <form
                                                    action="{{ route('admin.ecommerce.customer.resend-set-password', $customer) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-default btn-icon btn-sm rounded-circle"
                                                        title="Resend Set-Password Email">
                                                        <i class="fs-lg" data-lucide="mail"></i>
                                                    </button>
                                                </form>
                                            @endadmincan
                                            @admincan('customer.delete')
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                                    data-name="{{ $customer->name }}"
                                                    data-orders="{{ $customer->orders_count }}"
                                                    data-delete-url="{{ route('admin.ecommerce.customer.destroy', $customer) }}"
                                                    title="Delete">
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
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="customerForm" method="POST" enctype="multipart/form-data"
                    data-store-url="{{ route('admin.ecommerce.customer.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="customerEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone <span
                                        class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" class="form-control" id="customerPhone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="customerStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Avatar</label>
                                <input type="file" class="form-control" id="customerAvatar" name="avatar"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                            </div>
                            <div class="col-12 d-none" id="avatarPreviewContainer">
                                <img id="avatarPreview" src="" class="rounded border"
                                    style="width:70px;height:70px;object-fit:cover" alt="">
                            </div>
                            <div class="col-12" id="newCustomerNotice">
                                <div class="alert alert-info mb-0">
                                    <i class="me-1" data-lucide="info"></i>
                                    A temporary account will be created and an email will be sent to the customer
                                    with a link to set their own password.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="customerSubmitBtn"><i data-lucide="plus"
                                class="fs-sm me-1"></i> Add Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    @admincan('customer.delete')
        <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i data-lucide="triangle-alert" class="me-2"></i>Delete
                            Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="deleteModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteSingleForm" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" id="confirmDeleteBtn"><i data-lucide="trash-2"
                                    class="fs-sm me-1"></i>Delete</button>
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
                        <h5 class="modal-title text-danger"><i data-lucide="triangle-alert" class="me-2"></i>Delete
                            Selected Customers</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bulkDeleteMessage"></p>
                        <div class="alert alert-warning mb-0">
                            <i class="me-2" data-lucide="triangle-alert"></i>
                            Customers with existing orders cannot be deleted. This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.customer.bulk-destroy') }}"
                            method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="ids" id="bulkDeleteIds">
                            <button type="submit" class="btn btn-danger" id="confirmBulkDeleteBtn"><i data-lucide="trash-2"
                                    class="fs-sm me-1"></i>Delete Selected</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-ecommerce-customer.js'])
@endsection
