<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Declined</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #d9534f;">Car Wash Appointment Declined</h2>
        <p>Hi {{ $customer_name }},</p>
        <p>We regret to inform you that your car wash appointment at <strong>{{ $car_wash_name }}</strong> has been declined.</p>

        <p><strong>Reason:</strong> {{ $decline_reason }}</p>

        <p>We apologize for the inconvenience. Please contact us if you’d like to reschedule or have any questions.</p>

        <br>
        <p>Thank you,<br><strong>{{ $car_wash_name }}</strong><br>{{ $car_wash_address }}</p>
    </div>
</body>
</html>
