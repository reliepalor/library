<?php


namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\TeacherVisitor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Mail\StudentQrMail;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class TeacherVisitorController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachersVisitors = TeacherVisitor::active()->get();
        $archivedTeachersVisitors = TeacherVisitor::archived()->get();
        return view('admin.teachers_visitors.index', [
            "teachersVisitors" => $teachersVisitors,
            "archivedTeachersVisitors" => $archivedTeachersVisitors
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachersVisitors = TeacherVisitor::all();
        return view("admin.teachers_visitors.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Normalize/trim inputs before validation
            $request->merge([
                'lname'      => trim((string) $request->input('lname')),
                'fname'      => trim((string) $request->input('fname')),
                'MI'         => trim((string) $request->input('MI')),
                'email'      => strtolower(trim((string) $request->input('email'))),
                'department' => trim((string) $request->input('department')),
                'role'       => trim((string) $request->input('role')),
            ]);
            $validate = $request->validate([
                "lname"      => "required|string|max:155",
                "fname"      => "required|string|max:155",
                "MI"         => "nullable|string|max:155",
                "email"      => "required|string|email|max:155|unique:teachers_visitors,email",
                "department" => "required|string|max:155",
                "role"       => "required|in:teacher,visitor",
            ]);

            // Create the teacher/visitor record
            $teacherVisitor = TeacherVisitor::create($validate);

            // Generate QR code data in format: teacher_visitor_id|name|department|role
            $name = $teacherVisitor->fname . ' ' . $teacherVisitor->lname;
            $data = "{$teacherVisitor->id}|{$name}|{$teacherVisitor->department}|{$teacherVisitor->role}";

            // Ensure directory exists before saving QR
            $directory = 'qrcodes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Path to public storage where the QR code will be saved
            $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";

            // Generate and save the QR code with a white background and black foreground
            Storage::disk('public')->put($fileName, QrCode::format('png')
                ->size(300)  // Set size of the QR code
                ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
                ->color(0, 0, 0)  // Set the color of the QR code itself to black (RGB: 0, 0, 0)
                ->generate($data));

            // Save QR code path to record
            if ($teacherVisitor->qr_code_path !== $fileName) {
                $teacherVisitor->qr_code_path = $fileName;
                $teacherVisitor->save();
            }

            // Attempt to send email with QR code; do not fail the whole flow if mail fails
            try {
                $qrCodeBase64 = base64_encode(Storage::disk('public')->get($fileName));
                Mail::to($teacherVisitor->email)->send(new StudentQrMail($teacherVisitor, $qrCodeBase64));
                $successMessage = 'Teacher/Visitor saved and QR Code sent!';
            } catch (\Throwable $mailException) {
                // Log the mail error for debugging
                \Log::error('Failed to send teacher/visitor QR email', [
                    'id' => $teacherVisitor->id,
                    'email' => $teacherVisitor->email,
                    'error' => $mailException->getMessage(),
                ]);
                $successMessage = 'Teacher/Visitor saved, but sending the QR code email failed.';
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
            return redirect()->route("admin.teachers_visitors.index")->with("success", $successMessage);
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
            \Log::error('TeacherVisitor store failed', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);
            // Catch-all for other errors to avoid generic HTML 500 in AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred while adding the teacher/visitor.',
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
        $teacherVisitor = TeacherVisitor::findOrFail($id);

        $data = "Name: {$teacherVisitor->lname} {$teacherVisitor->fname} | Email: {$teacherVisitor->email} | Department: {$teacherVisitor->department} | Role: {$teacherVisitor->role}";

        // Generate the QR code image (no merge)
        $image = QrCode::format('png')->size(300)
            ->backgroundColor(255, 255, 255)  // Set background color to white (RGB: 255, 255, 255)
            ->color(0, 0, 0)
            ->generate($data);

        // Save the image in storage
        $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";
        Storage::disk('public')->put($fileName, $image);

        // Also generate a base64 version to show on the page
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($image);

        return view('admin.teachers_visitors.show', compact('teacherVisitor', 'qrCodeBase64'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $teacherVisitor = TeacherVisitor::findOrFail($id);
        return view("admin.teachers_visitors.edit", ["teacherVisitor" => $teacherVisitor]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $teacherVisitor = TeacherVisitor::findOrFail($id);
        $validate = $request->validate([
            "lname"      => "required|string|max:155",
            "fname"      => "required|string|max:155",
            "MI"         => "nullable|string|max:155",
            "email"      => "required|string|email|max:155|unique:teachers_visitors,email," . $id,
            "department" => "required|string|max:155",
            "role"       => "required|in:teacher,visitor",
        ]);
        $teacherVisitor->update($validate);

        // If QR code needs to be regenerated (e.g., name or email changed)
        $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";
        $name = $teacherVisitor->fname . ' ' . $teacherVisitor->lname;
        $data = "{$teacherVisitor->id}|{$name}|{$teacherVisitor->department}|{$teacherVisitor->role}";
        \Storage::disk('public')->put($fileName, \QrCode::format('png')
            ->size(300)
            ->generate($data));
        $teacherVisitor->qr_code_path = $fileName;
        $teacherVisitor->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Teacher/Visitor updated successfully.']);
        }
        return redirect()->route('admin.teachers_visitors.index')->with('success', 'Teacher/Visitor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacherVisitor = TeacherVisitor::findOrFail($id)->delete();
        return redirect()->route("admin.teachers_visitors.index")->with("success", "Teacher/Visitor Deleted.");
    }

    public function generateTeacherVisitorQr($id)
    {
        // Find the teacher/visitor by ID
        $teacherVisitor = TeacherVisitor::findOrFail($id);

        // Generate the QR code with teacher/visitor info
        $data = "Name: {$teacherVisitor->lname} {$teacherVisitor->fname}\n" .
            "Email: {$teacherVisitor->email}\n" .
            "Department: {$teacherVisitor->department}\n" .
            "Role: {$teacherVisitor->role}";

        // Path to save the QR code image
        $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";
        \Storage::disk('public')->put($fileName, \QrCode::format('png')->size(300)->generate($data));

        // Generate a base64 version of the QR code for use in the view
        $qrCodeBase64 = base64_encode(\Storage::disk('public')->get($fileName));

        // Return a styled Blade view for printing
        return view('admin.teachers_visitors.print_qr', [
            'teacherVisitor' => $teacherVisitor,
            'qrCodeBase64' => $qrCodeBase64,
        ]);
    }

    /**
     * Resend QR code to teacher/visitor's email
     */
    public function resendQrCode($id)
    {
        // Find the teacher/visitor by ID
        $teacherVisitor = TeacherVisitor::findOrFail($id);

        // Generate QR code data in format: teacher_visitor_id|name|department|role
        $name = $teacherVisitor->fname . ' ' . $teacherVisitor->lname;
        $data = "{$teacherVisitor->id}|{$name}|{$teacherVisitor->department}|{$teacherVisitor->role}";

        // Path to QR code file
        $fileName = "qrcodes/teacher_visitor_{$teacherVisitor->id}.png";

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
        Mail::to($teacherVisitor->email)->send(new StudentQrMail($teacherVisitor, $qrCodeBase64));

        // Redirect back with success message
        return redirect()->back()->with('success', "QR Code resent to {$teacherVisitor->fname} {$teacherVisitor->lname}'s email!");
    }

    /**
     * Archive the specified teacher/visitor.
     */
    public function archive(string $id)
    {
        $teacherVisitor = TeacherVisitor::findOrFail($id);
        $teacherVisitor->archive();
        return redirect()->route("admin.teachers_visitors.index")->with("success", "Teacher/Visitor Archived Successfully.");
    }

    /**
     * Unarchive the specified teacher/visitor.
     */
    public function unarchive(string $id)
    {
        $teacherVisitor = TeacherVisitor::findOrFail($id);
        $teacherVisitor->unarchive();
        return redirect()->route("admin.teachers_visitors.index")->with("success", "Teacher/Visitor Unarchived Successfully.");
    }

    /**
     * Display archived teachers/visitors.
     */
    public function archived()
    {
        $archivedTeachersVisitors = TeacherVisitor::archived()->get();
        return view('admin.teachers_visitors.archived', ["teachersVisitors" => $archivedTeachersVisitors]);
    }
}
