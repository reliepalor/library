<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cat;
use App\Models\DojoCat;

class CatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dojocat = DojoCat::first(); // Get an existing DojoCat

        Cat::factory(10)->create([
            'dojocat_id' => $dojocat ? $dojocat->id : null, // Assign a valid ID
        ]);
    }
}
