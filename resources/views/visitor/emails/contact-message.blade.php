<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New contact message</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f4;">
        <tbody>
            <tr>
                <td align="center" style="padding:30px 15px;">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0"
                        style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden;">
                        <tbody>
                            <tr>
                                <td style="background-color:#1a1a2e; padding:24px 30px;">
                                    <h1 style="margin:0; color:#ffffff; font-size:20px;">New contact message</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:30px;">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0"
                                        style="font-size:14px; color:#333333;">
                                        <tbody>
                                            <tr>
                                                <td style="padding-bottom:10px; width:110px; font-weight:bold;">Name</td>
                                                <td style="padding-bottom:10px;">{{ $senderName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:10px; font-weight:bold;">Email</td>
                                                <td style="padding-bottom:10px;">
                                                    <a href="mailto:{{ $senderEmail }}"
                                                        style="color:#1a1a2e;">{{ $senderEmail }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:10px; font-weight:bold;">Phone</td>
                                                <td style="padding-bottom:10px;">{{ $senderPhone }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <hr style="border:0; border-top:1px solid #e5e5e5; margin:20px 0;">

                                    <p style="margin:0 0 8px; font-size:14px; font-weight:bold; color:#333333;">
                                        Message
                                    </p>
                                    <p style="margin:0; font-size:14px; line-height:1.6; color:#333333;">
                                        {!! nl2br(e($messageBody)) !!}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#f8f9fa; padding:16px 30px; font-size:12px; color:#6c757d;">
                                    Sent from the {{ setting('company', 'name', config('app.name')) }} contact form.
                                    Reply directly to this email to answer the customer.
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
