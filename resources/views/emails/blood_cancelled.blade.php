@extends('emails.layout')

@section('title', 'Blood Request Cancelled')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Request Cancelled</h2>

    <p style="font-size: 16px; line-height: 1.6;">Hello <strong>{{ $user->name }}</strong>,</p>

    <p style="font-size: 16px; line-height: 1.6;">Your blood request has been <strong>cancelled by the hospital</strong>. This request is no longer active in our system.</p>

    <div style="background-color: #fff5f5; border-left: 4px solid #dc3545; padding: 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
        <p style="font-size: 14px; color: #dc3545; text-transform: uppercase; font-weight: 800; margin: 0 0 10px 0; letter-spacing: 0.5px;">
            Request Status:
        </p>
        <p style="font-size: 16px; color: #1a1a1a; margin: 0; font-weight: 600;">
            Cancelled
        </p>
        <p style="font-size: 14px; color: #4a5568; margin: 10px 0 0 0; line-height: 1.5;">
            You may contact the hospital directly for more information or submit a new request if needed in the future.
        </p>
    </div>

    <table border="0" cellpadding="15" cellspacing="0" width="100%"
        style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 25px; border: 1px solid #edf2f7;">
        <tr>
            <td style="font-size: 14px; line-height: 1.8; color: #4a5568;">
                <strong style="color: #1a1a1a; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 11px;">Request Details:</strong>
                • Blood Type: <strong>{{ $request->blood_type }}</strong><br>
                • Quantity: <strong>{{ $request->quantity }} units</strong><br>
                • Urgency: <strong>{{ $request->urgency }}</strong>
            </td>
        </tr>
        <tr>
            <td style="font-size: 14px; line-height: 1.8; color: #4a5568;">
                <strong style="color: #1a1a1a; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 11px;">Hospital Location:</strong>
                <strong style="color: #1a1a1a;">{{ $request->hospitalAdmin?->hospital_name ?? 'Partner Hospital' }}</strong><br>
                <span style="color: #4a5568;">{{ $request->hospitalAdmin?->address ?? 'Address not available' }}</span>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 35px 0;">
                <a href="{{ route('user.blood-requests') }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">
                    View All Requests
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br>
        <span style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span>
    </p>
@endsection
