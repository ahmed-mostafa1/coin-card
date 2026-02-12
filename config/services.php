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

    'marketcard99' => [
        'base_url' => env('MARKETCARD99_BASE_URL', 'https://app.market-card99.com'),
        'token' => env('MARKETCARD99_TOKEN'),
        'username' => env('MARKETCARD99_USERNAME'),
        'password' => env('MARKETCARD99_PASSWORD'),
        'token_cache_ttl_minutes' => (int) env('MARKETCARD99_TOKEN_CACHE_TTL_MINUTES', 1440),
        'timeout' => (int) env('MARKETCARD99_TIMEOUT', 25),
        'retry_times' => (int) env('MARKETCARD99_RETRY_TIMES', 2),
        'retry_delay_ms' => (int) env('MARKETCARD99_RETRY_DELAY_MS', 500),
    ],

];
