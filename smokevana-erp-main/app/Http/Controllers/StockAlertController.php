<?php

namespace App\Http\Controllers;

use App\Business;
use App\Contact;
use App\Product;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Utils\NotificationUtil;

class StockAlertController extends Controller
{
    public function requestAlert(Request $request)
    {
        try {
            Log::debug('StockAlert request received.', [
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'contact_id' => 'nullable|integer|exists:contacts,id',
                'variation_id' => 'nullable|integer|exists:variations,id',
                'email' => 'required|email',
                'is_recursive' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                Log::debug('Validation failed in StockAlertController@requestAlert', [
                    'errors' => $validator->errors()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product_id = $request->product_id;
            $contact_id = $request->contact_id; // Optional
            $variation_id = $request->variation_id;
            $email = $request->email; // Required
            $is_recursive = $request->has('is_recursive') ? (bool)$request->is_recursive : false;

            // Get product
            $product = Product::find($product_id);
            Log::debug('Product fetched', ['product_id' => $product_id, 'product_exists' => $product ? true : false]);

            if (!$product) {
                Log::debug('Product not found for stock alert request', ['product_id' => $product_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Get contact if provided (optional)
            $contact = null;
            if ($contact_id) {
                $contact = Contact::find($contact_id);
                Log::debug('Contact fetched', ['contact_id' => $contact_id, 'contact_exists' => $contact ? true : false]);
                if (!$contact) {
                    Log::debug('Contact not found for stock alert request', ['contact_id' => $contact_id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Contact not found'
                    ], 404);
                }
                
                // Verify business_id matches if contact is provided
                if ($product->business_id != $contact->business_id) {
                    Log::debug('Business mismatch: product and contact businesses differ', [
                        'product_id' => $product_id,
                        'product_business_id' => $product->business_id,
                        'contact_id' => $contact_id,
                        'contact_business_id' => $contact->business_id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Product and contact must belong to the same business'
                    ], 400);
                }
            }

            // Verify variation belongs to product if provided
            if ($variation_id) {
                $variation = \App\Variation::find($variation_id);
                Log::debug('Variation fetched for stock alert request', [
                    'variation_id' => $variation_id,
                    'variation_product_id' => $variation ? $variation->product_id : null
                ]);
                if (!$variation || $variation->product_id != $product_id) {
                    Log::debug('Variation not valid for this product', [
                        'variation_id' => $variation_id,
                        'product_id' => $product_id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid variation for this product'
                    ], 400);
                }
            }

            $business_id = $product->business_id;

            Log::debug('Checking product out-of-stock condition', [
                'product_id' => $product_id,
                'business_id' => $business_id
            ]);
            $isOutOfStock = $this->isProductOutOfStock($product_id, $business_id);
            
            Log::debug('isProductOutOfStock() result', [
                'result' => $isOutOfStock
            ]);

            if (!$isOutOfStock) {
                Log::debug('Product is NOT out of stock for alert request', [
                    'product_id' => $product_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product is currently in stock. No need to request stock alert.'
                ], 400);
            }

            // Check if alert already exists (with same product, email, and variation)
            // Use email as primary identifier since contact_id is optional
            Log::debug('Checking for existing StockAlert', [
                'product_id' => $product_id,
                'email' => $email,
                'contact_id' => $contact_id,
                'variation_id' => $variation_id,
            ]);
            $existingAlert = StockAlert::where('product_id', $product_id)
                ->where('email', $email)
                ->where(function($query) use ($contact_id) {
                    if ($contact_id) {
                        $query->where('contact_id', $contact_id);
                    } else {
                        $query->whereNull('contact_id');
                    }
                })
                ->where(function($query) use ($variation_id) {
                    if ($variation_id) {
                        $query->where('variation_id', $variation_id);
                    } else {
                        $query->whereNull('variation_id');
                    }
                })
                ->first();

            Log::debug('Existing StockAlert', [
                'exists' => $existingAlert ? true : false,
                'notified' => $existingAlert ? $existingAlert->notified : null,
                'existingAlert_id' => $existingAlert ? $existingAlert->id : null
            ]);

            if ($existingAlert) {
                // Update email and is_recursive if provided
                $updateData = [];
                if ($email) {
                    $updateData['email'] = $email;
                }
                if ($request->has('is_recursive')) {
                    $updateData['is_recursive'] = $is_recursive;
                }
                if (!empty($updateData)) {
                    Log::debug('Updating existing alert with new data', [
                        'existingAlert_id' => $existingAlert->id,
                        'updateData' => $updateData
                    ]);
                    $existingAlert->update($updateData);
                }

                if (!$existingAlert->notified) {
                    Log::debug('User is already subscribed to stock alerts and not notified yet.', [
                        'existingAlert_id' => $existingAlert->id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'You are already subscribed to stock alerts for this product.'
                    ], 400);
                } else {
                    // Reset notification status for existing alert
                    Log::debug('Resetting notification status for existing alert', [
                        'existingAlert_id' => $existingAlert->id
                    ]);
                    $existingAlert->resetNotification();

                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully resubscribed to stock alerts for this product.'
                    ]);
                }
            }

            // Create new alert
            $createdAlert = StockAlert::create([
                'product_id' => $product_id,
                'contact_id' => $contact_id, // Can be null
                'variation_id' => $variation_id,
                'email' => $email,
                'is_recursive' => $is_recursive
            ]);
            Log::debug('Created new StockAlert', [
                'createdAlert_id' => $createdAlert->id,
                'product_id' => $product_id,
                'contact_id' => $contact_id,
                'variation_id' => $variation_id,
                'email' => $email,
                'is_recursive' => $is_recursive,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to stock alerts. You will be notified when this product is back in stock.'
            ]);

        } catch (\Exception $e) {
            Log::error('Stock alert request error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while requesting stock alert.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function unsubscribe(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 422);
            }

            $tokenData = base64_decode($request->token);
            $data = json_decode($tokenData, true);

            // Validate token data - require product_id and email (contact_id is optional now)
            if (!$data || !isset($data['product_id']) || !isset($data['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid unsubscribe link'
                ], 400);
            }

            $alert = StockAlert::where('product_id', $data['product_id'])
                ->where('email', $data['email'])
                ->where(function($query) use ($data) {
                    if (isset($data['contact_id'])) {
                        $query->where('contact_id', $data['contact_id']);
                    } else {
                        $query->whereNull('contact_id');
                    }
                })
                ->where(function($query) use ($data) {
                    if (isset($data['variation_id'])) {
                        $query->where('variation_id', $data['variation_id']);
                    } else {
                        $query->whereNull('variation_id');
                    }
                })
                ->first();

            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock alert subscription not found'
                ], 404);
            }

            $alert->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from stock alerts.'
            ]);

        } catch (\Exception $e) {
            Log::error('Stock alert unsubscribe error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unsubscribing.'
            ], 500);
        }
    }

    private function isProductOutOfStock($product_id, $business_id)
    {
        $product = Product::find($product_id);
        
        if (!$product || !$product->enable_stock) {
            return false;
        }

        $stock = DB::table('variation_location_details as vld')
            ->join('variations as v', 'vld.variation_id', '=', 'v.id')
            ->where('v.product_id', $product_id)
            ->sum('vld.in_stock_qty');

        return $stock <= 0;
    }

    public function sendStockAlerts($product_id, $business_id)
    {
        try {
            $product = Product::with(['variations'])->find($product_id);
            if (!$product) {
                return false;
            }

            // Check if product is actually in stock before sending alerts
            $isOutOfStock = $this->isProductOutOfStock($product_id, $business_id);
            if ($isOutOfStock) {
                // Product is still out of stock, don't send alerts
                return true;
            }

            $alerts = StockAlert::where('product_id', $product_id)
                ->where('notified', false)
                ->whereNotNull('email') // Only get alerts with email
                ->with(['contact', 'product', 'variation'])
                ->get();

            Log::info('Stock alerts found for product', [
                'product_id' => $product_id,
                'alerts_count' => $alerts->count(),
                'business_id' => $business_id
            ]);

            if ($alerts->isEmpty()) {
                Log::info('No alerts found or all already notified', ['product_id' => $product_id]);
                return true;
            }

            $business = Business::find($business_id);
            $business_name = $business->name ?? 'Our Store';

            $notificationUtil = new NotificationUtil();

            foreach ($alerts as $alert) {
                try {
                    // Prepare custom data for notification template
                    $unsubscribeUrl = $this->getUnsubscribeUrl($alert);
                    $productUrl = config('app.front-url') . '/product/' . ($product->slug ?? $product->id);
                    
                    // Get contact name if contact exists, otherwise use email or default
                    $contactName = 'Valued Customer';
                    if ($alert->contact) {
                        $contactName = $alert->contact->name ?? $contactName;
                    } else {
                        // Try to extract name from email (before @)
                        $emailParts = explode('@', $alert->email);
                        if (!empty($emailParts[0])) {
                            $contactName = ucfirst($emailParts[0]);
                        }
                    }
                    
                    $customData = (object)[
                        'email' => $alert->email,
                        'name' => $contactName,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'product_url' => $productUrl,
                        'unsubscribe_url' => $unsubscribeUrl,
                        'brand_id' => null
                    ];
                    
                    // Use notification template system
                    $contactObj = (object)[
                        'email' => $alert->email,
                        'mobile' => $alert->contact->mobile ?? null
                    ];
                    
                    Log::info('Attempting to send stock alert notification', [
                        'alert_id' => $alert->id,
                        'email' => $alert->email,
                        'product_id' => $product_id,
                        'business_id' => $business_id
                    ]);

                    $notificationUtil->autoSendNotificationCustom($business_id, 'stock_alert', $customData, $contactObj);

                    Log::info('Stock alert notification sent', [
                        'alert_id' => $alert->id,
                        'email' => $alert->email
                    ]);

                    // Mark as notified and handle deletion based on is_recursive
                    $alert->markAsNotifiedAndHandleDeletion();

                } catch (\Exception $e) {
                    Log::error("Failed to send stock alert to {$alert->email}: " . $e->getMessage());
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending stock alerts: ' . $e->getMessage());
            return false;   
        }
    }

    private function getUnsubscribeUrl($alert)
    {
        $tokenData = [
            'product_id' => $alert->product_id,
            'email' => $alert->email, // Use email as primary identifier
            'variation_id' => $alert->variation_id
        ];
        
        // Include contact_id only if it exists
        if ($alert->contact_id) {
            $tokenData['contact_id'] = $alert->contact_id;
        }

        $token = base64_encode(json_encode($tokenData));
        return url('/api/stock-alerts/unsubscribe?token=' . $token);
    }
}

