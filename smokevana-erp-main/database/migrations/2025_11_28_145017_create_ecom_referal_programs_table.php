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
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('enable_referal_program')->default(false);
            $table->unsignedInteger('referal_program_custom_discount_id')->nullable()->comment('Takes the value from custom_discounts');
            $table->boolean('referal_sent_to_both_sides')->default(false)->comment('If true, the referal program will be sent to both sides');

            // available for b2b and b2c 
            $table->boolean('referal_available_for_b2b')->default(false);
            $table->boolean('referal_available_for_b2c')->default(false);
            // if enabled for b2c then referal brand list 
            $table->text('referal_brand_list')->nullable()->comment('Comma separated list of brand ids');

            // expire days for referal program
            $table->integer('referal_program_expire_days')->default(30)->comment('Expire days for referal program');

        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('referal_code')->nullable()->comment('Referal code of the customer');
        });
        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->boolean('is_referal_program_discount')->default(false);
        });
        Schema::create('ecom_referal_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('discount_id')->nullable()->comment('Store at moment of creation');
            $table->string('coupon_code');
            // $table->integer('expire_days')->default(30)->comment('Expire days for referal program');
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('referred_by_customer_id')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->boolean('mail_sent_to_customer')->default(false);
            $table->boolean('mail_sent_to_referred_by_customer')->default(false);
            
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
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('enable_referal_program');
            $table->dropColumn('referal_program_custom_discount_id');
            $table->dropColumn('referal_sent_to_both_sides');
            $table->dropColumn('referal_available_for_b2b');
            $table->dropColumn('referal_available_for_b2c');
            $table->dropColumn('referal_brand_list');
            $table->dropColumn('referal_program_expire_days');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('referal_code');
        });
        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->dropColumn('is_referal_program_discount');
        });
        Schema::dropIfExists('ecom_referal_programs');
    }
};
