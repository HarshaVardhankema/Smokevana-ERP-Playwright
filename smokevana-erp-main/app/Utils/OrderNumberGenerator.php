<?php

namespace App\Utils;

use App\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Generates standardized order numbers for different order types
 * 
 * Order Number Formats:
 * - WooCommerce Dropship: SOWDS25010001 (Sales Order WooCommerce DropShip)
 * - Vendor Portal Dropship: SOVDS25010001 (Sales Order Vendor DropShip)
 * - ERP In-House: SOERP25010001 (Sales Order ERP)
 */
class OrderNumberGenerator
{
    /**
     * Generate WooCommerce Dropship order number
     * Format: SOWDS{YYMM}{5-digit sequence}
     * Example: SOWDS25010001
     *
     * @param int $businessId
     * @return string
     */
    public function generateWooDropshipOrder($businessId)
    {
        return $this->generateOrderNumber($businessId, 'SOWDS', 'woo_dropship_order');
    }

    /**
     * Generate Vendor Portal Dropship order number
     * Format: SOVDS{YYMM}{5-digit sequence}
     * Example: SOVDS25010001
     *
     * @param int $businessId
     * @return string
     */
    public function generateVendorDropshipOrder($businessId)
    {
        return $this->generateOrderNumber($businessId, 'SOVDS', 'vendor_dropship_order');
    }

    /**
     * Generate ERP In-House order number
     * Format: SOERP{YYMM}{5-digit sequence}
     * Example: SOERP25010001
     *
     * @param int $businessId
     * @return string
     */
    public function generateErpOrder($businessId)
    {
        return $this->generateOrderNumber($businessId, 'SOERP', 'erp_order');
    }

    /**
     * Generate a standardized order number
     *
     * @param int $businessId
     * @param string $prefix
     * @param string $type
     * @return string
     */
    private function generateOrderNumber($businessId, $prefix, $type)
    {
        // Get current year and month
        $now = Carbon::now();
        $yearMonth = $now->format('ym'); // e.g., "2501" for January 2025

        // Get and increment the reference count atomically
        $refCount = $this->getNextReferenceCount($businessId, $type);

        // Format the sequence number with leading zeros (5 digits)
        $sequence = str_pad($refCount, 5, '0', STR_PAD_LEFT);

        return $prefix . $yearMonth . $sequence;
    }

    /**
     * Get the next reference count for a specific order type
     * Uses database locking to ensure uniqueness in concurrent scenarios
     *
     * @param int $businessId
     * @param string $type
     * @return int
     */
    private function getNextReferenceCount($businessId, $type)
    {
        return DB::transaction(function () use ($businessId, $type) {
            // Try to find existing counter
            $counter = DB::table('reference_counts')
                ->where('business_id', $businessId)
                ->where('ref_type', $type)
                ->lockForUpdate()
                ->first();

            if ($counter) {
                // Increment existing counter
                $newCount = $counter->ref_count + 1;
                DB::table('reference_counts')
                    ->where('id', $counter->id)
                    ->update([
                        'ref_count' => $newCount,
                        'updated_at' => now(),
                    ]);
                return $newCount;
            } else {
                // Create new counter starting at 1
                DB::table('reference_counts')->insert([
                    'business_id' => $businessId,
                    'ref_type' => $type,
                    'ref_count' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }
        });
    }

    /**
     * Generate child order invoice number from parent
     * Appends -C{index} to parent invoice number
     * Example: SO-2501-00001 -> SO-2501-00001-C1
     *
     * @param string $parentInvoiceNo
     * @param int $index
     * @return string
     */
    public function generateChildOrderNumber($parentInvoiceNo, $index)
    {
        return $parentInvoiceNo . '-C' . $index;
    }
}
