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
        Schema::table('attendances', function (Blueprint $table) {
            // Add user_type field with default 'student' for existing records
            $table->enum('user_type', ['student', 'teacher'])->default('student')->after('id');
            
            // Add teacher_visitor_id field (nullable)
            $table->unsignedBigInteger('teacher_visitor_id')->nullable()->after('user_type');
            
            // Make student_id nullable since teachers won't have student_id
            $table->string('student_id')->nullable()->change();
            
            // Add system_logout if it doesn't exist
            if (!Schema::hasColumn('attendances', 'system_logout')) {
                $table->boolean('system_logout')->default(false)->after('logout');
            }
            
            // Add foreign key for teacher_visitor_id
            $table->foreign('teacher_visitor_id')
                  ->references('id')
                  ->on('teachers_visitors')
                  ->onDelete('cascade');
            
            // Add composite index for better query performance
            $table->index(['user_type', 'login']);
            $table->index('teacher_visitor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign key and indexes
            $table->dropForeign(['teacher_visitor_id']);
            $table->dropIndex(['user_type', 'login']);
            $table->dropIndex(['teacher_visitor_id']);
            
            // Drop columns
            $table->dropColumn(['user_type', 'teacher_visitor_id']);
            
            // Make student_id non-nullable again
            $table->string('student_id')->nullable(false)->change();
        });
    }
};
