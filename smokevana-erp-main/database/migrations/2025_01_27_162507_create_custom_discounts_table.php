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
        Schema::create('custom_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('couponName')->nullable();
            $table->integer('setPriority')->nullable()->default(0);
            
            $table->string('logo')->nullable();
            $table->string('couponCode')->nullable();
            $table->date('applyDate');
            $table->date('endDate');
            $table->boolean('isDisabled')->default(false);
            $table->boolean('isPrimary')->default(false);

            $table->enum('discountType', [
                'productAdjustment',
                'cartAdustment',
                'freeShipping',
                'buyXgetX',
                'buyXgetY',
            ])->nullable();
            $table->json('filter')->nullable();
            $table->enum('discount', [
                'percentageDiscount',
                'fixedDiscount',
                'fixedPricePerItem',
                'free',
            ])->nullable();
            $table->integer('discountValue')->default(0);
            $table->integer('minBuyQty')->nullable();
            $table->integer('maxBuyQty')->nullable();
            $table->integer('freeQty')->nullable();
            $table->integer('useLimit')->nullable();
            $table->json('getYproductId')->nullable();
            $table->boolean('allRuleMatch')->nullable();
            $table->json('rulesOnCart')->nullable();
            $table->json('rulesOnPurchaseHistory')->nullable();
            $table->json('rulesOnShipping')->nullable();
            $table->json('rulesOnCustomer')->nullable();
            $table->boolean('isLifeCycleCoupon')->default(false);
            $table->json('couponLife')->nullable();
            $table->json('custom_meta')->nullable();
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
        Schema::dropIfExists('custom_discounts');
    }
};
