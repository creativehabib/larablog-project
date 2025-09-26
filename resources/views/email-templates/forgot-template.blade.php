<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Password Reset</title>
    <style>
        /* CLIENT RESETS */
        body,table,td,a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table,td { mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { -ms-interpolation-mode:bicubic; }
        body { margin:0; padding:0; width:100% !important; height:100% !important; }
        table { border-collapse:collapse !important; }

        /* MOBILE */
        @media screen and (max-width:600px) {
            .container { width:100% !important; }
            .responsive-btn { width:100% !important; display:block !important; }
        }
    </style>
</head>
<body style="background-color:#f6f6f6; margin:0; padding:0;">
<!-- FULL WIDTH WRAPPER -->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#f6f6f6" align="center" style="padding:20px 10px;">
            <!-- CONTAINER -->
            <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="max-width:600px; width:100%; background:#ffffff; border-radius:6px;">
                <!-- HEADER -->
                <tr>
                    <td bgcolor="#0073e6" align="center" style="padding:20px; border-radius:6px 6px 0 0;">
                        <h1 style="margin:0; font-size:22px; font-family:Arial,sans-serif; color:#ffffff;">Reset Your Password</h1>
                    </td>
                </tr>
                <!-- BODY -->
                <tr>
                    <td align="left" style="padding:30px; font-family:Arial,sans-serif; font-size:15px; line-height:1.6; color:#333333;">
                        <p style="margin:0 0 16px;">Hello, {{ $user->name }}</p>
                        <p style="margin:0 0 16px;">We received a request to reset your password. Click the button below to set a new password:</p>
                        <!-- BUTTON -->
                        <table border="0" cellspacing="0" cellpadding="0" align="center" style="margin:20px 0;">
                            <tr>
                                <td align="center" bgcolor="#0073e6" style="border-radius:5px;">
                                    <a href="{{ $actionlink }}" target="_blank" class="responsive-btn"
                                       style="font-size:16px; font-family:Arial,sans-serif; color:#ffffff; text-decoration:none; padding:14px 24px; display:inline-block; border-radius:5px; background-color:#0073e6;">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <!-- FALLBACK LINK -->
                        <p style="margin: 0 0 12px">This link is valid for 15 minutes.</p>
                        <p style="margin:0 0 12px;">If the button doesn’t work, copy and paste this link into your browser:</p>
                        <p style="word-break:break-all; font-size:14px;">
                            <a href="{{ $actionlink }}" target="_blank" style="color:#0073e6;">{{ $actionlink }}</a>
                        </p>
                        <p style="margin:20px 0 0;">If you didn’t request this reset, you can ignore this email or contact support if you have concerns.</p>
                        <p style="margin:20px 0 0;">Thanks,<br>The Support Team</p>
                    </td>
                </tr>
                <!-- FOOTER -->
                <tr>
                    <td align="center" bgcolor="#f6f6f6" style="padding:20px; font-family:Arial,sans-serif; font-size:12px; color:#999999; border-radius:0 0 6px 6px;">
                        &copy; 2024 Your Company. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
