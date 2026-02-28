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
        Schema::create('delivery_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('multiple_address_customer')->onDelete('cascade');
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Delivery Times - JSON field to store days with start/stop times
            // Format: {"monday": {"start": "09:00", "stop": "17:00"}, "tuesday": {...}, ...}
            $table->json('delivery_times')->nullable();
            
            // Smokevana Day preferences
            $table->string('preferred_day_1')->nullable(); // Monday, Tuesday, etc.
            $table->string('preferred_day_2')->nullable(); // Monday, Tuesday, etc. or null
            $table->boolean('make_default_delivery_option')->default(false);
            
            // Delivery Instructions
            $table->enum('drop_off_location', ['Front Desk', 'Loading Dock', 'Mail Room', 'In-Suite Reception', 'Front Door', 'No Preference'])->default('No Preference');
            $table->string('security_code')->nullable();
            $table->string('call_box_name_or_number')->nullable();
            $table->text('additional_info')->nullable();
            
            // Observed Holidays - JSON array of holiday names
            $table->json('observed_holidays')->nullable();
            
            // Pallet Preference - JSON field for future expansion
            $table->json('pallet_preference')->nullable();
            
            $table->timestamps();
            
            // Index for faster lookups
            $table->index('address_id');
            $table->index('contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_preferences');
    }
};
