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
        Schema::table('categories', function (Blueprint $table) {
            //
             $table->string('commission_type', 20)
                  ->default('none')
                  ->nullable()
                  ->after('slug');
            

            // Commission value: % or fixed amount
            $table->decimal('commission_value', 10, 4)
                  ->default(0.0000)
                  ->nullable()
                  ->after('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            //
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
