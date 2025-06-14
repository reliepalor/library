<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowedBook;

class FixCA01Status extends Command
{
    protected $signature = 'books:fix-ca01';
    protected $description = 'Fix the status of CA01 book';

    public function handle()
    {
        $borrowed = BorrowedBook::where('book_id', 'CA01')
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->first();

        if ($borrowed) {
            $borrowed->status = 'returned';
            $borrowed->returned_at = now();
            $borrowed->save();
            $this->info('CA01 status updated successfully.');
        } else {
            $this->info('No matching record found for CA01.');
        }
    }
} 