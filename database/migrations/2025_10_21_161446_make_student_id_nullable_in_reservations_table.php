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
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['student_id']);

            // Make student_id nullable
            $table->string('student_id')->nullable()->change();

            // Re-add the foreign key constraint with nullable
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['student_id']);

            // Make student_id not nullable again
            $table->string('student_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }
};
