<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Optimize attendance table indexes
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['student_id', 'login'], 'attendances_student_login_index');
            $table->index(['login', 'logout'], 'attendances_login_logout_index');
            $table->index(['student_id', 'login', 'logout'], 'attendances_student_login_logout_index');
            $table->index(['created_at'], 'attendances_created_at_index');
        });

        // Optimize attendance_histories table indexes
        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->index(['student_id', 'date'], 'attendance_histories_student_date_index');
            $table->index(['date', 'college'], 'attendance_histories_date_college_index');
            $table->index(['time_in', 'time_out'], 'attendance_histories_time_in_out_index');
        });

        // Add partial indexes for better performance
        // DB::statement('CREATE INDEX idx_attendance_active ON attendances (student_id, login) WHERE logout IS NULL');
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_student_login_index');
            $table->dropIndex('attendances_login_logout_index');
            $table->dropIndex('attendances_student_login_logout_index');
            $table->dropIndex('attendances_created_at_index');
        });

        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->dropIndex('attendance_histories_student_date_index');
            $table->dropIndex('attendance_histories_date_college_index');
            $table->dropIndex('attendance_histories_time_in_out_index');
        });

        // DB::statement('DROP INDEX IF EXISTS idx_attendance_active');
    }
};
