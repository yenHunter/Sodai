@extends("shared.base", ["title" => "Data Rendering Datatables"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Datatables", "title" => "Data Rendering"])

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header justify-content-between">
                                <h4 class="card-title">Example</h4>
                                <a class="icon-link icon-link-hover link-primary fw-semibold" href="https://datatables.net/examples/basic_init/data_rendering.html" target="_blank">
                                    View Docs
                                    <i class="bi align-middle fs-lg" data-lucide="arrow-right"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                                    <strong>Note:</strong>
                                    This is a jQuery-based plugin, so you need to include jQuery for it to work.
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="alert" type="button"></button>
                                </div>
                                <table class="table table-striped dt-responsive align-middle mb-0" id="datatable-rendering">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Progress</th>
                                            <th>Start date</th>
                                            <th>Salary</th>
                                        </tr>
                                    </thead>
                                </table>
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
    @vite(["resources/js/pages/datatables-rendering.js"])
@endsection
