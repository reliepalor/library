<!-- resources/views/emails/student_qr.blade.php -->

<p>Hello {{ $student->fname }} {{ $student->lname }},</p>

<p>Thank you for registering with our system. Below is your unique student QR code:</p>

<p>Student ID: {{ $student->student_id }}</p>
<p>Full Name: {{ $student->fname }} {{ $student->lname }}</p>
<p>College: {{ $student->college }}</p>
<p>Year: {{ $student->year }}</p>

<p>Your QR Code:</p>
<img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Student QR Code" />

<p>If you have any questions, feel free to contact us.</p>

<p>Best regards,</p>
<p>Digital Library Manager</p>
