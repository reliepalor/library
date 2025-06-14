<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_histories', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('college');
            $table->string('activity');
            $table->timestamp('time_in');
            $table->timestamp('time_out')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->index(['student_id', 'date']);
            $table->index('college');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_histories');
    }
}; 