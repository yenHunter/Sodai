@extends("shared.base", ["title" => "Sortables List"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["title" => "Sortables List", "subtitle" => "Plugins"])

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sortables List</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>nested-sortable</code>
                                    classes on the
                                    <code>list-group</code>
                                    element to enable drag-and-drop sorting of hierarchical task items.
                                </p>
                                <div class="list-group fw-medium nested-sortable">
                                    <div class="list-group-item">Design Phase</div>
                                    <div class="list-group-item">
                                        Development Phase
                                        <div class="list-group nested-sortable">
                                            <div class="list-group-item">Frontend Implementation</div>
                                            <div class="list-group-item">
                                                Backend API Setup
                                                <div class="list-group nested-sortable">
                                                    <div class="list-group-item">Authentication Module</div>
                                                    <div class="list-group-item">Database Schema</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        Testing Phase
                                        <div class="list-group nested-sortable">
                                            <div class="list-group-item">Unit Tests</div>
                                            <div class="list-group-item">Integration Tests</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sortables List with Handle</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>nested-sortable-handle</code>
                                    class to list-group class to set a nested list with sortable items.
                                </p>
                                <div class="list-group fw-medium nested-sortable-handle">
                                    <div class="list-group-item nested-1">
                                        <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                        Project Alpha
                                        <div class="list-group nested-sortable-handle">
                                            <div class="list-group-item nested-2">
                                                <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                Design Phase
                                            </div>
                                            <div class="list-group-item nested-2">
                                                <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                Development Phase
                                                <div class="list-group nested-sortable-handle">
                                                    <div class="list-group-item nested-3">
                                                        <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                        Frontend Module
                                                    </div>
                                                    <div class="list-group-item nested-3">
                                                        <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                        Backend Module
                                                    </div>
                                                    <div class="list-group-item nested-3">
                                                        <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                        API Integration
                                                    </div>
                                                    <div class="list-group-item nested-3">
                                                        <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                        Unit Testing
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item nested-2">
                                                <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                QA Review
                                            </div>
                                            <div class="list-group-item nested-2">
                                                <i class="align-middle sort-handle" data-lucide="grip-horizontal"></i>
                                                Deployment
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sortable with Icons</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>nested-sortable</code>
                                    class to list-group class to set a nested list with sortable items where icons are given within list-group-item.
                                </p>
                                <div class="list-group border-dashed">
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <i class="fs-sm text-primary" data-lucide="kanban"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">Tasks</h5>
                                            </div>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="list-check"></i>
                                                To Do
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="info"></i>
                                                In Progress
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="check-circle"></i>
                                                Completed
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <i class="fs-sm text-primary" data-lucide="flag"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">Milestones</h5>
                                            </div>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                Project Kickoff
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                Phase 1 Completion
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                Final Delivery
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar-xs flex-shrink-0">
                                                <span class="avatar-title text-bg-light rounded-circle">
                                                    <i class="fs-sm text-primary" data-lucide="users"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">Teams</h5>
                                            </div>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                Development Team
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                Design Team
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                QA Team
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sortable with Icons with Labels</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Use
                                    <code>nested-sortable</code>
                                    class with the list-group class to create a nested list with sortable items where icons are placed inside list-group-item.
                                </p>
                                <div class="list-group border-dashed">
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fs-4 text-primary" data-lucide="kanban"></i>
                                                <h5 class="mb-0 fw-semibold">Strategy</h5>
                                            </div>
                                            <span class="badge bg-primary-subtle text-primary">Phase A</span>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="list-check"></i>
                                                Research Topics
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="info"></i>
                                                Analysis in Progress
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="check-circle"></i>
                                                Insights Approved
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fs-4 text-primary" data-lucide="flag"></i>
                                                <h5 class="mb-0 fw-semibold">Key Deliverables</h5>
                                            </div>
                                            <span class="badge bg-info-subtle text-info">Phase B</span>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                Initial Draft Release
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                User Testing Round
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="flag"></i>
                                                Final Launch
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fs-4 text-primary" data-lucide="users"></i>
                                                <h5 class="mb-0 fw-semibold">Departments</h5>
                                            </div>
                                            <span class="badge bg-success-subtle text-success">Phase C</span>
                                        </div>
                                        <div class="list-group nested-sortable border-0">
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                Engineering Unit
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                Creative Studio
                                            </div>
                                            <div class="list-group-item">
                                                <i class="fs-sm me-2 text-muted" data-lucide="user"></i>
                                                Quality Assurance
                                            </div>
                                        </div>
                                    </div>
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
    @vite(["resources/js/pages/plugins-nestable.js"])
@endsection
