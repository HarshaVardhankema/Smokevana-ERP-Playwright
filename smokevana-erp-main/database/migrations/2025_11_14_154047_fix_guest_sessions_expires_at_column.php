<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix the expires_at column to prevent MySQL from automatically updating it
     * when other columns are modified (removes ON UPDATE CURRENT_TIMESTAMP behavior)
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to alter the expires_at column to remove ON UPDATE CURRENT_TIMESTAMP
        DB::statement('ALTER TABLE guest_sessions MODIFY COLUMN expires_at TIMESTAMP NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to the original behavior (first TIMESTAMP column gets auto-update)
        DB::statement('ALTER TABLE guest_sessions MODIFY COLUMN expires_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
};
