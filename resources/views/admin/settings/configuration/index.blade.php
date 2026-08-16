@extends('admin.include.vertical', ['title' => 'Configuration'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Settings', 'title' => 'Configuration'])

    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Company</h4>
                            <p class="mb-0">Set company information and details.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Design</h4>
                            <p class="mb-0">Set logo and favicon icon for admin panel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Email</h4>
                            <p class="mb-0">Set email configuration for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-bell"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Notifications</h4>
                            <p class="mb-0">Set notification preferences for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Google Captcha</h4>
                            <p class="mb-0">Set Google Captcha configuration for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Shipping </h4>
                            <p class="mb-0">Set shipping configuration for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Payment Methods</h4>
                            <p class="mb-0">Set payment method configurations for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-bell"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Inventory</h4>
                            <p class="mb-0">Set inventory management configurations for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Invoice Settings</h4>
                            <p class="mb-0">Set invoice configuration for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-paint"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Order Settings </h4>
                            <p class="mb-0">Set order configuration for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Taxes</h4>
                            <p class="mb-0">Set tax configurations for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 my-3">
                        <div class="avatar-xxl flex-shrink-0">
                            <span class="avatar-title text-bg-success bg-opacity-90 rounded-circle fs-48">
                                <i class="ti ti-bell"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">Inventory</h4>
                            <p class="mb-0">Set inventory management configurations for the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
@endsection
