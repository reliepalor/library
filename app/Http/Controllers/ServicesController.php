<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TeacherVisitor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Mail\StudentQrMail;
use App\Mail\TeacherVisitorQrMail;
use Illuminate\Support\Facades\Mail;

class ServicesController extends Controller
{
    /**
     * Display the services page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('services.index');
    }
    
    /**
     * Register a student or teacher/visitor and generate QR code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerQr(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'student') {
            // Validate student data
            $validate = $request->validate([
                "student_id" => "required|string|min:7|max:9|unique:students,student_id",
                "lname"      => "required|string|max:155",
                "fname"      => "required|string|max:155",
                "MI"         => "required|string|max:155",
                "college"    => "required|string|max:155",
                "year"       => "required|integer|min:1|max:5",
                "email"      => "required|string|email|max:155",
            ]);

            try {
                // Create the student record
                $student = Student::create($validate);

                // Generate QR code data
                $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

                // Path to public storage where the QR code will be saved
                $fileName = "qrcodes/student_{$student->student_id}.png";

                // Generate and save the QR code with a white background and black foreground
                Storage::disk('public')->put($fileName, QrCode::format('png')
                    ->size(300)  // Set size of the QR code
                    ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
                    ->color(0, 0, 0)  // Set the color of the QR code itself to black (RGB: 0, 0, 0)
                    ->generate($data));

                // Save QR code path to student record
                $student->qr_code_path = $fileName;
                $student->save();

                // Get the file path and base64 encode it for email
                $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));

                // Send an email with the QR code as an inline image or attachment
                Mail::to($student->email)->send(new StudentQrMail($student, $qrCodeBase64));

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Student registration successful! Your QR code has been sent to your email.'
                ]);
            } catch (\Exception $e) {
                // Return error response
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during student registration. Please try again.'
                ], 500);
            }
        } elseif ($type === 'teacher_visitor') {
            // Validate teacher/visitor data
            $validate = $request->validate([
                "lname"      => "required|string|max:155",
                "fname"      => "required|string|max:155",
                "MI"         => "nullable|string|max:155",
                "email"      => "required|string|email|max:155|unique:teachers_visitors,email",
                "department" => "required|string|max:155",
                "role"       => "required|in:Teacher,Visitor",
            ]);

            try {
                // Create the teacher/visitor record
                $teacherVisitor = TeacherVisitor::create($validate);

                // Generate QR code data
                $data = "{$teacherVisitor->id} | {$teacherVisitor->lname} {$teacherVisitor->fname} | {$teacherVisitor->department} | {$teacherVisitor->role}";

                // Path to public storage where the QR code will be saved
                $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";

                // Generate and save the QR code with a white background and black foreground
                Storage::disk('public')->put($fileName, QrCode::format('png')
                    ->size(300)  // Set size of the QR code
                    ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
                    ->color(0, 0, 0)  // Set the color of the QR code itself to black (RGB: 0, 0, 0)
                    ->generate($data));

                // Save QR code path to teacher/visitor record
                $teacherVisitor->qr_code_path = $fileName;
                $teacherVisitor->save();

                // Get the file path and base64 encode it for email
                $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));

                // Send an email with the QR code as an inline image or attachment
                Mail::to($teacherVisitor->email)->send(new TeacherVisitorQrMail($teacherVisitor, $qrCodeBase64));

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Teacher/Visitor registration successful! Your QR code has been sent to your email.'
                ]);
            } catch (\Exception $e) {
                // Return error response
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during teacher/visitor registration. Please try again.'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration type.'
            ], 400);
        }
    }
}