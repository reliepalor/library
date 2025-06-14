<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowedBook;
use App\Models\Books;

class CheckBorrowedBooks extends Command
{
    protected $signature = 'books:check-borrowed';
    protected $description = 'Check the status of borrowed books';

    public function handle()
    {
        $books = Books::all();
        
        foreach ($books as $book) {
            $borrowed = BorrowedBook::where('book_id', $book->book_code)
                ->where('status', 'approved')
                ->whereNull('returned_at')
                ->with('student')
                ->first();

            $this->info("Book: {$book->book_code} - {$book->name}");
            if ($borrowed) {
                $this->info("Status: Borrowed");
                $this->info("Borrowed by: {$borrowed->student->name}");
                $this->info("Status: {$borrowed->status}");
                $this->info("Returned at: " . ($borrowed->returned_at ? $borrowed->returned_at : 'Not returned'));
            } else {
                $this->info("Status: Available");
            }
            $this->info("-------------------");
        }
    }
} 