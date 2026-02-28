<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates P&L Transactions table for manual income and expense entries
     */
    public function up()
    {
        Schema::create('pl_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('reference_number', 50);
            $table->enum('transaction_type', ['income', 'expense']);
            $table->string('category', 50)->nullable(); // Category within income/expense
            $table->date('transaction_date');
            $table->decimal('amount', 22, 4);
            $table->text('description')->nullable();
            
            // Account references
            $table->unsignedBigInteger('account_id'); // Income or Expense account
            $table->unsignedBigInteger('payment_account_id'); // Cash/Bank account for payment
            
            // Customer/Vendor integration
            $table->unsignedInteger('contact_id')->nullable(); // Customer for income, Vendor for expense
            
            // Payment details
            $table->string('payment_method')->nullable(); // cash, check, bank_transfer, credit_card
            $table->string('payment_reference')->nullable(); // Check number, transaction ID
            
            // Status and tracking
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            
            // Additional info
            $table->string('invoice_number')->nullable(); // Related invoice for income
            $table->string('bill_number')->nullable(); // Related bill for expense
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            
            // Audit fields
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('payment_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['business_id', 'transaction_type']);
            $table->index(['business_id', 'transaction_date']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'contact_id']);
            $table->unique(['business_id', 'reference_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pl_transactions');
    }
};




