<x-mail::message>
# Test Email

This is a test email to verify SMTP configuration.

<x-mail::button :url="url('/')" color="success">
Go to Home
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
