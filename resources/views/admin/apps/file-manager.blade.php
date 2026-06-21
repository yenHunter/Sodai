@extends("shared.base", ["title" => "File Manager"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Apps", "title" => "File Manager"])

                <div class="outlook-box gap-1">
                    <div class="offcanvas-lg offcanvas-start outlook-left-menu outlook-left-menu-md" id="fileSidebaroffcanvas" tabindex="-1">
                        <div class="card h-100 mb-0 rounded-0 border-0" data-simplebar="">
                            <div class="card-body">
                                <a class="btn btn-danger fw-medium w-100" href="#!">Upload Files</a>
                                <div class="list-group list-group-flush list-custom mt-3">
                                    <a class="list-group-item list-group-item-action active" href="file-manager.html">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="folder"></i>
                                        <span class="align-middle">My Files</span>
                                        <span class="badge align-middle bg-danger-subtle fs-xxs text-danger float-end">12</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="share-2"></i>
                                        <span class="align-middle">Shared with Me</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="clock"></i>
                                        <span class="align-middle">Recent</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="star"></i>
                                        <span class="align-middle">Favourites</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="download"></i>
                                        <span class="align-middle">Downloads</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 opacity-75 fs-lg align-middle" data-lucide="trash-2"></i>
                                        <span class="align-middle">Trash</span>
                                    </a>
                                    <div class="list-group-item mt-2">
                                        <span class="align-middle">Categories</span>
                                    </div>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 align-middle fs-sm text-primary" data-lucide="chart-pie"></i>
                                        <span class="align-middle">Work</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 align-middle fs-sm text-secondary" data-lucide="chart-pie"></i>
                                        <span class="align-middle">Projects</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 align-middle fs-sm text-info" data-lucide="chart-pie"></i>
                                        <span class="align-middle">Media</span>
                                    </a>
                                    <a class="list-group-item list-group-item-action" href="javascript:void(0);">
                                        <i class="me-1 align-middle fs-sm text-warning" data-lucide="chart-pie"></i>
                                        <span class="align-middle">Documents</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card h-100 mb-0 rounded-0 flex-grow-1 border-0" data-table="" data-table-rows-per-page="8">
                        <div class="card-header border-light justify-content-between">
                            <div class="d-flex gap-2">
                                <div class="d-lg-none d-inline-flex gap-2">
                                    <button aria-controls="fileSidebaroffcanvas" class="btn btn-default btn-icon" data-bs-target="#fileSidebaroffcanvas" data-bs-toggle="offcanvas" type="button">
                                        <i class="fs-lg" data-lucide="menu"></i>
                                    </button>
                                </div>
                                <div class="app-search">
                                    <input class="form-control" data-table-search="" placeholder="Search files..." type="search" />
                                    <i class="app-search-icon text-muted" data-lucide="search"></i>
                                </div>
                                <button class="btn btn-danger d-none" data-table-delete-selected="">Delete</button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="me-2 fw-semibold">Filter By:</span>
                                <div class="app-search">
                                    <select class="form-select form-control my-1 my-md-0" data-table-filter="type">
                                        <option selected="">File Type</option>
                                        <option value="Folder">Folder</option>
                                        <option value="MySQL">MySQL</option>
                                        <option value="MP4">MP4</option>
                                        <option value="Audio">Audio</option>
                                        <option value="Figma">Figma</option>
                                    </select>
                                    <i class="app-search-icon text-muted" data-lucide="file"></i>
                                </div>
                                <div>
                                    <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" data-simplebar="" data-simplebar-md="" style="height: calc(100% - 100px)">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">Premium Multi</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">2.3 GB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#attex-react-folder" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">Material Design</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">105.3MB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#material-design" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">DashPro UI Kit</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">512MB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#dashpro-ui" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">VueStudio Pack</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">880MB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#vuestudio-pack" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">Nextify Pro</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">1.1 GB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#nextify-pro" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">Blazor Studio</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">780MB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#blazor-studio" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">Angular Prime</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">940MB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#angular-prime" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xxl-3">
                                    <div class="card border border-dashed mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                    <i class="fs-24 avatar-title" data-lucide="folder"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1 fs-sm">
                                                        <a class="link-reset" href="#!">React Kit Pro</a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-xs">1.6 GB</p>
                                                </div>
                                                <div class="dropdown flex-shrink-0 text-muted">
                                                    <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="share-2"></i>
                                                            Share
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="link"></i>
                                                            Get Sharable Link
                                                        </a>
                                                        <a class="dropdown-item" download="" href="/images/users/user-1.jpg">
                                                            <i class="me-1" data-lucide="download"></i>
                                                            Download
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="pin"></i>
                                                            Pin
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="square-pen"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" data-dismissible="#react-kit-pro" href="javascript:void(0);">
                                                            <i class="me-1" data-lucide="trash-2"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive-sm">
                                <table class="table table-custom table-select mb-0">
                                    <thead class="bg-light bg-opacity-25 thead-sm border-top border-light">
                                        <tr class="text-uppercase align-middle fs-xxs">
                                            <th class="ps-3" style="width: 1%">
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" data-table-select-all="" id="select-all-files" type="checkbox" value="option" />
                                            </th>
                                            <th data-table-sort="name">Name</th>
                                            <th data-column="type" data-table-sort="">Type</th>
                                            <th data-table-sort="">Modified</th>
                                            <th class="w-auto" data-table-sort="owner">Owner</th>
                                            <th class="w-auto">Shared with</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="video"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Project Overview Video</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">120MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>MP4</td>
                                            <td>April 2, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">john@techgroup.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-5.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-7.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-6.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-4.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="video"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Team Meeting Video</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">200MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>MP4</td>
                                            <td>April 15, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">michael@devteam.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-5.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" download="" href="/images/users/user-4.jpg">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 d-flex justify-content-center align-items-center avatar-md bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="figma"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Marketing Strategy Design</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">150MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Figma</td>
                                            <td>April 20, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">susan@marketing.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-1.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-6.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-9.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="file-text"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Client Proposal PDF</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">45MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>PDF</td>
                                            <td>May 5, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-8.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">mark@clientservices.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-4.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-7.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" download="" href="{{ asset("files/proposal.pdf") }}">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="database"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Database Schema</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">30MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>MySQL</td>
                                            <td>April 1, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">alex@creatix.io</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-4.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-8.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="code"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Website Script</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">15MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>JS</td>
                                            <td>April 2, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">john@techgroup.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-5.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-7.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-6.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-4.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="package"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Dependency Package</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">5MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>DEP</td>
                                            <td>April 15, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">michael@devteam.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-5.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" download="" href="{{ asset("files/dependency.dep") }}">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="folder"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#!">Internet Portal</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">87MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Folder</td>
                                            <td>March 10, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-6.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">emma@devcore.com</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-7.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-9.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-10.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-3.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-8.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" download="" href="/images/users/user-6.jpg">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="javascript:void(0);">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3">
                                                <input class="form-check-input form-check-input-light fs-14 file-item-check" type="checkbox" value="option" />
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 avatar-md d-flex justify-content-center align-items-center bg-light bg-opacity-50 text-muted rounded-2">
                                                        <i class="fs-xl avatar-title" data-lucide="music"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-base">
                                                            <a class="link-reset" data-sort="name" href="#">Podcast Episode 12</a>
                                                        </h5>
                                                        <p class="text-muted mb-0 fs-xs">55MB</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Audio</td>
                                            <td>April 1, 2025</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0 bg-light bg-opacity-50 text-muted d-inline-flex align-items-center justify-content-center rounded-2">
                                                        <img alt="" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                    </div>
                                                    <h5 class="mb-0 fs-base">
                                                        <a class="link-reset" data-sort="owner" href="#!">alex@creatix.io</a>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="avatar-group avatar-group-xs">
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-4.jpg" />
                                                    </div>
                                                    <div class="avatar">
                                                        <img alt="" class="rounded-circle avatar-xs" src="/images/users/user-8.jpg" />
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <span data-toggler="off">
                                                        <a class="d-none" data-toggler-on="" href="#">
                                                            <i class="text-warning fs-lg" data-lucide="star"></i>
                                                        </a>
                                                        <a data-toggler-off="" href="#">
                                                            <i class="text-muted fs-lg" data-lucide="star"></i>
                                                        </a>
                                                    </span>
                                                    <div class="dropdown flex-shrink-0 text-muted">
                                                        <a aria-expanded="false" class="dropdown-toggle drop-arrow-none fs-xxl link-reset p-0" data-bs-toggle="dropdown" href="#">
                                                            <i data-lucide="ellipsis-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="share-2"></i>
                                                                Share
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="link"></i>
                                                                Get Sharable Link
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="download"></i>
                                                                Download
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="pin"></i>
                                                                Pin
                                                            </a>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="me-1" data-lucide="square-pen"></i>
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item" data-table-delete-row="" href="#">
                                                                <i class="me-1" data-lucide="trash-2"></i>
                                                                Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2 p-3">
                                <strong>Loading...</strong>
                                <div aria-hidden="true" class="spinner-border spinner-border-sm text-danger" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include("shared.partials.footer")
        </div>
    </div>

    @include("shared.partials.customizer") @include("shared.partials.footer-scripts")
@endsection

@section("scripts")
    @vite(["resources/js/pages/custom-table.js"])
@endsection
