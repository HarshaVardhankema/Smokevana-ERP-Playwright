<?php

namespace App\Http\Controllers;

use App\CustomerPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPaymentMethodController extends Controller
{
    /**
     * Get all saved payment methods for the authenticated customer.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $methods = CustomerPaymentMethod::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'cardholder_name',
                'brand',
                'last4',
                'exp_month',
                'exp_year',
                'billing_zip',
                'is_default',
                'created_at',
            ]);

        return response()->json([
            'status' => true,
            'data' => $methods,
        ]);
    }

    /**
     * Store a new customer credit/debit card.
     *
     * NOTE: This endpoint intentionally does NOT store full PAN or CVV,
     * only the last 4 digits and expiry. For real payments, you should
     * tokenize the card with a PCI-compliant provider and pass the token
     * via the `token` field.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $data = $request->validate([
            'cardholder_name' => 'required|string|max:191',
            'card_number'     => 'required|string|min:12|max:19',
            'brand'           => 'nullable|string|max:50',
            'exp_month'       => 'required|integer|min:1|max:12',
            'exp_year'        => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 20),
            'billing_zip'     => 'nullable|string|max:20',
            'token'           => 'nullable|string|max:255',
            'is_default'      => 'sometimes|boolean',
        ]);

        // Derive last4 and never store full card number or CVV.
        $cardNumber = preg_replace('/\D/', '', $data['card_number']);
        $last4 = substr($cardNumber, -4);

        // If this card is marked as default, unset previous defaults.
        $isDefault = (bool) ($data['is_default'] ?? false);
        if ($isDefault) {
            CustomerPaymentMethod::where('user_id', $user->id)
                ->update(['is_default' => false]);
        }

        $paymentMethod = CustomerPaymentMethod::create([
            'user_id'        => $user->id,
            'cardholder_name'=> $data['cardholder_name'],
            'brand'          => $data['brand'] ?? null,
            'last4'          => $last4,
            'exp_month'      => $data['exp_month'],
            'exp_year'       => $data['exp_year'],
            'billing_zip'    => $data['billing_zip'] ?? null,
            'token'          => $data['token'] ?? null,
            'is_default'     => $isDefault,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Card saved successfully.',
            'data' => [
                'id' => $paymentMethod->id,
                'cardholder_name' => $paymentMethod->cardholder_name,
                'brand' => $paymentMethod->brand,
                'last4' => $paymentMethod->last4,
                'exp_month' => $paymentMethod->exp_month,
                'exp_year' => $paymentMethod->exp_year,
                'billing_zip' => $paymentMethod->billing_zip,
                'is_default' => $paymentMethod->is_default,
            ],
        ], 201);
    }
}

