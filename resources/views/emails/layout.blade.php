<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'InstaDugo Notification')</title>
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
                    @yield('content')
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 30px; background-color: #fcfcfc; border-top: 1px solid #f0f0f0;">
                    @hasSection('footer_note')
                        @yield('footer_note')
                    @else
                        <p style="font-size: 12px; color: #999999; margin: 0;">&copy; {{ date('Y') }} InstaDugo.
                            Helping
                            save lives.</p>
                    @endif
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
