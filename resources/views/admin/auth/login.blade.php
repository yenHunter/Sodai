@extends('admin.shared.base', ['title' => 'Login'])

@section('styles')
@endsection

@section('content')
    <div class="auth-box p-0 w-100">
        <div class="row w-100 g-0">
            <div class="col-md-auto">
                <div class="card auth-box-form border-0 mb-0">
                    <div class="position-absolute top-0 end-0" style="width: 180px">
                        <img alt="auth-card-bg" class="auth-card-bg-img" src="{{ asset('images/auth-card-bg.svg') }}" />
                    </div>
                    <div class="card-body min-vh-100 position-relative d-flex flex-column justify-content-center">
                        <div class="auth-brand mb-0 text-center">
                            <a class="logo-dark" href="{{ route('admin.login.view') }}">
                                <img alt="dark logo" src="{{ asset('images/logo-black.png') }}" />
                            </a>
                            <a class="logo-light" href="{{ route('admin.login.view') }}">
                                <img alt="logo" src="{{ asset('images/logo.png') }}" />
                            </a>
                        </div>
                        <div class="mt-auto">
                            <p class="text-muted text-center auth-sub-text mx-auto">Let’s get you signed in. Enter your
                                email and password to continue.</p>
                            {{-- Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Error Message --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <form id="login-form" class="mt-4" action="{{ route('admin.login.attempt') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="email">
                                        Email address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control" id="email" name="email"
                                            placeholder="account@mail.com" required="" type="email" />
                                        <i class="app-search-icon text-muted" data-lucide="mail"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password">
                                        Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control" id="password" name="password" placeholder="••••••••"
                                            required="" type="password" />
                                        <i class="app-search-icon text-muted" data-lucide="lock-keyhole"></i>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input checked="" class="form-check-input form-check-input-light fs-14"
                                            id="rememberMe" name="remember" type="checkbox" />
                                        <label class="form-check-label" for="rememberMe">Keep me signed in</label>
                                    </div>
                                    <a class="text-decoration-underline link-offset-3 text-muted"
                                        href="{{ route('admin.forgot-password.view') }}">Forgot Password?</a>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary fw-bold py-2" type="submit" id="submit_button"
                                        data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="onSubmit"
                                        data-action="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                        <p class="text-center text-muted mt-auto mb-0">
                            ©
                            <span data-current-year=""></span>
                            UBold — by
                            <span class="fw-bold">Coderthemes</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="h-100 position-relative card-side-img rounded-0 overflow-hidden"
                    style="background-image: url(/images/auth.jpg)">
                    <div class="p-4 card-img-overlay auth-overlay d-flex align-items-end justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.shared.partials.footer-scripts')
@endsection

@section('scripts')
    <!-- ================== Google reCaptcha ================== -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}">
    </script>
    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
@endsection
