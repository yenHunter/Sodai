@extends('visitor.layout.app', ['title' => 'Forgot Password', 'bodyClass' => ''])

@section('styles')
    <link rel="stylesheet" href="{{ asset('visitor/css/demo1.css') }}" />
@endsection

@push('head')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endpush

@section('content')
    <div class="sticky-header-next-sec  ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Forgot Password</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Forgot Password</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="ec-bg-title">Forgot Password</h2>
                        <h2 class="ec-title">Forgot Password</h2>
                        <p class="sub-title mb-3">Enter your email and we'll send you a reset link</p>
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

                <div class="ec-login-wrapper">
                    <div class="ec-login-container">
                        <div class="ec-login-form">
                            <form id="forgotPasswordForm" action="{{ route('visitor.password.email') }}" method="post">
                                @csrf
                                <span class="ec-login-wrap">
                                    <label>Email Address*</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your account email" required autofocus />
                                </span>

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <input type="hidden" id="recaptchaSiteKey" value="{{ config('services.recaptcha.site_key') }}">

                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" type="submit" id="sendLinkBtn">Send</button>
                                    <a href="{{ route('visitor.login') }}" class="btn btn-success">Login</a>
                                </span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @vite(['resources/js/pages/visitor-auth-forgot-password.js'])
@endsection