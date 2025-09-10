@component('mail::message')
# Borrow Request Rejected

Dear {{ $borrowedBook->student->fname }} {{ $borrowedBook->student->lname }},

We regret to inform you that your borrow request for the following book has been rejected.

**Book Details:**
- Book Title: {{ $borrowedBook->book->name }}
- Book Code: {{ $borrowedBook->book_id }}
- Requested Date: {{ $borrowedBook->created_at->format('M d, Y') }}

**Reason for Rejection:**
{{ $rejectionReason }}

If you have any questions or would like to request a different book, please contact the library administration.


Thank you for your understanding.

Best regards,<br>
@endcomponent
