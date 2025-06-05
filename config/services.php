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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'api_endpoint' => env('WHATSAPP_API_ENDPOINT', 'https://us-central1-pristine-nomad-264707.cloudfunctions.net/SendTemplateWhatsappV2'),
        'from_mobile_no' => env('WHATSAPP_FROM_MOBILE_NO', '918200599525'),
        'fb_token' => env('WHATSAPP_FB_TOKEN', 'EAB8uAArmndABO4ewJWGktpcbuBfZAiFV6XUIOYdEr9FR8L05gprGxlb1Sx9DNFwY9q1A8f3NcZAcs4b7DhE9nZBT5ZACPLxNIL3J0vTNHpvuam7zfrsHcwYilrmVybIowxY7xWapORJIUoPMcqWykvnFjgsnBqvZBURIzKEkUMZAfdPlQJdQ0b6W63W2sBgJ6i'),
        'employee_name' => env('WHATSAPP_EMPLOYEE_NAME', 'emp_name'),
        'hostel_location' => env('WHATSAPP_HOSTEL_LOCATION', 'Navrangpura'),
        'hostel_name' => env('WHATSAPP_HOSTEL_NAME', 'LN Stay'),
        'contact_number' => env('WHATSAPP_CONTACT_NUMBER', '9876543210'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
        'content_sid' => env('TWILIO_CONTENT_SID', 'HXa75b7536caffe4e4828226edcfafe8e1'),
    ],

];
