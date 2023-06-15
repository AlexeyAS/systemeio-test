<?php

namespace App\Factory;

use App\Enum\PaymentProcessorEnum;
use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;
use RuntimeException;


class PaymentProcessorFactory
{
    /**
     * Получаем требуемый объект PaymentProcessor
     * @param string|null $value
     * @return StripePaymentProcessor|PaypalPaymentProcessor
     */
    public function createObject(?string $value = null): StripePaymentProcessor|PaypalPaymentProcessor
    {
        return match ($value) {
            PaymentProcessorEnum::PAYMENT_PROCESSOR_STRIPE => new StripePaymentProcessor(),
            PaymentProcessorEnum::PAYMENT_PROCESSOR_PAYPAL,
            PaymentProcessorEnum::PAYMENT_PROCESSOR_DEFAULT => new PaypalPaymentProcessor(),
            default => throw new RuntimeException("Unknown Payment Processor Class")
        };
    }

    /**
     * Получаем требуемый метод для вызова из объекта
     * @param string|null $value
     * @return string
     */
    public function getMethod(?string $value = null): string
    {
        return match ($value) {
            PaymentProcessorEnum::PAYMENT_PROCESSOR_STRIPE => 'processPayment',
            PaymentProcessorEnum::PAYMENT_PROCESSOR_PAYPAL,
            PaymentProcessorEnum::PAYMENT_PROCESSOR_DEFAULT => 'pay',
            default => throw new RuntimeException("Unknown Payment Processor Method")
        };
    }

}