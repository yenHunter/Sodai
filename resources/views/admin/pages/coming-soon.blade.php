@extends("shared.base", ["title" => "Coming Soon!"])

@section("styles")
@endsection

@section("content")
    <div class="auth-box p-0 w-100">
        <div class="row w-100 g-0">
            <div class="col-xxl-4 col-xl-6">
                <div class="card border-0 mb-0">
                    <div class="position-absolute top-0 end-0" style="width: 180px">
                        <img alt="auth-card-bg" class="auth-card-bg-img" src="/images/auth-card-bg.svg" />
                    </div>
                    <div class="card-body min-vh-100 d-flex flex-column justify-content-center">
                        <div class="auth-brand mb-0 text-center">
                            <a class="logo-dark" href="{{ url("/") }}">
                                <img alt="dark logo" src="/images/logo-black.png" />
                            </a>
                            <a class="logo-light" href="{{ url("/") }}">
                                <img alt="logo" src="/images/logo.png" />
                            </a>
                        </div>
                        <div class="mt-auto">
                            <div class="p-2 text-center">
                                <h3 class="fw-bold my-2">Something Exciting is Coming</h3>
                                <p class="text-muted mb-0">We’re working hard to bring you something amazing. Stay tuned!</p>
                                <div class="row text-center justify-content-center my-4 g-2">
                                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                                        <div class="bg-light bg-opacity-50 py-3 px-2 rounded">
                                            <h2 class="fw-bold text-primary fs-36" id="days">00</h2>
                                            <p class="fw-semibold fs-xs mb-0">Days</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                                        <div class="bg-light bg-opacity-25 py-3 px-2 rounded">
                                            <h3 class="fw-bold text-primary fs-36" id="hours">00</h3>
                                            <p class="fw-semibold fs-xs mb-0">Hours</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                                        <div class="bg-light bg-opacity-50 py-3 px-2 rounded">
                                            <h3 class="fw-bold text-primary fs-36" id="minutes">00</h3>
                                            <p class="fw-semibold fs-xs mb-0">Minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                                        <div class="bg-light bg-opacity-25 py-3 px-2 rounded">
                                            <h3 class="fw-bold text-primary fs-36" id="seconds">00</h3>
                                            <p class="fw-semibold fs-xs mb-0">Seconds</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="error-text-alt fs-xl">Stay tunned!</div>
                                <div class="app-search w-xl-75 mx-auto input-group mt-3 rounded-pill">
                                    <input class="form-control py-2" placeholder="Enter email..." type="text" />
                                    <i class="app-search-icon text-muted" data-lucide="mail"></i>
                                    <button class="btn btn-secondary" type="button">Notify me!</button>
                                </div>
                            </div>
                        </div>
                        <p class="text-center text-muted mt-auto mb-0">
                            ©
                            <span data-current-year=""></span>
                            UBold — by
                            <span class="fw-semibold">Coderthemes</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="h-100 position-relative card-side-img rounded-0 overflow-hidden">
                    <div class="p-4 card-img-overlay auth-overlay d-flex align-items-end justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>

    @include("shared.partials.footer-scripts")
@endsection

@section("scripts")
    @vite(["resources/js/pages/coming-soon.js"])
@endsection
