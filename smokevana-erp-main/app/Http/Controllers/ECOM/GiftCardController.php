<?php

namespace App\Http\Controllers\ECOM;

use App\Contact;
use App\GiftCard;
use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class GiftCardController extends Controller
{
    /**
     * Lightweight auth helper for B2B ECOM APIs.
     */
    private function authCheck(Request $request)
    {
        $contact = Auth::guard('api')->user();

        if ($contact) {
            return [
                'status' => true,
                'user' => $contact,
            ];
        }

        return [
            'status' => false,
            'message' => 'User not authenticated',
        ];
    }

    /**
     * POST /api/gift-cards/purchase
     *
     * Create an Amazon-style gift card record after successful payment.
     * This endpoint assumes the payment is already handled by the caller.
     * In a full flow, this would be called from order/payment logic once
     * the transaction is confirmed.
     */
    public function purchase(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => $authData['message'] ?? 'Unauthorized',
            ], 401);
        }

        /** @var Contact $contact */
        $contact = $authData['user'];

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1'],
            'type' => ['nullable', 'in:egift,physical,printable'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $type = $data['type'] ?? 'egift';

        // Basic Amazon-style code (can be customized later)
        $code = GiftCard::generateUniqueCode();

        $giftCard = GiftCard::create([
            'code' => $code,
            'initial_amount' => $data['amount'],
            'balance' => $data['amount'],
            'currency' => strtoupper($data['currency'] ?? 'USD'),
            'purchaser_contact_id' => $contact->id,
            'type' => $type,
            'recipient_name' => $data['recipient_name'] ?? null,
            'recipient_email' => $data['recipient_email'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'active', // assuming payment handled externally and succeeded
            'purchased_at' => now(),
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        // Send gift card code notification via ERP notification templates (auto_send tabs)
        try {
            $businessId = method_exists($contact, 'getAttribute') ? ($contact->getAttribute('business_id') ?? 1) : 1;

            // Custom data object used by NotificationUtil::replaceCustomTags
            $customData = (object) [
                'name' => $contact->name ?? $contact->supplier_business_name ?? 'Customer',
                'brand_id' => $contact->brand_id ?? null,
                'gift_card_code' => $giftCard->code,
                'gift_card_amount' => (float) $giftCard->initial_amount,
                'gift_card_balance' => (float) $giftCard->balance,
                'gift_card_currency' => $giftCard->currency,
                'gift_card_expires_at' => $giftCard->expires_at ? $giftCard->expires_at->toDateString() : null,
                'gift_card_message' => $giftCard->message,
            ];

            // Dispatch using custom notification flow; template key: gift_card_code
            SendNotificationJob::dispatch(
                true, // is_custom
                $businessId,
                'gift_card_code',
                $customData, // passed as "user" / custom_data for tag replacement
                $contact     // actual contact recipient (email, mobile)
            );
        } catch (\Throwable $e) {
            \Log::error('Failed to queue gift card code notification', [
                'gift_card_id' => $giftCard->id ?? null,
                'contact_id' => $contact->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Gift card created successfully. Share the code with the recipient.',
            'data' => [
                'id' => $giftCard->id,
                'code' => $giftCard->code,
                'amount' => (float) $giftCard->initial_amount,
                'balance' => (float) $giftCard->balance,
                'currency' => $giftCard->currency,
                'type' => $giftCard->type,
                'recipient_name' => $giftCard->recipient_name,
                'recipient_email' => $giftCard->recipient_email,
                'status' => $giftCard->status,
                'purchased_at' => optional($giftCard->purchased_at)->toDateTimeString(),
                'expires_at' => optional($giftCard->expires_at)->toDateTimeString(),
            ],
        ]);
    }

    /**
     * POST /api/gift-cards/redeem
     *
     * Redeem a gift card code into the customer's wallet balance.
     * This matches the Amazon-style "add to your account" flow.
     */
    public function redeem(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => $authData['message'] ?? 'Unauthorized',
            ], 401);
        }

        /** @var Contact $contact */
        $contact = $authData['user'];

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:64'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = trim($request->input('code'));

        try {
            $giftCard = GiftCard::where('code', $code)->first();

            if (!$giftCard) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid gift card code.',
                ], 404);
            }

            // Validation checks mirroring the documented flow
            if ($giftCard->status !== 'active') {
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card is not active.',
                ], 400);
            }

            if ($giftCard->balance <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card has already been fully redeemed.',
                ], 400);
            }

            if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This gift card has expired.',
                ], 400);
            }

            DB::beginTransaction();

            // Add remaining balance to customer wallet
            $amountToCredit = (float) $giftCard->balance;

            /** @var Contact $freshContact */
            $freshContact = Contact::lockForUpdate()->find($contact->id);

            if (!$freshContact) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found for redemption.',
                ], 404);
            }

            $currentBalance = (float) ($freshContact->balance ?? 0);
            $newBalance = $currentBalance + $amountToCredit;

            $freshContact->update([
                'balance' => $newBalance,
            ]);

            // Mark gift card as redeemed
            $giftCard->update([
                'balance' => 0,
                'status' => 'redeemed',
                'redeemed_by_contact_id' => $freshContact->id,
                'redeemed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Gift card redeemed successfully. Balance added to your wallet.',
                'data' => [
                    'redeemed_amount' => $amountToCredit,
                    'wallet_balance' => $newBalance,
                    'gift_card' => [
                        'id' => $giftCard->id,
                        'code' => $giftCard->code,
                        'status' => $giftCard->status,
                        'redeemed_at' => optional($giftCard->redeemed_at)->toDateTimeString(),
                    ],
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to redeem gift card.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/gift-cards
     *
     * List all gift cards visible to the authenticated customer.
     * This includes:
     * - Gift cards purchased by the customer via API
     * - Gift cards created by admin/ERP (visible to all customers)
     * Optional filters: status, type
     */
    public function index(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => $authData['message'] ?? 'Unauthorized',
            ], 401);
        }

        /** @var Contact $contact */
        $contact = $authData['user'];

        // Show gift cards that are either:
        // 1. Assigned to this customer (purchaser_contact_id matches)
        // 2. Created by admin/ERP (created_by_user_id is not null) - visible to all customers
        $query = GiftCard::where(function($q) use ($contact) {
                $q->where('purchaser_contact_id', $contact->id)
                  ->orWhereNotNull('created_by_user_id'); // Admin-created cards visible to all
            })
            ->orderBy('created_at', 'desc');

        // Optional filters
        if ($request->has('status')) {
            $status = $request->input('status');
            if (in_array($status, ['pending_payment', 'active', 'redeemed', 'expired', 'cancelled'])) {
                $query->where('status', $status);
            }
        } else {
            // Default: only show active gift cards with balance if no status filter
            $query->where('status', 'active')
                  ->where('balance', '>', 0);
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            if (in_array($type, ['egift', 'physical', 'printable'])) {
                $query->where('type', $type);
            }
        }

        // Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $giftCards = $query->paginate($perPage);

        $formattedCards = collect($giftCards->items())->map(function ($card) {
            return [
                'id' => $card->id,
                // Code is hidden - will be sent via email when selected
                'initial_amount' => (float) $card->initial_amount,
                'balance' => (float) $card->balance,
                'currency' => $card->currency,
                'type' => $card->type,
                'recipient_name' => $card->recipient_name,
                'recipient_email' => $card->recipient_email,
                'message' => $card->message,
                'image' => $card->image ? asset('uploads/img/' . $card->image) : null,
                'status' => $card->status,
                'purchased_at' => optional($card->purchased_at)->toDateTimeString(),
                'redeemed_at' => optional($card->redeemed_at)->toDateTimeString(),
                'expires_at' => optional($card->expires_at)->toDateTimeString(),
                'is_expired' => $card->expires_at ? $card->expires_at->isPast() : false,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Gift cards retrieved successfully',
            'data' => [
                'gift_cards' => $formattedCards->values()->all(),
                'pagination' => [
                    'current_page' => $giftCards->currentPage(),
                    'per_page' => $giftCards->perPage(),
                    'total' => $giftCards->total(),
                    'last_page' => $giftCards->lastPage(),
                    'from' => $giftCards->firstItem(),
                    'to' => $giftCards->lastItem(),
                ],
            ],
        ]);
    }

    /**
     * GET /api/gift-cards/{id}
     *
     * Get details of a specific gift card.
     * Accessible if:
     * - Gift card belongs to authenticated user, OR
     * - Gift card was created by admin/ERP (visible to all customers)
     */
    public function show(Request $request, $id)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => $authData['message'] ?? 'Unauthorized',
            ], 401);
        }

        /** @var Contact $contact */
        $contact = $authData['user'];

        // Allow access to gift cards that are either:
        // 1. Assigned to this customer (purchaser_contact_id matches)
        // 2. Created by admin/ERP (created_by_user_id is not null) - visible to all customers
        $giftCard = GiftCard::where('id', $id)
            ->where(function($q) use ($contact) {
                $q->where('purchaser_contact_id', $contact->id)
                  ->orWhereNotNull('created_by_user_id'); // Admin-created cards visible to all
            })
            ->first();

        if (!$giftCard) {
            return response()->json([
                'status' => false,
                'message' => 'Gift card not found or access denied.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Gift card retrieved successfully',
            'data' => [
                'id' => $giftCard->id,
                // Code is hidden - will be sent via email when selected
                'initial_amount' => (float) $giftCard->initial_amount,
                'balance' => (float) $giftCard->balance,
                'currency' => $giftCard->currency,
                'type' => $giftCard->type,
                'recipient_name' => $giftCard->recipient_name,
                'recipient_email' => $giftCard->recipient_email,
                'message' => $giftCard->message,
                'image' => $giftCard->image ? asset('uploads/img/' . $giftCard->image) : null,
                'status' => $giftCard->status,
                'purchased_at' => optional($giftCard->purchased_at)->toDateTimeString(),
                'redeemed_at' => optional($giftCard->redeemed_at)->toDateTimeString(),
                'expires_at' => optional($giftCard->expires_at)->toDateTimeString(),
                'is_expired' => $giftCard->expires_at ? $giftCard->expires_at->isPast() : false,
                'redeemed_by' => $giftCard->redeemed_by_contact_id ? [
                    'contact_id' => $giftCard->redeemed_by_contact_id,
                    'name' => $giftCard->redeemer->name ?? null,
                    'email' => $giftCard->redeemer->email ?? null,
                ] : null,
            ],
        ]);
    }

    /**
     * GET /api/gift-cards/redeemed
     *
     * List all gift cards redeemed by the authenticated customer.
     */
    public function redeemed(Request $request)
    {
        $authData = $this->authCheck($request);
        if (!$authData['status']) {
            return response()->json([
                'status' => false,
                'message' => $authData['message'] ?? 'Unauthorized',
            ], 401);
        }

        /** @var Contact $contact */
        $contact = $authData['user'];

        $query = GiftCard::where('redeemed_by_contact_id', $contact->id)
            ->where('status', 'redeemed')
            ->orderBy('redeemed_at', 'desc');

        // Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $giftCards = $query->paginate($perPage);

        $formattedCards = collect($giftCards->items())->map(function ($card) {
            return [
                'id' => $card->id,
                'code' => $card->code,
                'initial_amount' => (float) $card->initial_amount,
                'redeemed_amount' => (float) $card->initial_amount, // Full amount since balance is 0 after redemption
                'currency' => $card->currency,
                'type' => $card->type,
                'status' => $card->status,
                'redeemed_at' => optional($card->redeemed_at)->toDateTimeString(),
                'purchased_at' => optional($card->purchased_at)->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Redeemed gift cards retrieved successfully',
            'data' => [
                'gift_cards' => $formattedCards->values()->all(),
                'pagination' => [
                    'current_page' => $giftCards->currentPage(),
                    'per_page' => $giftCards->perPage(),
                    'total' => $giftCards->total(),
                    'last_page' => $giftCards->lastPage(),
                    'from' => $giftCards->firstItem(),
                    'to' => $giftCards->lastItem(),
                ],
            ],
        ]);
    }

}

