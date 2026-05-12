@extends('emails.layout')

@section('title', $reminderLabel . ' - Donation Appointment Reminder')

@section('content')
    @php
        $hospital = $donation->hospitalAdmin;
        $user = $donation->user;
        $date = \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y');
        $time = \Carbon\Carbon::createFromFormat('H:i:s', $donation->donation_time)->format('h:i A');
        $isFinalReminder = str_contains(strtolower($reminderLabel), '2-hour');
        $accentColor = $isFinalReminder ? '#b91c1c' : '#dc3545';
        $bannerText = $isFinalReminder
            ? 'Final reminder before your appointment'
            : 'Your upcoming appointment reminder';
        $introText = $isFinalReminder
            ? 'This is your final reminder that your blood donation appointment is coming up soon.'
            : 'This is a reminder for your upcoming blood donation appointment.';
    @endphp

    <h2 style="margin: 0 0 14px 0; color: {{ $accentColor }}; font-size: 24px; font-weight: 800; text-align: center;">
        {{ $bannerText }}</h2>

    <p style="margin: 0 0 18px 0; font-size: 16px; line-height: 1.7;">Hi {{ $user->name }},</p>

    <div
        style="background-color: #fff5f5; border-left: 4px solid {{ $accentColor }}; padding: 18px 20px; border-radius: 8px; margin-bottom: 22px;">
        <p style="margin: 0; font-size: 15px; line-height: 1.7;">{{ $introText }}</p>
    </div>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 22px;">
        <tr>
            <td style="background-color: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 18px;">
                <p
                    style="margin: 0 0 10px 0; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">
                    Appointment Details</p>
                <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #111827;">
                    <strong>Hospital:</strong> {{ $hospital?->hospital_name ?? 'the selected hospital' }}<br>
                    <strong>Date:</strong> {{ $date }}<br>
                    <strong>Time:</strong> {{ $time }}<br>
                    <strong>Blood Type:</strong> {{ $donation->blood_type }}
                </p>
            </td>
        </tr>
    </table>

    <div
        style="background-color: #f8fafc; border: 1px solid #e5e7eb; padding: 18px 20px; border-radius: 8px; margin-bottom: 22px;">
        <p
            style="margin: 0 0 10px 0; font-size: 14px; color: #374151; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">
            Please remember</p>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #374151; line-height: 1.8;">
            <li>Arrive 10-15 minutes early</li>
            <li>Bring a valid ID</li>
            <li>Stay hydrated and eat a light meal before the appointment</li>
            <li>If you need to make changes, open your donation tab below</li>
        </ul>
    </div>

    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 1.7;">If you need to review or update your appointment, open
        your donation tab below.</p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
        <tr>
            <td align="center">
                <a href="{{ route('user.donate-schedule') }}" class="button"
                    style="display: inline-block; background-color: {{ $accentColor }}; color: #ffffff !important; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 15px;">Cancel
                    / view my donation tab</a>
            </td>
        </tr>
    </table>

    <p style="margin: 0; font-size: 14px; line-height: 1.7; color: #6b7280; text-align: center;">Thank you for donating.
        Your contribution saves lives.</p>
@endsection
