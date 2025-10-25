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
        Schema::table('teachers_visitors', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female', 'Prefer not to say', 'Other'])->nullable()->after('role');
        });
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
