@extends('visitor.layout.app', ['title' => 'Login', 'bodyClass' => ''])

@section('styles')
    <link rel="stylesheet" href="{{ asset('visitor/css/demo1.css') }}" />
@endsection

@push('head')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endpush

@section('content')
    <!-- Ec breadcrumb start -->
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Login</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Login</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ec breadcrumb end -->

    <!-- Ec login page -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Log In</h2>
                        <h2 class="ec-title">Log In</h2>
                        <p class="sub-title mb-3">Best place to buy and sell digital products</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="col-md-6 offset-md-3 mb-3">
                        <div class="alert alert-success">{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="col-md-6 offset-md-3 mb-3">
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('unverified_email'))
                    <div class="col-md-6 offset-md-3 mb-3">
                        <form action="{{ route('verification.send') }}" method="post"
                            class="d-flex gap-2 align-items-center flex-wrap">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                            <span class="mb-0">Didn't get the verification email?</span>
                            <button type="submit" class="btn btn-sm btn-secondary">Resend Link</button>
                        </form>
                    </div>
                @endif

                <div class="ec-login-wrapper">
                    <div class="ec-login-container">
                        <div class="ec-login-form">
                            <form id="customerLoginForm" action="{{ route('visitor.login.attempt') }}" method="post">
                                @csrf
                                <span class="ec-login-wrap">
                                    <label>Email Address*</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your email add..." required autofocus />
                                </span>
                                <span class="ec-login-wrap">
                                    <label>Password*</label>
                                    <input type="password" name="password" placeholder="Enter your password" required />
                                </span>
                                <span class="ec-login-wrap">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="remember" value="1" style="width:auto" />
                                        Remember Me
                                    </label>
                                </span>
                                <span class="ec-login-wrap ec-login-fp">
                                    <label><a href="{{ route('visitor.password.request') }}">Forgot Password?</a></label>
                                </span>

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <input type="hidden" id="recaptchaSiteKey"
                                    value="{{ config('services.recaptcha.site_key') }}">

                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" type="submit" id="loginBtn">Login</button>
                                    <a href="{{ route('visitor.register') }}" class="btn btn-secondary">Register</a>
                                </span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ec login page end -->
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-auth-login.js'])
@endsection
