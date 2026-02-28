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
        if (Schema::hasTable('stock_alerts')) {
            // Get the foreign key constraint name
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'stock_alerts' 
                AND COLUMN_NAME = 'contact_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            // Drop the foreign key constraint if it exists
            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                DB::statement("ALTER TABLE stock_alerts DROP FOREIGN KEY `{$constraintName}`");
            }
            
            // Make contact_id nullable using raw SQL (matching the original INT UNSIGNED type)
            DB::statement("ALTER TABLE stock_alerts MODIFY COLUMN contact_id INT UNSIGNED NULL");
            
            // Re-add the foreign key constraint (nullable foreign keys are allowed)
            // Use try-catch in case constraint already exists
            try {
                // Try to add with a specific name first
                $constraintAdded = DB::statement("
                    ALTER TABLE stock_alerts 
                    ADD CONSTRAINT stock_alerts_contact_id_foreign 
                    FOREIGN KEY (contact_id) REFERENCES contacts(id) 
                    ON DELETE CASCADE
                ");
            } catch (\Exception $e) {
                // If constraint already exists or name conflict, try without specifying name
                // MySQL will auto-generate a unique name
                try {
                    DB::statement("
                        ALTER TABLE stock_alerts 
                        ADD FOREIGN KEY (contact_id) REFERENCES contacts(id) 
                        ON DELETE CASCADE
                    ");
                } catch (\Exception $e2) {
                    // Constraint might already exist, that's okay
                    // Log but don't fail
                    Log::info('Foreign key constraint might already exist for stock_alerts.contact_id');
                }
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
        if (Schema::hasTable('stock_alerts')) {
            // Drop the foreign key constraint if it exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'stock_alerts' 
                AND COLUMN_NAME = 'contact_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE stock_alerts DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Check if there are any NULL values in contact_id
            $nullCount = DB::table('stock_alerts')->whereNull('contact_id')->count();
            
            if ($nullCount > 0) {
                // Delete rows with NULL contact_id since we can't make it NOT NULL with NULL values
                DB::table('stock_alerts')->whereNull('contact_id')->delete();
            }
            
            // Make contact_id NOT nullable again using raw SQL (matching the original INT UNSIGNED type)
            try {
                DB::statement("ALTER TABLE stock_alerts MODIFY COLUMN contact_id INT UNSIGNED NOT NULL");
            } catch (\Exception $e) {
                // If it fails, log and continue
                Log::warning('Failed to make contact_id NOT NULL in stock_alerts: ' . $e->getMessage());
            }
            
            // Re-add the foreign key constraint with error handling
            try {
                // Check if constraint already exists
                $existingConstraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'stock_alerts' 
                    AND COLUMN_NAME = 'contact_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                    AND CONSTRAINT_NAME = 'stock_alerts_contact_id_foreign'
                ");
                
                if (empty($existingConstraints)) {
                    DB::statement("
                        ALTER TABLE stock_alerts 
                        ADD CONSTRAINT stock_alerts_contact_id_foreign 
                        FOREIGN KEY (contact_id) REFERENCES contacts(id) 
                        ON DELETE CASCADE
                    ");
                }
            } catch (\Exception $e) {
                // Constraint might already exist or there's a data issue
                Log::warning('Failed to add foreign key constraint for stock_alerts.contact_id: ' . $e->getMessage());
            }
        }
    }
};
