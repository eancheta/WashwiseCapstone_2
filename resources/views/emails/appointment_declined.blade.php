<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Declined</title>
</head>
<body>
    <h2>Hello {{ $customer_name }},</h2>

    <p>We regret to inform you that your car wash booking for <strong>{{ $service_name }}</strong>
    on <strong>{{ $date_time }}</strong> has been <strong>declined</strong>.</p>

    <p><strong>Reason:</strong> {{ $decline_reason }}</p>

    <p>If you have any questions, please contact <strong>{{ $car_wash_name }}</strong>
    located at <strong>{{ $car_wash_address }}</strong>.</p>

    <br>
    <p>Thank you for understanding.</p>
</body>
</html>
