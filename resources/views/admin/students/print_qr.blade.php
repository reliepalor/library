<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Student QR Code</title>
    <style>
        body {
            background: #fff;
            color: #222;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .print-container {
            max-width: 400px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 2rem 2rem 1.5rem 2rem;
            text-align: center;
        }
        .qr-img {
            width: 220px;
            height: 220px;
            margin: 0 auto 1.5rem auto;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            object-fit: contain;
        }
        .student-info {
            margin-bottom: 1.5rem;
        }
        .student-info h2 {
            font-size: 1.3rem;
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }
        .student-info p {
            margin: 0.2rem 0;
            font-size: 1rem;
        }
        .print-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.6rem 1.5rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .print-btn:hover {
            background: #1d4ed8;
        }
        @media print {
            .print-btn { display: none; }
            .print-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="student-info">
            <h2>{{ $student->lname }}, {{ $student->fname }} @if($student->MI){{ $student->MI }}.@endif</h2>
            <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
            <p><strong>College:</strong> {{ $student->college }}</p>
            <p><strong>Year:</strong> {{ $student->year }}</p>
        </div>
        <img class="qr-img" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Student QR Code" />
        <button class="print-btn" onclick="window.print()">Print</button>
    </div>
</body>
</html> 