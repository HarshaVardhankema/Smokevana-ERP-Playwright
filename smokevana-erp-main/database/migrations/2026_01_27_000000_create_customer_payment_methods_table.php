<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payment_methods', function (Blueprint $table) {
            $table->integer('id');
            $table->unsignedBigInteger('user_id');
            $table->string('cardholder_name')->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('last4', 4);
            $table->unsignedTinyInteger('exp_month');
            $table->unsignedSmallInteger('exp_year');
            $table->string('billing_zip', 20)->nullable();
            $table->string('token')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payment_methods');
    }
};

