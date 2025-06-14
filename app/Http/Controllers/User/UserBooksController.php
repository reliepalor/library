<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Books;
use Illuminate\Http\Request;

class UserBooksController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $books = Books::all();
        return view('user.books.index', compact('books'));
    }

    /**
     * Display the specified book details.
     */
    public function show($id)
    {
        $book = Books::findOrFail($id);
        return view('user.books.show', compact('book'));
    }

    /**
     * Show the form for reserving the specified book.
     * For now, this can be a placeholder or redirect.
     */
    public function reserve($id)
    {
        // Placeholder for reserve logic
        // You can implement reservation logic here or redirect to a form
        return redirect()->route('user.books.index')->with('success', 'Reserve functionality coming soon.');
    }
}
