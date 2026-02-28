<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Basic lead information
            $table->string('contact_name')->nullable()->after('store_name');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('company_name')->nullable()->after('contact_phone');
            $table->string('website')->nullable()->after('company_name');
            
            // Lead source tracking
            $table->enum('lead_source', [
                'admin_panel', 
                'mobile_app', 
                'website', 
                'referral', 
                'cold_call', 
                'email_campaign', 
                'social_media', 
                'trade_show', 
                'other'
            ])->default('admin_panel')->after('contact_phone');
            
            // Assignment and ownership
            $table->unsignedInteger('assigned_to')->nullable()->after('created_by');
            $table->unsignedInteger('sales_rep_id')->nullable()->after('assigned_to');
            
            // Enhanced status and priority
            $table->enum('lead_status', [
                'new', 
                'in_progress', 
                'follow_up', 
                'converted', 
                'lost', 
                'qualified', 
                'unqualified'
            ])->default('new')->after('status');
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('lead_status');
            
            // Sales funnel stage
            $table->enum('funnel_stage', [
                'initial_contact',
                'qualification',
                'proposal',
                'negotiation',
                'closed_won',
                'closed_lost',
                'nurturing'
            ])->default('initial_contact')->after('priority');
            
            // Follow-up and contact tracking
            $table->datetime('next_follow_up_date')->nullable()->after('funnel_stage');
            $table->datetime('last_contact_date')->nullable()->after('next_follow_up_date');
            $table->text('notes')->nullable()->after('last_contact_date');
            $table->text('internal_notes')->nullable()->after('notes');
            
            // Visit proof and location tracking
            $table->string('visit_proof_url')->nullable()->after('internal_notes');
            $table->decimal('latitude', 10, 8)->nullable()->after('visit_proof_url');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('location_accuracy')->nullable()->after('longitude');
            
            // Value and conversion tracking
            $table->decimal('estimated_value', 15, 2)->nullable()->after('location_accuracy');
            $table->decimal('actual_value', 15, 2)->nullable()->after('estimated_value');
            $table->string('currency', 3)->default('USD')->after('actual_value');
            
            // Conversion tracking
            $table->datetime('converted_at')->nullable()->after('currency');
            $table->unsignedInteger('converted_to_contact_id')->nullable()->after('converted_at');
            $table->unsignedInteger('converted_to_customer_id')->nullable()->after('converted_to_contact_id');
            
            // Lead scoring and rating
            $table->integer('lead_score')->default(0)->after('converted_to_customer_id');
            $table->integer('rating')->nullable()->after('lead_score'); // 1-5 star rating
            
            // Tags and categorization
            $table->json('tags')->nullable()->after('rating');
            $table->string('industry')->nullable()->after('tags');
            $table->string('company_size')->nullable()->after('industry');
            
            // Communication preferences
            $table->enum('preferred_contact_method', ['email', 'phone', 'sms', 'whatsapp'])->default('phone')->after('company_size');
            $table->time('best_contact_time_start')->nullable()->after('preferred_contact_method');
            $table->time('best_contact_time_end')->nullable()->after('best_contact_time_start');
            
            // Marketing and campaign tracking
            $table->string('utm_source')->nullable()->after('best_contact_time_end');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('referral_source')->nullable()->after('utm_campaign');
            
            // Additional metadata
            $table->json('custom_fields')->nullable()->after('referral_source');
            $table->boolean('is_qualified')->default(false)->after('custom_fields');
            $table->boolean('is_hot_lead')->default(false)->after('is_qualified');
            $table->boolean('requires_immediate_attention')->default(false)->after('is_hot_lead');
            
            // Foreign key constraints
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('sales_rep_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('converted_to_contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('converted_to_customer_id')->references('id')->on('contacts')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['business_id', 'lead_status']);
            $table->index(['business_id', 'assigned_to']);
            $table->index(['business_id', 'sales_rep_id']);
            $table->index(['business_id', 'lead_source']);
            $table->index(['business_id', 'priority']);
            $table->index(['business_id', 'funnel_stage']);
            $table->index(['next_follow_up_date']);
            $table->index(['last_contact_date']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraints first (the ones added in this migration)
        // Check if they exist before trying to drop them
        $foreignKeyColumns = ['assigned_to', 'sales_rep_id', 'converted_to_contact_id', 'converted_to_customer_id'];
        
        foreach ($foreignKeyColumns as $column) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'leads' 
                AND COLUMN_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$column]);
            
            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE leads DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                    Log::info("Foreign key constraint {$constraintName} might not exist: " . $e->getMessage());
                }
            }
        }
        
        // Drop the foreign key on business_id temporarily (from original table)
        // so we can drop the composite indexes that use business_id
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'leads' 
            AND COLUMN_NAME = 'business_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $businessIdConstraintName = null;
        if (!empty($foreignKeys)) {
            $businessIdConstraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE leads DROP FOREIGN KEY `{$businessIdConstraintName}`");
        }
        
        // Drop indexes (check if they exist first)
        $indexesToDrop = [
            ['business_id', 'lead_status'],
            ['business_id', 'assigned_to'],
            ['business_id', 'sales_rep_id'],
            ['business_id', 'lead_source'],
            ['business_id', 'priority'],
            ['business_id', 'funnel_stage'],
            ['next_follow_up_date'],
            ['last_contact_date'],
            ['created_at'],
            ['updated_at']
        ];
        
        foreach ($indexesToDrop as $indexColumns) {
            try {
                Schema::table('leads', function (Blueprint $table) use ($indexColumns) {
                    $table->dropIndex($indexColumns);
                });
            } catch (\Exception $e) {
                // Index might not exist, continue
                Log::info("Index might not exist: " . implode('_', $indexColumns) . " - " . $e->getMessage());
            }
        }
        
        // Drop all the new columns
        try {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn([
                    'contact_name', 'contact_email', 'contact_phone', 'company_name', 'website',
                    'lead_source', 'assigned_to', 'sales_rep_id', 'lead_status', 'priority',
                    'funnel_stage', 'next_follow_up_date', 'last_contact_date', 'notes',
                    'internal_notes', 'visit_proof_url', 'latitude', 'longitude', 'location_accuracy',
                    'estimated_value', 'actual_value', 'currency', 'converted_at',
                    'converted_to_contact_id', 'converted_to_customer_id', 'lead_score', 'rating',
                    'tags', 'industry', 'company_size', 'preferred_contact_method',
                    'best_contact_time_start', 'best_contact_time_end', 'utm_source', 'utm_medium',
                    'utm_campaign', 'referral_source', 'custom_fields', 'is_qualified',
                    'is_hot_lead', 'requires_immediate_attention'
                ]);
            });
        } catch (\Exception $e) {
            // Some columns might not exist, log and continue
            Log::warning('Error dropping columns from leads table: ' . $e->getMessage());
        }
        
        // Re-add the business_id foreign key constraint
        if ($businessIdConstraintName) {
            try {
                // Check if constraint already exists (might have been recreated)
                $existingConstraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'leads' 
                    AND COLUMN_NAME = 'business_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                    AND CONSTRAINT_NAME = '{$businessIdConstraintName}'
                ");
                
                if (empty($existingConstraints)) {
                    DB::statement("
                        ALTER TABLE leads 
                        ADD CONSTRAINT `{$businessIdConstraintName}` 
                        FOREIGN KEY (business_id) REFERENCES business(id) 
                        ON DELETE CASCADE
                    ");
                }
            } catch (\Exception $e) {
                // If constraint already exists or there's an error, try without specifying name
                try {
                    DB::statement("
                        ALTER TABLE leads 
                        ADD FOREIGN KEY (business_id) REFERENCES business(id) 
                        ON DELETE CASCADE
                    ");
                } catch (\Exception $e2) {
                    // Log but don't fail - constraint might already exist
                    Log::warning('Failed to re-add business_id foreign key constraint in leads table: ' . $e2->getMessage());
                }
            }
        }
    }
};
