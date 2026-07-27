@extends('visitor.layout.app', ['title' => 'Register', 'bodyClass' => 'register_page'])

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
                            <h2 class="ec-breadcrumb-title">Register</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Register</li>
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
                        <h2 class="ec-bg-title">Register</h2>
                        <h2 class="ec-title">Register</h2>
                        <p class="sub-title mb-3">Best place to buy and sell digital products</p>
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

                <div class="ec-register-wrapper">
                    <div class="ec-register-container">
                        <div class="ec-register-form">
                            <form id="customerRegisterForm" action="{{ route('visitor.register.attempt') }}" method="post">
                                @csrf
                                <span class="ec-register-wrap">
                                    <label>Full Name*</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        placeholder="Enter your full name" required />
                                </span>
                                <span class="ec-register-wrap ec-register-half">
                                    <label>Email*</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your email add..." required />
                                </span>
                                <span class="ec-register-wrap ec-register-half">
                                    <label>Phone Number <small>(Optional)</small></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        placeholder="Enter your phone number" />
                                </span>
                                <span class="ec-register-wrap ec-register-half">
                                    <label>Password*</label>
                                    <input type="password" name="password" placeholder="Create a password" required />
                                </span>
                                <span class="ec-register-wrap ec-register-half">
                                    <label>Confirm Password*</label>
                                    <input type="password" name="password_confirmation" placeholder="Confirm your password"
                                        required />
                                </span>
                                <span class="ec-register-wrap">
                                    <small class="text-muted">At least 8 characters, with uppercase, lowercase and a
                                        number.</small>
                                </span>

                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <input type="hidden" id="recaptchaSiteKey"
                                    value="{{ config('services.recaptcha.site_key') }}">

                                <span class="ec-register-wrap ec-register-btn">
                                    <button class="btn btn-primary" type="submit" id="registerBtn">Register</button>
                                </span>
                                <span class="ec-register-wrap text-center mt-3">
                                    Already have an account? <a href="{{ route('visitor.login') }}">Login here</a>
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
    @vite(['resources/js/pages/visitor-auth-register.js'])
@endsection
