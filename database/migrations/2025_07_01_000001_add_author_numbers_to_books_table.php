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
        Schema::table('books', function (Blueprint $table) {
            $table->string('author_number1')->nullable()->after('author');
            $table->string('author_number2')->nullable()->after('author_number1');
            $table->string('author_number3')->nullable()->after('author_number2');
            $table->string('author_number4')->nullable()->after('author_number3');
            $table->string('author_number5')->nullable()->after('author_number4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'author_number1',
                'author_number2',
                'author_number3',
                'author_number4',
                'author_number5',
            ]);
        });
    }
};
