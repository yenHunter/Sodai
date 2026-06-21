@extends("shared.base", ["title" => "Create New Ticket"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Support", "title" => "Create New Ticket"])

                <div class="row justify-content-center">
                    <div class="col-xxl-10">
                        <div class="card">
                            <form action="#" method="post">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="requester">Requester Name</label>
                                        <input class="form-control" id="requester" placeholder="Enter your full name" type="text" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="email">Email Address</label>
                                        <input class="form-control" id="email" placeholder="you@example.com" type="email" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="subject">Ticket Subject</label>
                                        <input class="form-control" id="subject" placeholder="Brief summary of the issue" type="text" />
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="priority">Priority</label>
                                            <select class="form-select" id="priority">
                                                <option disabled="" selected="">Choose...</option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="tickstatus">Status</label>
                                            <select class="form-select" id="tickstatus">
                                                <option disabled="" selected="">Choose...</option>
                                                <option value="Pending">Pending</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Resolved">Resolved</option>
                                                <option value="Closed">Closed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tags">Tags (comma-separated)</label>
                                        <input class="form-control" id="tags" placeholder="e.g. login, error, payment" type="text" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="description">Description</label>
                                        <textarea class="form-control" id="description" placeholder="Describe the issue in detail..." rows="5"></textarea>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn btn-primary" type="submit"><i class="me-1" data-lucide="plus"></i> Submit Ticket</button>
                                        <button class="btn btn-outline-secondary" type="reset"><i class="me-1" data-lucide="refresh-ccw"></i>Reset</button>
                                    </div>
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
@endsection
