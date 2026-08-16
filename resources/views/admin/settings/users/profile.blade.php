@extends('admin.include.vertical', ['title' => 'My Profile'])

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'My Profile'])

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($admin->avatar_url)
                        <img src="{{ $admin->avatar_url }}" class="rounded-circle avatar-xxl mb-3" alt="admin-avater">
                    @else
                        <img src="{{ asset('images/users/administrator.png') }}" class="rounded-circle avatar-xxl mb-3" alt="admin-avater">
                    @endif
                    <h5 class="mb-1">{{ $admin->name }}</h5>
                    @foreach ($admin->roles as $role)
                        <span class="badge bg-info-subtle text-info">{{ $role->name }}</span>
                    @endforeach
                    <a href="{{ route('admin.users.profile.edit') }}" class="btn btn-sm btn-light w-100 mt-3">
                        <i class="fs-sm me-1" data-lucide="pencil"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="180">Email</td>
                            <td>{{ $admin->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td>{{ $admin->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Login</td>
                            <td>{{ $admin->last_login_at?->format('d M Y, h:i A') ?? 'Never' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Joined</td>
                            <td>{{ $admin->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
