@extends('emails.layout')

@section('title', 'Reset Your Password')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Password Reset Request</h2>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">We received a request to reset your password
        for your InstaDugo account. No problem! Simply click the button below to choose a new one:</p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 35px 0;">
                <a href="{{ $url }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">Reset
                    Password</a>
            </td>
        </tr>
    </table>

    <div style="background-color: #fff5f5; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 30px;">
        <p style="font-size: 14px; color: #b91c1c; margin: 0;"><strong>Note:</strong> This link is valid for
            <strong>60 minutes</strong>. For your security, if you did not make this request, please disregard this
            email.
        </p>
    </div>

    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br><span
            style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span></p>
@endsection

@section('footer_note')
    <p style="font-size: 12px; color: #999999; margin: 0; line-height: 1.4;">If the button doesn't work, copy and
        paste this link into your browser:<br>
        <span style="color: #dc3545; word-break: break-all; font-size: 11px;">{{ $url }}</span>
    </p>
@endsection
