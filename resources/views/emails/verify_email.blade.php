<!DOCTYPE html>
<html>

<head>
    <style>
        .button {
            background-color: #dc3545;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .container {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Hello, {{ $user->name }}!</h2>
        <p>Thank you for registering with <strong>InstaDugo</strong>. To start saving lives and managing your donations,
            please verify your email address.</p>

        <div style="margin: 30px 0;">
            <a href="{{ $url }}" class="button">Verify Email Address</a>
        </div>

        <p>If the button above doesn't work, copy and paste this link into your browser:</p>
        <p style="font-size: 12px; color: #777;">{{ $url }}</p>

        <hr style="border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #999;">If you did not create an account, no further action is required.</p>
    </div>
</body>

</html>
