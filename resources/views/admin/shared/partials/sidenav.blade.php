<div class="sidenav-menu">
    <!-- Brand Logo -->
    <a class="logo" href="{{ url("/") }}">
        <span class="logo logo-light">
            <span class="logo-lg"><img alt="logo" src="/images/logo.png" /></span>
            <span class="logo-sm"><img alt="small logo" src="/images/logo-sm.png" /></span>
        </span>
        <span class="logo logo-dark">
            <span class="logo-lg"><img alt="dark logo" src="/images/logo-black.png" /></span>
            <span class="logo-sm"><img alt="small logo" src="/images/logo-sm.png" /></span>
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
        <div class="sidenav-user" id="user-profile-settings" style="background: url(/images/user-bg-pattern.svg)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="link-reset" href="#!">
                        <img alt="user-image" class="rounded-circle mb-2 avatar-md" src="/images/users/user-1.jpg" />
                        <span class="sidenav-user-name fw-bold">Geneva K.</span>
                        <span class="fs-12 fw-semibold" data-lang="user-role">Art Director</span>
                    </a>
                </div>
                <div>
                    <a aria-expanded="false" aria-haspopup="false" class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-offset="0,12" data-bs-toggle="dropdown" href="#!">
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
                        <a class="dropdown-item" href="{{ url("/auth/lock-screen") }}">
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
                    <a aria-controls="dashboards" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#dashboards">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="menu-text" data-lang="dashboards">Dashboards</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="dashboards">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/") }}">
                                    <span class="menu-text" data-lang="dashboard-ecommerce">Ecommerce</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/dashboard/projects") }}">
                                    <span class="menu-text" data-lang="dashboard-projects">Projects</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/landing") }}">
                        <span class="menu-icon"><i data-lucide="rocket"></i></span>
                        <span class="menu-text" data-lang="landing">Landing</span>
                    </a>
                </li>
                <li class="side-nav-title mt-2" data-lang="apps">Apps</li>
                <li class="side-nav-item">
                    <a aria-controls="ecommerce" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#ecommerce">
                        <span class="menu-icon"><i data-lucide="shopping-basket"></i></span>
                        <span class="menu-text" data-lang="ecommerce">Ecommerce</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="ecommerce">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a aria-controls="products" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#products">
                                    <span class="menu-text" data-lang="products">Products</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="products">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/products") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-products">Products</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/products-grid") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-products-grid">Products Grid</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/product-details") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-product-details">Product Details</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/product-add") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-product-add">Add Product</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/categories") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-categories">Categories</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="orders" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#orders">
                                    <span class="menu-text" data-lang="orders">Orders</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="orders">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/orders") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-orders">Orders</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/order-details") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-order-details">Order Details</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/order-add") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-order-add">Add/Edit Order</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/customers") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-customers">Customers</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/cart") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-cart">Cart</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="sellers" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#sellers">
                                    <span class="menu-text" data-lang="sellers">Sellers</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sellers">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/sellers") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-sellers">Sellers</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/apps/ecommerce/seller-details") }}">
                                                <span class="menu-text" data-lang="apps-ecommerce-seller-details">Sellers Details</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/refunds") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-refunds">Refunds</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/reviews") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-reviews">Reviews</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ecommerce/attributes") }}">
                                    <span class="menu-text" data-lang="apps-ecommerce-attributes">Attributes</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="crm" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#crm">
                        <span class="menu-icon"><i data-lucide="handshake"></i></span>
                        <span class="menu-text" data-lang="crm">CRM</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="crm">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/contacts") }}">
                                    <span class="menu-text" data-lang="apps-crm-contacts">Contacts</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/opportunities") }}">
                                    <span class="menu-text" data-lang="apps-crm-opportunities">Opportunities</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/deals") }}">
                                    <span class="menu-text" data-lang="apps-crm-deals">Deals</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/leads") }}">
                                    <span class="menu-text" data-lang="apps-crm-leads">Leads</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/pipeline") }}">
                                    <span class="menu-text" data-lang="apps-crm-pipeline">Pipeline</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/campaign") }}">
                                    <span class="menu-text" data-lang="apps-crm-campaign">Campaign</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/proposals") }}">
                                    <span class="menu-text" data-lang="apps-crm-proposals">Proposals</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/estimations") }}">
                                    <span class="menu-text" data-lang="apps-crm-estimations">Estimations</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/customers") }}">
                                    <span class="menu-text" data-lang="apps-crm-customers">Customers</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/crm/activities") }}">
                                    <span class="menu-text" data-lang="apps-crm-activities">Activities</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="email" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#email">
                        <span class="menu-icon"><i data-lucide="mail"></i></span>
                        <span class="menu-text" data-lang="email">Email</span>
                        <span class="badge bg-danger text-white">New</span>
                    </a>
                    <div class="collapse" id="email">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/email/inbox") }}">
                                    <span class="menu-text" data-lang="apps-email-inbox">Inbox</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/email/details") }}">
                                    <span class="menu-text" data-lang="apps-email-details">Details</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/email/compose") }}">
                                    <span class="menu-text" data-lang="apps-email-compose">Compose</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="users" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#users">
                        <span class="menu-icon"><i data-lucide="users"></i></span>
                        <span class="menu-text" data-lang="users">Users</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="users">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/contacts") }}">
                                    <span class="menu-text" data-lang="apps-users-contacts">Contacts</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/profile") }}">
                                    <span class="menu-text" data-lang="apps-users-profile">Profile</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/account-settings") }}">
                                    <span class="menu-text" data-lang="apps-users-account-settings">Account Settings</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/roles") }}">
                                    <span class="menu-text" data-lang="apps-users-roles">Roles</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/role-details") }}">
                                    <span class="menu-text" data-lang="apps-users-role-details">Role Details</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/users/permissions") }}">
                                    <span class="menu-text" data-lang="apps-users-permissions">Permissions</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/file-manager") }}">
                        <span class="menu-icon"><i data-lucide="folder-open-dot"></i></span>
                        <span class="menu-text" data-lang="apps-file-manager">File Manager</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/chat") }}">
                        <span class="menu-icon"><i data-lucide="message-square-text"></i></span>
                        <span class="menu-text" data-lang="apps-chat">Chat</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/calendar") }}">
                        <span class="menu-icon"><i data-lucide="calendar"></i></span>
                        <span class="menu-text" data-lang="apps-calendar">Calendar</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/social-feed") }}">
                        <span class="menu-icon"><i data-lucide="rss"></i></span>
                        <span class="menu-text" data-lang="apps-social-feed">Social Feed</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="invoice" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#invoice">
                        <span class="menu-icon"><i data-lucide="receipt-text"></i></span>
                        <span class="menu-text" data-lang="invoice">Invoice</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="invoice">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/invoice/list") }}">
                                    <span class="menu-text" data-lang="apps-invoice-list">Invoices</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/invoice/details") }}">
                                    <span class="menu-text" data-lang="apps-invoice-details">Single Invoice</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/invoice/create") }}">
                                    <span class="menu-text" data-lang="apps-invoice-create">New Invoice</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/outlook") }}">
                        <span class="menu-icon"><i data-lucide="layout-panel-left"></i></span>
                        <span class="menu-text" data-lang="apps-outlook">Outlook View</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="support-center" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#support-center">
                        <span class="menu-icon"><i data-lucide="headset"></i></span>
                        <span class="menu-text" data-lang="support-center">Support Center</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="support-center">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ticket/list") }}">
                                    <span class="menu-text" data-lang="apps-ticket-list">Tickets List</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ticket/details") }}">
                                    <span class="menu-text" data-lang="apps-ticket-details">Ticket Details</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/apps/ticket/create") }}">
                                    <span class="menu-text" data-lang="apps-ticket-create">New Ticket</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/apps/api-keys") }}">
                        <span class="menu-icon"><i data-lucide="key"></i></span>
                        <span class="menu-text" data-lang="apps-api-keys">API Keys</span>
                    </a>
                </li>
                <li class="side-nav-title mt-2" data-lang="custom-pages">Custom Pages</li>
                <li class="side-nav-item">
                    <a aria-controls="pages" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#pages">
                        <span class="menu-icon"><i data-lucide="book-open-text"></i></span>
                        <span class="menu-text" data-lang="pages">Pages</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="pages">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/faq") }}">
                                    <span class="menu-text" data-lang="pages-faq">FAQ</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/pricing") }}">
                                    <span class="menu-text" data-lang="pages-pricing">Pricing</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/empty") }}">
                                    <span class="menu-text" data-lang="pages-empty">Empty Page</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/timeline") }}">
                                    <span class="menu-text" data-lang="pages-timeline">Timeline</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/search-results") }}">
                                    <span class="menu-text" data-lang="pages-search-results">Search Results</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/coming-soon") }}">
                                    <span class="menu-text" data-lang="pages-coming-soon">Coming Soon</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/privacy-policy") }}">
                                    <span class="menu-text" data-lang="pages-privacy-policy">Privacy Policy</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/pages/terms-conditions") }}">
                                    <span class="menu-text" data-lang="pages-terms-conditions">Terms &amp; Conditions</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="plugins" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#plugins">
                        <span class="menu-icon"><i data-lucide="cpu"></i></span>
                        <span class="menu-text" data-lang="plugins">Plugins</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="plugins">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/sortable") }}">
                                    <span class="menu-text" data-lang="plugins-sortable">Sortable List</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/pdf-viewer") }}">
                                    <span class="menu-text" data-lang="plugins-pdf-viewer">PDF Viewer</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/i18") }}">
                                    <span class="menu-text" data-lang="plugins-i18">i18 Support</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/sweet-alerts") }}">
                                    <span class="menu-text" data-lang="plugins-sweet-alerts">Sweet Alerts</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/pass-meter") }}">
                                    <span class="menu-text" data-lang="plugins-pass-meter">Password Meter</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/clipboard") }}">
                                    <span class="menu-text" data-lang="plugins-clipboard">Clipboard</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/tree-view") }}">
                                    <span class="menu-text" data-lang="plugins-tree-view">Tree View</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/tour") }}">
                                    <span class="menu-text" data-lang="plugins-tour">Tour</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/animation") }}">
                                    <span class="menu-text" data-lang="plugins-animation">Animation</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/plugins/video-player") }}">
                                    <span class="menu-text" data-lang="plugins-video-player">Video Player</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="authentication" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#authentication">
                        <span class="menu-icon"><i data-lucide="user-lock"></i></span>
                        <span class="menu-text" data-lang="authentication">Authentication</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="authentication">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a aria-controls="auth-basic" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#auth-basic">
                                    <span class="menu-text" data-lang="auth-basic">Basic</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="auth-basic">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/sign-in") }}">
                                                <span class="menu-text" data-lang="auth-sign-in">Sign In</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/sign-up") }}">
                                                <span class="menu-text" data-lang="auth-sign-up">Sign Up</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/reset-pass") }}">
                                                <span class="menu-text" data-lang="auth-reset-pass">Reset Password</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/new-pass") }}">
                                                <span class="menu-text" data-lang="auth-new-pass">New Password</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/two-factor") }}">
                                                <span class="menu-text" data-lang="auth-two-factor">Two Factor</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/lock-screen") }}">
                                                <span class="menu-text" data-lang="auth-lock-screen">Lock Screen</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/success-mail") }}">
                                                <span class="menu-text" data-lang="auth-success-mail">Success Mail</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/login-pin") }}">
                                                <span class="menu-text" data-lang="auth-login-pin">Login with PIN</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth/delete-account") }}">
                                                <span class="menu-text" data-lang="auth-delete-account">Delete Account</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="auth-split" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#auth-split">
                                    <span class="menu-text" data-lang="auth-split">Split</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="auth-split">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/sign-in") }}">
                                                <span class="menu-text" data-lang="auth-split-sign-in">Sign In</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/sign-up") }}">
                                                <span class="menu-text" data-lang="auth-split-sign-up">Sign Up</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/reset-pass") }}">
                                                <span class="menu-text" data-lang="auth-split-reset-pass">Reset Password</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/new-pass") }}">
                                                <span class="menu-text" data-lang="auth-split-new-pass">New Password</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/two-factor") }}">
                                                <span class="menu-text" data-lang="auth-split-two-factor">Two Factor</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/lock-screen") }}">
                                                <span class="menu-text" data-lang="auth-split-lock-screen">Lock Screen</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/success-mail") }}">
                                                <span class="menu-text" data-lang="auth-split-success-mail">Success Mail</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/login-pin") }}">
                                                <span class="menu-text" data-lang="auth-split-login-pin">Login with PIN</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/auth-split/delete-account") }}">
                                                <span class="menu-text" data-lang="auth-split-delete-account">Delete Account</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="error-pages" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#error-pages">
                        <span class="menu-icon"><i data-lucide="alert-triangle"></i></span>
                        <span class="menu-text" data-lang="error-pages">Error Pages</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="error-pages">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/400") }}">
                                    <span class="menu-text" data-lang="error-400">400 Bad Request</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/401") }}">
                                    <span class="menu-text" data-lang="error-401">401 Unauthorized</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/403") }}">
                                    <span class="menu-text" data-lang="error-403">403 Forbidden</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/404") }}">
                                    <span class="menu-text" data-lang="error-404">404 Not Found</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/408") }}">
                                    <span class="menu-text" data-lang="error-408">408 Request Timeout</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/500") }}">
                                    <span class="menu-text" data-lang="error-500">500 Internal Server</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/error/maintenance") }}">
                                    <span class="menu-text" data-lang="error-maintenance">Maintenance</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-title mt-2" data-lang="layouts">Layouts</li>
                <li class="side-nav-item">
                    <a aria-controls="layout-options" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#layout-options">
                        <span class="menu-icon"><i data-lucide="layout-panel-left"></i></span>
                        <span class="menu-text" data-lang="layout-options">Layout Options</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="layout-options">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/scrollable") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-scrollable">Scrollable</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/compact") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-compact">Compact</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/boxed") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-boxed">Boxed</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/horizontal") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-horizontal">Horizontal</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/preloader") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-preloader">Preloader</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="sidebars" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#sidebars">
                        <span class="menu-icon"><i data-lucide="panel-left-dashed"></i></span>
                        <span class="menu-text" data-lang="sidebars">Sidebars</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebars">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/dark") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-dark">Dark Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/gradient") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-gradient">Gradient Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/gray") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-gray">Gray Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/image") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-image">Image Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/compact") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-compact">Compact Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/on-hover") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-on-hover">On Hover Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/on-hover-active") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-on-hover-active">On Hover Active</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/offcanvas") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-offcanvas">Offcanvas Menu</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/no-icons") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-no-icons">No Icons with Lines</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/sidebar/with-lines") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-sidebar-with-lines">Sidebar with Lines</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="topbar" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#topbar">
                        <span class="menu-icon"><i data-lucide="panel-top-dashed"></i></span>
                        <span class="menu-text" data-lang="topbar">Topbar</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="topbar">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/topbar/light") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-topbar-light">Light Topbar</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/topbar/gray") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-topbar-gray">Gray Topbar</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/layouts/topbar/gradient") }}" target="_blank">
                                    <span class="menu-text" data-lang="layouts-topbar-gradient">Gradient Topbar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-title mt-2" data-lang="components">Components</li>
                <li class="side-nav-item">
                    <a aria-controls="base-ui" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#base-ui">
                        <span class="menu-icon"><i data-lucide="component"></i></span>
                        <span class="menu-text" data-lang="base-ui">Base UI</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="base-ui">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/accordions") }}">
                                    <span class="menu-text" data-lang="ui-accordions">Accordions</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/alerts") }}">
                                    <span class="menu-text" data-lang="ui-alerts">Alerts</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/images") }}">
                                    <span class="menu-text" data-lang="ui-images">Images</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/badges") }}">
                                    <span class="menu-text" data-lang="ui-badges">Badges</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/breadcrumb") }}">
                                    <span class="menu-text" data-lang="ui-breadcrumb">Breadcrumb</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/buttons") }}">
                                    <span class="menu-text" data-lang="ui-buttons">Buttons</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/cards") }}">
                                    <span class="menu-text" data-lang="ui-cards">Cards</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/carousel") }}">
                                    <span class="menu-text" data-lang="ui-carousel">Carousel</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/collapse") }}">
                                    <span class="menu-text" data-lang="ui-collapse">Collapse</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/colors") }}">
                                    <span class="menu-text" data-lang="ui-colors">Colors</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/dropdowns") }}">
                                    <span class="menu-text" data-lang="ui-dropdowns">Dropdowns</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/videos") }}">
                                    <span class="menu-text" data-lang="ui-videos">Videos</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/grid") }}">
                                    <span class="menu-text" data-lang="ui-grid">Grid Options</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/links") }}">
                                    <span class="menu-text" data-lang="ui-links">Links</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/list-group") }}">
                                    <span class="menu-text" data-lang="ui-list-group">List Group</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/modals") }}">
                                    <span class="menu-text" data-lang="ui-modals">Modals</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/notifications") }}">
                                    <span class="menu-text" data-lang="ui-notifications">Notifications</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/offcanvas") }}">
                                    <span class="menu-text" data-lang="ui-offcanvas">Offcanvas</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/placeholders") }}">
                                    <span class="menu-text" data-lang="ui-placeholders">Placeholders</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/pagination") }}">
                                    <span class="menu-text" data-lang="ui-pagination">Pagination</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/popovers") }}">
                                    <span class="menu-text" data-lang="ui-popovers">Popovers</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/progress") }}">
                                    <span class="menu-text" data-lang="ui-progress">Progress</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/scrollspy") }}">
                                    <span class="menu-text" data-lang="ui-scrollspy">Scrollspy</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/spinners") }}">
                                    <span class="menu-text" data-lang="ui-spinners">Spinners</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/tabs") }}">
                                    <span class="menu-text" data-lang="ui-tabs">Tabs</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/tooltips") }}">
                                    <span class="menu-text" data-lang="ui-tooltips">Tooltips</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/typography") }}">
                                    <span class="menu-text" data-lang="ui-typography">Typography</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/ui/utilities") }}">
                                    <span class="menu-text" data-lang="ui-utilities">Utilities</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link" href="{{ url("/widgets") }}">
                        <span class="menu-icon"><i data-lucide="layers"></i></span>
                        <span class="menu-text" data-lang="widgets">Widgets</span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="charts" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#charts">
                        <span class="menu-icon"><i data-lucide="chart-pie"></i></span>
                        <span class="menu-text" data-lang="charts">Charts</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="charts">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a aria-controls="apex-charts" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#apex-charts">
                                    <span class="menu-text" data-lang="apex-charts">Apex Charts</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="apex-charts">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/area") }}">
                                                <span class="menu-text" data-lang="charts-apex-area">Area</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/bar") }}">
                                                <span class="menu-text" data-lang="charts-apex-bar">Bar</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/bubble") }}">
                                                <span class="menu-text" data-lang="charts-apex-bubble">Bubble</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/candlestick") }}">
                                                <span class="menu-text" data-lang="charts-apex-candlestick">Candlestick</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/column") }}">
                                                <span class="menu-text" data-lang="charts-apex-column">Column</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/heatmap") }}">
                                                <span class="menu-text" data-lang="charts-apex-heatmap">Heatmap</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/line") }}">
                                                <span class="menu-text" data-lang="charts-apex-line">Line</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/mixed") }}">
                                                <span class="menu-text" data-lang="charts-apex-mixed">Mixed</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/timeline") }}">
                                                <span class="menu-text" data-lang="charts-apex-timeline">Timeline</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/boxplot") }}">
                                                <span class="menu-text" data-lang="charts-apex-boxplot">Boxplot</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/treemap") }}">
                                                <span class="menu-text" data-lang="charts-apex-treemap">Treemap</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/pie") }}">
                                                <span class="menu-text" data-lang="charts-apex-pie">Pie</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/radar") }}">
                                                <span class="menu-text" data-lang="charts-apex-radar">Radar</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/radialbar") }}">
                                                <span class="menu-text" data-lang="charts-apex-radialbar">RadialBar</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/scatter") }}">
                                                <span class="menu-text" data-lang="charts-apex-scatter">Scatter</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/polar-area") }}">
                                                <span class="menu-text" data-lang="charts-apex-polar-area">Polar Area</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/sparklines") }}">
                                                <span class="menu-text" data-lang="charts-apex-sparklines">Sparklines</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/range") }}">
                                                <span class="menu-text" data-lang="charts-apex-range">Range</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/funnel") }}">
                                                <span class="menu-text" data-lang="charts-apex-funnel">Funnel</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/apex/slope") }}">
                                                <span class="menu-text" data-lang="charts-apex-slope">Slope</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="chartjs" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#chartjs">
                                    <span class="menu-text" data-lang="chartjs">Chart Js</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="chartjs">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/chartjs/area") }}">
                                                <span class="menu-text" data-lang="charts-chartjs-area">Area</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/chartjs/bar") }}">
                                                <span class="menu-text" data-lang="charts-chartjs-bar">Bar</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/chartjs/line") }}">
                                                <span class="menu-text" data-lang="charts-chartjs-line">Line</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/charts/chartjs/other") }}">
                                                <span class="menu-text" data-lang="charts-chartjs-other">Other</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="forms" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#forms">
                        <span class="menu-icon"><i data-lucide="clipboard-type"></i></span>
                        <span class="menu-text" data-lang="forms">Forms</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="forms">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/elements") }}">
                                    <span class="menu-text" data-lang="form-elements">Basic Elements</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/pickers") }}">
                                    <span class="menu-text" data-lang="form-pickers">Pickers</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/select") }}">
                                    <span class="menu-text" data-lang="form-select">Select</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/validation") }}">
                                    <span class="menu-text" data-lang="form-validation">Validation</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/wizard") }}">
                                    <span class="menu-text" data-lang="form-wizard">Wizard</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/fileuploads") }}">
                                    <span class="menu-text" data-lang="form-fileuploads">File Uploads</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/text-editors") }}">
                                    <span class="menu-text" data-lang="form-text-editors">Text Editors</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/range-slider") }}">
                                    <span class="menu-text" data-lang="form-range-slider">Range Slider</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/layout") }}">
                                    <span class="menu-text" data-lang="form-layout">Layouts</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/form/other-plugin") }}">
                                    <span class="menu-text" data-lang="form-other-plugin">Other Plugins</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="tables" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#tables">
                        <span class="menu-icon"><i data-lucide="table-2"></i></span>
                        <span class="menu-text" data-lang="tables">Tables</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="tables">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/tables/static") }}">
                                    <span class="menu-text" data-lang="tables-static">Static Tables</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/tables/custom") }}">
                                    <span class="menu-text" data-lang="tables-custom">Custom Tables</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="datatables" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#datatables">
                                    <span class="menu-text" data-lang="datatables">DataTables</span>
                                    <span class="badge bg-success text-white">15</span>
                                </a>
                                <div class="collapse" id="datatables">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/basic") }}">
                                                <span class="menu-text" data-lang="tables-datatables-basic">Basic</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/export-data") }}">
                                                <span class="menu-text" data-lang="tables-datatables-export-data">Export Data</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/select") }}">
                                                <span class="menu-text" data-lang="tables-datatables-select">Select</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/ajax") }}">
                                                <span class="menu-text" data-lang="tables-datatables-ajax">Ajax</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/javascript") }}">
                                                <span class="menu-text" data-lang="tables-datatables-javascript">Javascript Source</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/rendering") }}">
                                                <span class="menu-text" data-lang="tables-datatables-rendering">Data Rendering</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/scroll") }}">
                                                <span class="menu-text" data-lang="tables-datatables-scroll">Scroll</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/fixed-columns") }}">
                                                <span class="menu-text" data-lang="tables-datatables-fixed-columns">Fixed Columns</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/fixed-header") }}">
                                                <span class="menu-text" data-lang="tables-datatables-fixed-header">Fixed Header</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/columns") }}">
                                                <span class="menu-text" data-lang="tables-datatables-columns">Show &amp; Hide Column</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/child-rows") }}">
                                                <span class="menu-text" data-lang="tables-datatables-child-rows">Child Rows</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/column-searching") }}">
                                                <span class="menu-text" data-lang="tables-datatables-column-searching">Column Searching</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/range-search") }}">
                                                <span class="menu-text" data-lang="tables-datatables-range-search">Range Search</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/rows-add") }}">
                                                <span class="menu-text" data-lang="tables-datatables-rows-add">Add Rows</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="{{ url("/tables/datatables/checkbox-select") }}">
                                                <span class="menu-text" data-lang="tables-datatables-checkbox-select">Checkbox Select</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="icons" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#icons">
                        <span class="menu-icon"><i data-lucide="cannabis"></i></span>
                        <span class="menu-text" data-lang="icons">Icons</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="icons">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/icons/tabler") }}">
                                    <span class="menu-text" data-lang="icons-tabler">Tabler</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/icons/lucide") }}">
                                    <span class="menu-text" data-lang="icons-lucide">Lucide</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/icons/flags") }}">
                                    <span class="menu-text" data-lang="icons-flags">Flags</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a aria-controls="maps" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#maps">
                        <span class="menu-icon"><i data-lucide="map"></i></span>
                        <span class="menu-text" data-lang="maps">Maps</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="maps">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/maps/vector") }}">
                                    <span class="menu-text" data-lang="maps-vector">Vector Maps</span>
                                </a>
                            </li>
                            <li class="side-nav-item">
                                <a class="side-nav-link" href="{{ url("/maps/leaflet") }}">
                                    <span class="menu-text" data-lang="maps-leaflet">Leaflet Maps</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-title mt-2" data-lang="menu-items">Menu Items</li>
                <li class="side-nav-item">
                    <a aria-controls="menu-levels" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#menu-levels">
                        <span class="menu-icon"><i data-lucide="list-tree"></i></span>
                        <span class="menu-text" data-lang="menu-levels">Menu Levels</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="menu-levels">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a aria-controls="second-level" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#second-level">
                                    <span class="menu-text" data-lang="second-level">Second Level</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="second-level">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="#">
                                                <span class="menu-text" data-lang="menu-item-1">Item 2.1</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="#">
                                                <span class="menu-text" data-lang="menu-item-2">Item 2.2</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="side-nav-item">
                                <a aria-controls="second-level-2" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#second-level-2">
                                    <span class="menu-text" data-lang="second-level-2">Second Level</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="second-level-2">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item">
                                            <a class="side-nav-link" href="#">
                                                <span class="menu-text" data-lang="menu-item-3">Item 2.1</span>
                                            </a>
                                        </li>
                                        <li class="side-nav-item">
                                            <a aria-controls="menu-item-4" aria-expanded="false" class="side-nav-link" data-bs-toggle="collapse" href="#menu-item-4">
                                                <span class="menu-text" data-lang="menu-item-4">Item 2.2</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="menu-item-4">
                                                <ul class="sub-menu">
                                                    <li class="side-nav-item">
                                                        <a class="side-nav-link" href="#">
                                                            <span class="menu-text" data-lang="menu-item-5">Item 3.1</span>
                                                        </a>
                                                    </li>
                                                    <li class="side-nav-item">
                                                        <a class="side-nav-link" href="#">
                                                            <span class="menu-text" data-lang="menu-item-6">Item 3.2</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a class="side-nav-link disabled" href="#">
                        <span class="menu-icon"><i data-lucide="ban"></i></span>
                        <span class="menu-text" data-lang="disabled-menu">Disabled Menu</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Sidenav Menu End -->
