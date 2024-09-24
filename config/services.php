<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id'  =>'247705689179505',
        'client_secret' => '1a13b97799539caffd162a61b477faa2',
        'redirect' => 'https://www.quick-pick.com/login_facebook'
        
    ],

    'stripe_test' => [
        'api_key' => env('STRIPE_TEST_API_KEY'),
        'api_secret' => env('STRIPE_TEST_API_SECRET'),
    ],
    'stripe_live' => [
        'api_key' => env('STRIPE_LIVE_API_KEY'),
        'api_secret' => env('STRIPE_LIVE_API_SECRET'),
    ],
    'stripe_client_id' => env('STRIPE_CLIENT_ID'),
    'stripe_token_url' => env('STRIPE_TOKEN_URL'),
    'stripe_authorize_url' => env('STRIPE_AUTHORIZE_URL'),
];
