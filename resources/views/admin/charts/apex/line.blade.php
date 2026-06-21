@extends("shared.base", ["title" => "Line Apexcharts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Line Apexchart"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Simple line chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Line with Data Labels</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-datalabel"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Zoomable Timeseries</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-zoomable"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Line Chart with Annotations</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-annotations"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Syncing charts</h4>
                            </div>
                            <div class="card-body">
                                <div id="line-chart-syncing2"></div>
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-syncing"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Gradient Line Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-gradient"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Missing / Null values</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-missing"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dashed Line Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="line-chart-dashed"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Stepline Chart</h4>
                            </div>
                            <div class="card-body">
                                <div class="apex-charts" id="line-chart-stepline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Brush Chart</h4>
                            </div>
                            <div class="card-body">
                                <div class="apex-charts" id="chart-line2"></div>
                                <div class="apex-charts" id="chart-line"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Realtime Chart</h4>
                            </div>
                            <div class="card-body">
                                <div class="apex-charts" id="line-chart-realtime"></div>
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
    @vite(["resources/js/pages/chart-apex-line.js"])
@endsection
