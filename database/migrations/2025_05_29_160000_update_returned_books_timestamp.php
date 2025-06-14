<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('borrowed_books')
            ->where('status', 'returned')
            ->whereNull('returned_at')
            ->update(['returned_at' => now()]);
    }

    public function down(): void
    {
        // No need to reverse this migration
    }
}; 