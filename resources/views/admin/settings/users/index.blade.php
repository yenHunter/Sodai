@extends('admin.include.vertical', ['title' => 'Admins'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Admin Users'])

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
                    <h5 class="card-title mb-0">Admin List</h5>
                    <div>
                        @admincan('admin.create')
                            <button class="btn btn-primary" type="button" id="addAdminBtn" data-bs-toggle="modal"
                                data-bs-target="#adminModal">
                                <i class="fs-sm me-1" data-lucide="plus"></i> Add Admin
                            </button>
                        @endadmincan
                    </div>
                </div>
                <div class="card-body">
                    <table id="adminTable" class="table table-striped dt-responsive align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>
                                        @if ($admin->avatar_url)
                                            <img src="{{ $admin->avatar_url }}" class="rounded-circle" width="40"
                                                height="40" style="object-fit:cover" alt="">
                                        @else
                                            <span
                                                class="avatar-title bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width:40px;height:40px">
                                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->phone ?? '—' }}</td>
                                    <td>
                                        @foreach ($admin->roles as $role)
                                            <span class="badge bg-info-subtle text-info">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @admincan('admin.edit')
                                            <form action="{{ route('admin.users.toggle-status', $admin) }}" method="POST"
                                                class="d-inline toggle-status-form">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="badge border-0 {{ $admin->is_active ? 'bg-success' : 'bg-danger' }}"
                                                    style="cursor:pointer">
                                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="badge {{ $admin->is_active ? 'bg-success' : 'bg-danger' }}">{{ $admin->is_active ? 'Active' : 'Inactive' }}</span>
                                        @endadmincan
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @admincan('admin.edit')
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#adminModal" data-mode="edit"
                                                    data-id="{{ $admin->id }}" data-name="{{ $admin->name }}"
                                                    data-email="{{ $admin->email }}" data-phone="{{ $admin->phone }}"
                                                    data-role-id="{{ $admin->roles->first()?->id }}"
                                                    data-status="{{ $admin->is_active ? 'active' : 'inactive' }}"
                                                    data-image="{{ $admin->avatar_url }}"
                                                    data-update-url="{{ route('admin.users.update', $admin) }}">
                                                    <i class="fs-lg" data-lucide="square-pen"></i>
                                                </button>
                                            @endadmincan
                                            @admincan('admin.delete')
                                                <button type="button" class="btn btn-default btn-icon btn-sm rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#deleteAdminModal"
                                                    data-name="{{ $admin->name }}"
                                                    data-delete-url="{{ route('admin.users.destroy', $admin) }}">
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
    <div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="adminForm" method="POST" enctype="multipart/form-data"
                    data-store-url="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="adminName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="adminEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone <span
                                        class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" class="form-control" id="adminPhone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="adminRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" id="passwordLabel">Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="adminPassword" name="password">
                                <div class="form-text" id="passwordHint" style="display:none">Leave blank to keep current
                                    password</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" class="form-control" id="adminPasswordConfirm"
                                    name="password_confirmation">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="adminStatus" name="is_active" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Avatar</label>
                                <input type="file" class="form-control" id="adminAvatar" name="avatar"
                                    accept="image/jpeg,image/jpg,image/png,image/webp">
                            </div>
                            <div class="col-12 d-none" id="avatarPreviewContainer">
                                <img id="avatarPreview" src="" class="rounded border"
                                    style="width:70px;height:70px;object-fit:cover" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="adminSubmitBtn"><i data-lucide="plus"
                                class="fs-sm me-1"></i> Add Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    @admincan('admin.delete')
        <div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i data-lucide="triangle-alert" class="me-2"></i>Delete Admin
                        </h5>
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
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-settings-users-index.js'])
@endsection
