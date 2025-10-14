<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Declined - WashWise</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            max-width: 650px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        h1 {
            color: #182235;
            font-size: 22px;
            margin-bottom: 15px;
        }
        .reason-box {
            background-color: #ffe5e5;
            border-left: 5px solid #FF2D2D;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            color: #991b1b;
        }
        a {
            color: #FF2D2D;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            font-size: 13px;
            color: #555;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 Application Declined - WashWise</h1>

        <p>Hello</p>

        <p>
            Thank you for applying to register with <strong>WashWise</strong>.
            After reviewing your information, we regret to inform you that
            your application has been <strong>declined</strong> at this time.
        </p>

        @if (!empty($reason))
        <div class="reason-box">
            <strong>Reason provided by the administrator:</strong><br>
            {{ $reason }}
        </div>
        @endif

        <p>
            This decision was made based on the evaluation of your submitted  details .
            You can reapply after addressing the issue or providing updated, valid information through our
            <a href="https://washwisecapstone2-production.up.railway.app">registration page</a>.
        </p>

        <p>
            For further assistance, you may contact our support team at
            <a href="mailto:washwise00@gmail.com">washwise00@gmail.com</a>.
        </p>

        <p>We appreciate your interest in partnering with WashWise and hope to work with you in the future.</p>

        <div class="footer">
            <strong>The WashWise Team</strong><br>
            <a href="https://washwisecapstone2-production.up.railway.app">www.washwise.ph</a> |
            <a href="mailto:washwise00@gmail.com">washwise00@gmail.com</a>
        </div>
    </div>
</body>
</html>
