# Mail Setup

Use this when deploying `Instadugo` to Laravel Cloud or any production host.

## Recommended

Use a transactional mail provider such as Mailgun or SendGrid for production delivery.

Set the provider's credentials in the Cloud environment, then redeploy:

```dotenv
MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS=instadugo@your-domain.com
MAIL_FROM_NAME="InstaDugo"
```

## SMTP fallback

If you are using SMTP instead, make sure the Cloud env includes all of these values:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=instadugo@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=instadugo@gmail.com
MAIL_FROM_NAME="InstaDugo"
```

## After updating Cloud env

Run these on the deployed app so cached config picks up the new values:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

If notifications are queued, keep a worker running:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```