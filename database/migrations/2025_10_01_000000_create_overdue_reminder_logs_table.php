<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOverdueReminderLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('overdue_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('student_name');
            $table->string('student_email');
            $table->string('college')->nullable();
            $table->text('books'); // JSON encoded list of books
            $table->timestamp('reminder_sent_at');
            $table->timestamps();

            $table->index('student_id');
            $table->index('reminder_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overdue_reminder_logs');
    }
}
