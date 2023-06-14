<?php

namespace App\Traits;

use App\Enum\ErrorEnum;
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

    public function getPaymentProcessorResponse(string|bool|null $value, ?string $paymentProcessor): array
    {
        if ($value !== null && $value !== true) {
            $paymentProcessor === PaymentEnum::PAYMENT_PROCESSOR_PAYPAL &&
            $response['error_message'] = $value ?: ErrorEnum::ERROR_MAX_PRICE;
            $paymentProcessor === PaymentEnum::PAYMENT_PROCESSOR_STRIPE &&
            $response['error_message'] = ErrorEnum::ERROR_MIN_PRICE;
        } else {
            $response['error_message'] = ErrorEnum::ERROR_RESPONSE . ' ' . $paymentProcessor;
        }
        $response['success'] = $value === true;
        return $response;
    }
}