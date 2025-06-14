<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowedBook;

class UpdateReturnedBooks extends Command
{
    protected $signature = 'books:update-returned';
    protected $description = 'Update returned books with returned_at timestamp';

    public function handle()
    {
        $count = BorrowedBook::where('status', 'returned')
            ->whereNull('returned_at')
            ->update(['returned_at' => now()]);

        $this->info("Updated {$count} returned books with returned_at timestamp.");
    }
} 