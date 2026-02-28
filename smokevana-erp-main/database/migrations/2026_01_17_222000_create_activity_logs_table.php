<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tableName = config('activitylog.table_name', 'activity_logs');

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('causer_id')->nullable();
                $table->string('causer_type')->nullable();
                $table->unsignedInteger('business_id')->nullable()->index();
                $table->json('properties')->nullable();
                $table->string('event')->nullable();
                $table->uuid('batch_uuid')->nullable();
                $table->timestamps();

                $table->index('log_name');
                $table->index(['subject_id', 'subject_type']);
                $table->index(['causer_id', 'causer_type']);
            });
        }
    }

    public function down()
    {
        $tableName = config('activitylog.table_name', 'activity_logs');

        Schema::dropIfExists($tableName);
    }
};
