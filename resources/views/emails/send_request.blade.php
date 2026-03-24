@extends('emails.layout')

@section('title', 'Direct Blood Request')

@section('content')
    <h2 style="font-size: 22px; margin-bottom: 20px; color: #1a1a1a; text-align: center;">
        You’re a Compatible Donor 💉
    </h2>

    <p style="font-size: 16px; line-height: 1.6;">
        Hello <span style="background:#ffffff; color:#dc3545; padding:4px 10px; border-radius:12px; font-weight:bold;">
            Donor #{{ $donor->id }}
        </span>,
    </p>

    <p style="font-size: 16px; line-height: 1.6;">
        We wanted to let you know that your blood type matches a current request from someone in need.
        If you are feeling well and available, your donation could make a meaningful difference today.
    </p>

    <table border="0" cellpadding="15" cellspacing="0" width="100%"
        style="background-color: #f9fafb; border-radius: 8px; margin: 25px 0; border: 1px solid #edf2f7;">
        <tr>
            <td style="font-size: 15px; line-height: 1.8;">
                <div style="margin-bottom: 10px;">
                    <strong style="color:#718096;">Urgency:</strong>
                    <span style="color:#dc3545; font-weight:bold;">{{ $urgency }}</span>
                </div>

                <div style="margin-bottom: 10px;">
                    <strong style="color:#718096;">From:</strong><br>
                    <span style="color:#1a1a1a;">{{ $sender->name }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong style="color:#718096;">Location:</strong><br>
                    <span style="color:#1a1a1a;">{{ $hospital }}</span>
                </div>

                <div
                    style="background: #ffffff; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; color: #4a5568;">
                    <p style="margin: 0 0 10px 0; font-style: italic; font-size: 14px; color: #718096;">
                        A patient is currently in need of blood, and your blood type is a compatible match.
                        If you are free and able to donate, your kindness could help save a life.
                    </p>
                    <div style="border-top: 1px solid #f0f0f0; padding-top: 10px; color: #1a1a1a;">
                        <strong>Requester's Note:</strong><br>
                        "{{ $personalMessage }}"
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 15px 0;">
                <a href="{{ route('user.dashboard') }}" class="button"
                    style="background-color: #dc3545; color: #ffffff; padding: 16px 35px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;">
                    View Invitation on Dashboard
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 15px; color: #666666; margin-top: 35px; text-align: center;">
        Thank you for being part of the InstaDugo community.
        Your willingness to help can bring hope to those in need.
        <br><br>
        Warm regards,<br>
        <strong style="color:#dc3545;">The InstaDugo Team</strong>
    </p>
@endsection

@section('footer_note')
    <p style="font-size: 11px; color: #999999; margin: 0;">
        This is an automated notification from InstaDugo Blood Coordination System.
    </p>
@endsection
