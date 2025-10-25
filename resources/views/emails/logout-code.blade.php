<x-mail::message>
# Confirm Your Logout

Hello {{ $student->fname }},

Your logout verification code is **{{ $code }}**.

Please enter this code on the attendance page to confirm your logout.

This code expires in 5 minutes.

<x-mail::button :url="url('/admin/attendance')" color="success">
Go to Attendance Page
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
