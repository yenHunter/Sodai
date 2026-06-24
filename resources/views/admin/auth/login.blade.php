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
                            {{-- Session success message --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2"
                                    role="alert">
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="alert"
                                        type="button"></button>
                                    <i class="fs-xl" data-lucide="circle-alert"></i>
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- Session error message --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2"
                                    role="alert">
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="alert"
                                        type="button"></button>
                                    <i class="fs-xl" data-lucide="circle-alert"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

                            {{-- Validation errors bag --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2"
                                    role="alert">
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="alert"
                                        type="button"></button>
                                    <i class="fs-xl" data-lucide="octagon-alert"></i>
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif
                            <form id="adminLoginForm" class="mt-4" action="{{ route('admin.login.attempt') }}"
                                method="POST" autocomplete="off" novalidate>
                                @csrf
                                <input type="hidden" id="recaptchaSiteKey"
                                    value="{{ config('services.recaptcha.site_key') }}">

                                {{-- reCAPTCHA v3 token field --}}
                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <div class="mb-3">
                                    <label class="form-label" for="email">
                                        Email address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control" id="email" name="email" type="email"
                                            value="{{ old('email') }}" placeholder="account@mail.com" required
                                            autofocus />
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
                                    <button class="btn btn-primary fw-bold py-2 btn-login" type="submit"
                                        id="loginBtn">Sign In</button>
                                </div>
                            </form>
                        </div>
                        <p class="text-center text-muted mt-auto mb-0">
                            © {{ date('Y') }} <span class="fw-bold">Cyberjatra</span>
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
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @vite(['resources/js/pages/admin-auth-login.js'])
@endsection
