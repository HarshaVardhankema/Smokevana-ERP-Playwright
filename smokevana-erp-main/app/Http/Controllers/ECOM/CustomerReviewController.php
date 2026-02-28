<?php

namespace App\Http\Controllers\ECOM;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Product;
use App\Contact;
use App\Business;
use App\Transaction;
use App\TransactionSellLine;
use App\Variation;
use App\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerReviewController extends Controller
{
    /**
     * Check authentication and return user
     */
    private function authCheck($request)
    {
        $contact = Auth::guard('api')->user();
        if ($contact) {
            return [
                'status' => true,
                'user' => $contact
            ];
        } else {
            return [
                'status' => false,
                'message' => 'User not authenticated',
            ];
        }
    }

    /**
     * Update ratings for product and variation (if provided).
     *
     * @param int $productId
     * @param int|null $variationId
     * @return void
     */
    private function updateRatings($productId, $variationId = null)
    {
        $productReviews = CustomerReview::where('product_id', $productId)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->whereNotNull('rating')
            ->get();

        $productTotal = $productReviews->count();
        $productAvg = $productTotal > 0 ? round($productReviews->avg('rating'), 2) : null;

        Product::where('id', $productId)->update([
            'average_rating' => $productAvg,
            'total_reviews' => $productTotal
        ]);

        if ($variationId) {
            $variationReviews = CustomerReview::where('product_id', $productId)
                ->where('variation_id', $variationId)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->whereNotNull('rating')
                ->get();

            $varTotal = $variationReviews->count();
            $varAvg = $varTotal > 0 ? round($variationReviews->avg('rating'), 2) : null;

            Variation::where('id', $variationId)->update([
                'average_rating' => $varAvg,
                'total_reviews' => $varTotal,
            ]);
        }
    }

    /**
     * Get all reviews for a product
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductReviews(Request $request, $productId)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);
            $sortBy = $request->query('sort_by', 'latest'); // 'latest' or 'likes'

            $query = CustomerReview::with(['contact', 'createdBy'])
                ->where('product_id', $productId)
                ->where('is_active', 1)
                ->where('is_deleted', 0);

            // Sort by likes or latest
            if ($sortBy === 'likes') {
                $query->orderBy('likes', 'desc')->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $reviews = $query->paginate($perPage, ['*'], 'page', $page);

            $reviewData = collect($reviews->items())->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_name' => $review->public_name ?? ($review->contact ? ($review->contact->name ?? $review->contact->supplier_business_name) : 'Anonymous'),
                    'variation_id' => $review->variation_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            })->values();

            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $reviews->currentPage(),
                    'data' => $reviewData,
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'from' => $reviews->firstItem(),
                    'to' => $reviews->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reviews by a customer
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerReviews(Request $request)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);
            $customerId = $auth['user']->id;

            $reviews = CustomerReview::with(['product'])
                ->where('contact_id', $customerId)
                ->where('is_deleted', 0)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $reviewData = collect($reviews->items())->map(function ($review) {
                return [
                    'id' => $review->id,
                    'product_id' => $review->product_id,
                    'product_name' => $review->product ? $review->product->name : null,
                    'product_image' => $review->product ? $review->product->image_url : null,
                    'variation_id' => $review->variation_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'public_name' => $review->public_name,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    'is_active' => $review->is_active,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
                ];
            })->values();

            return response()->json([
                'status' => true,
                'data' => [
                    'current_page' => $reviews->currentPage(),
                    'data' => $reviewData,
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'from' => $reviews->firstItem(),
                    'to' => $reviews->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new review for a product (using productId from URL)
     * 
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProductReview(Request $request, $productId)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:5|max:2000',
            'transaction_id' => 'nullable|integer|exists:transactions,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'variation_id' => 'nullable|integer|exists:variations,id',
            'title' => 'nullable|string|max:255',
            'public_name' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,avi|max:10240', // Max 10MB
            'media_url' => 'nullable|url|max:2048',
            'media_type' => 'nullable|string|max:20|in:photo,video',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages
                ];
            }
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $formattedErrors
            ], 422);
        }

        try {
            $customer = $auth['user'];
            $product = Product::find($productId);
            $variationId = $request->variation_id;
            
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($variationId) {
                $variation = Variation::where('id', $variationId)
                    ->where('product_id', $productId)
                    ->first();
                if (!$variation) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Variation does not belong to this product.'
                    ], 422);
                }
            }

            // Validate that customer has purchased and invoiced this product
            $transactionId = $request->transaction_id;
            $validTransaction = null;

            if ($transactionId) {
                // Verify the transaction belongs to the customer and is invoiced
                $validTransaction = Transaction::where('id', $transactionId)
                    ->where('contact_id', $customer->id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereHas('sell_lines', function($query) use ($productId, $variationId) {
                        $query->where('product_id', $productId);
                        if ($variationId) {
                            $query->where('variation_id', $variationId);
                        }
                    })
                    ->first();

                if (!$validTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Transaction not found or does not contain this product'
                    ], 403);
                }
            } else {
                // If transaction_id not provided, find a valid invoiced transaction for this product
                $validTransaction = Transaction::where('contact_id', $customer->id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereHas('sell_lines', function($query) use ($productId, $variationId) {
                        $query->where('product_id', $productId);
                        if ($variationId) {
                            $query->where('variation_id', $variationId);
                        }
                    })
                    ->orderBy('transaction_date', 'desc')
                    ->first();

                if (!$validTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You must purchase and receive an invoice for this product before reviewing it.'
                    ], 403);
                }

                $transactionId = $validTransaction->id;
            }

            // Check if customer already reviewed this product
            $existingReview = CustomerReview::where('contact_id', $customer->id)
                ->where('product_id', $productId)
                ->where('is_deleted', 0)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already reviewed this product. You can update your existing review.',
                    'review_id' => $existingReview->id
                ], 409);
            }

            // Get business_id from product or customer
            $businessId = $product->business_id ?? $customer->business_id ?? 1;

            // Handle image/file upload
            $mediaUrl = $request->media_url ?? null;
            $mediaType = $request->media_type ?? null;
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $mimeType = $file->getMimeType();
                
                // Determine media type from mime type
                if (strpos($mimeType, 'image/') !== false) {
                    $mediaType = 'photo';
                } elseif (strpos($mimeType, 'video/') !== false) {
                    $mediaType = 'video';
                }
                
                // Check file size (max 10MB = 10485760 bytes)
                $maxSize = config('constants.document_size_limit', 10485760);
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'status' => false,
                        'message' => 'File size exceeds maximum limit of ' . round($maxSize / 1048576, 2) . 'MB'
                    ], 422);
                }
                
                // Generate unique file name
                $timestamp = time();
                $randomNumber = mt_rand();
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = $timestamp . '_' . $randomNumber . '_' . $originalName;
                
                // Ensure uploads/media directory exists
                $destinationPath = public_path('uploads/media');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }
                
                // Move uploaded file to public/uploads/media
                if ($file->move($destinationPath, $fileName)) {
                    // Generate the media URL
                    $mediaUrl = asset('uploads/media/' . rawurlencode($fileName));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload image. Please try again.'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $review = CustomerReview::create([
                'contact_id' => $customer->id,
                'product_id' => $productId,
                'variation_id' => $variationId,
                'transaction_id' => $transactionId,
                'business_id' => $businessId,
                'description' => $request->description,
                'rating' => $request->rating,
                'title' => $request->title ?? null,
                'public_name' => $request->public_name ?? null,
                'media_url' => $mediaUrl,
                'media_type' => $mediaType,
                'is_active' => 1,
                'is_deleted' => 0,
            ]);

            // Update product/variation ratings
            $this->updateRatings($productId, $variationId);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Review created successfully',
                'data' => [
                    'id' => $review->id,
                    'product_id' => $review->product_id,
                    'variation_id' => $review->variation_id,
                    'transaction_id' => $review->transaction_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'public_name' => $review->public_name,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new review
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        // Custom validation rule: media_url is required if no image file is uploaded
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'description' => 'required|string|min:5|max:2000',
            'transaction_id' => 'nullable|integer|exists:transactions,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'variation_id' => 'nullable|integer|exists:variations,id',
            'title' => 'nullable|string|max:255',
            'public_name' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,avi|max:10240', // Max 10MB
            'media_url' => 'nullable|url|max:2048',
            'media_type' => 'nullable|string|max:20|in:photo,video',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages
                ];
            }
            return response()->json([
                'status' => false,
                'message' => $formattedErrors
            ], 422);
        }

        try {
            $customer = $auth['user'];
            $product = Product::find($request->product_id);
            $variationId = $request->variation_id;
            
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($variationId) {
                $variation = Variation::where('id', $variationId)
                    ->where('product_id', $request->product_id)
                    ->first();
                if (!$variation) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Variation does not belong to this product.'
                    ], 422);
                }
            }

            // Validate that customer has purchased and invoiced this product
            $transactionId = $request->transaction_id;
            $validTransaction = null;

            if ($transactionId) {
                // Verify the transaction belongs to the customer and is invoiced
                $validTransaction = Transaction::where('id', $transactionId)
                    ->where('contact_id', $customer->id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereHas('sell_lines', function($query) use ($request, $variationId) {
                        $query->where('product_id', $request->product_id);
                        if ($variationId) {
                            $query->where('variation_id', $variationId);
                        }
                    })
                    ->first();

                if (!$validTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not authorized.'
                    ], 403);
                }
            } else {
                // If transaction_id not provided, find a valid invoiced transaction for this product
                $validTransaction = Transaction::where('contact_id', $customer->id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereHas('sell_lines', function($query) use ($request, $variationId) {
                        $query->where('product_id', $request->product_id);
                        if ($variationId) {
                            $query->where('variation_id', $variationId);
                        }
                    })
                    ->orderBy('transaction_date', 'desc')
                    ->first();

                if (!$validTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please ensure the transaction is completed and invoiced before reviewing the product.'
                    ], 403);
                }

                $transactionId = $validTransaction->id;
            }

            // Check if customer already reviewed this product
            $existingReview = CustomerReview::where('contact_id', $customer->id)
                ->where('product_id', $request->product_id)
                ->where('is_deleted', 0)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already reviewed this product. You can update your existing review.',
                    'review_id' => $existingReview->id
                ], 409);
            }

            // Get business_id from product or customer
            $businessId = $product->business_id ?? $customer->business_id ?? 1;

            // Handle image/file upload
            $mediaUrl = $request->media_url ?? null;
            $mediaType = $request->media_type ?? null;
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $mimeType = $file->getMimeType();
                
                // Determine media type from mime type
                if (strpos($mimeType, 'image/') !== false) {
                    $mediaType = 'photo';
                } elseif (strpos($mimeType, 'video/') !== false) {
                    $mediaType = 'video';
                }
                
                // Check file size (max 10MB = 10485760 bytes)
                $maxSize = config('constants.document_size_limit', 10485760);
                if ($file->getSize() > $maxSize) {
                    return response()->json([
                        'status' => false,
                        'message' => 'File size exceeds maximum limit of ' . round($maxSize / 1048576, 2) . 'MB'
                    ], 422);
                }
                
                // Generate unique file name
                $timestamp = time();
                $randomNumber = mt_rand();
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = $timestamp . '_' . $randomNumber . '_' . $originalName;
                
                // Ensure uploads/media directory exists
                $destinationPath = public_path('uploads/media');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }
                
                // Move uploaded file to public/uploads/media
                if ($file->move($destinationPath, $fileName)) {
                    // Generate the media URL
                    $mediaUrl = asset('uploads/media/' . rawurlencode($fileName));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload image. Please try again.'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $review = CustomerReview::create([
                'contact_id' => $customer->id,
                'product_id' => $request->product_id,
                'variation_id' => $variationId,
                'transaction_id' => $transactionId,
                'business_id' => $businessId,
                'description' => $request->description,
                'rating' => $request->rating,
                'title' => $request->title ?? null,
                'public_name' => $request->public_name ?? null,
                'media_url' => $mediaUrl,
                'media_type' => $mediaType,
                'is_active' => 1,
                'is_deleted' => 0,
            ]);

            // Update product/variation ratings
            $this->updateRatings($request->product_id, $variationId);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Review created successfully',
                'data' => [
                    'id' => $review->id,
                    'product_id' => $review->product_id,
                    'variation_id' => $review->variation_id,
                    'transaction_id' => $review->transaction_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'public_name' => $review->public_name,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    // 'likes' => $review->likes ?? 0,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing review
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'public_name' => 'nullable|string|max:255',
            'media_url' => 'nullable|url|max:2048',
            'media_type' => 'nullable|string|max:20|in:photo,video',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages
                ];
            }
            return response()->json([
                'status' => false,
                'message' => $formattedErrors
            ], 422);
        }

        try {
            $customer = $auth['user'];
            
            $review = CustomerReview::where('id', $id)
                ->where('contact_id', $customer->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found or you do not have permission to update it'
                ], 404);
            }

            DB::beginTransaction();

            $review->description = $request->description;
            if ($request->has('rating')) {
                $review->rating = $request->rating;
            }
            if ($request->has('title')) {
                $review->title = $request->title;
            }
            if ($request->has('public_name')) {
                $review->public_name = $request->public_name;
            }
            if ($request->has('media_url')) {
                $review->media_url = $request->media_url;
            }
            if ($request->has('media_type')) {
                $review->media_type = $request->media_type;
            }
            $review->updated_by = null; // Customer reviews are updated by contacts, not users
            $review->save();

            // Update product/variation ratings
            $this->updateRatings($review->product_id, $review->variation_id);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Review updated successfully',
                'data' => [
                    'id' => $review->id,
                    'product_id' => $review->product_id,
                    'variation_id' => $review->variation_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'public_name' => $review->public_name,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single review by ID
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $review = CustomerReview::with(['contact', 'product', 'createdBy'])
                ->where('id', $id)
                ->where('is_deleted', 0)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $review->id,
                    'customer_name' => $review->public_name ?? ($review->contact ? ($review->contact->name ?? $review->contact->supplier_business_name) : 'Anonymous'),
                    'product_id' => $review->product_id,
                    'product_name' => $review->product ? $review->product->name : null,
                    'variation_id' => $review->variation_id,
                    'title' => $review->title,
                    'description' => $review->description,
                    'rating' => $review->rating ?? null,
                    'media_url' => $review->media_url,
                    'media_type' => $review->media_type,
                    'is_active' => $review->is_active,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete (soft delete) a review
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        try {
            $customer = $auth['user'];
            
            $review = CustomerReview::where('id', $id)
                ->where('contact_id', $customer->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found or you do not have permission to delete it'
                ], 404);
            }

            DB::beginTransaction();

            $productId = $review->product_id;
            $review->is_deleted = 1;
            $review->deleted_by = null; // Customer reviews are deleted by contacts, not users
            $review->deleted_at = now();
            $review->save();

            // Update product/variation ratings
            $this->updateRatings($productId, $review->variation_id);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Review deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update rating for a review (1-5 stars)
     * Customer can update rating for their own review
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleLike(Request $request, $id)
    {
        $auth = $this->authCheck($request);
        if (!$auth['status']) {
            return response()->json(['status' => false, 'message' => $auth['message']], 401);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $formattedErrors = [];
            foreach ($errors as $key => $errorMessages) {
                $formattedErrors[] = [
                    'field' => $key,
                    'messages' => $errorMessages
                ];
            }
            return response()->json([
                'status' => false,
                'message' => $formattedErrors
            ], 422);
        }

        try {
            $customer = $auth['user'];
            
            $review = CustomerReview::where('id', $id)
                ->where('contact_id', $customer->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found or you do not have permission to update it'
                ], 404);
            }

            DB::beginTransaction();

            // Update rating
            $review->rating = $request->rating;
            $review->updated_by = null; // Customer reviews are updated by contacts, not users
            $review->save();

            // Update product/variation ratings
            $this->updateRatings($review->product_id, $review->variation_id);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Review rating updated successfully',
                'data' => [
                    'id' => $review->id,
                    'product_id' => $review->product_id,
                    'variation_id' => $review->variation_id,
                    'rating' => $review->rating,
                    'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update rating',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
