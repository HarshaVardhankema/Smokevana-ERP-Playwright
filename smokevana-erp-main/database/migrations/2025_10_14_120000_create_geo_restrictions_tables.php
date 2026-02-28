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
        Schema::create('geo_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rule_type', ['allow', 'disallow'])->default('disallow');
            $table->enum('scope', ['product', 'variation', 'category', 'brand']);
            $table->json('target_entities'); // Array of product/variation/category/brand IDs
            $table->json('locations'); // Array of location objects with type, value, and rule_type
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('scope');
            $table->index('is_active');
        });

        // Create a table for tracking rule changes
        Schema::create('geo_restriction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('geo_restriction_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // created, updated, deleted, enabled, disabled
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->foreign('geo_restriction_id')
                  ->references('id')
                  ->on('geo_restrictions')
                  ->onDelete('cascade');

            $table->index('geo_restriction_id');
            $table->index('action');
        });

        // Product-level state restrictions (simpler approach)
        Schema::create('product_state', function(Blueprint $table){
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('state', 10); // State code like 'CA', 'NY', etc.
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('state');
        });

        // Add state_check column to products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function(Blueprint $table){
                $table->enum('state_check', ['all', 'in', 'not_in'])->default('all')->after('enable_selling'); // all, in, not_in
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_restriction_logs');
        Schema::dropIfExists('geo_restrictions');
        Schema::dropIfExists('product_state');
        Schema::table('products', function(Blueprint $table){
            $table->dropColumn('state_check');
        });
    }
};

