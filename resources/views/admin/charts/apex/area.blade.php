@extends("shared.base", ["title" => "Apex Area Charts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Area Apexchart"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Area Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="basic-area"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Spline Area</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="spline-area"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Area Chart - Datetime X-axis</h4>
                            </div>
                            <div class="card-body">
                                <div class="toolbar apex-toolbar">
                                    <button class="btn btn-sm btn-light" data-range="1M">1M</button>
                                    <button class="btn btn-sm btn-light" data-range="6M">6M</button>
                                    <button class="btn btn-sm btn-light active" data-range="1Y">1Y</button>
                                    <button class="btn btn-sm btn-light" data-range="YTD">YTD</button>
                                    <button class="btn btn-sm btn-light" data-range="ALL">ALL</button>
                                </div>
                                <div dir="ltr">
                                    <div class="apex-charts" id="area-chart-datetime"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Area with Negative Values</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="area-chart-negative"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Selection - Github Style</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="area-chart-github"></div>
                                    <div class="pt-2 pb-1">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <img alt="file-image" class="avatar-sm rounded" src="/images/logo-sm.png" />
                                            <a class="text-muted fw-bold fs-md" href="javascript:void(0);">UBold</a>
                                        </div>
                                    </div>
                                    <div class="apex-charts" id="area-chart-github2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Stacked Area</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="stacked-area"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Irregular TimeSeries</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="area-timeSeries"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Area Chart with Null values</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="area-chart-nullvalues"></div>
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
    <script src="https://apexcharts.com/samples/assets/github-data.js" type="module"></script>
    <script src="https://apexcharts.com/samples/assets/irregular-data-series.js" type="module"></script>
    @vite(["resources/js/pages/chart-apex-area.js"])
@endsection
