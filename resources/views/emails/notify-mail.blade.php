<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:30px 0;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

    <!-- Header -->
    <tr>
        <td style="background:#111827; padding:20px; text-align:center;">
            <h2 style="color:#ffffff; margin:0;">
                {{ config('app.name') }} - Admin Notification
            </h2>
        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="padding:30px; color:#333333;">

            <h3 style="margin-top:0; color:#111827;">
                {{ $title }}
            </h3>

            <table width="100%" cellpadding="8" cellspacing="0" style="margin-top:15px; border-collapse: collapse;">
                @foreach($data as $key => $value)
                    @if($key !== 'title')
                        <tr>
                            <td style="background:#f9fafb; border:1px solid #e5e7eb; font-weight:bold; width:35%;">
                                {{ ucfirst(str_replace('_',' ',$key)) }}
                            </td>
                            <td style="border:1px solid #e5e7eb;">
                                {{ $value }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>

        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb; padding:20px; text-align:center; font-size:12px; color:#6b7280;">
            This is an automated message from {{ config('app.name') }}.
            <br>
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>