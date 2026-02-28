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
        // Enable auto_send for order_shipped notification template
        DB::table('notification_templates')
            ->where('template_for', 'order_shipped')
            ->update([
                'auto_send' => '1',
                'updated_at' => now()
            ]);
        
        // Also ensure order_packed has auto_send enabled (if it exists)
        DB::table('notification_templates')
            ->where('template_for', 'order_packed')
            ->update([
                'auto_send' => '1',
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Disable auto_send for order_shipped notification template
        DB::table('notification_templates')
            ->where('template_for', 'order_shipped')
            ->update([
                'auto_send' => '0',
                'updated_at' => now()
            ]);
    }
};
