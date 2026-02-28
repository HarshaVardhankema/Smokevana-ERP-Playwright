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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('quarterly_bonus_amount', 22, 4)->nullable();
            $table->decimal('quarterly_sales_target', 22, 4)->nullable();
            $table->decimal('yearly_bonus_amount', 22, 4)->nullable();
            $table->decimal('yearly_sales_target', 22, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'quarterly_bonus_amount',
                'quarterly_sales_target',
                'yearly_bonus_amount',
                'yearly_sales_target',
            ]);
        });
    }
};
