@extends('admin.include.base', ['title' => 'Two-Factor Authentication'])

@section('styles')
@endsection

@section('content')
    <div class="auth-box p-0 w-100">
        <div class="row w-100 g-0">
            <div class="col-md-auto">
                <div class="card auth-box-form border-0 mb-0">
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
                            <p class="text-muted text-center auth-sub-text mx-auto">
                                Enter the 6-digit code from your authenticator app. You can also use one of your
                                recovery codes.
                            </p>

                            @if ($errors->any())
                                <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                                    <i class="fs-xl" data-lucide="octagon-alert"></i>
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif

                            <form class="mt-4" action="{{ route('admin.two-factor.verify') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="code">Authentication code</label>
                                    <input class="form-control text-center @error('code') is-invalid @enderror"
                                        id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                                        autofocus required placeholder="123456 or XXXXX-XXXXX" />
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary fw-bold py-2" type="submit">Verify</button>
                                </div>
                            </form>

                            <p class="text-center mt-4 mb-0">
                                <a href="{{ route('admin.login.view') }}" class="text-muted">Back to sign in</a>
                            </p>
                        </div>
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
