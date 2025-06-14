@component('mail::message')
# Welcome to CSU Library, {{ $student->fname }}!

<div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <p style="margin: 0 0 10px 0;">You have successfully logged in to the CSU Digital Library at <strong>{{ $loginTime }}</strong>.</p>
    <p style="margin: 0 0 10px 0;">Your chosen activity: <strong>{{ $activity }}</strong></p>
</div>

<div style="margin: 20px 0;">
    <p style="margin: 0 0 10px 0;">Feel free to use our services and resources. If you need any assistance, please don't hesitate to ask our library staff.</p>
</div>

@component('mail::panel')
<strong>Quick Information:</strong>
<ul style="margin: 10px 0; padding-left: 20px;">
    <li>Student ID: {{ $student->student_id }}</li>
    <li>College: {{ $student->college }}</li>
    <li>Year Level: {{ $student->year }}</li>
</ul>
@endcomponent

Thanks,<br>
<strong>CSU Digital Library Team</strong>
@endcomponent 