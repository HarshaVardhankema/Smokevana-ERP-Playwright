<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ship_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // warehouse name
            $table->string('api'); // warehouse address
            $table->integer('priority')->nullable();
            $table->integer('business_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->boolean('usable')->nullable()->default(false);
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address_1')->nullable();
            $table->string('city_locality')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code')->nullable();
            $table->json('serviceList')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ship_stations');
    }
};
