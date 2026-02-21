<x-mail::message>

    # Hello from InstaDugo! 🩸

    Welcome to **InstaDugo Blood Donation System**.

    Before you can proceed to your dashboard, please verify your email address by clicking the button below. This helps
    us make sure your account is secure and that you will receive important notifications about your blood donation
    appointments and updates.

    <x-mail::button :url="$actionUrl" color="primary">
        Verify My Email
    </x-mail::button>

    If you did not create an account with InstaDugo, you may safely ignore this email.

    Thank you for helping save lives through blood donation 💖

    Regards,
    **InstaDugo Team**
    {{ config('app.name') }}

    <x-slot:subcopy>
        If you’re having trouble clicking the "Verify My Email" button, copy and paste the URL below into your web
        browser:

        {{ $displayableActionUrl }}
    </x-slot:subcopy>

</x-mail::message>
