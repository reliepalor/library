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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string(column: 'student_id');
            $table->string(column: 'lname');
            $table->string(column: 'fname');
            $table->string(column: 'MI');
            $table->string(column: 'college');
            $table->string(column: 'year');
            $table->string(column: 'email');
            $table->string('qr_code_path')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
