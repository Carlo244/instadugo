<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #e63946;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #e63946;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
            font-size: 16px;
        }
        .appointment-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .appointment-box h3 {
            color: #e63946;
            margin-top: 0;
        }
        .appointment-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .appointment-details p {
            margin: 10px 0;
            font-size: 15px;
        }
        .detail-label {
            font-weight: bold;
            color: #e63946;
        }
        .cta-button {
            display: inline-block;
            background-color: #e63946;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }
        .cta-button:hover {
            background-color: #d62828;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 13px;
            color: #666;
        }
        .important-note {
            background-color: #ffe0e0;
            border-left: 4px solid #e63946;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .important-note strong {
            color: #e63946;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🩸 Donation Appointment Reminder</h1>
        </div>

        <div class="content">
            <p>Hi {{ $donorName }},</p>

            <p>This is a friendly reminder that you have a blood donation appointment scheduled <strong>tomorrow</strong>!</p>

            <div class="appointment-box">
                <h3>Your Appointment Details</h3>
                <div class="appointment-details">
                    <p><span class="detail-label">Hospital:</span> {{ $hospital }}</p>
                    <p><span class="detail-label">Date:</span> {{ \Carbon\Carbon::parse($donationDate)->format('l, F j, Y') }}</p>
                    <p><span class="detail-label">Time:</span> {{ \Carbon\Carbon::parse($donationTime)->format('h:i A') }}</p>
                    <p><span class="detail-label">Blood Type:</span> {{ $donation->blood_type }}</p>
                </div>
            </div>

            <div class="important-note">
                <strong>Please remember:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Arrive 10-15 minutes early</li>
                    <li>Bring a valid ID and proof of address</li>
                    <li>Stay hydrated before your appointment</li>
                    <li>Eat a light meal beforehand</li>
                    <li>Get adequate rest the night before</li>
                </ul>
            </div>

            <p>Your donation will help save lives. Thank you for your generous contribution to our community!</p>

            <p style="text-align: center;">
                <a href="{{ route('user.donate-schedule') }}" class="cta-button">View My Donation Schedule</a>
            </p>
        </div>

        <div class="footer">
            <p>If you need to reschedule or have any questions, please contact the hospital directly or log into your account.</p>
            <p>&copy; {{ date('Y') }} InstaDugo Blood Donation System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
