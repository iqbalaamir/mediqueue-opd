<?php

namespace App\Domain\Enums;

enum PaymentGateway: string
{
    case Demo = 'demo';
    case Razorpay = 'razorpay';
    case Stripe = 'stripe';
    case Manual = 'manual';
}
