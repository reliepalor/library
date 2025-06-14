<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BooksController extends Controller
{
    public function index()
    {
        $books = Books::active()->get();
        return view('books.index', compact('books'));
    }

    public function archived()
    {
        $books = Books::archived()->get();
        return view('books.archived', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_code' => 'required|unique:books',
            'name' => 'required',
            'author' => 'required',
            'description' => 'required',
            'section' => 'required',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Handle image uploads
        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("image$i")) {
                $image = $request->file("image$i");
                $imageName = time() . "_$i." . $image->getClientOriginalExtension();
                $image->move(public_path('images/books'), $imageName);
                $data["image$i"] = $imageName;
            }
        }

        Books::create($data);

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Books $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Books $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Books $book)
    {
        $request->validate([
            'book_code' => 'required|unique:books,book_code,' . $book->id,
            'name' => 'required',
            'author' => 'required',
            'description' => 'required',
            'section' => 'required',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Handle image uploads
        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("image$i")) {
                // Delete old image if exists
                if ($book->{"image$i"} && file_exists(public_path('images/books/' . $book->{"image$i"}))) {
                    unlink(public_path('images/books/' . $book->{"image$i"}));
                }

                $image = $request->file("image$i");
                $imageName = time() . "_$i." . $image->getClientOriginalExtension();
                $image->move(public_path('images/books'), $imageName);
                $data["image$i"] = $imageName;
            }
        }

        $book->update($data);

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function archive(Books $book)
    {
        if ($book->isBorrowed()) {
            return redirect()->route('books.index')
                ->with('error', 'Cannot archive book that is currently borrowed.');
        }

        $book->archive();

        return redirect()->route('books.index')
            ->with('success', 'Book archived successfully.');
    }

    public function unarchive(Books $book)
    {
        $book->unarchive();

        return redirect()->route('books.archived')
            ->with('success', 'Book unarchived successfully.');
    }

    public function destroy(Books $book)
    {
        if ($book->isBorrowed()) {
            return redirect()->route('books.index')
                ->with('error', 'Cannot delete book that is currently borrowed.');
        }

        // Delete associated images
        for ($i = 1; $i <= 5; $i++) {
            if ($book->{"image$i"} && file_exists(public_path('images/books/' . $book->{"image$i"}))) {
                unlink(public_path('images/books/' . $book->{"image$i"}));
            }
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
} 