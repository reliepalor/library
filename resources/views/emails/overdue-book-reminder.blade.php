@component('mail::message')
# Overdue Book Reminder

Dear {{ $borrowedBook->student->fname }} {{ $borrowedBook->student->lname }},

This is a reminder that you have an overdue book that needs to be returned to the library.

**Book Details:**
- Book Title: {{ $borrowedBook->book->name }}
- Book Code: {{ $borrowedBook->book_id }}
- Borrowed Date: {{ $borrowedBook->created_at->format('M d, Y') }}


Please return the book as soon as possible to avoid any penalties.

@component('mail::button', ['url' => route('admin.attendance.index')])
Return to Library
@endcomponent

Thank you for your cooperation.

Best regards,<br>
{{ config('app.name') }}
@endcomponent 