<?php

namespace Database\Seeders;

use App\Models\DojoCat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DojoCatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DojoCat::factory()->count(20)->create();
    }
}
