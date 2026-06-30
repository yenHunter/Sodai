@extends('admin.include.base', ['title' => 'New Password'])

@section('styles')
@endsection

@section('content')
    <div class="auth-box p-0 w-100">
        <div class="row w-100 g-0">
            <div class="col-md-auto">
                <div class="card auth-box-form border-0 mb-0">
                    <div class="position-absolute top-0 end-0" style="width: 180px">
                        <img alt="auth-card-bg" class="auth-card-bg-img" src="/images/auth-card-bg.svg" />
                    </div>
                    <div class="card-body min-vh-100 position-relative d-flex flex-column justify-content-center">
                        <div class="auth-brand mb-0 text-center">
                            <a class="logo-dark" href="{{ url('/') }}">
                                <img alt="dark logo" src="/images/logo-black.png" />
                            </a>
                            <a class="logo-light" href="{{ url('/') }}">
                                <img alt="logo" src="/images/logo.png" />
                            </a>
                        </div>
                        <div class="mt-auto">
                            <p class="text-muted text-center auth-sub-text mx-auto">We've emailed you a 6-digit verification
                                code. Please enter it below to confirm your Email Address.</p>

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2"
                                    role="alert">
                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="alert"
                                        type="button"></button>
                                    <i class="fs-xl" data-lucide="circle-alert"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

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
                            <div id="jsErrorContainer" class="alert alert-danger d-none mb-3"></div>
                            <form class="mt-4" id="resetPasswordForm" action="{{ route('admin.reset-password.attempt') }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" id="recaptchaSiteKey"
                                    value="{{ config('services.recaptcha.site_key') }}">
                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <div class="mb-3">
                                    <label class="form-label" for="email">
                                        Email address
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control" type="email" value="{{ $email }}"
                                            placeholder="account@email.com" required readonly />
                                        <i class="app-search-icon text-muted" data-lucide="mail"></i>
                                    </div>
                                </div>
                                <div class="mb-3" data-password="bar">
                                    <label class="form-label" for="password">
                                        New Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" placeholder="••••••••" type="password" required autofocus />
                                        <i class="app-search-icon text-muted" data-lucide="lock-keyhole"></i>
                                    </div>
                                    <div class="password-bar my-2"></div>
                                    <p class="text-muted fs-xs mb-0">Use 8+ characters with letters, numbers &amp; symbols.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password_confirmation">
                                        Confirm New Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="app-search">
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation" placeholder="••••••••"
                                            type="password" required autofocus />
                                        <i class="app-search-icon text-muted" data-lucide="lock-keyhole"></i>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="form-check">
                                        <input class="form-check-input form-check-input-light fs-14" id="termAndPolicy"
                                            type="checkbox" />
                                        <label class="form-check-label" for="termAndPolicy">Agree the Terms &amp;
                                            Policy</label>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary fw-semibold py-2 btn-login" type="submit"
                                        id="resetBtn">Update Password</button>
                                </div>
                            </form>
                        </div>
                        <p class="text-muted text-center mt-4 mb-0">
                            Return to
                            <a class="text-decoration-underline link-offset-3 fw-semibold"
                                href="{{ route('admin.login.view') }}">Sign in</a>
                        </p>
                        <p class="text-center text-muted mt-auto mb-0">
                            © {{ date('Y') }} <a class="fw-bold" href="https://www.cyberjatra.com"
                                target="_blank">Cyberjatra</a>
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

    @include('admin.include.partials.footer-scripts')
@endsection

@section('scripts')
    <!-- ================== Google reCaptcha ================== -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @vite(['resources/js/pages/auth-password.js'])
    @vite(['resources/js/pages/admin-auth-reset-password.js'])
@endsection
