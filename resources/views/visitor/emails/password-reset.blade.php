<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .btn-set {
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
    <table
        style="table-layout: fixed; vertical-align: top; min-width: 320px; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f1d0ff; width: 100%;user-select: none;"
        width="100%" cellspacing="0" cellpadding="0" bgcolor="#000000">
        <tbody>
            <tr style="vertical-align: top;" valign="top">
                <td style="word-break: break-word; vertical-align: top;" valign="top">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td style="background-color:#000000" align="center">
                                    <div style="background-color: #fff;">
                                        <div
                                            style="min-width: 320px; max-width: 650px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; margin: 0 auto; ">
                                            <div style="border-collapse: collapse; display: table; width: 100%; ">
                                                <table style="background-color:#385a64;" width="100%" cellspacing="0"
                                                    cellpadding="0" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center">
                                                                <table style="width:650px" cellspacing="0"
                                                                    cellpadding="0" border="0">
                                                                    <tbody>
                                                                        <tr style="background-color:transparent">
                                                                            <td style="background-color:transparent;width:650px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;"
                                                                                width="650" valign="top"
                                                                                align="center">
                                                                                <table width="100%" cellspacing="0"
                                                                                    cellpadding="0" border="0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td
                                                                                                style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;">
                                                                                                <div
                                                                                                    style="min-width: 320px; max-width: 650px; display: table-cell; vertical-align: top; width: 650px;">
                                                                                                    <div
                                                                                                        style="width: 100% !important;">
                                                                                                        <div
                                                                                                            style="border: 0px solid transparent; padding: 5px 0px 5px 0px;">
                                                                                                            <div style="padding-right: 0px; padding-left: 0px;"
                                                                                                                align="center">
                                                                                                                <table
                                                                                                                    width="100%"
                                                                                                                    cellspacing="0"
                                                                                                                    cellpadding="0"
                                                                                                                    border="0">
                                                                                                                    <tbody>
                                                                                                                        <tr
                                                                                                                            style="line-height:0px">
                                                                                                                            <td style="padding-right: 0px;padding-left: 0px;"
                                                                                                                                align="center">
                                                                                                                                <div
                                                                                                                                    style="font-size: 1px; line-height: 15px;">
                                                                                                                                    &nbsp;
                                                                                                                                </div>
                                                                                                                                <img style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 100%; max-width: 130px; display: block;"
                                                                                                                                    title="your logo"
                                                                                                                                    alt="your logo"
                                                                                                                                    src="{{ asset('visitor/images/logo/dark-logo.png') }}"
                                                                                                                                    width="130"
                                                                                                                                    border="0"
                                                                                                                                    align="middle">
                                                                                                                                <div
                                                                                                                                    style="font-size: 1px; line-height: 25px;">
                                                                                                                                    &nbsp;
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="background-color: #fff;">
                                        <div
                                            style="min-width: 320px; max-width: 650px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; margin: 0 auto; background-color: #f1d0ff;">
                                            <div
                                                style="border-collapse: collapse; display: table; width: 100%; background-color: #f1d0ff; background-image: url('https://d1oco4z2z1fhwp.cloudfront.net/templates/default/2971/bg-white-rombo.png'); background-position: top left; background-repeat: no-repeat;">
                                                <table style="background-color:#f3e6f8;" width="100%" cellspacing="0"
                                                    cellpadding="0" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center">
                                                                <table style="width:650px" cellspacing="0"
                                                                    cellpadding="0" border="0">
                                                                    <tbody>
                                                                        <tr style="background-color:#f1d0ff">
                                                                            <td style="background-color:#385a64;width:650px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;"
                                                                                width="650" valign="top"
                                                                                align="center">
                                                                                <table width="100%" cellspacing="0"
                                                                                    cellpadding="0" border="0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td style="   ">
                                                                                                <div
                                                                                                    style="min-width: 320px; max-width: 650px; display: table-cell; vertical-align: top; width: 650px;">
                                                                                                    <div
                                                                                                        style="width: 100% !important;">
                                                                                                        <div
                                                                                                            style="border: 0px solid transparent; padding: 0;">

                                                                                                            <div style=" "
                                                                                                                align="center">
                                                                                                                <table
                                                                                                                    width="100%"
                                                                                                                    cellspacing="0"
                                                                                                                    cellpadding="0"
                                                                                                                    border="0">
                                                                                                                    <tbody>
                                                                                                                        <tr
                                                                                                                            style="line-height:0px">
                                                                                                                            <td style=""
                                                                                                                                align="center">

                                                                                                                                <img style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; width: 100%; max-width: 100%; display: block;"
                                                                                                                                    title="Forgot your password?"
                                                                                                                                    alt="Forgot your password?"
                                                                                                                                    src="{{ asset('visitor/images/email-template/forgot-email-5.jpg') }}"
                                                                                                                                    width="325"
                                                                                                                                    border="0"
                                                                                                                                    align="middle">

                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                            <table
                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0">
                                                                                                                <tbody>
                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                        valign="top">
                                                                                                                        <td style="word-break: break-word; vertical-align: top; text-align: center; width: 100%; padding: 35px 0px 0px 0px;"
                                                                                                                            width="100%"
                                                                                                                            valign="top"
                                                                                                                            align="center">
                                                                                                                            <h1
                                                                                                                                style="color: #fff; direction: ltr; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; font-weight: normal; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
                                                                                                                                Hello,
                                                                                                                                <strong>{{ $customerName }}</strong>!
                                                                                                                            </h1>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                        valign="top">
                                                                                                                        <td style="word-break: break-word; vertical-align: top; text-align: center; width: 100%;"
                                                                                                                            width="100%"
                                                                                                                            valign="top"
                                                                                                                            align="center">
                                                                                                                            <h1
                                                                                                                                style="color: #fff; direction: ltr; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 28px; font-weight: normal; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
                                                                                                                                <strong>Forgot
                                                                                                                                    your
                                                                                                                                    password?</strong>
                                                                                                                            </h1>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <table
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td
                                                                                                                            style="padding-right: 45px; padding-left: 45px; padding-top: 10px; padding-bottom: 0px; font-family: Arial, sans-serif">
                                                                                                                            <div
                                                                                                                                style="color: #393d47; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.5; padding: 10px 45px 0px 45px;">
                                                                                                                                <div
                                                                                                                                    style="line-height: 1.5; font-size: 12px; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #393d47; mso-line-height-alt: 18px;">
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.5; word-break: break-word; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; mso-line-height-alt: 27px; margin: 0;">
                                                                                                                                        <span
                                                                                                                                            style="font-size: 18px; color: #fff;">We
                                                                                                                                            received
                                                                                                                                            a
                                                                                                                                            request
                                                                                                                                            to
                                                                                                                                            reset
                                                                                                                                            your
                                                                                                                                            password.</span>
                                                                                                                                    </p>
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.5; word-break: break-word; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 18px; mso-line-height-alt: 27px; margin: 0;">
                                                                                                                                        <span
                                                                                                                                            style="font-size: 18px; color: #fff;">If
                                                                                                                                            you
                                                                                                                                            didn't
                                                                                                                                            make
                                                                                                                                            this
                                                                                                                                            request,
                                                                                                                                            simply
                                                                                                                                            ignore
                                                                                                                                            this
                                                                                                                                            email.</span>
                                                                                                                                    </p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <table
                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                        valign="top">
                                                                                                                        <td style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 20px;"
                                                                                                                            valign="top">
                                                                                                                            <table
                                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 1px solid #D5E2E6; width: 80%;"
                                                                                                                                width="80%"
                                                                                                                                cellspacing="0"
                                                                                                                                cellpadding="0"
                                                                                                                                border="0"
                                                                                                                                align="center">
                                                                                                                                <tbody>
                                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                                        valign="top">
                                                                                                                                        <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                                                            valign="top">
                                                                                                                                            &nbsp;
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <table
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td
                                                                                                                            style="padding-right: 45px; padding-left: 45px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif">
                                                                                                                            <div
                                                                                                                                style="color: #393d47; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.5; padding: 10px 45px 10px 45px;">
                                                                                                                                <div
                                                                                                                                    style="line-height: 1.5; font-size: 12px; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center; color: #393d47; mso-line-height-alt: 18px;">
                                                                                                                                    <span
                                                                                                                                        style="font-size: 15px; color: #fff; mso-ansi-font-size: 14px;">
                                                                                                                                        If
                                                                                                                                        you
                                                                                                                                        did
                                                                                                                                        make
                                                                                                                                        this
                                                                                                                                        request
                                                                                                                                        just
                                                                                                                                        click
                                                                                                                                        the
                                                                                                                                        button
                                                                                                                                        below.
                                                                                                                                    </span>
                                                                                                                                    <br>
                                                                                                                                    <span
                                                                                                                                        style="font-size: 12px; color: #fff; mso-ansi-font-size: 14px;">
                                                                                                                                        This
                                                                                                                                        link
                                                                                                                                        will
                                                                                                                                        expire
                                                                                                                                        in
                                                                                                                                        <strong>{{ $expiresInMinutes }}
                                                                                                                                            minutes</strong>
                                                                                                                                    </span>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <div style="padding: 10px;"
                                                                                                                align="center">
                                                                                                                <table
                                                                                                                    style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                                                                                    width="100%"
                                                                                                                    cellspacing="0"
                                                                                                                    cellpadding="0"
                                                                                                                    border="0">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td style="padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px"
                                                                                                                                align="center">
                                                                                                                                <v:roundrect
                                                                                                                                    xmlns:v="urn:schemas-microsoft-com:vml"
                                                                                                                                    xmlns:w="urn:schemas-microsoft-com:office:word"
                                                                                                                                    href="{{ $resetUrl }}"
                                                                                                                                    style="height:40.5pt;width:236.25pt;v-text-anchor:middle;"
                                                                                                                                    arcsize="0%"
                                                                                                                                    strokeweight="0.75pt"
                                                                                                                                    strokecolor="#8412c0"
                                                                                                                                    fillcolor="#8412c0">
                                                                                                                                    <w:anchorlock>
                                                                                                                                        <v:textbox
                                                                                                                                            inset="0,0,0,0">
                                                                                                                                            <center
                                                                                                                                                style="color:#ffffff; font-family:Arial, sans-serif; font-size:14px">
                                                                                                                                                <a style="-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #fff; background-color: #385a64; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; width: auto; padding-top: 10px; padding-bottom: 10px; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all; border: 1px solid #fff;font-weight: 700;"
                                                                                                                                                    href="{{ $resetUrl }}"
                                                                                                                                                    target="_blank"><span
                                                                                                                                                        style="padding-left: 40px; padding-right: 40px; font-size: 14px; display: inline-block; letter-spacing: undefined;"><span
                                                                                                                                                            style="font-size: 16px; line-height: 2; word-break: break-word; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; mso-line-height-alt: 32px;"><span
                                                                                                                                                                style="font-size: 14px; line-height: 28px;">RESET
                                                                                                                                                                MY
                                                                                                                                                                PASSWORD</span></span></span></a>
                                                                                                                                            </center>
                                                                                                                                        </v:textbox>
                                                                                                                                    </w:anchorlock>
                                                                                                                                </v:roundrect>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                            <table
                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0">
                                                                                                                <tbody>
                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                        valign="top">
                                                                                                                        <td style="word-break: break-word; vertical-align: top; padding: 10px;"
                                                                                                                            valign="top">
                                                                                                                            <table
                                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-tspace: 0; mso-table-rspace: 0; mso-table-bspace: 0; mso-table-lspace: 0;"
                                                                                                                                cellspacing="0"
                                                                                                                                cellpadding="0"
                                                                                                                                align="center">
                                                                                                                                <tbody>
                                                                                                                                    <tr style="vertical-align: top; display: inline-block; text-align: center;"
                                                                                                                                        valign="top"
                                                                                                                                        align="center">
                                                                                                                                        <p
                                                                                                                                            style="margin: 0; font-size: 18px; color: #ffffff; line-height: 1.8; word-break: break-word; text-align: center; mso-line-height-alt: 32px; margin-top: 0; margin-bottom: 0;">
                                                                                                                                            <span
                                                                                                                                                style="font-size: 18px;">Follow
                                                                                                                                                us</span>
                                                                                                                                        </p>
                                                                                                                                    </tr>
                                                                                                                                    <tr style="vertical-align: top; display: inline-block; text-align: center; padding: 10px 15px 40px 15px;"
                                                                                                                                        valign="top"
                                                                                                                                        align="center">
                                                                                                                                        <td style="word-break: break-word; vertical-align: top; padding-bottom: 0; padding-right: 2.5px; padding-left: 2.5px;"
                                                                                                                                            valign="top">
                                                                                                                                            <a href="https://www.facebook.com/"
                                                                                                                                                target="_blank"><img
                                                                                                                                                    style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;"
                                                                                                                                                    title="facebook"
                                                                                                                                                    src="{{ asset('visitor/images/email-template/facebook_2.png') }}"
                                                                                                                                                    alt="Facebook"
                                                                                                                                                    width="32"
                                                                                                                                                    height="32"></a>
                                                                                                                                        </td>
                                                                                                                                        <td style="word-break: break-word; vertical-align: top; padding-bottom: 0; padding-right: 2.5px; padding-left: 2.5px;"
                                                                                                                                            valign="top">
                                                                                                                                            <a href="https://www.instagram.com/"
                                                                                                                                                target="_blank"><img
                                                                                                                                                    style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;"
                                                                                                                                                    title="instagram"
                                                                                                                                                    src="{{ asset('visitor/images/email-template/instagram_2.png') }}"
                                                                                                                                                    alt="Facebook"
                                                                                                                                                    width="32"
                                                                                                                                                    height="32"></a>
                                                                                                                                        </td>
                                                                                                                                        <td style="word-break: break-word; vertical-align: top; padding-bottom: 0; padding-right: 2.5px; padding-left: 2.5px;"
                                                                                                                                            valign="top">
                                                                                                                                            <a href="https://www.twitter.com/"
                                                                                                                                                target="_blank"><img
                                                                                                                                                    style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;"
                                                                                                                                                    title="twitter"
                                                                                                                                                    src="{{ asset('visitor/images/email-template/twitter_2.png') }}"
                                                                                                                                                    alt="Twitter"
                                                                                                                                                    width="32"
                                                                                                                                                    height="32"></a>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="background-color: #fff;">
                                        <div
                                            style="min-width: 320px; max-width: 650px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; margin: 0 auto; background-color: transparent;">
                                            <div
                                                style="border-collapse: collapse; display: table; width: 100%; background-color: transparent;">
                                                <table style="background-color:#ff4f5a;" width="100%"
                                                    cellspacing="0" cellpadding="0" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center">
                                                                <table style="width:650px" cellspacing="0"
                                                                    cellpadding="0" border="0">
                                                                    <tbody>
                                                                        <tr style="background-color:transparent">
                                                                            <td style="background-color:transparent;width:650px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;"
                                                                                width="650" valign="top"
                                                                                align="center">
                                                                                <table width="100%" cellspacing="0"
                                                                                    cellpadding="0" border="0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td
                                                                                                style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:10px;">
                                                                                                <div
                                                                                                    style="min-width: 320px; max-width: 650px; display: table-cell; vertical-align: top; width: 650px;">
                                                                                                    <div
                                                                                                        style="width: 100% !important;">
                                                                                                        <div
                                                                                                            style="border: 0px solid transparent; padding: 5px 0px 10px 0px;">
                                                                                                            <table
                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                        valign="top">
                                                                                                                        <td style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding: 5px;"
                                                                                                                            valign="top">
                                                                                                                            <table
                                                                                                                                style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid #BBBBBB; width: 100%;"
                                                                                                                                width="100%"
                                                                                                                                cellspacing="0"
                                                                                                                                cellpadding="0"
                                                                                                                                border="0"
                                                                                                                                align="center">
                                                                                                                                <tbody>
                                                                                                                                    <tr style="vertical-align: top;"
                                                                                                                                        valign="top">
                                                                                                                                        <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"
                                                                                                                                            valign="top">
                                                                                                                                            &nbsp;
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <table
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td
                                                                                                                            style="padding-right: 0px; padding-left: 0px; padding-top: 0px; padding-bottom: 20px; font-family: Arial, sans-serif">
                                                                                                                            <div
                                                                                                                                style="color: #393d47; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; line-height: 1.2; padding: 0px;">
                                                                                                                                <div
                                                                                                                                    style="line-height: 1.2; font-size: 12px; color: #393d47; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; mso-line-height-alt: 14px;">
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.2; word-break: break-word; font-size: 11px; mso-line-height-alt: 13px; margin: 0;">
                                                                                                                                        <span
                                                                                                                                            style="font-size: 13px; color: #fff;">
                                                                                                                                            ©
                                                                                                                                            {{ date('Y') }}
                                                                                                                                            {{ config('app.name') }}.
                                                                                                                                            All
                                                                                                                                            rights
                                                                                                                                            reserved.
                                                                                                                                        </span>
                                                                                                                                    </p>
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.2; word-break: break-word; mso-line-height-alt: 14px; margin: 0;">
                                                                                                                                        &nbsp;
                                                                                                                                    </p>
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.2; word-break: break-word; font-size: 11px; mso-line-height-alt: 13px; margin: 0;">
                                                                                                                                        <span
                                                                                                                                            style="font-size: 13px;">
                                                                                                                                            <span
                                                                                                                                                style="color: #fff;">
                                                                                                                                                If
                                                                                                                                                the
                                                                                                                                                button
                                                                                                                                                doesn't
                                                                                                                                                work,
                                                                                                                                                copy
                                                                                                                                                and
                                                                                                                                                paste
                                                                                                                                                this
                                                                                                                                                URL:
                                                                                                                                            </span>
                                                                                                                                        </span>
                                                                                                                                    </p>
                                                                                                                                    <p
                                                                                                                                        style="text-align: center; line-height: 1.2; word-break: break-word; font-size: 10px; mso-line-height-alt: 14px; margin: 0;">
                                                                                                                                        {{ $resetUrl }}
                                                                                                                                    </p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
