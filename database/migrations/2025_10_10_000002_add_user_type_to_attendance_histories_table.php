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
        Schema::table('attendance_histories', function (Blueprint $table) {
            // Add user_type field with default 'student' for existing records
            $table->enum('user_type', ['student', 'teacher'])->default('student')->after('id');
            
            // Add teacher_visitor_id field (nullable)
            $table->unsignedBigInteger('teacher_visitor_id')->nullable()->after('user_type');
            
            // Make student_id nullable since teachers won't have student_id
            $table->unsignedBigInteger('student_id')->nullable()->change();
            
            // Add department field for teachers (nullable)
            $table->string('department')->nullable()->after('college');
            
            // Add role field for teachers (nullable - 'teacher' or 'visitor')
            $table->enum('role', ['teacher', 'visitor'])->nullable()->after('department');
            
            // Add foreign key for teacher_visitor_id
            $table->foreign('teacher_visitor_id')
                  ->references('id')
                  ->on('teachers_visitors')
                  ->onDelete('cascade');
            
            // Add indexes for better query performance
            $table->index(['user_type', 'date']);
            $table->index('teacher_visitor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_histories', function (Blueprint $table) {
            // Drop foreign key and indexes
            $table->dropForeign(['teacher_visitor_id']);
            $table->dropIndex(['user_type', 'date']);
            $table->dropIndex(['teacher_visitor_id']);
            
            // Drop columns
            $table->dropColumn(['user_type', 'teacher_visitor_id', 'department', 'role']);
            
            // Make student_id non-nullable again
            $table->unsignedBigInteger('student_id')->nullable(false)->change();
        });
    }
};
