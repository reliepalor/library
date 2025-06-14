@component('mail::message')
# Thank You for Using CSU Library, {{ $student->fname }}!

<div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <p style="margin: 0 0 10px 0;">You have successfully logged out of the CSU Digital Library at <strong>{{ $logoutTime }}</strong>.</p>
    <p style="margin: 0 0 10px 0;">Your activity: <strong>{{ $activity }}</strong></p>
</div>

<div style="margin: 20px 0;">
    <p style="margin: 0 0 10px 0;">We hope you had a productive time in our library. Please come again!</p>
</div>

@component('mail::panel')
<strong>Session Summary:</strong>
<ul style="margin: 10px 0; padding-left: 20px;">
    <li>Student ID: {{ $student->student_id }}</li>
    <li>College: {{ $student->college }}</li>
    <li>Year Level: {{ $student->year }}</li>
    <li>Duration: {{ $duration }}</li>
</ul>
@endcomponent

Thanks,<br>
<strong>CSU Digital Library Team</strong>
@endcomponent 