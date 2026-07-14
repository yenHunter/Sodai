@extends('admin.include.vertical', ['title' => 'Role Permissions'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Manage Permissions: ' . $role->name])

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.users.roles.index') }}" class="btn btn-sm btn-light">
            <i class="fs-sm me-1" data-lucide="arrow-left"></i> Back to Roles
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($role->name === 'super-admin')
        <div class="alert alert-info">
            <i class="me-2" data-lucide="info"></i>
            The super-admin role automatically has full access to everything and bypasses permission checks. Its permission list cannot be edited.
        </div>
    @endif

    <form action="{{ route('admin.users.roles.update', $role) }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Permissions</h5>
                @if ($role->name !== 'super-admin')
                    <div>
                        <button type="button" class="btn btn-sm btn-light" id="selectAllPerms">Select All</button>
                        <button type="button" class="btn btn-sm btn-light" id="clearAllPerms">Clear All</button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($permissions_by_group as $group => $permissions)
                        <div class="col-md-4 mb-4">
                            <h6 class="text-uppercase text-muted fs-xs mb-2">{{ $group }}</h6>
                            @foreach ($permissions as $permission)
                                <div class="form-check mb-1">
                                    <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]"
                                        value="{{ $permission->id }}" id="perm{{ $permission->id }}"
                                        {{ in_array($permission->id, $assigned_ids) ? 'checked' : '' }}
                                        {{ $role->name === 'super-admin' ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="perm{{ $permission->id }}">
                                        {{ Str::after($permission->name, '.') }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @if ($role->name !== 'super-admin')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="fs-sm me-1"></i> Save Permissions</button>
                </div>
            @endif
        </div>
    </form>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    @vite(['resources/js/pages/admin-settings-users-role-details.js'])
@endsection