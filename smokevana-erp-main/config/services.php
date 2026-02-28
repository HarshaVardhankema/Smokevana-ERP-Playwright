<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'nmi' => [
        'security' => env('NMI_SECURITY_KEY'),
        'transactionkey' => env('NMI_TOKENIZATION_KEY'),
    ],
    'authorizenet' => [
        'login_id' => env('AUTHORIZE_LOGIN_ID'),
        'transaction_key' => env('AUTHORIZE_TRANSACTION_KEY'),
    ],
    'firebase' => [
        'server_key' => env('FIREBASE_SERVER_KEY'),
        'key' => env('FCM_KEY'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),
    ],

    // B2B storefront configuration
    'b2b' => [
        // Default B2B location id (used across CatalogController)
        'location_id' => env('B2B_LOCATION', 1),

        // Public web price group for non‑authenticated B2B users
        // This should be set to the ID of the selling_price_group
        // that represents your web/B2B prices (e.g. "Web Price").
        'public_price_group_id' => env('B2B_PUBLIC_PRICE_GROUP_ID'),

        // Prime storefront: when request Referer/Origin matches this host, use prime_price_group_id for ad_price/pricing.
        'prime_storefront_host' => env('B2B_PRIME_STOREFRONT_HOST', 'prime.smokevana.com'),
        // Price group ID for Prime storefront (e.g. "Prime" group with 90 in Group Prices). Must match selling_price_groups.id.
        'prime_price_group_id' => env('B2B_PRIME_PRICE_GROUP_ID'),
    ],
    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ]
];
