<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Template</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#4f46e5;padding:20px;text-align:center;color:#ffffff;">
                            <h2 style="margin:0;">Your One-Time Password (OTP)</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="font-size:16px;color:#333;margin-bottom:20px;">
                                Hello,
                            </p>

                            <p style="font-size:15px;color:#555;margin-bottom:25px;">
                                You have requested a one-time password (OTP) for authentication. Please use the OTP below to complete your reset password process.
                            </p>

                            <!-- OTP Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;text-align:center;">
                                <tr>
                                    <td style="padding:10px 0;">
                                        <span style="font-size:24px;font-weight:bold;color:#111;">{{ $otp }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px;color:#777;margin-top:30px;">
                                This OTP is valid for the next 5 minutes. If you did not request this OTP, please ignore this email.
                            </p>

                            <p style="font-size:15px;color:#333;margin-top:25px;">
                                Best regards,<br>
                                <strong>LAKE NORMAN LABS</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                </table>
            </td>
        </tr>
    </table>