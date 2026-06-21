@extends("shared.base", ["title" => "Support Tickets"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Support", "title" => "Tickets"])

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-2">
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="avatar-lg rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i class="fs-xxl" data-lucide="ticket"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <h5 class="fs-xl mb-0">148</h5>
                                            <span>
                                                +3%
                                                <i class="text-success" data-lucide="arrow-up"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Open Tickets</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="avatar-lg rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="fs-xxl" data-lucide="check-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <h5 class="fs-xl mb-0">1,289</h5>
                                            <span>
                                                +12%
                                                <i class="text-success" data-lucide="arrow-up"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Resolved Tickets</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="avatar-lg rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="fs-xxl" data-lucide="hourglass"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <h5 class="fs-xl mb-0">93</h5>
                                            <span>
                                                -2.1%
                                                <i class="text-danger" data-lucide="arrow-down"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Pending Tickets</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="avatar-lg rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                                        <i class="fs-xxl" data-lucide="circle-alert"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <h5 class="fs-xl mb-0">25</h5>
                                            <span>
                                                +1.8%
                                                <i class="text-warning" data-lucide="arrow-up"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Escalated Tickets</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="avatar-lg rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i class="fs-xxl" data-lucide="clock"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <h5 class="fs-xl mb-0">
                                                8.4
                                                <small class="fs-6">hrs</small>
                                            </h5>
                                            <span>+0.5%</span>
                                        </div>
                                        <p class="text-muted mb-0">Average Response Time</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card" data-table="" data-table-rows-per-page="8">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input class="form-control" data-table-search="" placeholder="Search tickets..." type="search" />
                                        <i class="app-search-icon text-muted" data-lucide="search"></i>
                                    </div>
                                    <a class="btn btn-primary" href="{{ url("/apps/ticket/create") }}"> <i class="me-1" data-lucide="plus"></i> New Ticket </a>
                                    <button class="btn btn-danger d-none" data-table-delete-selected="">Delete</button>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>
                                    <div class="app-search">
                                        <select class="form-select form-control my-1 my-md-0" data-table-filter="status">
                                            <option value="">Status</option>
                                            <option value="Open">Open</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Resolved">Resolved</option>
                                            <option value="Closed">Closed</option>
                                            <option value="Escalated">Escalated</option>
                                        </select>
                                        <i class="app-search-icon text-muted" data-lucide="shuffle"></i>
                                    </div>
                                    <div class="app-search">
                                        <select class="form-select form-control my-1 my-md-0" data-table-filter="priority">
                                            <option value="">Priority</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Urgent">Urgent</option>
                                        </select>
                                        <i class="app-search-icon text-muted" data-lucide="alert-triangle"></i>
                                    </div>
                                    <div>
                                        <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                            <option value="5">5</option>
                                            <option selected="" value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                        <thead class="bg-light align-middle bg-opacity-25 thead-sm text-nowrap">
                                            <tr class="text-uppercase fs-xxs">
                                                <th class="ps-3" style="width: 1%">
                                                    <input class="form-check-input form-check-input-light fs-14 mt-0" data-table-select-all="" type="checkbox" />
                                                </th>
                                                <th data-table-sort="">ID</th>
                                                <th>Requested By</th>
                                                <th>Ticket Subject</th>
                                                <th>Assigned Agent</th>
                                                <th data-column="priority" data-table-sort="">Priority</th>
                                                <th data-column="status" data-table-sort="">Status</th>
                                                <th data-table-sort="">Date Created</th>
                                                <th data-table-sort="">Due Date</th>
                                                <th class="text-center" style="width: 1%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2451</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Emily Clark" class="avatar-xs rounded-circle" src="/images/users/user-3.jpg" />
                                                        <span>Emily Clark</span>
                                                    </div>
                                                </td>
                                                <td>Unable to login with 2FA</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                        <a class="link-reset" href="#!">Daniel Ray</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-danger">High</span></td>
                                                <td><span class="badge bg-warning-subtle text-warning badge-label">Pending</span></td>
                                                <td>04 Aug, 2025 <small class="text-muted">11:30 AM</small></td>
                                                <td>08 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2517</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Lucas Graham" class="avatar-xs rounded-circle" src="/images/users/user-9.jpg" />
                                                        <span>Lucas Graham</span>
                                                    </div>
                                                </td>
                                                <td>Cannot upload profile image</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-1.jpg" />
                                                        <a class="link-reset" href="#!">Sarah Lee</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-warning">Medium</span></td>
                                                <td><span class="badge bg-info-subtle text-info badge-label">In Progress</span></td>
                                                <td>05 Aug, 2025 <small class="text-muted">2:05 PM</small></td>
                                                <td>10 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2518</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Nina Patel" class="avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                                        <span>Nina Patel</span>
                                                    </div>
                                                </td>
                                                <td>Incorrect invoice total shown</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-8.jpg" />
                                                        <a class="link-reset" href="#!">Alex Morgan</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-danger">High</span></td>
                                                <td><span class="badge bg-warning-subtle text-warning badge-label">Pending</span></td>
                                                <td>05 Aug, 2025 <small class="text-muted">10:42 AM</small></td>
                                                <td>07 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2519</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Michael Foster" class="avatar-xs rounded-circle" src="/images/users/user-6.jpg" />
                                                        <span>Michael Foster</span>
                                                    </div>
                                                </td>
                                                <td>Website not loading on Safari</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-5.jpg" />
                                                        <a class="link-reset" href="#!">Jessica Hughes</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-success">Low</span></td>
                                                <td><span class="badge bg-success-subtle text-success badge-label">Resolved</span></td>
                                                <td>04 Aug, 2025 <small class="text-muted">6:20 PM</small></td>
                                                <td>06 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2520</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Isabella Reed" class="avatar-xs rounded-circle" src="/images/users/user-7.jpg" />
                                                        <span>Isabella Reed</span>
                                                    </div>
                                                </td>
                                                <td>Cannot change account email</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                        <a class="link-reset" href="#!">Daniel Ray</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-warning">Medium</span></td>
                                                <td><span class="badge bg-info-subtle text-info badge-label">In Progress</span></td>
                                                <td>03 Aug, 2025 <small class="text-muted">9:50 AM</small></td>
                                                <td>08 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2521</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Olivia White" class="avatar-xs rounded-circle" src="/images/users/user-1.jpg" />
                                                        <span>Olivia White</span>
                                                    </div>
                                                </td>
                                                <td>Can't access dashboard after update</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-9.jpg" />
                                                        <a class="link-reset" href="#!">James Carter</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-danger">High</span></td>
                                                <td><span class="badge bg-danger-subtle text-danger badge-label">Escalated</span></td>
                                                <td>05 Aug, 2025 <small class="text-muted">7:45 AM</small></td>
                                                <td>06 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2522</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Emma King" class="avatar-xs rounded-circle" src="/images/users/user-10.jpg" />
                                                        <span>Emma King</span>
                                                    </div>
                                                </td>
                                                <td>Feature request: export as PDF</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-3.jpg" />
                                                        <a class="link-reset" href="#!">Harper Wells</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-primary">Low</span></td>
                                                <td><span class="badge bg-secondary-subtle text-secondary badge-label">Closed</span></td>
                                                <td>01 Aug, 2025 <small class="text-muted">4:10 PM</small></td>
                                                <td>05 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2523</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Ava Sullivan" class="avatar-xs rounded-circle" src="/images/users/user-1.jpg" />
                                                        <span>Ava Sullivan</span>
                                                    </div>
                                                </td>
                                                <td>App freezes when uploading files</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                                        <a class="link-reset" href="#!">Liam Brooks</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-danger">High</span></td>
                                                <td><span class="badge bg-warning-subtle text-warning badge-label">Pending</span></td>
                                                <td>05 Aug, 2025 <small class="text-muted">1:20 PM</small></td>
                                                <td>09 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2524</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Sophie Bennett" class="avatar-xs rounded-circle" src="/images/users/user-3.jpg" />
                                                        <span>Sophie Bennett</span>
                                                    </div>
                                                </td>
                                                <td>Missing transaction history</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-6.jpg" />
                                                        <a class="link-reset" href="#!">Lucas Shaw</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-warning">Medium</span></td>
                                                <td><span class="badge bg-info-subtle text-info badge-label">In Progress</span></td>
                                                <td>04 Aug, 2025 <small class="text-muted">4:30 PM</small></td>
                                                <td>08 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2525</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Noah Allen" class="avatar-xs rounded-circle" src="/images/users/user-4.jpg" />
                                                        <span>Noah Allen</span>
                                                    </div>
                                                </td>
                                                <td>Feedback form submission error</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-5.jpg" />
                                                        <a class="link-reset" href="#!">Sophia Reed</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-success">Low</span></td>
                                                <td><span class="badge bg-success-subtle text-success badge-label">Resolved</span></td>
                                                <td>03 Aug, 2025 <small class="text-muted">12:00 PM</small></td>
                                                <td>06 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2526</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Ethan James" class="avatar-xs rounded-circle" src="/images/users/user-5.jpg" />
                                                        <span>Ethan James</span>
                                                    </div>
                                                </td>
                                                <td>Can't generate report summary</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-7.jpg" />
                                                        <a class="link-reset" href="#!">Chloe Adams</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-danger">High</span></td>
                                                <td><span class="badge bg-danger-subtle text-danger badge-label">Escalated</span></td>
                                                <td>05 Aug, 2025 <small class="text-muted">8:50 AM</small></td>
                                                <td>07 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" />
                                                </td>
                                                <td><a class="fw-semibold link-reset" href="{{ url("/apps/ticket/details") }}">#SUP-2527</a></td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Grace Carter" class="avatar-xs rounded-circle" src="/images/users/user-8.jpg" />
                                                        <span>Grace Carter</span>
                                                    </div>
                                                </td>
                                                <td>Feature request: Dark mode toggle</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img alt="Agent" class="avatar-xs rounded-circle" src="/images/users/user-2.jpg" />
                                                        <a class="link-reset" href="#!">Daniel Ray</a>
                                                    </div>
                                                </td>
                                                <td><span class="badge text-bg-primary">Low</span></td>
                                                <td><span class="badge bg-secondary-subtle text-secondary badge-label">Closed</span></td>
                                                <td>02 Aug, 2025 <small class="text-muted">5:25 PM</small></td>
                                                <td>06 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="{{ url("/apps/ticket/details") }}"><i class="fs-lg" data-lucide="eye"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#!"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                        <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="Support Tickets"></div>
                                    <div data-table-pagination=""></div>
                                </div>
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
