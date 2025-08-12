<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\Student;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Mail\StudentQrMail;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class StudentController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::active()->get();
        $archivedStudents = Student::archived()->get();
        return view('admin.students.index', [
            "students" => $students,
            "archivedStudents" => $archivedStudents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::all();
        return view("admin.students.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            "student_id" => "required|string|min:7|max:9|unique:students,student_id",
            "lname"      => "required|string|max:155",
            "fname"      => "required|string|max:155",
            "MI"         => "required|string|max:155",
            "college"    => "required|string|max:155",
            "year"       => "required|integer|min:1|max:5",
            "email"      => "required|string|email|max:155",
        ]);

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

        // Check if the request is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            // Return success response for AJAX requests
            return response()->json([
                'success' => true,
                'message' => 'Student Added and QR Code Sent!'
            ]);
        }
        
        // Redirect to the index with a success message for non-AJAX requests
        return redirect()->route("admin.students.index")->with("success", "Student Added and QR Code Sent!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $students = Student::findOrFail($id);

        $data = "Student ID: {$students->student_id} | " .
            "Name: {$students->lname}, {$students->fname} | " .
            "College: {$students->college} | " .
            "Year: {$students->year}";

        // Generate the QR code image (no merge)
        $image = QrCode::format('png')->size(300)
            ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
            ->color(0, 0, 0)
            ->generate($data);

        // Save the image in storage
        $fileName = "qrcodes/student_{$students->student_id}.png";
        Storage::disk('public')->put($fileName, $image);

        // Also generate a base64 version to show on the page
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($image);

        return view('admin.students.show', compact('students', 'qrCodeBase64'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $students = Student::findOrFail($id);
        return view("admin.students.edit", ["students" => $students]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $validate = $request->validate([
            "student_id" => "required|string|min:7|max:9|unique:students,student_id," . $id,
            "lname"      => "required|string|max:155",
            "fname"      => "required|string|max:155",
            "MI"         => "required|string|max:155",
            "college"    => "required|string|max:155",
            "year"       => "required|integer|min:1|max:5",
            "email"      => "required|string|email|max:155",
        ]);
        $student->update($validate);

        // If QR code needs to be regenerated (e.g., student_id or name changed)
        $fileName = "qrcodes/student_{$student->student_id}.png";
        \Storage::disk('public')->put($fileName, \QrCode::format('png')
            ->size(300)
            ->generate($student->student_id . '|' . $student->lname . ' ' . $student->fname . '|' . $student->college . '|' . $student->year));
        $student->qr_code_path = $fileName;
        $student->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Student updated successfully.']);
        }
        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $students = Student::findOrFail($id)->delete();
        return redirect()->route("admin.students.index")->with("success", "Student Deleted.");
    }

    public function generateStudentQr($id)
    {
        // Find the student by ID
        $student = Student::findOrFail($id);

        // Generate the QR code with student info
        $data = "ID: {$student->student_id}\n" .
            "Name: {$student->lname}, {$student->fname}\n" .
            "College: {$student->college}\n" .
            "Year: {$student->year}";

        // Path to save the QR code image
        $fileName = "qrcodes/student_{$student->student_id}.png";
        \Storage::disk('public')->put($fileName, \QrCode::format('png')->size(300)->generate($data));

        // Generate a base64 version of the QR code for use in the view
        $qrCodeBase64 = base64_encode(\Storage::disk('public')->get($fileName));

        // Return a styled Blade view for printing
        return view('admin.students.print_qr', [
            'student' => $student,
            'qrCodeBase64' => $qrCodeBase64,
        ]);
    }

    /**
     * Resend QR code to student's email
     */
    public function resendQrCode($id)
    {
        // Find the student by ID
        $student = Student::findOrFail($id);

        // Generate QR code data
        $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

        // Path to QR code file
        $fileName = "qrcodes/student_{$student->student_id}.png";

        // Make sure directory exists
        $directory = 'qrcodes';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Generate QR code (or use existing one)
        if (!Storage::disk('public')->exists($fileName)) {
            // Create new QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->generate($data);

            // Save to storage
            Storage::disk('public')->put($fileName, $qrCode);

            // For email
            $qrCodeBase64 = base64_encode($qrCode);
        } else {
            // Use existing QR code
            $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));
        }

        // Send email
        Mail::to($student->email)->send(new StudentQrMail($student, $qrCodeBase64));

        // Redirect back with success message
        return redirect()->back()->with('success', "QR Code resent to {$student->fname} {$student->lname}'s email!");
    }

    /**
     * Archive the specified student.
     */
    public function archive(string $id)
    {
        $student = Student::findOrFail($id);
        $student->archive();
        return redirect()->route("admin.students.index")->with("success", "Student Archived Successfully.");
    }

    /**
     * Unarchive the specified student.
     */
    public function unarchive(string $id)
    {
        $student = Student::findOrFail($id);
        $student->unarchive();
        return redirect()->route("admin.students.index")->with("success", "Student Unarchived Successfully.");
    }

    /**
     * Display archived students.
     */
    public function archived()
    {
        $archivedStudents = Student::archived()->get();
        return view('admin.students.archived', ["students" => $archivedStudents]);
    }
}
