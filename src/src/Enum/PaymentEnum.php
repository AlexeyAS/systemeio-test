<?php

namespace App\Enum;

use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;

class PaymentEnum extends BaseEnum
{
    const NAME = 'payment';
    const PAYMENT_PROCESSOR_PAYPAL = 'paypal';
    const PAYMENT_PROCESSOR_STRIPE = 'stripe';
    const PAYMENT_PROCESSOR_METHODS = [
        self::PAYMENT_PROCESSOR_PAYPAL => 'pay',
        self::PAYMENT_PROCESSOR_STRIPE => 'processPayment'
    ];

    const PAYMENT_PROCESSOR_DEFAULT = self::PAYMENT_PROCESSOR_PAYPAL;
}