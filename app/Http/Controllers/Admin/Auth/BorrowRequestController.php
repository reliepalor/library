<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\BorrowedBook;
use Illuminate\Http\Request;

class BorrowRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'book_id' => 'required|string',
        ]);

        // Convert book_id to uppercase for validation
        $bookCode = strtoupper($request->book_id);

        // Validate book code exists
        $book = \App\Models\Books::where('book_code', $bookCode)->first();
        if (!$book) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid book code.'], 422);
            }
            return back()->withErrors(['book_id' => 'Invalid book code.']);
        }

        // Check if book is already borrowed
        $isBorrowed = \App\Models\BorrowedBook::where('book_id', $bookCode)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->exists();

        if ($isBorrowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This book is already borrowed by another student.'], 422);
            }
            return back()->withErrors(['book_id' => 'This book is already borrowed by another student.']);
        }

        $borrow = \App\Models\BorrowedBook::create([
            'student_id' => $request->student_id,
            'book_id' => $bookCode, // Use the uppercase version
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Borrow request submitted and waiting for admin approval.']);
        }
        return redirect()->route('admin.attendance.index')->with('success', 'Borrow request submitted and waiting for admin approval.');
    }

    public function index()
    {
        $requests = \App\Models\BorrowedBook::where('status', 'pending')->with(['student', 'book'])->latest()->get();
        $borrowedBooks = \App\Models\BorrowedBook::whereIn('status', ['approved', 'rejected'])->with(['student', 'book'])->latest()->get();
        return view('admin.borrow_requests.index', compact('requests', 'borrowedBooks'));
    }

    public function approve($id)
    {
        $request = \App\Models\BorrowedBook::findOrFail($id);
        $request->status = 'approved';
        $request->save();
        return redirect()->back()->with('success', 'Borrow request approved.');
    }

    public function reject(Request $request, $id)
    {
        $borrow = \App\Models\BorrowedBook::findOrFail($id);
        $borrow->status = 'rejected';
        $borrow->rejection_reason = $request->input('rejection_reason');
        $borrow->save();
        return redirect()->back()->with('success', 'Borrow request rejected.');
    }

    public function updateStatus(Request $request, BorrowedBook $borrowRequest)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,returned'
            ]);

            $borrowRequest->update([
                'status' => $validated['status'],
                'rejection_reason' => $validated['status'] === 'rejected' ? 'Rejected by admin' : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark a borrowed book as returned
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReturned($id)
    {
        try {
            $borrowRequest = BorrowedBook::findOrFail($id);
            
            $borrowRequest->update([
                'status' => 'returned',
                'returned_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Book marked as returned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark book as returned: ' . $e->getMessage()
            ], 500);
        }
    }
}
