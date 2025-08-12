<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Mail\StudentQrMail;
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
     * Register a student and generate QR code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerQr(Request $request)
    {
        // Validate the request data
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
                'message' => 'Registration successful! Your QR code has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration. Please try again.'
            ], 500);
        }
    }
}