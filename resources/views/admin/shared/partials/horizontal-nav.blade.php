<header class="topnav">
    <nav class="navbar navbar-expand-lg">
        <nav class="container-fluid">
            <div class="collapse navbar-collapse" id="topnav-menu">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-main" role="button">
                            <span class="menu-icon"><i class="fs-xl" data-lucide="layout-dashboard"></i></span>
                            <span class="menu-text" data-lang="main">Main</span>
                            <div class="menu-arrow"></div>
                        </a>
                        <div aria-labelledby="topnav-main" class="dropdown-menu">
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-dashboards" role="button">
                                    <span data-lang="dashboards">Dashboards</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-dashboards" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/") }}"><span data-lang="dashboard-ecommerce">Ecommerce</span></a>
                                    <a class="dropdown-item" href="{{ url("/dashboard/projects") }}"><span data-lang="dashboard-projects">Projects</span></a>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ url("/landing") }}"><span data-lang="landing">Landing</span></a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-apps" role="button">
                            <span class="menu-icon"><i class="fs-xl" data-lucide="layout-grid"></i></span>
                            <span class="menu-text" data-lang="apps">Apps</span>
                            <div class="menu-arrow"></div>
                        </a>
                        <div aria-labelledby="topnav-apps" class="dropdown-menu">
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-ecommerce" role="button">
                                    <span data-lang="ecommerce">Ecommerce</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-ecommerce" class="dropdown-menu">
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-products" role="button">
                                            <span data-lang="products">Products</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-products" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/products") }}"><span data-lang="apps-ecommerce-products">Products</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/products-grid") }}"><span data-lang="apps-ecommerce-products-grid">Products Grid</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/product-details") }}"><span data-lang="apps-ecommerce-product-details">Product Details</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/product-add") }}"><span data-lang="apps-ecommerce-product-add">Add Product</span></a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/categories") }}"><span data-lang="apps-ecommerce-categories">Categories</span></a>
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-orders" role="button">
                                            <span data-lang="orders">Orders</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-orders" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/orders") }}"><span data-lang="apps-ecommerce-orders">Orders</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/order-details") }}"><span data-lang="apps-ecommerce-order-details">Order Details</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/order-add") }}"><span data-lang="apps-ecommerce-order-add">Add/Edit Order</span></a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/customers") }}"><span data-lang="apps-ecommerce-customers">Customers</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/cart") }}"><span data-lang="apps-ecommerce-cart">Cart</span></a>
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-sellers" role="button">
                                            <span data-lang="sellers">Sellers</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-sellers" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/sellers") }}"><span data-lang="apps-ecommerce-sellers">Sellers</span></a>
                                            <a class="dropdown-item" href="{{ url("/apps/ecommerce/seller-details") }}"><span data-lang="apps-ecommerce-seller-details">Sellers Details</span></a>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/refunds") }}"><span data-lang="apps-ecommerce-refunds">Refunds</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/reviews") }}"><span data-lang="apps-ecommerce-reviews">Reviews</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/ecommerce/attributes") }}"><span data-lang="apps-ecommerce-attributes">Attributes</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-crm" role="button">
                                    <span data-lang="crm">CRM</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-crm" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/apps/crm/contacts") }}"><span data-lang="apps-crm-contacts">Contacts</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/opportunities") }}"><span data-lang="apps-crm-opportunities">Opportunities</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/deals") }}"><span data-lang="apps-crm-deals">Deals</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/leads") }}"><span data-lang="apps-crm-leads">Leads</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/pipeline") }}"><span data-lang="apps-crm-pipeline">Pipeline</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/campaign") }}"><span data-lang="apps-crm-campaign">Campaign</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/proposals") }}"><span data-lang="apps-crm-proposals">Proposals</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/estimations") }}"><span data-lang="apps-crm-estimations">Estimations</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/customers") }}"><span data-lang="apps-crm-customers">Customers</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/crm/activities") }}"><span data-lang="apps-crm-activities">Activities</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-email" role="button">
                                    <span data-lang="email">Email</span><span class="badge bg-danger text-white">New</span>
                                </a>
                                <div aria-labelledby="topnav-submenu-email" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/apps/email/inbox") }}"><span data-lang="apps-email-inbox">Inbox</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/email/details") }}"><span data-lang="apps-email-details">Details</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/email/compose") }}"><span data-lang="apps-email-compose">Compose</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-users" role="button">
                                    <span data-lang="users">Users</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-users" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/apps/users/contacts") }}"><span data-lang="apps-users-contacts">Contacts</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/users/profile") }}"><span data-lang="apps-users-profile">Profile</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/users/account-settings") }}"><span data-lang="apps-users-account-settings">Account Settings</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/users/roles") }}"><span data-lang="apps-users-roles">Roles</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/users/role-details") }}"><span data-lang="apps-users-role-details">Role Details</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/users/permissions") }}"><span data-lang="apps-users-permissions">Permissions</span></a>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ url("/apps/file-manager") }}"><span data-lang="apps-file-manager">File Manager</span></a>
                            <a class="dropdown-item" href="{{ url("/apps/chat") }}"><span data-lang="apps-chat">Chat</span></a>
                            <a class="dropdown-item" href="{{ url("/apps/calendar") }}"><span data-lang="apps-calendar">Calendar</span></a>
                            <a class="dropdown-item" href="{{ url("/apps/social-feed") }}"><span data-lang="apps-social-feed">Social Feed</span></a>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-invoice" role="button">
                                    <span data-lang="invoice">Invoice</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-invoice" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/apps/invoice/list") }}"><span data-lang="apps-invoice-list">Invoices</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/invoice/details") }}"><span data-lang="apps-invoice-details">Single Invoice</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/invoice/create") }}"><span data-lang="apps-invoice-create">New Invoice</span></a>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ url("/apps/outlook") }}"><span data-lang="apps-outlook">Outlook View</span></a>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-support-center" role="button">
                                    <span data-lang="support-center">Support Center</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-support-center" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/apps/ticket/list") }}"><span data-lang="apps-ticket-list">Tickets List</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/ticket/details") }}"><span data-lang="apps-ticket-details">Ticket Details</span></a>
                                    <a class="dropdown-item" href="{{ url("/apps/ticket/create") }}"><span data-lang="apps-ticket-create">New Ticket</span></a>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ url("/apps/api-keys") }}"><span data-lang="apps-api-keys">API Keys</span></a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-custom-pages" role="button">
                            <span class="menu-icon"><i class="fs-xl" data-lucide="book-open-text"></i></span>
                            <span class="menu-text" data-lang="custom-pages">Custom Pages</span>
                            <div class="menu-arrow"></div>
                        </a>
                        <div aria-labelledby="topnav-custom-pages" class="dropdown-menu">
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-pages" role="button">
                                    <span data-lang="pages">Pages</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-pages" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/pages/faq") }}"><span data-lang="pages-faq">FAQ</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/pricing") }}"><span data-lang="pages-pricing">Pricing</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/empty") }}"><span data-lang="pages-empty">Empty Page</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/timeline") }}"><span data-lang="pages-timeline">Timeline</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/search-results") }}"><span data-lang="pages-search-results">Search Results</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/coming-soon") }}"><span data-lang="pages-coming-soon">Coming Soon</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/privacy-policy") }}"><span data-lang="pages-privacy-policy">Privacy Policy</span></a>
                                    <a class="dropdown-item" href="{{ url("/pages/terms-conditions") }}"><span data-lang="pages-terms-conditions">Terms &amp; Conditions</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-plugins" role="button">
                                    <span data-lang="plugins">Plugins</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-plugins" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/plugins/sortable") }}"><span data-lang="plugins-sortable">Sortable List</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/pdf-viewer") }}"><span data-lang="plugins-pdf-viewer">PDF Viewer</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/i18") }}"><span data-lang="plugins-i18">i18 Support</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/sweet-alerts") }}"><span data-lang="plugins-sweet-alerts">Sweet Alerts</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/pass-meter") }}"><span data-lang="plugins-pass-meter">Password Meter</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/clipboard") }}"><span data-lang="plugins-clipboard">Clipboard</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/tree-view") }}"><span data-lang="plugins-tree-view">Tree View</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/tour") }}"><span data-lang="plugins-tour">Tour</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/animation") }}"><span data-lang="plugins-animation">Animation</span></a>
                                    <a class="dropdown-item" href="{{ url("/plugins/video-player") }}"><span data-lang="plugins-video-player">Video Player</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-authentication" role="button">
                                    <span data-lang="authentication">Authentication</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-authentication" class="dropdown-menu">
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-auth-basic" role="button">
                                            <span data-lang="auth-basic">Basic</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-auth-basic" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/auth/sign-in") }}"><span data-lang="auth-sign-in">Sign In</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/sign-up") }}"><span data-lang="auth-sign-up">Sign Up</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/reset-pass") }}"><span data-lang="auth-reset-pass">Reset Password</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/new-pass") }}"><span data-lang="auth-new-pass">New Password</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/two-factor") }}"><span data-lang="auth-two-factor">Two Factor</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/lock-screen") }}"><span data-lang="auth-lock-screen">Lock Screen</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/success-mail") }}"><span data-lang="auth-success-mail">Success Mail</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/login-pin") }}"><span data-lang="auth-login-pin">Login with PIN</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth/delete-account") }}"><span data-lang="auth-delete-account">Delete Account</span></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-auth-split" role="button">
                                            <span data-lang="auth-split">Split</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-auth-split" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/auth-split/sign-in") }}"><span data-lang="auth-split-sign-in">Sign In</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/sign-up") }}"><span data-lang="auth-split-sign-up">Sign Up</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/reset-pass") }}"><span data-lang="auth-split-reset-pass">Reset Password</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/new-pass") }}"><span data-lang="auth-split-new-pass">New Password</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/two-factor") }}"><span data-lang="auth-split-two-factor">Two Factor</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/lock-screen") }}"><span data-lang="auth-split-lock-screen">Lock Screen</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/success-mail") }}"><span data-lang="auth-split-success-mail">Success Mail</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/login-pin") }}"><span data-lang="auth-split-login-pin">Login with PIN</span></a>
                                            <a class="dropdown-item" href="{{ url("/auth-split/delete-account") }}"><span data-lang="auth-split-delete-account">Delete Account</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-error-pages" role="button">
                                    <span data-lang="error-pages">Error Pages</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-error-pages" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/error/400") }}"><span data-lang="error-400">400 Bad Request</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/401") }}"><span data-lang="error-401">401 Unauthorized</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/403") }}"><span data-lang="error-403">403 Forbidden</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/404") }}"><span data-lang="error-404">404 Not Found</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/408") }}"><span data-lang="error-408">408 Request Timeout</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/500") }}"><span data-lang="error-500">500 Internal Server</span></a>
                                    <a class="dropdown-item" href="{{ url("/error/maintenance") }}"><span data-lang="error-maintenance">Maintenance</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-layouts" role="button">
                            <span class="menu-icon"><i class="fs-xl" data-lucide="table-2"></i></span>
                            <span class="menu-text" data-lang="layouts">Layouts</span>
                            <div class="menu-arrow"></div>
                        </a>
                        <div aria-labelledby="topnav-layouts" class="dropdown-menu">
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-layout-options" role="button">
                                    <span data-lang="layout-options">Layout Options</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-layout-options" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/layouts/scrollable") }}" target="_blank"><span data-lang="layouts-scrollable">Scrollable</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/compact") }}" target="_blank"><span data-lang="layouts-compact">Compact</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/boxed") }}" target="_blank"><span data-lang="layouts-boxed">Boxed</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/horizontal") }}" target="_blank"><span data-lang="layouts-horizontal">Horizontal</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/preloader") }}" target="_blank"><span data-lang="layouts-preloader">Preloader</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-sidebars" role="button">
                                    <span data-lang="sidebars">Sidebars</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-sidebars" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/dark") }}" target="_blank"><span data-lang="layouts-sidebar-dark">Dark Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/gradient") }}" target="_blank"><span data-lang="layouts-sidebar-gradient">Gradient Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/gray") }}" target="_blank"><span data-lang="layouts-sidebar-gray">Gray Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/image") }}" target="_blank"><span data-lang="layouts-sidebar-image">Image Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/compact") }}" target="_blank"><span data-lang="layouts-sidebar-compact">Compact Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/on-hover") }}" target="_blank"><span data-lang="layouts-sidebar-on-hover">On Hover Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/on-hover-active") }}" target="_blank"><span data-lang="layouts-sidebar-on-hover-active">On Hover Active</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/offcanvas") }}" target="_blank"><span data-lang="layouts-sidebar-offcanvas">Offcanvas Menu</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/no-icons") }}" target="_blank"><span data-lang="layouts-sidebar-no-icons">No Icons with Lines</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/sidebar/with-lines") }}" target="_blank"><span data-lang="layouts-sidebar-with-lines">Sidebar with Lines</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-topbar" role="button">
                                    <span data-lang="topbar">Topbar</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-topbar" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/layouts/topbar/light") }}" target="_blank"><span data-lang="layouts-topbar-light">Light Topbar</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/topbar/gray") }}" target="_blank"><span data-lang="layouts-topbar-gray">Gray Topbar</span></a>
                                    <a class="dropdown-item" href="{{ url("/layouts/topbar/gradient") }}" target="_blank"><span data-lang="layouts-topbar-gradient">Gradient Topbar</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-components" role="button">
                            <span class="menu-icon"><i class="fs-xl" data-lucide="component"></i></span>
                            <span class="menu-text" data-lang="components">Components</span>
                            <div class="menu-arrow"></div>
                        </a>
                        <div aria-labelledby="topnav-components" class="dropdown-menu">
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-base-ui" role="button">
                                    <span data-lang="base-ui">Base UI</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-base-ui" class="dropdown-menu dropdown-menu-columns">
                                    <a class="dropdown-item" href="{{ url("/ui/accordions") }}"><span data-lang="ui-accordions">Accordions</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/alerts") }}"><span data-lang="ui-alerts">Alerts</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/images") }}"><span data-lang="ui-images">Images</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/badges") }}"><span data-lang="ui-badges">Badges</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/breadcrumb") }}"><span data-lang="ui-breadcrumb">Breadcrumb</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/buttons") }}"><span data-lang="ui-buttons">Buttons</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/cards") }}"><span data-lang="ui-cards">Cards</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/carousel") }}"><span data-lang="ui-carousel">Carousel</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/collapse") }}"><span data-lang="ui-collapse">Collapse</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/colors") }}"><span data-lang="ui-colors">Colors</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/dropdowns") }}"><span data-lang="ui-dropdowns">Dropdowns</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/videos") }}"><span data-lang="ui-videos">Videos</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/grid") }}"><span data-lang="ui-grid">Grid Options</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/links") }}"><span data-lang="ui-links">Links</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/list-group") }}"><span data-lang="ui-list-group">List Group</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/modals") }}"><span data-lang="ui-modals">Modals</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/notifications") }}"><span data-lang="ui-notifications">Notifications</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/offcanvas") }}"><span data-lang="ui-offcanvas">Offcanvas</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/placeholders") }}"><span data-lang="ui-placeholders">Placeholders</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/pagination") }}"><span data-lang="ui-pagination">Pagination</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/popovers") }}"><span data-lang="ui-popovers">Popovers</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/progress") }}"><span data-lang="ui-progress">Progress</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/scrollspy") }}"><span data-lang="ui-scrollspy">Scrollspy</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/spinners") }}"><span data-lang="ui-spinners">Spinners</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/tabs") }}"><span data-lang="ui-tabs">Tabs</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/tooltips") }}"><span data-lang="ui-tooltips">Tooltips</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/typography") }}"><span data-lang="ui-typography">Typography</span></a>
                                    <a class="dropdown-item" href="{{ url("/ui/utilities") }}"><span data-lang="ui-utilities">Utilities</span></a>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ url("/widgets") }}"><span data-lang="widgets">Widgets</span></a>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-charts" role="button">
                                    <span data-lang="charts">Charts</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-charts" class="dropdown-menu">
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-apex-charts" role="button">
                                            <span data-lang="apex-charts">Apex Charts</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-apex-charts" class="dropdown-menu dropdown-menu-columns">
                                            <a class="dropdown-item" href="{{ url("/charts/apex/area") }}"><span data-lang="charts-apex-area">Area</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/bar") }}"><span data-lang="charts-apex-bar">Bar</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/bubble") }}"><span data-lang="charts-apex-bubble">Bubble</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/candlestick") }}"><span data-lang="charts-apex-candlestick">Candlestick</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/column") }}"><span data-lang="charts-apex-column">Column</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/heatmap") }}"><span data-lang="charts-apex-heatmap">Heatmap</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/line") }}"><span data-lang="charts-apex-line">Line</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/mixed") }}"><span data-lang="charts-apex-mixed">Mixed</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/timeline") }}"><span data-lang="charts-apex-timeline">Timeline</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/boxplot") }}"><span data-lang="charts-apex-boxplot">Boxplot</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/treemap") }}"><span data-lang="charts-apex-treemap">Treemap</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/pie") }}"><span data-lang="charts-apex-pie">Pie</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/radar") }}"><span data-lang="charts-apex-radar">Radar</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/radialbar") }}"><span data-lang="charts-apex-radialbar">RadialBar</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/scatter") }}"><span data-lang="charts-apex-scatter">Scatter</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/polar-area") }}"><span data-lang="charts-apex-polar-area">Polar Area</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/sparklines") }}"><span data-lang="charts-apex-sparklines">Sparklines</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/range") }}"><span data-lang="charts-apex-range">Range</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/funnel") }}"><span data-lang="charts-apex-funnel">Funnel</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/apex/slope") }}"><span data-lang="charts-apex-slope">Slope</span></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-chartjs" role="button">
                                            <span data-lang="chartjs">Chart Js</span>
                                            <div class="menu-arrow"></div>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-chartjs" class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url("/charts/chartjs/area") }}"><span data-lang="charts-chartjs-area">Area</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/chartjs/bar") }}"><span data-lang="charts-chartjs-bar">Bar</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/chartjs/line") }}"><span data-lang="charts-chartjs-line">Line</span></a>
                                            <a class="dropdown-item" href="{{ url("/charts/chartjs/other") }}"><span data-lang="charts-chartjs-other">Other</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-forms" role="button">
                                    <span data-lang="forms">Forms</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-forms" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/form/elements") }}"><span data-lang="form-elements">Basic Elements</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/pickers") }}"><span data-lang="form-pickers">Pickers</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/select") }}"><span data-lang="form-select">Select</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/validation") }}"><span data-lang="form-validation">Validation</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/wizard") }}"><span data-lang="form-wizard">Wizard</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/fileuploads") }}"><span data-lang="form-fileuploads">File Uploads</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/text-editors") }}"><span data-lang="form-text-editors">Text Editors</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/range-slider") }}"><span data-lang="form-range-slider">Range Slider</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/layout") }}"><span data-lang="form-layout">Layouts</span></a>
                                    <a class="dropdown-item" href="{{ url("/form/other-plugin") }}"><span data-lang="form-other-plugin">Other Plugins</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-tables" role="button">
                                    <span data-lang="tables">Tables</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-tables" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/tables/static") }}"><span data-lang="tables-static">Static Tables</span></a>
                                    <a class="dropdown-item" href="{{ url("/tables/custom") }}"><span data-lang="tables-custom">Custom Tables</span></a>
                                    <div class="dropdown">
                                        <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-datatables" role="button">
                                            <span data-lang="datatables">DataTables</span><span class="badge bg-success text-white">15</span>
                                        </a>
                                        <div aria-labelledby="topnav-submenu-datatables" class="dropdown-menu dropdown-menu-columns">
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/basic") }}"><span data-lang="tables-datatables-basic">Basic</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/export-data") }}"><span data-lang="tables-datatables-export-data">Export Data</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/select") }}"><span data-lang="tables-datatables-select">Select</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/ajax") }}"><span data-lang="tables-datatables-ajax">Ajax</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/javascript") }}"><span data-lang="tables-datatables-javascript">Javascript Source</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/rendering") }}"><span data-lang="tables-datatables-rendering">Data Rendering</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/scroll") }}"><span data-lang="tables-datatables-scroll">Scroll</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/fixed-columns") }}"><span data-lang="tables-datatables-fixed-columns">Fixed Columns</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/fixed-header") }}"><span data-lang="tables-datatables-fixed-header">Fixed Header</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/columns") }}"><span data-lang="tables-datatables-columns">Show &amp; Hide Column</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/child-rows") }}"><span data-lang="tables-datatables-child-rows">Child Rows</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/column-searching") }}"><span data-lang="tables-datatables-column-searching">Column Searching</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/range-search") }}"><span data-lang="tables-datatables-range-search">Range Search</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/rows-add") }}"><span data-lang="tables-datatables-rows-add">Add Rows</span></a>
                                            <a class="dropdown-item" href="{{ url("/tables/datatables/checkbox-select") }}"><span data-lang="tables-datatables-checkbox-select">Checkbox Select</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-icons" role="button">
                                    <span data-lang="icons">Icons</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-icons" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/icons/tabler") }}"><span data-lang="icons-tabler">Tabler</span></a>
                                    <a class="dropdown-item" href="{{ url("/icons/lucide") }}"><span data-lang="icons-lucide">Lucide</span></a>
                                    <a class="dropdown-item" href="{{ url("/icons/flags") }}"><span data-lang="icons-flags">Flags</span></a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a aria-expanded="false" aria-haspopup="true" class="dropdown-item dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" href="#" id="topnav-submenu-maps" role="button">
                                    <span data-lang="maps">Maps</span>
                                    <div class="menu-arrow"></div>
                                </a>
                                <div aria-labelledby="topnav-submenu-maps" class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url("/maps/vector") }}"><span data-lang="maps-vector">Vector Maps</span></a>
                                    <a class="dropdown-item" href="{{ url("/maps/leaflet") }}"><span data-lang="maps-leaflet">Leaflet Maps</span></a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </nav>
</header>
<!-- Horizontal Menu End -->
