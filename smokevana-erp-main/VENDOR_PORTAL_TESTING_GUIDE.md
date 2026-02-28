# Vendor Portal End-to-End Testing Guide

**Document Version:** 1.0  
**Last Updated:** January 11, 2026  
**System:** Go Hunter ERP - Dropshipping & Vendor Portal Module

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Test Credentials](#test-credentials)
4. [Part 1: Setting Up Test Data](#part-1-setting-up-test-data)
5. [Part 2: Creating a Test Order](#part-2-creating-a-test-order)
6. [Part 3: Order Splitting Verification](#part-3-order-splitting-verification)
7. [Part 4: Vendor Portal Testing](#part-4-vendor-portal-testing)
8. [Part 5: ERP Post-Fulfillment Validation](#part-5-erp-post-fulfillment-validation)
9. [Expected Results Summary](#expected-results-summary)
10. [Troubleshooting](#troubleshooting)

---

## Overview

This document provides step-by-step instructions for testing the complete Vendor Portal flow, including:
- Product-to-vendor mapping
- Order creation with mixed products (ERP + Dropship)
- Automatic order splitting
- Vendor portal fulfillment workflow
- Status synchronization back to ERP

### System URLs

| Component | URL |
|-----------|-----|
| **ERP Admin Login** | `https://testerp.gohunterdistro.com/login` |
| **Vendor Portal Login** | `https://testerp.gohunterdistro.com/vendorlogin` |
| **Vendor Portal Dashboard** | `https://testerp.gohunterdistro.com/vendor-portal` |

---

## Prerequisites

Before testing, ensure:
- [ ] Access to ERP Admin account
- [ ] Access to Vendor Portal account
- [ ] At least one product tagged as "dropshipped" with vendor assignment
- [ ] At least one product without vendor assignment (ERP/in-house)

---

## Test Credentials

### Vendor Portal Test Account

| Field | Value |
|-------|-------|
| **Login URL** | `https://testerp.gohunterdistro.com/vendorlogin` |
| **Email** | `testvendor@gohunter.com` |
| **Password** | `Vendor@123` |
| **Vendor Type** | ERP Dropship Vendor |
| **Vendor ID** | 3 |

### Creating Additional Test Vendors

Run via SSH/Terminal:
```bash
cd /home/u666321275/domains/testerp.gohunterdistro.com/public_html
php artisan vendor:create-test --email=newvendor@test.com --password=Test@123 --name="New Test Vendor"
```

---

## Part 1: Setting Up Test Data

### Step 1.1: Tag a Product to a Vendor (Admin UI Method)

1. **Login to ERP Admin:** `https://testerp.gohunterdistro.com/login`
2. **Navigate to:** `Dropship > Vendors`
3. **Click on:** The vendor you want to assign products to
4. **Go to:** "Products" tab
5. **Click:** "Add Product" button
6. **Select:** The product from the dropdown
7. **Fill in:**
   - Vendor Cost Price (what you pay the vendor)
   - Dropship Selling Price (what customer pays)
   - Vendor SKU (optional)
   - Status: Active
8. **Save** the mapping

### Step 1.2: Mark Product as Dropshipped

1. **Navigate to:** `Products > List Products`
2. **Edit** the product
3. **Set** `Product Source Type` to "Dropshipped"
4. **Save** changes

### Step 1.3: Database Method (Alternative)

For bulk setup via command line:

```bash
php artisan tinker
```

```php
// Map product to vendor
DB::table('products_wp_vendors_table_pivot')->insert([
    'wp_vendor_id' => 3,  // Vendor ID
    'product_id' => 1,     // Product ID
    'vendor_cost_price' => 25.00,
    'dropship_selling_price' => 35.00,
    'status' => 'active',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Mark product as dropshipped
App\Product::where('id', 1)->update(['product_source_type' => 'dropshipped']);
```

### Current Test Data Configuration

| Product ID | Product Name | Vendor ID | Vendor Name | Type |
|------------|-------------|-----------|-------------|------|
| 1 | MR FOG NOVA LIMITED EDITION... | 3 | Test Dropship Vendor | erp_dropship |
| 2 | MR FOG NOVA STEEZY SERIES... | 3 | Test Dropship Vendor | erp_dropship |
| 3+ | Other products | None | - | in_house (ERP) |

---

## Part 2: Creating a Test Order

### Step 2.1: Create Order via ERP

1. **Login to ERP Admin**
2. **Navigate to:** `Sell > Add Sale` or `POS`
3. **Select Customer:** Choose or create a test customer
4. **Add Products:**
   - Add 1x **Dropship Product** (Product ID 1 or 2 - mapped to vendor)
   - Add 1x **ERP Product** (Product ID 3+ - no vendor mapping)
5. **Complete Order:**
   - Set payment status
   - Save as "Sales Order" (not invoice)
6. **Note the Order Number:** e.g., `SO202601XXXX`

### Step 2.2: Order Types Reference

| Order Type | Code Prefix | Description |
|------------|-------------|-------------|
| `sales_order` | SO | Parent order (before split) |
| `erp_sales_order` | SOERP | ERP in-house fulfillment |
| `erp_dropship_order` | SOVDS | Vendor Portal fulfillment |
| `wp_sales_order` | SOWDS | WooCommerce vendor fulfillment |

---

## Part 3: Order Splitting Verification

### Step 3.1: Trigger Order Splitting

Orders are split during the **Preprocessing** stage:

1. **Navigate to:** `Order Fulfillment > Preprocessing` tab
2. **Find** your test order
3. **Click:** "Start Processing" or similar action
4. The system will automatically:
   - Detect dropship products
   - Create child orders by vendor
   - Create `DropshipOrderTracking` records

### Step 3.2: Verify Split Orders

1. **Navigate to:** `Order Fulfillment > Pending/Processing` tabs
2. **Expected Results:**

| Original Order | Split Child Orders |
|----------------|-------------------|
| SO202601XXXX (Parent) | → |
| | SOERP202601XXXX (ERP products) |
| | SOVDS202601XXXX (Dropship products - Vendor ID 3) |

### Step 3.3: Database Verification

Via terminal:
```bash
php artisan tinker
```

```php
// Find parent order
$parent = App\Transaction::where('invoice_no', 'SO202601XXXX')->first();

// Check child orders
$children = App\Transaction::where('transfer_parent_id', $parent->id)->get();
foreach($children as $child) {
    echo "Child: {$child->invoice_no}, Type: {$child->type}\n";
}

// Check dropship tracking records
$tracking = App\Models\DropshipOrderTracking::where('parent_transaction_id', $parent->id)->get();
foreach($tracking as $t) {
    echo "Tracking ID: {$t->id}, Vendor: {$t->wp_vendor_id}, Status: {$t->fulfillment_status}\n";
}
```

### Step 3.4: Expected Database Records

**transactions table:**
```
| id  | invoice_no      | type              | transfer_parent_id |
|-----|-----------------|-------------------|-------------------|
| 100 | SO202601XXXX    | sales_order       | NULL              |
| 101 | SOERP202601XXX  | erp_sales_order   | 100               |
| 102 | SOVDS202601XXX  | erp_dropship_order| 100               |
```

**dropship_order_tracking table:**
```
| id | parent_transaction_id | transaction_id | wp_vendor_id | fulfillment_status |
|----|----------------------|----------------|--------------|-------------------|
| 1  | 100                  | 102            | 3            | pending           |
```

---

## Part 4: Vendor Portal Testing

### Step 4.1: Login to Vendor Portal

1. **Open:** `https://testerp.gohunterdistro.com/vendorlogin`
2. **Enter:**
   - Email: `testvendor@gohunter.com`
   - Password: `Vendor@123`
3. **Click:** "Sign In to Portal"

### Step 4.2: Dashboard Verification

After login, verify the dashboard shows:
- [ ] Pending Orders count
- [ ] Active Products count
- [ ] Monthly Revenue stats
- [ ] Orders Needing Action section

### Step 4.3: View Vendor Orders

1. **Click:** "My Orders" in sidebar
2. **Verify:** The dropship order appears (SOVDS202601XXX)
3. **Check columns:**
   - Date
   - Order #
   - Customer name
   - Items count
   - Total amount
   - Status (should be "Pending" or "Vendor Notified")

### Step 4.4: Accept Order

1. **Find** the pending order
2. **Click:** Green checkmark (✓) Accept button
3. **Expected Result:**
   - Status changes to "Vendor Accepted"
   - Toast notification: "Order accepted successfully!"

### Step 4.5: Add Tracking Information

1. **Click:** Blue truck icon (🚚) or "Add Tracking" button
2. **Fill in:**
   - Tracking Number: `TEST123456789`
   - Carrier: Select (USPS/UPS/FedEx/DHL)
   - Tracking URL: (optional)
3. **Click:** "Ship Order"
4. **Expected Result:**
   - Status changes to "Shipped"
   - Toast notification: "Order marked as shipped!"

### Step 4.6: View Order Details

1. **Click:** Eye icon (👁) to view order details
2. **Verify:**
   - [ ] Order information is correct
   - [ ] Customer shipping address displayed
   - [ ] Product items listed correctly
   - [ ] Timeline shows status progression
   - [ ] Tracking information displayed

### Step 4.7: Vendor Portal Test Checklist

| Test Case | Expected Result | Pass/Fail |
|-----------|-----------------|-----------|
| Login with valid credentials | Dashboard loads | ☐ |
| View dashboard stats | Stats display with correct counts | ☐ |
| View orders list | Orders table loads, shows vendor's orders | ☐ |
| Accept pending order | Status → Vendor Accepted | ☐ |
| Add tracking number | Status → Shipped, tracking saved | ☐ |
| View order details | All order info displayed correctly | ☐ |
| View products | Assigned products shown | ☐ |
| View earnings | Earnings data displayed | ☐ |
| Update profile | Profile saves successfully | ☐ |
| Change password | Password updates | ☐ |
| Logout | Redirected to login page | ☐ |

---

## Part 5: ERP Post-Fulfillment Validation

### Step 5.1: Verify Status in ERP

1. **Login to ERP Admin**
2. **Navigate to:** `Order Fulfillment > Processing` or `Picked` tabs
3. **Find** the original order or dropship child order
4. **Verify:**
   - Dropship order status reflects vendor action
   - Tracking number is visible
   - Order cannot be edited (if restriction applies)

### Step 5.2: Check Dropship Orders Page

1. **Navigate to:** `Dropship > Orders`
2. **Find** the order by invoice number
3. **Verify columns:**
   - Fulfillment Status: "Shipped"
   - Tracking Number: Shows entered tracking
   - Carrier: Shows selected carrier

### Step 5.3: Verify Edit Restrictions

1. **Try to edit** the dropship order
2. **Expected:** Edit should be restricted or show warning
3. **Reason:** Vendor-fulfilled orders shouldn't be modified in ERP

### Step 5.4: Database Verification

```php
// Check updated tracking record
$tracking = App\Models\DropshipOrderTracking::where('transaction_id', 102)->first();
echo "Status: {$tracking->fulfillment_status}\n";
echo "Tracking: {$tracking->tracking_number}\n";
echo "Carrier: {$tracking->carrier}\n";
echo "Shipped At: {$tracking->shipped_at}\n";
```

---

## Expected Results Summary

### Order Flow Summary

```
┌─────────────────────────────────────────────────────────────────┐
│                      ORDER CREATION                              │
│  Customer creates order with mixed products (ERP + Dropship)    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PREPROCESSING (ERP)                           │
│  • Detect product types                                          │
│  • Split order by vendor                                         │
│  • Create child orders (SOERP, SOVDS)                           │
│  • Create DropshipOrderTracking records                         │
└─────────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              ▼                               ▼
┌─────────────────────────┐     ┌─────────────────────────┐
│    ERP FULFILLMENT      │     │   VENDOR PORTAL         │
│  • SOERP orders         │     │   FULFILLMENT           │
│  • Picked by warehouse  │     │  • SOVDS orders         │
│  • Standard ERP flow    │     │  • Vendor accepts       │
└─────────────────────────┘     │  • Vendor adds tracking │
                                │  • Status: Shipped      │
                                └─────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    STATUS SYNC TO ERP                            │
│  • Fulfillment status updated                                    │
│  • Tracking info visible in ERP                                  │
│  • Edit restrictions enforced                                    │
└─────────────────────────────────────────────────────────────────┘
```

### Status Progression

| Stage | Vendor Portal Status | ERP Status |
|-------|---------------------|------------|
| Order Split | Pending | Preprocessing |
| Vendor Notified | Vendor Notified | Processing |
| Vendor Accepts | Vendor Accepted | Processing |
| Vendor Ships | Shipped | Picked/Shipped |
| Delivered | Delivered | Completed |

---

## Troubleshooting

### Issue: Order not appearing in Vendor Portal

**Possible Causes:**
1. Product not marked as `product_source_type = 'dropshipped'`
2. Product not mapped to vendor in pivot table
3. Vendor type is not `erp_dropship`
4. Order not preprocessed (split job not run)

**Solution:**
```php
// Check product setup
$product = App\Product::find(PRODUCT_ID);
echo $product->product_source_type; // Should be 'dropshipped'

// Check vendor mapping
$mapping = DB::table('products_wp_vendors_table_pivot')
    ->where('product_id', PRODUCT_ID)
    ->first();
print_r($mapping);

// Check dropship tracking
$tracking = App\Models\DropshipOrderTracking::where('wp_vendor_id', VENDOR_ID)->get();
```

### Issue: Vendor login fails

**Possible Causes:**
1. User not linked to vendor record
2. Vendor status not 'active'
3. Vendor type not 'erp_dropship'

**Solution:**
```php
// Check vendor
$vendor = App\Models\WpVendor::find(VENDOR_ID);
echo "User ID: " . $vendor->user_id . "\n";
echo "Status: " . $vendor->status . "\n";
echo "Type: " . $vendor->vendor_type . "\n";
```

### Issue: Order not splitting

**Possible Causes:**
1. SplitOrderJob not dispatched
2. Product source type not 'dropshipped'
3. No vendor assigned to product

**Solution:**
```php
// Manually dispatch split job
App\Jobs\SplitOrderJob::dispatch(ORDER_ID);
```

### Issue: Dashboard shows "Trying to access array offset on null"

**Solution:** This was fixed. Clear caches:
```bash
php artisan view:clear
php artisan cache:clear
```

---

## Appendix: Quick Test Commands

### Create Test Vendor
```bash
php artisan vendor:create-test --email=test@example.com --password=Test@123 --name="Test Vendor"
```

### Map Product to Vendor (Tinker)
```php
DB::table('products_wp_vendors_table_pivot')->insert([
    'wp_vendor_id' => 3,
    'product_id' => 1,
    'vendor_cost_price' => 25.00,
    'dropship_selling_price' => 35.00,
    'status' => 'active',
    'created_at' => now(),
    'updated_at' => now(),
]);
App\Product::where('id', 1)->update(['product_source_type' => 'dropshipped']);
```

### Check Vendor Orders
```php
$orders = App\Models\DropshipOrderTracking::forVendor(3)->with('transaction')->get();
foreach($orders as $o) {
    echo "{$o->transaction->invoice_no}: {$o->fulfillment_status}\n";
}
```

### Clear All Caches
```bash
php artisan cache:clear && php artisan view:clear && php artisan route:clear && php artisan config:clear
```

---

## Document Revision History

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | Jan 11, 2026 | Initial version | AI Assistant |

---

**End of Testing Guide**
