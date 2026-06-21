@extends("shared.base", ["title" => "Role Details"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-sm-center flex-sm-row flex-column my-3">
                            <div class="flex-grow-1">
                                <h4 class="fs-lg mb-1">Role Details</h4>
                                <p class="text-muted mb-0">Define and manage roles to streamline operations and ensure secure access control.</p>
                            </div>
                            <div class="text-end mt-3 mt-sm-0">
                                <a class="btn btn-success" href="javascript:void(0);"> <i class="me-1" data-lucide="plus"></i> Add New Role </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="position-absolute top-0 end-0" style="width: 180px">
                                        <img alt="auth-card-bg" class="auth-card-bg-img" src="/images/auth-card-bg.svg" />
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xl rounded bg-primary-subtle d-flex align-items-center justify-content-center">
                                                    <i class="fs-24 text-primary" data-lucide="shield-half"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="mb-1">Security Officer</h5>
                                                <p class="text-muted mb-0 fs-base">Handles platform safety and protocol reviews.</p>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="dropdown">
                                                    <a class="text-muted fs-xl" data-bs-toggle="dropdown" href="#">
                                                        <i data-lucide="ellipsis-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#"><i class="me-2" data-lucide="square-pen"></i>Edit</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"><i class="me-2" data-lucide="trash-2"></i>Remove</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled mb-3">
                                            <li class="d-flex align-items-center mb-2"><i class="fs-lg text-success me-2" data-lucide="check"></i> Daily Risk Assessment</li>
                                            <li class="d-flex align-items-center mb-2"><i class="fs-lg text-success me-2" data-lucide="check"></i> Manage Security Logs</li>
                                            <li class="d-flex align-items-center mb-2"><i class="fs-lg text-success me-2" data-lucide="check"></i> Control Access Rights</li>
                                            <li class="d-flex align-items-center"><i class="fs-lg text-success me-2" data-lucide="check"></i> Emergency Protocols</li>
                                        </ul>
                                        <p class="mb-2 text-muted">Total 17 users</p>
                                        <div class="avatar-group avatar-group-sm mb-3">
                                            <div class="avatar">
                                                <img alt="" class="rounded-circle avatar-sm" src="/images/users/user-7.jpg" />
                                            </div>
                                            <div class="avatar">
                                                <img alt="" class="rounded-circle avatar-sm" src="/images/users/user-8.jpg" />
                                            </div>
                                            <div class="avatar">
                                                <img alt="" class="rounded-circle avatar-sm" src="/images/users/user-9.jpg" />
                                            </div>
                                            <div class="avatar">
                                                <img alt="" class="rounded-circle avatar-sm" src="/images/users/user-10.jpg" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted fs-xs"><i class="me-1" data-lucide="clock"></i> Updated 1 hour ago</span>
                                            <a class="btn btn-sm btn-outline-primary rounded-pill" data-bs-target="#editRoleModal" data-bs-toggle="modal" href="#">Edit Role</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <div class="card" data-table="" data-table-rows-per-page="8">
                                    <div class="card-header border-light justify-content-between">
                                        <div class="d-flex gap-2">
                                            <div class="app-search">
                                                <input class="form-control" data-table-search="" placeholder="Search users..." type="search" />
                                                <i class="app-search-icon text-muted" data-lucide="search"></i>
                                            </div>
                                            <button class="btn btn-danger d-none" data-table-delete-selected="">Delete</button>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="me-2 fw-semibold">Filter By:</span>
                                            <div class="app-search">
                                                <select class="form-select form-control my-1 my-md-0" data-table-filter="status">
                                                    <option value="All">Status</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                    <option value="Suspended">Suspended</option>
                                                </select>
                                                <i class="app-search-icon text-muted" data-lucide="user-check"></i>
                                            </div>
                                            <div>
                                                <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="15">15</option>
                                                    <option value="20">20</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-secondary" data-bs-target="#addUserModal" data-bs-toggle="modal" type="button">Add User</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                                <tr class="text-uppercase fs-xxs">
                                                    <th class="ps-3" style="width: 1%">
                                                        <input class="form-check-input form-check-input-light fs-14 mt-0" data-table-select-all="" type="checkbox" />
                                                    </th>
                                                    <th data-table-sort="">ID</th>
                                                    <th data-table-sort="user">User</th>
                                                    <th data-table-sort="">Joined Date</th>
                                                    <th data-column="status" data-table-sort="">Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" />
                                                    </td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR76129</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Elena Carter" class="img-fluid rounded-circle" src="/images/users/user-1.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Elena Carter</a></h5>
                                                                <p class="text-muted fs-xs mb-0">elena@webcore.dev</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>19 Jul, 2025 <small class="text-muted">11:00 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR58647</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Jordan Smith" class="img-fluid rounded-circle" src="/images/users/user-2.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Jordan Smith</a></h5>
                                                                <p class="text-muted fs-xs mb-0">jordan@mediaflow.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>21 Jul, 2025 <small class="text-muted">9:15 AM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR94715</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Lucas Brown" class="img-fluid rounded-circle" src="/images/users/user-3.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Lucas Brown</a></h5>
                                                                <p class="text-muted fs-xs mb-0">lucas@intechlabs.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>20 Jul, 2025 <small class="text-muted">3:00 PM</small></td>
                                                    <td><span class="badge bg-danger-subtle text-danger badge-label">Suspended</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR40289</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Sophia Green" class="img-fluid rounded-circle" src="/images/users/user-4.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Sophia Green</a></h5>
                                                                <p class="text-muted fs-xs mb-0">sophia@skygrid.org</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>18 Jul, 2025 <small class="text-muted">10:30 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR23981</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Ethan Ross" class="img-fluid rounded-circle" src="/images/users/user-5.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Ethan Ross</a></h5>
                                                                <p class="text-muted fs-xs mb-0">ethan@logico.io</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>17 Jul, 2025 <small class="text-muted">5:55 PM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR83742</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Zara Mitchell" class="img-fluid rounded-circle" src="/images/users/user-6.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Zara Mitchell</a></h5>
                                                                <p class="text-muted fs-xs mb-0">zara@fusionui.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>22 Jul, 2025 <small class="text-muted">9:10 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR51268</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Benjamin Gray" class="img-fluid rounded-circle" src="/images/users/user-7.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Benjamin Gray</a></h5>
                                                                <p class="text-muted fs-xs mb-0">benjamin@stackpulse.dev</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>20 Jul, 2025 <small class="text-muted">6:25 PM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR17456</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Ava Patel" class="img-fluid rounded-circle" src="/images/users/user-8.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Ava Patel</a></h5>
                                                                <p class="text-muted fs-xs mb-0">ava@cleardash.io</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>23 Jul, 2025 <small class="text-muted">8:45 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR96421</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Mason Rivera" class="img-fluid rounded-circle" src="/images/users/user-9.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Mason Rivera</a></h5>
                                                                <p class="text-muted fs-xs mb-0">mason@softmeta.app</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>21 Jul, 2025 <small class="text-muted">2:10 PM</small></td>
                                                    <td><span class="badge bg-danger-subtle text-danger badge-label">Suspended</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox" /></td>
                                                    <td>
                                                        <h5 class="m-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">#USR71539</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img alt="Chloe Walker" class="img-fluid rounded-circle" src="/images/users/user-10.jpg" /></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a class="link-reset" href="{{ url("/apps/users/profile") }}">Chloe Walker</a></h5>
                                                                <p class="text-muted fs-xs mb-0">chloe@flowbase.org</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>19 Jul, 2025 <small class="text-muted">12:35 PM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="eye"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" href="#"><i class="fs-lg" data-lucide="square-pen"></i></a>
                                                            <a class="btn btn-default btn-icon btn-sm rounded-circle" data-table-delete-row="" href="#"><i class="fs-lg" data-lucide="trash-2"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div data-table-pagination-info="roles"></div>
                                            <div data-table-pagination=""></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" aria-labelledby="editRoleModalLabel" class="modal fade" id="editRoleModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                            </div>
                            <form id="editRoleForm">
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="editRoleName">Role Name</label>
                                            <input class="form-control" id="editRoleName" required="" type="text" value="Developer" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="editRoleDescription">Description</label>
                                            <input class="form-control" id="editRoleDescription" required="" type="text" value="Builds and maintains the platform core features." />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="editRoleResponsibilities">Key Responsibilities</label>
                                            <textarea class="form-control" id="editRoleResponsibilities" required="" rows="4">
Codebase Maintenance
API Integration
Unit Testing
Feature Deployment</textarea>
                                            <small class="text-muted">Separate each item by comma or line</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="editRoleUsers">Assign Users</label>
                                            <select class="form-select" id="editRoleUsers" multiple="">
                                                <option selected="" value="1">Leah Kim</option>
                                                <option selected="" value="2">David Tran</option>
                                                <option value="3">Michael Brown</option>
                                                <option value="4">Emma Wilson</option>
                                            </select>
                                            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="editRoleIcon">Role Icon</label>
                                            <input class="form-control" id="editRoleIcon" type="text" value="ti ti-code" />
                                            <small class="text-muted">Use icon class from your icon library</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                                    <button class="btn btn-primary" type="submit">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" aria-labelledby="addUserModalLabel" class="modal fade" id="addUserModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                            </div>
                            <form id="addUserForm">
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="userFullName">Full Name</label>
                                            <input class="form-control" id="userFullName" placeholder="Enter full name" required="" type="text" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="userEmail">Email Address</label>
                                            <input class="form-control" id="userEmail" placeholder="Enter email" required="" type="email" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="userRole">Role</label>
                                            <select class="form-select" id="userRole" required="">
                                                <option value="">Select role</option>
                                                <option value="Project Manager">Project Manager</option>
                                                <option value="Developer">Developer</option>
                                                <option value="Support Lead">Support Lead</option>
                                                <option value="Security Officer">Security Officer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="userStatus">Status</label>
                                            <select class="form-select" id="userStatus" required="">
                                                <option value="">Select status</option>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                                <option value="Suspended">Suspended</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="userAvatar">User Avatar</label>
                                            <input accept="image/*" class="form-control" id="userAvatar" type="file" />
                                            <small class="text-muted">Optional: Upload avatar image</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
                                    <button class="btn btn-primary" type="submit">Add User</button>
                                </div>
                            </form>
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
