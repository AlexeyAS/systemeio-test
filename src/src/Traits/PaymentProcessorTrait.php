<?php

namespace App\Traits;

use App\Enum\PaymentEnum;
use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;

trait PaymentProcessorTrait
{
    public function getPaymentProcessorObject(?string $value = null):
    StripePaymentProcessor|PaypalPaymentProcessor
    {
        return match ($value) {
            PaymentEnum::PAYMENT_PROCESSOR_STRIPE => new StripePaymentProcessor(),
//          PaymentEnum::PAYMENT_PROCESSOR_PAYPAL,
//          PaymentEnum::PAYMENT_PROCESSOR_DEFAULT => new PaypalPaymentProcessor(),
            default => new PaypalPaymentProcessor()
        };
    }

    public function getPaymentProcessorMethod(?string $value = null): string
    {
        if (isset(PaymentEnum::PAYMENT_PROCESSOR_METHODS[$value])) {
            return PaymentEnum::PAYMENT_PROCESSOR_METHODS[$value];
        }
        return PaymentEnum::PAYMENT_PROCESSOR_METHODS[PaymentEnum::PAYMENT_PROCESSOR_DEFAULT];
    }
}