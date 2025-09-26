<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Password Changed</title>
    <style>
        /* RESET */
        body,table,td,a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table,td { mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { -ms-interpolation-mode:bicubic; }
        body { margin:0; padding:0; width:100% !important; height:100% !important; }
        table { border-collapse:collapse !important; }

        /* MOBILE */
        @media screen and (max-width:600px) {
            .container { width:100% !important; }
            .content { padding:20px !important; }
        }
    </style>
</head>
<body style="background-color:#f6f6f6; margin:0; padding:0; font-family:Arial,sans-serif;">
<!-- WRAPPER -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#f6f6f6" align="center" style="padding:20px 10px;">
            <!-- CONTAINER -->
            <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="max-width:600px; width:100%; background:#ffffff; border-radius:6px; overflow:hidden;">
                <!-- HEADER -->
                <tr>
                    <td bgcolor="#0073e6" align="center" style="padding:20px;">
                        <h1 style="margin:0; font-size:20px; color:#ffffff;">Password Changed</h1>
                    </td>
                </tr>
                <!-- BODY -->
                <tr>
                    <td class="content" align="left" style="padding:30px; font-size:15px; line-height:1.6; color:#333333;">
                        <p style="margin:0 0 16px;">Hello, {{ $user->name }}</p>
                        <p style="margin:0 0 16px;">Your account password has been successfully updated. Here are your login details:</p>

                        <!-- INFO BOX -->
                        <table border="0" cellpadding="10" cellspacing="0" width="100%" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px;">
                            <tr>
                                <td style="font-size:14px; color:#111827;">
                                    <strong>Username/Email:</strong> {{ $user->email }} or {{ $user->username }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size:14px; color:#111827;">
                                    <strong>New Password:</strong> {{ $new_password }}
                                </td>
                            </tr>
                        </table>

                        <p style="margin:20px 0 0; font-size:14px; color:#555;">
                            Please keep this information safe. If you didn’t request this change, contact our support team immediately.
                        </p>

                        <p style="margin:20px 0 0;">Thanks,<br>The Support Team</p>
                    </td>
                </tr>
                <!-- FOOTER -->
                <tr>
                    <td align="center" bgcolor="#f6f6f6" style="padding:20px; font-size:12px; color:#999999;">
                        &copy; {{ date('Y') }} Larablog. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
