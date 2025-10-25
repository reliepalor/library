<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers_visitors', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('MI');
        });

        // Assign random gender to existing teachers/visitors
        DB::table('teachers_visitors')->whereNull('gender')->update([
            'gender' => DB::raw("CASE WHEN RAND() > 0.5 THEN 'Male' ELSE 'Female' END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers_visitors', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
