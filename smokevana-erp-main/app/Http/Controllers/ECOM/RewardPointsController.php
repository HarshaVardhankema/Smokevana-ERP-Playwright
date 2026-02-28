<?php

namespace App\Http\Controllers\ECOM;

use App\Business;
use App\Cart;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardPointsController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Get authenticated customer (Contact) or 401.
     */
    private function authContact()
    {
        $contact = Auth::guard('api')->user();
        if (!$contact) {
            return null;
        }
        return $contact;
    }

    /**
     * GET /api/customer/reward-points
     * Balance + redemption rules summary for the authenticated customer.
     */
    public function index(Request $request)
    {
        $contact = $this->authContact();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $business = Business::find($contact->business_id);
        $enabled = $business && (int) $business->enable_rp === 1;

        // Use contact.total_rp as the source of truth for balance so that:
        // - Transaction-based earning/redeeming (via updateCustomerRewardPoints) is reflected
        // - Manually added points (e.g. customer:add-reward-points) also appear in the API
        $totalExpired = (int) ($contact->total_rp_expired ?? 0);
        $balance = max(0, (int) ($contact->total_rp ?? 0));
        $totalUsed = (int) ($contact->total_rp_used ?? 0);

        $rules = null;
        $redeemPreview = null;
        $cartApplied = null;
        if ($enabled) {
            $rules = [
                'earning' => [
                    'amount_per_point' => (float) ($business->amount_for_unit_rp ?? 1),
                    'min_order_for_earning' => (float) ($business->min_order_total_for_rp ?? 0),
                    'max_points_per_order' => $business->max_rp_per_order ? (int) $business->max_rp_per_order : null,
                ],
                'redemption' => [
                    'points_per_unit_currency' => (float) ($business->redeem_amount_per_unit_rp ?? 0.01),
                    'min_points_to_redeem' => $business->min_redeem_point ? (int) $business->min_redeem_point : null,
                    'max_points_per_redemption' => $business->max_redeem_point ? (int) $business->max_redeem_point : null,
                    'min_order_for_redeem' => (float) ($business->min_order_total_for_redeem ?? 0),
                ],
            ];
            $redeemPreview = $this->transactionUtil->getRewardRedeemDetails($contact->business_id, $contact->id);
            // If cart has reward points applied, include points_used and dollars_discount
            $cart = Cart::where('user_id', $contact->id)->first();
            if ($cart && (int) ($cart->reward_points_to_redeem ?? 0) > 0) {
                $amountPerPoint = (float) ($business->redeem_amount_per_unit_rp ?? 0.01);
                $pointsUsed = (int) $cart->reward_points_to_redeem;
                $dollarsDiscount = round($pointsUsed * $amountPerPoint, 2);
                $cartApplied = [
                    'points_used' => $pointsUsed,
                    'dollars_discount' => $dollarsDiscount,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'enabled' => $enabled,
                'balance' => $balance,
                'total_used' => $totalUsed,
                'total_expired' => $totalExpired,
                'rules' => $rules,
                'redeem_preview' => $redeemPreview ? [
                    'max_redeemable_points' => (int) $redeemPreview['points'],
                    'equivalent_amount' => (float) $redeemPreview['amount'],
                ] : null,
                'cart_applied' => $cartApplied,
            ],
        ]);
    }

    /**
     * GET /api/customer/reward-points/rules
     * Public rules only (for display when not logged in or for UI).
     */
    public function rules(Request $request)
    {
        $contact = $this->authContact();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $business = Business::find($contact->business_id);
        $enabled = $business && (int) $business->enable_rp === 1;

        if (!$enabled) {
            return response()->json([
                'status' => true,
                'data' => ['enabled' => false, 'earning' => null, 'redemption' => null],
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'enabled' => true,
                'earning' => [
                    'amount_per_point' => (float) ($business->amount_for_unit_rp ?? 1),
                    'min_order_for_earning' => (float) ($business->min_order_total_for_rp ?? 0),
                    'max_points_per_order' => $business->max_rp_per_order ? (int) $business->max_rp_per_order : null,
                ],
                'redemption' => [
                    'points_per_unit_currency' => (float) ($business->redeem_amount_per_unit_rp ?? 0.01),
                    'min_points_to_redeem' => $business->min_redeem_point ? (int) $business->min_redeem_point : null,
                    'max_points_per_redemption' => $business->max_redeem_point ? (int) $business->max_redeem_point : null,
                    'min_order_for_redeem' => (float) ($business->min_order_total_for_redeem ?? 0),
                ],
            ],
        ]);
    }

    /**
     * GET /api/customer/reward-points/history
     * Paginated list of transactions that earned or redeemed points.
     */
    public function history(Request $request)
    {
        $contact = $this->authContact();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $perPage = max(1, min(50, (int) $request->query('per_page', 15)));

        $query = Transaction::where('contact_id', $contact->id)
            ->where('business_id', $contact->business_id)
            ->where('type', 'sell')
            ->where(function ($q) {
                $q->where('rp_earned', '>', 0)->orWhere('rp_redeemed', '>', 0);
            })
            ->select(
                'id',
                'invoice_no',
                'transaction_date',
                'final_total',
                'rp_earned',
                'rp_redeemed',
                'rp_redeemed_amount',
                'status'
            )
            ->orderByDesc('transaction_date');

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($tx) {
            return [
                'id' => $tx->id,
                'invoice_no' => $tx->invoice_no,
                'transaction_date' => $tx->transaction_date,
                'final_total' => (float) $tx->final_total,
                'points_earned' => (int) ($tx->rp_earned ?? 0),
                'points_redeemed' => (int) ($tx->rp_redeemed ?? 0),
                'redeemed_amount' => (float) ($tx->rp_redeemed_amount ?? 0),
                'status' => $tx->status,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'current_page' => $paginator->currentPage(),
                'data' => $items,
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * GET /api/customer/reward-points/redeem-preview
     * How much discount can be applied for current balance (for checkout UI).
     */
    public function redeemPreview(Request $request)
    {
        $contact = $this->authContact();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $details = $this->transactionUtil->getRewardRedeemDetails($contact->business_id, $contact->id);

        return response()->json([
            'status' => true,
            'data' => [
                'points_available' => (int) $details['points'],
                'equivalent_amount' => (float) $details['amount'],
            ],
        ]);
    }

    /**
     * POST /api/customer/reward-points/apply
     * Apply reward points by amount (e.g. $10) or by points. Returns how much discount
     * will be applied and balance after, so the customer can use only part of their rewards.
     * Request body: amount (dollars) and/or points; optional order_total/cart_total to cap discount.
     * Response: points_to_redeem, amount_discount, balance_after_points, balance_after_amount.
     */
    public function apply(Request $request)
    {
        $contact = $this->authContact();
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $business = Business::find($contact->business_id);
        if (!$business || (int) $business->enable_rp !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Reward points are not enabled.',
            ], 400);
        }

        $amountPerPoint = (float) ($business->redeem_amount_per_unit_rp ?? 0.01);
        if ($amountPerPoint <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Reward redemption rate is not configured.',
            ], 400);
        }

        $redeemDetails = $this->transactionUtil->getRewardRedeemDetails($contact->business_id, $contact->id);
        $availablePoints = (int) ($redeemDetails['points'] ?? 0);
        $equivalentAmount = (float) ($redeemDetails['amount'] ?? 0);

        if ($availablePoints <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'You have no reward points available to use.',
                'data' => [
                    'points_available' => 0,
                    'equivalent_amount' => 0,
                    'points_to_redeem' => 0,
                    'amount_discount' => 0,
                    'balance_after_points' => 0,
                    'balance_after_amount' => 0,
                ],
            ], 422);
        }

        $requestAmount = $request->input('amount') !== null && $request->input('amount') !== '' ? (float) $request->input('amount') : null;
        $requestPoints = $request->input('points') !== null && $request->input('points') !== '' ? (int) $request->input('points') : null;
        $orderTotal = $request->input('order_total') !== null && $request->input('order_total') !== '' ? (float) $request->input('order_total') : null;
        if ($orderTotal === null) {
            $orderTotal = $request->input('cart_total') !== null && $request->input('cart_total') !== '' ? (float) $request->input('cart_total') : null;
        }

        $pointsToRedeem = 0;
        if ($requestAmount !== null && $requestAmount > 0) {
            $pointsToRedeem = (int) round($requestAmount / $amountPerPoint);
            if ($pointsToRedeem > $availablePoints) {
                $pointsToRedeem = $availablePoints;
            }
        } elseif ($requestPoints !== null && $requestPoints > 0) {
            $pointsToRedeem = min($requestPoints, $availablePoints);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Please provide amount (in currency) or points to use.',
            ], 422);
        }

        $minRedeemPoint = $business->min_redeem_point ? (int) $business->min_redeem_point : null;
        $maxRedeemPoint = $business->max_redeem_point ? (int) $business->max_redeem_point : null;
        $minOrderForRedeem = $business->min_order_total_for_redeem !== null && $business->min_order_total_for_redeem !== ''
            ? (float) $business->min_order_total_for_redeem
            : null;

        if ($pointsToRedeem > 0 && $minRedeemPoint !== null && $pointsToRedeem < $minRedeemPoint) {
            $minAmount = round($minRedeemPoint * $amountPerPoint, 2);
            return response()->json([
                'status' => false,
                'message' => 'Minimum redemption is ' . $minRedeemPoint . ' points (equivalent to ' . $minAmount . ').',
                'data' => [
                    'min_points' => $minRedeemPoint,
                    'min_amount' => $minAmount,
                    'points_available' => $availablePoints,
                    'equivalent_amount' => $equivalentAmount,
                ],
            ], 422);
        }

        if ($pointsToRedeem > 0 && $maxRedeemPoint !== null && $pointsToRedeem > $maxRedeemPoint) {
            $pointsToRedeem = $maxRedeemPoint;
        }

        if ($pointsToRedeem > 0 && $minOrderForRedeem !== null && $minOrderForRedeem > 0) {
            if ($orderTotal === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order total (order_total or cart_total) is required to validate redemption. Minimum order total to redeem is ' . $minOrderForRedeem . '.',
                    'data' => ['min_order_total_for_redeem' => $minOrderForRedeem],
                ], 422);
            }
            if ($orderTotal < $minOrderForRedeem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order total must be at least ' . $minOrderForRedeem . ' to redeem reward points.',
                    'data' => [
                        'min_order_total_for_redeem' => $minOrderForRedeem,
                        'order_total' => $orderTotal,
                    ],
                ], 422);
            }
        }

        if ($pointsToRedeem <= 0) {
            $saveToCart = filter_var($request->input('save_to_cart', true), FILTER_VALIDATE_BOOLEAN);
            if ($saveToCart) {
                Cart::where('user_id', $contact->id)->update(['reward_points_to_redeem' => 0]);
            }
            $zeroData = [
                'points_available' => $availablePoints,
                'equivalent_amount' => $equivalentAmount,
                'points_to_redeem' => 0,
                'amount_discount' => 0,
                'balance_after_points' => $availablePoints,
                'balance_after_amount' => round($availablePoints * $amountPerPoint, 2),
                'total_after_discount' => $orderTotal,
            ];
            if ($orderTotal !== null) {
                $zeroData['cart_total'] = round($orderTotal, 2);
                $zeroData['order_total'] = round($orderTotal, 2);
            }
            return response()->json(['status' => true, 'data' => $zeroData]);
        }

        $amountDiscount = round($pointsToRedeem * $amountPerPoint, 2);
        if ($orderTotal !== null && $orderTotal > 0 && $amountDiscount > $orderTotal) {
            $amountDiscount = round($orderTotal, 2);
            $pointsToRedeem = (int) round($amountDiscount / $amountPerPoint);
        }

        $balanceAfterPoints = $availablePoints - $pointsToRedeem;
        $balanceAfterAmount = round($balanceAfterPoints * $amountPerPoint, 2);
        $totalAfterDiscount = $orderTotal !== null ? max(0, round($orderTotal - $amountDiscount, 2)) : null;

        $data = [
            'points_available' => $availablePoints,
            'equivalent_amount' => $equivalentAmount,
            'points_to_redeem' => $pointsToRedeem,
            'amount_discount' => $amountDiscount,
            'points_used' => $pointsToRedeem,
            'dollars_discount' => $amountDiscount,
            'balance_after_points' => $balanceAfterPoints,
            'balance_after_amount' => $balanceAfterAmount,
            'total_after_discount' => $totalAfterDiscount,
            'rules' => [
                'min_points_to_redeem' => $minRedeemPoint,
                'max_points_to_redeem' => $maxRedeemPoint,
                'min_order_total_for_redeem' => $minOrderForRedeem,
                'amount_per_point' => $amountPerPoint,
            ],
        ];
        // Echo back cart/order total so the client can show: "Subtotal X, Reward -Y, Total Z"
        if ($orderTotal !== null) {
            $data['cart_total'] = round($orderTotal, 2);
            $data['order_total'] = round($orderTotal, 2);
        }

        // Store reward points to redeem on cart so GET /api/cart returns discounted total without query params
        $saveToCart = filter_var($request->input('save_to_cart', true), FILTER_VALIDATE_BOOLEAN);
        if ($saveToCart) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $contact->id],
                ['user_id' => $contact->id, 'reward_points_to_redeem' => $pointsToRedeem]
            );
            $cart->reward_points_to_redeem = $pointsToRedeem;
            $cart->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Reward points applied. Use points_to_redeem when placing order or loading cart.',
            'data' => $data,
        ]);
    }
}
