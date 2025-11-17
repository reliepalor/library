<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\Reservation;
use App\Models\TeacherVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBooksController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $books = Books::where('archived', false)->paginate(9);
        return view('user.books.index', compact('books'));
    }

    /**
     * Display the specified book details.
     */
    public function show($id)
    {
        $book = Books::findOrFail($id);

        $user = Auth::user();
        $hasReservation = false;

        // Determine user type and identifiers
        if ($user->usertype === 'user') {
            $student = $user->student;
            $teacherVisitor = $user->teacherVisitor;

            if ($student) {
                $studentId = $student->student_id;
                $teacherVisitorEmail = null;
            } elseif ($teacherVisitor) {
                $studentId = null;
                $teacherVisitorEmail = $teacherVisitor->email;
            }

            // Check if user has an active reservation for this book
            $hasReservation = Reservation::where(function ($query) use ($studentId, $teacherVisitorEmail) {
                if ($studentId) {
                    $query->where('student_id', $studentId);
                } else {
                    $query->where('teacher_visitor_email', $teacherVisitorEmail);
                }
            })
                ->where('book_id', $book->book_code)
                ->where('status', 'active')
                ->exists();
        }

        return view('user.books.show', compact('book', 'hasReservation'));
    }

    /**
     * Reserve the specified book.
     */
    public function reserve(Request $request, $id)
    {
        $book = Books::findOrFail($id);

        // Check if book is archived
        if ($book->archived) {
            return redirect()->back()->with('error', 'This book is not available for reservation.');
        }

        // Check if book is already borrowed
        if ($book->isBorrowed()) {
            return redirect()->back()->with('error', 'This book is currently borrowed and cannot be reserved.');
        }

        // Allow reserving even if already reserved

        $user = Auth::user();

        // Determine user type and identifiers
        if ($user->usertype === 'user') {
            // Check if user is a student or teacher/visitor
            $student = $user->student;
            $teacherVisitor = $user->teacherVisitor;

            if ($student) {
                // This is a student
                $studentId = $student->student_id;
                $teacherVisitorEmail = null;
                $userType = 'student';
            } elseif ($teacherVisitor) {
                // This is a teacher/visitor
                $studentId = null;
                $teacherVisitorEmail = $teacherVisitor->email;
                $userType = 'teacher_visitor';
            } else {
                return redirect()->back()->with('error', 'User record not found. Please contact administrator.');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid user type.');
        }

        // Check if user already has an active reservation for this book
        $existingReservation = Reservation::where(function ($query) use ($studentId, $teacherVisitorEmail) {
            if ($studentId) {
                $query->where('student_id', $studentId);
            } else {
                $query->where('teacher_visitor_email', $teacherVisitorEmail);
            }
        })
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->first();

        if ($existingReservation) {
            return redirect()->back()->with('error', 'You already have an active reservation for this book.');
        }

        // Check if user has reached maximum reservations (e.g., 3)
        $activeReservationsCount = Reservation::where(function ($query) use ($studentId, $teacherVisitorEmail) {
            if ($studentId) {
                $query->where('student_id', $studentId);
            } else {
                $query->where('teacher_visitor_email', $teacherVisitorEmail);
            }
        })
            ->where('status', 'active')
            ->count();

        if ($activeReservationsCount >= 3) {
            return redirect()->back()->with('error', 'You have reached the maximum number of active reservations (3).');
        }

        // Create reservation
        Reservation::create([
            'student_id' => $studentId,
            'teacher_visitor_email' => $teacherVisitorEmail,
            'user_type' => $userType,
            'book_id' => $book->book_code,
            'status' => 'active',
            'reserved_at' => now(),
            'expires_at' => now()->addDays(7), // Reservation expires in 7 days
        ]);

        return redirect()->back()->with('success', 'Book reserved successfully! You have 7 days to borrow it.');
    }
}
