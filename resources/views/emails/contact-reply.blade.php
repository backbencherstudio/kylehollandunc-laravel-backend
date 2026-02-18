<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Reply</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#4f46e5;padding:20px;text-align:center;color:#ffffff;">
                            <h2 style="margin:0;">New Reply Received from Lake Norman Labs</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="font-size:16px;color:#333;margin-bottom:20px;">
                                Hello {{ $reply->user_name ?? 'User' }},
                            </p>

                            <p style="font-size:15px;color:#555;margin-bottom:25px;">
                                You have received a reply to your contact message. Here are the details:
                            </p>

                            <!-- Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;">
                                <tr>
                                    <td style="padding:10px 0;">
                                        <strong style="color:#111;">Subject:</strong>
                                        <span style="color:#555;">{{ $reply->subject }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;">
                                        <strong style="color:#111;">Message:</strong><br>
                                        <span style="color:#555;line-height:1.6;">
                                            {{ $reply->description }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px;color:#777;margin-top:30px;">
                                If you have any questions, feel free to reply to this email.
                            </p>

                            <p style="font-size:15px;color:#333;margin-top:25px;">
                                Best regards,<br>
                                <strong>LAKE NORMAN LABS</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:15px;text-align:center;font-size:12px;color:#888;">
                            © {{ date('Y') }} All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
