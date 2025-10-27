<x-mail::message>
# Confirm Your Logout

Hello {{ $student->fname }},

Your logout verification code is **{{ $code }}**.

Please enter this code on the attendance page to confirm your logout.

**Important Notes:**
- This code expires in 2 minutes
- If you didn't request this logout, please ignore this email
- For security reasons, codes are valid for one use only

<x-mail::button :url="url('/admin/attendance')" color="success">
Go to Attendance Page
</x-mail::button>

If the button doesn't work, copy and paste this link into your browser:
{{ url('/admin/attendance') }}

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
