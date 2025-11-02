<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Reminder</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hi {{ $name }},</h2>

    <p>This is a friendly reminder from <strong>WashWise</strong> about your upcoming car wash booking.</p>

    <ul>
        <li><strong>Shop:</strong> {{ $shopName }}</li>
        <li><strong>Address:</strong> {{ $address }}</li>
        <li><strong>Date:</strong> {{ $date }}</li>
        <li><strong>Time:</strong> {{ $time }}</li>
    </ul>

    <p>🕐 Please arrive at least 10 minutes before your scheduled time to avoid delays.</p>
    <p>Thank you for trusting WashWise!</p>

    <p>— The WashWise Team 🚗💧</p>
</body>
</html>
