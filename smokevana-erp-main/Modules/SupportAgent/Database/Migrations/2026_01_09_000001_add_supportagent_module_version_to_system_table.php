<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSupportagentModuleVersionToSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('system')->insert([
            'key' => 'supportagent_version',
            'value' => '1.0.0',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('system')->where('key', 'supportagent_version')->delete();
    }
}
