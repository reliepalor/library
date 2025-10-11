<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix the login timestamp field to remove ON UPDATE current_timestamp()
        // This prevents the login time from being overwritten when other fields are updated
        DB::statement('ALTER TABLE attendances MODIFY COLUMN login TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original behavior (though this shouldn't be needed)
        DB::statement('ALTER TABLE attendances MODIFY COLUMN login TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()');
    }
};
