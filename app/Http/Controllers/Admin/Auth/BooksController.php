<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Support\Facades\Storage;

class BooksController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Books::all();
        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = Books::all();
        return view('admin.books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'author' => 'required',
            'description' => 'nullable',
            'section' => 'required',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Generate book code
        $section = $request->section;
        $latestBook = Books::where('section', $section)
            ->orderBy('book_code', 'desc')
            ->first();

        if ($latestBook) {
            // Extract the number from the latest book code
            $number = (int) substr($latestBook->book_code, -2);
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        // Format the new book code (e.g., CICS01, CICS02, etc.)
        $data['book_code'] = $section . str_pad($newNumber, 2, '0', STR_PAD_LEFT);

        // Handle image uploads
        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile("image$i")) {
                $image = $request->file("image$i");
                $imageName = time() . "_$i." . $image->getClientOriginalExtension();
                $path = $image->storeAs('books', $imageName, 'public');
                $data["image$i"] = $path;
            }
        }

        Books::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Book created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $books = Books::findOrFail($id);
        return view('admin.books.edit', compact('books'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'author' => 'required',
            'description' => 'nullable',
            'section' => 'required',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $book = Books::findOrFail($id);
            
            // Update basic book information
            $book->name = $request->name;
            $book->author = $request->author;
            $book->description = $request->description;
            $book->section = $request->section;

            // Handle image uploads
            for ($i = 1; $i <= 5; $i++) {
                if ($request->hasFile("image$i")) {
                    // Delete old image if exists
                    if ($book->{"image$i"} && Storage::disk('public')->exists($book->{"image$i"})) {
                        Storage::disk('public')->delete($book->{"image$i"});
                    }

                    $image = $request->file("image$i");
                    $imageName = time() . "_$i." . $image->getClientOriginalExtension();
                    $path = $image->storeAs('books', $imageName, 'public');
                    $book->{"image$i"} = $path;
                }
            }

            // Handle additional authors
            for ($i = 1; $i <= 5; $i++) {
                $book->{"author_number$i"} = $request->{"author_number$i"};
            }

            $book->save();

            return redirect()->route('admin.books.index')
                ->with('success', 'Book updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update book: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function archive($id)
    {
        $book = Books::findOrFail($id);
        
        if ($book->isBorrowed()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Cannot archive book that is currently borrowed.');
        }

        $book->archive();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book archived successfully.');
    }

    public function unarchive($id)
    {
        $book = Books::findOrFail($id);
        $book->unarchive();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book unarchived successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Books::findOrFail($id);
        
        if ($book->isBorrowed()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Cannot delete book that is currently borrowed.');
        }

        // Delete associated images
        for ($i = 1; $i <= 5; $i++) {
            if ($book->{"image$i"} && file_exists(public_path('images/books/' . $book->{"image$i"}))) {
                unlink(public_path('images/books/' . $book->{"image$i"}));
            }
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
