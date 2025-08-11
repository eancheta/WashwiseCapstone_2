<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Verify Your WashWise Business Owner Account</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#222; line-height:1.4;">
  <p>Hello {{ $name ?? 'Owner' }},</p>

  <p>Thank you for registering your car wash business with WashWise.</p>

  <p>Before we can activate your account, please verify your email address by entering the code below:</p>

  <p style="font-weight:700; font-size:18px; padding:10px; background:#f4f4f4; display:inline-block; border-radius:4px;">
    🔐 Your Verification Code: {{ $code }}
  </p>

  <p>If you did not sign up as a business owner on WashWise, please disregard this email.</p>

  <p>Best regards,<br>
     The WashWise Team<br>
     <a href="https://www.washwise.ph">www.washwise.ph</a> | <a href="mailto:support@washwise.ph">support@washwise.ph</a>
  </p>
</body>
</html>
