@extends('visitor.layout.app', ['title' => 'Verify Your Email', 'bodyClass' => ''])

@section('content')
    <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row ec_breadcrumb_inner">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="ec-breadcrumb-title">Verify Email</h2>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <ul class="ec-breadcrumb-list">
                                <li class="ec-breadcrumb-item"><a href="{{ route('visitor.index') }}">Home</a></li>
                                <li class="ec-breadcrumb-item active">Verify Email</li>
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
                        <h2 class="ec-bg-title">Almost There!</h2>
                        <h2 class="ec-title">Verify Your Email</h2>
                        <p class="sub-title mb-3">
                            @if ($email)
                                We've sent a verification link to <strong>{{ $email }}</strong>.
                            @else
                                We've sent a verification link to your email address.
                            @endif
                            Click the link to activate your account.
                        </p>
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
                            <form action="{{ route('verification.send') }}" method="post">
                                @csrf
                                <span class="ec-login-wrap">
                                    <label>Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $email) }}"
                                        placeholder="Enter your email" required />
                                </span>
                                <span class="ec-login-wrap ec-login-btn">
                                    <button class="btn btn-primary" type="submit">Resend</button>
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
@endsection