<?php

namespace App\Http\Controllers\B2CECOM;

use App\GuestSession;
use App\GuestCartItem;
use App\CartItem;
use App\Http\Controllers\Controller;
use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    /**
     * Create a new guest session
     */
    public function createSession(Request $request)
    {
        try {
            $locationId = $request->route('location_id');
            $brandName = $request->route('brand_name');
            
            // Get location and brand from middleware
            $location = $request->get('current_location');
            $brand = $request->get('current_brand');
            
            if (!$location || !$brand) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid location or brand.',
                ], 400);
            }
            
            // Create guest session
            $guestSession = GuestSession::createSession($locationId, $brand->id);
            
            return response()->json([
                'status' => true,
                'message' => 'Guest session created successfully',
                'data' => [
                    'guest_token' => $guestSession->uuid,
                    'expires_at' => $guestSession->expires_at->toISOString(),
                    'location_id' => $locationId,
                    'brand_name' => $brandName
                ]
            ], 201);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create guest session',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validate guest session
     */
    public function validateSession(Request $request)
    {
        try {
            $guestSessionUuid = $request->get('guest_session');
            if (!$guestSessionUuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }

            // Fetch the guest session by uuid
            $guestSession = GuestSession::where('uuid', $guestSessionUuid)->first();

            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid guest session.',
                ], 401);
            }

            // Check if expires_at is within 7 days from now, and extend if needed
            $expiresAt = \Carbon\Carbon::parse($guestSession->expires_at);
            $now = now();

            if ($expiresAt->diffInDays($now, false) < 7) {
                $guestSession->expires_at = $now->copy()->addDays(7);
                $guestSession->save();
            }

            $brand = \App\Brands::find($guestSession->brand_id);
            return response()->json([
                'status' => true,
                'message' => 'Guest session is valid',
                'data' => [
                    'guest_token' => $guestSession->uuid,
                    'expires_at' => \Carbon\Carbon::parse($guestSession->expires_at)->toISOString(),
                    'location_id' => $guestSession->location_id,
                    'brand_name' => $brand->slug
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to validate guest session',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Convert guest session to registered user
     */
    public function convertToUser(Request $request)
    {
        try {
            $guestSessionId = $request->get('guest_session');
            
            $guestSession = GuestSession::where('uuid', $guestSessionId)->first();
            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }
            
            // Validate registration data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:contacts,email',
                'password' => 'required|min:8|confirmed',
                'first_name' => 'required|string|max:50',
                'last_name' => 'nullable|string|max:50',
                'mobile' => 'required|string|min:10|max:10',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Handle customer_group_id for B2C customers
            // Only set if explicitly provided in request, otherwise leave as null for B2C
            $customerGroupId = null;
            if ($request->has('customer_group_id') && !empty($request->input('customer_group_id'))) {
                $customerGroupId = $request->input('customer_group_id');
            }
            
            // Create user account
            $user = Contact::create([
                'business_id' => $guestSession->brand->business_id,
                'location_id' => $guestSession->location_id,
                'brand_id' => $guestSession->brand_id,
                'type' => 'customer',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'mobile' => $request->mobile,
                'country' => 'US',
                'isApproved' => true,
                'customer_group_id' => $customerGroupId, // Nullable for B2C customers
                'created_by' => 1,
            ]);
            
            // Transfer guest cart to user cart
            $guestCartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();
            
            foreach ($guestCartItems as $guestItem) {
                // Create regular cart item for the user
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $guestItem->product_id,
                    'variation_id' => $guestItem->variation_id,
                    'qty' => $guestItem->qty,
                    'item_type' => $guestItem->item_type,
                    'discount_id' => $guestItem->discount_id,
                    'lable' => $guestItem->lable,
                ]);
            }
            
            // Delete guest cart items
            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();
            
            // Delete guest session
            $guestSession->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Guest session converted to user account successfully',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'message' => 'Your cart has been transferred to your new account.'
                ]
            ], 201);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to convert guest session',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extend guest session
     */
    public function extendSession(Request $request)
    {
        try {
            $guestSessionId = $request->get('guest_session');
            if (!$guestSessionId) {
                return response()->json([
                    'status' => false,
                    'message' => 'No guest session provided.',
                ], 401);
            }
            
            $guestSession = GuestSession::where('uuid', $guestSessionId)->first();
            if (!$guestSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid guest session.',
                ], 404);
            }

            $guestSession->extendSession();
            
            return response()->json([
                'status' => true,
                'message' => 'Guest session extended successfully',
                'data' => [
                    'guest_token' => $guestSession->uuid,
                    'expires_at' => \Carbon\Carbon::parse($guestSession->expires_at)->toISOString()
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to extend guest session',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
