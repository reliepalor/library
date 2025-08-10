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
            $table->index(['student_id', 'login']);
            $table->index(['login', 'logout']);
            $table->index(['student_id', 'login', 'logout']);
            $table->index(['created_at']);
        });

        // Optimize attendance_histories table indexes
        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->index(['student_id', 'date']);
            $table->index(['date', 'college']);
            $table->index(['time_in', 'time_out']);
        });

        // Add partial indexes for better performance
        DB::statement('CREATE INDEX idx_attendance_active ON attendances (student_id, login) WHERE logout IS NULL');
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'login']);
            $table->dropIndex(['login', 'logout']);
            $table->dropIndex(['student_id', 'login', 'logout']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'date']);
            $table->dropIndex(['date', 'college']);
            $table->dropIndex(['time_in', 'time_out']);
        });

        DB::statement('DROP INDEX IF EXISTS idx_attendance_active');
    }
};
