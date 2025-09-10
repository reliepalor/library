<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAttendanceIdNullableInBorrowedBooks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->foreignId('attendance_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->foreignId('attendance_id')->nullable(false)->change();
        });
    }
}
