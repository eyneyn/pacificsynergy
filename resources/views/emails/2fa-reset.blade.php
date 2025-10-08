<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #edf2f7; padding: 30px;">
    <div style="max-width: 600px; margin: auto;">
        
        <!-- Header -->
        <div style="padding: 20px; text-align: center; color: #000000;">
            <h2 style="margin: 0;">{{ config('app.name') }}</h2>
        </div>

        <!-- Body -->
        <div style="padding: 30px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
            <h2 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                Hello {{ $user->first_name }}!
            </h2>

            <p style="font-size: 14px; color: #555; line-height: 1.6;">
                Your Two-Factor Authentication (2FA) has been <strong>reset by the administrator</strong>.  
                Please scan the QR code below in your Google Authenticator app to set it up again:
            </p>

            <!-- QR Code -->
            <div style="text-align: center; margin: 25px 0;">
                {!! $qrSvg !!}
            </div>

            <p style="font-size: 14px; color: #555; line-height: 1.6;">
                After scanning, log in with your email and password, then enter the OTP from your authenticator app.
            </p>

            <p style="font-size: 14px; color: #555; line-height: 1.6;">
                If you did not request this reset, please contact support immediately.
            </p>

            <br>
            <p style="font-size: 14px; color: #555;">
                Regards,  
                <br>{{ config('app.name') }} Team
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f4f6f8; padding: 15px; text-align: center; font-size: 12px; color: #888;">
            <p>If you’re having trouble scanning the QR code, please contact our support team.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
