<?php

namespace App\Traits;

use App\Enum\ErrorEnum;
use App\Enum\PaymentProcessorEnum;
use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;

trait PaymentProcessorTrait
{
    public function getPaymentProcessorResponse(string|bool|null $value, ?string $paymentProcessor): array
    {
        if ($value === null) {
            $response['error_message'] = ErrorEnum::ERROR_LOST_RESPONSE . ' ' . $paymentProcessor;
        } elseif ($value !== true) {
            $paymentProcessor === PaymentProcessorEnum::PAYMENT_PROCESSOR_PAYPAL &&
            $response['error_message'] = $value ?: ErrorEnum::ERROR_MAX_PRICE;
            $paymentProcessor === PaymentProcessorEnum::PAYMENT_PROCESSOR_STRIPE &&
            $response['error_message'] = ErrorEnum::ERROR_MIN_PRICE;
        }
        $response['success'] = $value === true;
        return $response;
    }
}