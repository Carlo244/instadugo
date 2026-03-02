<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A Donor Has Been Found!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f4f7f9;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
        }

        @media screen and (max-width: 600px) {
            .content-table {
                width: 100% !important;
            }

            .mobile-padding {
                padding: 20px !important;
            }

            .button {
                display: block !important;
                width: auto !important;
                text-align: center;
            }
        }
    </style>
</head>

<body style="background-color: #f4f7f9; padding: 20px 0;">
    <center>
        <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="600"
            style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">
            <tr>
                <td align="center" style="padding: 40px 0 20px 0; background-color: #ffffff;">
                    <h1
                        style="color: #dc3545; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; margin: 0; font-size: 26px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                        InstaDugo</h1>
                </td>
            </tr>
            <tr>
                <td class="mobile-padding"
                    style="padding: 20px 50px 40px 50px; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; color: #333333;">
                    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Donor Found!
                    </h2>

                    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello,</p>

                    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
                        Great news! <strong>{{ $donorName }}</strong> has accepted your blood request. A donation has
                        been scheduled based on the details below:
                    </p>

                    <table border="0" cellpadding="15" cellspacing="0" width="100%"
                        style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 25px; border: 1px solid #edf2f7;">
                        <tr>
                            <td
                                style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.8; color: #4a5568;">
                                <strong
                                    style="color: #1a1a1a; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 11px;">
                                    Appointment Details:
                                </strong>
                                • Scheduled Date:
                                <strong>{{ \Carbon\Carbon::parse($donationDate)->format('M d, Y') }}</strong><br>
                                • Scheduled Time: <strong>{{ $donationTime }}</strong><br>
                                • Location: <strong>{{ $hospital }}</strong>
                            </td>
                        </tr>
                    </table>

                    <p style="font-size: 15px; line-height: 1.6; color: #4a5568; margin-bottom: 30px;">
                        Please ensure you are at the hospital at the scheduled time. Your presence is vital to
                        completing this life-saving process.
                    </p>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="padding: 10px 0 35px 0;">
                                <a href="{{ url('/user/dashboard') }}" class="button"
                                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">
                                    View Request Status
                                </a>
                            </td>
                        </tr>
                    </table>

                    <div style="background-color: #fff5f5; border-left: 4px solid #dc3545; padding: 15px;">
                        <p style="font-size: 13px; color: #b91c1c; margin: 0;">
                            <strong>Note:</strong> Please bring a valid ID and your request reference number for
                            verification at the hospital.
                        </p>
                    </div>

                    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br>
                        <span style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #f0f0f0;">
                    <p style="font-size: 12px; color: #999999; margin: 0;">&copy; {{ date('Y') }} InstaDugo. Helping
                        save lives through technology.</p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
