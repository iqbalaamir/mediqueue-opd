<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Branding
    |--------------------------------------------------------------------------
    */
    'name' => env('HOSPITAL_APP_NAME', 'MediQueue OPD'),
    'tagline' => env('HOSPITAL_APP_TAGLINE', 'Smart Hospital Queue Management'),
    'brand_color' => env('HOSPITAL_BRAND_COLOR', '#0f766e'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp / outreach contact (manual promotion)
    |--------------------------------------------------------------------------
    */
    'outreach' => [
        'contact_name' => env('OUTREACH_CONTACT_NAME', 'Shashank'),
        'contact_email' => env('OUTREACH_CONTACT_EMAIL', 'sunnyns60@gmail.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */
    'booking' => [
        'otp_required' => env('BOOKING_OTP_REQUIRED', false),
        'otp_ttl_minutes' => (int) env('BOOKING_OTP_TTL_MINUTES', 10),
        'advance_booking_days' => (int) env('BOOKING_ADVANCE_DAYS', 7),
        'appointment_number_prefix' => env('BOOKING_APPOINTMENT_PREFIX', 'MQ'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Tracking
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'poll_interval_ms' => (int) env('QUEUE_POLL_INTERVAL_MS', 5000),
        'eta_buffer_minutes' => (int) env('QUEUE_ETA_BUFFER_MINUTES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Defaults (hospital-level overrides in DB)
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'default_hold_minutes' => (int) env('PAYMENT_HOLD_MINUTES', 15),
        'default_advance_percent' => (int) env('PAYMENT_ADVANCE_PERCENT', 50),
        'demo_gateway_enabled' => env('PAYMENT_DEMO_GATEWAY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'channels' => [
            'database' => true,
            'sms' => env('NOTIFY_SMS_ENABLED', false),
            'whatsapp' => env('NOTIFY_WHATSAPP_ENABLED', false),
            'push' => env('NOTIFY_PUSH_ENABLED', false),
        ],
        'providers' => [
            'sms' => env('NOTIFY_SMS_DRIVER', 'log'),
            'whatsapp' => env('NOTIFY_WHATSAPP_DRIVER', 'log'),
            'push' => env('NOTIFY_PUSH_DRIVER', 'log'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (requests per minute)
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'booking_store' => (int) env('RATE_LIMIT_BOOKING_STORE', 10),
        'fee_quote' => (int) env('RATE_LIMIT_FEE_QUOTE', 60),
        'otp_send' => (int) env('RATE_LIMIT_OTP_SEND', 5),
        'otp_verify' => (int) env('RATE_LIMIT_OTP_VERIFY', 20),
        'payment_demo' => (int) env('RATE_LIMIT_PAYMENT_DEMO', 30),
        'admin_write' => (int) env('RATE_LIMIT_ADMIN_WRITE', 60),
    ],

];
