<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Books;
use Illuminate\Support\Facades\Storage;

class FixBookImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:book-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix book image paths to move from public/images/books to storage/app/public/books';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to fix book image paths...');

        $books = Books::all();

        foreach ($books as $book) {
            $updated = false;
            for ($i = 1; $i <= 5; $i++) {
                $imageField = "image$i";
                $imagePath = $book->$imageField;

                if ($imagePath && !str_starts_with($imagePath, 'books/')) {
                    $oldPath = public_path('images/books/' . $imagePath);
                    $newPath = storage_path('app/public/books/' . $imagePath);

                    if (file_exists($oldPath)) {
                        // Create directory if not exists
                        if (!file_exists(dirname($newPath))) {
                            mkdir(dirname($newPath), 0755, true);
                        }
                        // Move the file
                        rename($oldPath, $newPath);
                        $book->$imageField = 'books/' . $imagePath;
                        $updated = true;
                        $this->info("Moved $oldPath to $newPath");
                    } else {
                        $this->warn("File does not exist: $oldPath");
                    }
                }
            }
            if ($updated) {
                $book->save();
                $this->info("Updated book ID {$book->id} image paths.");
            }
        }

        $this->info('Finished fixing book image paths.');
        return 0;
    }
}
