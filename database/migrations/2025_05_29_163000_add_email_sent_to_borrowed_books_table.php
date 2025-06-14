<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->timestamp('email_sent_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('borrowed_books', function (Blueprint $table) {
            $table->dropColumn('email_sent_at');
        });
    }
}; 