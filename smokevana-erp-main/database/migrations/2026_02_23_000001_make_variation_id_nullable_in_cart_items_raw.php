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
        // Drop the foreign key constraint first
        DB::statement('ALTER TABLE cart_items DROP FOREIGN KEY cart_items_variation_id_foreign');
        
        // Make variation_id nullable
        DB::statement('ALTER TABLE cart_items MODIFY variation_id BIGINT UNSIGNED NULL');
        
        // Recreate the foreign key constraint (without ON DELETE SET NULL to avoid issues)
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT cart_items_variation_id_foreign FOREIGN KEY (variation_id) REFERENCES variations(id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the foreign key constraint
        DB::statement('ALTER TABLE cart_items DROP FOREIGN KEY cart_items_variation_id_foreign');
        
        // Make variation_id NOT NULL again
        DB::statement('ALTER TABLE cart_items MODIFY variation_id BIGINT UNSIGNED NOT NULL');
        
        // Recreate the original foreign key constraint
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT cart_items_variation_id_foreign FOREIGN KEY (variation_id) REFERENCES variations(id)');
    }
};
