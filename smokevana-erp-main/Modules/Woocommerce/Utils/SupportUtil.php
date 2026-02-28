<?php

namespace Modules\Woocommerce\Utils;

use App\Media;
use App\Models\Business;
use App\Utils\Util;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SupportUtil extends Util
{

    // ------------------- Product Sync from Woo to ERP -------------------

    /**
     * Download and store image in the public/uploads/img folder
     * @param mixed $image_url
     * @param mixed $product_id
     * @param mixed $is_variation
     * @param mixed $sku
     * @return string|null
     */
    public function downloadAndStoreImage($image_url, $product_id, $is_variation = false ,$sku="")
    {
        try {
            $response = Http::timeout(30)->get($image_url);
            if ($response->successful()) {
                $image_data = $response->body();
                $year = date('Y');
                $month = date('m');
                $day = date('d');
                $url_parts = parse_url($image_url);
                $extension = 'jpg';
                if (isset($url_parts['path'])) {
                    $path_parts = pathinfo($url_parts['path']);
                    if (isset($path_parts['extension'])) {
                        $extension = strtolower($path_parts['extension']);
                    }
                }
                $base_name = basename($image_url, '.' . $extension);
                $filename = $sku.'_'.$year . '-' . $month . '-' . $day . '-' . $base_name . '.' . $extension;
                $mediaURL = $sku.'_'.$year . '_' . $month . '_' . $day . '_' . $base_name . '.' . $extension;
                $storagePath = public_path('uploads/img');
                $fullPath = $storagePath . '/' . $filename;
                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                if (!file_exists($fullPath)) {
                    file_put_contents($fullPath, $image_data);
                }
                Media::updateOrCreate(
                    ['model_id' => $product_id],
                    [
                        'business_id' => 1,
                        'file_name' => $mediaURL,
                        'uploaded_by' => 1,
                        'model_type' => $is_variation ? "App\Variation" : "App\Product"
                    ]
                );
                return $filename;
            } else {
                Log::error("Image download failed for URL: $image_url");
            }
        } catch (\Exception $e) {
            Log::error('Failed to download image: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Download and store gallery image in the public/uploads/img/gallery folder
     * @param mixed $image_url
     * @param mixed $product_id
     * @param mixed $is_variation
     * @param mixed $sku
     * @return string|null
     */
    public function downloadAndStoreGalleryImage($image_url, $product_id, $is_variation = false,$sku="")
    {
        try {
            // Parse the URL and prepare filename
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $url_parts = parse_url($image_url);
            $extension = 'jpg';

            if (isset($url_parts['path'])) {
                $path_parts = pathinfo($url_parts['path']);
                if (isset($path_parts['extension'])) {
                    $extension = strtolower($path_parts['extension']);
                }
            }

            $base_name = basename($image_url, '.' . $extension);
            $filename =$sku.'_'. $year . '-' . $month . '-' . $day . '-' . $base_name . '.' . $extension;
            $mediaURL =$sku.'_'. $year . '_' . $month . '_' . $day . '_' . $base_name . '.' . $extension;
            $storagePath = public_path('uploads/img/gallery/');
            $fullPath = $storagePath . '/' . $filename;

            // Skip if file already exists
            if (file_exists($fullPath)) {
                return 'uploads/img/gallery/' . $filename;
            }

            // Download only if not already present
            $response = Http::timeout(30)->get($image_url);
            if ($response->successful()) {
                $image_data = $response->body();

                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                file_put_contents($fullPath, $image_data);

                Media::updateOrCreate(
                    ['model_id' => $product_id],
                    [
                        'business_id' => 1,
                        'file_name' => $mediaURL,
                        'uploaded_by' => 1,
                        'model_type' => $is_variation ? "App\Variation" : "App\Product"
                    ]
                );

                return 'uploads/img/gallery/' . $filename;
            } else {
                Log::error("Image download failed for URL: $image_url");
            }
        } catch (\Exception $e) {
            Log::error('Failed to download image: ' . $e->getMessage());
        }

        return null;
    }

    // ------------------- Product Sync from ERP to Woo -------------------



    // ------------------- Order Sync from Woo to ERP -------------------



    // ------------------- Order Sync from ERP to Woo -------------------
}
