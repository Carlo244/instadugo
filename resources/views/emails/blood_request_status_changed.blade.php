@extends('emails.layout')

@section('title', 'Blood Request Status Update')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Blood Request Status Update</h2>

    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello <strong>{{ $notifiable->name }}</strong>,</p>

    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
        {{ $message }}
    </p>

    <table border="0" cellpadding="15" cellspacing="0" width="100%"
        style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 30px; border: 1px solid #edf2f7;">
        <tr>
            <td style="font-size: 15px; width: 50%; border-right: 1px solid #edf2f7;">
                <span style="color: #718096; font-weight: 600; text-transform: uppercase; font-size: 12px;">Blood
                    Type</span><br>
                <span style="color: #dc3545; font-weight: bold; font-size: 18px;">{{ $bloodRequest->blood_type }}</span>
            </td>
            <td style="font-size: 15px; width: 50%;">
                <span
                    style="color: #718096; font-weight: 600; text-transform: uppercase; font-size: 12px;">Urgency</span><br>
                <span style="color: #dc3545; font-weight: bold; font-size: 18px;">{{ $bloodRequest->urgency }}</span>
            </td>
        </tr>
    </table>

    @if ($bloodRequest->hospitalAdmin)
        <table border="0" cellpadding="15" cellspacing="0" width="100%"
            style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 30px; border: 1px solid #edf2f7;">
            <tr>
                <td style="font-size: 15px;">
                    <span
                        style="color: #718096; font-weight: 600; text-transform: uppercase; font-size: 12px;">Hospital</span><br>
                    <span
                        style="color: #1a1a1a; font-weight: bold; font-size: 16px;">{{ $bloodRequest->hospitalAdmin->hospital_name }}</span>
                </td>
            </tr>
        </table>
    @endif

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 10px 0;">
                <a href="{{ route('user.blood-requests') }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">View
                    My Requests</a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; margin-top: 40px; text-align: center;">Regards,<br><span
            style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span></p>
@endsection

@section('footer_note')
    <p style="font-size: 11px; color: #999999; margin: 0; line-height: 1.5;">This is an automated message from the Blood
        Management System.<br>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
@endsection
