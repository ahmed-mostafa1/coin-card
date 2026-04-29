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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'dailycard' => [
        'base_url' => env('DAILYCARD_BASE_URL', 'https://dailycard.shop/UAPI'),
        'api_key'  => env('DAILYCARD_API_KEY'),
        'secret'   => env('DAILYCARD_SECRET'),
        'timeout'  => (int) env('DAILYCARD_TIMEOUT', 25),
        'enabled'  => (bool) env('DAILYCARD_ENABLED', true),
    ],

    'provider_status_sync' => [
        'max_pages' => (int) env('PROVIDER_STATUS_SYNC_MAX_PAGES', 100),
    ],

];
