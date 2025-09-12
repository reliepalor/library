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
    Schema::create('teachers_visitors', function (Blueprint $table) {
        $table->id();
        $table->string('lname');
        $table->string('fname');
        $table->string('MI')->nullable();
        $table->string('email')->unique();
        $table->string('department');
        $table->enum('role', ['teacher', 'visitor']);
        $table->string('qr_code_path')->nullable();
        $table->boolean('archived')->default(false);
        $table->timestamp('archived_at')->nullable();
        $table->timestamps();

        $table->index('email');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_visitors');
    }
};
