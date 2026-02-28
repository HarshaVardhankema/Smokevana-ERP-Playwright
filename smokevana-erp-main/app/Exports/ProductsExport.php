<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductsExport implements FromArray
{
    public function array(): array
    {
        $business_id = request()->session()->get('user.business_id');

        $products = Product::where('business_id', $business_id)
                    ->with(['brand', 'unit', 'category', 'sub_category', 'product_variations', 'product_variations.variations', 'product_tax', 'rack_details', 'product_locations'])
                    ->select('products.*')
                    ->get();

        //set headers - 38 columns matching ImportProductsController format
        $products_array = [['NAME', 'BRAND', 'UNIT', 'CATEGORY', 'SUB-CATEGORY', 'SKU (Leave blank to auto generate sku)', 'BARCODE TYPE', 'APPLICABLE TAX', 'Selling Price Tax Type (inclusive or exclusive)', 'PRODUCT TYPE (single or variable)', 'VARIATION NAME (Keep blank if product type is single)', 'VARIATION VALUES (| seperated values & blank if product type if single)', 'VARIATION SKUs (| seperated values & blank if product type if single)', 'PURCHASE PRICE (Including tax)', 'PURCHASE PRICE (Excluding tax)', 'PROFIT MARGIN', 'SELLING PRICE', 'ENABLE IMEI OR SERIAL NUMBER(1=yes 0=No)', 'WEIGHT', 'IMAGE', 'PRODUCT DESCRIPTION', 'CUSTOM FIELD 1', 'CUSTOM FIELD 2', 'CUSTOM FIELD 3', 'CUSTOM FIELD 4', 'NOT FOR SELLING(1=yes 0=No)', 'PRODUCT LOCATIONS', 'IS INACTIVE(1=yes 0=No)', 'WARRANTY', 'SECONDARY UNIT', 'PREPARATION TIME (minutes)', 'ML', 'CT', 'PRODUCT VISIBILITY (public/private/hidden)', 'MAX SALE LIMIT', 'ENABLE SELLING(1=yes 0=No)', 'TOP SELLING(1=yes 0=No)', 'SLUG']];
        foreach ($products as $product) {
            $product_variation = $product->product_variations->first();

            $product_variation_name = $product->type == 'variable' ? $product_variation?->name??'' : '';
            $variation_values = $product->type == 'variable' && $product_variation && $product_variation->variations ? implode('|', $product_variation->variations->pluck('name')->toArray()) : '';
            $variation_skus = $product_variation && $product_variation->variations ? implode('|', $product_variation->variations->pluck('sub_sku')->toArray()) : '';
            $purchase_prices = implode('|', $product_variation?->variations?->pluck('dpp_inc_tax')->toArray()??[]);
            $purchase_prices_ex_tax = implode('|', $product_variation?->variations?->pluck('default_purchase_price')->toArray()??[]);
            $profit_percents = implode('|', $product_variation?->variations?->pluck('profit_percent')->toArray()??[]);
            $selling_prices = $product->tax_type == 'inclusive' ? implode('|', $product_variation?->variations?->pluck('sell_price_inc_tax')->toArray()??[]) : implode('|', $product_variation?->variations?->pluck('default_sell_price')->toArray()??[]);
            $locations = implode(',', $product->product_locations?->pluck('name')->toArray()??[]);

            // Get secondary unit
            $secondary_unit = '';
            if ($product->secondary_unit_id) {
                $sec_unit = \App\Unit::find($product->secondary_unit_id);
                if ($sec_unit) {
                    $secondary_unit = $sec_unit->short_name ?? $sec_unit->actual_name ?? '';
                }
            }

            // Get warranty name
            $warranty_name = '';
            if ($product->warranty_id) {
                $warranty = \App\Warranty::find($product->warranty_id);
                if ($warranty) {
                    $warranty_name = $warranty->name ?? '';
                }
            }

            $product_arr = [
                $product->name,                                                      // 0: NAME
                $product->brand->name ?? '',                                         // 1: BRAND
                $product->unit->short_name ?? '',                                    // 2: UNIT
                $product->category->name ?? '',                                      // 3: CATEGORY
                $product->sub_category->name ?? '',                                  // 4: SUB-CATEGORY
                $product->sku,                                                       // 5: SKU
                $product->barcode_type,                                              // 6: BARCODE TYPE
                $product->product_tax->name ?? '',                                  // 7: APPLICABLE TAX
                $product->tax_type,                                                  // 8: Selling Price Tax Type
                $product->type,                                                       // 9: PRODUCT TYPE
                $product_variation_name,                                             // 10: VARIATION NAME
                $variation_values,                                                    // 11: VARIATION VALUES
                $variation_skus,                                                      // 12: VARIATION SKUs
                $purchase_prices,                                                     // 13: PURCHASE PRICE (Including tax)
                $purchase_prices_ex_tax,                                             // 14: PURCHASE PRICE (Excluding tax)
                $profit_percents,                                                     // 15: PROFIT MARGIN
                $selling_prices,                                                      // 16: SELLING PRICE
                $product->enable_sr_no,                                               // 17: ENABLE IMEI OR SERIAL NUMBER
                $product->weight ?? '',                                               // 18: WEIGHT
                $product->image_url ?? '',                                           // 19: IMAGE
                $product->product_description ?? '',                                  // 20: PRODUCT DESCRIPTION
                $product->product_custom_field1 ?? '',                               // 21: CUSTOM FIELD 1
                $product->product_custom_field2 ?? '',                               // 22: CUSTOM FIELD 2
                $product->product_custom_field3 ?? '',                               // 23: CUSTOM FIELD 3
                $product->product_custom_field4 ?? '',                               // 24: CUSTOM FIELD 4
                $product->not_for_selling,                                            // 25: NOT FOR SELLING
                $locations,                                                           // 26: PRODUCT LOCATIONS
                $product->is_inactive ?? 0,                                          // 27: IS INACTIVE
                $warranty_name,                                                       // 28: WARRANTY
                $secondary_unit,                                                      // 29: SECONDARY UNIT
                $product->preparation_time_in_minutes ?? '',                          // 30: PREPARATION TIME
                $product->ml ?? 0,                                                    // 31: ML
                $product->ct ?? 0,                                                    // 32: CT
                $product->productVisibility ?? 'public',                               // 33: PRODUCT VISIBILITY
                $product->maxSaleLimit ?? '',                                         // 34: MAX SALE LIMIT
                $product->enable_selling ?? 1,                                        // 35: ENABLE SELLING
                $product->top_selling ?? 0,                                           // 36: TOP SELLING
                $product->slug ?? '',                                                  // 37: SLUG
            ];

            $products_array[] = $product_arr;
        }

        return $products_array;
    }
}
