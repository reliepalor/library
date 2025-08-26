<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            // The returned_at column already exists in the base table, so we don't need to add it again
            // If we need to modify it, we can do so here
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->dropColumn('returned_at');
        });
    }
};
