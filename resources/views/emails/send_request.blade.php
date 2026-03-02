<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Blood Request</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a { font-family: Arial, sans-serif !important; }
    </style>
    <![endif]-->
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

        .urgency-badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            color: #ffffff;
            font-size: 12px;
            text-transform: uppercase;
        }

        .Emergency {
            background-color: #dc3545;
        }

        .High {
            background-color: #fd7e14;
        }

        .Normal {
            background-color: #6c757d;
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
        <table border="0" cellpadding="0" cellspacing="0" width="600"
            style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">

            <!-- Header -->
            <tr>
                <td align="center" style="padding: 40px 0 20px 0;">
                    <h1
                        style="color: #dc3545; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: 1px;">
                        InstaDugo
                    </h1>
                </td>
            </tr>

            <!-- Body -->
            <tr>
                <td
                    style="padding: 20px 50px 40px 50px; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; color: #333333;">

                    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">
                        You’re a Compatible Donor 💉
                    </h2>

                    <p style="font-size: 16px; line-height: 1.6;">
                        Hello <strong>{{ $donor->name }}</strong>,
                    </p>

                    <p style="font-size: 16px; line-height: 1.6;">
                        We wanted to let you know that your blood type matches a current request from someone in need.
                        If you are feeling well and available, your donation could make a meaningful difference today.
                    </p>

                    <!-- Info Box -->
                    <table border="0" cellpadding="15" cellspacing="0" width="100%"
                        style="background-color: #f9fafb; border-radius: 8px; margin: 25px 0; border: 1px solid #edf2f7;">
                        <tr>
                            <td style="font-size: 15px; line-height: 1.8;">

                                <div style="margin-bottom: 10px;">
                                    <strong style="color:#718096;">Urgency:</strong>
                                    <span style="color:#dc3545; font-weight:bold;">{{ $urgency }}</span>
                                </div>

                                <div style="margin-bottom: 10px;">
                                    <strong style="color:#718096;">From:</strong><br>
                                    <span style="color:#1a1a1a;">{{ $sender->name }}</span>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <strong style="color:#718096;">Location:</strong><br>
                                    <span style="color:#1a1a1a;">{{ $hospital }}</span>
                                </div>

                                <div
                                    style="background: #ffffff; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; color: #4a5568;">
                                    <p style="margin: 0 0 10px 0; font-style: italic; font-size: 14px; color: #718096;">
                                        A patient is currently in need of blood, and your blood type is a compatible
                                        match.
                                        If you are free and able to donate, your kindness could help save a life.
                                    </p>
                                    <div style="border-top: 1px solid #f0f0f0; padding-top: 10px; color: #1a1a1a;">
                                        <strong>Requester's Note:</strong><br>
                                        "{{ $personalMessage }}"
                                    </div>
                                </div>

                            </td>
                        </tr>
                    </table>

                    <!-- Button -->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="padding: 15px 0;">
                                <a href="{{ url('user.dashboard') }}"
                                    style="background-color: #dc3545; color: #ffffff; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">
                                    View Invitation on Dashboard
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #666666; margin-top: 35px; text-align: center;">
                        Thank you for being part of the InstaDugo community.
                        Your willingness to help can bring hope to those in need.
                        <br><br>
                        Warm regards,<br>
                        <strong style="color:#dc3545;">The InstaDugo Team</strong>
                    </p>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td align="center" style="padding: 25px; background-color: #fcfcfc; border-top: 1px solid #f0f0f0;">
                    <p style="font-size: 11px; color: #999999; margin: 0;">
                        This is an automated notification from InstaDugo Blood Coordination System.
                    </p>
                </td>
            </tr>

        </table>
    </center>
</body>

</html>
