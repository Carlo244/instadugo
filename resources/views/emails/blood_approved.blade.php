<!DOCTYPE html>
<html>

<head>
    <style>
        .email-card {
            font-family: sans-serif;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 10px;
        }

        .header {
            background: #e63946;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            color: #333;
        }

        .button {
            background: #e63946;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="email-card">
        <div class="header">
            <h1>InstaDugo</h1>
        </div>
        <div class="content">
            <h2>Hello, {{ $user->name }}!</h2>
            <p>Your blood request for <strong>Type {{ $request->blood_type }}</strong> has been approved by the
                hospital.</p>
            <p><strong>Quantity:</strong> {{ $request->quantity }} units</p>
            <p>Please proceed to the hospital with your valid ID.</p>
            <br>
            <a href="{{ url('/user/dashboard') }}" class="button">View Dashboard</a>
        </div>
    </div>
</body>

</html>
