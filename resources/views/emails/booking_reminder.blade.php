<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Reminder</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hi {{ $customer_name }},</h2>

    <p>This is a friendly reminder from <strong>WashWise</strong> about your upcoming car wash booking.</p>

    <ul>
        <li><strong>Shop:</strong> {{ $car_wash_name }}</li>
        <li><strong>Address:</strong> {{ $car_wash_address }}</li>
        <li><strong>Date & Time:</strong> {{ $date_time }}</li>
        <li><strong>Service:</strong> {{ $service_name }}</li>
    </ul>

    <p>🕐 Please arrive at least 10 minutes before your scheduled time to avoid delays.</p>
    <p>Thank you for trusting WashWise!</p>

    <p>— The WashWise Team 🚗💧</p>
</body>
</html>
