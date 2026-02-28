<?php

namespace App\Exports;

use App\Product;
use App\Variation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductImageMappingExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $business_id = request()->session()->get('user.business_id');
        $user = auth()->user();
        
        // Get user's permitted locations
        $permitted_locations = $user->permitted_locations($business_id);
        
        // Build query for products
        $products_query = Product::where('business_id', $business_id)
            ->with(['variations' => function($query) {
                $query->with('media');
            }]);
        
        // Filter by location permissions if user doesn't have access to all locations
        if ($permitted_locations != 'all' && is_array($permitted_locations) && !empty($permitted_locations)) {
            $products_query->whereHas('product_locations', function($query) use ($permitted_locations) {
                $query->whereIn('product_locations.location_id', $permitted_locations);
            });
        }
        
        // Get filtered products
        $products = $products_query->get();

        $export_data = [];

        foreach ($products as $product) {
            // Export parent product
            $export_data[] = [
                $product->sku ?? '',
                'product',
                $product->name,
                $product->image ?? '',
            ];

            // Export variations
            foreach ($product->variations as $variation) {
                // Get variation image from media table (first media if exists)
                $variation_image = '';
                if ($variation->media && $variation->media->count() > 0) {
                    $variation_image = $variation->media->first()->file_name ?? '';
                }

                $export_data[] = [
                    $variation->sub_sku ?? '',
                    'variation',
                    $variation->name ?? '',
                    $variation_image,
                ];
            }
        }

        return $export_data;
    }

    public function headings(): array
    {
        return ['SKU', 'Type', 'Name', 'Image File Name'];
    }
}

