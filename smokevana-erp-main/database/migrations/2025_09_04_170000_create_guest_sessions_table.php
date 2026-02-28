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
        Schema::create('guest_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->integer('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->timestamp('expires_at');

            $table->boolean('isFreeze')->default(false);
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address1')->nullable();
            $table->string('billing_address2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_country')->nullable()->default('US');
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('shipping_first_name')->nullable();
            $table->string('shipping_last_name')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_address1')->nullable();
            $table->string('shipping_address2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_country')->nullable()->default('US');
            $table->string('extra1')->nullable();
            $table->string('extra2')->nullable();
            $table->json('applied_discounts')->nullable();


            $table->timestamps();
            
            $table->index(['uuid', 'location_id', 'brand_id']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_sessions');
    }
};
