@extends("shared.base", ["title" => "Sparkline Apexcharts"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Charts", "title" => "Sparkline Apexcharts"])

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3" dir="ltr">
                                    <div class="col-md-4">
                                        <div class="apex-charts" id="spark1"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="apex-charts" id="spark2"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="apex-charts" id="spark3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-centered table-custom mb-0">
                                            <thead class="bg-light bg-opacity-50 fs-xxs thead-sm text-uppercase">
                                                <tr>
                                                    <th>Total Value</th>
                                                    <th>Percentage of Portfolio</th>
                                                    <th>Last 10 days</th>
                                                    <th>Volume</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>$32,554</td>
                                                    <td>15%</td>
                                                    <td>
                                                        <div id="chart1"></div>
                                                    </td>
                                                    <td>
                                                        <div id="chart5"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>$23,533</td>
                                                    <td>7%</td>
                                                    <td>
                                                        <div id="chart2"></div>
                                                    </td>
                                                    <td>
                                                        <div id="chart6"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>$54,276</td>
                                                    <td>9%</td>
                                                    <td>
                                                        <div id="chart3"></div>
                                                    </td>
                                                    <td>
                                                        <div id="chart7"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>$11,533</td>
                                                    <td>2%</td>
                                                    <td>
                                                        <div id="chart4"></div>
                                                    </td>
                                                    <td>
                                                        <div id="chart8"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
    @vite(["resources/js/pages/chart-apex-sparklines.js"])
@endsection
