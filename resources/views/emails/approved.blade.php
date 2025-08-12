<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Approved</title>
</head>
<body>
    <h1>Hello {{ $owner->name }},</h1>

    <p>Good news! Your car wash owner account has been approved. 🎉</p>

    <p>You can now log in to your account and start managing your business:</p>

    <p>
        <a href="{{ url('/owner/login') }}" style="background-color: #4CAF50; padding: 10px 15px; color: white; text-decoration: none;">
            Log In Now
        </a>
    </p>

    <p>Thank you for joining WashWise!</p>
</body>
</html>
