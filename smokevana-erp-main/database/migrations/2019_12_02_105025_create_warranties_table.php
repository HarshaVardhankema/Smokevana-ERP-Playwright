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
        Schema::create('warranties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('business_id');
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->enum('duration_type', ['days', 'months', 'years']);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('warranty_id')->nullable()->index()->after('created_by');
        });

        Schema::create('sell_line_warranties', function (Blueprint $table) {
            $table->integer('sell_line_id');
            $table->integer('warranty_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop sell_line_warranties table first
        Schema::dropIfExists('sell_line_warranties');
        
        // Drop warranty_id column from products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'warranty_id')) {
                    $table->dropColumn('warranty_id');
                }
            });
        }
        
        // Drop warranties table
        Schema::dropIfExists('warranties');
    }
};
