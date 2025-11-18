<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\Student;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Mail\StudentQrMail;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;

class StudentController extends \App\Http\Controllers\Controller
{
    /**
     * Show the form for bulk creating students.
     */
    public function bulkCreate()
    {
        return view('admin.students.bulk_create');
    }

    /**
     * Store bulk students from uploaded file.
     */
    public function bulkStore(Request $request)
    {
        // Prevent timeout/memory issues during bulk processing
        try { @set_time_limit(0); } catch (\Throwable $e) {}
        try { @ini_set('max_execution_time', '0'); } catch (\Throwable $e) {}
        try { @ini_set('memory_limit', '512M'); } catch (\Throwable $e) {}

        $request->validate([
            'file' => 'required|file|mimes:pdf,xlsx,xls,csv|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $studentsData = [];

        try {
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                // Handle Excel/CSV files with header-based mapping
                $sheet = Excel::toArray([], $file)[0] ?? [];
                if (empty($sheet)) { throw new \Exception('The uploaded file is empty or unreadable.'); }

                $headers = array_map(function($h){
                    return is_string($h) ? strtolower(preg_replace('/[^a-z0-9]+/i','', $h)) : '';
                }, array_shift($sheet) ?? []);

                // Build column index map using common header synonyms
                $map = [];
                foreach ($headers as $i => $h) {
                    if ($h === '') continue;
                    $map[$h] = $i;
                }
                $idx = function(array $keys) use ($map) {
                    foreach ($keys as $k) { if (isset($map[$k])) return $map[$k]; }
                    return null; // not found
                };
                $colStudentId = $idx(['studentid','idnumber','id','studentno','studentnumber']);
                $colLname     = $idx(['lastname','surname','familyname','lname']);
                $colFname     = $idx(['firstname','givenname','fname','first']);
                $colMI        = $idx(['mi','middleinitial','middlename','middle']);
                $colCollege   = $idx(['college','department','dept','school']);
                $colYear      = $idx(['year','yearlevel','level','grade']);
                $colGender    = $idx(['gender','sex']);
                $colEmail     = $idx(['email','emailaddress','mail']);

                foreach ($sheet as $row) {
                    // Skip completely empty rows
                    if (!is_array($row) || count(array_filter($row, function($v){ return trim((string)$v) !== ''; })) === 0) {
                        continue;
                    }

                    $get = function($col) use ($row) {
                        return $col !== null ? (trim((string)($row[$col] ?? ''))) : '';
                    };

                    $student_id = $get($colStudentId) ?: trim((string)($row[0] ?? ''));
                    $lname      = $get($colLname)     ?: trim((string)($row[1] ?? ''));
                    $fname      = $get($colFname)     ?: trim((string)($row[2] ?? ''));
                    $MI         = $get($colMI)        ?: trim((string)($row[3] ?? ''));
                    $college    = $get($colCollege)   ?: trim((string)($row[5] ?? ''));
                    $yearRaw    = $get($colYear)      ?: trim((string)($row[6] ?? ''));
                    $genderRaw  = $get($colGender)    ?: trim((string)($row[4] ?? ''));
                    $email      = strtolower($get($colEmail) ?: trim((string)($row[7] ?? '')));

                    // Normalize gender
                    $g = strtolower($genderRaw);
                    $gender = null;
                    if ($g !== '') {
                        if (in_array($g, ['male','m','1'])) { $gender = 'Male'; }
                        elseif (in_array($g, ['female','f','2'])) { $gender = 'Female'; }
                        elseif (in_array($g, ['prefernottosay','prefernotto say','prefer not to say','na','n/a','unknown'])) { $gender = 'Prefer not to say'; }
                        else { $gender = 'Other'; }
                    }

                    // Normalize year to integer
                    $yearDigits = preg_match('/\d+/', $yearRaw, $m) ? (int)$m[0] : null;

                    $studentsData[] = [
                        'student_id' => $student_id,
                        'lname'      => $lname,
                        'fname'      => $fname,
                        'MI'         => $MI,
                        'gender'     => $gender,
                        'college'    => $college,
                        'year'       => $yearDigits,
                        'email'      => $email,
                    ];
                }
            } elseif ($extension === 'pdf') {
                // Handle PDF files
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();

                // Parse PDF text - assuming tab or space separated data
                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 8) {
                        $studentsData[] = [
                            'student_id' => trim($parts[0] ?? ''),
                            'lname' => trim($parts[1] ?? ''),
                            'fname' => trim($parts[2] ?? ''),
                            'MI' => trim($parts[3] ?? ''),
                            'gender' => trim($parts[4] ?? ''),
                            'college' => trim($parts[5] ?? ''),
                            'year' => trim($parts[6] ?? ''),
                            'email' => strtolower(trim($parts[7] ?? '')),
                        ];
                    }
                }
            }

            $successCount = 0;
            $errors = [];

            foreach ($studentsData as $index => $studentData) {
                try {
                    // Validate each student data
                    $validator = \Illuminate\Support\Facades\Validator::make($studentData, [
                        'student_id' => 'required|string|min:5|max:20|unique:students,student_id',
                        'lname'      => 'required|string|max:155',
                        'fname'      => 'required|string|max:155',
                        'MI'         => 'nullable|string|max:155',
                        'gender'     => 'nullable|string|in:Male,Female,Prefer not to say,Other',
                        'college'    => 'required|string|max:155',
                        'year'       => 'required|integer|min:1|max:4',
                        'email'      => 'required|string|email|max:155',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    // Create student
                    $student = Student::create($studentData);

                    // Generate composite QR code (white background with headers)
                    $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";
                    $directory = 'qrcodes';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    $fileName = "qrcodes/student_{$student->student_id}.png";
                    try {
                        $composite = $this->generateCompositeQr($student);
                        Storage::disk('public')->put($fileName, $composite->toString());
                    } catch (\Throwable $imgEx) {
                        // Fallback: save plain QR if composite fails
                        $plainQr = QrCode::format('png')
                            ->size(300)
                            ->backgroundColor(255, 255, 255)
                            ->color(0, 0, 0)
                            ->generate("{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}");
                        Storage::disk('public')->put($fileName, $plainQr);
                        \Log::warning('Composite QR failed, saved plain QR instead', [
                            'student_id' => $student->student_id,
                            'error' => $imgEx->getMessage(),
                        ]);
                    }
                    $student->update(['qr_code_path' => $fileName]);

                    // Send email synchronously to ensure delivery without a queue worker
                    try {
                        $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));
                        Mail::to($student->email)->send(new StudentQrMail($student, $qrCodeBase64));
                    } catch (\Throwable $mailException) {
                        \Log::error('Failed to send bulk student QR email', [
                            'student_id' => $student->student_id,
                            'email' => $student->email,
                            'error' => $mailException->getMessage(),
                        ]);
                    }

                    $successCount++;
                } catch (\Throwable $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully registered {$successCount} students.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5)); // Show first 5 errors
                if (count($errors) > 5) {
                    $message .= " and " . (count($errors) - 5) . " more errors.";
                }
            }

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'total' => count($studentsData),
                    'success_count' => $successCount,
                    'failed_count' => count($errors),
                    'errors' => $errors,
                ]);
            }

            return redirect()->route('admin.students.index')->with('success', $message);

        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process file: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::active()->orderBy('created_at', 'desc')->get();
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
                "gender"     => "nullable|string|in:Male,Female,Prefer not to say,Other",
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

            // Generate QR code data (kept for data encoded in QR)
            $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

            // Ensure directory exists before saving QR
            $directory = 'qrcodes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Path to public storage where the QR code will be saved
            $fileName = "qrcodes/student_{$student->student_id}.png";

            // Generate composite QR image with white padded background and headers
            $composite = $this->generateCompositeQr($student);
            Storage::disk('public')->put($fileName, $composite->toString());

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
            "gender"     => "nullable|string|in:Male,Female,Prefer not to say,Other",
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

        // Generate QR code data (used inside composite)
        $data = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

        // Path to QR code file
        $fileName = "qrcodes/student_{$student->student_id}.png";

        // Make sure directory exists
        $directory = 'qrcodes';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Always regenerate composite QR to reflect current layout and data
        $composite = $this->generateCompositeQr($student);
        Storage::disk('public')->put($fileName, $composite->toString());
        $qrCodeBase64 = base64_encode($composite->toString());

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

    /**
     * Permanently delete the specified archived student.
     */
    public function permanentDelete(string $id)
    {
        $student = Student::findOrFail($id);

        // Ensure the student is archived before allowing permanent deletion
        if (!$student->archived) {
            return redirect()->route('admin.students.index')->with('error', 'Only archived students can be permanently deleted.');
        }

        // Delete the QR code file if it exists
        if ($student->qr_code_path && Storage::disk('public')->exists($student->qr_code_path)) {
            Storage::disk('public')->delete($student->qr_code_path);
        }

        // Permanently delete the student from the database
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student permanently deleted successfully.');
    }

    /**
     * Bulk archive selected students.
     */
    public function bulkArchive(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $studentIds = $request->input('student_ids');
        $archivedCount = 0;

        foreach ($studentIds as $id) {
            $student = Student::find($id);
            if ($student && !$student->archived) {
                $student->archive();
                $archivedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully archived {$archivedCount} student(s).",
            'archived_count' => $archivedCount,
        ]);
    }

    /**
     * Bulk delete selected archived students.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $studentIds = $request->input('student_ids');
        $deletedCount = 0;

        foreach ($studentIds as $id) {
            $student = Student::find($id);
            if ($student && $student->archived) {
                // Delete the QR code file if it exists
                if ($student->qr_code_path && Storage::disk('public')->exists($student->qr_code_path)) {
                    Storage::disk('public')->delete($student->qr_code_path);
                }
                // Permanently delete the student
                $student->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} archived student(s).",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Generate composite QR code image with text overlay.
     */
    private function generateCompositeQr(Student $student)
{
    $fullName = $student->fname . ' ' . $student->lname;
    if ($student->MI) {
        $fullName .= ' ' . $student->MI . '.';
    }

    $collegeYear = $student->college . ' - ' . $student->year;
    $qrData = "{$student->student_id} | {$student->lname} {$student->fname} | {$student->college} | Year: {$student->year}";

    // Generate QR code image (larger and clearer)
    $qrImage = QrCode::format('png')
        ->size(350)
        ->margin(1)
        ->backgroundColor(255, 255, 255)
        ->color(0, 0, 0)
        ->generate($qrData);

    $manager = new ImageManager(new Driver());

    // Adjusted canvas size — smaller bottom, enough for text
    $canvas = $manager->create(420, 420);
    $canvas->fill('#ffffff');

    /*/ Text positions higher up and larger font
    $canvas->text($fullName, 225, 60, function ($font) {
        $font->size(46);
        $font->color('#000000');
        $font->align('center');
    });

    $canvas->text($collegeYear, 225, 120, function ($font) {
        $font->size(34);
        $font->color('#333333');
        $font->align('center');
    });
    */

    // Place QR code lower (avoid overlapping text)
    $qrImg = $manager->read('data:image/png;base64,' . base64_encode($qrImage));
    $canvas->place($qrImg, 'center', 0, 10);

    return $canvas->toPng();
}

}
