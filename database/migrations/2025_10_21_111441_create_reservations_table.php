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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('user_type')->default('student');
            $table->string('book_id');
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('reserved_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('book_id')->references('book_code')->on('books');
            $table->index(['book_id', 'status']);
            $table->index(['student_id', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
