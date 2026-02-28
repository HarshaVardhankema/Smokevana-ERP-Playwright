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
        Schema::table('transactions', function (Blueprint $table) {

            if (!Schema::hasColumn('transactions', 'shipment')) {
                $table->string('picking_status')->nullable()->default(null); // picking || verifying || 
                $table->unsignedInteger('pickerID')->nullable()->default(null);
                $table->boolean('isVerified')->nullable()->default(false);
                $table->json('shipment')->nullable();
                // $table->dropColumn('shipment');
                $table->boolean('isEditable')->default(true);
                $table->unsignedInteger('editingSalesRep')->default(null);

            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {

            if (Schema::hasColumn('transactions', 'shipment')) {
                $table->dropColumn('picking_status');
                $table->dropColumn('pickerID');
                $table->dropColumn('isVerified');
                $table->dropColumn('shipment');
                if (Schema::hasColumn('transactions', 'isEditable')) {
                    $table->dropColumn('isEditable');
                    $table->dropColumn('editingSalesRep');
                }
                
            }
        });
    }
};
