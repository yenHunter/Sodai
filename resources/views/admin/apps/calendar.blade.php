@extends("shared.base", ["title" => "Calendar"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Apps", "title" => "Calendar"])

                <div class="d-flex gap-1">
                    <div class="card d-none d-lg-flex rounded-end-0">
                        <div class="card-body">
                            <button class="btn btn-primary w-100 btn-new-event">
                                <i class="me-2 align-middle" data-lucide="plus"></i>
                                Create New Event
                            </button>
                            <div id="external-events">
                                <p class="text-muted mt-2 fst-italic fs-xs mb-3">Drag and drop your event or click in the calendar</p>
                                <div class="external-event fc-event bg-primary-subtle text-primary fw-semibold" data-class="bg-primary-subtle text-primary border-primary">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Design Review
                                </div>
                                <div class="external-event fc-event bg-secondary-subtle text-secondary fw-semibold" data-class="bg-secondary-subtle text-secondary border-secondary">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Marketing Strategy
                                </div>
                                <div class="external-event fc-event bg-success-subtle text-success fw-semibold" data-class="bg-success-subtle text-success border-success">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Sales Demo
                                </div>
                                <div class="external-event fc-event bg-danger-subtle text-danger fw-semibold" data-class="bg-danger-subtle text-danger border-danger">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Deadline Submission
                                </div>
                                <div class="external-event fc-event bg-info-subtle text-info fw-semibold" data-class="bg-info-subtle text-info border-info">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Training Session
                                </div>
                                <div class="external-event fc-event bg-warning-subtle text-warning fw-semibold" data-class="bg-warning-subtle text-warning border-warning">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Budget Review
                                </div>
                                <div class="external-event fc-event bg-dark-subtle text-dark fw-semibold" data-class="bg-dark-subtle text-dark border-dark">
                                    <i class="me-2" data-lucide="circle"></i>
                                    Board Meeting
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded-start-0 flex-grow-1 border-start-0">
                        <div class="d-lg-none d-inline-flex card-header">
                            <button class="btn btn-primary btn-new-event">
                                <i class="me-2 align-middle" data-lucide="plus"></i>
                                Create New Event
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="event-modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form class="needs-validation" id="forms-event" name="event-form" novalidate="">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal-title">Create Event</h4>
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="control-label form-label" for="event-title">Event Name</label>
                                                <input class="form-control" id="event-title" name="title" placeholder="Insert Event Name" required="" type="text" />
                                                <div class="invalid-feedback">Please provide a valid event name</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="control-label form-label" for="event-category">Category</label>
                                                <select class="form-select" id="event-category" name="category" required="">
                                                    <option disabled="" value="">Select a category</option>
                                                    <option selected="" value="bg-primary-subtle text-primary">Primary</option>
                                                    <option value="bg-secondary-subtle text-secondary">Secondary</option>
                                                    <option value="bg-success-subtle text-success">Success</option>
                                                    <option value="bg-info-subtle text-info">Info</option>
                                                    <option value="bg-warning-subtle text-warning">Warning</option>
                                                    <option value="bg-danger-subtle text-danger">Danger</option>
                                                    <option value="bg-dark-subtle text-dark">Dark</option>
                                                </select>
                                                <div class="invalid-feedback">Please select a valid event category</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <button class="btn btn-danger" id="btn-delete-event" type="button">Delete</button>
                                        <button class="btn btn-light ms-auto" data-bs-dismiss="modal" type="button">Close</button>
                                        <button class="btn btn-primary" id="btn-save-event" type="submit">Save</button>
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
    @vite(["resources/js/pages/apps-calendar.js"])
@endsection
