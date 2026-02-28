<?php

return [
    'name' => 'Subscription',
    
    /*
    |--------------------------------------------------------------------------
    | Demo Mode Configuration (Fallback)
    |--------------------------------------------------------------------------
    |
    | This is the fallback setting when business settings are not available.
    | Demo mode is primarily controlled from the Subscription Settings page
    | in the admin panel (Prime Subscriptions > Settings > Payment Gateway).
    |
    | When demo_mode is enabled, payment gateway calls are simulated
    | instead of making real API requests. Perfect for testing the flow
    | without processing actual payments.
    |
    */
    'demo_mode' => env('SUBSCRIPTION_DEMO_MODE', false),
    
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans Configuration
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'billing_cycles' => [
            'monthly' => 30,
            'quarterly' => 90,
            'semi_annual' => 180,
            'annual' => 365,
            'lifetime' => 0,
        ],
        'trial_period_days' => 14,
        'grace_period_days' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Prime Benefits Configuration
    |--------------------------------------------------------------------------
    */
    'prime_benefits' => [
        'fast_delivery' => [
            'enabled' => true,
            'priority_hours' => 24,
        ],
        'exclusive_discounts' => [
            'enabled' => true,
            'discount_percentage' => 10,
        ],
        'reward_points_multiplier' => [
            'enabled' => true,
            'multiplier' => 2,
        ],
        'prime_only_products' => [
            'enabled' => true,
        ],
        'buy_now_pay_later' => [
            'enabled' => true,
            'max_amount' => 500,
            'payment_days' => 30,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'auto_renew_enabled' => true,
        'retry_failed_payments' => 3,
        'retry_interval_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'renewal_reminder_days' => [7, 3, 1],
        'expiry_notification_enabled' => true,
        'payment_confirmation_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Version
    |--------------------------------------------------------------------------
    */
    'version' => '1.0.0',
];
