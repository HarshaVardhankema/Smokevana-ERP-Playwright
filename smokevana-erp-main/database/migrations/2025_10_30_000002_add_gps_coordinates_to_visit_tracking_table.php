<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_tracking', function (Blueprint $table) {
            // Add check-in GPS coordinates
            $table->decimal('checkin_latitude', 10, 7)->nullable()->after('start_time');
            $table->decimal('checkin_longitude', 10, 7)->nullable()->after('checkin_latitude');
            
            // Add checkout_time if it doesn't exist yet
            if (!Schema::hasColumn('visit_tracking', 'checkout_time')) {
                $table->datetime('checkout_time')->nullable()->after('start_time');
            }
            
            // Add check-out GPS coordinates
            $table->decimal('checkout_latitude', 10, 7)->nullable()->after('checkout_time');
            $table->decimal('checkout_longitude', 10, 7)->nullable()->after('checkout_latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visit_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_latitude',
                'checkin_longitude',
                'checkout_latitude',
                'checkout_longitude'
            ]);
        });
    }
};

