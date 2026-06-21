@extends("shared.base", ["title" => "PDF Viewer"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["title" => "PDF Viewer", "subtitle" => "Plugins"])

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="btn-group text-nowrap">
                                        <button class="btn btn-dark" id="prev">
                                            <i data-lucide="arrow-left"></i>
                                            <span class="d-none d-sm-inline ms-2">Previous</span>
                                        </button>
                                        <button class="btn btn-dark" id="next">
                                            <i data-lucide="arrow-right"></i>
                                            <span class="d-none d-sm-inline ms-2">Next</span>
                                        </button>
                                        <button class="btn btn-dark" id="zoomin">
                                            <i data-lucide="zoom-in"></i>
                                            <span class="d-none d-sm-inline ms-2">Zoom In</span>
                                        </button>
                                        <button class="btn btn-dark" id="zoomout">
                                            <i data-lucide="zoom-out"></i>
                                            <span class="d-none d-sm-inline ms-2">Zoom Out</span>
                                        </button>
                                        <button class="btn btn-dark rounded-end-3" id="zoomfit">100%</button>
                                        <input class="form-control rounded-end-0 ms-1" id="page_num" style="width: 50px" type="text" />
                                        <span class="input-group-text rounded-start-0 border-start-0" id="page_count">/ 00</span>
                                    </div>
                                </div>
                                <div class="text-center overflow-auto mt-3">
                                    <canvas class="pdfcanvas border rounded-3" id="the-canvas"></canvas>
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
    @vite(["resources/js/pages/plugins-pdf-viewer.js"])
@endsection
