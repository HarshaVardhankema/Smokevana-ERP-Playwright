<?php

namespace App\Http\Controllers\ECOM;

use App\Models\EcomReferalProgram;
use App\Models\CustomDiscount;
use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Services\CustomDiscountRuleService;

class ReferralProgramController extends Controller
{
    protected $discountService;

    public function __construct(CustomDiscountRuleService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Generate referral code for authenticated customer
     */
    public function generateReferralCode(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            // Check if referral program is enabled
            $business = $this->getBusinessSettings();
            if (!$business->enable_referal_program) {
                return response()->json([
                    'status' => false,
                    'message' => 'Referral program is not enabled'
                ], 403);
            }

            // Check if user already has an active referral code
            $existingReferral = EcomReferalProgram::where('referred_by_customer_id', $user->id)
                ->where('is_used', false)
                ->first();

            if ($existingReferral) {
                return response()->json([
                    'status' => true,
                    'message' => 'Referral code already exists',
                    'data' => [
                        'referral_code' => $existingReferral->coupon_code,
                        'created_at' => $existingReferral->created_at
                    ]
                ]);
            }

            // Generate new referral code
            $referralCode = $this->generateUniqueReferralCode();
            
            // Create referral record
            $referral = EcomReferalProgram::create([
                'referred_by_customer_id' => $user->id,
                'customer_id' => null, // Will be set when someone uses the code
                'discount_id' => $business->referal_program_custom_discount_id,
                'coupon_code' => $referralCode,
                'is_used' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Referral code generated successfully',
                'data' => [
                    'referral_code' => $referralCode,
                    'created_at' => $referral->created_at,
                    'discount_info' => $this->getDiscountInfo($business->referal_program_custom_discount_id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate referral code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral code for authenticated customer
     */
    public function getMyReferralCode(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $referral = EcomReferalProgram::where('referred_by_customer_id', $user->id)
                ->where('is_used', false)
                ->first();

            if (!$referral) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active referral code found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Referral code retrieved successfully',
                'data' => [
                    'referral_code' => $referral->coupon_code,
                    'created_at' => $referral->created_at,
                    'discount_info' => $this->getDiscountInfo($referral->discount_id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve referral code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply referral code (for new customer registration/checkout)
     */
    public function applyReferralCode(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $referralCode = $request->input('referral_code');
            
            // Validate referral coupon
            $validation = $this->discountService->validateCoupon(
                $referralCode,
                [],
                [],
                0,
                $user->id
            );

            if (!$validation['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $validation['message']
                ], 400);
            }

            // Update referral record with the new customer
            if (isset($validation['referral_record'])) {
                $referralRecord = $validation['referral_record'];
                $referralRecord->customer_id = $user->id;
                $referralRecord->is_used = true;
                $referralRecord->used_at = now();
                $referralRecord->save();

                // If "Send to both sides" is enabled, create discount for referrer too
                $business = $this->getBusinessSettings();
                if ($business->referal_sent_to_both_sides) {
                    $this->createReferrerDiscount($referralRecord->referred_by_customer_id);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Referral code applied successfully',
                    'data' => [
                        'discount_applied' => $validation['discount'],
                        'referrer_id' => $referralRecord->referred_by_customer_id,
                        'both_sides_rewarded' => $business->referal_sent_to_both_sides
                    ]
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid referral code'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to apply referral code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral statistics for authenticated customer
     */
    public function getMyReferralStats(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $referralsGiven = EcomReferalProgram::where('referred_by_customer_id', $user->id)->get();
            $referralsReceived = EcomReferalProgram::where('customer_id', $user->id)->get();

            $stats = [
                'referrals_given' => [
                    'total' => $referralsGiven->count(),
                    'used' => $referralsGiven->where('is_used', true)->count(),
                    'pending' => $referralsGiven->where('is_used', false)->count(),
                    'details' => $referralsGiven->map(function ($referral) {
                        return [
                            'referral_code' => $referral->coupon_code,
                            'is_used' => $referral->is_used,
                            'used_at' => $referral->used_at,
                            'referred_customer' => $referral->customer_id ? Contact::find($referral->customer_id) : null
                        ];
                    })
                ],
                'referrals_received' => [
                    'total' => $referralsReceived->count(),
                    'details' => $referralsReceived->map(function ($referral) {
                        return [
                            'referral_code' => $referral->coupon_code,
                            'referred_by' => $referral->referred_by_customer_id ? Contact::find($referral->referred_by_customer_id) : null,
                            'used_at' => $referral->used_at
                        ];
                    })
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Referral statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve referral statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral program settings (public info)
     */
    public function getReferralProgramSettings(Request $request)
    {
        try {
            $business = $this->getBusinessSettings();
            
            if (!$business->enable_referal_program) {
                return response()->json([
                    'status' => false,
                    'message' => 'Referral program is not enabled'
                ], 403);
            }

            $settings = [
                'enabled' => $business->enable_referal_program,
                'available_for_b2b' => $business->referal_available_for_b2b,
                'available_for_b2c' => $business->referal_available_for_b2c,
                'sent_to_both_sides' => $business->referal_sent_to_both_sides,
                'discount_info' => $this->getDiscountInfo($business->referal_program_custom_discount_id),
                'brand_restrictions' => $this->getBrandRestrictions($business)
            ];

            return response()->json([
                'status' => true,
                'message' => 'Referral program settings retrieved successfully',
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve referral program settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate referral code without applying it
     */
    public function validateReferralCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $referralCode = $request->input('referral_code');
            
            $validation = $this->discountService->validateCoupon(
                $referralCode,
                [],
                [],
                0
            );

            return response()->json([
                'status' => $validation['status'],
                'message' => $validation['message'],
                'data' => [
                    'is_referral' => isset($validation['referral_record']),
                    'discount_info' => $validation['discount'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to validate referral code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper methods

    private function generateUniqueReferralCode()
    {
        do {
            $code = 'REF' . strtoupper(Str::random(8));
        } while (EcomReferalProgram::where('coupon_code', $code)->exists());
        
        return $code;
    }

    private function getBusinessSettings()
    {
        // This should be adapted based on your business settings retrieval logic
        return DB::table('business')->first();
    }

    private function getDiscountInfo($discountId)
    {
        if (!$discountId) return null;
        
        $discount = CustomDiscount::find($discountId);
        if (!$discount) return null;

        return [
            'id' => $discount->id,
            'name' => $discount->couponName,
            'type' => $discount->discountType,
            'value' => $discount->discountValue,
            'description' => $discount->description ?? null
        ];
    }

    private function getBrandRestrictions($business)
    {
        // This should be adapted based on your brand restrictions logic
        return $business->referal_brand_list ?? [];
    }

    private function createReferrerDiscount($referrerId)
    {
        // Create a discount for the referrer when "both sides" is enabled
        // This would depend on your specific business logic
        // You might create a new coupon code or credit their account
    }
}
