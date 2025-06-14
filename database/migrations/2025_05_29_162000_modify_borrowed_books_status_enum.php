<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, modify the column to be a string temporarily
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->string('status')->change();
        });

        // Then update any 'returned' status to 'approved' temporarily
        DB::table('borrowed_books')
            ->where('status', 'returned')
            ->update(['status' => 'approved']);

        // Now modify it back to enum with the new value
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->change();
        });
    }
}; 