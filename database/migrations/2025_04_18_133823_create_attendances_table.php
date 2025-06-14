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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // FK from students.student_id
            $table->string('activity')->nullable(); // optional (Study, Borrow, etc)
            $table->timestamp('login')->nullable();
            $table->timestamp('logout')->nullable();
            $table->timestamps();
    
            // FK relationship (optional for integrity)
            $table->foreign('student_id')->references('student_id')->on('students');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
