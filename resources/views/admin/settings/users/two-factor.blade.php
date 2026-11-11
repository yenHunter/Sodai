@extends('admin.include.vertical', ['title' => 'Two-Factor Authentication'])

@section('styles')
@endsection

@section('content')
    @include('admin.include.partials.page-title', ['subtitle' => 'Account', 'title' => 'Two-Factor Authentication'])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="me-2" data-lucide="circle-check"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            @if ($enabled)
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fs-xl text-success" data-lucide="shield-check"></i>
                        <h4 class="card-title mb-0">Two-factor authentication is enabled</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Signing in requires your password and a 6-digit code from your authenticator app.
                            Recovery codes can be used if you lose access to the app.
                        </p>

                        <form action="{{ route('admin.two-factor.disable') }}" method="POST" class="mt-3"
                            onsubmit="return confirm('Disable two-factor authentication for this account?');">
                            @csrf
                            @method('DELETE')
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="password">
                                    Confirm your password to disable
                                </label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password"
                                    name="password" type="password" required autocomplete="current-password" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="btn btn-danger" type="submit">
                                <i class="fs-sm me-1" data-lucide="shield-off"></i> Disable two-factor authentication
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fs-xl text-primary" data-lucide="shield"></i>
                        <h4 class="card-title mb-0">Set up two-factor authentication</h4>
                    </div>
                    <div class="card-body">
                        <ol class="text-muted mb-4">
                            <li class="mb-2">Add this secret key to your authenticator app (Google Authenticator,
                                Authy, 1Password…).</li>
                            <li class="mb-2">Enter the 6-digit code the app generates to confirm.</li>
                            <li>Store the recovery codes shown after confirmation somewhere safe.</li>
                        </ol>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Secret key</label>
                            <div class="input-group">
                                <input class="form-control font-monospace" type="text" value="{{ $secret }}" readonly
                                    onclick="this.select()" />
                                <a class="btn btn-light" href="{{ $otpauthUrl }}">Open in app</a>
                            </div>
                            <div class="form-text">
                                The secret is shown once during setup and stored encrypted after confirmation.
                            </div>
                        </div>

                        <form action="{{ route('admin.two-factor.enable') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="code">Verification code</label>
                                <input class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                                    type="text" inputmode="numeric" autocomplete="one-time-code" required
                                    placeholder="123456" />
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fs-sm me-1" data-lucide="shield-check"></i> Enable two-factor authentication
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if (session('recovery_codes'))
                <div class="card border-warning">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fs-xl text-warning" data-lucide="key-round"></i>
                        <h4 class="card-title mb-0">Save your recovery codes now</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">Each code works once. They will not be shown again.</p>
                        <pre class="bg-light p-3 rounded mb-0">@foreach (session('recovery_codes') as $recoveryCode){{ $recoveryCode }}
@endforeach</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
