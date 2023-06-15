<?php

namespace App\Enum;

use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorEnum extends BaseEnum
{
    const PAYMENT_PROCESSOR_PAYPAL = 'paypal';
    const PAYMENT_PROCESSOR_STRIPE = 'stripe';

    const PAYMENT_PROCESSOR_DEFAULT = self::PAYMENT_PROCESSOR_PAYPAL;
}