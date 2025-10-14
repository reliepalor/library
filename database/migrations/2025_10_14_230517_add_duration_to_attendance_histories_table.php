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
            $table->integer('duration')->nullable()->after('time_out')->comment('Duration in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_histories', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
