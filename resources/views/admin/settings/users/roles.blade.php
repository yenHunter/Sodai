@extends('admin.include.vertical', ['title' => 'Roles'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Roles'])

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-2">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-2">{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-end mb-3">
                @admincan('role.create')
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#roleModal">
                        <i class="fs-sm me-1" data-lucide="plus"></i> Add Role
                    </button>
                @endadmincan
            </div>

            <div class="row g-3">
                @foreach ($roles as $role)
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="position-absolute top-0 end-0" style="width: 180px">
                                <img alt="auth-card-bg" class="auth-card-bg-img" src="/images/auth-card-bg.svg" />
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="mb-1 text-capitalize">{{ str_replace('-', ' ', $role->name) }}</h5>
                                    @if ($role->is_protected)
                                        <span class="badge bg-warning-subtle text-warning">System</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-2">{{ $role->permissions_count }} permissions ·
                                    {{ $role->admins_count }} admin(s)</p>
                                <div class="d-flex gap-2">
                                    @admincan('role.edit')
                                        <a href="{{ route('admin.users.roles.edit', $role) }}" class="btn btn-sm btn-light">
                                            <i data-lucide="shield" class="fs-xs me-1"></i> Manage Permissions
                                        </a>
                                    @endadmincan
                                    @admincan('role.delete')
                                        @if (!$role->is_protected && $role->admins_count === 0)
                                            <form action="{{ route('admin.users.roles.destroy', $role) }}" method="POST"
                                                onsubmit="return confirm('Delete this role?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i data-lucide="trash-2" class="fs-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endadmincan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ADD ROLE MODAL --}}
    @admincan('role.create')
        <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.users.roles.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. inventory-manager"
                                required>
                            <div class="form-text">Lowercase, hyphens only. You'll assign permissions on the next screen.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create & Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endadmincan

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
