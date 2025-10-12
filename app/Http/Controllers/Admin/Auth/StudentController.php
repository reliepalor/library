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
        try {
            // Normalize/trim inputs before validation
            $request->merge([
                'student_id' => trim((string) $request->input('student_id')),
                'lname'      => trim((string) $request->input('lname')),
                'fname'      => trim((string) $request->input('fname')),
                'MI'         => trim((string) $request->input('MI')),
                'college'    => trim((string) $request->input('college')),
                'year'       => (int) $request->input('year'),
                'email'      => strtolower(trim((string) $request->input('email'))),
            ]);
            $validate = $request->validate([
                "student_id" => "required|string|min:5|max:20",
                "lname"      => "required|string|max:155",
                "fname"      => "required|string|max:155",
                "MI"         => "nullable|string|max:155",
                "college"    => "required|string|max:155",
                // Limit year level to 1-4 as requested
                "year"       => "required|integer|min:1|max:4",
                "email"      => "required|string|email|max:155",
            ]);

            // Create or update the student record by student_id (idempotent)
            $student = Student::updateOrCreate(
                ['student_id' => $validate['student_id']],
                $validate
            );

            // Generate QR code data
            $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

            // Ensure directory exists before saving QR
            $directory = 'qrcodes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Path to public storage where the QR code will be saved
            $fileName = "qrcodes/student_{$student->student_id}.png";

            // Generate and save the QR code with a white background and black foreground
            Storage::disk('public')->put($fileName, QrCode::format('png')
                ->size(300)  // Set size of the QR code
                ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
                ->color(0, 0, 0)  // Set the color of the QR code itself to black (RGB: 0, 0, 0)
                ->generate($data));

            // Save QR code path to student record
            if ($student->qr_code_path !== $fileName) {
                $student->qr_code_path = $fileName;
                $student->save();
            }

            // Attempt to send email with QR code; do not fail the whole flow if mail fails
            try {
                $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));
                Mail::to($student->email)->send(new StudentQrMail($student, $qrCodeBase64));
                $successMessage = 'Student saved and QR Code sent!';
            } catch (\Throwable $mailException) {
                // Log the mail error for debugging
                \Log::error('Failed to send student QR email', [
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'error' => $mailException->getMessage(),
                ]);
                $successMessage = 'Student saved, but sending the QR code email failed.';
            }

            // Check if the request is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                // Return success response for AJAX requests
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                ]);
            }

            // Redirect to the index with a success message for non-AJAX requests
            return redirect()->route("admin.students.index")->with("success", $successMessage);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                $errors = $e->errors();
                $firstError = null;
                foreach ($errors as $field => $messages) {
                    if (!empty($messages)) { $firstError = $messages[0]; break; }
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'first_error' => $firstError,
                    'errors' => $errors,
                ], 422);
            }

            // Re-throw the exception for non-AJAX requests
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Student store failed', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);
            // Catch-all for other errors to avoid generic HTML 500 in AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred while adding the student.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            throw $e;
        }
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
            "student_id" => "required|string|min:5|max:20|unique:students,student_id," . $id,
            "lname"      => "required|string|max:155",
            "fname"      => "required|string|max:155",
            "MI"         => "nullable|string|max:155",
            "college"    => "required|string|max:155",
            "year"       => "required|integer|min:1|max:4",
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

        // Redirect to index to avoid GET on POST-only route after PRG
        return redirect()->route('admin.students.index')->with('success', "QR Code resent to {$student->fname} {$student->lname}'s email!");
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
