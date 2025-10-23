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
            // Add new column for teacher/visitor email
            $table->string('teacher_visitor_email')->nullable()->after('student_id');

            // Add foreign key constraints
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('teacher_visitor_email')->references('email')->on('teachers_visitors')->onDelete('cascade');

            // Update indexes
            $table->index(['teacher_visitor_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['student_id']);
            $table->dropForeign(['teacher_visitor_email']);

            // Drop the new column
            $table->dropColumn('teacher_visitor_email');
        });
    }
};
