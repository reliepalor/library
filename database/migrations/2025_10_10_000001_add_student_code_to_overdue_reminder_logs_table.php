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
        Schema::table('overdue_reminder_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('overdue_reminder_logs', 'student_code')) {
                $table->string('student_code')->nullable()->after('student_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overdue_reminder_logs', function (Blueprint $table) {
            if (Schema::hasColumn('overdue_reminder_logs', 'student_code')) {
                $table->dropColumn('student_code');
            }
        });
    }
};
