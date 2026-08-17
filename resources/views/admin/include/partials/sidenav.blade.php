<div class="sidenav-menu">
    <!-- Brand Logo -->
    <a class="logo" href="{{ route('admin.dashboard') }}">
        <span class="logo logo-light">
            <span class="logo-lg"><img alt="logo" src="{{ asset('images/logo.png') }}" /></span>
            <span class="logo-sm"><img alt="small logo" src="{{ asset('images/logo-sm.png') }}" /></span>
        </span>
        <span class="logo logo-dark">
            <span class="logo-lg"><img alt="dark logo" src="{{ asset('images/logo-black.png') }}" /></span>
            <span class="logo-sm"><img alt="small logo" src="{{ asset('images/logo-sm.png') }}" /></span>
        </span>
    </a>
    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <span class="btn-on-hover-icon"></span>
    </button>
    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="align-middle" data-lucide="menu"></i>
    </button>
    <div class="scrollbar" data-simplebar="">
        <div class="sidenav-user" id="user-profile-settings"
            style="background: url({{ asset('images/user-bg-pattern.svg') }})">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="link-reset" href="#!">
                        <img alt="user-image" class="rounded-circle mb-2 avatar-md"
                            src="{{ asset('images/users/user-1.jpg') }}" />
                        <span class="sidenav-user-name fw-bold">Geneva K.</span>
                        <span class="fs-12 fw-semibold" data-lang="user-role">Art Director</span>
                    </a>
                </div>
                <div>
                    <a aria-expanded="false" aria-haspopup="false"
                        class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-offset="0,12"
                        data-bs-toggle="dropdown" href="#!">
                        <i class="fs-24 align-middle ms-1" data-lucide="settings"></i>
                    </a>
                    <div class="dropdown-menu">
                        <!-- Header -->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome back!</h6>
                        </div>
                        <!-- My Profile -->
                        <a class="dropdown-item" href="#!">
                            <i class="me-1 fs-lg align-middle" data-lucide="circle-user-round"></i>
                            <span class="align-middle">Profile</span>
                        </a>
                        <!-- Settings -->
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="me-1 fs-lg align-middle" data-lucide="bolt"></i>
                            <span class="align-middle">Account Settings</span>
                        </a>
                        <!-- Lock -->
                        <a class="dropdown-item" href="{{ url('/auth/lock-screen') }}">
                            <i class="me-1 fs-lg align-middle" data-lucide="lock-keyhole"></i>
                            <span class="align-middle">Lock Screen</span>
                        </a>
                        <!-- Logout -->
                        <a class="dropdown-item text-danger fw-semibold" href="javascript:void(0);">
                            <i class="me-1 fs-lg align-middle" data-lucide="log-out"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--- Sidenav Menu -->
        <div id="sidenav-menu">
            <ul class="side-nav">
                <li class="side-nav-title mt-2" data-lang="main">Main</li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.dashboard') }}">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="menu-text" data-lang="landing">Dashboard</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('visitor.index') }}">
                        <span class="menu-icon"><i data-lucide="rocket"></i></span>
                        <span class="menu-text" data-lang="landing">Website</span>
                    </a>
                </li>
                <li class="side-nav-title mt-2" data-lang="ecommerce">Ecommerce</li>
                <li class="side-nav-item">
                    <a aria-controls="products" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse"
                        href="#products">
                        <span class="menu-icon"><i data-lucide="shopping-basket"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-products">Products</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="products">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.ecommerce.product.index') }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-products">Products</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.ecommerce.category.index') }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-categories">Categories</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.ecommerce.brand.index') }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-order-add">Brand</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.order.index') }}">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-text" data-lang="orders">Orders</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.customer.index') }}">
                        <span class="menu-icon"><i data-lucide="users-round"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-customers">Customers</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.cart.index') }}">
                        <span class="menu-icon"><i data-lucide="shopping-bag"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-cart">Cart</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.refund.index') }}">
                        <span class="menu-icon"><i data-lucide="dollar-sign"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-refunds">Refunds</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.coupon.index') }}">
                        <span class="menu-icon"><i data-lucide="ticket-percent"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-coupons">Coupons</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.review.index') }}">
                        <span class="menu-icon"><i data-lucide="star"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-reviews">Reviews</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ route('admin.ecommerce.attribute.index') }}">
                        <span class="menu-icon"><i data-lucide="columns-3-cog"></i></span>
                        <span class="menu-text" data-lang="apps-ecommerce-attributes">Attributes</span>
                    </a>
                </li>
                <li class="side-nav-title mt-2" data-lang="ecommerce">CRM</li>
                <li class="side-nav-item">
                    <a aria-controls="email" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse"
                        href="#email">
                        <span class="menu-icon"><i data-lucide="mail"></i></span>
                        <span class="menu-text" data-lang="email">Email</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="email">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url('/apps/email/inbox') }}">
                                    <span class="menu-text" data-lang="apps-email-inbox">Inbox</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url('/apps/email/details') }}">
                                    <span class="menu-text" data-lang="apps-email-details">Details</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url('/apps/email/compose') }}">
                                    <span class="menu-text" data-lang="apps-email-compose">Compose</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url('/apps/chat') }}">
                        <span class="menu-icon"><i data-lucide="message-square-text"></i></span>
                        <span class="menu-text" data-lang="apps-chat">Chat</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url('/apps/social-feed') }}">
                        <span class="menu-icon"><i data-lucide="rss"></i></span>
                        <span class="menu-text" data-lang="apps-social-feed">Social Feed</span>
                    </a>
                </li>
                <li class="side-nav-title mt-2" data-lang="ecommerce">Settings</li>
                <li class="side-nav-item">
                    <a aria-controls="users" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse"
                        href="#users">
                        <span class="menu-icon"><i data-lucide="users"></i></span>
                        <span class="menu-text" data-lang="users">Users</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="users">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.users.index') }}">
                                    <span class="menu-text" data-lang="apps-users-contacts">Users</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.users.roles.index') }}">
                                    <span class="menu-text" data-lang="apps-users-roles">Roles</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.users.permissions.index') }}">
                                    <span class="menu-text" data-lang="apps-users-permissions">Permissions</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ route('admin.users.profile.show') }}">
                                    <span class="menu-text" data-lang="apps-users-profile">Profile</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-title mt-2" data-lang="ecommerce">System</li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url('/apps/file-manager') }}">
                        <span class="menu-icon"><i data-lucide="folder-open-dot"></i></span>
                        <span class="menu-text" data-lang="apps-file-manager">File Manager</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Sidenav Menu End -->
