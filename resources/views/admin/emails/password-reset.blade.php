<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #1a1a2e;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .email-body p {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn-reset {
            display: inline-block;
            padding: 14px 32px;
            background-color: #1a1a2e;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
        }
        .expire-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 14px;
            color: #856404;
            margin-top: 20px;
        }
        .url-fallback {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 12px;
            font-size: 12px;
            color: #6c757d;
            word-break: break-all;
            margin-top: 16px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">

        {{-- Header --}}
        <div class="email-header">
            <h1>🔐 Password Reset</h1>
        </div>

        {{-- Body --}}
        <div class="email-body">
            <p>Hello, <strong>{{ $adminName }}</strong>!</p>

            <p>
                We received a request to reset the password for your
                <strong>{{ config('app.name') }}</strong> admin account.
                Click the button below to reset your password.
            </p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset">
                    Reset My Password
                </a>
            </div>

            <div class="expire-notice">
                ⏰ This link will expire in
                <strong>{{ $expiresInMinutes }} minutes</strong>.
            </div>

            <p style="margin-top: 20px;">
                If you did not request a password reset, please ignore this
                email or contact the super admin immediately if you believe
                someone is trying to access your account.
            </p>

            <p>If the button doesn't work, copy and paste this URL:</p>
            <div class="url-fallback">
                {{ $resetUrl }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="email-footer">
            <p>
                This email was sent to you because a password reset was
                requested for your admin account at
                <strong>{{ config('app.name') }}</strong>.
            </p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

    </div>
</body>
</html>