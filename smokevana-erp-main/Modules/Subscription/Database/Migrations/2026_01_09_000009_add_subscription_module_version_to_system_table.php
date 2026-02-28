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
     * @return void
     */
    public function up()
    {
        // Add module version to system table
        DB::table('system')->updateOrInsert(
            ['key' => 'subscription_module_version'],
            ['value' => '1.0.0']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('system')->where('key', 'subscription_module_version')->delete();
    }
};
