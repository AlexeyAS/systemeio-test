<?php

namespace App\Enum;

class PaymentProcessor extends BaseEnum
{
    const PAYMENT_CODE_PAYPAL = 'paypal';
    const PAYMENT_CODE_STRIPE = 'stripe';

}