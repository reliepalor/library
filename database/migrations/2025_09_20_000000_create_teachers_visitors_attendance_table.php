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
        Schema::create('teachers_visitors_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_visitor_id');
            $table->string('activity');
            $table->timestamp('login');
            $table->timestamp('logout')->nullable();
            $table->boolean('system_logout')->default(false);
            $table->timestamps();

            $table->foreign('teacher_visitor_id')->references('id')->on('teachers_visitors')->onDelete('cascade');
            $table->index('teacher_visitor_id');
            $table->index('login');
            $table->index('logout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_visitors_attendance');
    }
};
