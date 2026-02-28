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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('request_type');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->json('variation_ids')->nullable(); // Store multiple variation IDs as JSON array
            $table->text('description')->nullable();
            $table->text('attachments')->nullable(); // Store image paths as JSON
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            // Note: Cannot add foreign key on JSON column, validation handled in application
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};
