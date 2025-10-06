<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA Reset</title>
</head>
<body>
    <h2>Hello {{ $user->first_name }},</h2>
    <p>Your Two-Factor Authentication (2FA) has been reset by the administrator.</p>
    <p>Please scan the QR code below in your Google Authenticator app:</p>

    <div style="text-align:center; margin:20px 0;">
        {!! $qrSvg !!}
    </div>

    <p>After scanning, log in with your email & password, then enter the OTP from your phone.</p>
    <p>If you didn’t request this reset, please contact support immediately.</p>

    <br>
    <p>– {{ config('app.name') }} Team</p>
</body>
</html>
