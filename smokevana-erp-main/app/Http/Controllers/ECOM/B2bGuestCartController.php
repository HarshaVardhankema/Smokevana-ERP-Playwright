<?php

namespace App\Http\Controllers\ECOM;

use App\GuestCartItem;
use App\Product;
use App\Variation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class B2bGuestCartController extends Controller
{
    /**
     * Get guest cart items (B2B)
     */
    public function getCart(Request $request)
    {
        try {
            $guestSession = $request->get('current_guest_session');

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            $cartItems = GuestCartItem::with(['product', 'variation'])
                ->where('guest_session_id', $guestSession->id)
                ->get();

            $cartData = $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variation_id' => $item->variation_id,
                    'product_name' => $item->product->name ?? 'Unknown Product',
                    'variation_name' => $item->variation->name ?? null,
                    'qty' => $item->qty,
                    'item_type' => $item->item_type,
                    'discount_id' => $item->discount_id,
                    'lable' => $item->lable,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $cartData,
                'total_items' => $cartItems->count(),
                'total_qty' => $cartItems->sum('qty'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get cart items',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Add item to guest cart (B2B)
     */
    public function addToCart(Request $request)
    {
        try {
            $guestSession = $request->get('current_guest_session');

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'variation_id' => 'nullable|exists:variations,id',
                'qty' => 'required|integer|min:1',
                'item_type' => 'nullable|string|max:50',
                'discount_id' => 'nullable|exists:discounts,id',
                'lable' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if product exists and is active
            $product = Product::where('id', $request->product_id)
                ->where('is_inactive', 0)
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found or inactive.',
                ], 404);
            }

            // Check if variation exists (if provided)
            if ($request->variation_id) {
                $variation = Variation::where('id', $request->variation_id)
                    ->where('product_id', $request->product_id)
                    ->first();

                if (!$variation) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid variation for this product.',
                    ], 404);
                }
            }

            // Check if item already exists in cart
            $existingItem = GuestCartItem::where('guest_session_id', $guestSession->id)
                ->where('product_id', $request->product_id)
                ->where('variation_id', $request->variation_id)
                ->first();

            if ($existingItem) {
                // Update quantity
                $existingItem->update([
                    'qty' => $existingItem->qty + $request->qty,
                ]);

                $cartItem = $existingItem;
            } else {
                // Create new cart item
                $cartItem = GuestCartItem::create([
                    'guest_session_id' => $guestSession->id,
                    'product_id' => $request->product_id,
                    'variation_id' => $request->variation_id,
                    'qty' => $request->qty,
                    'item_type' => $request->item_type ?? 'line_item',
                    'discount_id' => $request->discount_id,
                    'lable' => $request->lable ?? 'Item',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'variation_id' => $cartItem->variation_id,
                    'qty' => $cartItem->qty,
                    'item_type' => $cartItem->item_type,
                    'discount_id' => $cartItem->discount_id,
                    'lable' => $cartItem->lable,
                ],
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add item to cart',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item quantity (B2B guest)
     */
    public function updateCartItem(Request $request, $itemId)
    {
        try {
            $guestSession = $request->get('current_guest_session');

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            $itemId = $request->route('itemId'); // avoid dynamic route conflict

            $validator = Validator::make($request->all(), [
                'qty' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cartItem = GuestCartItem::where('id', $itemId)
                ->where('guest_session_id', $guestSession->id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found.',
                ], 404);
            }

            $cartItem->update([
                'qty' => $request->qty,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cart item updated successfully',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'variation_id' => $cartItem->variation_id,
                    'qty' => $cartItem->qty,
                    'item_type' => $cartItem->item_type,
                    'discount_id' => $cartItem->discount_id,
                    'lable' => $cartItem->lable,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update cart item',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove item from B2B guest cart
     */
    public function removeFromCart(Request $request, $itemId)
    {
        try {
            $guestSession = $request->get('current_guest_session');

            $itemId = $request->route('itemId'); // avoid dynamic route conflict

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            $cartItem = GuestCartItem::where('id', $itemId)
                ->where('guest_session_id', $guestSession->id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found.',
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'status' => true,
                'message' => 'Item removed from cart successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all items from B2B guest cart
     */
    public function clearCart(Request $request)
    {
        try {
            $guestSession = $request->get('current_guest_session');

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear cart',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

