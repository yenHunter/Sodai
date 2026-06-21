@extends("shared.base", ["title" => "Apex Bar Charts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Bar Apexchart"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Bar Charts</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="basic-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grouped Bar Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="grouped-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Stacked Bar Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="stacked-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">100% Stacked Bar Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="full-stacked-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Grouped Stacked Bars</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="grouped-stacked-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Bar with Negative Values</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="negative-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Reversed Bar Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="reversed-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Bar with Image Fill</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="image-fill-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Custom DataLabels Bar</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="datalables-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Patterned Bar Chart</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="pattern-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Bar with Markers</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="bar-markers"></div>
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
    @vite(["resources/js/pages/chart-apex-bar.js"])
@endsection
