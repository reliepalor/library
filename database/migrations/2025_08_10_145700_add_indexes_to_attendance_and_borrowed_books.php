<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add helpful indexes to speed up frequent queries
        Schema::table('attendances', function (Blueprint $table) {
            // Composite index for common lookups today by student and login range
            $table->index(['student_id', 'login'], 'attendances_student_login_index');
            // For quick filtering by null/non-null logout
            $table->index('logout', 'attendances_logout_index');
        });

        // If your table is named differently, adjust accordingly
        if (Schema::hasTable('borrowed_books')) {
            Schema::table('borrowed_books', function (Blueprint $table) {
                $table->index(['book_id', 'status'], 'borrowed_books_book_status_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_student_login_index');
            $table->dropIndex('attendances_logout_index');
        });

        if (Schema::hasTable('borrowed_books')) {
            Schema::table('borrowed_books', function (Blueprint $table) {
                $table->dropIndex('borrowed_books_book_status_index');
            });
        }
    }
};
