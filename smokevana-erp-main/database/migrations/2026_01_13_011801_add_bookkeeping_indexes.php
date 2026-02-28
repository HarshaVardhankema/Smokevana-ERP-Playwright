<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes to bookkeeping tables for improved query performance
     *
     * @return void
     */
    public function up()
    {
        // Indexes for journal_entries table
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index(['business_id', 'entry_date'], 'idx_journal_entries_business_date');
            $table->index(['business_id', 'status'], 'idx_journal_entries_business_status');
            $table->index('entry_number', 'idx_journal_entries_entry_number');
            $table->index('entry_date', 'idx_journal_entries_entry_date');
        });
        
        // Indexes for journal_entry_lines table
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->index(['journal_entry_id', 'account_id'], 'idx_journal_lines_entry_account');
            $table->index('account_id', 'idx_journal_lines_account');
            $table->index('journal_entry_id', 'idx_journal_lines_entry');
        });
        
        // Indexes for chart_of_accounts table
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->index(['business_id', 'account_type'], 'idx_chart_accounts_business_type');
            $table->index(['business_id', 'is_active'], 'idx_chart_accounts_business_active');
            $table->index('account_code', 'idx_chart_accounts_code');
            $table->index('parent_id', 'idx_chart_accounts_parent');
        });
        
        // Indexes for bank_deposits table
        Schema::table('bank_deposits', function (Blueprint $table) {
            $table->index(['business_id', 'status'], 'idx_bank_deposits_business_status');
            $table->index('deposit_date', 'idx_bank_deposits_date');
        });
        
        // Indexes for business_liabilities table
        Schema::table('business_liabilities', function (Blueprint $table) {
            $table->index(['business_id', 'status'], 'idx_liabilities_business_status');
            $table->index('due_date', 'idx_liabilities_due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('idx_journal_entries_business_date');
            $table->dropIndex('idx_journal_entries_business_status');
            $table->dropIndex('idx_journal_entries_entry_number');
            $table->dropIndex('idx_journal_entries_entry_date');
        });
        
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex('idx_journal_lines_entry_account');
            $table->dropIndex('idx_journal_lines_account');
            $table->dropIndex('idx_journal_lines_entry');
        });
        
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex('idx_chart_accounts_business_type');
            $table->dropIndex('idx_chart_accounts_business_active');
            $table->dropIndex('idx_chart_accounts_code');
            $table->dropIndex('idx_chart_accounts_parent');
        });
        
        Schema::table('bank_deposits', function (Blueprint $table) {
            $table->dropIndex('idx_bank_deposits_business_status');
            $table->dropIndex('idx_bank_deposits_date');
        });
        
        Schema::table('business_liabilities', function (Blueprint $table) {
            $table->dropIndex('idx_liabilities_business_status');
            $table->dropIndex('idx_liabilities_due_date');
        });
    }
};
