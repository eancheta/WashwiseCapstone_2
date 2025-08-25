<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your appointment is confirmed</title>
</head>
<body>
  <p>Hello {{ $appointment->name ?? 'Customer' }},</p>

  <p>Thank you for booking with <strong>WashWise</strong>! 🎉</p>

  <p>Your appointment has been successfully confirmed. Below are your booking details:</p>

  <ul>
    <li><strong>Service:</strong> {{ $appointment->service_name ?? 'Car Wash' }}</li>
    <li><strong>Date & Time:</strong> {{ ($appointment->date_of_booking ?? 'N/A') }} {{ ($appointment->time_of_booking ?? '') }}</li>
    <li><strong>Car Wash Location:</strong> {{ $appointment->shop_name ?? $appointment->car_wash_name ?? 'WashWise Location' }}</li>
    <li><strong>Reservation Fee:</strong> 50php</li>
  </ul>

  <p>Please arrive on time for your appointment. For any changes, you can manage your booking directly in your WashWise account (or contact the shop).</p>

  <p>We look forward to serving you!</p>

  <p>Best regards,<br>
  WashWise Team</p>
</body>
</html>
