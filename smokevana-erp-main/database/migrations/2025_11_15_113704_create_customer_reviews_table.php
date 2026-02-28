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
        if (!Schema::hasTable('customer_reviews')) {
            Schema::create('customer_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('contact_id');
                $table->unsignedInteger('product_id');
                $table->unsignedInteger('transaction_id'); // Required: reviews can only be created after invoiced transaction
                $table->unsignedInteger('business_id');
                $table->text('description');
                $table->tinyInteger('rating')->nullable()->comment('1-5 star rating');
                $table->integer('likes')->default(0); 
                $table->boolean('is_active')->default(1);
                $table->boolean('is_deleted')->default(0);
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamps();
                
                // Indexes for better query performance
                $table->index(['product_id', 'is_active']);
                $table->index(['contact_id', 'product_id']);
                $table->index(['transaction_id', 'product_id']); // Index for transaction validation
                $table->index('likes'); // Index for sorting by likes
            });
        } else {
            // If table already exists, add missing columns and constraints
            Schema::table('customer_reviews', function (Blueprint $table) {
                // Add rating column if it doesn't exist
                if (!Schema::hasColumn('customer_reviews', 'rating')) {
                    $table->tinyInteger('rating')->nullable()->after('description')->comment('1-5 star rating');
                }
            });
            
            // Add foreign key and index for transaction_id if they don't exist
            // Note: Making transaction_id required should be done carefully if existing data has nulls
            // For now, we'll add the constraint but keep it nullable if table already exists
            try {
                Schema::table('customer_reviews', function (Blueprint $table) {
                    // Check if foreign key exists before adding
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                         FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'customer_reviews' 
                         AND COLUMN_NAME = 'transaction_id' 
                         AND REFERENCED_TABLE_NAME = 'transactions'"
                    );
                    
                    if (empty($foreignKeyExists)) {
                        $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
                    }
                });
            } catch (\Exception $e) {
                // Foreign key might already exist or transaction_id column might not exist
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_reviews');
    }
};

