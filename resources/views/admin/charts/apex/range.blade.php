@extends("shared.base", ["title" => "Range Apexcharts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Range Apexcharts"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Range Area</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="basic-range"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Range Area With Line</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="combo-range"></div>
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
    @vite(["resources/js/pages/chart-apex-range.js"])
@endsection
