<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Declined - WashWise</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h1>🏢 Verify Your WashWise Business Owner Account</h1>

    <p>Hello {{ $owner->name }},</p>

    <p>
        Thank you for applying to register your car wash business with <strong>WashWise</strong>.
        After reviewing the information and Business Permit you provided, we regret to inform you
        that your application has been <strong>declined</strong> at this time.
    </p>

    <p>
        This decision was made based on discrepancies or issues found in the uploaded documents
        and business information.
    </p>

    <p>
        If you believe this decision was made in error or would like to reapply, you may do so by
        submitting updated and valid information through our
        <a href="{{ url('/owner/register') }}" style="color: #4CAF50; text-decoration: none;">registration page</a>.
    </p>

    <p>
        For further assistance, you may contact our support team at
        <a href="mailto:support@washwise.ph" style="color: #4CAF50;">support@washwise.ph</a>.
    </p>

    <p>
        We appreciate your interest in WashWise and hope to work with you in the future.
    </p>

    <p>Best regards,</p>
    <p>
        <strong>The WashWise Team</strong><br>
        <a href="https://www.washwise.ph" style="color: #4CAF50;">www.washwise.ph</a> |
        <a href="mailto:support@washwise.ph" style="color: #4CAF50;">support@washwise.ph</a>
    </p>
</body>
</html>
