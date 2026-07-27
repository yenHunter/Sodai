@extends('visitor.layout.app', ['title' => 'Reset Password', 'bodyClass' => ''])

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
                            <h2 class="ec-breadcrumb-title">Reset Password</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Reset Password</li>
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
                        <h2 class="ec-bg-title">Reset Password</h2>
                        <h2 class="ec-title">Reset Password</h2>
                        <p class="sub-title mb-3">Choose a new password for your account</p>
                    </div>
                </div>

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
                            <form id="resetPasswordForm" action="{{ route('visitor.password.update') }}" method="post">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <span class="ec-login-wrap">
                                    <label>Email</label>
                                    <input type="email" value="{{ $email }}" readonly />
                                    <input type="hidden" name="email" value="{{ $email }}">
                                </span>
                                <span class="ec-login-wrap">
                                    <label>New Password*</label>
                                    <input type="password" name="password" placeholder="Enter new password" required />
                                    <small class="text-muted">At least 8 characters, with uppercase, lowercase, and a number.</small>
                                </span>
                                <span class="ec-login-wrap">
                                    <label>Confirm New Password*</label>
                                    <input type="password" name="password_confirmation" placeholder="Confirm new password" required />
                                </span>

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <input type="hidden" id="recaptchaSiteKey" value="{{ config('services.recaptcha.site_key') }}">

                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" type="submit" id="resetBtn">Update</button>
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
    @vite(['resources/js/pages/visitor-auth-reset-password.js'])
@endsection