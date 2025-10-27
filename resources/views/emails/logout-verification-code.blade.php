<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logout Verification Code</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .container { max-width: 600px; margin: 0 auto; padding: 16px; }
        .code { text-align: center; font-size: 28px; font-weight: 700; margin: 24px 0; letter-spacing: 4px; }
        .footer { color: #555; font-size: 12px; margin-top: 24px; }
    </style>
    </head>
<body>
    <div class="container">
        <h2>Logout Verification Code</h2>
        <p>Hello {{ $student->fname }},</p>
        <p>Your verification code for logging out is:</p>
        <div class="code">{{ $code }}</div>
        <p><strong>This code will expire in 2 minutes.</strong></p>
        <p>If you didn't request this, you can safely ignore this email.</p>
        <p class="footer">Thanks</p>
    </div>
</body>
</html>
