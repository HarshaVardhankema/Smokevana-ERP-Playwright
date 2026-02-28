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
        // Create pivot table for many-to-many relationship
        Schema::create('brand_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('brand_id');
            $table->unsignedInteger('category_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['brand_id', 'category_id']);
            
            // Indexes for better performance
            $table->index('brand_id');
            $table->index('category_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('billing_first_name')->nullable();
            $table->string('billing_last_name')->nullable();
            $table->string('billing_company')->nullable();
            $table->string('billing_address1')->nullable();
            $table->string('billing_address2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_zip')->nullable();

            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();

            $table->string('unique_public_url')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_category');
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('billing_first_name');
            $table->dropColumn('billing_last_name');
            $table->dropColumn('billing_company');
            $table->dropColumn('billing_address1');
            $table->dropColumn('billing_address2');
            $table->dropColumn('billing_city');
            $table->dropColumn('billing_state');
            $table->dropColumn('billing_country');
            $table->dropColumn('billing_zip');
            $table->dropColumn('billing_phone');
            $table->dropColumn('billing_email');

            $table->dropColumn('unique_public_url');
        });
    }
};
