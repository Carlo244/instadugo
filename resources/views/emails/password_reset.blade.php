<!DOCTYPE html>
<html>

<head>
    <style>
        .button {
            background-color: #dc3545;
            /* InstaDugo Red */
            color: #ffffff !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .container {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 30px;
            color: #444;
            max-width: 600px;
            margin: auto;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #dc3545;">Reset Your Password</h2>
        <p>Hello, {{ $user->name }}!</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>

        <div style="margin: 35px 0; text-align: center;">
            <a href="{{ $url }}" class="button">Reset Password</a>
        </div>

        <p>This password reset link will expire in **60 minutes**.</p>
        <p>If you did not request a password reset, no further action is required.</p>

        <hr style="border: none; border-top: 1px solid #eee; margin-top: 30px;">
        <p style="font-size: 12px; color: #999;">
            Regards,<br>
            <strong>The InstaDugo Team</strong>
        </p>
    </div>
</body>

</html>
