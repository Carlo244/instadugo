@extends('emails.layout')

@section('title', 'Verify Your Email')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">Welcome to InstaDugo!</h2>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 25px;">Hello <strong>{{ $user->name }}</strong>,</p>
    <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">Thank you for registering with
        <strong>InstaDugo</strong>. To start saving lives and managing your donations, please verify your email
        address by clicking the button below:
    </p>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 35px 0;">
                <a href="{{ $url }}" class="button"
                    style="background-color: #dc3545; color: #ffffff !important; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">Verify
                    Email Address</a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; line-height: 1.6; margin-bottom: 10px;">We're excited to have you in
        our life-saving community.</p>

    <p style="font-size: 15px; color: #666666; margin-top: 40px;">Regards,<br><span
            style="color: #dc3545; font-weight: bold;">The InstaDugo Team</span></p>
@endsection

@section('footer_note')
    <p style="font-size: 12px; color: #999999; margin: 0; line-height: 1.4;">If you did not create an account, no
        further action is required.<br>
        <span style="color: #dc3545; word-break: break-all; font-size: 11px;">{{ $url }}</span>
    </p>
@endsection
