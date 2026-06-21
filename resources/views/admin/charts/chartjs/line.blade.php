@extends("shared.base", ["title" => "Line Charts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Line Charts"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Basic Line</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="basic-line-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Interpolation</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="interpolation-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Multi-Axes</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="multi-axes-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Point Styling</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="point-styling-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Line Segment</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="line-segment-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Stepped</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px">
                                        <canvas id="stepped-line-chart"></canvas>
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
    @vite(["resources/js/pages/chartjs-line.js"])
@endsection
