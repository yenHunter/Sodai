@extends("shared.base", ["title" => "Password Meter"])

@section("styles")
@endsection

@section("content")
    <div class="wrapper">
        @include("shared.partials.topbar") @include("shared.partials.sidenav")

        <div class="content-page">
            <div class="container-fluid">
                @include("shared.partials.page-title", ["title" => "Password Meter", "subtitle" => "Plugins"])

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Progress Bar</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <input class="form-control mb-2" placeholder="Password" type="password" />
                                <div class="password-bar mb-2"></div>
                                <p class="text-muted fs-xs mb-0">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Password Condition</h4>
                                <div class="card-action">
                                    <a class="card-action-item" data-action="card-toggle" href="#!">
                                        <i class="align-middle" data-lucide="chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-label" for="password-input">Magic Password ✨ (Click Here)</label>
                                    <input class="form-control" id="password-input" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="Enter password" type="password" />
                                    <div class="form-text">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div>
                                    <div class="password-box collapse bg-light-subtle border border-light mt-2 rounded">
                                        <div class="p-3">
                                            <h5 class="fs-sm mb-2">Password Recipe:</h5>
                                            <p class="invalid fs-xs mb-2" id="pass-length">
                                                Minimum
                                                <b>8 characters</b>
                                            </p>
                                            <p class="invalid fs-xs mb-2" id="pass-lower">
                                                At
                                                <b>lowercase</b>
                                                letter (a-z)
                                            </p>
                                            <p class="invalid fs-xs mb-2" id="pass-upper">
                                                At least
                                                <b>uppercase</b>
                                                letter (A-Z)
                                            </p>
                                            <p class="invalid fs-xs mb-0" id="pass-number">
                                                A least
                                                <b>number</b>
                                                (0-9)
                                            </p>
                                        </div>
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

    <script></script>
@endsection

@section("scripts")
    @vite(["resources/js/pages/plugins-pass-meter.js"])
@endsection
