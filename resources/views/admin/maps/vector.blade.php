@extends("shared.base", ["title" => "Vector Maps"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["subtitle" => "Maps", "title" => "Vector Maps"])

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">World Vector Map</h5>
                                <p class="text-muted mb-0">A global map showing countries with interactive markers.</p>
                            </div>
                            <div class="card-body">
                                <div id="world-map-markers" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">World Vector Live Map</h5>
                                <p class="text-muted mb-0">Live dynamic vector representation of the world with real-time features.</p>
                            </div>
                            <div class="card-body">
                                <div id="world-map-markers-line" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">US Vector Map</h5>
                                <p class="text-muted mb-0">Interactive vector map of the United States with state-level details.</p>
                            </div>
                            <div class="card-body">
                                <div id="usa-vector-map" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">India Vector Map</h5>
                                <p class="text-muted mb-0">Detailed vector map of India with region highlights.</p>
                            </div>
                            <div class="card-body">
                                <div id="india-vector-map" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">Canada Vector Map</h5>
                                <p class="text-muted mb-0">Displays Canadian provinces and territories with interactive regions.</p>
                            </div>
                            <div class="card-body">
                                <div id="canada-vector-map" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">Russia Vector Map</h5>
                                <p class="text-muted mb-0">Interactive map highlighting major regions across Russia.</p>
                            </div>
                            <div class="card-body">
                                <div id="russia-vector-map" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">Iraq Vector Map</h5>
                                <p class="text-muted mb-0">Vector visualization of Iraq highlighting provinces and regions.</p>
                            </div>
                            <div class="card-body">
                                <div id="iraq-vector-map" style="height: 360px"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header d-block">
                                <h5 class="mb-1 card-title">Spain Vector Map</h5>
                                <p class="text-muted mb-0">Geographical map of Spain with region-based interaction.</p>
                            </div>
                            <div class="card-body">
                                <div id="spain-vector-map" style="height: 360px"></div>
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
    @vite(["resources/js/pages/maps-vector.js"])
@endsection
