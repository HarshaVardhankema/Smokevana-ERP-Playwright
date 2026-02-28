<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create gift_cards table for Amazon-style gift card functionality.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Unique gift card code (e.g., ABCD-EFGH-IJKL-MNOP)');
            $table->decimal('initial_amount', 15, 2)->comment('Original amount when purchased');
            $table->decimal('balance', 15, 2)->comment('Current remaining balance');
            $table->string('currency', 3)->default('USD')->comment('Currency code (USD, EUR, etc.)');
            $table->unsignedInteger('purchaser_contact_id')->nullable()->comment('Contact who purchased the gift card');
            $table->foreign('purchaser_contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->enum('type', ['egift', 'physical', 'printable'])->default('egift')->comment('Gift card type');
            $table->string('recipient_name')->nullable()->comment('Recipient name for gift card');
            $table->string('recipient_email')->nullable()->comment('Recipient email for gift card');
            $table->text('message')->nullable()->comment('Message from purchaser to recipient');
            $table->enum('status', ['pending_payment', 'active', 'redeemed', 'expired', 'cancelled'])->default('active')->comment('Gift card status');
            $table->timestamp('purchased_at')->nullable()->comment('When the gift card was purchased');
            $table->timestamp('redeemed_at')->nullable()->comment('When the gift card was fully redeemed');
            $table->unsignedInteger('redeemed_by_contact_id')->nullable()->comment('Contact who redeemed the gift card');
            $table->foreign('redeemed_by_contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->timestamp('expires_at')->nullable()->comment('Expiration date for the gift card');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('status');
            $table->index('purchaser_contact_id');
            $table->index('redeemed_by_contact_id');
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
        Schema::dropIfExists('gift_cards');
    }
};
