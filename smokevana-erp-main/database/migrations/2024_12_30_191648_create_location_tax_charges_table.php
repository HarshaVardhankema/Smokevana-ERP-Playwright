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
        Schema::create('location_tax_charges', function (Blueprint $table) {
            $table->id();
            $table->string('state_name');
            $table->string('state_code');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')               // This defines the foreign key constraint
            ->references('id')
            ->on('location_tax_types')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->string('tax_type');
            $table->float('value')->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_tax_charges');
    }
};
