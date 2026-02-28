<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('vendor_product_requests')) {
            return;
        }

        $addReviewedBy = !Schema::hasColumn('vendor_product_requests', 'reviewed_by');
        $addReviewedAt = !Schema::hasColumn('vendor_product_requests', 'reviewed_at');
        $addAdminNotes = !Schema::hasColumn('vendor_product_requests', 'admin_notes');

        if ($addReviewedBy || $addReviewedAt || $addAdminNotes) {
            Schema::table('vendor_product_requests', function (Blueprint $table) use ($addReviewedBy, $addReviewedAt, $addAdminNotes) {
                if ($addReviewedBy) {
                    $table->unsignedInteger('reviewed_by')->nullable()->after('approved_by');
                    $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
                }
                if ($addReviewedAt) {
                    $table->timestamp('reviewed_at')->nullable()->after('approved_at');
                }
                if ($addAdminNotes) {
                    $table->text('admin_notes')->nullable()->after('notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('vendor_product_requests')) {
            return;
        }

        Schema::table('vendor_product_requests', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_product_requests', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('vendor_product_requests', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('vendor_product_requests', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
};
