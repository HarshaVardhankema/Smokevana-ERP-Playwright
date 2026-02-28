<?php

namespace App\Http\Controllers\ECOM;

use App\GuestSession;
use App\GuestCartItem;
use App\CartItem;
use App\Http\Controllers\Controller;
use App\Contact;
use App\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class B2bGuestController extends Controller
{
    /**
     * Create a new B2B guest session
     */
    public function createSession(Request $request)
    {
        try {
            // Use configured B2B location
            $locationId = config('services.b2b.location_id', 1);

            // Resolve a default brand for this B2B location
            $brand = Brands::where('location_id', $locationId)
                ->where('visibility', 'public')
                ->first();

            if (!$brand) {
                $brand = Brands::where('location_id', $locationId)->first();
            }

            if (!$brand) {
                return response()->json([
                    'status' => false,
                    'message' => 'No brand configured for B2B location.',
                ], 500);
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
                    'brand_name' => $brand->slug,
                ],
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create guest session',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate B2B guest session
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

            $guestSession = GuestSession::where('uuid', $guestSessionUuid)->first();

            if (!$guestSession || !$guestSession->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired guest session.',
                ], 401);
            }

            // Optionally extend if close to expiry (7 days)
            $expiresAt = \Carbon\Carbon::parse($guestSession->expires_at);
            $now = now();

            if ($expiresAt->diffInDays($now, false) < 7) {
                $guestSession->expires_at = $now->copy()->addDays(7);
                $guestSession->save();
            }

            $brand = $guestSession->brand;

            return response()->json([
                'status' => true,
                'message' => 'Guest session is valid',
                'data' => [
                    'guest_token' => $guestSession->uuid,
                    'expires_at' => \Carbon\Carbon::parse($guestSession->expires_at)->toISOString(),
                    'location_id' => $guestSession->location_id,
                    'brand_name' => $brand ? $brand->slug : null,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to validate guest session',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert B2B guest session to registered customer
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

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:contacts,email',
                'password' => 'required|min:8|confirmed',
                'first_name' => 'required|string|max:50',
                'last_name' => 'nullable|string|max:50',
                'mobile' => 'required|string|min:10|max:15',
                'customer_group_id' => 'nullable|integer|exists:customer_groups,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $brand = $guestSession->brand;

            $user = Contact::create([
                'business_id' => $brand ? $brand->business_id : 1,
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
                'customer_group_id' => $request->input('customer_group_id'),
                'created_by' => 1,
            ]);

            // Transfer guest cart to user cart if using GuestCartItem
            $guestCartItems = GuestCartItem::where('guest_session_id', $guestSession->id)->get();

            foreach ($guestCartItems as $guestItem) {
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

            GuestCartItem::where('guest_session_id', $guestSession->id)->delete();
            $guestSession->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Guest session converted to user account successfully',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'message' => 'Your cart has been transferred to your new account.',
                ],
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to convert guest session',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Extend B2B guest session
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
                    'expires_at' => \Carbon\Carbon::parse($guestSession->expires_at)->toISOString(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to extend guest session',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

