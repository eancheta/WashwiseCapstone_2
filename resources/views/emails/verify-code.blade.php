<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email for WashWise</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #2c3e50;">Verify Your Email for WashWise</h2>

        <p>Hi {{ $toName }},</p>

        <p>
            Thank you for signing up with <strong>WashWise</strong> – your go-to platform for hassle-free car wash bookings in Quezon City!
        </p>

        <p>
            To complete your registration, please verify your email address by entering the code below:
        </p>

        <h1 style="text-align: center; color: #FF2D2D; font-size: 36px; letter-spacing: 4px;">{{ $code }}</h1>

        <p>If you did not sign up for a WashWise account, please ignore this message.</p>

        <p style="margin-top: 30px;">
            Thank you,<br>
            <strong>The WashWise Team</strong><br>
            <a href="https://www.washwise.ph" style="color: #3490dc;">www.washwise.ph</a><br>
            <a href="mailto:support@washwise.ph" style="color: #3490dc;">support@washwise.ph</a>
        </p>
    </div>
</body>
</html>
