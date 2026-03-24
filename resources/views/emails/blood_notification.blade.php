@extends('emails.layout')

@section('title', 'Urgent Need for Help')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Urgent Need for Help</h2>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello <strong>{{ $donorName }}</strong>,</p>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">A patient at
        <strong>{{ $hospital }}</strong> is in urgent need of blood. Based on our records, your blood type is a
        <strong>compatible match!</strong>
    </p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 35px;">
        <tr>
            <td align="center">
                <div
                    style="background-color: #fff5f5; border: 2px dashed #dc3545; padding: 20px; border-radius: 12px; display: inline-block; min-width: 200px;">
                    <span
                        style="font-size: 12px; color: #dc3545; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; display: block; margin-bottom: 5px;">Required
                        Type</span>
                    <span
                        style="font-size: 36px; font-weight: 800; color: #dc3545; display: block;">{{ $request->blood_type }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="15" cellspacing="0" width="100%"
        style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 30px; border: 1px solid #edf2f7;">
        <tr>
            <td style="font-size: 15px;">
                <span style="color: #718096; font-weight: 600; text-transform: uppercase; font-size: 12px;">Urgency
                    Level:</span><br>
                <span style="color: #e53e3e; font-weight: bold; font-size: 18px;">{{ $request->urgency }}</span>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; line-height: 1.6; color: #4a5568; margin-bottom: 30px; text-align: center;">
        Your contribution can save a life today. If you are feeling healthy and able to donate, please let us know as
        soon as possible.
    </p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 10px 0;">
                <a href="{{ route('user.dashboard') }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">View
                    Request Details</a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; margin-top: 40px; text-align: center;">Regards,<br><span
            style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span></p>
@endsection

@section('footer_note')
    <p style="font-size: 11px; color: #999999; margin: 0; line-height: 1.5;">This is an automated message from the
        Blood Management System.<br>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
@endsection
