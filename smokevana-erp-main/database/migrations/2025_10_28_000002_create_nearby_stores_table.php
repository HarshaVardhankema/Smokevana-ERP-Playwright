<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNearbyStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('nearby_stores');
        Schema::create('nearby_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('store_name');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->unsignedInteger('discovered_by_sales_rep_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('converted_to_lead_id')->nullable();
            $table->boolean('is_converted')->default(false);
            $table->timestamp('discovered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->foreign('discovered_by_sales_rep_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('converted_to_lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('set null');

            // Indexes
            $table->index('business_id');
            $table->index('is_converted');
            $table->index(['latitude', 'longitude']);
            $table->index('discovered_by_sales_rep_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nearby_stores');
    }
}

