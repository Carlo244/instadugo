<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
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
                    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Welcome to
                        InstaDugo!</h2>
                    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello
                        <strong>{{ $user->name }}</strong>,</p>
                    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">Thank you for registering with
                        <strong>InstaDugo</strong>. To start saving lives and managing your donations, please verify
                        your email address by clicking the button below:</p>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="padding: 10px 0 35px 0;">
                                <a href="{{ $url }}" class="button"
                                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">Verify
                                    Email Address</a>
                            </td>
                        </tr>
                    </table>

                    <p style="font-size: 15px; color: #666666; line-height: 1.6; margin-bottom: 10px;">We're excited to
                        have you in our life-saving community.</p>

                    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br><span
                            style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span></p>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #f0f0f0;">
                    <p style="font-size: 12px; color: #999999; margin: 0; line-height: 1.4;">If you did not create an
                        account, no further action is required.<br>
                        <span style="color: #dc3545; word-break: break-all; font-size: 11px;">{{ $url }}</span>
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
