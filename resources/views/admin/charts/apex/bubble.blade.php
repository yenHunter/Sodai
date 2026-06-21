@extends("shared.base", ["title" => "Apex Bubble Charts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Bubble Apexchart"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Simple Bubble Charts</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="simple-bubble"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">3D Bubble Charts</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="threed-bubble"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Bubble Charts</h4>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="apex-charts" id="three-bubble"></div>
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
    @vite(["resources/js/pages/chart-apex-bubble.js"])
@endsection
