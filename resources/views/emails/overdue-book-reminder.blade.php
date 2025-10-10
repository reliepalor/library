@component('mail::message')
# Overdue Book Reminder

Dear {{ $student->fname }} {{ $student->lname }},

This is a reminder that you have overdue book(s) that need to be returned to the library.

**Overdue Book(s):**
@foreach($overdueBooks as $borrow)
- Title: {{ optional($borrow->book)->name }}  
  Code: {{ $borrow->book_id }}  
  Borrowed: {{ optional($borrow->created_at)->format('M d, Y h:i A') }}
@endforeach

Please return the book(s) as soon as possible to avoid any penalties. If you have already returned them, kindly disregard this message.

@component('mail::button', ['url' => url('/')])
Go to Library Portal
@endcomponent

Thank you for your cooperation.

Best regards,<br>
{{ config('app.name') }}
@endcomponent