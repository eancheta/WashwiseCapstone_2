<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to WashWise – Your Application is Approved!</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h1>Welcome to WashWise – Your Application is Approved!</h1>

    <p>Hello {{ $owner->name }},</p>

    <p>Congratulations! 🎉<br>
    Your WashWise application has been reviewed and approved. Your car wash is now officially part of our network.</p>

    <p><strong>You can now:</strong></p>
    <ul>
        <li>Log in to your WashWise account.</li>
        <li>Set up your service details, pricing, and schedule.</li>
        <li>Start accepting online bookings from customers in Quezon City.</li>
    </ul>

    <p>
        <a href="{{ url('/owner/login') }}"
           style="background-color: #4CAF50; padding: 10px 15px; color: white; text-decoration: none; border-radius: 4px;">
           Log In to Your Account
        </a>
    </p>

    <p>We’re excited to have you onboard and look forward to helping your business grow through our platform.</p>

    <p>If you need any assistance setting up your profile, our team is here to help at
        <a href="mailto:support@washwise.ph" style="color: #4CAF50;">support@washwise.ph</a>.
    </p>

    <p>Welcome to the WashWise community! 🚗✨</p>

    <p>Warm regards,</p>
    <p>
        <strong>The WashWise Team</strong><br>
        <a href="https://www.washwise.ph" style="color: #4CAF50;">www.washwise.ph</a> |
        <a href="mailto:support@washwise.ph" style="color: #4CAF50;">support@washwise.ph</a>
    </p>
</body>
</html>
