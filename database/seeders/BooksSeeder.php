<?php

namespace Database\Seeders;

use App\Models\Books;
use App\Models\Books as ModelsBooks;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BooksSeeder extends Seeder
{

    public function run(): void
    {
        ModelsBooks::factory(10)->create();
    }
}
