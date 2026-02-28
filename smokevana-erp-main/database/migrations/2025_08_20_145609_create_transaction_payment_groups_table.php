<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('transaction_payment_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable(); // sell, purchase, sales_return, purchase_return
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('payment_method_id');
            $table->decimal('amount', 22, 4);
            $table->unsignedInteger('group_ref_no');
            $table->unsignedInteger('contact_id')->nullable();
            $table->timestamps();
        });
        DB::statement("INSERT INTO reference_counts (`ref_type`, `ref_count`, `business_id`, `created_at`, `updated_at`) VALUES ('transaction_payment_groups_count', 1, 1, '2025-08-09 05:42:19', '2025-08-09 05:42:19')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_payment_groups');
        
        DB::statement("DELETE FROM reference_counts WHERE ref_type = 'transaction_payment_groups_count' AND business_id = 1");
    }
};
