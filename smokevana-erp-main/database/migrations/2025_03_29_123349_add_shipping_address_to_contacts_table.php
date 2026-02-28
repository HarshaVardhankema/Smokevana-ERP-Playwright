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
        Schema::table('contacts', function (Blueprint $table) {
                $table->string('shipping_first_name')->nullable();
                $table->string('shipping_last_name')->nullable();
                $table->string('shipping_company')->nullable();
                $table->string('shipping_address1')->nullable();
                $table->string('shipping_address2')->nullable()->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_state')->nullable();
                $table->string('shipping_zip')->nullable();
                $table->string('shipping_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_first_name',
                'shipping_last_name',
                'shipping_company',
                'shipping_address1',
                'shipping_address2',
                'shipping_city',
                'shipping_state',
                'shipping_zip',
                'shipping_country',
            ]);
        });
    }
};
