@extends('emails.layout')

@section('title', 'Verify Your Email')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Verify your email address</h2>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">Please verify your email address to activate your
        InstaDugo account.</p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 35px 0;">
                <a href="{{ $url }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">Verify
                    Email Address</a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; line-height: 1.6; margin-bottom: 10px;">If you did not create this
        account, you can ignore this email.</p>

    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br><span
            style="color: #dc3545; font-weight: bold;">InstaDugo</span></p>
@endsection

@section('footer_note')
    <p style="font-size: 12px; color: #999999; margin: 0; line-height: 1.4;">Verification link:<br>
        <span style="color: #dc3545; word-break: break-all; font-size: 11px;">{{ $url }}</span>
    </p>
@endsection
