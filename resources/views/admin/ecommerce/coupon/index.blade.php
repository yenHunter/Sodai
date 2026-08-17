@extends('admin.include.vertical', ['title' => 'Coupons'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Ecommerce', 'title' => 'Coupons'])

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
                    <h5 class="card-title mb-0">Coupon List</h5>
                    <div>
                        @admincan('coupon.delete')
                            <button class="btn btn-danger d-none" id="bulkDeleteBtn" type="button">
                                <i class="fs-sm me-1" data-lucide="trash-2"></i>
                                Delete Selected
                            </button>
                        @endadmincan
                        @admincan('coupon.create')
                            <button class="btn btn-primary" type="button" id="addCouponBtn" data-bs-toggle="modal"
                                data-bs-target="#couponModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i>
                                Add Coupon
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="couponTable"
                        class="table table-striped dt-responsive checkbox-select-datatable align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" />
                                </th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Min. Order</th>
                                <th>Usage</th>
                                <th>Validity</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                                <tr data-id="{{ $coupon->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $coupon->id }}" />
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $coupon->code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary text-capitalize">
                                            {{ $coupon->type }}
                                        </span>
                                    </td>
                                    <td>{{ $coupon->value_label }}</td>
                                    <td>${{ number_format((float) $coupon->minimum_order_amount, 2) }}</td>
                                    <td>
                                        {{ $coupon->used_count }}
                                        @if ($coupon->usage_limit)
                                            / {{ $coupon->usage_limit }}
                                        @else
                                            <span class="text-muted">/ &infin;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon->starts_at || $coupon->expires_at)
                                            <small class="text-muted d-block">
                                                {{ $coupon->starts_at?->format('d M Y') ?? '—' }}
                                                to
                                                {{ $coupon->expires_at?->format('d M Y') ?? '—' }}
                                            </small>
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td>
                                        @admincan('coupon.edit')
                                            <form action="{{ route('admin.ecommerce.coupon.toggle-status', $coupon) }}"
                                                method="POST" class="d-inline toggle-status-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="badge border-0 {{ $coupon->status_badge_class }}"
                                                    style="cursor:pointer" title="Click to toggle active/inactive">
                                                    {{ $coupon->status_label }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge {{ $coupon->status_badge_class }}">
                                                {{ $coupon->status_label }}
                                            </span>
                                        @endadmincan
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @admincan('coupon.edit')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Edit" data-bs-toggle="modal" data-bs-target="#couponModal"
                                                    data-mode="edit" data-id="{{ $coupon->id }}"
                                                    data-code="{{ $coupon->code }}" data-type="{{ $coupon->type }}"
                                                    data-value="{{ $coupon->value }}"
                                                    data-minimum-order-amount="{{ $coupon->minimum_order_amount }}"
                                                    data-maximum-discount="{{ $coupon->maximum_discount }}"
                                                    data-usage-limit="{{ $coupon->usage_limit }}"
                                                    data-usage-per-user="{{ $coupon->usage_per_user }}"
                                                    data-starts-at="{{ $coupon->starts_at?->format('Y-m-d\TH:i') }}"
                                                    data-expires-at="{{ $coupon->expires_at?->format('Y-m-d\TH:i') }}"
                                                    data-status="{{ $coupon->is_active ? 'active' : 'inactive' }}"
                                                    data-update-url="{{ route('admin.ecommerce.coupon.update', $coupon) }}">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endadmincan

                                            @admincan('coupon.delete')
                                                <button type="button"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    title="Delete" data-bs-toggle="modal"
                                                    data-bs-target="#deleteCouponModal" data-id="{{ $coupon->id }}"
                                                    data-code="{{ $coupon->code }}"
                                                    data-used="{{ $coupon->used_count }}"
                                                    data-delete-url="{{ route('admin.ecommerce.coupon.destroy', $coupon) }}">
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
    <div class="modal fade" id="couponModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Add New Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="couponForm" method="POST"
                    data-store-url="{{ route('admin.ecommerce.coupon.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponCode">
                                    Coupon Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="couponCode" name="code"
                                    placeholder="e.g. SUMMER25" style="text-transform:uppercase" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponType">
                                    Discount Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="couponType" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount ($)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponValue">
                                    Discount Value <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="couponValue"
                                    name="value" placeholder="e.g. 25" required>
                            </div>
                            <div class="col-md-6" id="maximumDiscountWrapper">
                                <label class="form-label fw-semibold" for="couponMaximumDiscount">
                                    Maximum Discount <small class="text-muted fw-normal">(Optional, for %
                                        only)</small>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="couponMaximumDiscount" name="maximum_discount" placeholder="e.g. 50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponMinimumOrder">
                                    Minimum Order Amount <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="couponMinimumOrder" name="minimum_order_amount" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponUsageLimit">
                                    Total Usage Limit <small class="text-muted fw-normal">(Optional, blank =
                                        unlimited)</small>
                                </label>
                                <input type="number" min="1" class="form-control" id="couponUsageLimit"
                                    name="usage_limit" placeholder="e.g. 100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponUsagePerUser">
                                    Usage Limit Per Customer
                                </label>
                                <input type="number" min="1" class="form-control" id="couponUsagePerUser"
                                    name="usage_per_user" value="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponStartsAt">
                                    Starts At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="couponStartsAt"
                                    name="starts_at">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponExpiresAt">
                                    Expires At <small class="text-muted fw-normal">(Optional)</small>
                                </label>
                                <input type="datetime-local" class="form-control" id="couponExpiresAt"
                                    name="expires_at">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="couponStatus">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="couponStatus" name="is_active" required>
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
                        <button type="submit" class="btn btn-primary" id="couponSubmitBtn">
                            <i data-lucide="plus" class="fs-sm me-1"></i>
                            Add Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE SINGLE MODAL --}}
    @admincan('coupon.delete')
        <div class="modal fade" id="deleteCouponModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i data-lucide="triangle-alert" class="me-2"></i>Delete Coupon
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
                            Delete Selected Coupons
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bulkDeleteMessage"></p>
                        <div class="alert alert-warning mb-0">
                            <i data-lucide="triangle-alert" class="me-2"></i>
                            Coupons that have already been used in orders cannot be deleted.
                            This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="bulkDeleteForm" action="{{ route('admin.ecommerce.coupon.bulk-destroy') }}"
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
    @vite(['resources/js/pages/admin-ecommerce-coupon.js'])
@endsection