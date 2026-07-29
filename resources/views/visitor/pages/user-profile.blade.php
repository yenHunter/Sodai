@extends('visitor.layout.app', ['title' => 'Sodai - My Profile', 'bodyClass' => 'shop_page'])

@section('styles')
@endsection

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">User Profile</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content ec-vendor-uploads ec-user-account section-space-p">
        <div class="container">
            <div class="row">
                @include('visitor.partials.account-sidebar')

                <div class="ec-shop-rightside col-lg-9 col-md-12">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <div class="ec-vendor-dashboard-card ec-vendor-setting-card">
                        <div class="ec-vendor-card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ec-vendor-block-profile">
                                        <div class="ec-vendor-block-img space-bottom-30">
                                            <div class="ec-vendor-block-bg">
                                                <a href="#" class="btn btn-lg btn-primary"
                                                    data-bs-toggle="modal" data-bs-target="#edit_modal">Edit Detail</a>
                                            </div>
                                            <div class="ec-vendor-block-detail">
                                                @if ($customer->avatar)
                                                    <img class="v-img" src="{{ Storage::url($customer->avatar) }}" alt="{{ $customer->name }}">
                                                @else
                                                    <img class="v-img" src="{{ asset('visitor/images/user/1.jpg') }}" alt="{{ $customer->name }}">
                                                @endif
                                                <h5 class="name">{{ $customer->name }}</h5>
                                                <p>Member since {{ $customer->created_at->format('M Y') }}</p>
                                            </div>
                                            <p>Hello <span>{{ $customer->name }}!</span></p>
                                            <p>From your account you can easily view and track orders. You can manage and
                                                change your account information like address, contact information and
                                                history of orders.</p>
                                        </div>

                                        <h5>Account Information</h5>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-email space-bottom-30">
                                                    <h6>E-mail address <a href="javascript:void(0)"
                                                            data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="fi-rr-edit"></i></a></h6>
                                                    <ul>
                                                        <li><strong>Email: </strong>{{ $customer->email }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-contact space-bottom-30">
                                                    <h6>Contact number <a href="javascript:void(0)"
                                                            data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="fi-rr-edit"></i></a></h6>
                                                    <ul>
                                                        <li><strong>Phone: </strong>{{ $customer->phone ?? 'Not provided' }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-address mar-b-30">
                                                    <h6>Default Address <a href="{{ route('account.addresses.index') }}"><i class="fi-rr-edit"></i></a></h6>
                                                    <ul>
                                                        @php $default = $addresses->firstWhere('is_default', true); @endphp
                                                        @if ($default)
                                                            <li><strong>{{ $default->label }}: </strong>{{ $default->address_line_1 }}, {{ $default->city }}, {{ $default->state }} - {{ $default->zip_code }}, {{ $default->country }}</li>
                                                        @else
                                                            <li>No address added yet. <a href="{{ route('account.addresses.index') }}">Add one</a>.</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="ec-vendor-detail-block ec-vendor-block-address">
                                                    <h6>Saved Addresses <a href="{{ route('account.addresses.index') }}"><i class="fi-rr-edit"></i></a></h6>
                                                    <ul>
                                                        <li>{{ $addresses->count() }} address(es) on file. <a href="{{ route('account.addresses.index') }}">Manage</a>.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ec-vendor-dashboard-card ec-vendor-setting-card">
                        <div class="ec-vendor-card-header">
                            <h5>Change Password</h5>
                        </div>
                        <div class="ec-vendor-card-body">
                            <form action="{{ route('account.password') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="password" name="current_password" class="form-control" placeholder="Current Password" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="password" name="password" class="form-control" placeholder="New Password" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
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
@endsection

@section('scripts')
@endsection