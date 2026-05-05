@extends('emails.layout')

@section('title', 'Donation Appointment Reminder')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">🩸 Donation Appointment Reminder</h2>

    <p style="font-size: 16px; line-height: 1.6;">Hi {{ $donorName }},</p>

    <p style="font-size: 16px; line-height: 1.6;">This is a friendly reminder that you have a blood donation appointment scheduled <strong>tomorrow</strong>!</p>

    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
        <p style="font-size: 14px; color: #856404; text-transform: uppercase; font-weight: 800; margin: 0 0 10px 0; letter-spacing: 0.5px;">Your Appointment Details</p>
        <table border="0" cellpadding="10" cellspacing="0" width="100%">
            <tr>
                <td style="font-size: 14px; line-height: 1.8; color: #333;">
                    <strong style="color: #dc3545;">Hospital:</strong> {{ $hospital }}<br>
                    <strong style="color: #dc3545;">Date:</strong> {{ \Carbon\Carbon::parse($donationDate)->format('l, F j, Y') }}<br>
                    <strong style="color: #dc3545;">Time:</strong> {{ \Carbon\Carbon::parse($donationTime)->format('h:i A') }}<br>
                    <strong style="color: #dc3545;">Blood Type:</strong> {{ $donation->blood_type }}
                </td>
            </tr>
        </table>
    </div>

    <div style="background-color: #ffe0e0; border-left: 4px solid #dc3545; padding: 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
        <p style="font-size: 14px; color: #dc3545; text-transform: uppercase; font-weight: 800; margin: 0 0 10px 0; letter-spacing: 0.5px;">Please Remember:</p>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #333; line-height: 1.8;">
            <li>Arrive 10-15 minutes early</li>
            <li>Bring a valid ID and proof of address</li>
            <li>Stay hydrated before your appointment</li>
            <li>Eat a light meal beforehand</li>
            <li>Get adequate rest the night before</li>
        </ul>
    </div>

    <p style="font-size: 16px; line-height: 1.6; margin: 25px 0;">Your donation will help save lives. Thank you for your generous contribution to our community!</p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 35px 0;">
                <a href="{{ route('user.donate-schedule') }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">
                    View My Donation Schedule
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br>
        <span style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span>
    </p>
@endsection
